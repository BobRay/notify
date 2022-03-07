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

/** Translation by Viktor Matushevskyi (Viktorminator) <https://github.com/Viktorminator> */

/* Notify Property Description strings */
/* Lexicon Strings in: properties.notify.php */
$_lang['nf.url_shortening_service_desc'] = 'Служба, которая используется для сокращения всех URL в тексте и твиттах.';
$_lang['nf.groups_desc'] = 'Список через запятую груп пользователей к которым слать почту (без пробелов). Група Subscribers будет установлена в форме, но если вы удалите её и отправите с пустым полем для Груп, то почта будет отправлена ВСЕМ пользователям сайта';
$_lang['nf.tags_desc'] = ' (опционально) Список тегов через запятую (без пробелов). Выставляется, только если пользователи в определённой групе указали интерес(ы) - темы, по которым хотят получать уведомления.';
$_lang['nf.notify_facebook_desc'] = 'Уведомить Facebook через Twitter с помощью #fb в твитте -- должен быть настроен Facebook Twitter App.';
$_lang['nf.twitter_consumer_key_desc'] = 'Ключ пользователя Twitter';
$_lang['nf.twitter_consumer_secret_desc'] = 'Секретный ключ пользователя Twitter';
$_lang['nf.twitter_access_token_desc'] = 'Токен доступа Twitter';
$_lang['nf.twitter_oauth_token_secret_desc'] = 'Секретный ключ доступа Twitter';
$_lang['nf.bitly_api_key_desc'] = 'bit.ly API ключ (необходим)';
$_lang['nf.bitly_username_desc'] = 'bit.ly username (необходим)';
$_lang['nf.google_api_key_desc'] = 'Google API ключ';
$_lang['nf.supr_api_key_desc'] = 'StumbleUpon API ключ (опционально)';
$_lang['nf.supr_username_desc'] = 'StumbleUpon имя пользователя (опционально)';
$_lang['nf.tinyurl_api_key_desc'] = 'TinyUrl API key (опционально)';
$_lang['nf.tinyurl_username_desc'] = 'TinyUrl username (опционально)';
$_lang['nf.mail_from_desc'] = '(опционально) MAIL_FROM настройки для email. По-умолчанию: системная настройка emailsender';
$_lang['nf.mail_from_name_desc'] = '(опционально) MAIL_FROM_NAME настройки для email. По-умолчанию: системная настройка site_name';
$_lang['nf.mail_sender_desc'] = '(опционально) EMAIL_SENDER настройки для email. По-умолчанию: системная настройка emailsender';
$_lang['nf.mail_reply_to_desc'] = '(опционально) REPLY_TO настройки для email. По-умолчанию: системная настройка emailsender';
$_lang['nf.form_tpl_desc'] = 'Название чанка для использования в Notify форме; по-умолчанию: NfNotifyFormTpl';
$_lang['nf.email_tpl_new_desc'] = 'Название чанка для использования для почты нового ресурса; по-умолчанию: NfSubscriberEmailTplNew';
$_lang['nf.email_tpl_existing_desc'] = 'Название чанка для использования для почты обновлённого ресурса; по-умолчанию: NfSubscriberEmailTplExisting';
$_lang['nf.email_tpl_custom_desc'] = 'Название чанка для использования для пользовательской почты; по-умолчанию: NfSubscriberEmailTplCustom';
$_lang['nf.tweet_tpl_new_desc'] = 'Название чанка для использования для текста твиттера нового ресурса; по-умолчанию: nfTweetTplNew';
$_lang['nf.tweet_tpl_existing_desc'] = 'Название чанка для использования для текста твиттера обновлённого ресурса; по-умолчанию: nfTweetTplExisting';
$_lang['nf.tweet_tpl_custom_desc'] = 'Название чанка для использования в пользовательском тексте для твиттера; по-умолчанию: nfTweetTplCustom';
$_lang['nf.subject_tpl_desc'] = 'Название чанка для использования в теме письма; по-умолчанию: NfEmailSubjectTpl';
$_lang['nf.test_email_address_desc'] = ' (опционально) Email адрес для тестового письма. По-умолчанию: системная настройка emailsender';
$_lang['nf.sortby_desc'] = ' (опционально) Поля для сортировки при выборе пользователей; по-умолчанию: username';
$_lang['nf.userClass_desc'] = ' (опционально) класс объекта пользователь. Необходим только если вы расширили вас объект пользователя. По-умолчанию: modUser';
$_lang['nf.sortby_alias_desc'] = ' (опционально) класс объекта пользователь. Необходим только если вы расширили вас объект пользователя. По-умолчанию: modUser';
$_lang['nf.profile_desc'] = ' (опционально) class of the user profile object. Необходим только если вы расширили вас объект профиль пользователя. По-умолчанию: modUserProfile';
$_lang['nf.profile_class_desc'] = ' (опционально) class of the user profile object. Необходим только если вы расширили вас объект профиль пользователя. По-умолчанию: modUser';
$_lang['nf.batch_size_desc'] = ' (опционально) Объем партии для массовой рассылки подписчикам. По-умолчанию: 50';
$_lang['nf.batch_delay_desc'] = ' (опционально) Задержка между партиями в секундах. По-умолчанию: 1';
$_lang['nf.item_delay_desc'] = ' (опционально) Задержка между отдельными письмами в секундах. По-умолчанию: .51';
$_lang['nf.pref_list_chunk_name_desc'] = ' (опционально) Чанк для вывода списка предпочтений (тегов). По-умолчанию: sbsPrefListTpl';
$_lang['nf.require_all_tags_default_desc'] = ' (опционально) устанавливает значение по-умолчанию для чекбокса Require All Tags; если выставлено, то только те пользователи будут получать письма, которые выставили все теги; по-умолчанию: No';
$_lang['nf.unsubscribe_tpl_desc'] = 'Название чанка для ссылки по отмене подписки.';


/* Used in properties.notify.snippet.php */
$_lang['nf_inject_unsubscribe_url_desc'] = 'If set, adds an unsubscribe/manange preferences link to every email; default: Yes. Be aware that if you send bulk emails, such a link is required by USA law.';
$_lang['nf_testMode_desc'] = 'Test mode -- Notify functions normally, but no emails are sent.
';

$_lang['include_tv_list_property_desc'] = 'Названия ТВ через запятую. Только ТВ из списка будут иметь свой набор плейсхолдеров.';
$_lang['include_tvs_property_desc'] = 'Если установлено, то плейсхолдеры будут установлены для Resource ТВ; по-умолчанию: Нет.';
$_lang['use_extended_fields_property_desc'] = 'Если установлено, плейсхолдеры будут установлены для разширенных полей профиля Пользователя; по-умолчанию: Нет';
$_lang['debug_property_desc'] = 'Если Да, то будет выведенна информация об отладке';
$_lang['maxlogs_property_desc'] = 'Устанавливает лимит количества сохраняемых email логов. Самые старые будут удалены. Установите 0 для неограниченных логов. По-умолчанию: 5';
$_lang['nf.process_tvs_desc'] = 'Если установлено в Нет, то будут использованы необработанные значения ТВ. По-умолчанию: Да.';

$_lang['nf.group_list_chunk_name_desc'] = 'Определяет чанк, который будет использоваться для кнопок под инпутом Груп в форме; по-умолчанию: sbsGroupListTpl';
