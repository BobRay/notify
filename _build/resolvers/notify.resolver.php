<?php
/**
 * Resolver for Notify extra
 *
 * Copyright 2013-2017 Bob Ray <https://bobsguides.com>
 * Created on 03-13-2014
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
 * @package notify
 * @subpackage build
 */

/* @var $object xPDOObject */
/* @var $modx modX */
/* @var array $options */

/** @var  $resource modResource */
/** @var  $setting modSystemSetting */

/** Fix properties for MODX 3 and remove obsolete properties
 *
 * @param $modx modX
 * @param $class string
 * @param $name string
 *
 */
function fixProperties($modx, $class, $name) {
    $isMODX3 = $modx->getVersionData()['version'] >= 3;
    $prefix = $isMODX3
            ? 'MODX\Revolution\\'
            : '';
    $fixes = array(
            'modUserProfile',
            'modUser',
    );

    $removeList = array(
            'debug',
            'mailgun.debug',
            'mandrill_api_key',
            'nfUseMandrill',
            'subaccount',
    );

    /* number of changes */
    $count = 0;
    $object = $modx->getObject($class, array('name' => $name));
    if ($object) {
        $props = $object->get('properties');
    }
    if (!empty($props)) {
        if ($isMODX3) {
            foreach ($props as $key => $fields) {
                $value = $fields['value'];
                if (in_array($value, $fixes, true)) {
                    $count++;
                    $props[$key]['value'] = $prefix . $value;
                }
            }
        }
        foreach ($removeList as $k => $v) {
            if (isset($props[$v])) {
                unset($props[$v]);
                $count++;
            }
        }
        if (isset($props['mailService']['options'])) {
            $options = $props['mailService']['options'];
            foreach ($options as $k => $v) {
                if (isset($v['text']) && ($v['text'] === 'MandrillX')) {
                    unset($props['mailService']['options'][$k]);
                    $count++;
                }
            }
        }
    }

    if ($count > 0) {
        $object->set('properties', $props);
        $object->save();
    }
}

if ($object->xpdo) {
    $modx =& $object->xpdo;

    $classPrefix = $modx->getVersionData()['version'] >= 3
            ? 'MODX\Revolution\\'
            : '';
    switch ($options[xPDOTransport::PACKAGE_ACTION]) {
        case xPDOTransport::ACTION_INSTALL:
        case xPDOTransport::ACTION_UPGRADE:
            fixProperties($modx, 'modSnippet', 'Notify');
            fixProperties($modx, 'modPropertySet', 'NotifyProperties');
            $resource = $modx->getObject($classPrefix . 'modResource', array('alias' => 'notify-status'));
            $setting = $modx->getObject($classPrefix . 'modSystemSetting', array('key' => 'nf_status_resource_id'));
            if ($resource) {
                $resource->set('template', 0);
                $resource->save();
            }
            if ($resource && $setting) {
                $id = $resource->get('id');
                $setting->set('value', $id);
                $setting->save();
            }
            break;

        case xPDOTransport::ACTION_UNINSTALL:
            /*$resource = $modx->getObject($classPrefix . 'modResource', array('alias' => 'notify'));
            if ($resource) $resource->remove();
            $setting = $modx->getObject($classPrefix . 'modSystemSetting', array('key' => 'nf_status_resource_id'));
            if ($setting) {
                $setting->remove();
            }*/
            break;
    }
}

return true;