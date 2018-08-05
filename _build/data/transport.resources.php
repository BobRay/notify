<?php
/**
 * resources transport file for Notify extra
 *
 * Copyright 2013-2018 by Bob Ray <https://bobsguides.com>
 * Created on 02-17-2014
 *
 * @package notify
 * @subpackage build
 */

if (! function_exists('stripPhpTags')) {
    function stripPhpTags($filename) {
        $o = file_get_contents($filename);
        $o = str_replace('<' . '?' . 'php', '', $o);
        $o = str_replace('?>', '', $o);
        $o = trim($o);
        return $o;
    }
}
/* @var $modx modX */
/* @var $sources array */
/* @var xPDOObject[] $resources */


$resources = array();

$resources[1] = $modx->newObject('modResource');
$resources[1]->fromArray(array (
  'id' => 1,
  'type' => 'document',
  'contentType' => 'text/html',
  'pagetitle' => 'Notify',
  'longtitle' => 'Notify',
  'description' => '',
  'alias' => 'notify',
  'link_attributes' => '',
  'published' => true,
  'isfolder' => false,
  'introtext' => '',
  'richtext' => false,
  'template' => 'NotifyTemplate',
  'menuindex' => 15,
  'searchable' => true,
  'cacheable' => true,
  'createdby' => 1,
  'editedby' => 1,
  'deleted' => false,
  'deletedon' => 0,
  'deletedby' => 0,
  'menutitle' => '',
  'donthit' => false,
  'privateweb' => false,
  'privatemgr' => false,
  'content_dispo' => 0,
  'hidemenu' => true,
  'class_key' => 'modDocument',
  'context_key' => 'web',
  'content_type' => 1,
  'hide_children_in_tree' => 0,
  'show_in_tree' => 1,
  'properties' => '',
), '', true, true);
$resources[1]->setContent(file_get_contents($sources['data'].'resources/notify.content.html'));

$resources[2] = $modx->newObject('modResource');
$resources[2]->fromArray(array (
  'id' => 2,
  'type' => 'document',
  'contentType' => 'text/html',
  'pagetitle' => 'NotifyPreview',
  'longtitle' => '',
  'description' => '',
  'alias' => 'notify-preview',
  'link_attributes' => '',
  'published' => true,
  'isfolder' => false,
  'introtext' => '',
  'richtext' => false,
  'template' => 'NotifyTemplate',
  'menuindex' => 47,
  'searchable' => false,
  'cacheable' => false,
  'createdby' => 1,
  'editedby' => 1,
  'deleted' => false,
  'deletedon' => 0,
  'deletedby' => 0,
  'menutitle' => '',
  'donthit' => false,
  'privateweb' => false,
  'privatemgr' => false,
  'content_dispo' => 0,
  'hidemenu' => true,
  'class_key' => 'modDocument',
  'context_key' => 'web',
  'content_type' => 1,
  'hide_children_in_tree' => 0,
  'show_in_tree' => 1,
  'properties' => '',
), '', true, true);
$resources[2]->setContent(file_get_contents($sources['data'].'resources/notifypreview.content.html'));

$resources[3] = $modx->newObject('modResource');
$resources[3]->fromArray(array (
  'id' => 3,
  'type' => 'document',
  'contentType' => 'text/html',
  'pagetitle' => 'NotifyStatus',
  'longtitle' => '',
  'description' => '',
  'alias' => 'notify-status',
  'link_attributes' => '',
  'published' => true,
  'isfolder' => false,
  'introtext' => '',
  'richtext' => false,
  'template' => 0,
  'menuindex' => 68,
  'searchable' => false,
  'cacheable' => false,
  'createdby' => 1,
  'editedby' => 1,
  'deleted' => false,
  'deletedon' => 0,
  'deletedby' => 0,
  'menutitle' => '',
  'donthit' => false,
  'privateweb' => false,
  'privatemgr' => false,
  'content_dispo' => 0,
  'hidemenu' => true,
  'class_key' => 'modDocument',
  'context_key' => 'web',
  'content_type' => 1,
  'hide_children_in_tree' => 0,
  'show_in_tree' => 1,
  'properties' => NULL,
), '', true, true);
$resources[3]->setContent(file_get_contents($sources['data'].'resources/notifystatus.content.html'));

return $resources;
