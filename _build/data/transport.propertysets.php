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
$propertysets = array();

$propertysets[0] = $modx->newObject('modPropertySet');
$propertysets[0] ->fromArray(array(
    'id' => 0,
    'name' => 'NotifyProperties',
    'description' => 'Properties for Notify Extra',
),'',true,true);

$properties = include $sources['data'].'properties/properties.notify.php';
$propertysets[0]->setProperties($properties);
unset($properties);
return $propertysets;
