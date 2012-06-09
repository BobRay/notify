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
$templatevars = array();

$templatevars[1] = $modx->newObject('modTemplateVar');
$templatevars[1] ->fromArray(array(
    'id' => 1,
    'property_preprocess' => '0',
    'type' => 'option',
    'name' => 'NotifySubscribers',
    'caption' => '<button id="nf-b" onClick="nf()"> Launch Notify </button>',
    'description' => 'Click to launch Notify',
    'elements' => 'Notify for new resource==new||Update to existing resource==existing||Use custom Tpl==custom||Use blank Tpl==blank',
    'rank' => '1',
    'display' => 'default',
    'default_text' => 'existing',
    'properties' => '',
    'input_properties' => 'a:2:{s:10:"allowBlank";s:4:"true";s:7:"columns";s:1:"1";}',
    'output_properties' => 'a:0:{}',
),'',true,true);
return $templatevars;
