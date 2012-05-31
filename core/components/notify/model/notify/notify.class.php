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
    protected $successHeader;
    protected $errorHeader;
    /* @var $errors array */
    protected $errors;
    protected $pageId;
    protected $sendBulkEmail;
    protected $sendTestEmail;
    protected $sendTweet;
    /* @var $previewPage modResource */
    protected $previewPage;



    public function __construct(&$modx, &$props, &$resource = null)
    {
        /* @var $modx modX */
        /* @var $resource modResource */

        $this->modx =& $modx;
        $this->props = $props;

        /* set $this->resource to argument for OnDocFormPrerender
           and $this->modx->resource for OnWebPagePrerender
        */
        
        /* Create $replace array from resource fields + url and site_name */

        /* nf paths; Set the nf. System Settings only for development */
        $this->corePath = $this->modx->getOption('nf.core_path', null, MODX_CORE_PATH . 'components/notify/');
    }

    public function init($action) {
        $this->errors = array();
        $this->errorHeader = '';
        $this->successHeader= '';
        
        $this->previewPage = $this->modx->getObject('modResource', array('alias'=> 'notify-preview'));
        if (! $this->previewPage) {
            $this->setError($this->modx->lexicon('could_not_find_preview_page'));
        }
        
        
        switch($action) {

            /* *********************************************** */
            case 'displayForm':
                /* @var $res modResource */
                $pageId = $_SESSION['nf_page_id'];
                if (empty($pageId)) {
                    $this->setError($this->modx->lexicon('Session Variable not set'));
                    return '';
                }
                    
                $res = $this->modx->getObject('modResource',$pageId);
                if (!$res) {
                    $this->setError($this->modx->lexicon('Could not get resource'));
                    return '';
                }
                $this->resource =& $res;
                $this->pageId = $res->get('id');
                $fields = $this->resource->toArray();
                $fields['url'] = $this->modx->makeUrl($this->pageId, "", "", "full");
                $this->emailTpl = $this->modx->getOption('nfEmailTpl', $this->props, 'NfSubscriberEmailTpl');
                $this->emailText = $this->modx->getChunk($this->emailTpl, $fields);
                //echo '<pre><fixedpre>' . $this->emailText . '</fixedpre></pre>';
                if (empty($this->emailText)) {
                    $this->setError($this->modx->lexicon('could_not_find_email_tpl_chunk'));
                }
                $tweetTpl = $this->modx->getOption('nfTweetTpl', $this->props, 'NfTweetTpl');
                $this->tweetText = $this->modx->getChunk($tweetTpl, $fields);
                if (empty($this->tweetText)) {
                    $this->setError($this->modx->lexicon('could_not_find_tweet_tpl_chunk'));
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
                        $this->modx->setPlaceholder($field, $_POST[$field]);
                    }
                }
                $this->emailText = $_POST['nf_email_text'];
                $this->updatePreviewPage();
                $tmpUrl = $this->modx->makeUrl($this->previewPage->get('id'), "", "", "full");
                /*$this->modx->setPlaceholder('nf_temp_url', $this->modx->makeUrl($this->previewPage->get('id'), "", "", "full"));*/
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
                                $testEmailAddress = '';
                                $username = $this->modx->user->get('username');
                                $this->sendTestEmail($testEmailAddress, $username);
                            }
                        }
                        if ($this->sendTweet) {
                            $this->tweet();
                        }

                return $this->modx->getChunk('nfNotifyForm');

                break;

        }

        return "";
        switch ($action) {
            case 'OnWebPagePrerender':
                /* initialize success and error headers */
                $this->errorHeader = '';
                $this->successHeader = '';

                $this->emailText = $this->resource->getTVValue('nf_subscriber_email');
                if (!empty($this->emailText)) {
                    if (strtr($this->emailText,'[[')  ) {
                        $this->emailText = $this->strReplaceAssoc($this->replace, $this->emailText);
                        $this->emailText = str_replace('[[', '[ [', $this->emailText);
                    }
                }

                $this->tweetText = $this->resource->getTVValue('nf_tweet');
                if (!empty($this->tweetText)) {
                    if (strtr($this->emailText,'[[')  ) {
                        $this->tweetText = $this->strReplaceAssoc($this->replace, $this->tweetText);
                        $this->tweetText = str_replace('[[', '[ [', $this->tweetText);
                    }
                }

                foreach ($this->replace as $k => $v) {
                    $this->replace['[[+' . $k . ']]'] = $v;
                    unset ($this->replace[$k]);
                }

                break;
            /* end OnWebPagePrerender case */
            case 'OnResourceTVFormPrerender':
                //$url = $this->modx->makeUrl($this->resource->get('id'), "", "", "full");
                /*$fields = $this->resource->toArray();
                $fields['url'] = $url;*/

                $emailTpl = $this->modx->getOption('nfEmailTpl', $this->props, 'NfSubscriberEmailTpl');

                $emailSubjectTpl = $this->modx->getOption('nfEmailSubjectTpl', $this->props, 'NfEmailSubjectTpl');
                $tweetTpl = $this->modx->getOption('nfTweetTpl', $this->props, 'NfTweetTpl');
                /* pre-fill TVs */

                $emailAddressTv = $this->modx->getObject('modTemplateVar', array('name'=>'nf_email_address_for_test'));
                $subscriberEmailTv = $this->modx->getObject('modTemplateVar', array('name'=>'nf_subscriber_email'));
                $emailSubjectTv = $this->modx->getObject('modTemplateVar', array('name'=>'nf_email_subject'));
                $emailTextTv = $this->modx->getObject('modTemplateVar', array('name'=>'nf_subscriber_email'));
                $tweetTextTv = $this->modx->getObject('modTemplateVar', array('name'=>'nf_tweet'));

                $txt = $this->resource->getTVValue('nf_email_address_for_test');
                if (empty($txt)) {
                    $txt = $this->modx->getOption('emailsender');
                    $emailAddressTv->setValue($this->resourceId,$txt);
                    $emailAddressTv->save();
                }
                    //$this->resource->setTVValue('nf_email_address_for_test', $txt);
                 
                $txt = $emailTextTv->getValue($this->resourceId);

                if (empty($txt)) {
                    $txt = $this->modx->getChunk($emailTpl, $this->replace);
                    $emailTextTv->setValue($this->resourceId,$txt);
                    $emailTextTv->save();
                    //$this->resource->setTVValue('nf_subscriber_email', $txt);
                }


                //$txt = $this->resource->getTVValue('nf_email_subject');
                $txt = $emailSubjectTv->getValue($this->resourceId);
                if (empty($txt)) {
                    $txt = $this->modx->getChunk($emailSubjectTpl, $this->replace);
                    $emailSubjectTv->setValue($this->resourceId,$txt);
                    $emailSubjectTv->save();
                    //$this->resource->setTVValue('nf_email_subject', $txt);
                }
                //$txt = $this->resource->getTVValue('nf_tweet');
                $txt = $tweetTextTv->getValue($this->resourceId);
                if (empty($txt)) {
                    $txt = $this->modx->GetChunk($tweetTpl, $this->replace);
                    //$this->resource->setTVValue('nf_tweet', $txt);
                    $tweetTextTv->setValue($this->resourceId, $txt);
                    $tweetTextTv->save();
                }
                break;
            /* end OnDocFormPrerender case */
        }
    }

    public function displayForm() {
        $testEmailAddress = $this->modx->getOption('nf_test_email_address', $this->props, $this->modx->getOption('emailsender'));

        $this->modx->setPlaceholder('nf_test_email_address', $testEmailAddress);

        $groups = $this->modx->getOption('groups', $this->props, 'Subscribers');
        if (empty($groups)) {
            $groups = 'Subscribers';
        }
        $this->modx->setPlaceholder('nf_groups', $groups);


        $tags = $this->modx->getOption('tags', $this->props, '');
        $this->modx->setPlaceholder('nf_tags', $tags);

        /*$fields = $this->resource->toArray();
        $fields['url'] = $this->modx->makeUrl($this->pageId,"","","full");
        unset($fields['content']);
        $this->modx->setPlaceholders($fields);*/


        /* @var $tempPage modResource */
        $this->updatePreviewPage();
/*
        $tempPage = $this->modx->getObject('modResource', array('alias'=> 'notify-preview'));
        $tmpUrl = $this->modx->makeUrl($tempPage->get('id'), "", "", "full");*/

        $this->modx->setPlaceholder('nf_email_text', $this->emailText);
        $this->modx->setPlaceholder('nf_email_subject',$this->modx->getChunk('NfEmailSubjectTpl'));
        $this->modx->setPlaceholder('nf_tweet_text', $this->tweetText);


        return $this->modx->getChunk('nfNotifyForm');

    }
    protected function updatePreviewPage() {
        $this->previewPage->setContent($this->emailText);
        $this->previewPage->save();
        $this->modx->setPlaceholder('nf_temp_url', $this->modx->makeUrl($this->previewPage->get('id'), "", "", "full"));

    }

    public function displayResults($preview, $emailit, $sendTestEmail) {
        /* don't show email text unless one of these is set */
         $emailText = $sendTestEmail || $preview || $emailit? $this->emailText : '';

        /* inject headers if there is a body tag in the Tpl chunk*/
        if (strstr($emailText, '<body>')) {
            $pattern = '~(<body[^>]*>)~';
            $replacement = '$1' . $this->successHeader . "<br /><br />" . $this->errorHeader;
            $output =  preg_replace($pattern,$replacement, $emailText );
        } else {
            /* no injection */
            $output = $this->successHeader ."<br /><br />" . $this->errorHeader .  $emailText;
        }

        $this->resource->_output = $output;
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
            $msg .= '<h3>' . $error . '</h3>';
        }
        return $msg;
    }
    public function displaySuccessMessage() {
        return $this->successHeader;
    }

    public function getTweetText() {
        return $this->tweetText;
    }
    public function getEmailText() {
        return $this->emailText;
    }
    public function initEmail() {
        $this->sortBy = $this->modx->getOption('sortBy',$this->props,'username');
        $this->sortByAlias = $this->modx->getOption('sortByAlias',$this->props,'modUser');
        $this->userClass = $this->modx->getOption('userClass',$this->props,'modUser');
        $this->profileAlias = $this->modx->getOption('profileAlias',$this->props,'Profile');
        $this->profileClass = $this->modx->getOption('profileClass',$this->props,'modUserProfile');
        $this->logFile = $this->corePath . 'notify-logs/' . $this->resource->get('alias') . '--'. date('Y-m-d-h.i.sa');
        $this->tags = $this->resource->getTVValue('Tags');

        $this->groups = $this->resource->getTVValue('nf_groups');
        $this->tags = $this->resource->getTVValue('nf_tags');
    
        $batchSize = $this->resource->getTVValue('BatchSize');
        $this->batchSize = empty($batchSize)? 50 : $batchSize;
        $batchDelay = $this->resource->getTVValue('BatchDelay');
        $this->batchDelay = empty($batchDelay)? 1 : $batchDelay;
        $itemDelay = $this->resource->getTVValue('itemDelay');
        $this->itemDelay = empty($itemDelay)? .51 : $itemDelay;
    }


    public function strReplaceAssoc($replace, $subject) {
           $msg =  str_replace(array_keys($replace), array_values($replace), $subject);
           return $msg;

    }

    public function initializeMailer() {
        set_time_limit(0);
        $this->modx->getService('mail', 'mail.modPHPMailer');
        $mail_from = $this->modx->getOption('mail_from', $this->props);
        $this->mail_from = empty($mail_from) ? $this->modx->getOption('emailsender', null) : $mail_from;

        $mail_from_name = $this->modx->getOption('mail_from_name', $this->props);
        $this->mail_from_name = empty($mail_from_name) ? $this->modx->getOption('site_name', null) : $mail_from_name;

        $mail_sender = $this->modx->getOption('mail_sender', $this->props);
        $this->mail_sender = empty($mail_sender) ? $this->modx->getOption('emailsender', null) : $mail_sender;

        $mail_reply_to = $this->modx->getOption('mail_reply_to', $this->props);
        $this->mail_reply_to = empty($mail_reply_to) ? $this->modx->getOption('emailsender', null) : $mail_reply_to;

        $mail_subject = $this->resource->getTVValue('nf_email_subject');
        $this->mail_subject = empty($mail_subject) ? 'Update from ' . $this->modx->getOption('site_name') : $mail_subject;

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
            $this->errorHeader .= '<h3>' . $this->modx->mail->mailer->ErrorInfo . '</h3>';
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
                /* allow UserGroup name or ID */
                $g = intval($userGroupName);
                $g = is_int($g) && !empty($g) ? $userGroupName : array('name' => $userGroupName);
                $group = $this->modx->getObject('modUserGroup',$g);

                if (empty($group)) {
                    $this->errorHeader = '<h3>' . 'Could not find User Group: ' . $userGroupName . '</h3>';
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
            $this->errorHeader = '<h3>' . 'No Recipients to send to' . '</h3>';
        }
        /* skip mail send if any errors are set */
        if (!empty($this->errorHeader)) {
            $this->errorHeader = '<h3>' . 'Bulk Emails not sent' . '</h3>';
            return false;
        }
        /* $this->recipients array now complete and no errors - send bulk emails */
        $i = 1;
        $fp = fopen($this->logFile, 'w');
        if (!$fp) {
            $this->errorHeader = '<h3>' . 'Could not open log file (make sure /logs directory exists): ' . $this->logFile . '</h3>';
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
        $this->successHeader .= '<h3>' . 'Email to Subscribers sent successfully' . '</h3>';
        return true;


    }
    protected function addUsers($users, $userGroupName) {
        foreach ($users as $user) {
            /* @var $user modUser */

            $username = $user->get('username');

            $profile = $user->getOne($this->profileAlias);
            $userTags = null;
            if (! $profile) {
                $this->errorHeader = '<h3>' . 'No Profile for: ' . $username . '</h3>';
            } else {
                if ( $this->modx->getOption('sbs_use_comment_field', null, null) == 'No') {
                    $field = $this->modx->getOption('sbs_extended_field');
                    if (empty($field)) {
                        $this->errorHeader = '<h3>' . 'sbs_extended_field is not set' . '</h3>';
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
                $this->errorHeader = '<h3>' . 'User: ' . $username . ' has no email address' . '</h3>';
            }
        }
    }
    public function sendTestEmail($address, $name){
        if (empty($address)) {
            $this->errorHeader = '<h3>' . 'TestEmailAddress is empty; test email not sent' . '</h3>';
            return;
        }
        if (! $this->sendMail($address, $name)) {
            $this->errorHeader = '<h3>' . 'Mail error sending test email' . '</h3>';
        } else {
            $this->successHeader = '<h3>' . 'Test Email Sent successfully' . '</h3>';
        }
        return;
    }

    public function tweet() {

        require_once(MODX_CORE_PATH . 'components/notify/model/notify/twitteroauth.php');
        $consumer_key = $this->modx->getOption('twitter_consumer_key',$this->props, null);
        if (! $consumer_key) {
            $this->errorHeader = '<h3>' . 'Twitter Consumer Key is not set' . '</h3>';
        }
        $consumer_secret = $this->modx->getOption('twitter_consumer_secret',$this->props, null);
        if (! $consumer_secret) {
            $this->errorHeader = '<h3>' . 'Twitter Consumer Secret is not set' . '</h3>';
        }
        $oauth_token = $this->modx->getOption('twitter_oauth_token',$this->props, null);
        if (! $oauth_token) {
            $this->errorHeader = '<h3>' . 'Twitter Access Token is not set' . '</h3>';
        }        
        $oauth_secret = $this->modx->getOption('twitter_oauth_secret',$this->props, null);
        if (! $oauth_secret) {
            $this->errorHeader = '<h3>' . 'Twitter Access Token Secret is not set' . '</h3>';
        }        
        $msg = $this->tweetText;
        if (empty($msg)) {
            $this->errorHeader = '<h3>' . 'Tweet TV is empty' . '</h3>';
        } else {
            //$text = 'Tweeted from PHP - just testing some code';
            $tweet = new TwitterOAuth($consumer_key, $consumer_secret, $oauth_token, $oauth_secret);
            $response = $tweet->post('statuses/update', array('status' => $msg));
            /* This will get recent tweets */
            //$response = $tweet->get('statuses/user_timeline', array('screen_name' => 'BobRay'));

            if (!$response) {
                $this->errorHeader = '<h3>' . 'Unknown error using the Twitter API' . '</h3>';
            } elseif ($response->error) {
                $this->errorHeader = '<h3>' . 'Twitter said, there was an error' . "</h3>
                <p>$response->error</code</p>\n
                <p>Full response:</p>\n
                <pre>" . print_r($response,true) . "</pre><br />";
            } else {
                $this->successHeader .= '<h3>' . 'Tweet sent successfully' . '</h3>';
                $this->successHeader .= '<p>' . $this->tweetText . '</p>';
            }
        }
    }

    public function resetTVs() {
        /* turn the TVs off to prevent accidental resending */
        /* @var $tv modTemplateVar */

        $tv = $this->modx->getObject('modTemplateVar', array('name' => 'nf_send_test_email'));
        $tv->setValue($this->resource->get('id'), 'No');
        $tv->save();
        $tv = $this->modx->getObject('modTemplateVar', array('name' => 'nf_notify_subscribers'));
        $tv->setValue($this->resource->get('id'), 'No');
        $tv->save();
        $tv = $this->modx->getObject('modTemplateVar', array('name' => 'nf_twitter'));
        $tv->setValue($this->resource->get('id'), 'No');
        $tv->save();
        $tv = $this->modx->getObject('modTemplateVar', array('name' => 'nf_preview_email'));
        $tv->setValue($this->resource->get('id'), 'No');
        $tv->save();
        /* Need to change the TV values in memory too */

        $fields = array(
            'nf_send_test_email',
            'No',
            'default',
            '',
            'option',
        );
        $this->resource->set('nf_send_test_email', $fields);
        $fields[0] = 'nf_notify_subscribers';
        $this->resource->set('nf_notify_subscribers', $fields);
        $fields[0] = 'nf_twitter';
        $this->resource->set('nf_twitter', $fields);
        $fields[0] = 'nf_preview_email';
        $this->resource->set('nf_preview_email', $fields);
        
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

    /* not used -- for later development */
    public function fullUrls($base) {
        /* extract domain name from $base */
        $splitBase = explode('//', $base);
        $domain = $splitBase[1];
        $domain = rtrim($domain,'/ ');

        /* remove space around = sign */

        $html =& preg_replace('@(?<=href|src)\s*=\s*@', '=', $this->html);

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

        return $html;
    }

    /* not used -- for later development */
    public function imgAttributes() {
        $html =& $this->html;
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