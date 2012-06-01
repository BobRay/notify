<?php
/**
 * Notify
 *
 * Copyright 2012 by Bob Ray <http://bobsguides.com>
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
 *
 * @package notify
 */
/**
 * @var modX $modx
 */
$templates = array();

$templates[1] = $modx->newObject('modTemplate');
$templates[1] ->fromArray(array(
    'id' => 1,
    'property_preprocess' => '0',
    'templatename' => 'NotifyTemplate',
    'description' => 'Template for Notify Resource',
    'icon' => '',
    'template_type' => '0',
    'properties' => '',
    'content' => file_get_contents($sources['source_core'].'/elements/templates/notifytemplate.template.html'),
),'',true,true);
return $templates;
