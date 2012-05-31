<?php

/**
 * Notify resolver script - runs on install.
 *
 * Copyright 2012 Bob Ray <http://bobsguides/com>
 * @author Bob Ray <http://bobsguides/com>
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

/* Example Resolver script */

/* The $modx object is not available here. In its place we
 * use $object->xpdo
 */

/* @var $modx modX */


$modx =& $object->xpdo;

/* Connecting plugins to the appropriate system events and
 * connecting TVs to their templates is done here.
 *
 * Be sure to set the name of the category in $category.
 *
 * You will have to hand-code the names of the elements and events
 * in the arrays below.
 */

$pluginEvents = array('OnWebPagePrerender');
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

$modx->log(xPDO::LOG_LEVEL_INFO,'Running PHP Resolver.');
switch($options[xPDOTransport::PACKAGE_ACTION]) {

    case xPDOTransport::ACTION_INSTALL:

        /* Assign plugins to System events */
        /* @var $pluginEvent modPluginEvent */
        /* @var $plugin modPlugin */

        $plugin = $modx->getObject('modPlugin', array('name' => 'Notify'));
        if ($plugin) {
            $pluginId = $plugin->get('id');
        } else {
            $pluginId = 0;
            $modx->log(xPDO::LOG_LEVEL_ERROR,'Could not retrieve Notify Plugin');
        }

        $propertySet = $modx->getObject('modPropertySet', array('name'=>'NotifyProperties'));
        if (empty ($propertySet)) {
            $modx->log(xPDO::LOG_LEVEL_ERROR,'Could not retrieve Property set');
        }

        if ( (!empty ($propertySet))) {
            $propertySet->set('name','NotifyProperties');
            $propertySet->set('description', 'Properties for Notify Extra');
            $propertySet->save();
            $intersect = $modx->newObject('modElementPropertySet');
            $intersect->set('element',$plugin->get('id'));
            $intersect->set('element_class','modPlugin');
            $intersect->set('property_set',$propertySet->get('id'));
            if (! $intersect->save()) {
                $modx->log(xPDO::LOG_LEVEL_ERROR,'Coult not create modElementPropertySet');
            }
            $properties = $propertySet->getProperties();
            $plugin->setProperties($properties);
            $plugin->save();
        }

        $pluginEvents = $modx->getCollection('modPluginEvent', array('pluginId' => $plugin->get('id')));
        if (empty ($pluginEvents)) {
            $modx->log(xPDO::LOG_LEVEL_ERROR,'Could not get Plugin Events');
        } else {
            /* @var $event modPluginEvent */
            foreach ($pluginEvents as $event) {
                $event->set('propertyset', $propertySet->get('id'));
                if (!$event->save()) {
                    $modx->log(xPDO::LOG_LEVEL_ERROR,'Could not Set Plugin Event');
                }

            }

        }

        $ok = true;

        $categoryId = null;
        $categoryObj = $modx->getObject('modCategory', array('category' => $category));
        if (!$categoryObj) {
            $modx->log(xPDO::LOG_LEVEL_INFO, 'Could not retrieve category object: ' . $category);
        } else {
            $categoryId = $categoryObj->get('id');
        }
        $defaultTemplateId = $modx->getOption('default_template', null, null);

        if ($defaultTemplateId === null) {
            $modx->log(xPDO::LOG_LEVEL_ERROR, 'Could not get default_template ID');
        } else {

            $modx->log(xPDO::LOG_LEVEL_INFO, 'Attempting to attach TVs to Default Template');

            $tvs = $modx->getCollection('modTemplateVar', array('category' => $categoryId));

            if (!empty($tvs)) {
                foreach ($tvs as $tv) {
                    $tvt = $modx->newObject('modTemplateVarTemplate');
                    if ($tvt) {
                        $r1 = $tvt->set('templateid', $defaultTemplateId);
                        $r2 = $tvt->set('tmplvarid', $tv->get('id'));
                        if ($r1 && $r2) {
                            $tvt->save();
                        } else {
                            $ok = false;
                            $modx->log(xPDO::LOG_LEVEL_ERROR, 'Could not set TemplateVarTemplate fields');
                        }
                    } else {
                        $ok = false;
                        $modx->log(xPDO::LOG_LEVEL_ERROR, 'Could not create TemplateVarTemplate');
                    }
                }

            } else {
                $ok = false;
                $modx->log(xPDO::LOG_LEVEL_ERROR, 'Could not retrieve TVs in category: ' . $category);
            }

        }

        if ($ok) {
            $modx->log(xPDO::LOG_LEVEL_INFO, 'TVs attached to Templates successfully');
        } else {
            $modx->log(xPDO::LOG_LEVEL_INFO, 'Failed to attach TVs to Templates');
        }

        break;
    case xPDOTransport::ACTION_UPGRADE:
        break;


    /* This code will execute during an uninstall */
    case xPDOTransport::ACTION_UNINSTALL:
        /* @var $category modCategory */
        $modx->log(xPDO::LOG_LEVEL_INFO,'Uninstalling . . .');
        $category = $modx->getObject('modCategory', array ('category' => 'Notify'));
        if ($category) {
            $category->remove();
        }
        $success = true;
        break;

}
$modx->log(xPDO::LOG_LEVEL_INFO,'Script resolver actions completed');
return $success;