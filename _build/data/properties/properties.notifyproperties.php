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
 *//**
* Default properties for the properties.notifyproperties.phpsnippet
* @author Bob Ray <http://bobsguides.com>
*
* @package notify
* @subpackage build
*/



$properties = array( 
    'twitter_consumer_key' => array( 
        'name' => 'twitter_consumer_key',
        'desc' => 'Twitter Consumer Key',
        'type' => 'textfield',
        'options' => '',
        'value' => '',
        'lexicon' => 'notify',
        'area' => 'notify',
        'desc_trans' => 'Twitter Consumer Key',
        ),
    'twitter_consumer_secret' => array( 
        'name' => 'twitter_consumer_secret',
        'desc' => 'Twitter Consumer Secret',
        'type' => 'textfield',
        'options' => '',
        'value' => '',
        'lexicon' => 'notify',
        'area' => 'notify',
        'desc_trans' => 'Twitter Consumer Secret',
        ),
    'twitter_oath_token' => array( 
        'name' => 'twitter_oath_token',
        'desc' => 'Twitter Access Token',
        'type' => 'textfield',
        'options' => '',
        'value' => '',
        'lexicon' => 'notify',
        'area' => 'notify',
        'desc_trans' => 'Twitter Access Token',
        ),
    'twitter_oauth_secret' => array( 
        'name' => 'twitter_oauth_secret',
        'desc' => 'Twitter Access Token Secret',
        'type' => 'textfield',
        'options' => '',
        'value' => '',
        'lexicon' => 'notify',
        'area' => 'notify',
        'desc_trans' => 'Twitter Access Token Secret',
        ),

);

return $properties;

