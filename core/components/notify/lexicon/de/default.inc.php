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
 * Default Lexicon Topic -- de
 * Translation by Jo Lichter
 *
 * @package notify
 * @subpackage lexicon
 *
 */

/* notify default strings */

$_lang['notify'] = 'Notify';
$_lang['nf.unauthorized_access'] = 'Nicht autorisierter Zugriff - du muss Mitglied der Administratorgruppe sein';
$_lang['nf.could_not_find_preview_page'] = 'Benachrichtigungsvorschau-Seite konnte nicht gefunden werden';
$_lang['nf_page_id_is_empty'] = '$_POST pageId ist leer';
$_lang['nf.could_not_get_resource'] = 'Ressource konnte nicht abgerufen werden';
$_lang['nf.could_not_find_email_tpl_chunk'] = 'Konnte email Tpl chunk nicht finden';
$_lang['nf.could_not_find_tweet_tpl_chunk'] = 'Konnte tweet Tpl chunk nicht finden';
$_lang['nf.no_recipients_to_send_to'] = 'Keine Empfänger zum Senden';
$_lang['nf.could_not_open_log_file'] = 'Logdatei konnte nicht geöffnet werden (stellen sicher, dass das Protokollverzeichnis vorhanden ist)';
$_lang['nf.no_profile_for'] = 'Kein Profil für Benutzer';
$_lang['nf.sbs_extended_field_not_set'] = 'sbs_extended_field Eigenschaft nicht festgelegt';
$_lang['nf.twitter_consumer_key_not_set'] = 'Twitter Consumer Key ist nicht gesetzt';
$_lang['nf.twitter_consumer_secret_not_set'] = 'Twitter Consumer Secret ist nicht gesetzt';
$_lang['nf.twitter_access_token_not_set'] = 'Twitter Access Token ist nicht gesetzt';
$_lang['nf.twitter_access_token_secret_not_set'] = 'Twitter Access Token Secret ist nicht gesetzt';
$_lang['nf.tweet_field_is_empty'] = 'Tweet Feld ist leer';
$_lang['nf.unknown_error_using_twitter_api'] = 'Unbekannter Fehler bei der Verwendung von Twitter API';
$_lang['nf.twitter_said_there_was_an_error'] = 'Twitter sagte, es sei ein Fehler aufgetreten';
$_lang['nf.full_response'] = 'Vollständige Antwort: ';
$_lang['nf.email_to_subscribers_sent_successfully'] = 'E-Mail an [[+nf_number]] Abonnenten erfolgreich gesendet';
$_lang['nf.using'] = 'Using';
$_lang['nf.tweet_sent_successfully'] = 'Tweet erfolgreich gesendet';


/* Used in notify.class.php */
$_lang['nf.no_mandrill_api_key'] = 'Kein Mandrill API Key';
$_lang['nf.no_mandrill'] = 'Konnte Mandrill Objekt nicht instanziieren';
$_lang['nf.user_not_found'] = 'Benutzer wurde nicht gefunden';
$_lang['nf.sending_batch_of'] = 'Senden Batch von';
$_lang['nf.no_messages_sent'] = 'Keine Nachrichten gesendet';
$_lang['nf.send_user_mandrill'] = 'Sendet an Benutzer (Mandrill)';
$_lang['nf_status_resource_id_not_set'] = 'nf_status_resource_id ist nicht gesetzt';
$_lang['nf_status_resource_id_bad_resource'] = 'nf_status_resource_id ist auf eine nicht vorhandene Ressource gesetzt';


/* Used in transport.settings.php */
$_lang['setting_nf_status_resource_id'] = 'Notify Status Resource ID';
$_lang['setting_nf_status_resource_id_desc'] = 'ID von Notify Status Ressource';

/* Used in nfsendemail.class.php */
$_lang['nf.processor_nf'] = 'Konnte Mail-Service-Klasse nicht finden:';
$_lang['nf.failed_ms_instantation'] = 'Mail-Dienst konnte nicht instanziiert werden: ';
$_lang['nf.no_mailgun_api_key'] = 'Mailgun API Key nicht gesetzt';
$_lang['nf.no_mailgun_domain'] = 'Mailgun Domain ist nicht gesetzt';
$_lang['nf.no_view_user_permission'] = 'Benutzer hat keine view_user Erlaubnis';
$_lang['nf._no_single_id'] = 'Keine E-Mail oder ID für Single E-Mail';
$_lang['nf.processing_batch'] = 'Batch Bearbeitung: ';
$_lang['nf.users_emailed_in_batch'] = 'E-Mail-Adresse des Benutzers in dieser Batch: ';
$_lang['nf.finished'] = 'Fertig';
$_lang['nf.successful_send_to'] = 'Erfolgreich gesendet an';
$_lang['nf.user_tags'] = 'Benutzer Tags';
$_lang['nf.error_sending_to'] = 'Fehler gesendet an';
$_lang['nf.test_mode_on'] = '(testMode ist an, es werden keine Nachrichten oder Tweets gesendet)';

/* Used in mandrillx.class.php */
$_lang['nf_malformed_header'] = 'Malformed Header';
