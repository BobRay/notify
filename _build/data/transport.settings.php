<?php
/**
 * systemSettings transport file for Notify extra
 *
 * Copyright 2013-2017 Bob Ray <https://bobsguides.com>
 * Created on 03-02-2014
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
/* @var xPDOObject[] $systemSettings */


$systemSettings = array();

$systemSettings[1] = $modx->newObject('modSystemSetting');
$systemSettings[1]->fromArray(array (
  'key' => 'nf_status_resource_id',
  'value' => '',
  'xtype' => 'integer',
  'namespace' => 'notify',
  'area' => '',
  'name' => 'Notify Status Resource ID',
  'description' => 'ID of Notify Status Resource',
), '', true, true);
return $systemSettings;
