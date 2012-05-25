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
    'type' => 'text',
    'name' => 'nf_groups',
    'caption' => 'Groups',
    'description' => 'Comma-separated list of user groups to send to',
    'elements' => '',
    'rank' => '4',
    'display' => 'default',
    'default_text' => 'Subscribers',
    'properties' => '',
    'input_properties' => 'a:3:{s:10:"allowBlank";s:4:"true";s:9:"maxLength";s:0:"";s:9:"minLength";s:0:"";}',
    'output_properties' => 'a:0:{}',
),'',true,true);
$templatevars[2] = $modx->newObject('modTemplateVar');
$templatevars[2] ->fromArray(array(
    'id' => 2,
    'property_preprocess' => '0',
    'type' => 'text',
    'name' => 'nf_tags',
    'caption' => 'Tags',
    'description' => 'Comma-separated list of tags',
    'elements' => '',
    'rank' => '5',
    'display' => 'default',
    'default_text' => '',
    'properties' => '',
    'input_properties' => 'a:3:{s:10:"allowBlank";s:4:"true";s:9:"maxLength";s:0:"";s:9:"minLength";s:0:"";}',
    'output_properties' => 'a:0:{}',
),'',true,true);
$templatevars[3] = $modx->newObject('modTemplateVar');
$templatevars[3] ->fromArray(array(
    'id' => 3,
    'property_preprocess' => '0',
    'type' => 'option',
    'name' => 'nf_notify_subscribers',
    'caption' => 'Notify Subscribers',
    'description' => 'Send notification email to subscribers',
    'elements' => 'Yes==Yes||No==No',
    'rank' => '1',
    'display' => 'default',
    'default_text' => 'No',
    'properties' => '',
    'input_properties' => 'a:2:{s:10:"allowBlank";s:4:"true";s:7:"columns";s:1:"1";}',
    'output_properties' => 'a:0:{}',
),'',true,true);
$templatevars[4] = $modx->newObject('modTemplateVar');
$templatevars[4] ->fromArray(array(
    'id' => 4,
    'property_preprocess' => '0',
    'type' => 'textarea',
    'name' => 'nf_subscriber_email',
    'caption' => 'Subscriber Email',
    'description' => 'Text of message for email',
    'elements' => '',
    'rank' => '6',
    'display' => 'default',
    'default_text' => '',
    'properties' => '',
    'input_properties' => 'a:1:{s:10:"allowBlank";s:4:"true";}',
    'output_properties' => 'a:0:{}',
),'',true,true);
$templatevars[5] = $modx->newObject('modTemplateVar');
$templatevars[5] ->fromArray(array(
    'id' => 5,
    'property_preprocess' => '0',
    'type' => 'option',
    'name' => 'nf_twitter',
    'caption' => 'Notify Twitter',
    'description' => 'Send a tweet',
    'elements' => 'Yes==Yes||No==No',
    'rank' => '8',
    'display' => 'default',
    'default_text' => 'No',
    'properties' => '',
    'input_properties' => 'a:2:{s:10:"allowBlank";s:4:"true";s:7:"columns";s:1:"1";}',
    'output_properties' => 'a:0:{}',
),'',true,true);
$templatevars[6] = $modx->newObject('modTemplateVar');
$templatevars[6] ->fromArray(array(
    'id' => 6,
    'property_preprocess' => '0',
    'type' => 'text',
    'name' => 'nf_tweet',
    'caption' => 'Tweet',
    'description' => 'Tweet to send',
    'elements' => '',
    'rank' => '9',
    'display' => 'default',
    'default_text' => '',
    'properties' => '',
    'input_properties' => 'a:3:{s:10:"allowBlank";s:4:"true";s:9:"maxLength";s:0:"";s:9:"minLength";s:0:"";}',
    'output_properties' => 'a:0:{}',
),'',true,true);
$templatevars[7] = $modx->newObject('modTemplateVar');
$templatevars[7] ->fromArray(array(
    'id' => 7,
    'property_preprocess' => '0',
    'type' => 'option',
    'name' => 'nf_preview_email',
    'caption' => 'Preview Email',
    'description' => 'Show preview of email when resource is previewed.',
    'elements' => 'Yes==Yes||No==No',
    'rank' => '0',
    'display' => 'default',
    'default_text' => 'No',
    'properties' => '',
    'input_properties' => 'a:2:{s:10:"allowBlank";s:4:"true";s:7:"columns";s:1:"1";}',
    'output_properties' => 'a:0:{}',
),'',true,true);
$templatevars[8] = $modx->newObject('modTemplateVar');
$templatevars[8] ->fromArray(array(
    'id' => 8,
    'property_preprocess' => '0',
    'type' => 'option',
    'name' => 'nf_send_test_email',
    'caption' => 'Send Test Email',
    'description' => 'Send a test email of the resource when it is previewed.',
    'elements' => 'Yes==Yes||No==No',
    'rank' => '2',
    'display' => 'default',
    'default_text' => 'No',
    'properties' => '',
    'input_properties' => 'a:2:{s:10:"allowBlank";s:4:"true";s:7:"columns";s:1:"1";}',
    'output_properties' => 'a:0:{}',
),'',true,true);
$templatevars[9] = $modx->newObject('modTemplateVar');
$templatevars[9] ->fromArray(array(
    'id' => 9,
    'property_preprocess' => '0',
    'type' => 'text',
    'name' => 'nf_email_address_for_test',
    'caption' => 'Email Address For Test',
    'description' => 'Email address to send test email to.',
    'elements' => '',
    'rank' => '7',
    'display' => 'default',
    'default_text' => '',
    'properties' => '',
    'input_properties' => 'a:3:{s:10:"allowBlank";s:4:"true";s:9:"maxLength";s:0:"";s:9:"minLength";s:0:"";}',
    'output_properties' => 'a:0:{}',
),'',true,true);
$templatevars[10] = $modx->newObject('modTemplateVar');
$templatevars[10] ->fromArray(array(
    'id' => 10,
    'property_preprocess' => '0',
    'type' => 'text',
    'name' => 'nf_email_subject',
    'caption' => 'Email Subject',
    'description' => 'Subject for subscriber email',
    'elements' => '',
    'rank' => '3',
    'display' => 'default',
    'default_text' => '',
    'properties' => '',
    'input_properties' => 'a:3:{s:10:"allowBlank";s:4:"true";s:9:"maxLength";s:0:"";s:9:"minLength";s:0:"";}',
    'output_properties' => 'a:0:{}',
),'',true,true);
return $templatevars;
