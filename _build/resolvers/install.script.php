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

/* The $modx object is not available here. In its place we
 * use $object->xpdo
 */

/* @var $modx modX */


$modx =& $object->xpdo;


$plugins = array('Notify');

$category = 'Notify';


/* @var $options array */
/* @var $intersect modPluginEvent */
/* @var $pluginObj modPlugin */
/* @var $categoryObj modCategory */
/* @var $tvt modTemplateVarTemplate */
/* @var $tv modTemplateVar */

$success = true;

$modx->log(xPDO::LOG_LEVEL_INFO, 'Running PHP Resolver.');
switch ($options[xPDOTransport::PACKAGE_ACTION]) {

    case xPDOTransport::ACTION_INSTALL:
    case xPDOTransport::ACTION_UPGRADE:

        /* Assign plugins to System events */
        /* @var $pluginEvent modPluginEvent */
        /* @var $plugin modPlugin */

        $categoryId = null;
        $categoryObj = $modx->getObject('modCategory', array('category' => $category));
        if (!$categoryObj) {
            $modx->log(xPDO::LOG_LEVEL_INFO, 'Could not retrieve category object: ' . $category);
        } else {
            $categoryId = $categoryObj->get('id');
        }

        $plugin = $modx->getObject('modPlugin', array('name' => 'Notify'));
        if ($plugin) {
            $pluginId = $plugin->get('id');
        } else {
            $pluginId = 0;
            $modx->log(xPDO::LOG_LEVEL_ERROR, 'Could not retrieve Notify Plugin');
        }
        /* @var $notifyResource modResource */
        $notifyResource = $modx->getObject('modResource', array('alias' => 'notify'));
        if (!$notifyResource) {
            $modx->log(xPDO::LOG_LEVEL_ERROR, 'Could not retrieve Notify Resource');
        }

        /* set resource ID in plugin */
        if ($notifyResource && $plugin) {
            $plugin->set('category', $categoryId);
        }
        /* set Template of Notify and Preview resources to NotifyTemplate */
        /* @var $notifyTemplate modTemplate */
        $notifyTemplateId = 0;
        $notifyTemplate = $modx->getObject('modTemplate', array('templatename' => 'NotifyTemplate'));
        if (!$notifyTemplate) {
            $modx->log(xPDO::LOG_LEVEL_ERROR, 'Could not retrieve Notify Template');
        } else {
            $notifyTemplateId = $notifyTemplate->get('id');
        }

        if ($notifyTemplateId && $notifyResource) {
            $notifyResource->set('template', $notifyTemplateId);
            if ($notifyResource->save()) {
                $modx->log(xPDO::LOG_LEVEL_INFO, 'Set template for Notify Resource');
            } else {
                $modx->log(xPDO::LOG_LEVEL_ERROR, 'Failed to Set template for Notify Resource');
            }
        }
        /* @var $notifyPreviewResource modResource */
        $notifyPreviewResource = $modx->getObject('modResource', array('alias' => 'notify-preview'));
        if (!$notifyPreviewResource) {
            $modx->log(xPDO::LOG_LEVEL_ERROR, 'Could not retrieve Notify Preview Resource');
        }
        if ($notifyTemplateId && $notifyPreviewResource) {
            $notifyPreviewResource->set('template', $notifyTemplateId);
            if ($notifyPreviewResource->save()) {
                $modx->log(xPDO::LOG_LEVEL_INFO, 'Set template for Notify Preview Resource');
            }
        }


        /* Connect TV to default template if not already connected */
        $ok = true;

        $defaultTemplateId = $modx->getOption('default_template', null, null);

        if ($defaultTemplateId === null) {
            $modx->log(xPDO::LOG_LEVEL_ERROR, 'Could not get default_template ID');
        } else {

            $modx->log(xPDO::LOG_LEVEL_INFO, 'Attempting to attach TVs to Default Template');

            $tvs = $modx->getCollection('modTemplateVar', array('category' => $categoryId));

            if (!empty($tvs)) {
                foreach ($tvs as $tv) {
                    if (!$modx->getObject('modTemplateVarTemplate', array(
                        'templateid' => $defaultTemplateId,
                        'tmplvarid' => $tv->get('id'),
                    ))
                    ) {
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

    /* This code will execute during an uninstall */
    case xPDOTransport::ACTION_UNINSTALL:
        /* @var $category modCategory */
        $modx->log(xPDO::LOG_LEVEL_INFO, 'Uninstalling . . .');
        $category = $modx->getObject('modCategory', array('category' => 'Notify'));
        if ($category) {
            $category->remove();
        }
        $success = true;
        break;

}
$modx->log(xPDO::LOG_LEVEL_INFO, 'Script resolver actions completed');
return $success;