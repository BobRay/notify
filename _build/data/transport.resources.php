<?php
/**
 * Notify
 *
 * Copyright 2012 by Bob Ray <http://bobsguides.com>
 *
 * Notify is free software; you can redistribute it and/or modify it under the
 * terms of the GNU General Public License as published by the Free Software
 * Foundation; either version 2 of the License, or (at your option) any later
 * version.
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
 * @var modX $modx
 */
$resources = array();

$resources[1] = $modx->newObject('modResource');
$resources[1] ->fromArray(array(
    'id' => 1,
    'type' => 'document',
    'contentType' => 'text/html',
    'pagetitle' => 'Notify',
    'longtitle' => 'Notify',
    'description' => '',
    'alias' => 'notify',
    'link_attributes' => '',
    'published' => '1',
    'isfolder' => '0',
    'introtext' => '',
    'richtext' => '0',
    'template' => '21',
    'menuindex' => '0',
    'searchable' => '1',
    'cacheable' => '1',
    'createdby' => '1',
    'editedby' => '1',
    'deleted' => '0',
    'deletedon' => '0',
    'deletedby' => '0',
    'menutitle' => '',
    'donthit' => '0',
    'privateweb' => '0',
    'privatemgr' => '0',
    'content_dispo' => '0',
    'hidemenu' => '1',
    'class_key' => 'modDocument',
    'context_key' => 'web',
    'content_type' => '1',
    'hide_children_in_tree' => '0',
    'show_in_tree' => '1',
    'properties' => '',
),'',true,true);
$resources[1]->setContent(file_get_contents($sources['data'].'resources/notify.content.html'));

$resources[2] = $modx->newObject('modResource');
$resources[2] ->fromArray(array(
    'id' => 2,
    'type' => 'document',
    'contentType' => 'text/html',
    'pagetitle' => 'NotifyPreview',
    'longtitle' => '',
    'description' => '',
    'alias' => 'notify-preview',
    'link_attributes' => '',
    'published' => '1',
    'isfolder' => '0',
    'introtext' => '',
    'richtext' => '0',
    'template' => '21',
    'menuindex' => '43',
    'searchable' => '0',
    'cacheable' => '0',
    'createdby' => '1',
    'editedby' => '1',
    'deleted' => '0',
    'deletedon' => '0',
    'deletedby' => '0',
    'menutitle' => '',
    'donthit' => '0',
    'privateweb' => '0',
    'privatemgr' => '0',
    'content_dispo' => '0',
    'hidemenu' => '1',
    'class_key' => 'modDocument',
    'context_key' => 'web',
    'content_type' => '1',
    'hide_children_in_tree' => '0',
    'show_in_tree' => '1',
    'properties' => '',
),'',true,true);
$resources[2]->setContent(file_get_contents($sources['data'].'resources/notifypreview.content.html'));

return $resources;
