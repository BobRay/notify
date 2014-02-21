<?php

/**
 * Notify
 *
 * Copyright 2012-2013-2013 Bob Ray
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

    /** @var $modx modX */
    /** @var $resource modResource */
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
    /** @var $recipients array */
    protected $recipients = array();
    protected $emailText;
    protected $emailTpl;
    protected $tweetTpl;
    protected $tweetText;
    protected $replace;
    /** @var $successMessages array */
    protected $successMessages;
    /** @var $errors array */
    protected $errors;
    protected $pageId;
    protected $pageAlias;
    protected $sendBulkEmail;
    protected $sendTestEmail;
    protected $sendTweet;
    /** @var $previewPage modResource */
    protected $previewPage;
    protected $formTpl;
    protected $urlShorteningService;
    protected $shortenUrls;
    /** @var $shortener UrlShortener */
    public $shortener;
    protected $tplType; /* new, update, blank, custom */
    /** @var $unSub Unsubscribe */
    protected $unSub;
    protected $unSubUrl;
    protected $unSubTpl;
    /** @var $profile modUserProfile */
    protected $profile;
    /** @var $html2text html2text */
    protected $html2text;
    protected $requireAllTags;
    protected $requireDefault;
    protected $badSends = 0;
    protected $useMandrill = false;
    /** @var $mx MandrillX */
    protected $mx = null;
    /** @var $userFields array - array of user placeholders used in message */
    protected $userFields = array();
    protected $debug;


    /**
     * Class constructor
     * 
     * @param $modx modX - $modx object
     * @param $props array - $scriptProperties array
     */
    public function __construct(&$modx, &$props) {
        /* @var $modx modX */
        /* @var $resource modResource */

        $this->modx =& $modx;
        $this->props = $props;

        /* nf paths; Set the nf. System Settings only for development */
        $this->corePath = $this->modx->getOption('nf.core_path', null, MODX_CORE_PATH . 'components/notify/');
    }

    public function init() {
        $this->useMandrill = $this->modx->getOption('nfUseMandrill', $this->props, false);
        $this->useMandrill = empty($this->useMandrill)
            ? false
            : true;
        $this->errors = array();
        $this->successMessages = array();
        $this->previewPage = $this->modx->getObject('modResource', array('alias' => 'notify-preview'));
        if (!$this->previewPage) {
            $this->setError($this->modx->lexicon('nf.could_not_find_preview_page'));
        }

        $this->formTpl = $this->modx->getOption('nfFormTpl', $this->props, 'NfNotifyFormTpl');
        $this->formTpl = empty($this->formTpl)
            ? 'NfNotifyFormTpl'
            : $this->formTpl;
        $this->requireDefault = $this->modx->getOption('requireAllTagsDefault', $this->props, false);
        $this->setTags();
        $this->setUserGroups();

        /* Message Settings */
        $this->mail_from = $this->modx->getOption('mailFrom', $this->props,
            $this->modx->getOption('emailsender'));
        if (empty($this->mail_from)) {
            $this->mail_from = $this->modx->getOption('emailsender');
        }
        $this->props['mail_from'] = $this->mail_from;

        $this->mail_from_name = $this->modx->getOption('mailFromName',
            $this->props, $this->modx->getOption('site_name', NULL));
        if (empty($this->mail_from_name)) {
            $this->mail_from_name = $this->modx->getOption('site_name', NULL);
        }
        $this->props['from_name'] = $this->mail_from_name;
        $this->mail_sender = $this->modx->getOption('mailSender',
            $this->props, $this->mail_from);

        $this->mail_reply_to = $this->modx->getOption('mailReplyTo',
            $this->props, $this->mail_from);
        if (empty($this->mail_reply_to)) {
            $this->mail_reply_to = $this->mail_from;
        }

        $this->mail_subject = isset($_POST['nf_email_subject'])
            ? $_POST['nf_email_subject']
            : 'Update from ' . $this->modx->getOption('site_name');

        $this->props['mail_subject'] = $this->mail_subject;

        /* Unsubscribe settings */
        $unSubId = $this->modx->getOption('sbs_unsubscribe_page_id', NULL, NULL);
        $this->unSubUrl = $this->modx->makeUrl($unSubId, "", "", "full");
        $subscribeCorePath = $this->modx->getOption('subscribe.core_path', NULL,
            $this->modx->getOption('core_path', NULL, MODX_CORE_PATH) .
            'components/subscribe/');
        require_once($subscribeCorePath . 'model/subscribe/unsubscribe.class.php');
        $unSubTpl = $this->modx->getOption('nfUnsubscribeTpl',
            $this->props, 'NfUnsubscribeTpl');
        $this->unSub = new Unsubscribe($this->modx, $this->props);
        $this->unSub->init();
        $this->unSubTpl = $this->modx->getChunk($unSubTpl);
        $profile = $this->modx->user->getOne('Profile');
        $this->profile = $profile
            ? $profile
            : NULL;

        $this->debug = $this->modx->getOption('debug', $this->props, false);

        set_time_limit(0);
    }

    /**
     * @param $action string - 'displayform' or 'handleSubmission'
     * @return string - returns formTpl or empty string 
     */
    public function process($action) {

        switch($action) {

            /* *********************************************** */
            case 'displayForm':  /* Not a repost */
                $this->pageId = isset($_POST['pageId'])? $_POST['pageId'] : '';

                if (empty($this->pageId) ) {
                    $this->setError($this->modx->lexicon('nf_page_id_is_empty'));
                    return '';
                }
                if ($this->requireDefault) {
                    $this->modx->setPlaceholder('nf_require_checked', 'checked="checked"');
                }

                $this->tplType = isset($_POST['pageType'])? $_POST['pageType'] : '';
                /* set Tpl name using $_POST data */
                $this->emailTpl = 'NfSubscriberEmailTpl' . $this->tplType;
                $this->tweetTpl = 'NfTweetTpl' . $this->tplType;
                if (! $this->prepareTpl()) {
                    return '';
                }

                break;
            /* *********************************************** */
            case 'handleSubmission':
                $this->requireAllTags = isset($_POST['nf_require_all_tags']) &&
                    (!empty($_POST['nf_require_all_tags']));
                $this->pageAlias = isset($_POST['pageAlias'])? $_POST['pageAlias']: 0;
                $this->modx->setPlaceholder('pageAlias',$this->pageAlias);
                $this->sendTestEmail = isset($_POST['nf_send_test_email']);
                $this->sendBulkEmail = isset($_POST['nf_notify']);
                $this->sendTweet = isset($_POST['nf_send_tweet']);
                $this->emailText = isset($_POST['nf_email_text'])? $_POST['nf_email_text'] : '';
                $this->tweetText = isset($_POST['nf_tweet_text'])? $_POST['nf_tweet_text'] : '';
                if ($this->sendTestEmail) {
                    $this->modx->setPlaceholder('nf_send_test_email_checked','checked="checked"');
                }
                if ($this->requireAllTags) {
                    $this->modx->setPlaceholder('nf_require_checked', 'checked="checked"' );
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
                // $this->fullUrls();
                $this->imgAttributes();
                $this->updatePreviewPage($this->emailText);


                /* **************************** */

                /* perform requested actions */
                if ($this->sendBulkEmail || $this->sendTestEmail) {

                    require_once('html2text.php');
                    $this->html2text = new html2text();
                    /* Set preview in case user forgot */
                    $this->initEmail();
                    $this->initializeMailer();

                    if ($this->sendBulkEmail) {
                        /* send bulk email */
                        $this->sendBulkEmail();
                    }

                    if ($this->sendTestEmail) {
                        /* send test email */
                        $testEmailAddress = isset($_POST['nf_test_email_address'])
                            ? $_POST['nf_test_email_address']
                            : '';
                        $this->tags = '';
                        $this->groups = '';
                        $this->sendBulkEmail($testEmailAddress);
                    }
                }
                if ($this->sendTweet) {
                    $this->tweet();
                }

                return $this->modx->getChunk($this->formTpl);

                break;

            default:
                break;

        }

        return "";
    }

    public function prepareTpl() {
        if ($this->tplType == 'blank') {
            $this->emailText = '';
            $this->tweetText = '';
            return true;
        }

        $this->resource = $this->modx->getObject('modResource', $this->pageId);
        if (!$this->resource) {
            $this->setError($this->modx->lexicon('nf.could_not_get_resource'));
            return false;
        } else {
            $this->modx->setPlaceholder('pageAlias', $this->resource->get('alias'));
        }

        $notifyFacebook = $this->modx->getOption('notifyFacebook', $this->props, NULL);
        $this->urlShorteningService = $this->modx->getOption('urlShorteningService', $this->props, 'None');
        $this->shortenUrls = stristr($this->urlShorteningService, 'None') === false;

        if ($this->shortenUrls) {
            require_once $this->corePath . 'model/notify/urlshortener.class.php';
            $this->shortener = new UrlShortener($this->props);
        }
        $fields = $this->resource->toArray();
        $fields['url'] = $this->modx->makeUrl($this->pageId, "", "", "full");
        $includeTVs = $this->modx->getOption('includeTVs', $this->props, false);
        $includeTVList = !empty($includeTVList)
            ? explode(',', $includeTVList)
            : array();

        if ($includeTVs) {
            $renderTvs = $this->modx->getOption('renderTvs', $this->props, true);
            if (!empty($includeTVList)) {
                $tvs = $this->modx->getCollection('modTemplateVar', array('name:IN' => $includeTVList));
            } else {
                $tvs = $this->resource->getMany('TemplateVars');
            }

            foreach ($tvs as $tvId => $templateVar) {
                /** @var $templateVar modTemplateVar */
                if ($renderTvs) {
                    $fields[$templateVar->get('name')] = $templateVar->renderOutput($this->pageId);
                } else {
                    $fields[$templateVar->get('name')] = $templateVar->getValue($this->pageId);
                }
            }
        }

        $this->emailText = $this->modx->getChunk($this->emailTpl, $fields);

        if (empty($this->emailText)) {
            $this->setError($this->modx->lexicon('nf.could_not_find_email_tpl_chunk'));
        } else {
            /* convert any relative URLS in email text */
            $this->fullUrls();
            /* Fix image attributes */
            $this->imgAttributes();
            /* Inject unsubscribe link */
            $this->emailText = $this->injectUnsubscribe($this->emailText);
            /* Convert all {{-style placeholders to lowercase */
            $pattern = '#\{\{\+([a-zA-Z_\-]+?)\}\}#';
            preg_match_all($pattern, $this->emailText, $matches);
            if (isset($matches[1])) {
                foreach($matches[1] as $match) {
                    $this->emailText = str_replace('{{+' . $match . '}}',
                        '{{+' . strtolower($match) . '}}', $this->emailText);
                }
            }

            /* shorten URLs if property is set */
            if ($this->shortenUrls) {
                $this->shortenUrls($this->emailText);
            }
        }
        $this->tweetText = $this->modx->getChunk($this->tweetTpl, $fields);
        if (empty($this->tweetText)) {
            $this->setError($this->modx->lexicon('nf.could_not_find_tweet_tpl_chunk'));
        } else {
            if ($this->shortenUrls) {
                $this->shortenUrls($this->tweetText);
            }
            if ($notifyFacebook) {
                $this->tweetText = rtrim($this->tweetText, ' ') . ' #fb';
            }
        }

    return true;
    }
    /**
     * Injects Unsubscribe URL above body tag or appends it if no body tag
     * 
     * @param $content string - Entire page content
     * @return string - Content with Unsubscribe link injected
     */
    public function injectUnsubscribe($content) {
        $tpl = $this->unSubTpl;
        /* for backward compatibility */

        $tpl = str_ireplace('"UNSUBSCRIBE_URL"', '"{{+UNSUBSCRIBE_URL}}"', $tpl);
        $tpl = str_ireplace('{{UNSUBSCRIBE_URL}}', '{{+UNSUBSCRIBE_URL}}', $tpl);
        if (stristr($content, '</body>')) {
            /* inject link just above the closing body tag */
            // $html = $content;
            $content = str_replace('</body>', "\n" . $tpl . "\n" . '</body>', $content);
        } else {
            /* append link to the end if there is no body tag */
            $content = $content . $tpl;
        }

        return $content;

    }

    /**
     * Shorten URLs using specified service
     * @param $text string - url to shorten
     */
    public function shortenUrls(&$text) {
        $this->shortener->init_curl();
        $this->shortener->process($text, $this->urlShorteningService);
        $this->shortener->close_curl();
    }

    /**
     * Displays fully formatted form
     *
     * @return string - form
     */
    public function displayForm() {
        $testEmailAddress = $this->modx->getOption('nfTestEmailAddress', $this->props, '');
        $testEmailAddress = empty($testEmailAddress)? $this->modx->user->get('username'): $testEmailAddress;

        $this->modx->setPlaceholder('nf_test_email_address', $testEmailAddress);

        $groups = $this->modx->getOption('groups', $this->props, 'Subscribers');
        if (empty($groups)) {
            $groups = 'Subscribers';
        }
        $this->modx->setPlaceholder('nf_groups', $groups);


        $tags = $this->modx->getOption('tags', $this->props, '');
        $this->modx->setPlaceholder('nf_tags', $tags);

        /* @var $tempPage modResource */

        $content = $this->emailText;
        $this->updatePreviewPage($content);

        $this->modx->setPlaceholder('nf_email_text', $content);
        $subjectTpl = $this->modx->getOption('nfSubjectTpl', $this->props);
        $subjectTpl = empty($subjectTpl)? 'NfEmailSubjectTpl' : $subjectTpl;
        $this->modx->setPlaceholder('nf_email_subject',$this->modx->getChunk($subjectTpl));
        $this->modx->setPlaceholder('nf_tweet_text', $this->tweetText);
        return $this->modx->getChunk($this->formTpl);
    }

    /**
     * Updates the preview page resource and sets the URL placeholder for the
     * iFrame to show it in the form
     *
     * @param $content string - content to place in content field of preview resource
     */
    protected function updatePreviewPage($content) {
        $this->previewPage->setContent($content);
        $this->previewPage->save();
        $this->modx->setPlaceholder('nf_temp_url', $this->modx->makeUrl($this->previewPage->get('id'), "", "", "full"));

    }

    /**
     * Adds an error string to the errors array
     *
     * @param $msg string - error message string
     */
    protected function setError($msg) {
        $this->errors[] = $msg;
    }

    /**
     * Returns true if there are errors, false if not
     *
     * @return bool
     */
    public function hasErrors() {
        return ! empty($this->errors);
    }

    /**
     * Returns the current array of error strings
     *
     * @return array
     */
    public function getErrors() {
        return $this->errors;
    }

    /**
     * Returns HTML to display all current error messages
     *
     * @return string
     */
    public function displayErrors() {
        $msg = "\n" . '<p class="nf_error">';
        foreach ($this->errors as $error) {
            $msg .= "\n" . '<br />' . $error;
        }
        return $msg . "\n</p>";
    }

    /**
     * Adds a success message to the array of success messages to print at the top of the form
     *
     * @param $msg string - success message to add
     */
    public function setSuccess($msg) {
        $this->successMessages[] = $msg;
    }

    /**
     * Creates HTML to display the current success messages
     *
     * @return string
     */
    public function displaySuccessMessages() {
        $msg = "\n" . '<p class="nf_success">';
        foreach($this->successMessages as $message)
            $msg .= "\n<br />" . $message;
        return $msg . "\n</p>";
    }

    /**
     * Returns the text for the Tweet
     *
     * @return string
     */
    public function getTweetText() {
        return $this->tweetText;
    }

    /**
     * Returns the current email text
     *
     * @return string
     */
    public function getEmailText() {
        return $this->emailText;
    }

    /**
     * Initialize variables used for mailing
     */
    protected function  initEmail() {
        $this->sortBy = $this->modx->getOption('sortBy',$this->props);
        $this->sortBy = empty($this->sortBy)? 'username' : $this->sortBy;
        $this->sortByAlias = $this->modx->getOption('sortByAlias',$this->props);
        $this->sortByAlias = empty ($this->sortByAlias)? 'modUser' : $this->sortByAlias;
        $this->userClass = $this->modx->getOption('userClass',$this->props);
        $this->userClass = empty($this->userClass)? 'modUser' : $this->userClass;
        $this->profileAlias = $this->modx->getOption('profileAlias',$this->props,'Profile');
        $this->profileAlias = empty($this->profileAlias)? 'Profile' : $this->profileAlias;
        $this->profileClass = $this->modx->getOption('profileClass',$this->props,'modUserProfile');
        $this->profileClass = empty($this->profileClass)? 'modUserProfile' : $this->profileClass;
        $this->logFile = $this->corePath . 'notify-logs/' . $this->pageAlias . '--'. date('Y-m-d-h.i.sa');

        $this->tags = isset($_POST['nf_tags'])? $_POST['nf_tags']: '';

        $this->groups = isset($_POST['nf_groups'])? $_POST['nf_groups']: '';

        $this->batchSize = (integer) $this->modx->getOption('batchSize', $this->props, 25);
        $this->batchDelay = (integer) $this->modx->getOption('batchDelay', $this->props, 1);
        $this->itemDelay = (float) $this->modx->getOption('itemDelay', $this->props, .51);

    }


    /**
     * Uses an associative array for replacing multiple strings
     * @param $replace - array of search => replace terms
     * @param $subject string - string to do replacement on
     * @return string - $subject with replacements done
     */
    public function strReplaceAssoc($replace, $subject) {
           $msg =  str_replace(array_keys($replace), array_values($replace), $subject);
           return $msg;

    }

    /**
     * Initialize the modx Mailer
     */
    public function initializeMailer() {
        set_time_limit(0);
        $this->modx->getService('mail', 'mail.modPHPMailer');


        $this->modx->mail->set(modMail::MAIL_FROM, $this->mail_from);
        $this->modx->mail->set(modMail::MAIL_FROM_NAME, $this->mail_from_name);
        $this->modx->mail->set(modMail::MAIL_SENDER, $this->mail_sender);
        $this->modx->mail->set(modMail::MAIL_SUBJECT, $this->mail_subject);
        $this->modx->mail->address('reply-to', $this->mail_reply_to);
        $this->modx->mail->setHtml(true);
    }


    /**
     * Sends an individual email - not used if sending via Mandrill
     *

     * @param $fields array - fields for user placeholders.
     * @return bool - true on success; false on failure to mail.
     */
    public function sendMail($fields) {

        $content  = $this->emailText;

        /* Get Fields used in Tpl */
        $fieldsUsed = $this->userFields;

        foreach ($fields as $key => $value) {
            if (is_array($value)) {
                continue;
            }
            if (in_array($key, $fieldsUsed)) {}
            if (! is_array($value)) {
                $content = str_replace('{{+' . $key . '}}', $value, $content);
            }
        }

        $this->modx->mail->set(modMail::MAIL_BODY, $content);
        $this->html2text->set_html($content);
        $text = $this->html2text->get_text();
        $this->modx->mail->set(modMail::MAIL_BODY_TEXT, $text );
        $this->modx->mail->address('to', $fields['email'], $fields['name']);
        $success = $this->modx->mail->send();
        if (! $success) {
            $this->setError($this->modx->mail->mailer->ErrorInfo);
            $this->badSends++;
        }
        $this->modx->mail->mailer->ClearAddresses();
        return $success;

    }
    public function getUserFields() {
        $content = $this->emailText;

        /* Get Fields used in Tpl */
        $fieldsUsed = array();
        $pattern = '#\{\{\+([a-zA-Z_\-]+?)\}\}#';
        preg_match_all($pattern, $content, $matches);
        if (isset($matches[1]) && (!empty($matches[1]))) {
            $fieldsUsed = $matches[1];
        }
        return $fieldsUsed;
    }

    public function sendBulkEmail($singleEmail = null) {
        $singleUser = $singleEmail !== null;

        if ($this->useMandrill) {
            require_once $this->modx->getOption('mandrillx.core.path', null,  MODX_CORE_PATH) . 'components/mandrillx/model/mandrillx/mandrillx.class.php';

            $apiKey = $this->modx->getOption('mandrill_api_key');
            if (empty($apiKey)) {
                $this->setError('nf.no_mandrill_api_key~~No Mandrill API Key');
                return false;
            } else {
                $this->props['html'] = $this->emailText;
                $this->html2text->set_html($this->emailText);
                $text = $this->html2text->get_text();
                $this->props['text'] = $text;
                unset($text);
                $this->mx = new MandrillX($this->modx, $apiKey, $this->props);
                if (! $this->mx instanceof MandrillX) {
                    $this->setError($this->modx->lexicon('nf.no_mandrill~~Could not instantiate Mandrill object'));
                    return false;
                }

                $this->mx->init();
                $this->userFields = $this->mx->getUserPlaceholders();
            }
        } else {
            $this->userFields = $this->getUserFields();
        }

        $groups = empty($this->groups)
            ? array()
            : explode(',', $this->groups);

        foreach ($groups as $key => $group) {
            $group = trim($group);
            if (!is_numeric($group)) {
                $grp = $this->modx->getObject('modUserGroup', array('name' => $group));
                $groups[$key] = $grp
                    ? $grp->get('id')
                    : '';
                unset($grp);
            } else {
                $groups[$key] = $group;
            }
        }

        $userClass = $this->userClass;

        $c = $this->modx->newQuery($userClass);
        $c->select($this->modx->getSelectColumns($userClass, $userClass, "", array(
            'id',
            'username',
            'active',
        )));
        $c->sortby($this->modx->escape('username'), 'ASC');
        if ($singleUser) {
            if ($u = $this->modx->getObject('modUser', array('username' => $singleEmail))) {
                $c->where(array('username' => $singleEmail));
                unset($u);
            } elseif ($p = $this->modx->getObject('modUserProfile', array('email' => $singleEmail))) {
                $c->where(array('id' => $p->get('internalKey') ));
                unset($p);
            } else {
                $this->setError($this->modx->lexicon('nf_user_not_found~~User Not Found'));
                return false;
            }

        } else if (!empty($groups)) {
            $c->where(array(
                'UserGroupMembers.user_group:IN' => $groups,
                'active'                         => '1',
            ));
            $c->leftJoin('modUserGroupMember', 'UserGroupMembers');
        } else {
            $c->where(array(
                'active' => '1',
            ));
        }

        $c->prepare();
        $totalCount = $this->modx->getCount('modUser', $c);
        if ($this->debug) {
            echo "<br>Total Count: " . $totalCount;
        }
        $totalSent = 0;
        $i = 0;
        $offset = 0;
        while ($offset < $totalCount) {
            $i++;

            $c->limit($this->batchSize, $offset);
            $c->prepare();
            $users = $this->modx->getCollectionGraph($userClass, '{"Profile":{}', $c);
            // echo "<br>User Count: " . count($users);
            $offset += $this->batchSize;

            if ($this->debug) {
            $msg = "\n\n<br />" . $i . "  Count: " . count($users) .
                "\n<br />Offset: " . $offset . "\n<br />BatchSize: " .
                $this->batchSize;
                echo($msg);
            }
            $sentCount = 0;
            foreach ($users as $user) {
                /** @var $user modUser */
                $username = $user->get('username');
                if (!empty($this->tags)) {
                    if (!$this->qualifyUser($user->Profile, $username, $this->requireAllTags)) {
                        continue;
                    }
                }
                /* Now we have a user to send to */
                $fields = array();
                $fields['username'] = $username;
                $fields['unsubscribe_url'] = $this->unSub->createUrl($this->unSubUrl, $user->Profile);
                $fields = array_merge($user->Profile->toArray(), $fields);
                if ($this->modx->getOption('useExtendedFields', $this->props, false)) {
                    $extended = $user->Profile->get('extended');
                    $fields = array_merge($extended, $fields);
                }
                $fields['tags'] = $this->tags;
                if (isset($user->Extra) && (!empty($user->Extra))) {
                    $fields = array_merge($user->Extra->toArray(), $fields);
                }

                $fields['name'] = empty($fields['fullname'])? $fields['username'] : $fields['fullname'];

                /* If firstname field is not set, extract it from fullname */
                $fields['firstname'] = isset($fields['firstname']) && (!empty($fields['firstname']))
                    ? $fields['firstname']
                    : substr($fields['name'], 0, strpos($fields['name'], ' '));
                $fields['firstname'] = !empty($fields['firstname'])? $fields['firstname'] : $username;
                /* Send the email */
                if ($this->useMandrill) {
                    /* This will not trigger an error because the message
                       is not sent here */
                    $this->addUsertoMandrill($fields);
                } else {
                    $this->sendMail($fields);
                }
                if ($this->debug) {
                    echo "\n" . $user->get('username') . ' -- ' . $user->Profile->get('email');
                }
                $sentCount++;

            }
            if (!empty($sentCount)) {
                $this->setSuccess($this->modx->lexicon('sending_batch_of~~Sending Batch of') .
                    ' ' . $sentCount);
            }
            if ($this->useMandrill) {
                $results = $this->mx->sendMessage();
                $this->mx->clearUsers();
                if ($this->mx->hasError()) {
                    $errors = $this->mx->getErrors();
                    $this->successMessages = array();
                    foreach($errors as $error) {
                        $this->setError($error);
                    }
                }
                if ($this->debug) {
                    echo "\n<pre>" . print_r($results, true) . "</pre>\n";
                }
            }
            $totalSent += $sentCount;


        }
        if ((! $this->hasErrors()) && $totalSent) {
            $msg = $this->modx->lexicon('nf.email_to_subscribers_sent_successfully');
            $msg = str_replace('[[+nf_number]]', $totalSent, $msg);
            $this->setSuccess($msg);
        }
        if ($totalSent == 0) {
            $this->setError($this->modx->lexicon('nf_no_messages_sent~~No Messages Sent'));
        }
        return true;
    }

    /**
     * See if User should receive email based on
     * tags selected in form
     *
     * @param $profile modUserProfile - User Profile object
     * @param $username string
     * @param bool $requireAll
     * @return bool - True if use should receive email
     */
    public function qualifyUser($profile, $username, $requireAll = false) {

        /* Get User's Tags */
        $userTags = NULL;
        if (!$profile) {
            $this->setError($this->modx->lexicon('nf.no_profile_for') . ': ' . $username);
        } else {
            if ($this->modx->getOption('sbs_use_comment_field', NULL, NULL) == 'No') {
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
        }
        $hasTag = false;
        if (!empty($userTags)) {
            $tags = explode(',', $this->tags);

            foreach ($tags as $tag) {
                $tag = trim($tag);
                $hasTag = false;
                if ((!empty($tag)) && stristr($userTags, $tag)) {
                    $hasTag = true;
                    if (!$requireAll) {
                        break;
                    }
                }
                if ((!$hasTag) && $requireAll) {
                    /* needs all tags and doesn't have this one, skip to next user */
                    $hasTag = false;
                    break;
                }
            }
        }

        return $hasTag;
    }

    /**
     * Add user's info to the Mandrill Message array
     * @param $fields array - user fields with values
     */
    protected function addUserToMandrill($fields) {
        if ($this->debug) {
            echo $this->modx->lexicon('nf_send_user_mandrill~~Sending to user (Mandrill)') .
                ': ' . $fields['username'];
        }
        if ($this->mx) {
            $this->mx->addUser($fields);
        }

    }
    /**
     * Fills the $this->recipients array and sends email to all users on it
     *
     * @return bool - true on success, false on failure
     */


    public function XsendBulkEmail() {
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
            fwrite($fp, "MESSAGE\n*****************************\n" . 
                $this->emailText .
                "\n*****************************\n\n");
            
        }
        foreach ($this->recipients as $recipient) {
            /* @var  array $recipient  */
            if ($this->sendMail($recipient['email'], $recipient['fullName'],
                $recipient['profileId'], $recipient['username'])) {
                if ($fp) {
                    fwrite($fp, 'Successful send to: ' . $recipient['email'] . ' (' . $recipient['fullName'] . ') User Tags: ' . $recipient['userTags'] . "\n");
                }
            } else {
                if ($fp) {
                    fwrite($fp, 'Error sending to: ' . 
                        $recipient['email'] . ' (' . 
                        $recipient['fullName'] . ') ' .
                        "\n");
                }


            }
            sleep($this->itemDelay);

            /* implement batch delay if it's time */
            if (($i % $this->batchSize) == 0) {
                sleep($this->batchDelay);
                set_time_limit(0);
            }
            $i++;
        }
        if ($fp) {
            fclose($fp);
        }
        $successCount = count($this->recipients) - $this->badSends;
        $msg = $this->modx->lexicon('nf.email_to_subscribers_sent_successfully');
        $msg = str_replace('[[+nf_number]]', $successCount, $msg);
        $this->setSuccess($msg);
        return true;


    }

    /**
     * Adds users to $this->recipients array 
     * 
     * @param $users array of modUserObjects
     * @param $userGroupName string - name of user group being processed
     */
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
                    if ( (!$hasTag) && $this->requireAllTags) {
                        /* needs all tags and doesn't have this one, skip to next user */
                        continue 2;
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
                    'profileId' => $profile->get('id'),
                    'username' => $username,
                );
            } else {
                $this->setError($username . ' ' .  $this->modx->lexicon('nf.has_no_email_address'));
            }
        }
    }

    /**
     * Sends a test email to the selected test email address
     *
     * @param $address string - email address for test email
     * @param $name string - name to use
     */
    public function sendTestEmail($address, $name){

        if (empty($address)) {
            $this->setError ($this->modx->lexicon('nf.test_email_address_empty') . $this->modx->lexicon('nf.test_email_not_sent'));
            return;
        }
        if (! $this->profile instanceof modUserProfile) {
            $this->setError('Admin Profile is empty');
        }
        if (! $this->sendMail($address, $name, $this->profile, $name)) {
            $this->setError($this->modx->lexicon('nf.mail_error_sending_test_email'));
        } else {
            $this->setSuccess($this->modx->lexicon('nf.test_email_sent_successfully'));
        }
        return;
    }

    /**
     * Sends a Tweet from the form via Twitter API
     */
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
            $tweet = new TwitterOAuth($consumer_key, $consumer_secret, $oauth_token, $oauth_secret);
            $response = $tweet->post('statuses/update', array('status' => $msg));
            /* This will get recent tweets */
            /* $response = $tweet->get('statuses/user_timeline', array('screen_name' => 'BobRay')); */

            if ($response == null) {
                $this->setError($this->modx->lexicon('nf.unknown_error_using_twitter_api'));
            } elseif (isset($response->errors)) {
                $this->setError('<p>' . $this->modx->lexicon('nf.twitter_said_there_was_an_error') .  ': ' . $response->errors[0]->message . '</p><br />');
            } else {
                $this->setSuccess($this->modx->lexicon('nf.tweet_sent_successfully'));
            }
        }
    }


    /**
     * Writes debugging code to 'debug' chunk
     *
     * @param $message string - message to write
     * @param bool $clear - if set, chunk will be cleared before adding this message
     */
    public function my_debug($message, $clear = false)
    {
        /* @var $chunk modChunk */
        $chunk = $this->modx->getObject('modChunk', array('name' => 'Debug'));

        if (!$chunk) {
            $chunk = $this->modx->newObject('modChunk', array('name' => 'Debug'));
            $chunk->save();
            $chunk = $this->modx->getObject('modChunk', array('name' => 'Debug'));
        }
        if ($clear) {
            $content = '';
        } else {
            $content = $chunk->getContent();
        }
        $content .= "\n" . $message . "\n";
        $chunk->setContent($content);
        $chunk->save();
    }


    /**
     * Correct any non-full urls in email text
     */
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


    /**
     * Fix image attributes for Microsoft Mail
     */
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

    /* ToDo: Set user groups with JS like the tags */

    public function setUserGroups(){
        $groups = '';
        $groupChunkName = $this->modx->getOption('groupListChunkName', $this->props, 'sbsGroupListTpl');
        $groupList = $this->modx->getChunk($groupChunkName);
        if (!empty($groupList)) {

            $src = '<script type="text/javascript">
function nf_insert_group(group) {
    var text = document.getElementById("nf_groups").value;
    if (text.indexOf(group) != -1) {
       text= text.replace("," + group + ",","," );
       text = text.replace(group + ",","");
       text = text.replace("," + group,"");
       text = text.replace(group,"");
    } else {
        if (text) {
        text = text + "," + group;
        } else {
          text = group;
        }
    }
    var groupArray = text.split(",");
    groupArray.sort();
    text = groupArray.join(",");
    document.getElementById("nf_groups").value = text;
return false;
    }
</script>';

            $this->modx->regClientStartupScript($src);
            $groups = '<p>';
            $groupArray = explode('||', $groupList);
            natcasesort($groupArray);
            $i = 0;
            foreach ($groupArray as $t) {
                /* $t = strtolower($t); */
                $pos = strpos($t, '==');
                $group = $pos
                    ? substr($t, $pos + 2)
                    : $t;
                $group = trim($group);
                $groups .= '<button name="button' . $i . '" id="button' . $i . '" type="button" class="nf_group" onclick="nf_insert_group(' . "'" . $group . "'" . ');"' . '">' . $group . "</button>\n";
                $i++;
            }
            $groups .= '</p>';
        }
        $this->modx->setPlaceholder('nf_group_list', $groups);
    }
    /**
     * Gets the possible tags from the preList Tpl chunk and, if not empty,
     * injects the HTML and JS code to let user add tags by clicking on the buttons
     */
    protected function setTags() {
        $tags = '';
        $tagChunkName = $this->modx->getOption('prefListChunkName', $this->props, 'sbsPrefListTpl');
        $tagList = $this->modx->getChunk($tagChunkName);
        if (!empty($tagList)) {

            $src = '<script type="text/javascript">
function nf_insert_tag(tag) {
    var text = document.getElementById("nf_tags").value;
    if (text.indexOf(tag) != -1) {
       text= text.replace("," + tag + ",","," );
       text = text.replace(tag + ",","");
       text = text.replace("," + tag,"");
       text = text.replace(tag,"");
    } else {
        if (text) {
        text = text + "," + tag;
        } else {
          text = tag;
        }
    }
    var tagArray = text.split(",");
    tagArray.sort();
    text = tagArray.join(",");
    document.getElementById("nf_tags").value = text;
return false;
    }
</script>';

            $this->modx->regClientStartupScript($src);
            $tags = '<p>';
            $tagArray = explode('||', $tagList);
            natcasesort($tagArray);
            $i = 0;
            foreach ($tagArray as $t) {
                $t = strtolower($t);
                $pos = strpos($t, '==');
                $tag = $pos ? substr($t, $pos + 2) : $t;
                $tag = trim($tag);
                $tags .= '<button name="button' . $i . '" id="button' . $i . '" type="button" class="nf_tag" onclick="nf_insert_tag(' . "'" . $tag . "'" . ');"' . '">' . $tag . "</button>\n";
                $i++;
            }
            $tags .= '</p>';
        }
        $this->modx->setPlaceholder('nf_tag_list', $tags);
    }

} /* end class */
