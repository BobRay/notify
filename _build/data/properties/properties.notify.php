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
        'desc' => 'nf.url_shortening_service_desc~~Service used to shorten all URLs in text and Tweet.',
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
        'desc' => 'nf.groups_desc~~Comma-separated list of User Groups to send to (no spaces). The Subscribers group will be set in the form, but if you delete it and submit with the Groups field empty, email will be sent to all users on the site',
        'type' => 'textfield',
        'options' => '',
        'value' => 'Subscribers',
        'lexicon' => 'notify:properties',
        'area' => '',
        ),
    array(
        'name' => 'tags',
        'desc' => 'nf.tags_desc~~ (optional) Comma-separated list of tags (no spaces). If set, only users in specified Groups with the interest(s) set will receive the email.',
        'type' => 'textfield',
        'options' => '',
        'value' => 'Subscribers',
        'lexicon' => 'notify:properties',
        'area' => '',
        ),
    array(
        'name' => 'notify_facebook',
        'desc' => 'nf.notify_facebook_desc~~Notify Facebook via Twitter with #fb in tweet -- must be set up in the Facebook Twitter App.',
        'type' => 'combo-boolean',
        'options' => '',
        'value' => false,
        'lexicon' => 'notify:properties',
        'area' => '',
        ),

    array(
           'name' => 'twitter_consumer_key',
           'desc' => 'nf.twitter_consumer_key_desc~~Twitter Consumer Key',
           'type' => 'textfield',
           'options' => '',
           'value' => '',
           'lexicon' => 'notify:properties',
           'area' => '',
           ),
    array(
           'name' => 'twitter_consumer_secret',
           'desc' => 'nf.twitter_consumer_secret_desc~~Twitter Consumer Secret',
           'type' => 'textfield',
           'options' => '',
           'value' => '',
           'lexicon' => 'notify:properties',
           'area' => '',
           ),
    array(
           'name' => 'twitter_oauth_token',
           'desc' => 'nf.twitter_access_token_desc~~Twitter Access Token',
           'type' => 'textfield',
           'options' => '',
           'value' => '',
           'lexicon' => 'notify:properties',
           'area' => '',
           ),
    array(
           'name' => 'twitter_oauth_secret',
           'desc' => 'nf.twitter_access_token_secret_desc~~Twitter Access Token Secret',
           'type' => 'textfield',
           'options' => '',
           'value' => '',
           'lexicon' => 'notify:properties',
           'area' => '',
           ),

    array(
        'name' => 'bitly_api_key',
        'desc' => 'nf.bitly_api_key_desc~~bit.ly API key (required)',
        'type' => 'textfield',
        'options' => '',
        'value' => '',
        'lexicon' => 'notify:properties',
        'area' => '',
        ),
    array(
        'name' => 'bitly_username',
        'desc' => 'nf.bitly_username_desc~~bit.ly username (required)',
        'type' => 'textfield',
        'options' => '',
        'value' => '',
        'lexicon' => 'notify:properties',
        'area' => '',
    ),
    array(
        'name' => 'google_api_key',
        'desc' => 'nf.google_api_key_desc~~Google API key',
        'type' => 'textfield',
        'options' => '',
        'value' => '',
        'lexicon' => 'notify:properties',
        'area' => '',
        ),
    array(
        'name' => 'supr_api_key',
        'desc' => 'nf.supr_api_key_desc~~StumbleUpon API key (optional)',
        'type' => 'textfield',
        'options' => '',
        'value' => '',
        'lexicon' => 'notify:properties',
        'area' => '',
        ),
    array(
        'name' => 'supr_username',
        'desc' => 'nf.supr_username_desc~~Stumble Upon Username (optional)',
        'type' => 'textfield',
        'options' => '',
        'value' => '',
        'lexicon' => 'notify:properties',
        'area' => '',
        ),
    array(
        'name' => 'tiny_url_api_key',
        'desc' => 'nf.tinyurl_api_key_desc~~TinyUrl API key (optional)',
        'type' => 'textfield',
        'options' => '',
        'value' => '',
        'lexicon' => 'notify:properties',
        'area' => '',
        ),
    array(
        'name' => 'tiny_url_username',
        'desc' => 'nf.tinyurl_username_desc~~TinyUrl username (optional)',
        'type' => 'textfield',
        'options' => '',
        'value' => '',
        'lexicon' => 'notify:properties',
        'area' => '',
        ),

    array(
        'name' => 'mail_from',
        'desc' => 'nf.mail_from_desc~~(optional) MAIL_FROM setting for email. Defaults to emailsender System Setting',
        'type' => 'textfield',
        'options' => '',
        'value' => '',
        'lexicon' => 'notify:properties',
        'area' => '',
    ),
    array(
            'name' => 'mail_from_name',
            'desc' => 'nf.mail_from_name_desc~~(optional) MAIL_FROM_NAME setting for email. Defaults to site_name System Setting.',
            'type' => 'textfield',
            'options' => '',
            'value' => '',
            'lexicon' => 'notify:properties',
            'area' => '',
        ),
    array(
            'name' => 'mail_sender',
            'desc' => 'nf.mail_sender_desc~~(optional) EMAIL_SENDER setting for email. Defaults to emailsender System Setting',
            'type' => 'textfield',
            'options' => '',
            'value' => '',
            'lexicon' => 'notify:properties',
            'area' => '',
        ),
    array(
        'name' => 'mail_reply_to',
        'desc' => 'nf.mail_reply_to_desc~~(optional) REPLY_TO setting for email. Defaults to emailsender System Setting',
        'type' => 'textfield',
        'options' => '',
        'value' => '',
        'lexicon' => 'notify:properties',
        'area' => '',
    ),
    array(
        'name' => 'nfFormTpl',
        'desc' => 'nf.form_tpl_desc~~Name of chunk to use for the Notify form; default: NfNotifyFormTpl',
        'type' => 'textfield',
        'options' => '',
        'value' => 'NfNotifyFormTpl',
        'lexicon' => 'notify:properties',
        'area' => '',
    ),
    array(
        'name' => 'nfEmailTpl',
        'desc' => 'nf.email_tpl_desc~~Name of chunk to use for the Email to send to subscribers; default: NfSubscriberEmailTpl',
        'type' => 'textfield',
        'options' => '',
        'value' => 'NfSubscriberEmailTpl',
        'lexicon' => 'notify:properties',
        'area' => '',
    ),
    array(
        'name' => 'nfTweetTpl',
        'desc' => 'nf.tweet_tpl_desc~~Name of chunk to use for the Tweet text; default: nfTweetTpl',
        'type' => 'textfield',
        'options' => '',
        'value' => 'nfTweetTpl',
        'lexicon' => 'notify:properties',
        'area' => '',
    ),
    array(
        'name' => 'nfSubjectTpl',
        'desc' => 'nf.subject_tpl_desc~~Name of chunk to use for the Email subject; default: NfEmailSubjectTpl',
        'type' => 'textfield',
        'options' => '',
        'value' => 'NfEmailSubjectTpl',
        'lexicon' => 'notify:properties',
        'area' => '',
        ),
    array(
        'name' => 'nf_test_email_address',
        'desc' => 'nf.test_email_address_desc~~ (optional) Email address for test email. Defaults to emailsender System Setting',
        'type' => 'textfield',
        'options' => '',
        'value' => '',
        'lexicon' => 'notify:properties',
        'area' => '',
        ),


    array(
        'name' => 'sortBy',
        'desc' => 'nf.sortby_desc~~ (optional) Field to sort by when selecting users; default: username',
        'type' => 'textfield',
        'options' => '',
        'value' => 'username',
        'lexicon' => 'notify:properties',
        'area' => '',
        ),

    array(
        'name' => 'userClass',
        'desc' => 'nf.userClass_desc~~ (optional) class of the user object. Only necessary if you have subclassed the user object. Default: modUser',
        'type' => 'textfield',
        'options' => '',
        'value' => 'modUser',
        'lexicon' => 'notify:properties',
        'area' => '',
        ),
    array(
        'name' => 'sortByAlias',
        'desc' => 'nf.sortby_alias_desc~~ (optional) class of the user object. Only necessary if you have subclassed the user object. Default: modUser',
        'type' => 'textfield',
        'options' => '',
        'value' => 'modUser',
        'lexicon' => 'notify:properties',
        'area' => '',
        ),

    array(
        'name' => 'profileAlias',
        'desc' => 'nf.profile_desc~~ (optional) class of the user profile object. Only necessary if you have subclassed the user profile object. Default: modUserProfile',
        'type' => 'textfield',
        'options' => '',
        'value' => 'modUserProfile',
        'lexicon' => 'notify:properties',
        'area' => '',
        ),
    array(
        'name' => 'profileClass',
        'desc' => 'nf.profile_class_desc~~ (optional) class of the user profile object. Only necessary if you have subclassed the user profile object. Default: modUser',
        'type' => 'textfield',
        'options' => '',
        'value' => 'modUserProfile',
        'lexicon' => 'notify:properties',
        'area' => '',
        ),
/* batchSize 50, batchDelay 1, itemDelay .51*/
    array(
        'name' => 'batchSize',
        'desc' => 'nf.batch_size_desc~~ (optional) Batch size for bulk email to subscribers. Default: 50',
        'type' => 'textfield',
        'options' => '',
        'value' => '50',
        'lexicon' => 'notify:properties',
        'area' => '',
        ),
    array(
        'name' => 'batchDelay',
        'desc' => 'nf.batch_delay_desc~~ (optional) Delay between batches in seconds. Default: 1',
        'type' => 'textfield',
        'options' => '',
        'value' => '1',
        'lexicon' => 'notify:properties',
        'area' => '',
        ),

    array(
        'name' => 'itemDelay',
        'desc' => 'nf.item_delay_desc~~ (optional) Delay between individual emails in seconds. Default: .51',
        'type' => 'textfield',
        'options' => '',
        'value' => '.51',
        'lexicon' => 'notify:properties',
        'area' => '',
        ),

);


return $properties;

