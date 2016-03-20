<?php

/**
 * Notify
 *
 * Copyright 2012-2015 Bob Ray
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
/* Properties (note: some of these are passed through to the processors)

 * @property &batchDelay textfield -- (optional) Delay between batches in seconds; Default: 1.
 * @property &batchSize textfield -- (optional) Batch size for bulk email to subscribers; Default: 50.
 * @property &bitlyApiKey textfield -- bit.ly API key (required); Default: (empty)..
 * @property &bitlyUsername textfield -- bit.ly username (required); Default: (empty)..
 * @property &debug combo-boolean -- Set to Yes to output debugging information; Default: (empty)..
 * @property &googleApiKey textfield -- Google API key; Default: (empty)..
 * @property &groupListChunkName textfield -- Specifies the chunk that will be used for the buttons under the Groups input in the form; Default: sbsGroupListTpl.
 * @property &groups textfield -- Comma-separated list of User Groups to send to (no spaces). The Subscribers group will be set in the form, but if you delete it and submit with the Groups field empty, email will be sent to all users on the site; Default: (empty)..
 * @property &includeTVList textfield -- Comma-separated list of TV names. Only TVs on the list will have their placeholders set; Default: (empty)..
 * @property &includeTVs combo-boolean -- If set, placeholders will be set for Resource TVs; Default: No.
 * @property &itemDelay textfield -- (optional) Delay between individual emails in seconds; Default: .51.
 * @property &mailFrom textfield -- (optional) MAIL_FROM setting for email; Default: emailsender System Setting.
 * @property &mailFromName textfield -- (optional) MAIL_FROM_NAME setting for email; Default: site_name System Setting.
 * @property &mailReplyTo textfield -- (optional) REPLY_TO setting for email; Default: emailsender System Setting.
 * @property &mailSender textfield -- (optional) EMAIL_SENDER setting for email; Default: emailsender System Setting.
 * @property &maxLogs textfield -- Set this to limit the number of email logs kept. The oldest one will be deleted. Set to 0 for unlimited logs; Default: 5.
 * @property &nfEmailTplCustom textfield -- Name of chunk to use for custom Notify email Tpl; Default: NfSubscriberEmailTplCustom.
 * @property &nfEmailTplExisting textfield -- Name of chunk to use for updated resource Notify email; Default: NfSubscriberEmailTplExisting.
 * @property &nfEmailTplNew textfield -- Name of chunk to use for the new resource Notify email; Default: NfSubscriberEmailTplNew.
 * @property &nfFormTpl textfield -- Name of chunk to use for the Notify form; Default: NfNotifyFormTpl.
 * @property &nfSubjectTpl textfield -- Name of chunk to use for the Email subject; Default: NfEmailSubjectTpl.
 * @property &nfTestEmailAddress textfield -- (optional) Email address for test email; Default: emailsender System Setting.
 * @property &nfTweetTplCustom textfield -- Name of chunk to use for the custom Tweet text; Default: nfTweetTplCustom.
 * @property &nfTweetTplExisting textfield -- Name of chunk to use for the updated resource Tweet text; Default: nfTweetTplExisting.
 * @property &nfTweetTplNew textfield -- Name of chunk to use for the new resource Tweet text; Default: nfTweetTplNew.
 * @property &nfUnsubscribeTpl textfield -- Name of chunk to use for Unsubscribe link; Default: (empty)..
 * @property &nfUseMandrill combo-boolean -- Use Mandrill service to send email; Default: (empty)..
 * @property &notifyFacebook combo-boolean -- Notify Facebook via Twitter with #fb in tweet -- must be set up in the Facebook Twitter App; Default: (empty)..
 * @property &prefListChunkName textfield -- (optional) Chunk to use for preferences (tags) list; Default: sbsPrefListTpl.
 * @property &processTVs combo-boolean -- If set to No, the raw values of the TVs will used; Default: Yes.
 * @property &profileAlias textfield -- (optional) class of the user profile object. Only necessary if you have subclassed the user profile object; Default: modUserProfile.
 * @property &profileClass textfield -- (optional) class of the user profile object. Only necessary if you have subclassed the user profile object; Default: modUser.
 * @property &requireAllTagsDefault combo-boolean -- (optional) sets the default value of the Require All Tags checkbox; if set, only users who have all tags will receive email; Default: No.
 * @property &sortBy textfield -- (optional) Field to sort by when selecting users; Default: username.
 * @property &sortByAlias textfield -- (optional) class of the user object. Only necessary if you have subclassed the user object; Default: modUser.
 * @property &subaccount textfield -- Name of the Mandrill subaccount to send through if using Mandrill. Subaccount must exist or send will fail; Default: test.
 * @property &suprApiKey textfield -- StumbleUpon API key (optional); Default: (empty)..
 * @property &suprUsername textfield -- Stumble Upon Username (optional); Default: (empty)..
 * @property &tags textfield -- (optional) Comma-separated list of tags (no spaces). If set, only users in specified Groups with the interest(s) set will receive the email; Default: (empty)..
 * @property &testMode combo-boolean -- Test mode -- Notify functions normally, but no emails are sent.
; Default: (empty)..
 * @property &tinyurlApiKey textfield -- TinyUrl API key (optional); Default: (empty)..
 * @property &tinyurlUsername textfield -- TinyUrl username (optional); Default: (empty)..
 * @property &twitterConsumerKey textfield -- Twitter Consumer Key; Default: (empty)..
 * @property &twitterConsumerSecret textfield -- Twitter Consumer Secret; Default: (empty)..
 * @property &twitterOauthSecret textfield -- Twitter Access Token Secret; Default: (empty)..
 * @property &twitterOauthToken textfield -- Twitter Access Token; Default: (empty)..
 * @property &urlShorteningService list -- Service used to shorten all URLs in text and Tweet; Default: (empty)..
 * @property &useExtendedFields combo-boolean -- If set, placeholders will be set from the extended fields of the User Profile; Default: No.
 * @property &userClass textfield -- (optional) class of the user object. Only necessary if you have subclassed the user object; Default: modUser.
 * @property &injectUnsubscribeUrl textfield -- If set, adds an unsubscribe/manange preferences link to every email; Default: Yes. Be aware that if you send bulk emails, such a link is required by USA law.

 */


class Notify
{

    /** @var $modx modX */
    /** @var $resource modResource */
    protected $resource = null;
    protected $modx = null;
    protected $props = array();
    protected $mail_from = '';
    protected $mail_from_name = '';
    protected $mail_sender = '';
    protected $mail_reply_to = '';
    protected $mail_subject = '';

    protected $emailText;
    protected $emailTpl;
    protected $tweetTpl;
    protected $tweetText;
    /** @var $successMessages array */
    protected $successMessages;
    /** @var $errors array */
    protected $errors;
    protected $pageId;
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
    protected $requireDefault;
    /** @var $mx MandrillX */
    protected $mx = null;
    /** @var $userFields array - array of user placeholders used in message */
    protected $userFields = array();
    protected $debug;
    protected $maxLogs = 5;
    protected $testMode;
    protected $injectUnsubscribeUrl;


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
        $this->initJS();

        $this->props['testMode'] = $this->modx->getOption('testMode', $this->props, false);
        $this->props['useMandrill'] = $this->modx->getOption('nfUseMandrill', $this->props, false);
        $this->props['injectUnsubscribeUrl'] = $this->modx->getOption('injectUnsubscribeUrl', $this->props, true);
        $this->injectUnsubscribeUrl = $this->modx->getOption('injectUnsubscribeUrl', $this->props, true);
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
        $this->setTags();  /* Set up JS for tags */
        $this->setUserGroups(); /* Set up JS for user groups */

        /* Message Settings */
        $this->mail_from = $this->modx->getOption('mailFrom', $this->props,
            $this->modx->getOption('emailsender'));
        if (empty($this->mail_from)) {
            $this->mail_from = $this->modx->getOption('emailsender');
        }
        $this->props['from_email'] = $this->mail_from;

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
        $this->props['reply_to'] = $this->mail_reply_to;

        $this->mail_subject = isset($_POST['nf_email_subject'])
            ? $_POST['nf_email_subject']
            : 'Update from ' . $this->modx->getOption('site_name');

        $this->props['mail_subject'] = $this->mail_subject;
        $this->props['subject'] = $this->mail_subject;

        $this->maxLogs = $this->modx->getOption('maxLogs', $this->props, 5);

        /* Unsubscribe settings */
        if ($this->injectUnsubscribeUrl) {
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
        }
        /*$profile = $this->modx->user->getOne('Profile');
        $this->profile = $profile
            ? $profile
            : NULL;*/

        $this->debug = $this->modx->getOption('debug', $this->props, false);
        $this->saveConfig();
        set_time_limit(0);
    }

    public function saveConfig() {
        $config = $this->modx->toJSON($this->props);
        $_SESSION['nf_config'] = $config;
    }

    /**
     * @param $action string - 'displayform' or 'handleSubmission'
     * @return string - returns formTpl or empty string 
     */
    public function process($action) {

        switch($action) {

            /* *********************************************** */
            case 'displayForm':  /* Not a repost */
                /* Check $_SESSION in case launched as a service */
                if (isset($_SESSION['nf.pageId'])) {
                    $this->pageId = $_SESSION['nf.pageId'];
                    unset($_SESSION['nf.pageId']);
                    // echo "<br />pageId: " . $this->pageId;
                } else {
                    $this->pageId = isset($_POST['pageId']) ? $_POST['pageId'] : '';
                }

                if (empty($this->pageId) ) {
                    $this->setError($this->modx->lexicon('nf_page_id_is_empty'));
                    return '';
                }

                /* Check $_SESSION in case launched as a service */
                if (isset($_SESSION['nf.pageType'])) {
                    $this->tplType = $_SESSION['nf.pageType'];
                    unset($_SESSION['nf.pageType']);
                    // echo "<br />pageType: " . $this->tplType;
                } else {
                    $this->tplType = isset($_POST['pageType'])
                        ? $_POST['pageType']
                        : '';
                }


                if ($this->requireDefault) {
                    $this->modx->setPlaceholder('nf_require_checked', 'checked="checked"');
                }

                $this->emailTpl = '';
                $this->tweetTpl = '';
                $this->tplType = ucfirst($this->tplType);
                /* First try 'My' prefix */
                $chunkName = 'MyNfSubscriberEmailTpl' . $this->tplType;
                if ($this->modx->getCount('modChunk', array('name' => $chunkName ))) {
                    $this->emailTpl = $chunkName;
                }
                if (empty($this->emailTpl)) {
                    $temp = 'nfEmailTpl' . $this->tplType;
                    $this->emailTpl = $this->modx->getOption($temp, $this->props,
                        'NfSubscriberEmailTpl' . $this->tplType, true);
                }

                $chunkName = 'NfTweetTpl' . $this->tplType;
                if ($this->modx->getCount('modChunk', array('name' => 'My' . $chunkName))) {
                    $this->tweetTpl = 'My' . $chunkName;
                }
                if (empty($this->tweetTpl)) {
                    $this->tweetTpl = $this->modx->getOption(lcfirst($chunkName), $this->props,
                        'NfTweetTpl' . $this->tplType);
                }

                if (! $this->prepareTpl()) {
                    return '';
                }

                break;
            /* *********************************************** */
            case 'handleSubmission':
                $requireAllTags = isset($_POST['nf_require_all_tags']) &&
                    (!empty($_POST['nf_require_all_tags']));
                $pageAlias = isset($_POST['pageAlias'])? $_POST['pageAlias']: 0;
                $this->modx->setPlaceholder('pageAlias',$pageAlias);
                $sendTestEmail = isset($_POST['nf_send_test_email']);
                $sendBulkEmail = isset($_POST['nf_notify']);
                $sendTweet = isset($_POST['nf_send_tweet']);
                $this->emailText = isset($_POST['nf_email_text'])? $_POST['nf_email_text'] : '';
                $this->tweetText = isset($_POST['nf_tweet_text'])? $_POST['nf_tweet_text'] : '';
                if ($sendTestEmail) {
                    $this->modx->setPlaceholder('nf_send_test_email_checked','checked="checked"');
                }
                if ($requireAllTags) {
                    $this->modx->setPlaceholder('nf_require_checked', 'checked="checked"' );
                }
                /* set form placeholders */
                if ($sendBulkEmail) {
                    $this->modx->setPlaceholder('nf_notify_checked','checked="checked"');
                }
                if ($sendTweet) {
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
                $this->updatePreviewPage($this->emailText);

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

        $this->resource = $this->modx->getObject('modResource', (int) $this->pageId);
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
        $includeTVs = (bool) $this->modx->getOption('includeTVs', $this->props, false);

        if ($includeTVs) {
            $includeTVList = $this->modx->getOption('includeTVList', $this->props, '');
            $includeTVList = !empty($includeTVList)
                ? explode(',', $includeTVList)
                : array();

            $renderTvs = $this->modx->getOption('processTVs', $this->props, true);
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
            $this->setError($this->modx->lexicon('nf.could_not_find_email_tpl_chunk') .
                ' ' . $this->emailTpl);
        } else {
            /* convert any relative URLS in email text */
            $this->fullUrls();
            /* Fix image attributes */
            $this->imgAttributes();
            /* Inject unsubscribe link */
            if ($this->injectUnsubscribeUrl) {
                $this->emailText = $this->injectUnsubscribe($this->emailText);
            }
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
            $this->setError($this->modx->lexicon('nf.could_not_find_tweet_tpl_chunk') .
                ' ' . $this->tweetTpl);
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
        if (empty($nfTestEmailAddress)) {
            $profile = $this->modx->user->getOne('Profile');
            if ($profile) {
                $testEmailAddress = $profile->get('email');
                unset($profile);
            }
        }

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
     * Adds a success message to the array of success messages to print in the results
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
     * Uses an associative array for replacing multiple strings
     * @param $replace - array of search => replace terms
     * @param $subject string - string to do replacement on
     * @return string - $subject with replacements done
     */
    public function strReplaceAssoc($replace, $subject) {
           $msg =  str_replace(array_keys($replace), array_values($replace), $subject);
           return $msg;

    }


    protected function initJS() {
        header("X-XSS-Protection: 0");

        /* This is a System Setting, not a property,
         * but it can be overridden by setting it as a
         * property in the snippet tag. */

        $nf_status_resource_id = $this->modx->getOption('nf_status_resource_id', $this->props);

        /* try to set the System Setting if it didn't
         * get set during the install */
        if (empty($nf_status_resource_id)) {
            $r = $this->modx->getObject('modResource', array('alias' => 'notify-status'));
            $s = $this->modx->getObject('modSystemSetting', array('key' => 'nf_status_resource_id'));
            if ($r && $s) {
                $s->set('value', $r->get('id'));
                $s->save();
                $nf_status_resource_id = $r->get('id');
                unset($r, $s);
                $cm = $this->modx->getCacheManager();
                $cm->refresh();
            }

        }

        /* Make sure we have it */
        if (empty($nf_status_resource_id)) {
            die($this->modx->lexicon('nf_status_resource_id_not_set'));
        }

        /* Make sure nf_status_resource_id points to a real resource */
        $nf_status_url = $this->modx->makeUrl((integer) $nf_status_resource_id, "", "", "full");
        if (empty($nf_status_url)) {
            die($this->modx->lexicon('nf_status_resource_id_bad_resource'));
        }

        $cssUrl = $this->modx->getOption('nf.assets_url', NULL, MODX_ASSETS_URL . 'components/notify/') . 'css/notify.css';
        $headStuff =
    '<link rel="stylesheet" href="//ajax.googleapis.com/ajax/libs/jqueryui/1.10.3/themes/smoothness/jquery-ui.min.css">
    <script src="//ajax.googleapis.com/ajax/libs/jquery/1.11.0/jquery.min.js" ></script>
    <script type="text/javascript" src="http://ajax.googleapis.com/ajax/libs/jqueryui/1.10.4/jquery-ui.js"></script>
    <link rel="stylesheet" href="' . $cssUrl . '" type="text/css" />';
            $this->modx->regClientStartupHTMLBlock($headStuff);

    $path = $this->modx->getOption('nf.assets_path', null, MODX_ASSETS_PATH .
            'components/notify/') . 'js/notify.js';
    $js = file_get_contents($path);


    $nf_connector_url = $this->modx->getOption('nf.assets_url', NULL, MODX_ASSETS_URL .
            'components/notify/') . 'connector.php';

    /* This can be set in the Notify snippet tag to override
               the default (800). The value is in milliseconds (1000 = 1 sec.)*/
    $nf_interval = $this->modx->getOption('nf_set_interval', $this->props);
    $nf_interval = empty($nf_interval)
        ? 800
        : $nf_interval;

    $js = str_replace('[[+nf_status_url]]', $nf_status_url, $js);
    $js = str_replace('[[+nf_set_interval]]', $nf_interval, $js);
    $js = str_replace('[[+nf_connector_url]]', $nf_connector_url, $js);



    $this->modx->regClientStartupScript('<script type="text/javascript">' .
        $js . '</script>');
}


    /**
     * Correct any non-full urls in email text
     */
    public function fullUrls() {
        /* extract domain name from $base */
        $base = $this->modx->getOption('site_url');
        $splitBase = explode('//', $base);
        $domain = $splitBase[1];
        $domain = rtrim($domain,'/\\ ');
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


    /**
     * Create JS to add user groups with buttons
     * 
     * Gets group list from groupList Tpl chunk
     */
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

                $pos = strpos($t, '==');
                $group = $pos
                    ? substr($t, $pos + 2)
                    : $t;
                $group = trim($group);
                $groups .= '<button name="button' . $i . '" id="button' . $i .
                    '" type="button" class="nf_group" onclick="nf_insert_group(' .
                    "'" . $group . "'" . ');"' . '">' . $group . "</button>\n";
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
