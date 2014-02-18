<?php
/**
 * Subpackage Validator file for Notify extra
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
        /**
 * Verify mandrillx is latest or equal in version
 *
 * @var modX $modx
 * @var xPDOTransport $transport
 * @var array $options
 * @package notify
 */
$newer= true;
if ($transport && $transport->xpdo) {
    switch ($options[xPDOTransport::PACKAGE_ACTION]) {
        case xPDOTransport::ACTION_INSTALL:
        case xPDOTransport::ACTION_UPGRADE:
            $modx =& $transport->xpdo;

            /* define mandrillx version */
            $newVersion = '1.0.2-pl';
            $newVersionMajor = '1';
            $name = 'mandrillx';

            /* now loop through packages and check for newer versions
             * Do not install if newer or equal versions are found */
            $newer = true;
            $modx->addPackage('modx.transport',$modx->getOption('core_path').'model/');
            $c = $modx->newQuery('transport.modTransportPackage');
            $c->where(array(
                'package_name' => $name,
                'version_major:>=' => $newVersionMajor,
            ));
            $packages = $modx->getCollection('transport.modTransportPackage',$c);

            foreach ($packages as $package) {
                /** @var $package modTransportPackage */
                if ($package->compareVersion($newVersion)) {
                    $newer = false;
                    break;
                }
            }
            break;
    }
}

return $newer;
