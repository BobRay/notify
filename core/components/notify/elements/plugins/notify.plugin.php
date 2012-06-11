<?php
/**
 * Notify plugin
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
 * Description: Creates and Sends an email to subscribers and notifies social media
 * Events: OnDocFormPrerender
 *
 *
 * @package notify
 *
 */


/* ToDo: Internationalize error messages */
/* @var $modx modX */
/* @var $resource modResource */
/* @var $nf Notify */


function my_debug($message, $clear = false)
    {
        global $modx;
        /* @var $chunk modChunk */
        $chunk = $modx->getObject('modChunk', array('name' => 'debug'));

        if (!$chunk) {
            $chunk = $modx->newObject('modChunk', array('name' => 'debug'));
            $chunk->save();
            $chunk = $modx->getObject('modChunk', array('name' => 'debug'));
        }
        if ($clear) {
            $content = '';
        } else {
            $content = $chunk->getContent();
        }
        $content .= $message;
        $chunk->setContent($content);
        $chunk->save();
    }



$sp =& $scriptProperties;


/* Act only when previewing from the back end */
if (!$modx->user->hasSessionContext('mgr')) {
    return '';
}

require_once $modx->getOption('nf.core_path', null, $modx->getOption('core_path') . 'components/notify/') . 'model/notify/notify.class.php';

    $src = "<script type='text/javascript'>
        Ext.onReady(function() {
        var btn = Ext.get('nf-b');
        btn.setStyle('color','red');
        btn.setStyle('margin-bottom','15px');
    });";
    if (!isset($resource)) {
        $src .= "
      function nf() {
         Ext.MessageBox.alert('Oops', 'You must save the page before launching Notify');
       }
      ";

    } else {

        $src .= "
        function nf() {
            var tv = '[[+tv]]';
            var path = '[[+notifyUrl]]';
            var url = '[[+url]]';
            var pagetitle = '[[+pagetitle]]';
            var form = document.createElement('form');
            var pageId = '[[+pageId]]';
            var checkedItem = Ext.getCmp(tv).getValue();
            var pageType = checkedItem.getGroupValue();

            form.setAttribute('method', 'post');
            form.setAttribute('action', path);
            form.setAttribute('target', '_blank');
            var hiddenField = document.createElement('input');
            hiddenField.setAttribute('type', 'hidden');
            hiddenField.setAttribute('name', 'pageId');
            hiddenField.setAttribute('value', pageId);
            form.appendChild(hiddenField);

            var hiddenField2 = document.createElement('input');
            hiddenField2.setAttribute('type', 'hidden');
            hiddenField2.setAttribute('name', 'pageType');
            hiddenField2.setAttribute('value', pageType);
            form.appendChild(hiddenField2);
            document.body.appendChild(form);
            form.submit();

          }
   ";
    }
    $src .= "\n</script>";

    if (isset($resource)) {
        /* @var $tvObj modTemplateVar */
        /* @var $notifyObj modResource */

        $tvObj = $modx->getObject('modTemplateVar', array('name' => 'NotifySubscribers'));
        if (! $tvObj) {
            my_debug('No TV object');
            return '';
        }
        $tvId = $tvObj->get('id');
        $templateId = $resource->get('template');

        /* bail if the Notify TV is not attached to this template */

        $tvt = $modx->getObject('modTemplateVarTemplate', array('templateid'=> $templateId, 'tmplvarid' => $tvId));
        if (!$tvt) {
            return '';
        }

        $notifyObj = $modx->getObject('modResource', array('alias' => 'notify'));
        if (! $notifyObj) {
            my_debug('No resource');
            return '';
        }
        $notifyUrl = $modx->makeUrl($notifyObj->get('id'), "", "", "full");
        $tv = 'tv' . $tvObj->get('id');
        $pageId = $resource->get('id');
        $url = $modx->makeUrl($pageId, "", "", "full");
        if (empty($url)) {
            /* try again after refreshing documentMap */
            //$modx->cacheManager->refresh();
            $modx->reloadContext('web');
            $url = $modx->makeUrl($resource->get('id'), "", "", "full");
        }
        $src = str_replace('[[+pageId]]', $pageId, $src);
        $src = str_replace('[[+notifyUrl]]', $notifyUrl, $src);
        $src = str_replace('[[+tv]]', $tv, $src);
        $src = str_replace('[[+url]]', $url, $src);
        $src = str_replace('[[+pagetitle]]', $resource->get('pagetitle'), $src);
    }

    $modx->regClientStartupScript($src);
    return '';