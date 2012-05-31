<?php
/**
 * Notify snippet
 *
 * Copyright 2012 Bob Ray <http:bobsguides.com>
 *
 * @author Bob Ray <http:bobsguides.com>
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
 * Description: Removes all vestiges of Notify package except the files
 *
 *
 * @package notify
 *
 */

/* @var $modx modX */
/* @var $scriptProperties array */
/* @var $category modCategory */
/* @var $tv modTemplateVar */
/* @var $chunk modChunk */
/* @var $plugin modPlugin */
/* @var $pluginEvent modPluginEvent */
/* @var $propertySet modPropertySet */
/* @var $nameSpace modNameSpace */
/* @var $elementPropertySet modElementPropertySet */


/* abort if not previewing from 'mgr' */

if (! $modx->user->hasSessionContext('mgr') || ! $modx->user->isMember('Administrator')) {
    return $modx->lexicon('Unauthorized Access');
}
$output = '';
require_once $modx->getOption('nf.core_path', null, $modx->getOption('core_path') . 'components/notify/') . 'model/notify/notify.class.php';

$sp =& $scriptProperties;
$nf = new Notify($modx, $sp);

/* Handle repost here */


if (isset($_POST['submitVar']) && ($_POST['submitVar'] == 'submitVar')) {
    $output .= "Post Submitted<br /><br />";
    $output .= $nf->init('handleSubmission');
    $output = $nf->displayErrors() . $nf->displaySuccessMessage() . $output;
} else {

    /* Display form */
    /* @var $res modResource */

    $nf->init('displayForm');
    if ($nf->hasErrors()) {
        $output = $nf->displayErrors();
    } else {
    $output = $nf->displayForm();
    }
}

return $output;


/*$subscriberEmail = $modx->getOption('nf_test_email_address', $sp, $modx->getOption('emailsender'));

$modx->setPlaceholder('nf_test_email_address', $subscriberEmail);


$groups = $modx->getOption('groups', $sp, 'Subscribers');
if (empty($groups)) {
    $groups = 'Subscribers';
}
$modx->setPlaceholder('nf_groups', $groups);


$tags = $modx->getOption('tags', $sp, '');
$modx->setPlaceholder('nf_tags', $tags);




$fields = $res->toArray();
$fields['url'] = $modx->makeUrl($pageId,"","","full");
unset($fields['content']);
$modx->setPlaceholders($fields);


$emailText = $modx->getChunk('NfSubscriberEmail');
$tempPage = $modx->getObject('modResource', array('alias'=> 'notify-preview'));
$tempPage->setContent($emailText);
$tempPage->save();
$tmpUrl = $modx->makeUrl($tempPage->get('id'), "", "", "full");
$modx->setPlaceholder('nf_temp_url', $tmpUrl);


//$fields['pagetitle'] = $resource->get('pagetitle');
$modx->setPlaceholder('nf_email_text', $emailText);
$modx->setPlaceholder('nf_email_subject',$modx->getChunk('NfEmailSubjectTpl'));
$modx->setPlaceholder('nf_tweet_text', $modx->getChunk('NfTweetTpl'));


$output .= $modx->getChunk('nfNotifyForm');



return $output; */