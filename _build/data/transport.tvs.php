<?php

$templateVariables = array();

$templateVariables[1] = $modx->newObject('modTemplateVar');
$templateVariables[1] ->fromArray(array(
    'id' => 1,
    'type' => 'option',
    'name' => 'nf_preview_email',
    'caption' => 'Preview Email',
    'description' => 'Show preview of email when resource is previewed.',
    'elements' => 'Yes==Yes||No==No',
    'rank' => '0',
    'display' => 'default',
    'default_text' => 'No',
    'properties' => '',
    'input_properties' => '{"allowBlank":"true","columns":"1"}',
    'output_properties' => '',
),'',true,true);
$templateVariables[2] = $modx->newObject('modTemplateVar');
$templateVariables[2] ->fromArray(array(
    'id' => 2,
    'type' => 'option',
    'name' => 'nf_notify_subscribers',
    'caption' => 'Notify Subscribers',
    'description' => 'Send notification email to subscribers',
    'elements' => 'Yes==Yes||No==No',
    'rank' => '1',
    'display' => 'default',
    'default_text' => 'No',
    'properties' => '',
    'input_properties' => '{"allowBlank":"true","columns":"1"}',
    'output_properties' => '',
),'',true,true);
$templateVariables[3] = $modx->newObject('modTemplateVar');
$templateVariables[3] ->fromArray(array(
    'id' => 3,
    'type' => 'option',
    'name' => 'nf_send_test_email',
    'caption' => 'Send Test Email',
    'description' => 'Send a test email of the resource when it is previewed.',
    'elements' => 'Yes==Yes||No==No',
    'rank' => '2',
    'display' => 'default',
    'default_text' => 'No',
    'properties' => '',
    'input_properties' => '{"allowBlank":"true","columns":"1"}',
    'output_properties' => '',
),'',true,true);
$templateVariables[4] = $modx->newObject('modTemplateVar');
$templateVariables[4] ->fromArray(array(
    'id' => 4,
    'type' => 'text',
    'name' => 'nf_email_subject',
    'caption' => 'Email Subject',
    'description' => 'Subject for subscriber email',
    'elements' => '',
    'rank' => '3',
    'display' => 'default',
    'default_text' => '',
    'properties' => '',
    'input_properties' => '{"allowBlank":"true","maxLength":"","minLength":""}',
    'output_properties' => '',
),'',true,true);
$templateVariables[5] = $modx->newObject('modTemplateVar');
$templateVariables[5] ->fromArray(array(
    'id' => 5,
    'type' => 'text',
    'name' => 'nf_groups',
    'caption' => 'Groups',
    'description' => 'Comma-separated list of user groups to send to',
    'elements' => '',
    'rank' => '4',
    'display' => 'default',
    'default_text' => 'Subscribers',
    'properties' => '',
    'input_properties' => '{"allowBlank":"true","maxLength":"","minLength":""}',
    'output_properties' => '',
),'',true,true);
$templateVariables[6] = $modx->newObject('modTemplateVar');
$templateVariables[6] ->fromArray(array(
    'id' => 6,
    'type' => 'text',
    'name' => 'nf_tags',
    'caption' => 'Tags',
    'description' => 'Comma-separated list of tags',
    'elements' => '',
    'rank' => '5',
    'display' => 'default',
    'default_text' => '',
    'properties' => '',
    'input_properties' => '{"allowBlank":"true","maxLength":"","minLength":""}',
    'output_properties' => '',
),'',true,true);
$templateVariables[7] = $modx->newObject('modTemplateVar');
$templateVariables[7] ->fromArray(array(
    'id' => 7,
    'type' => 'textarea',
    'name' => 'nf_subscriber_email',
    'caption' => 'Subscriber Email',
    'description' => 'Notify users of updates',
    'elements' => '',
    'rank' => '6',
    'display' => 'default',
    'default_text' => '',
    'properties' => '',
    'input_properties' => '{"allowBlank":"true"}',
    'output_properties' => '',
),'',true,true);
$templateVariables[8] = $modx->newObject('modTemplateVar');
$templateVariables[8] ->fromArray(array(
    'id' => 8,
    'type' => 'text',
    'name' => 'nf_email_address_for_test',
    'caption' => 'Email Address For Test',
    'description' => 'Email address to send test email to.',
    'elements' => '',
    'rank' => '6',
    'display' => 'default',
    'default_text' => '',
    'properties' => '',
    'input_properties' => '{"allowBlank":"true","maxLength":"","minLength":""}',
    'output_properties' => '',
),'',true,true);
$templateVariables[9] = $modx->newObject('modTemplateVar');
$templateVariables[9] ->fromArray(array(
    'id' => 9,
    'type' => 'option',
    'name' => 'nf_twitter',
    'caption' => 'Notify Twitter',
    'description' => 'Send a tweet',
    'elements' => 'Yes==Yes||No==No',
    'rank' => '8',
    'display' => 'default',
    'default_text' => 'No',
    'properties' => '',
    'input_properties' => '{"allowBlank":"true","columns":"1"}',
    'output_properties' => '',
),'',true,true);
$templateVariables[10] = $modx->newObject('modTemplateVar');
$templateVariables[10] ->fromArray(array(
    'id' => 10,
    'type' => 'text',
    'name' => 'nf_tweet',
    'caption' => 'Tweet',
    'description' => 'Tweet to send',
    'elements' => '',
    'rank' => '9',
    'display' => 'default',
    'default_text' => '',
    'properties' => '',
    'input_properties' => '{"allowBlank":"true","maxLength":"","minLength":""}',
    'output_properties' => '',
),'',true,true);
return $templateVariables;