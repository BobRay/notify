<?php

/**
 * Notify
 *
 * Copyright 2012 Bob Ray
 *
 * @author Bob Ray <http://bobsguides.com>
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
 * Notify; if not, write to the Free Software Foundation, Inc., 59 Temple
 * Place, Suite 330, Boston, MA 02111-1307 USA
 *
 * @package notify
 */


/**
 * MODx Notify class
 *
 * Description: Creates and Sends an email to subscribers and notifies social media
 *
 * @package notify
 *
 */



class Notify
{

    /* @var $modx modX */
    /* @var $resource modResource */
    protected $resource;
    protected $resourceId;
    protected $modx;
    protected $props;
    protected $mail_from;
    protected $mail_from_name;
    protected $mail_sender;
    protected $mail_reply_to;
    protected $mail_subject;
    protected $groups;
    protected $batchSize;
    protected $batchDelay;
    protected $itemDelay;
    protected $logFile;
    protected $userClass;
    protected $profileAlias;
    protected $profileClass;
    protected $sortBy;
    protected $sortByAlias;
    protected $tags;
    /* @var $recipients array */
    protected $recipients;
    protected $emailText;
    protected $emailTpl;
    protected $tweetText;
    protected $replace;
    /* @var $successMessages array */
    protected $successMessages;
    /* @var $errors array */
    protected $errors;
    protected $pageId;
    protected $sendBulkEmail;
    protected $sendTestEmail;
    protected $sendTweet;
    /* @var $previewPage modResource */
    protected $previewPage;
    protected $formTpl;
    protected $urlShorteningService;
    protected $shortenUrls;
    /* @var $shortener UrlShortener */
    public $shortener;
    protected $tplType; /* new, update, blank, custom */



    public function __construct(&$modx, &$props, &$resource = null)
    {
        /* @var $modx modX */
        /* @var $resource modResource */

        $this->modx =& $modx;
        $this->props = $props;

        /* nf paths; Set the nf. System Settings only for development */
        $this->corePath = $this->modx->getOption('nf.core_path', null, MODX_CORE_PATH . 'components/notify/');
    }

    public function init($action) {
        $this->errors = array();
        $this->successMessages = array();

        $language = !empty($this->props['language'])
            ? $this->props['language']
            : $this->modx->getOption('cultureKey',null,$this->modx->getOption('manager_language',null,'en'));

        $this->modx->lexicon->load($language . ':notify:default');

        $this->previewPage = $this->modx->getObject('modResource', array('alias'=> 'notify-preview'));
        if (! $this->previewPage) {
            $this->setError($this->modx->lexicon('nf.could_not_find_preview_page'));
        }

        $this->formTpl = $this->modx->getOption('nfFormTpl', $this->props, 'NfNotifyFormTpl');
        $this->formTpl = empty($this->formTpl)? 'NfNotifyFormTpl' : $this->formTpl;

        switch($action) {

            /* *********************************************** */
            case 'displayForm':
                $this->pageId = isset($_POST['pageId'])? $_POST['pageId'] : '';

                if (empty($this->pageId) ) {
                    $this->setError('nf_page_id_is_empty');
                    return '';
                }

                $this->tplType = isset($_POST['pageType'])? $_POST['pageType'] : '';


                $this->resource = $this->modx->getObject('modResource',$this->pageId);
                if (!$this->resource) {
                    $this->setError($this->modx->lexicon('nf.could_not_get_resource'));
                    return '';
                }

                $notifyFacebook = $this->modx->getOption('notifyFacebook', $this->props, null);
                $this->urlShorteningService = $this->modx->getOption('urlShorteningService', $this->props, 'none');
                $this->shortenUrls = $this->urlShorteningService != 'none';
                echo 'ShorteningService: ' . $this->urlShorteningService . "<br />";
                if ($this->shortenUrls) {
                    require_once $this->corePath . 'model/notify/urlshortener.class.php';
                    $this->shortener = new UrlShortener($this->props);
                }
                $fields = $this->resource->toArray();
                $fields['url'] = $this->modx->makeUrl($this->pageId, "", "", "full");
                $this->emailTpl = $this->modx->getOption('nfEmailTpl', $this->props, 'NfSubscriberEmailTpl');
                $this->emailTpl = empty($this->emailTpl)? 'NfSubscriberEmailTpl' : $this->emailTpl;

                $this->emailText = $this->modx->getChunk($this->emailTpl, $fields);
                if (empty($this->emailText)) {
                    $this->setError($this->modx->lexicon('nf.could_not_find_email_tpl_chunk'));
                } else {
                    /* convert any relative URLS in email text */
                    $this->fullUrls();
                    $this->imgAttributes();


                    /* shorten URLs if property is set */
                    if ($this->shortenUrls) {
                        echo 'ShortenUrls is true';
                        $this->shortenUrls($this->emailText);
                    }
                }

                $tweetTpl = $this->modx->getOption('nfTweetTpl', $this->props, 'NfTweetTpl');
                $tweetTpl = empty($tweetTpl)? 'nfTweetTpl' : $tweetTpl;
                $this->tweetText = $this->modx->getChunk($tweetTpl, $fields);
                if (empty($this->tweetText)) {
                    $this->setError($this->modx->lexicon('nf.could_not_find_tweet_tpl_chunk'));
                } else {
                    /* shorten URLs if property is set */
                    if ($this->shortenUrls) {
                        $this->shortenUrls($this->tweetText);
                    }
                    if ($notifyFacebook) {
                        $this->tweetText = rtrim($this->tweetText,' ') . ' #fb';
                    }
                }
                    break;
            /* *********************************************** */
            case 'handleSubmission':
                $this->sendTestEmail = isset($_POST['nf_send_test_email']);
                $this->sendBulkEmail = isset($_POST['nf_notify']);
                $this->sendTweet = isset($_POST['nf_send_tweet']);
                $this->emailText = isset($_POST['nf_email_text'])? $_POST['nf_email_text'] : '';
                $this->tweetText = isset($_POST['nf_tweet_text'])? $_POST['nf_tweet_text'] : '';
                if ($this->sendTestEmail) {
                    $this->modx->setPlaceholder('nf_send_test_email_checked','checked="checked"');
                }
                /* set form placeholders */
                if ($this->sendBulkEmail) {
                    $this->modx->setPlaceholder('nf_notify_checked','checked="checked"');
                }
                if ($this->sendTweet) {
                    $this->modx->setPlaceholder('nf_send_tweet_checked','checked="checked"');
                }
                $postFields= array(
                    'nf_test_email_address',
                    'nf_email_subject',
                    'nf_groups',
                    'nf_tags',
                    'nf_email_text',
                    'nf_tweet_text',
                );
                foreach ($postFields as $field) {
                    if (isset($_POST[$field])) {
                        /* sanitize fields and set placeholder */
                        $_POST[$field] = str_replace('[[', '[ [', $_POST[$field]);
                        $this->modx->setPlaceholder($field, $_POST[$field]);
                    }
                }
                $this->emailText = $_POST['nf_email_text'];
                $this->fullUrls();
                $this->imgAttributes();
                $this->updatePreviewPage();

                /* **************************** */

                /* perform requested actions */
                if ($this->sendBulkEmail || $this->sendTestEmail) {
                    /* Set preview in case user forgot */
                    $this->initEmail();
                    $this->initializeMailer();

                    if ($this->sendBulkEmail) {
                        /* send bulk email */
                        $this->sendBulkEmail();
                    }

                    if ($this->sendTestEmail) {
                        /* send test email */
                        $testEmailAddress = isset($_POST['nf_test_email_address'])? $_POST['nf_test_email_address'] : '';
                        $username = $this->modx->user->get('username');
                        $this->sendTestEmail($testEmailAddress, $username);
                    }
                }
                if ($this->sendTweet) {
                    $this->tweet();
                }

                return $this->modx->getChunk($this->formTpl);

                break;

        }

        return "";
    }

    public function shortenUrls(&$text) {
        echo "Before: " . $text . "<br />";
        $this->shortener->init_curl();

        $this->shortener->process($text, $this->urlShorteningService);
        $this->shortener->close_curl();
        echo "After: " . $text . "<br />";

    }

    public function displayForm() {
        $testEmailAddress = $this->modx->getOption('nfTestEmailAddress', $this->props, '');
        $testEmailAddress = empty($testEmailAddress)? $this->modx->getOption('emailsender'): $testEmailAddress;

        $this->modx->setPlaceholder('nf_test_email_address', $testEmailAddress);

        $groups = $this->modx->getOption('groups', $this->props, 'Subscribers');
        if (empty($groups)) {
            $groups = 'Subscribers';
        }
        $this->modx->setPlaceholder('nf_groups', $groups);


        $tags = $this->modx->getOption('tags', $this->props, '');
        $this->modx->setPlaceholder('nf_tags', $tags);

        /* @var $tempPage modResource */
        $this->updatePreviewPage();

        $this->modx->setPlaceholder('nf_email_text', $this->emailText);
        $subjectTpl = $this->modx->getOption('nfSubjectTpl', $this->props);
        $subjectTpl = empty($subjectTpl)? 'NfEmailSubjectTpl' : $subjectTpl;
        $this->modx->setPlaceholder('nf_email_subject',$this->modx->getChunk($subjectTpl));
        $this->modx->setPlaceholder('nf_tweet_text', $this->tweetText);

        return $this->modx->getChunk($this->formTpl);

    }
    protected function updatePreviewPage() {
        $this->previewPage->setContent($this->emailText);
        $this->previewPage->save();
        $this->modx->setPlaceholder('nf_temp_url', $this->modx->makeUrl($this->previewPage->get('id'), "", "", "full"));

    }

    protected function setError($msg) {
        $this->errors[] = $msg;
    }
    public function hasErrors() {
        return ! empty($this->errors);
    }
    public function getErrors() {
        return $this->errors;
    }
    public function displayErrors() {
        $msg = '';
        foreach ($this->errors as $error) {
            $msg .= '<p>' . $error . '</p>';
        }
        return $msg;
    }
    public function setSuccess($msg) {
        $this->successMessages[] = $msg;
    }

    public function displaySuccessMessages() {
        $msg = '';
        foreach($this->successMessages as $message)
            $msg .= '<p>' . $message . '</p>';
        return $msg;
    }

    public function getTweetText() {
        return $this->tweetText;
    }
    public function getEmailText() {
        return $this->emailText;
    }
    public function initEmail() {
        $this->sortBy = $this->modx->getOption('sortBy',$this->props);
        $this->sortBy = empty($this->sortBy)? 'username' : $this->sortBy;
        $this->sortByAlias = $this->modx->getOption('sortByAlias',$this->props);
        $this->sortByAlias = empty ($this->sortByAlias)? 'modUser' : $this->sortByAlias;
        $this->userClass = $this->modx->getOption('userClass',$this->props);
        $this->userClass = empty($this->userClass)? 'modUser' : $this->userClass;
        $this->profileAlias = $this->modx->getOption('profileAlias',$this->props,'Profile');
        $this->profileAlias = empty($this->profileAlias)? 'modUserProfile' : $this->profileAlias;
        $this->profileClass = $this->modx->getOption('profileClass',$this->props,'modUserProfile');
        $this->profileClass = empty($this->profileClass)? 'modUserProfile' : $this->profileClass;
        $this->logFile = $this->corePath . 'notify-logs/' . $this->resource->get('alias') . '--'. date('Y-m-d-h.i.sa');

        $this->tags = isset($_POST['nf_tags'])? $_POST['nf_tags']: '';

        $this->groups = isset($_POST['nf_groups'])? $_POST['nf_groups']: '';

        $this->batchSize = (integer) $this->modx->getOption('batchSize', $this->props, 50);
        $this->batchDelay = (integer) $this->modx->getOption('batchDelay', $this->props, 1);
        $this->itemDelay = (float) $this->modx->getOption('itemDelay', $this->props, .51);

    }


    public function strReplaceAssoc($replace, $subject) {
           $msg =  str_replace(array_keys($replace), array_values($replace), $subject);
           return $msg;

    }

    public function initializeMailer() {
        set_time_limit(0);
        $this->modx->getService('mail', 'mail.modPHPMailer');
        $this->mail_from = $this->modx->getOption('mailFrom', $this->props, $this->modx->getOption('emailsender'));
        if (empty($this->mail_from)) $this->mail_from = $this->modx->getOption('emailsender');

        $this->mail_from_name = $this->modx->getOption('mailFromName', $this->props, $this->modx->getOption('site_name', null));
        if (empty($this->mail_from_name)) $this->mail_from_name = $this->modx->getOption('site_name', null);
        $this->mail_sender = $this->modx->getOption('mailSender', $this->props, $this->mail_from);

        $this->mail_reply_to = $this->modx->getOption('mailReplyTo', $this->props, $this->mail_from);

        $this->mail_subject = isset($_POST['nf_email_subject'])? $_POST['nf_email_subject'] :
         'Update from ' . $this->modx->getOption('site_name');

        $this->modx->mail->set(modMail::MAIL_BODY, $this->emailText);
        $this->modx->mail->set(modMail::MAIL_FROM, $this->mail_from);
        $this->modx->mail->set(modMail::MAIL_FROM_NAME, $this->mail_from_name);
        $this->modx->mail->set(modMail::MAIL_SENDER, $this->mail_sender);
        $this->modx->mail->set(modMail::MAIL_SUBJECT, $this->mail_subject);
        $this->modx->mail->address('reply-to', $this->mail_reply_to);
        $this->modx->mail->setHTML(true);

    }

    /* Sends an individual email */

    public function sendMail($address, $name)
    {
        $this->modx->mail->address('to', $address, $name);
        $success = $this->modx->mail->send();
        if (! $success) {
            $this->setError($this->modx->mail->mailer->ErrorInfo);
        }
        $this->modx->mail->mailer->ClearAddresses();
        return $success;

    }

    public function sendBulkEmail()
    {
        /* @var $user modUser */

        $this->recipients = array();

        /* if groups is empty, send to all active users */
        if (empty ($this->groups)) {
            $c = $this->modx->newQuery($this->userClass);
            $c->select($this->modx->getSelectColumns($this->userClass,$this->userClass),"", array('id','username','active'));
                        $c->sortby($this->modx->escape($this->sortByAlias).'.'.$this->modx->escape($this->sortBy),'ASC');
            $c->where(array(
                'active' => '1',
            ));
            $users = $this->modx->getIterator($this->userClass,$c);
            $this->addUsers($users, 'All Users');
            unset($users);
        } else {  /* send to named groups */

            $userGroupNames = explode(',', $this->groups);
            /* Build Recipient array */
            foreach ($userGroupNames as $userGroupName) {
                /* @var $group modUserGroup */
                $userGroupName = trim($userGroupName);
                echo '<h3>GROUP: ' . $userGroupName . '</h3>';
                flush();
                /* allow UserGroup name or ID */
                $g = intval($userGroupName);
                $g = is_int($g) && !empty($g) ? $userGroupName : array('name' => $userGroupName);
                $group = $this->modx->getObject('modUserGroup',$g);

                if (empty($group)) {
                    $this->setError ($this->modx->lexicon('nf.could_not_find_user_group') . ': ' . $userGroupName);
                } else {
                    /* get users */
                    $c = $this->modx->newQuery($this->userClass);
                    $c->select($this->modx->getSelectColumns($this->userClass,$this->userClass),"", array('id','username','active'));
                    $c->sortby($this->modx->escape($this->sortByAlias).'.'.$this->modx->escape($this->sortBy),'ASC');
                    $c->where(array(
                        'UserGroupMembers.user_group' => $group->get('id'),
                        'active' => '1',
                    ));
                    $c->innerJoin('modUserGroupMember','UserGroupMembers');
                    //$total = $this->modx->getCount($this->userClass,$c);

                    /* ToDo: Get these in batches with offset to conserve memory
                     * $c->limit($number, $offset);
                     */
                    $users = $this->modx->getIterator($this->userClass,$c);
                    $this->addUsers($users, $userGroupName);
                    unset($users);
                }

            }
        }




        if (empty($this->recipients)) {
            $this->setError($this->modx->lexicon('nf.no_recipients_to_send_to'));
        }
        /* skip mail send if any errors are set */
        if (!empty($this->errors) ) {
            $this->setError($this->modx->lexicon('nf.bulk_emails_not_sent'));
            return false;
        }
        /* $this->recipients array now complete and no errors - send bulk emails */
        $i = 1;
        $fp = fopen($this->logFile, 'w');
        if (!$fp) {
            $this->setError($this->modx->lexicon('nf.could_not_open_log_file') . ': ' . $this->logFile);
        } else {
            fwrite($fp, "MESSAGE\n*****************************\n" . $this->emailText . "\n*****************************\n\n");
            //fwrite($fp,print_r($this->recipients, true));
        }
        foreach ($this->recipients as $recipient) {
            if ($this->sendMail($recipient['email'], $recipient['fullName'])) {
                if ($fp) {
                    fwrite($fp, 'Successful send to: ' . $recipient['email'] . ' (' . $recipient['fullName'] . ') User Tags: ' . $recipient['userTags'] . "\n");
                }
            } else {
                if ($fp) {
                    fwrite($fp, 'Error sending to: ' . $recipient['email'] . ' (' . $recipient['fullName'] . ') ' . "\n");
                }


            }
            sleep($this->itemDelay);

            /* implement batch delay if it's time */
            if (($i % $this->batchSize) == 0) {
                sleep($this->batchDelay);
            }
            $i++;
        }
        if ($fp) {
            fclose($fp);
        }
        $this->setSuccess($this->modx->lexicon('nf.email_to_subscribers_sent_successfully'));
        return true;


    }
    protected function addUsers($users, $userGroupName) {
        foreach ($users as $user) {
            /* @var $user modUser */

            $username = $user->get('username');

            $profile = $user->getOne($this->profileAlias);
            $userTags = null;
            if (! $profile) {
                $this->setError($this->modx->lexicon('nf.no_profile_for') . ': ' . $username);
            } else {
                if ( $this->modx->getOption('sbs_use_comment_field', null, null) == 'No') {
                    $field = $this->modx->getOption('sbs_extended_field');
                    if (empty($field)) {
                        $this->setError($this->modx->lexicon('nf.sbs_extended_field_not_set'));
                    } else {
                        $extended = $profile->get('extended');
                        $userTags = $extended[$field];
                    }
                } else {
                    $userTags = $profile->get('comment');
                }
                $email = $profile->get('email');
                $fullName = $profile->get('fullname');
            }

            /* fall back to username if fullname is empty */
            $fullName = empty($fullName) ? $username : $fullName;

            /* process tags if Tags TV is set */
            if (!empty ($this->tags)) {
                $tags = explode(',',$this->tags);
                $hasTag = false;

                foreach ($tags as $tag) {
                    $tag = trim($tag);


                    if ( (!empty($tag)) && stristr($userTags,$tag)) {
                        $hasTag = true;
                    }
                }
                if (! $hasTag) {
                    continue;
                }
            }

            if (! empty($email)) {
                /* add user data to recipient array */

                /* Either no tags are in use or this user has a tag.
                 * Add user to recipient array */
                $this->recipients[] = array(
                    'group' => $userGroupName,
                    'email' => $email,
                    'fullName' => $fullName,
                    'userTags' => $userTags,
                );
            } else {
                $this->setError($username . ' ' .  $this->modx->lexicon('nf.has_no_email_address'));
            }
        }
    }
    public function sendTestEmail($address, $name){

        if (empty($address)) {
            $this->setError ($this->modx->lexicon('nf.test_email_address_empty') . $this->modx->lexicon('nf.test_email_not_sent'));
            return;
        }
        if (! $this->sendMail($address, $name)) {
            $this->setError($this->modx->lexicon('nf.mail_error_sending_test_email'));
        } else {
            $this->setSuccess($this->modx->lexicon('nf.test_email_sent_successfully'));
        }
        return;
    }

    public function tweet() {

        require_once(MODX_CORE_PATH . 'components/notify/model/notify/twitteroauth.php');
        $consumer_key = $this->modx->getOption('twitterConsumerKey',$this->props, null);
        if (! $consumer_key) {
            $this->setError($this->modx->lexicon('nf.twitter_consumer_key_not_set'));
        }
        $consumer_secret = $this->modx->getOption('twitterConsumerSecret',$this->props, null);
        if (! $consumer_secret) {
            $this->setError($this->modx->lexicon('nf.twitter_consumer_secret_not_set'));
        }
        $oauth_token = $this->modx->getOption('twitterOauthToken',$this->props, null);
        if (! $oauth_token) {
            $this->setError($this->modx->lexicon('nf.twitter_access_token_not_set'));
        }        
        $oauth_secret = $this->modx->getOption('twitterOauthSecret',$this->props, null);
        if (! $oauth_secret) {
            $this->setError($this->modx->lexicon('nf.twitter_access_token_secret_not_set'));
        }        
        $msg = $this->tweetText;
        if (empty($msg)) {
            $this->setError($this->modx->lexicon('nf.tweet_field_is_empty'));
        } else {
            //$text = 'Tweeted from PHP - just testing some code';
            $tweet = new TwitterOAuth($consumer_key, $consumer_secret, $oauth_token, $oauth_secret);
            $response = $tweet->post('statuses/update', array('status' => $msg));
            /* This will get recent tweets */
            //$response = $tweet->get('statuses/user_timeline', array('screen_name' => 'BobRay'));

            if (!$response) {
                $this->setError($this->modx->lexicon('nf.unknown_error_using_twitter_api'));
            } elseif ($response->error) {
                $this->setError('<p>' . $this->modx->lexicon('nf.twitter_said_there_was_an_error') .      '</p><p>$response->error</p><p>' . $this->modx->lexicon('nf.full_response') . '</p>
                <pre>" . print_r($response,true) . "</pre><br />"');
            } else {
                $this->setSuccess($this->modx->lexicon('nf.tweet_sent_successfully'));
            }
        }
    }


    public function my_debug($message, $clear = false)
    {
        /* @var $chunk modChunk */
        $chunk = $this->modx->getObject('modChunk', array('name' => 'debug'));

        if (!$chunk) {
            $chunk = $this->modx->newObject('modChunk', array('name' => 'debug'));
            $chunk->save();
            $chunk = $this->modx->getObject('modChunk', array('name' => 'debug'));
        }
        if ($clear) {
            $content = '';
        } else {
            $content = $chunk->getContent();
        }
        $content .= $message;
        $chunk->setContent($content);
        $chunk->save();
    }

    /* correct any non-full urls in email text */
    public function fullUrls() {
        /* extract domain name from $base */
        $base = $this->modx->getOption('site_url');
        $splitBase = explode('//', $base);
        $domain = $splitBase[1];
        $domain = rtrim($domain,'/ ');
        $html = $this->emailText;

        /* remove space around = sign */

        $html = preg_replace('@(?<=href|src)\s*=\s*@', '=', $html);

        /* fix google link weirdness */
        $html = str_ireplace('google.com/undefined', 'google.com',$html);

        /* add http to naked domain links so they'll be ignored later */
        $html = str_ireplace('a href="' . $domain, 'a href="http://'. $domain, $html);

        /* standardize orthography of domain name */
        $html = str_ireplace($domain, $domain, $html);

        /* Correct base URL, if necessary */
        $server = preg_replace('@^([^\:]*)://([^/*]*)(/|$).*@', '\1://\2/', $base);

        /* handle root-relative URLs */
        $html = preg_replace('@\<([^>]*) (href|src)="/([^"]*)"@i', '<\1 \2="' . $server . '\3"', $html);

        /* handle base-relative URLs */
        $html = preg_replace('@\<([^>]*) (href|src)="(?!http|mailto|sip|tel|callto|sms|ftp|sftp|gtalk|skype)(([^\:"])*|([^"]*:[^/"].*))"@i', '<\1 \2="' . $base . '\3"', $html);

        $this->emailText = $html;
    }

    /* Correct image tags for email use */
    public function imgAttributes() {
        $html =& $this->emailText;
        $replace = array (
            '<img style="vertical-align: baseline;' =>'<img align="bottom" hspace="4" vspace="4" style="vertical-align: baseline;',
            '<img style="vertical-align: middle;' => '<img align="middle" hspace="4" vspace="4" style="vertical-align: middle;',
            '<img style="vertical-align: top;' => '<img align="top" hspace="4" vspace="4" style="vertical-align: top;',
            '<img style="vertical-align: bottom;' => '<img align="bottom" hspace="4" vspace="4" style="vertical-align: bottom;',
            '<img style="vertical-align: text-top;' =>'<img align="top" hspace="4" vspace="4" style="vertical-align: text-top;',
            '<img style="vertical-align: text-bottom;' => '<img align="bottom" hspace="4" vspace="4" style="vertical-align: text-bottom;',
            '<img style="float: left;' => '<img align="left" hspace="4" vspace="4" style="float: left;',
            '<img style="float: right;' => '<img align="right" hspace="4" vspace="4" style="float: right;',
        );
        $html = $this->strReplaceAssoc($replace, $html);

    }

} /* end class */