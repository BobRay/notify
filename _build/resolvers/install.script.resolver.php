<?php

/**
 * Notify resolver script - runs on install.
 *
 * Copyright 2012-2017 Bob Ray <https://bobsguides/com>
 * @author Bob Ray <https://bobsguides/com>
 * 3/27/12
 *
 * Notify is free software; you can redistribute it and/or modify it
 * under the terms of the GNU General Public License as published by the Free
 * Software Foundation; either version 2 of the License, or (at your option) any
 * later version.
 *
 * Notify is distributed in the hope that it will be useful, but WITHOUT ANY
 * WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR
 * A PARTICULAR PURPOSE. See the GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License along with
 * Notify; if not, write to the Free Software Foundation, Inc., 59 Temple
 * Place, Suite 330, Boston, MA 02111-1307 USA
 *
 * @package notify
 */
/**
 * Description: Resolver script for Notify package
 * @package notify
 * @subpackage build
 */

/* The $modx object is not available here. In its place we
 * use $object->xpdo
 */

/** @var $object xPDO */
/** @var $modx modX */
$modx =& $object->xpdo;

$classPrefix = $modx->getVersionData()['version'] >= 3
        ? 'MODX\Revolution\\'
        : '';

$plugins = array('Notify');

$category = 'Notify';


/* @var $options array */
/* @var $propertySet modPropertySet */
/* @var $intersect modPluginEvent */
/* @var $pluginObj modPlugin */
/* @var $categoryObj modCategory */
/* @var $elementPropertySet modElementPropertySet */
/* @var $tvt modTemplateVarTemplate */
/* @var $tv modTemplateVar */

$success = true;

$modx->log(xPDO::LOG_LEVEL_INFO, 'Running PHP Resolver.');
switch ($options[xPDOTransport::PACKAGE_ACTION]) {

    case xPDOTransport::ACTION_INSTALL:
    case xPDOTransport::ACTION_UPGRADE:
        $ps = $modx->getObject($classPrefix . 'modPropertySet', array('name' => 'NotifyProperties' ));
        $sn = $modx->getObject($classPrefix . 'modSnippet', array('name' => 'Notify'));
        if ($sn && $ps) {
            $fields = array(
                'property_set' => $ps->get('id'),
                'element' => $sn->get('id'),
                'element_class' => 'modSnippet',
            );

            $eps = $modx->getObject($classPrefix . 'modElementPropertySet', $fields );
            if (! $eps) {
                $eps = $modx->newObject($classPrefix . 'modElementPropertySet');
                foreach ($fields as $k => $value) {
                    $eps->set($k, $value);
                }
                $eps->save();
            }
        }
        $success = true;
        break;

    /* This code will execute during an uninstall */
    case xPDOTransport::ACTION_UNINSTALL:
        /* @var $category modCategory */
        $modx->log(xPDO::LOG_LEVEL_INFO, 'Uninstalling . . .');
        $category = $modx->getObject($classPrefix . 'modCategory', array('category' => 'Notify'));
        if ($category) {
            $category->remove();
        }
        $success = true;
        break;

}

return $success;