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
    array(
        'name' => 'url_shortening_service',
        'desc' => 'Service used to shorten all URLs in text and tweet. ',
        'type' => 'list',
        'options' => array(
            array(
                'name' => 'None (no shortening)',
                'value' => 'none',


            ),
            array(
                'name' => 'bit.ly',
                'value' => 'bitly',
             ),
            array(
                'name' => 'TinyUrl',
                'value' => 'tinyurl',
             ),
            array(
                'name' => 'to.ly',
                'value' => 'toly',
            ),
            array(
                'name' => 'goo.gl (Google)',
                'value' => 'googl',
            ),
            array(
                'name' => 'is.gd',
                'value' => 'isgd',
            ),
            array(
                'name' => 'su.pr (StumbleUpon)',
                'value' => 'supr',

            ),
        ),
        'value' => 'none',
        'lexicon' => 'notify:properties',
        'area'=> '',
        ),
    array(
        'name' => 'notify_facebook',
        'desc' => 'Notify Facebook via Twitter with #fb in tweet -- must be set up at Twitter.com.',
        'type' => 'combo-boolean',
        'options' => '',
        'value' => false,
        'lexicon' => 'notify:properties',
        'area' => '',
        ),

    array(
           'name' => 'twitter_consumer_key',
           'desc' => 'Twitter Consumer Key',
           'type' => 'textfield',
           'options' => '',
           'value' => '',
           'lexicon' => 'notify:properties',
           'area' => '',
           ),
    array(
           'name' => 'twitter_consumer_secret',
           'desc' => 'Twitter Consumer Secret',
           'type' => 'textfield',
           'options' => '',
           'value' => '',
           'lexicon' => 'notify:properties',
           'area' => '',
           ),
    array(
           'name' => 'twitter_oath_token',
           'desc' => 'Twitter Access Token',
           'type' => 'textfield',
           'options' => '',
           'value' => '',
           'lexicon' => 'notify:properties',
           'area' => '',
           ),
    array(
           'name' => 'twitter_oauth_secret',
           'desc' => 'Twitter Access Token Secret',
           'type' => 'textfield',
           'options' => '',
           'value' => '',
           'lexicon' => 'notify:properties',
           'area' => '',
           ),

    array(
        'name' => 'bitly_api_key',
        'desc' => 'bit.ly API key (required)',
        'type' => 'textfield',
        'options' => '',
        'value' => '',
        'lexicon' => 'notify:properties',
        'area' => '',
        ),
    array(
        'name' => 'bitly_username',
        'desc' => 'bit.ly username (required)',
        'type' => 'textfield',
        'options' => '',
        'value' => '',
        'lexicon' => 'notify:properties',
        'area' => '',
    ),
    array(
        'name' => 'google_api_key',
        'desc' => 'Google API key',
        'type' => 'textfield',
        'options' => '',
        'value' => '',
        'lexicon' => 'notify:properties',
        'area' => '',
        ),
    array(
        'name' => 'supr_api_key',
        'desc' => 'StumbleUpon API key (optional',
        'type' => 'textfield',
        'options' => '',
        'value' => '',
        'lexicon' => 'notify:properties',
        'area' => '',
        ),
    array(
        'name' => 'supr_username',
        'desc' => 'Stumble Upon Username (optional)',
        'type' => 'textfield',
        'options' => '',
        'value' => '',
        'lexicon' => 'notify:properties',
        'area' => '',
        ),
    array(
        'name' => 'tiny_url_api_key',
        'desc' => 'TinyUrl API key (optional)',
        'type' => 'textfield',
        'options' => '',
        'value' => '',
        'lexicon' => 'notify:properties',
        'area' => '',
        ),
    array(
        'name' => 'tiny_url_username',
        'desc' => 'TinyUrl username (optional)',
        'type' => 'textfield',
        'options' => '',
        'value' => '',
        'lexicon' => 'notify:properties',
        'area' => '',
        ),
);

return $properties;

