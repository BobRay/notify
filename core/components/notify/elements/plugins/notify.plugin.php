<?php
/**
 * Notify plugin
 *
 * Copyright 2012 Bob Ray <http:bobsguides.com>
 *
 * @author Bob Ray <http:bobsguides.com>
 * @version Version 1.0.0 Beta-1
 * 8/20/11
 *
 * Notify is free software; you can redistribute it and/or modify it
 * under the terms of the GNU General Public License as published by the Free
 * Software Foundation; either version 2 of the License, or (at your option) any
 * later version.
 *
 * Notify is distributed in the hope that it will be useful, but WITHOUT ANY
 * WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR
 * A PARTICULAR PURPOSE. See the GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License along with
 * Notify; if not, write to the Free Software Foundation, Inc., 59 Temple
 * Place, Suite 330, Boston, MA 02111-1307 USA
 *
 * @package notify
 */

/**
 * MODx Notify plugin
 *
 * Description: Creates and Sends an email to subscribers and notifies social media
 * Events: OnWebPagePrerender, OnDocFormPrerender
 *
 *
 * @package notify
 *
 */


/* ToDo: Internationalize error messages */
/* @var $modx modX */
/* @var $id int */
/* @var $resource modResource */


$sp =& $scriptProperties;
$base_url = $modx->getOption('site_url');
$header = '';

/* Act only when previewing from the back end */
if (!$modx->user->hasSessionContext('mgr')) {
    return '';
}

require_once $modx->getOption('nf.core_path', null, $modx->getOption('core_path') . 'components/notify/') . 'model/notify/notify.class.php';


/* Abort if in a resource that won't be emailed */
$templates = $modx->getOption('template_list', $sp, null);
if (!empty($templates)) {
    $templates = explode(',', $templates);
    if (!in_array($modx->resource->get('template'), $templates)) {
        return '';
    }

}
unset($templates);

switch ($modx->event->name) {
    /* @var $nf Notify */
    case 'OnWebPagePrerender':
        $nf = new Notify($modx, $sp);

        /* Get TV values */
        $sendTweet = $modx->resource->getTVValue('nf_twitter') == 'Yes';
        $preview = $modx->resource->getTVValue('nf_preview_email') == 'Yes';
        $emailit = $modx->resource->getTVValue('nf_notify_subscribers') == 'Yes';
        $inlineCss = $modx->resource->getTVValue('InlineCss') == 'Yes';
        $sendTestEmail = $modx->resource->getTVValue('nf_send_test_email') == 'Yes';


        $testEmailAddress = $modx->resource->getTVValue('nf_email_address_for_test');


        if ($emailit || $preview || $sendTestEmail) {
            /* @var $resource modResource */
            /* @var $id int */

            $nf->init();
            $url = $modx->makeUrl($modx->resource->get('id'), "", "", "full");
            $fields = $modx->resource->toArray();
            $fields['url'] = $url;

            $txt = $modx->getChunk('NfSubScriberEmail', $fields);

            $nf->setHtml($txt);

            if ($inlineCss) {
                $nf->inlineCss();
            }

            $output = $nf->getHtml();

        } else {
            /* just return without modifying output */
            return '';
        }

        /* turn the TVs off to prevent accidental resending */
        /* @var $tv modTemplateVar */
        $tv = $modx->getObject('modTemplateVar', array('name' => 'nf_send_test_email'));
        $tv->setValue($modx->resource->get('id'), 'No');
        $tv->save();
        $tv = $modx->getObject('modTemplateVar', array('name' => 'nf_notify_subscribers'));
        $tv->setValue($modx->resource->get('id'), 'No');
        $tv->save();
        /* Need to change the TV values in memory too */

        $fields = array(
            'SendTestEmail',
            'No',
            'default',
            '',
            'option',
        );
        $modx->resource->set('nf_send_test_email', $fields);
        $fields[0] = 'nf_notify_subscrbers';
        $modx->resource->set('nf_notify_subscribers', $fields);

        /* Work starts here */

        if ($emailit || $sendTestEmail) {
            $preview = true;

            $nf->initializeMailer();

            if ($emailit) {
                /* send bulk email */
                $nf->sendBulkEmail();
            }

            if ($sendTestEmail) {
                /* send test email */
                $username = $modx->user->get('username');
                $nf->sendTestEmail($testEmailAddress, $username);
            }
        }
        if ($sendTweet) {
            $nf->tweet();
        }

        $errors = $nf->getErrors();
        if (!empty($errors)) {
            $header = $nf->showErrorStrings();
        } else {
            if ($sendTestEmail) {
                $header = '<h3>Test email sent successfully</h3>';
            }
            if ($emailit) {
                $header .= '<h3>Bulk Email sent successfully</h3>';
            }
            if ($sendTweet) {
                $header .= '<h3>Tweet sent successfully</h3>';
            }
            if ($preview && !($sendTestEmail || $emailit)) {
                $header .= '<h3>Preview of Email:</h3>';
            }
        }
        $output = $nf->getHtml();
        /* make unprocessed tags visible */
        $output = str_replace('[[', '[ [', $output);

        /* inject header if there is a body tag */
        if (strstr($output, '<body>')) {
            $pattern = '~(<body[^>]*>)~';
            $replacement = '$1' . $header . "<br /><br />";
            $output =  preg_replace($pattern,$replacement, $output );
        } else {
            $output = $header . $output;
        }

        $modx->resource->_output = $output;
        break;
    case 'OnDocFormPrerender':
        $url = $modx->makeUrl($id, "", "", "full");
        $fields = $resource->toArray();
        $fields['url'] = $url;
        /* Turn these off so notices are not sent on every save */
        $resource->setTVValue('nf_notify_subscribers', 'No');
        $resource->setTVValue('nf_twitter', 'No');
        $resource->setTVValue('nf_send_test_email', 'No');



        $txt = $resource->getTVValue('nf_email_address_for_test');
        if (empty($txt)) {
            $txt = $modx->getOption('emailsender');
            $resource->setTVValue('nf_email_address_for_test', $txt);

        }

        $txt = $resource->getTVValue('nf_subscriber_email');
        if (empty($txt)) {
            $txt = $modx->getChunk('NfSubScriberEmail', $fields);
            $resource->setTVValue('nf_subscriber_email', $txt);
        }
        $txt = $resource->getTVValue('nf_email_subject');
        if (empty($txt)) {
            $txt = $modx->getChunk('NfEmailSubject', $fields);
            $resource->setTVValue('nf_email_subject', $txt);
        }

        $txt = $resource->getTVValue('nf_tweet');
        if (empty($txt)) {
            $txt = $modx->GetChunk('NfTweet', $fields);
            $resource->setTVValue('nf_tweet', $txt);
        }
        break;
}
return '';


