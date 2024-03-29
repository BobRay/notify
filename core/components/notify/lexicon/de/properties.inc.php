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
 * Properties (property descriptions) Lexicon Topic -- de
 * Translation by Jo Lichter
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
$_lang['nf.test_email_address_desc'] = ' (optional) Email address or username for single email. Default: empty';
$_lang['nf.sortby_desc'] = ' (optional) Field to sort by when selecting users; default: username';
$_lang['nf.userClass_desc'] = ' (optional) class of the user object. Only necessary if you have subclassed the user object. Default: modUser';
$_lang['nf.sortby_alias_desc'] = ' (optional) class of the user object. Only necessary if you have subclassed the user object. Default: modUser';
$_lang['nf.profile_desc'] = ' (optional) class of the user profile object. Only necessary if you have subclassed the user profile object. Default: modUserProfile';
$_lang['nf.profile_class_desc'] = ' (optional) class of the user profile object. Only necessary if you have subclassed the user profile object. Default: modUser';
$_lang['nf.batch_size_desc'] = ' (optional) Batch size for bulk email to subscribers. Default: 50';
$_lang['nf.batch_delay_desc'] = ' (optional) Delay between batches in seconds. Default: 1';
$_lang['nf.item_delay_desc'] = ' (optional) Delay between individual emails in seconds. Default: .51';
$_lang['nf.pref_list_chunk_name_desc'] = ' (optional) Chunk to use for preferences (tags) list. Default: sbsPrefListTpl';
$_lang['nf.require_all_tags_default_desc'] = ' (optional) sets the default value of the Require All Tags checkbox; if set, only users who have all tags will receive email; default: No';
$_lang['nf.unsubscribe_tpl_desc'] = 'Name of chunk to use for Unsubscribe link.';


/* Used in properties.notify.snippet.php */
$_lang['nf.additional_headers_desc'] = 'JSON string specifying custom headers (do not use for regular mailfields: cc, bcc, to, from, reply-to). Example: {"X-header1":"someValue","X-header2":"someOtherValue"}';
$_lang['nf.mail_service_desc'] = 'Mail Service to use for sending mail';
$_lang['mailgun.debug_desc'] = 'Write Mailgun debugging information to the MODX Error log.';
$_lang['mailgun.api_key_desc'] = 'Mailgun API key. Available on your Mailgun Dashboard.';
$_lang['mailgun.domain_desc'] = 'Mailgun domain (sub-account) for sending from at Mailgun. Must be set up at Mailgun and verified. Also requires SPF and DKIM records on your server (and optionally CNAM record for tracking).';
$_lang['mailgun.public_api_key_desc'] = 'Optional. Used only for utility to verify emails. Not necessary to send emails.';
$_lang['mailgun.sandbox_domain_desc'] = 'Sandbox domain at Mailgun for testing. Must be set up at Mailgun.';
$_lang['mailgun.use_mailgun_desc'] = 'Deprecated - use MailService Property';
$_lang['mailgun.use_sandbox_desc'] = 'Use Mailgun Sandbox domain for testing rather than sending emails. Requires mailgun.sandbox_domain property to be set and Sandobox domain to be set up at Mailgun.';
$_lang['nf_allowed_groups_desc'] = 'Comma-separated list of groups allowed to use Notify; default:Administrator';
$_lang['nf_inject_unsubscribe_url_desc'] = 'If set, adds an unsubscribe/manage preferences link to every email; default: Yes. Be aware that if you send bulk emails, such a link is required by USA law.';
$_lang['nf_testMode_desc'] = 'Test mode -- Notify functions normally, but no emails are sent.';
$_lang['include_tv_list_property_desc'] = 'Comma-separated list of TV names. Only TVs on the list will have their placeholders set.';
$_lang['include_tvs_property_desc'] = 'If set, placeholders will be set for Resource TVs; default: No.';
$_lang['use_extended_fields_property_desc'] = 'If set, placeholders will be set from the extended fields of the User Profile; default: No';
$_lang['debug_property_desc'] = 'Set to Yes to output debugging information';
$_lang['maxlogs_property_desc'] = 'Set this to limit the number of email logs kept. The oldest one will be deleted. Set to 0 for unlimited logs. Default: 5';
$_lang['nf.process_tvs_desc'] = 'If set to No, the raw values of the TVs will used. Default: Yes.';
$_lang['nf.group_list_chunk_name_desc'] = 'Specifies the chunk that will be used for the buttons under the Groups input in the form; default: sbsGroupListTpl';
