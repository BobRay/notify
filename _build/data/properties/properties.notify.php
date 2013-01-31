<?php
/**
 * Notify
 *
 * Copyright 2012-2013 by Bob Ray <http://bobsguides.com>
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
* Default properties for the properties.notifyproperties.php snippet
* @author Bob Ray <http://bobsguides.com>
*
* @package notify
* @subpackage build
*/


$properties = array(
    array(
        'name' => 'urlShorteningService',
        'desc' => 'nf.url_shortening_service_desc',
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
        'name' => 'groups',
        'desc' => 'nf.groups_desc',
        'type' => 'textfield',
        'options' => '',
        'value' => 'Subscribers',
        'lexicon' => 'notify:properties',
        'area' => '',
        ),
    array(
        'name' => 'tags',
        'desc' => 'nf.tags_desc',
        'type' => 'textfield',
        'options' => '',
        'value' => '',
        'lexicon' => 'notify:properties',
        'area' => '',
        ),
    array(
        'name' => 'requireAllTagsDefault',
        'desc' => 'nf.require_all_tags_default_desc',
        'type' => 'combo-boolean',
        'options' => '',
        'value' => false,
        'lexicon' => 'notify:properties',
        'area' => '',
    ),
    array(
        'name' => 'notifyFacebook',
        'desc' => 'nf.notify_facebook_desc',
        'type' => 'combo-boolean',
        'options' => '',
        'value' => false,
        'lexicon' => 'notify:properties',
        'area' => '',
        ),

    array(
           'name' => 'twitterConsumerKey',
           'desc' => 'nf.twitter_consumer_key_desc',
           'type' => 'textfield',
           'options' => '',
           'value' => '',
           'lexicon' => 'notify:properties',
           'area' => '',
           ),
    array(
           'name' => 'twitterConsumerSecret',
           'desc' => 'nf.twitter_consumer_secret_desc',
           'type' => 'textfield',
           'options' => '',
           'value' => '',
           'lexicon' => 'notify:properties',
           'area' => '',
           ),
    array(
           'name' => 'twitterOauthToken',
           'desc' => 'nf.twitter_access_token_desc',
           'type' => 'textfield',
           'options' => '',
           'value' => '',
           'lexicon' => 'notify:properties',
           'area' => '',
           ),
    array(
           'name' => 'twitterOauthSecret',
           'desc' => 'nf.twitter_oauth_token_secret_desc',
           'type' => 'textfield',
           'options' => '',
           'value' => '',
           'lexicon' => 'notify:properties',
           'area' => '',
           ),

    array(
        'name' => 'bitlyApiKey',
        'desc' => 'nf.bitly_api_key_desc',
        'type' => 'textfield',
        'options' => '',
        'value' => '',
        'lexicon' => 'notify:properties',
        'area' => '',
        ),
    array(
        'name' => 'bitlyUsername',
        'desc' => 'nf.bitly_username_desc',
        'type' => 'textfield',
        'options' => '',
        'value' => '',
        'lexicon' => 'notify:properties',
        'area' => '',
    ),
    array(
        'name' => 'googleApiKey',
        'desc' => 'nf.google_api_key_desc',
        'type' => 'textfield',
        'options' => '',
        'value' => '',
        'lexicon' => 'notify:properties',
        'area' => '',
        ),
    array(
        'name' => 'suprApiKey',
        'desc' => 'nf.supr_api_key_desc',
        'type' => 'textfield',
        'options' => '',
        'value' => '',
        'lexicon' => 'notify:properties',
        'area' => '',
        ),
    array(
        'name' => 'suprUsername',
        'desc' => 'nf.supr_username_desc',
        'type' => 'textfield',
        'options' => '',
        'value' => '',
        'lexicon' => 'notify:properties',
        'area' => '',
        ),
    array(
        'name' => 'tinyurlApiKey',
        'desc' => 'nf.tinyurl_api_key_desc',
        'type' => 'textfield',
        'options' => '',
        'value' => '',
        'lexicon' => 'notify:properties',
        'area' => '',
        ),
    array(
        'name' => 'tinyurlUsername',
        'desc' => 'nf.tinyurl_username_desc',
        'type' => 'textfield',
        'options' => '',
        'value' => '',
        'lexicon' => 'notify:properties',
        'area' => '',
        ),

    array(
        'name' => 'mailFrom',
        'desc' => 'nf.mail_from_desc',
        'type' => 'textfield',
        'options' => '',
        'value' => '',
        'lexicon' => 'notify:properties',
        'area' => '',
    ),
    array(
            'name' => 'mailFromName',
            'desc' => 'nf.mail_from_name_desc',
            'type' => 'textfield',
            'options' => '',
            'value' => '',
            'lexicon' => 'notify:properties',
            'area' => '',
        ),
    array(
            'name' => 'mailSender',
            'desc' => 'nf.mail_sender_desc',
            'type' => 'textfield',
            'options' => '',
            'value' => '',
            'lexicon' => 'notify:properties',
            'area' => '',
        ),
    array(
        'name' => 'mailReplyTo',
        'desc' => 'nf.mail_reply_to_desc',
        'type' => 'textfield',
        'options' => '',
        'value' => '',
        'lexicon' => 'notify:properties',
        'area' => '',
    ),
    array(
        'name' => 'nfFormTpl',
        'desc' => 'nf.form_tpl_desc',
        'type' => 'textfield',
        'options' => '',
        'value' => 'NfNotifyFormTpl',
        'lexicon' => 'notify:properties',
        'area' => '',
    ),
    array(
        'name' => 'nfEmailTplNew',
        'desc' => 'nf.email_tpl_new_desc',
        'type' => 'textfield',
        'options' => '',
        'value' => 'NfSubscriberEmailTplNew',
        'lexicon' => 'notify:properties',
        'area' => '',
    ),
    array(
       'name' => 'nfEmailTplExisting',
       'desc' => 'nf.email_tpl_existing_desc',
       'type' => 'textfield',
       'options' => '',
       'value' => 'NfSubscriberEmailTplExisting',
       'lexicon' => 'notify:properties',
       'area' => '',
       ),
    array(
        'name' => 'nfEmailTplCustom',
        'desc' => 'nf.email_tpl_custom_desc',
        'type' => 'textfield',
        'options' => '',
        'value' => 'NfSubscriberEmailTplCustom',
        'lexicon' => 'notify:properties',
        'area' => '',
        ),
    array(
        'name' => 'nfTweetTplNew',
        'desc' => 'nf.tweet_tpl_new_desc',
        'type' => 'textfield',
        'options' => '',
        'value' => 'nfTweetTplNew',
        'lexicon' => 'notify:properties',
        'area' => '',
    ),
    array(
        'name' => 'nfTweetTplExisting',
        'desc' => 'nf.tweet_tpl_existing_desc',
        'type' => 'textfield',
        'options' => '',
        'value' => 'nfTweetTplExisting',
        'lexicon' => 'notify:properties',
        'area' => '',
    ),
    array(
        'name' => 'nfTweetTplCustom',
        'desc' => 'nf.tweet_tpl_custom_desc',
        'type' => 'textfield',
        'options' => '',
        'value' => 'nfTweetTplCustom',
        'lexicon' => 'notify:properties',
        'area' => '',
    ),
    array(
        'name' => 'nfSubjectTpl',
        'desc' => 'nf.subject_tpl_desc',
        'type' => 'textfield',
        'options' => '',
        'value' => 'NfEmailSubjectTpl',
        'lexicon' => 'notify:properties',
        'area' => '',
        ),
    array(
        'name' => 'nfTestEmailAddress',
        'desc' => 'nf.test_email_address_desc',
        'type' => 'textfield',
        'options' => '',
        'value' => '',
        'lexicon' => 'notify:properties',
        'area' => '',
        ),


    array(
        'name' => 'sortBy',
        'desc' => 'nf.sortby_desc',
        'type' => 'textfield',
        'options' => '',
        'value' => 'username',
        'lexicon' => 'notify:properties',
        'area' => '',
        ),

    array(
        'name' => 'userClass',
        'desc' => 'nf.userClass_desc',
        'type' => 'textfield',
        'options' => '',
        'value' => 'modUser',
        'lexicon' => 'notify:properties',
        'area' => '',
        ),
    array(
        'name' => 'sortByAlias',
        'desc' => 'nf.sortby_alias_desc',
        'type' => 'textfield',
        'options' => '',
        'value' => 'modUser',
        'lexicon' => 'notify:properties',
        'area' => '',
        ),

    array(
        'name' => 'profileAlias',
        'desc' => 'nf.profile_desc',
        'type' => 'textfield',
        'options' => '',
        'value' => 'Profile',
        'lexicon' => 'notify:properties',
        'area' => '',
        ),
    array(
        'name' => 'profileClass',
        'desc' => 'nf.profile_class_desc',
        'type' => 'textfield',
        'options' => '',
        'value' => 'modUserProfile',
        'lexicon' => 'notify:properties',
        'area' => '',
        ),

    array(
        'name' => 'batchSize',
        'desc' => 'nf.batch_size_desc',
        'type' => 'textfield',
        'options' => '',
        'value' => '50',
        'lexicon' => 'notify:properties',
        'area' => '',
        ),
    array(
        'name' => 'batchDelay',
        'desc' => 'nf.batch_delay_desc',
        'type' => 'textfield',
        'options' => '',
        'value' => '1',
        'lexicon' => 'notify:properties',
        'area' => '',
        ),

    array(
        'name' => 'itemDelay',
        'desc' => 'nf.item_delay_desc',
        'type' => 'textfield',
        'options' => '',
        'value' => '.51',
        'lexicon' => 'notify:properties',
        'area' => '',
        ),

    array(
        'name' => 'prefListChunkName',
        'desc' => 'nf.pref_list_chunk_name_desc',
        'type' => 'textfield',
        'options' => '',
        'value' => 'sbsPrefListTpl',
        'lexicon' => 'notify:properties',
        'area' => '',
        ),

);

return $properties;

