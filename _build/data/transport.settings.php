<?php
/**
 * systemSettings transport file for Notify extra
 *
 * Copyright 2013-2022 Bob Ray <https://bobsguides.com>
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
  'key' => 'allowedGroups',
  'value' => 'Administrator',
  'xtype' => 'textfield',
  'namespace' => 'notify',
  'area' => '',
  'name' => 'setting_allowedGroups',
  'description' => 'setting_allowedGroups_desc',
), '', true, true);
$systemSettings[2] = $modx->newObject('modSystemSetting');
$systemSettings[2]->fromArray(array (
  'key' => 'nf_status_resource_id',
  'value' => '',
  'xtype' => 'integer',
  'namespace' => 'notify',
  'area' => '',
  'name' => 'setting_nf_status_resource_id',
  'description' => 'setting_nf_status_resource_id_desc',
), '', true, true);
$systemSettings[3] = $modx->newObject('modSystemSetting');
$systemSettings[3]->fromArray(array (
  'key' => 'nf_debug',
  'value' => false,
  'xtype' => 'combo-boolean',
  'namespace' => 'notify',
  'area' => '',
  'name' => 'setting_nf_debug',
  'description' => 'setting_nf_debug_desc',
), '', true, true);
return $systemSettings;
