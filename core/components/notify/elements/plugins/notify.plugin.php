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
/* @var $resource modResource */


$sp =& $scriptProperties;

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


        /* Get TV values */
        $sendTweet = $modx->resource->getTVValue('nf_twitter') == 'Yes';
        $preview = $modx->resource->getTVValue('nf_preview_email') == 'Yes';
        $emailit = $modx->resource->getTVValue('nf_notify_subscribers') == 'Yes';
        $sendTestEmail = $modx->resource->getTVValue('nf_send_test_email') == 'Yes';

        /* bail out if no action requested */
        if (! ($sendTweet || $preview || $emailit || $sendTestEmail)) {
            return '';
        }

        $nf = new Notify($modx, $sp);
        $nf->init();

        $nf->resetTVs();
        /* Work starts here */

        if ($emailit || $sendTestEmail) {
            $preview = true;
            $nf->initEmail();
            $nf->initializeMailer();

            if ($emailit) {
                /* send bulk email */
                $nf->sendBulkEmail();
            }

            if ($sendTestEmail) {
                /* send test email */
                $testEmailAddress = $modx->resource->getTVValue('nf_email_address_for_test');
                $username = $modx->user->get('username');
                $nf->sendTestEmail($testEmailAddress, $username);
            }
        }
        if ($sendTweet) {
            $nf->tweet();
        }
        /* Show results and/or preview */

        /* don't show email text unless one of these is set */
        $emailText = $emailit || $preview || $sendTestEmail? $nf->getEmailText() : '';

        /* inject headers if there is a body tag */
        if (strstr($emailText, '<body>')) {
            $pattern = '~(<body[^>]*>)~';
            $replacement = '$1' . $nf->getSuccessHeader() . "<br /><br />" . $nf->getErrorHeader();
            $output =  preg_replace($pattern,$replacement, $emailText );
        } else {
            /* no injection */
            $output = $nf->getSuccessHeader() ."<br /><br />" . $nf->getErrorHeader() .  $emailText;
        }

        $modx->resource->_output = $output;
        break;
    case 'OnDocFormPrerender':

        $url = $modx->makeUrl($resource->get('id'), "", "", "full");
        $fields = $resource->toArray();
        $fields['url'] = $url;
        /* Turn these off so notices are not sent accidentally */
        $resource->setTVValue('nf_notify_subscribers', 'No');
        $resource->setTVValue('nf_twitter', 'No');
        $resource->setTVValue('nf_send_test_email', 'No');
        $resource->setTVValue('nf_preview_email', 'No');

        /* get Tpl names */
        $emailTpl = $modx->getOption('nfEmailTpl', $sp, 'NfSubscriberEmailTpl');
        $emailSubjectTpl = $modx->getOption('nfEmailSubjectTpl', $sp, 'NfEmailSubjectTpl');
        $tweetTpl = $modx->getOption('nfTweetTpl', $sp, 'NfTweetTpl');

        /* pre-fill TVs */
        $txt = $resource->getTVValue('nf_email_address_for_test');
        if (empty($txt)) {
            $txt = $modx->getOption('emailsender');
            $resource->setTVValue('nf_email_address_for_test', $txt);
        }
        $txt = $resource->getTVValue('nf_subscriber_email');
        if (empty($txt)) {
            $txt = $modx->getChunk($emailTpl, $fields);
            $resource->setTVValue('nf_subscriber_email', $txt);
        }
        $txt = $resource->getTVValue('nf_email_subject');
        if (empty($txt)) {
            $txt = $modx->getChunk($emailSubjectTpl, $fields);
            $resource->setTVValue('nf_email_subject', $txt);
        }
        $txt = $resource->getTVValue('nf_tweet');
        if (empty($txt)) {
            $txt = $modx->GetChunk($tweetTpl, $fields);
            $resource->setTVValue('nf_tweet', $txt);
        }
        break;
}
return '';


