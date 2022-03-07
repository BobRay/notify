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
/** Translation by Viktor Matushevskyi (Viktorminator) <https://github.com/Viktorminator> */
/* notify default strings */

$_lang['notify'] = 'Notify';
$_lang['nf.unauthorized_access'] = 'Неавторизированный доступ &dash; должен принадлежать к группе администраторов';
$_lang['nf.could_not_find_preview_page'] = 'Страница NotifyPreview не найдена';
$_lang['nf_page_id_is_empty'] = '$_POST pageId пуст';
$_lang['nf.could_not_get_resource'] = 'Ресурс не получен';
$_lang['nf.could_not_find_email_tpl_chunk'] = 'Не найден шаблонирующий чанк для email';
$_lang['nf.could_not_find_tweet_tpl_chunk'] = 'Не найден шаблонирующий чанк для твиттера';
$_lang['nf.no_recipients_to_send_to'] = 'Нет получателей для отправки';
$_lang['nf.could_not_open_log_file'] = 'Не могу открыть файл логов (убедитесь, что директория логов существует)';
$_lang['nf.no_profile_for'] = 'У пользователя нет профиля';
$_lang['nf.sbs_extended_field_not_set'] = 'Параметр sbs_extended_field не установлен';
$_lang['nf.twitter_consumer_key_not_set'] = 'Ключ пользователя Twitter не установлен';
$_lang['nf.twitter_consumer_secret_not_set'] = 'Секретный ключ пользователя Twitter не установлен';
$_lang['nf.twitter_access_token_not_set'] = 'Токен доступа Twitter не установлен';
$_lang['nf.twitter_access_token_secret_not_set'] = 'Секретный токен доступа Twitter не установлен';
$_lang['nf.tweet_field_is_empty'] = 'Поле твита не заполнено';
$_lang['nf.unknown_error_using_twitter_api'] = 'При использовании Twitter API возникла неизвестная ошибка';
$_lang['nf.twitter_said_there_was_an_error'] = 'Twitter уведомил о возникновении ошибки';
$_lang['nf.full_response'] = 'Полный ответ: ';
$_lang['nf.email_to_subscribers_sent_successfully'] = 'Email было успешно отправлено к Подписчикам в количестве [[+nf_number]] получателей';
$_lang['nf.using'] = 'с помощью';
$_lang['nf.tweet_sent_successfully'] = 'Твит успешно отправлен';



/* Used in notify.class.php */

$_lang['nf.user_not_found'] = 'Пользователь не найден';
$_lang['nf.sending_batch_of'] = 'Отправляю партию';
$_lang['nf.no_messages_sent'] = 'Сообщения не отправлены';



/* Used in transport.settings.php */
$_lang['setting_nf_status_resource_id'] = 'Notify Status Resource ID';
$_lang['setting_nf_status_resource_id_desc'] = 'ID of Notify Status Resource';

/* Used in nfsendemail.class.php */
$_lang['nf.no_view_user_permission'] = 'Пользователь не имеет прав для просмотра';
$_lang['nf._no_single_id'] = 'No email or ID for single email';
$_lang['nf.processing_batch'] = 'Обработка партии: ';
$_lang['nf.users_emailed_in_batch'] = 'Пользователи, которым были отправлены письма в этой партии: ';
$_lang['nf.finished'] = 'Закончено';
$_lang['nf.successful_send_to'] = 'Успешно послано';
$_lang['nf.user_tags'] = 'Теги пользователя';
$_lang['nf.error_sending_to'] = 'Ошибка отправки';
$_lang['nf.test_mode_on'] = '(включён testMode, сообщения или твитты не были отправлены)';