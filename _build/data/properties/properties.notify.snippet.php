<?php
/**
 * Properties file for Notify snippet
 *
 * Copyright 2013-2015 by Bob Ray <http://bobsguides.com>
 * Created on 02-17-2014
 *
 * @package notify
 * @subpackage build
 */




$properties = array (
  'allowedGroups' => 
  array (
    'name' => 'allowedGroups',
    'desc' => 'nf_allowed_groups_desc',
    'type' => 'textfield',
    'options' => 
    array (
    ),
    'value' => 'Administrator',
    'lexicon' => 'notify:properties',
    'area' => 'Basic Settings',
  ),
  'batchDelay' => 
  array (
    'name' => 'batchDelay',
    'desc' => 'nf.batch_delay_desc',
    'type' => 'textfield',
    'options' => 
    array (
    ),
    'value' => '1',
    'lexicon' => 'notify:properties',
    'area' => 'Basic Settings',
  ),
  'batchSize' => 
  array (
    'name' => 'batchSize',
    'desc' => 'nf.batch_size_desc',
    'type' => 'textfield',
    'options' => 
    array (
    ),
    'value' => '25',
    'lexicon' => 'notify:properties',
    'area' => 'Basic Settings',
  ),
  'debug' => 
  array (
    'name' => 'debug',
    'desc' => 'debug_property_desc',
    'type' => 'combo-boolean',
    'options' => 
    array (
    ),
    'value' => false,
    'lexicon' => 'notify:properties',
    'area' => 'Basic Settings',
  ),
  'groupListChunkName' => 
  array (
    'name' => 'groupListChunkName',
    'desc' => 'nf.group_list_chunk_name_desc',
    'type' => 'textfield',
    'options' => 
    array (
    ),
    'value' => 'sbsGroupListTpl',
    'lexicon' => 'notify:properties',
    'area' => 'Basic Settings',
  ),
  'groups' => 
  array (
    'name' => 'groups',
    'desc' => 'nf.groups_desc',
    'type' => 'textfield',
    'options' => 
    array (
    ),
    'value' => 'Subscribers',
    'lexicon' => 'notify:properties',
    'area' => 'Basic Settings',
  ),
  'includeTVList' => 
  array (
    'name' => 'includeTVList',
    'desc' => 'include_tv_list_property_desc',
    'type' => 'textfield',
    'options' => 
    array (
    ),
    'value' => '',
    'lexicon' => 'notify:properties',
    'area' => 'Basic Settings',
  ),
  'includeTVs' => 
  array (
    'name' => 'includeTVs',
    'desc' => 'include_tvs_property_desc',
    'type' => 'combo-boolean',
    'options' => 
    array (
    ),
    'value' => false,
    'lexicon' => 'Notify:Properties',
    'area' => 'Basic Settings',
  ),
  'injectUnsubscribeUrl' => 
  array (
    'name' => 'injectUnsubscribeUrl',
    'desc' => 'nf_inject_unsubscribe_url_desc',
    'type' => 'combo-boolean',
    'options' => 
    array (
    ),
    'value' => true,
    'lexicon' => 'notify:properties',
    'area' => 'Basic Settings',
  ),
  'itemDelay' => 
  array (
    'name' => 'itemDelay',
    'desc' => 'nf.item_delay_desc',
    'type' => 'textfield',
    'options' => 
    array (
    ),
    'value' => '.51',
    'lexicon' => 'notify:properties',
    'area' => 'Basic Settings',
  ),
  'mailService' => 
  array (
    'name' => 'mailService',
    'desc' => 'nf.mail_service_desc',
    'type' => 'list',
    'options' => 
    array (
      0 => 
      array (
        'text' => 'modMailX',
        'value' => 'modMailX',
        'name' => 'modMailX',
      ),
      1 => 
      array (
        'text' => 'MailgunX',
        'value' => 'MailgunX',
        'name' => 'MailgunX',
      ),
      2 => 
      array (
        'text' => 'MandrillX',
        'value' => 'MandrillX',
        'name' => 'MandrillX',
      ),
    ),
    'value' => 'modMailX',
    'lexicon' => 'notify:properties',
    'area' => 'Basic Settings',
  ),
  'maxLogs' => 
  array (
    'name' => 'maxLogs',
    'desc' => 'maxlogs_property_desc',
    'type' => 'textfield',
    'options' => 
    array (
    ),
    'value' => '5',
    'lexicon' => 'notify:properties',
    'area' => 'Basic Settings',
  ),
  'nfTestEmailAddress' => 
  array (
    'name' => 'nfTestEmailAddress',
    'desc' => 'nf.test_email_address_desc',
    'type' => 'textfield',
    'options' => 
    array (
    ),
    'value' => '',
    'lexicon' => 'notify:properties',
    'area' => 'Basic Settings',
  ),
  'notifyFacebook' => 
  array (
    'name' => 'notifyFacebook',
    'desc' => 'nf.notify_facebook_desc',
    'type' => 'combo-boolean',
    'options' => 
    array (
    ),
    'value' => false,
    'lexicon' => 'notify:properties',
    'area' => 'Basic Settings',
  ),
  'prefListChunkName' => 
  array (
    'name' => 'prefListChunkName',
    'desc' => 'nf.pref_list_chunk_name_desc',
    'type' => 'textfield',
    'options' => 
    array (
    ),
    'value' => 'sbsPrefListTpl',
    'lexicon' => 'notify:properties',
    'area' => 'Basic Settings',
  ),
  'processTVs' => 
  array (
    'name' => 'processTVs',
    'desc' => 'nf.process_tvs_desc',
    'type' => 'combo-boolean',
    'options' => 
    array (
    ),
    'value' => true,
    'lexicon' => 'notify:properties',
    'area' => 'Basic Settings',
  ),
  'profileAlias' => 
  array (
    'name' => 'profileAlias',
    'desc' => 'nf.profile_desc',
    'type' => 'textfield',
    'options' => 
    array (
    ),
    'value' => 'Profile',
    'lexicon' => 'notify:properties',
    'area' => 'Basic Settings',
  ),
  'profileClass' => 
  array (
    'name' => 'profileClass',
    'desc' => 'nf.profile_class_desc',
    'type' => 'textfield',
    'options' => 
    array (
    ),
    'value' => 'modUserProfile',
    'lexicon' => 'notify:properties',
    'area' => 'Basic Settings',
  ),
  'requireAllTagsDefault' => 
  array (
    'name' => 'requireAllTagsDefault',
    'desc' => 'nf.require_all_tags_default_desc',
    'type' => 'combo-boolean',
    'options' => 
    array (
    ),
    'value' => false,
    'lexicon' => 'notify:properties',
    'area' => 'Basic Settings',
  ),
  'sortBy' => 
  array (
    'name' => 'sortBy',
    'desc' => 'nf.sortby_desc',
    'type' => 'textfield',
    'options' => 
    array (
    ),
    'value' => 'username',
    'lexicon' => 'notify:properties',
    'area' => 'Basic Settings',
  ),
  'sortByAlias' => 
  array (
    'name' => 'sortByAlias',
    'desc' => 'nf.sortby_alias_desc',
    'type' => 'textfield',
    'options' => 
    array (
    ),
    'value' => 'modUser',
    'lexicon' => 'notify:properties',
    'area' => 'Basic Settings',
  ),
  'tags' => 
  array (
    'name' => 'tags',
    'desc' => 'nf.tags_desc',
    'type' => 'textfield',
    'options' => 
    array (
    ),
    'value' => '',
    'lexicon' => 'notify:properties',
    'area' => 'Basic Settings',
  ),
  'testMode' => 
  array (
    'name' => 'testMode',
    'desc' => 'nf_testMode_desc',
    'type' => 'combo-boolean',
    'options' => 
    array (
    ),
    'value' => false,
    'lexicon' => 'notify:properties',
    'area' => 'Basic Settings',
  ),
  'urlShorteningService' => 
  array (
    'name' => 'urlShorteningService',
    'desc' => 'nf.url_shortening_service_desc',
    'type' => 'list',
    'options' => 
    array (
      0 => 
      array (
        'text' => 'None (no shortening)',
        'value' => 'none',
        'name' => 'None (no shortening)',
      ),
      1 => 
      array (
        'text' => 'bit.ly',
        'value' => 'bitly',
        'name' => 'bit.ly',
      ),
      2 => 
      array (
        'text' => 'TinyUrl',
        'value' => 'tinyurl',
        'name' => 'TinyUrl',
      ),
      3 => 
      array (
        'text' => 'to.ly',
        'value' => 'toly',
        'name' => 'to.ly',
      ),
      4 => 
      array (
        'text' => 'goo.gl (Google)',
        'value' => 'googl',
        'name' => 'goo.gl (Google)',
      ),
      5 => 
      array (
        'text' => 'is.gd',
        'value' => 'isgd',
        'name' => 'is.gd',
      ),
      6 => 
      array (
        'text' => 'su.pr (StumbleUpon)',
        'value' => 'supr',
        'name' => 'su.pr (StumbleUpon)',
      ),
    ),
    'value' => 'none',
    'lexicon' => 'notify:properties',
    'area' => 'Basic Settings',
  ),
  'useExtendedFields' => 
  array (
    'name' => 'useExtendedFields',
    'desc' => 'use_extended_fields_property_desc',
    'type' => 'combo-boolean',
    'options' => 
    array (
    ),
    'value' => false,
    'lexicon' => 'notify:properties',
    'area' => 'Basic Settings',
  ),
  'userClass' => 
  array (
    'name' => 'userClass',
    'desc' => 'nf.userClass_desc',
    'type' => 'textfield',
    'options' => 
    array (
    ),
    'value' => 'modUser',
    'lexicon' => 'notify:properties',
    'area' => 'Basic Settings',
  ),
  'additionalHeaders' => 
  array (
    'name' => 'additionalHeaders',
    'desc' => 'nf.additional_headers_desc',
    'type' => 'textfield',
    'options' => 
    array (
    ),
    'value' => '',
    'lexicon' => 'notify:properties',
    'area' => 'Email Settings',
  ),
  'mailFrom' => 
  array (
    'name' => 'mailFrom',
    'desc' => 'nf.mail_from_desc',
    'type' => 'textfield',
    'options' => 
    array (
    ),
    'value' => '',
    'lexicon' => 'notify:properties',
    'area' => 'Email Settings',
  ),
  'mailFromName' => 
  array (
    'name' => 'mailFromName',
    'desc' => 'nf.mail_from_name_desc',
    'type' => 'textfield',
    'options' => 
    array (
    ),
    'value' => '',
    'lexicon' => 'notify:properties',
    'area' => 'Email Settings',
  ),
  'mailReplyTo' => 
  array (
    'name' => 'mailReplyTo',
    'desc' => 'nf.mail_reply_to_desc',
    'type' => 'textfield',
    'options' => 
    array (
    ),
    'value' => '',
    'lexicon' => 'notify:properties',
    'area' => 'Email Settings',
  ),
  'mailSender' => 
  array (
    'name' => 'mailSender',
    'desc' => 'nf.mail_sender_desc',
    'type' => 'textfield',
    'options' => 
    array (
    ),
    'value' => '',
    'lexicon' => 'notify:properties',
    'area' => 'Email Settings',
  ),
  'mailgun.debug' => 
  array (
    'name' => 'mailgun.debug',
    'desc' => 'mailgun.debug_desc',
    'type' => 'combo-boolean',
    'options' => 
    array (
    ),
    'value' => false,
    'lexicon' => 'notify:properties',
    'area' => 'Mailgun',
  ),
  'mailgun.domain' => 
  array (
    'name' => 'mailgun.domain',
    'desc' => 'mailgun.domain_desc',
    'type' => 'textfield',
    'options' => 
    array (
    ),
    'value' => '',
    'lexicon' => 'notify:properties',
    'area' => 'Mailgun',
  ),
  'mailgun.public_api_key' => 
  array (
    'name' => 'mailgun.public_api_key',
    'desc' => 'mailgun.public_api_key_desc',
    'type' => 'textfield',
    'options' => 
    array (
    ),
    'value' => '',
    'lexicon' => 'notify:properties',
    'area' => 'Mailgun',
  ),
  'mailgun.sandbox_domain' => 
  array (
    'name' => 'mailgun.sandbox_domain',
    'desc' => 'mailgun.sandbox_domain_desc',
    'type' => 'textfield',
    'options' => 
    array (
    ),
    'value' => '',
    'lexicon' => 'notify:properties',
    'area' => 'Mailgun',
  ),
  'mailgun.use_mailgun' => 
  array (
    'name' => 'mailgun.use_mailgun',
    'desc' => 'mailgun.use_mailgun_desc',
    'type' => 'combo-boolean',
    'options' => 
    array (
    ),
    'value' => false,
    'lexicon' => 'notify:properties',
    'area' => 'Mailgun',
  ),
  'mailgun.use_sandbox' => 
  array (
    'name' => 'mailgun.use_sandbox',
    'desc' => 'mailgun.use_sandbox_desc',
    'type' => 'combo-boolean',
    'options' => 
    array (
    ),
    'value' => true,
    'lexicon' => 'notify:properties',
    'area' => 'Mailgun',
  ),
  'mailgun_api_key' => 
  array (
    'name' => 'mailgun_api_key',
    'desc' => 'mailgun.api_key_desc',
    'type' => 'textfield',
    'options' => 
    array (
    ),
    'value' => '',
    'lexicon' => 'notify:properties',
    'area' => 'Mailgun',
  ),
  'mandrill_api_key' => 
  array (
    'name' => 'mandrill_api_key',
    'desc' => 'mandrill_api_key_desc',
    'type' => 'textfield',
    'options' => 
    array (
    ),
    'value' => '',
    'lexicon' => 'notify:properties',
    'area' => 'Mandrill',
  ),
  'nfUseMandrill' => 
  array (
    'name' => 'nfUseMandrill',
    'desc' => 'nf.use_mandrill_desc',
    'type' => 'combo-boolean',
    'options' => 
    array (
    ),
    'value' => false,
    'lexicon' => 'notify:properties',
    'area' => 'Mandrill',
  ),
  'subaccount' => 
  array (
    'name' => 'subaccount',
    'desc' => 'subaccount_property_desc',
    'type' => 'textfield',
    'options' => 
    array (
    ),
    'value' => 'test',
    'lexicon' => 'notify:properties',
    'area' => 'Mandrill',
  ),
  'bitlyApiKey' => 
  array (
    'name' => 'bitlyApiKey',
    'desc' => 'nf.bitly_api_key_desc',
    'type' => 'textfield',
    'options' => 
    array (
    ),
    'value' => '',
    'lexicon' => 'notify:properties',
    'area' => 'Notify API Keys',
  ),
  'bitlyUsername' => 
  array (
    'name' => 'bitlyUsername',
    'desc' => 'nf.bitly_username_desc',
    'type' => 'textfield',
    'options' => 
    array (
    ),
    'value' => '',
    'lexicon' => 'notify:properties',
    'area' => 'Notify API Keys',
  ),
  'googleApiKey' => 
  array (
    'name' => 'googleApiKey',
    'desc' => 'nf.google_api_key_desc',
    'type' => 'textfield',
    'options' => 
    array (
    ),
    'value' => '',
    'lexicon' => 'notify:properties',
    'area' => 'Notify API Keys',
  ),
  'suprApiKey' => 
  array (
    'name' => 'suprApiKey',
    'desc' => 'nf.supr_api_key_desc',
    'type' => 'textfield',
    'options' => 
    array (
    ),
    'value' => '',
    'lexicon' => 'notify:properties',
    'area' => 'Notify API Keys',
  ),
  'suprUsername' => 
  array (
    'name' => 'suprUsername',
    'desc' => 'nf.supr_username_desc',
    'type' => 'textfield',
    'options' => 
    array (
    ),
    'value' => '',
    'lexicon' => 'notify:properties',
    'area' => 'Notify API Keys',
  ),
  'tinyurlApiKey' => 
  array (
    'name' => 'tinyurlApiKey',
    'desc' => 'nf.tinyurl_api_key_desc',
    'type' => 'textfield',
    'options' => 
    array (
    ),
    'value' => '',
    'lexicon' => 'notify:properties',
    'area' => 'Notify API Keys',
  ),
  'tinyurlUsername' => 
  array (
    'name' => 'tinyurlUsername',
    'desc' => 'nf.tinyurl_username_desc',
    'type' => 'textfield',
    'options' => 
    array (
    ),
    'value' => '',
    'lexicon' => 'notify:properties',
    'area' => 'Notify API Keys',
  ),
  'twitterConsumerKey' => 
  array (
    'name' => 'twitterConsumerKey',
    'desc' => 'nf.twitter_consumer_key_desc',
    'type' => 'textfield',
    'options' => 
    array (
    ),
    'value' => '',
    'lexicon' => 'notify:properties',
    'area' => 'Notify API Keys',
  ),
  'twitterConsumerSecret' => 
  array (
    'name' => 'twitterConsumerSecret',
    'desc' => 'nf.twitter_consumer_secret_desc',
    'type' => 'textfield',
    'options' => 
    array (
    ),
    'value' => '',
    'lexicon' => 'notify:properties',
    'area' => 'Notify API Keys',
  ),
  'twitterOauthSecret' => 
  array (
    'name' => 'twitterOauthSecret',
    'desc' => 'nf.twitter_oauth_token_secret_desc',
    'type' => 'textfield',
    'options' => 
    array (
    ),
    'value' => '',
    'lexicon' => 'notify:properties',
    'area' => 'Notify API Keys',
  ),
  'twitterOauthToken' => 
  array (
    'name' => 'twitterOauthToken',
    'desc' => 'nf.twitter_access_token_desc',
    'type' => 'textfield',
    'options' => 
    array (
    ),
    'value' => '',
    'lexicon' => 'notify:properties',
    'area' => 'Notify API Keys',
  ),
  'nfEmailTplCustom' => 
  array (
    'name' => 'nfEmailTplCustom',
    'desc' => 'nf.email_tpl_custom_desc',
    'type' => 'textfield',
    'options' => 
    array (
    ),
    'value' => 'NfSubscriberEmailTplCustom',
    'lexicon' => 'notify:properties',
    'area' => 'Notify Tpls',
  ),
  'nfEmailTplExisting' => 
  array (
    'name' => 'nfEmailTplExisting',
    'desc' => 'nf.email_tpl_existing_desc',
    'type' => 'textfield',
    'options' => 
    array (
    ),
    'value' => 'NfSubscriberEmailTplExisting',
    'lexicon' => 'notify:properties',
    'area' => 'Notify Tpls',
  ),
  'nfEmailTplNew' => 
  array (
    'name' => 'nfEmailTplNew',
    'desc' => 'nf.email_tpl_new_desc',
    'type' => 'textfield',
    'options' => 
    array (
    ),
    'value' => 'NfSubscriberEmailTplNew',
    'lexicon' => 'notify:properties',
    'area' => 'Notify Tpls',
  ),
  'nfFormTpl' => 
  array (
    'name' => 'nfFormTpl',
    'desc' => 'nf.form_tpl_desc',
    'type' => 'textfield',
    'options' => 
    array (
    ),
    'value' => 'NfNotifyFormTpl',
    'lexicon' => 'notify:properties',
    'area' => 'Notify Tpls',
  ),
  'nfSubjectTpl' => 
  array (
    'name' => 'nfSubjectTpl',
    'desc' => 'nf.subject_tpl_desc',
    'type' => 'textfield',
    'options' => 
    array (
    ),
    'value' => 'NfEmailSubjectTpl',
    'lexicon' => 'notify:properties',
    'area' => 'Notify Tpls',
  ),
  'nfTweetTplCustom' => 
  array (
    'name' => 'nfTweetTplCustom',
    'desc' => 'nf.tweet_tpl_custom_desc',
    'type' => 'textfield',
    'options' => 
    array (
    ),
    'value' => 'NfTweetTplCustom',
    'lexicon' => 'notify:properties',
    'area' => 'Notify Tpls',
  ),
  'nfTweetTplExisting' => 
  array (
    'name' => 'nfTweetTplExisting',
    'desc' => 'nf.tweet_tpl_existing_desc',
    'type' => 'textfield',
    'options' => 
    array (
    ),
    'value' => 'NfTweetTplExisting',
    'lexicon' => 'notify:properties',
    'area' => 'Notify Tpls',
  ),
  'nfTweetTplNew' => 
  array (
    'name' => 'nfTweetTplNew',
    'desc' => 'nf.tweet_tpl_new_desc',
    'type' => 'textfield',
    'options' => 
    array (
    ),
    'value' => 'NfTweetTplNew',
    'lexicon' => 'notify:properties',
    'area' => 'Notify Tpls',
  ),
  'nfUnsubscribeTpl' => 
  array (
    'name' => 'nfUnsubscribeTpl',
    'desc' => 'nf.unsubscribe_tpl_desc',
    'type' => 'textfield',
    'options' => 
    array (
    ),
    'value' => 'NfUnsubscribeTpl',
    'lexicon' => 'notify:properties',
    'area' => 'Notify Tpls',
  ),
);

return $properties;

