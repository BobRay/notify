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
    'area' => '',
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
    'area' => '',
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
    'area' => '',
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
    'area' => '',
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
    'area' => '',
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
    'area' => '',
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
    'area' => '',
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
    'area' => '',
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
    'area' => '',
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
    'area' => '',
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
    'area' => '',
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
    'area' => '',
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
    'area' => '',
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
    'area' => '',
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
    'area' => '',
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
    'area' => '',
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
    'area' => '',
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
    'area' => '',
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
    'area' => '',
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
    'area' => '',
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
    'area' => '',
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
    'area' => '',
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
    'area' => '',
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
    'area' => '',
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
    'area' => '',
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
    'area' => '',
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
    'area' => '',
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
    'area' => '',
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
    'area' => '',
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
    'area' => '',
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
    'area' => '',
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
    'area' => '',
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
    'area' => '',
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
    'area' => '',
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
    'area' => '',
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
    'area' => '',
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
    'area' => '',
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
    'area' => '',
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
    'area' => '',
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
    'area' => '',
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
    'area' => '',
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
    'area' => '',
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
    'area' => '',
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
    'area' => '',
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
    'area' => '',
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
    'area' => '',
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
    'area' => '',
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
        'value' => 'none',
        'text' => 'None (no shortening)',
        'name' => 'None (no shortening)',
      ),
      1 => 
      array (
        'value' => 'bitly',
        'text' => 'bit.ly',
        'name' => 'bit.ly',
      ),
      2 => 
      array (
        'value' => 'tinyurl',
        'text' => 'TinyUrl',
        'name' => 'TinyUrl',
      ),
      3 => 
      array (
        'value' => 'toly',
        'text' => 'to.ly',
        'name' => 'to.ly',
      ),
      4 => 
      array (
        'value' => 'googl',
        'text' => 'goo.gl (Google)',
        'name' => 'goo.gl (Google)',
      ),
      5 => 
      array (
        'value' => 'isgd',
        'text' => 'is.gd',
        'name' => 'is.gd',
      ),
      6 => 
      array (
        'value' => 'supr',
        'text' => 'su.pr (StumbleUpon)',
        'name' => 'su.pr (StumbleUpon)',
      ),
    ),
    'value' => 'none',
    'lexicon' => 'notify:properties',
    'area' => '',
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
    'area' => '',
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
    'area' => '',
  ),
);

return $properties;

