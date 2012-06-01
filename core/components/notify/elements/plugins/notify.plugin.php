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

unset($templates);


$res = null;


/* Get TV values */

$nfDoNotify = $modx->resource->getTVValue('nf_notify_subscribers') == 'Yes';

if ($nfDoNotify ) {
    $modx->resource->setTVValue('nf_notify_subscribers', 'No');
    unset($emailit);
    $_SESSION['nf_page_id'] = $modx->resource->get('id');
    $nfUrl = $modx->makeUrl(999,"","","full");
    $modx->sendRedirect($nfUrl);
} else {
    return '';
}

return "Dispatching Failed URL: " . $nfUrl;







