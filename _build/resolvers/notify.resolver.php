<?php
/**
 * Resolver for Notify extra
 *
 * Copyright 2013-2014 by Bob Ray <http://bobsguides.com>
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
    switch ($options[xPDOTransport::PACKAGE_ACTION]) {
        case xPDOTransport::ACTION_INSTALL:
        case xPDOTransport::ACTION_UPGRADE:


            $resource = $modx->getObject('modResource', array('alias' => 'notify-status'));
            $setting = $modx->getObject('modSystemSetting', array('key' => 'nf_status_resource_id'));
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
            $resource = $modx->getObject('modResource', array('alias' => 'notify'));
            if ($resource) $resource->remove();
            $setting = $modx->getObject('modSystemSetting', array('key' => 'nf_status_resource_id'));
            if ($setting) {
                $setting->remove();
            }
            break;
    }
}

return true;