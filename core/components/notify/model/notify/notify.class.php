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
 * MODx Notify Class
 *
 * @version Version 1.1.0-beta1
 *
 * @package  notify
 *
 * The Notify plugin for emailing resources to users
 *
 * The Notify class contains all functions relating to Notify's
 * operation.
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

    protected $html;
    protected $modx;
    protected $props;
    protected $cssMode;
    protected $cssFiles;
    protected $cssBasePath;
    protected $mail_from;
    protected $mail_from_name;
    protected $mail_sender;
    protected $mail_reply_to;
    protected $mail_subject;
    protected $groups;
    protected $batchSize;
    protected $batchDelay;
    protected $itemDelay;
    protected $errors;
    protected $logFile;
    protected $userClass;
    protected $profileAlias;
    protected $profileClass;
    protected $sortBy;
    protected $sortByAlias;
    protected $tags;
    protected $recipients;


    public function __construct(&$modx, &$props)
    {

        $this->modx =& $modx;
        $this->props =& $props;
        /* @var $modx modX */
        /* nf paths; Set the nf. System Settings only for development */
        $this->corePath = $this->modx->getOption('nf.core_path', null, MODX_CORE_PATH . 'components/notify/');
        $this->assetsPath = $this->modx->getOption('nf.assets_path', null, MODX_ASSETS_PATH . 'components/notify/');
        $this->assetsUrl = $this->modx->getOption('nf.assets_url', null, MODX_ASSETS_URL . 'components/notify/');

    }

    public function init()
    {
        $this->sortBy = $this->modx->getOption('sortBy',$this->props,'username');
        $this->sortByAlias = $this->modx->getOption('sortByAlias',$this->props,'modUser');
        $this->userClass = $this->modx->getOption('userClass',$this->props,'modUser');
        $this->profileAlias = $this->modx->getOption('profileAlias',$this->props,'Profile');
        $this->profileClass = $this->modx->getOption('profileClass',$this->props,'modUserProfile');
        $this->logFile = $this->corePath . 'notify-logs/' . $this->modx->resource->get('alias') . '--'. date('Y-m-d-h.i.sa');
        $this->errors = array();
        $cssBasePath = $this->modx->resource->getTVValue('CssBasePath');
        $this->tags = $this->modx->resource->getTVValue('Tags');

        if (empty ($cssBasePath)) {
            $cssBasePath = MODX_BASE_PATH . 'assets/components/notify/css/';
        } else {
            if (strstr($cssBasePath, '{modx_base_path}')) {
                $cssBasePath = str_replace('{modx_base_path}', MODX_BASE_PATH, $cssBasePath);
            }
        }
        $this->cssBasePath = $cssBasePath;
        $cssTv = $this->modx->resource->getTVValue('CssFile');
        $cssTv = empty($cssTv)? 'notify.css': $cssTv;
        $this->cssFiles = explode(',', $cssTv);

        $cssMode = $this->modx->resource->getTVValue('CssMode');

        if (empty ($cssMode)) {
            $this->cssMode = 'FILE';
        } else {
            $this->cssMode = strtoupper($cssMode);
        }

        /* Bulk email settings */
        $this->groups = $this->modx->resource->getTVValue('nf_groups');
        $this->tags = $this->modx->resource->getTVValue('nf_tags');

        $batchSize = $this->modx->resource->getTVValue('BatchSize');
        $this->batchSize = empty($batchSize)? 50 : $batchSize;
        $batchDelay = $this->modx->resource->getTVValue('BatchDelay');
        $this->batchDelay = empty($batchDelay)? 1 : $batchDelay;
        $itemDelay = $this->modx->resource->getTVValue('itemDelay');
        $this->itemDelay = empty($itemDelay)? .51 : $itemDelay;

    }

    public function fullUrls($base)
    {
        /* extract domain name from $base */
        $splitBase = explode('//', $base);
        $domain = $splitBase[1];
        $domain = rtrim($domain,'/ ');

        /* remove space around = sign */
        //$html = preg_replace('@(href|src)\s*=\s*@', '\1=', $html);
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

    public function inlineCss()
    {
        $root = MODX_BASE_PATH;
        //$assets_path = $root . 'assets/components/notify/';
        $core_path = $root . 'core/components/notify/';
        require $core_path . 'model/notify/css_to_inline_styles.class.php';

        $css = '';
        if (empty($this->cssFiles)) {
            $this->setError('cssFiles is empty');
        }
        foreach ($this->cssFiles as $cssFile) {
            switch ($this->cssMode) {

                case 'RESOURCE':
                    /* @var $res modResource */
                    $res = $this->modx->getObject('modResource', array('pagetitle' => $cssFile));
                    $tempCss = $res->getContent();
                    unset($res);
                    if (empty($tempCss)) {
                        $this->setError('Could not get resource content: ' . $cssFile);
                    }
                    break;
                case 'CHUNK':
                    $tempCss = $this->modx->getChunk($cssFile);
                    if (empty($tempCss)) {
                        $this->setError('Could not get chunk content: ' . $cssFile);
                    }
                default:
                case 'FILE':
                    $tempCss = file_get_contents($this->cssBasePath . $cssFile);
                    if (empty($tempCss)) {
                        $this->setError('Could not get CSS file: ' . $this->cssBasePath . $cssFile);
                    }
                    break;

            }
            $css .= $tempCss . "\n";
        }

        $ctis = new CSSToInlineStyles($this->html, $css);
        $this->html = $ctis->convert();
    }

    public function strReplaceAssoc(array $replace, $subject) {
           return str_replace(array_keys($replace), array_values($replace), $subject);
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
    public function setHtml($html) {
        $this->html = $html;
    }
    public function getHtml() {
        return $this->html;
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

                $mail_subject = $this->modx->resource->getTVValue('nf_email_subject');
                $this->mail_subject = empty($mail_subject) ? 'Update from ' . $this->modx->getOption('site_name') : $mail_subject;

        $this->modx->mail->set(modMail::MAIL_BODY, $this->html);
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
        /* $this->modx->mail->mailer->ClearBCCs(); */
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
                    $this->setError('Could not find User Group: ' . $userGroupName);
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
            $this->setError('No Recipients to send to');
        }
        /* skip mail send if any errors are set */
        if (!empty($this->errors)) {
            $this->setError('Bulk Emails not sent');
            return false;
        }
        /* $this->recipients array now complete and no errors - send bulk emails */
        $i = 1;
        $fp = fopen($this->logFile, 'w');
        if (!$fp) {
            $this->setError('Could not open log file (make sure /logs directory exists): ' . $this->logFile);
        } else {
            fwrite($fp, "MESSAGE\n*****************************\n" . $this->html . "\n*****************************\n\n");
            //fwrite($fp,print_r($this->recipients, true));
        }
        foreach ($this->recipients as $recipient) {
            if ($this->sendMail($recipient['email'], $recipient['fullName'])) {
                if ($fp) {
                    fwrite($fp, 'Successful send to: ' . $recipient['email'] . ' (' . $recipient['fullName'] . ') User Tags: ' . $recipient['userTags'] . "\n");
                }
            } else {
                if ($fp) {
                    $e = array_pop($this->errors);
                    fwrite($fp, 'Error sending to: ' . $recipient['email'] . ' (' . $recipient['fullName'] . ') ' . $e . "\n");
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
        return true;


    }
    protected function addUsers($users, $userGroupName) {
        foreach ($users as $user) {
            /* @var $user modUser */

            $username = $user->get('username');

            $profile = $user->getOne($this->profileAlias);
            $userTags = null;
            if (! $profile) {
                $this->setError('No Profile for: ' . $username);
            } else {
                if ( $this->modx->getOption('sbs_use_comment_field', null, null) == 'No') {
                    $field = $this->modx->getOption('sbs_extended_field');
                    if (empty($field)) {
                        $this->setError('sbs_extended_field is not set');
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
                $this->setError('User: ' . $username . ' has no email address');
            }
        }
    }
    public function sendTestEmail($address, $name){
        if (empty($address)) {
            $this->setError('TestEmailAddress is empty; test email not sent');
            return;
        }
        if (! $this->sendMail($address, $name)) {
            $this->setError('Test email not sent');
        }
        return;
    }

    public function tweet() {

        require_once(MODX_CORE_PATH . 'components/notify/model/twitteroauth.php');
        $consumer_key = $this->modx->getOption('twitter_consumer_key',null,null);
        if (! $consumer_key) {
            $this->setError('twitter_consumer_key is not set');
        }
        $consumer_secret = $this->modx->getOption('twitter_consumer_secret',null,null);
        if (! $consumer_secret) {
            $this->setError('twitter_consumer_secret is not set');
        }
        $oauth_token = $this->modx->getOption('twitter_oauth_token',null,null);
        if (! $oauth_token) {
            $this->setError('twitter_oauth_token is not set');
        }        
        $oauth_secret = $this->modx->getOption('twitter_oauth_secret',null,null);
        if (! $oauth_secret) {
            $this->setError('twitter_oauth_secret is not set');
        }        
        $msg = $this->modx->resource->getTVValue('nf_tweet');

        if (empty($msg)) {
            $this->setError('Tweet TV is empty');
        } else {
            //$text = 'Tweeted from PHP - just testing some code';
            $tweet = new TwitterOAuth($consumer_key, $consumer_secret, $oauth_token, $oauth_secret);
            $response = $tweet->post('statuses/update', array('status' => $msg));

            //$response = $tweet->get('statuses/user_timeline', array('screen_name' => 'BobRay'));

            if (!$response) {
                $this->setError("<h3>Unknown error using the Twitter API</h3>\n");
            } elseif ($response->error) {
                $this->setError("<h3>Twitter said, there was an error</h3>\n
                <p>$response->error</code</p>\n
                <p>Full response:</p>\n
                <pre>" . var_dump($response) . "</pre><br />");
            } else {
                 /* debugging stuff */
                /*echo "<_pre>" . var_dump($response) . "</pre_>"; */

               /* foreach ($response as $x) {
                    echo ($x->text). "\n";

                }*/
            }
        }
    }
    public function showErrorStrings() {
           $retVal = '';
           foreach ($this->errors as $error) {
               $retVal .= '<h3>' . $error . "</h3>\n";
           }
           return $retVal;
    }

    public function setError($error){
        $this->errors[] = $error;
    }
    public function getErrors() {
        return count($this->errors);
    }

} /* end class */
