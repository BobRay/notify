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
/* @var $nf Notify */


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

    case 'OnWebPagePrerender':

        $res = null;


        /* Get TV values */

        $nfDoNotify = $modx->resource->getTVValue('nf_notify_subscribers') == 'Yes';

        if ($nfDoNotify ) {
            $modx->resource->setTVValue('nf_notify_subscribers', 'No');
            unset($emailit);
            $_SESSION['nf_page_id'] = $modx->resource->get('id');
            $nfUrl = $modx->makeUrl(403,"","","full");
            $modx->sendRedirect($nfUrl);
        } else {
            return '';
        }

        return "Dispatching Failed URL: " . $nfUrl;

        /* bail out if no action requested */
        if (! ($sendTweet || $preview || $emailit || $sendTestEmail)) {
            return '';
        }
        $nf = new Notify($modx, $sp, $res);

        $nf->init($modx->event->name);

        /* reset TVs to prevent accidental re-sending */
        $nf->resetTVs();

        /* Work starts here */

        if ($emailit || $sendTestEmail) {
            /* Set preview in case user forgot */
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
        $nf->displayResults($preview, $emailit, $sendTestEmail);
        break;

    case 'OnResourceTVFormPrerender':
        /* pre-set Notify TV fields for doc form TV tab*/
        $nf = new Notify($modx, $sp, $resource);
        /* Turn off TVs so notices are not re-sent accidentally */
        $resource->setTVValue('nf_tweet','Testing');
        $nf->resetTVs();
        //$nf->init($modx->event->name);

        break;
}
return '';


