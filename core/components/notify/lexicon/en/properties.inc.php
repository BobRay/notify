<?php
/**
 * Notify
 *
 *
 *
 * Notify is free software; you can redistribute it and/or modify it
 * under the terms of the GNU General Public License as published by the Free
 * Software Foundation; either version 2 of the License, or (at your option) any
 * later version.
 *
 * Notify is distributed in the hope that it will be useful, but WITHOUT ANY
 * WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR
 * A PARTICULAR PURPOSE. See the GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License along with
 * Notify; if not, write to the Free Software Foundation, Inc., 59 Temple Place,
 * Suite 330, Boston, MA 02111-1307 USA
 *
 * @package notify
 */
/**
 * Properties (property descriptions) Lexicon Topic
 *
 * @package notify
 * @subpackage lexicon
 */

/* Notify Property Description strings */

/* Lexicon Strings in: properties.notify.php */

$_lang['nf.url_shortening_service_desc'] = 'Service used to shorten all URLs in text and Tweet.';
$_lang['nf.groups_desc'] = 'Comma-separated list of User Groups to send to (no spaces). The Subscribers group will be set in the form, but if you delete it and submit with the Groups field empty, email will be sent to all users on the site';
$_lang['nf.tags_desc'] = ' (optional) Comma-separated list of tags (no spaces). If set, only users in specified Groups with the interest(s) set will receive the email.';
$_lang['nf.notify_facebook_desc'] = 'Notify Facebook via Twitter with #fb in tweet -- must be set up in the Facebook Twitter App.';
$_lang['nf.twitter_consumer_key_desc'] = 'Twitter Consumer Key';
$_lang['nf.twitter_consumer_secret_desc'] = 'Twitter Consumer Secret';
$_lang['nf.twitter_access_token_desc'] = 'Twitter Access Token';
$_lang['nf.twitter_oauth_token_secret_desc'] = 'Twitter Access Token Secret';
$_lang['nf.bitly_api_key_desc'] = 'bit.ly API key (required)';
$_lang['nf.bitly_username_desc'] = 'bit.ly username (required)';
$_lang['nf.google_api_key_desc'] = 'Google API key';
$_lang['nf.supr_api_key_desc'] = 'StumbleUpon API key (optional)';
$_lang['nf.supr_username_desc'] = 'Stumble Upon Username (optional)';
$_lang['nf.tinyurl_api_key_desc'] = 'TinyUrl API key (optional)';
$_lang['nf.tinyurl_username_desc'] = 'TinyUrl username (optional)';
$_lang['nf.mail_from_desc'] = '(optional) MAIL_FROM setting for email. Default: emailsender System Setting';
$_lang['nf.mail_from_name_desc'] = '(optional) MAIL_FROM_NAME setting for email. Default: site_name System Setting.';
$_lang['nf.mail_sender_desc'] = '(optional) EMAIL_SENDER setting for email. Default: emailsender System Setting';
$_lang['nf.mail_reply_to_desc'] = '(optional) REPLY_TO setting for email. Default: emailsender System Setting';
$_lang['nf.form_tpl_desc'] = 'Name of chunk to use for the Notify form; default: NfNotifyFormTpl';
$_lang['nf.email_tpl_new_desc'] = 'Name of chunk to use for the new resource Notify email; default: NfSubscriberEmailTplNew';
$_lang['nf.email_tpl_existing_desc'] = 'Name of chunk to use for updated resource Notify email; default: NfSubscriberEmailTplExisting';
$_lang['nf.email_tpl_custom_desc'] = 'Name of chunk to use for custom Notify email Tpl; default: NfSubscriberEmailTplCustom';
$_lang['nf.tweet_tpl_new_desc'] = 'Name of chunk to use for the new resource Tweet text; default: nfTweetTplNew';
$_lang['nf.tweet_tpl_existing_desc'] = 'Name of chunk to use for the updated resource Tweet text; default: nfTweetTplExisting';
$_lang['nf.tweet_tpl_custom_desc'] = 'Name of chunk to use for the custom Tweet text; default: nfTweetTplCustom';
$_lang['nf.subject_tpl_desc'] = 'Name of chunk to use for the Email subject; default: NfEmailSubjectTpl';
$_lang['nf.test_email_address_desc'] = ' (optional) Email address for test email. Default: emailsender System Setting';
$_lang['nf.sortby_desc'] = ' (optional) Field to sort by when selecting users; default: username';
$_lang['nf.userClass_desc'] = ' (optional) class of the user object. Only necessary if you have subclassed the user object. Default: modUser';
$_lang['nf.sortby_alias_desc'] = ' (optional) class of the user object. Only necessary if you have subclassed the user object. Default: modUser';
$_lang['nf.profile_desc'] = ' (optional) class of the user profile object. Only necessary if you have subclassed the user profile object. Default: modUserProfile';
$_lang['nf.profile_class_desc'] = ' (optional) class of the user profile object. Only necessary if you have subclassed the user profile object. Default: modUser';
$_lang['nf.batch_size_desc'] = ' (optional) Batch size for bulk email to subscribers. Default: 50';
$_lang['nf.batch_delay_desc'] = ' (optional) Delay between batches in seconds. Default: 1';
$_lang['nf.item_delay_desc'] = ' (optional) Delay between individual emails in seconds. Default: .51';
$_lang['nf.pref_list_chunk_name_desc'] = ' (optional) Chunk to use for preferences (tags) list. Default: sbsPrefListTpl';
