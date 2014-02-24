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
 * Default Lexicon Topic
 *
 * @package notify
 * @subpackage lexicon
 */

/* notify default strings */

$_lang['notify'] = 'Notify';
$_lang['nf.unauthorized_access'] = 'Unauthorized Access -- must be a member of Administrator group';
$_lang['nf.could_not_find_preview_page'] = 'Could not find NotifyPreview page';
$_lang['nf_page_id_is_empty'] = '$_POST pageId is empty';
$_lang['nf.could_not_get_resource'] = 'Could not get resource';
$_lang['nf.could_not_find_email_tpl_chunk'] = 'Could not find email Tpl chunk';
$_lang['nf.could_not_find_tweet_tpl_chunk'] = 'Could not find tweet Tpl chunk';
$_lang['nf.no_recipients_to_send_to'] = 'No recipients to send to';
$_lang['nf.bulk_emails_not_sent'] = 'Bulk emails not sent';
$_lang['nf.could_not_open_log_file'] = 'Could not open log file (make sure log directory exists)';
$_lang['nf.no_profile_for'] = 'No profile for user';
$_lang['nf.sbs_extended_field_not_set'] = 'sbs_extended_field property not set';
$_lang['nf.has_no_email_address'] = 'has no email address';
$_lang['nf.test_email_address_empty'] = 'Test email address is empty';
$_lang['nf.test_email_not_sent'] = 'Test email not sent';
$_lang['nf.mail_error_sending_test_email'] = 'Error sending test email';
$_lang['nf.test_email_sent_successfully'] = 'Test email sent successfully';
$_lang['nf.twitter_consumer_key_not_set'] = 'Twitter consumer key is not set';
$_lang['nf.twitter_consumer_secret_not_set'] = 'Twitter consumer secret is not set';
$_lang['nf.twitter_access_token_not_set'] = 'Twitter access token is not set';
$_lang['nf.twitter_access_token_secret_not_set'] = 'Twitter access token secret is not set';
$_lang['nf.tweet_field_is_empty'] = 'Tweet field is empty';
$_lang['nf.unknown_error_using_twitter_api'] = 'Unknown error using Twitter API';
$_lang['nf.twitter_said_there_was_an_error'] = 'Twitter said there was an error';
$_lang['nf.full_response'] = 'Full response: ';
$_lang['nf.email_to_subscribers_sent_successfully'] = 'Email to Subscribers sent successfully to [[+nf_number]] recipients';
$_lang['nf.tweet_sent_successfully'] = 'Tweet sent successfully';


/* Used in notify.class.php */
$_lang['nf.no_mandrill_api_key'] = 'No Mandrill API Key';
$_lang['nf.no_mandrill'] = 'Could not instantiate Mandrill object';
$_lang['nf.user_not_found'] = 'User Not Found';
$_lang['nf.sending_batch_of'] = 'Sending Batch of';
$_lang['nf.no_messages_sent'] = 'No Messages Sent';
$_lang['nf.send_user_mandrill'] = 'Sending to user (Mandrill)';




/* Used in nfnotifyformtpl.chunk.html */
$_lang['nf.notify_subscribers'] = '';
$_lang['nf.send_test_email'] = '';
$_lang['nf.email_address_for_test'] = '';
$_lang['nf.email_subject'] = '';
$_lang['nf.groups'] = '';
$_lang['nf.tags'] = '';
$_lang['nf.require_all_tags'] = '';
$_lang['nf.text_for_email'] = '';
$_lang['nf.email_preview'] = '';
$_lang['nf.notify_twitter'] = '';
$_lang['nf.send_tweet'] = '';
$_lang['nf.text_for_tweet'] = '';