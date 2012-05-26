<?php

/**
 * RemoveNotify snippet
 *
 * Copyright 2012 Bob Ray <http:bobsguides.com>
 *
 * @author Bob Ray <http:bobsguides.com>
 *
 * RemoveNotify is free software; you can redistribute it and/or modify it
 * under the terms of the GNU General Public License as published by the Free
 * Software Foundation; either version 2 of the License, or (at your option) any
 * later version.
 *
 * RemoveNotify is distributed in the hope that it will be useful, but WITHOUT ANY
 * WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR
 * A PARTICULAR PURPOSE. See the GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License along with
 * RemoveNotify; if not, write to the Free Software Foundation, Inc., 59 Temple
 * Place, Suite 330, Boston, MA 02111-1307 USA
 *
 * @package notify
 */

/**
 * MODx RemoveNotify plugin
 *
 * Description: Removes all vestiges of Notify package except the files
 *
 *
 * @package notify
 *
 */

/* @var $modx modX */
/* @var $category modCategory */
/* @var $tv modTemplateVar */
/* @var $chunk modChunk */
/* @var $plugin modPlugin */
/* @var $pluginEvent modPluginEvent */
/* @var $propertySet modPropertySet */
/* @var $nameSpace modNameSpace */
/* @var $elementPropertySet modElementPropertySet */

$category = $modx->getObject('modCategory', array('category'=>'Notify'));
$output = '';
$pluginId = null;
if (! $category) return 'Category not found<br />';

$categoryId = $category->get('id');
$tvs = $modx->getCollection('modTemplateVar', array('category'=> $categoryId));

if (empty ($tvs)) {
  $output .= 'TVs already removed<br />';
} else {
   foreach ($tvs as $tv) {
      $tv->remove();
      $output .= 'Removed TV<br />';
   }

}

$chunks = $modx->getCollection('modChunk', array('category'=> $categoryId));

if (empty ($chunks)) {
  $output .= 'Chunks already removed<br />';
} else {
   foreach ($chunks as $chunk) {
      $chunk->remove();
      $output .= 'Removed Chunk<br />';
   }

}
$plugin = $modx->getObject('modPlugin', array('name' => 'Notify'));

if (!$plugin) {
   $output .= 'plugin already removed<br />';
} else {
   $pluginId = $plugin->get('id');
   $plugin->remove();
   $output .= 'Removed plugin<br />';
}
$pluginEvents = $modx->getCollection('modPluginEvent', array('pluginId'=> $pluginId));

if (empty ($pluginEvents)) {
  $output .= 'pluginEvents already removed<br />';
} else {
   foreach ($pluginEvents as $pluginEvent) {
      $pluginEvent->remove();
      $output .= 'Removed PluginEvent<br />';
   }

}

$propertySet = $modx->getObject('modPropertySet', array ('name' => 'NotifyProperties'));

if (!$propertySet) {
   $output .= 'propertySet already removed<br />';
   $propertySetId = null;

} else {
   $propertySetId = $propertySet->get('id');
   $propertySet->remove();
   $output .= 'Removed Property Set<br />';
}


$fields = array (
    'element' =>$pluginId,
    'element_class' => 'modPlugin',
    'property_set' => $propertySetId,
    
);

$elementPropertySet = $modx->getObject('modElementPropertySet', $fields);

if (! $elementPropertySet) {
    $output .= 'elementPropertySet already removed<br />';
} else {
    $elementPropertySet->remove();
    $output .= 'Removed ElementPropertySet object<br />';
}

$category->remove();
$output .= 'Removed category<br />';


$nameSpace = $modx->getObject('modNamespace', array ('name' => 'notify'));

if (!$nameSpace) {
   $output .= 'namespace already removed<br />';
} else {
   $nameSpace->remove();
   $output .= 'Removed namespace<br />';
}

return $output;