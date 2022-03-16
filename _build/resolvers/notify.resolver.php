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


if ($object->xpdo) {
    $modx =& $object->xpdo;

    $classPrefix = $modx->getVersionData()['version'] >= 3
            ? 'MODX\Revolution\\'
            : '';
    switch ($options[xPDOTransport::PACKAGE_ACTION]) {
        case xPDOTransport::ACTION_INSTALL:
        case xPDOTransport::ACTION_UPGRADE:
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
            /* Remove deprecated properties from Notify snippet
               and NotifyProperties property set */
        $removeList = array(
                'debug',
                'mailgun.debug',
                'mandrill_api_key',
                'nfUseMandrill',
                'subaccount',
        );

            $set = $modx->getObject($classPrefix . 'modPropertySet', array('name' => 'NotifyProperties'));
            if ($set) {
                $props = $set->get('properties');

                foreach ($removeList as $k => $v) {
                    if (isset($props[$v])) {
                        unset($props[$v]);
                    }
                }

                if (isset($props['mailService']['options'])) {
                    $options = $props['mailService']['options'];
                    foreach ($options as $k => $v) {
                        if (isset($v['text']) && ($v['text'] === 'MandrillX')) {
                            unset($props['mailService']['options'][$k]);
                        }
                    }
                }

                $set->set('properties', $props);
                $set->save();
            }


            $snippet = $modx->getObject($classPrefix . 'modSnippet', array('name' => 'Notify'));
            $props = $snippet->get('properties');
            if ($snippet && $props) {
                foreach ($removeList as $k => $v) {
                    if (isset($props[$v])) {
                        unset($props[$v]);
                    }
                }

                if (isset($props['mailService']['options'])) {
                    $options = $props['mailService']['options'];
                    foreach ($options as $k => $v) {
                        if (isset($v['text']) && ($v['text'] === 'MandrillX')) {
                            unset($props['mailService']['options'][$k]);
                        }
                    }
                }
                /* Add MODX\Revolution\ prefix if >= MODX 3 and not set already */
                $fixes = array(
                    'userClass',
                    'profileClass',
                );
                foreach ($fixes as $k => $v) {
                    if (isset($props[$v]['value'])) {
                        if (!strpos($props[$v]['value'], 'Revolution') === false) {
                            $props[$v]['value'] = $classPrefix . $v;
                        }
                    }
                }

                $snippet->set('properties', $props);
                $snippet->save();
            }

            break;

        case xPDOTransport::ACTION_UNINSTALL:
            $resource = $modx->getObject($classPrefix . 'modResource', array('alias' => 'notify'));
            if ($resource) $resource->remove();
            $setting = $modx->getObject($classPrefix . 'modSystemSetting', array('key' => 'nf_status_resource_id'));
            if ($setting) {
                $setting->remove();
            }
            break;
    }
}

return true;