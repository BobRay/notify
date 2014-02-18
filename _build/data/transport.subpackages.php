<?php
/**
 * Subpackage transport file for Notify extra
 *
 * Copyright 2013-2014 by Bob Ray <http://bobsguides.com>
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
/** Package in subpackages
 *
 * @var modX $modx
 * @var modPackageBuilder $builder
 * @var array $sources
 * @package articles
 */
$subpackages = array (
  'mandrillx' => 'mandrillx-1.0.2-pl',
  'subscribe' => 'subscribe-1.2.0-pl',
);
$spAttr = array('vehicle_class' => 'xPDOTransportVehicle');

foreach ($subpackages as $name => $signature) {
    $vehicle = $builder->createVehicle(array(
        'source' => $sources['subpackages'] . $signature.'.transport.zip',
        'target' => "return MODX_CORE_PATH . 'packages/';",
    ), $spAttr);
    $vehicle->validate('php',array(
        'source' => $sources['validators'].'validate.'.$name.'.php'
    ));
    $vehicle->resolve('php',array(
        'source' => $sources['resolvers'].'packages/resolve.'.$name.'.php'
    ));
    $builder->putVehicle($vehicle);
}
return true;
