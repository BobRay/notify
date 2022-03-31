<?php
/**
 * templateVars transport file for Notify extra
 *
 * Copyright 2013-2022 Bob Ray <https://bobsguides.com>
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
/* @var xPDOObject[] $templateVars */


$templateVars = array();

$templateVars[1] = $modx->newObject('modTemplateVar');
$templateVars[1]->fromArray(array (
  'id' => 1,
  'property_preprocess' => false,
  'type' => 'option',
  'name' => 'NotifySubscribers',
  'caption' => '<button id="nf-b" onClick="nf()"> Launch Notify </button>',
  'description' => 'Click to launch Notify',
  'elements' => 'Notify for new resource==new||Update to existing resource==existing||Use custom Tpl==custom||Use blank Tpl==blank',
  'rank' => 1,
  'display' => 'default',
  'default_text' => 'existing',
  'properties' => 
  array (
  ),
  'input_properties' => 
  array (
    'allowBlank' => 'true',
    'columns' => '1',
  ),
  'output_properties' => 
  array (
  ),
), '', true, true);
return $templateVars;
