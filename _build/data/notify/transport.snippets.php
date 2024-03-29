<?php
/**
 * snippets transport file for Notify extra
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
/* @var xPDOObject[] $snippets */


$snippets = array();

$snippets[1] = $modx->newObject('modSnippet');
$snippets[1]->fromArray(array (
  'id' => 1,
  'property_preprocess' => false,
  'name' => 'Notify',
  'description' => 'Send site updates to Subscribers and Twitter',
), '', true, true);
$snippets[1]->setContent(file_get_contents($sources['source_core'] . '/elements/snippets/notify.snippet.php'));


$properties = include $sources['data'].'properties/properties.notify.snippet.php';
$snippets[1]->setProperties($properties);
unset($properties);

return $snippets;
