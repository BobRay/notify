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

my_debug('After auth check', true);


require_once $modx->getOption('nf.core_path', null, $modx->getOption('core_path') . 'components/notify/') . 'model/notify/notify.class.php';
my_debug('After class include', true);

    my_debug("\nexecuting after event name");
    $src = "<script type='text/javascript'>
        Ext.onReady(function() {
        Ext.get('nf-b').setStyle('color', 'red');
        Ext.get('nf-b').setStyle('margin-bottom', '15px');
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
            form.setAttribute('action', path);  // Notify page URL !!!

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
        $tvObj = $modx->getObject('modTemplateVar', array('name' => 'Testing'));
        $notifyObj = $modx->getObject('modResource', array('alias' => 'notify'));
        $notifyUrl = $modx->makeUrl($notifyObj->get('id'), "", "", "full");
        $tv = 'tv' . $tvObj->get('id');
        $pageId = $modx->resource->get('id');
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
    my_debug("\nSRC: " . $src);
    $modx->regClientStartupScript($src);
    return '';



/*
var form = document.createElement("form");
    form.setAttribute("method", "post");
    form.setAttribute("action", path);  // URL !!!

    for(var key in params) {
        if(params.hasOwnProperty(key)) {
            var hiddenField = document.createElement("input");
            hiddenField.setAttribute("type", "hidden");
            hiddenField.setAttribute("name", key);
            hiddenField.setAttribute("value", params[key]);

            form.appendChild(hiddenField);
         }
    }

    document.body.appendChild(form);
    form.submit();
*/


/* Get TV values */

$nfDoNotify = $modx->resource->getTVValue('nf_notify_subscribers') == 'Yes';


if ($nfDoNotify) {
    $modx->resource->setTVValue('nf_notify_subscribers', 'No');
    //unset($emailit);
    $_SESSION['nf_page_id'] = $modx->resource->get('id');
    $nfUrl = $modx->makeUrl(437, "", "", "full");
    $modx->sendRedirect($nfUrl);
} else {
    return '';
}