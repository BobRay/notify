<?php

/**
 * Class NfSendEmailProcessor
 *
 * (for LexiconHelper)
 * $modx->lexicon->load('notify:default');
 */

/** @var $modx modX */

include_once MODX_CORE_PATH . 'model/modx/modprocessor.class.php';

class NfSendEmailProcessor extends modProcessor {

    protected $errors = array();
    protected $successMessages = array();
    protected $testMode = false;
    protected $userFields = array();
    protected $emailText = '';
    protected $logFile = '';
    protected $debug = false;
    protected $tags = '';
    protected $corePath = '';
    protected $modelPath = '';
    protected $html2text = null;
    protected $injectUnsubscribeUrl = true;
    /** @var MailService $mailService */
    public $mailService = null;
    protected $mailServiceClass = '';


    public function initialize() {
        $this->modx->lexicon->load('notify:default');
        if (isset($_SESSION['nf_config'])) {
            $config = $this->modx->fromJSON($_SESSION['nf_config']);
            $this->properties = array_merge($config, $this->properties);
        }
        $this->debug = $this->getProperty('debug', false);

        if ($this->debug) {
            $this->modx->log(modx::LOG_LEVEL_ERROR, "\nProperties after merge\n" . print_r($this->properties, true));
        }
        unset ($t, $config);
        $this->testMode = $this->getProperty('testMode',false);
        $this->injectUnsubscribeUrl = $this->getProperty('injectUnsubscribeUrl', true);
        $this->setCheckbox('send_tweet');
        $this->setCheckbox('send_bulk');
        $this->setCheckbox('require_all_tags');
        $this->setCheckbox('single');
        $this->emailText = $this->getProperty('email_text', '');
        $this->tags = $this->getProperty('tags', '');
        $this->corePath = $this->modx->getOption('nf.core_path', NULL, MODX_CORE_PATH . 'components/notify/');
        $this->modelPath = $this->corePath . 'model/notify/';
        $pageAlias = $this->getProperty('page_alias', '');
        $this->logFile = $this->corePath . 'notify-logs/' . $pageAlias . '--' . date('Y-m-d-h.i.sa');
        $this->mailServiceClass = $this->getProperty('mailService', '');
        /* Backward Compatibility */
        if (empty($this->mailServiceClass)) {
            if ($this->getProperty('useMandrill','')) {
                $this->mailServiceClass = 'MandrillX';
            } else {
                $this->mailServiceClass = 'modMailX';
            }
        }
        $msLower = strtolower($this->mailServiceClass);
        $shortName = str_replace('x', '', $msLower );
        $filename = $this->modelPath . strtolower($this->mailServiceClass) . '.class.php';
        $apiKey = $this->modx->getOption($shortName . '_api_key', $this->properties ,'');
        $this->properties['apiKey'] = $apiKey;
        if ($this->mailServiceClass != 'modMailX' && empty($apiKey)) {
            $this->setError('nf.missing_api_key');
        }
        /*if (! file_exists($filename)) {
            $this->setError($this->modx->lexicon('nf.processor_nf')
                . $filename);
            return false;
        } else {
            include_once $filename;
        }*/
        $this->mailService = new $this->mailServiceClass($this->modx, $this->properties);
        if (! $this->mailService instanceof $this->mailServiceClass) {
            $this->setError($this->modx->lexicon('nf.failed_ms_instantation')
                . $this->mailServiceClass);
            return false;
        }



        $this->mailService->init();
         if (! $this->sendMailFields()) {
              return false;
         }
        $this->sendHeaderFields();
        if ($this->mailService->hasError()) {
            $this->errors = array_merge($this->errors, $this->mailService->getErrors());
            return false;
        }
        return true;
    }
    /**
     * Update file that is read to create status bar.
     * @param $percent int - percentage complete
     * @param $text1 string - First text message
     * @param $text2 string - Second text message
     * @param $pb_target modChunk (object) - chunk used to record the data
     */
    public  function update($percent, $text1, $text2, &$pb_target) {

        $msg = $this->modx->toJSON(array(
            'percent' => $percent,
            'text1'   => $text1,
            'text2'   => $text2,
        ));

        /* use a chunk for the status "file" */

        $pb_target->setContent($msg);
        $pb_target->save();
    }

    public function checkPermissions() {
        $valid =  $this->modx->hasPermission('view_user');
        if (! $valid) {
            $this->setError($this->modx->lexicon('nf.no_view_user_permission'));
        }
        return $valid;
    }

    public function process($scriptProperties = array()) {
        $retVal = true;
        if($this->debug) {

            if (isset($this->properties)) {
                $content =  print_r($this->properties, true);
            } else {
                $content = 'No Props';
            }
            $this->modx->log(modX::LOG_LEVEL_ERROR, "\n Content: " .$content);
        }

        $sendBulk = (bool) $this->getProperty('send_bulk', false);
        $sendSingle = (bool) $this->getProperty('single', false);

        if ($sendBulk) {
            $retVal = $this->sendBulk();
        }
        $singleId = null;

        if ($sendSingle) {
            $singleId = $this->getProperty('single_id', 'admin');
            if (empty($singleId)) {
                $this->setError($this->modx->lexicon('nf._no_single_id'));
            } else {
                $this->sendBulk($singleId);
            }
        }

        if ($retVal == false) {
            $this->modx->log(modX::LOG_LEVEL_ERROR, print_r($this->errors, true));
        }

        $results["status"] = empty($this->errors)? "Yes" : "No";
        $results["errors"] = $this->errors;
        $results["successMessages"] = $this->successMessages;

        return $this->modx->toJSON($results);
    }

    public function sendMailFields() { // xxx
        $fields = array();
        $success = true;
        $from = $this->getProperty('from_name', '');
        $fromEmail = $this->getProperty('from_email', '');
        $fields['html'] = $this->emailText;
        $fields['html'] = $this->mailService->prepareTpl($fields['html']);
        require_once $this->modelPath . 'html2text.php';
        $html2text = new \Html2Text\Html2Text($fields['html']);
        $fields['text'] = $html2text->getText();
        $fields['subject'] = $this->getProperty('email_subject', '');
        $fields['from'] = $from . ' <' . $fromEmail . '>';
        /* Used by modMailX */
        $fields['fromEmail'] = $fromEmail;
        $fields['fromName'] = $from;


        if (empty($fields['html'])) {
            $this->setError(('nf.empty_message'));
            $success = false;
        }

        if (empty($fields['text'])) {
            $this->setError(('nf.empty_text'));
            $success = false;
        }

        if (empty($fields['subject'])) {
            $this->setError(('nf.empty_subject'));
            $success = false;
        }
        if (empty($fields['from'])) {
            $this->setError(('nf.empty_from'));
            $success = false;
        }

        if ($success) {
            $this->mailService->setMailFields($fields);
        }

        return $success;
    }

    public function sendHeaderFields() {
        $headers = $this->modx->fromJSON($this->getProperty('mailHeaders', array()));
        if (empty($headers)) {
            $headers = array(
                'Reply-To' => $this->getProperty('mailReplyTo', ''),
            );
        }
        $this->mailService->setHeaderFields($headers);
    }

    protected function sendBulk($singleId = null) {
        $singleUser = $singleId !== NULL;
        $fp = NULL;
        $mx = NULL;
        $mg = NULL;
        $mailFields = array();
        $batchSize = $this->getProperty('batchSize', 25);
        $batchDelay = $this->getProperty('batchDelay', 1);
        $itemDelay = (float) $this->getProperty('itemDelay', .51);
        $profileAlias = $this->getProperty('profileAlias', 'Profile');
        $profileAlias = empty($profileAlias) ? 'Profile' : $profileAlias;
        $profileClass = $this->getProperty('profileClass', 'modUserProfile');
        $profileClass = empty($profileClass) ? 'modUserProfile' : $profileClass;
        /* Don't remove this */
        $emailText = $this->emailText;
        if (empty($emailText)) {
            $this->setError('No Email Text');
            return false;
        }

        /* Set up variables for unSubScribe link - user-specific part is added later.
            Link itself is a user merge variable processed in the mailService class.
        */
        if ($this->injectUnsubscribeUrl) {
            $unSubId = $this->modx->getOption('sbs_unsubscribe_page_id', NULL, NULL);
            $unSubUrl = $this->modx->makeUrl($unSubId, "", "", "full");
            $subscribeCorePath = $this->modx->getOption('subscribe.core_path', NULL,
                $this->modx->getOption('core_path', NULL, MODX_CORE_PATH) .
                'components/subscribe/');
            require_once($subscribeCorePath . 'model/subscribe/unsubscribe.class.php');
            $unSub = new Unsubscribe($this->modx, $this->properties);
            $unSub->init();
        }

        $userFields = $this->getUserFields($this->emailText);

        /* Tell service what the fields are (not their values) */
        $this->mailService->setUserPlaceholders($userFields); // xxx

        $this->logFile .= ' (' . $this->mailServiceClass . ')';

        $fp = fopen($this->logFile, 'w');

        if (!$fp) {
            $this->setError($this->modx->lexicon('nf.could_not_open_log_file') . ': ' . $this->logFile);
        } else {
            fwrite($fp, "MESSAGE\n*****************************\n" .
                $this->emailText .
                "\n*****************************\n\n");

        }

        /* Select users to send to */
        $groups = $this->getProperty('groups');
        $groups = empty($groups)
            ? array()
            : explode(',', $groups);

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

        $userClass = $this->getProperty('userClass', 'modUser');

        $c = $this->modx->newQuery($userClass);
        $c->select($this->modx->getSelectColumns($userClass, $userClass, "", array(
            'id',
            'username',
            'active',
        )));
        $c->sortby($this->modx->escape('username'), 'ASC');
        if ($singleUser) {
            $c->limit(1);
            /* Try to retrieve user, first by username, then  email */
            if ($u = $this->modx->getObject('modUser', array('username' => $singleId))) {
                $c->where(array('username' => $singleId));
                unset($u);
            } elseif ($p = $this->modx->getObject($profileClass, array('email' => $singleId))) {
                $c->where(array('id' => $p->get('internalKey')));
                unset($p);
            } else {
                $this->setError($this->modx->lexicon('nf.user_not_found'));
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
        if (! $totalCount) {
            $this->setError($this->modx->lexicon('nf.no_recipients_to_send_to'));
            return false;
        }
        if ($this->debug) {
            $this->modx->log(modX::LOG_LEVEL_ERROR, "\nTotal Count: " . $totalCount);
        }
        if ($totalCount % $batchSize) {
            $batches = floor($totalCount / $batchSize) + 1;
        } else {
            $batches = $totalCount / $batchSize;
        }
        $totalSent = 0;
        $i = 0;
        $offset = 0;
        $batchNumber = 1;
        $stepSize = floor(100 / $batches);
        /** @var $statusChunk modChunk */
        $statusChunk = $this->modx->getObject('modChunk', array('name' => 'NfStatus'));
        $this->update(0, "", '', $statusChunk);
        $processMsg1 = $this->modx->lexicon('nf.processing_batch');
        $processMsg2 = $this->modx->lexicon('nf.users_emailed_in_batch');
        $finishedMsg = $this->modx->lexicon('nf.finished');
        while ($offset < $totalCount) {
            $this->mailService->clearUserData();

            // sleep(4);
            $i++;

            $c->limit($batchSize, $offset);
            // $c->leftJoin('modUserProfile', 'Profile', 'Profile.internalKey=modUser.id');
            $c->prepare();
            $users = $this->modx->getCollectionGraph($userClass, '{"' . $profileAlias . '":{}', $c);

            $offset += $batchSize;

            if ($this->debug) {
                $msg = "\n\n" . $i . "  Count: " . count($users) .
                    "\nOffset: " . $offset . "\nBatchSize: " .
                    $batchSize;
                $this->modx->log(modX::LOG_LEVEL_ERROR, $msg);
            }
            $sentCount = 0;

            $requireAllTags=$this->getProperty('require_all_tags', false);
            foreach ($users as $user) {
                /** @var $user modUser */
                /** @var $profile modUserProfile */
                $profile = $user->$profileAlias;
                $username = $user->get('username');
                if (!empty($this->tags)) {
                    if (!$this->qualifyUser($profile, $username, $requireAllTags)) {
                        continue;
                    }
                }
                /* Now we have a user to send to */
                $fields = array();
                $fields['userid'] = $user->get('id');
                $fields['username'] = $username;
                if ($this->injectUnsubscribeUrl) {
                    $fields['unsubscribe_url'] = $unSub->createUrl($unSubUrl, $profile);
                }
                $fields = array_merge($profile->toArray(), $fields);
                if ($this->modx->getOption('useExtendedFields', $this->properties, false)) {
                    $extended = $profile->get('extended');
                    $fields = array_merge($extended, $fields);
                }
                $fields['tags'] = $this->tags;
                if (isset($user->Extra) && (!empty($user->Extra))) {
                    $fields = array_merge($user->Extra->toArray(), $fields);
                }

                $fields['name'] = empty($fields['fullname'])
                    ? $fields['username']
                    : $fields['fullname'];

                /* If firstname field is not set, extract it from fullname */
                $fields['firstname'] = isset($fields['firstname']) && (!empty($fields['firstname']))
                    ? $fields['firstname']
                    : substr($fields['name'], 0, strpos($fields['name'], ' '));
                $fields['firstname'] = !empty($fields['firstname'])
                    ? $fields['firstname']
                    : $username;
                $fields['first'] = $fields['firstname'];
                
                /* do last name */

                if (strpos($fields['name'], ' ') !== false) {
                    $fields['lastname'] = isset($fields['lastname']) && (!empty($fields['lastname']))
                        ? $fields['lastname']
                        : substr($fields['name'], strpos($fields['name'], ' ') + 1);
                    $fields['lastname'] = !empty($fields['lastname'])
                        ? $fields['lastname']
                        : $username;
                    $fields['last'] = $fields['lastname'];
                }
                
                
                
                /* Send the email */
                $this->mailService->addUser($fields);

                if ($this->debug) {
                    $this->modx->log(modX::LOG_LEVEL_ERROR, "\n" . $user->get('username') . ' -- ' . $profile->get('email'));
                }
                $sentCount++;

            }
            $response = $this->mailService->sendBatch();
            if ($this->debug) {
                $this->modx->log(modX::LOG_LEVEL_ERROR, "\n" . $this->modx->lexicon('nf.full_response') .
                    "\n" . print_r($response, true) . "\n");
            }


            if ($this->mailService->hasError()) {
                $errors = $this->mailService->getErrors();
                $this->successMessages = array();
                foreach ($errors as $error) {
                    $this->setError($error);
                }
                return false;
            }
            $percent = $stepSize * $batchNumber;

            $percent = ($percent >= 100)? 99: $percent;

            $this->update($percent, $processMsg1 . ' ' . $batchNumber,
                $processMsg2 . ' ' . $sentCount, $statusChunk);
            $batchNumber++;
            sleep($batchDelay);
            set_time_limit(0);
            if ((!empty($sentCount)) && $this->debug) {

                $this->setSuccess($this->modx->lexicon('nf.sending_batch_of') .
                    ' ' . $sentCount);
            }

            $totalSent += $sentCount;
        }
        $this->update(99, '','', $statusChunk);
        sleep(1);
        $this->update(100, $finishedMsg, '', $statusChunk);
        sleep(1);

        if ((!$this->hasErrors()) && $totalSent) {
            $msg = $this->modx->lexicon('nf.email_to_subscribers_sent_successfully');
            $msg = str_replace('[[+nf_number]]', $totalSent, $msg);
            $msg .= ' ' . $this->modx->lexicon('nf.using') . ' ' . $this->mailServiceClass;
            $this->setSuccess($msg);
            if ($this->testMode) {
                $msg = $this->modx->lexicon('nf.test_mode_on');
                $this->setSuccess($msg);
            }
        }
        if ($totalSent == 0) {
            $this->setError($this->modx->lexicon('nf.no_messages_sent'));
        }
        if ($fp !== NULL) {

            $maxLogs = $this->getProperty('maxLogs', 5);
            $dir = $this->corePath . 'notify-logs';
            if ($maxLogs != '0') {
                $this->removeOldFiles($dir, $maxLogs);
            }
            fclose($fp);
        }

        $this->update(0, 'Starting', '', $statusChunk);
        return true;
    }

    /**
     * See if User should receive email based on
     * tags selected in form
     *
     * @param $profile modUserProfile - User Profile object
     * @param $username string
     * @param bool $requireAll
     * @return bool - True if user should receive email
     */
    public function qualifyUser($profile, $username, $requireAll = false) {
        /* Get User's Tags */
        $userTags = NULL;
        if (!$profile) {
            $this->setError($this->modx->lexicon('nf.no_profile_for') . ': ' . $username);
        } else {
            if ($this->modx->getOption('sbs_use_comment_field', NULL, false, true) == false) {
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


    public function removeOldFiles($dir, $maxLogs) {
        $files = glob($dir . '/*.*');

        $over = count($files) - $maxLogs;

        if ($over > 0) {
            array_multisort(
                array_map('filemtime', $files),
                SORT_NUMERIC,
                SORT_ASC,
                $files
            );
            for ($i = 0; $i < $over; $i ++) {
                unlink($files[$i]);
            }
        }
    }

    /**
     * Sends an individual email - not used if sending via Mandrill
     *
     * @param $fields array - fields for user placeholders.
     * @return bool - true on success; false on failure to mail.
     */

    /* This function all goes to the modMailX class */
    /*public function sendMail($fields) {

        $content = $this->emailText;

        //
        $fieldsUsed = $this->userFields;

        foreach ($fields as $key => $value) {
            if (is_array($value)) {
                continue;
            }
            if (in_array($key, $fieldsUsed)) {
            }
            if (!is_array($value)) {
                $content = str_replace('{{+' . $key . '}}', $value, $content);
            }
        }

        $this->modx->mail->set(modMail::MAIL_BODY, $content);
        $this->html2text->set_html($content);
        $text = $this->html2text->get_text();
        $this->modx->mail->set(modMail::MAIL_BODY_TEXT, $text);
        $this->modx->mail->address('to', $fields['email'], $fields['name']);

        $success = $this->testMode
            ? true
            : $this->modx->mail->send();

        if (!$success) {
            $this->setError($this->modx->mail->mailer->ErrorInfo);
        }

        return $success;

    }*/

    public function getUserFields($text) {
        $content = $text;

        /* Get Fields used in Tpl */
        $fieldsUsed = array();
        $pattern = '#\{\{\+([a-zA-Z_\-]+?)\}\}#';
        preg_match_all($pattern, $content, $matches);
        if (isset($matches[1]) && (!empty($matches[1]))) {
            $fieldsUsed = $matches[1];
        }

        return $fieldsUsed;
    }

    /**
     * Add user's info to the Mandrill Message array
     *
     * @param $fields array - user fields with values
     */
    /*protected function addUserToMandrill($fields, &$mx) {

        $mx->addUser($fields);
    }*/

    /**
     * Add user's info to the Mailgun Message array
     *
     * @param $fields array - user fields with values
     */
    /*protected function addUserToMailgun($fields, &$mx) {

        $email = $fields['email'];
        $mx->addUser($email, $fields);
    }*/


    public function setError($msg) {
        $this->modx->log(modX::LOG_LEVEL_ERROR, $msg);
        $this->errors[] = $msg;
    }

    public function getErrors() {
        return $this->errors;
    }

    public function setSuccess($msg) {
        $this->successMessages[] = $msg;
    }
}

return 'NfSendEmailProcessor';