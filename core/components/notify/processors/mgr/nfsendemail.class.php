<?php

/**
 * Class NfSendEmailProcessor
 *
 * (for LexiconHelper)
 * $modx->lexicon->load('notify:default');
 */
class NfSendEmailProcessor extends modProcessor {

    protected $errors;
    protected $successMessages;
    protected $testMode;
    protected $userFields;
    protected $emailText;
    protected $logFile;
    protected $debug;
    protected $tags;
    protected $corePath;
    protected $modelPath;
    protected $html2text;
    protected $injectUnsubscribeUrl;


    public function initialize() {
        $config = $this->modx->fromJSON($_SESSION['nf_config']);
        $this->properties = array_merge($config, $this->properties);
        $this->testMode = $this->getProperty('testMode',false);
        $this->debug = $this->getProperty('debug', false);
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

        require_once $this->modelPath . 'html2text.php';
        $this->html2text = new html2text();

        return true;
    }
    /**
     * Update file that is read to create status bar.
     * @param $percent int - percentage complete
     * @param $text1 string - First text message
     * @param $text2 string - Second text message
     * @param $pb_target modChunk - chunk used to record the data
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

        if($this->debug) {
            $chunk = $this->modx->getObject('modChunk', array('name' => 'Debug'));

            if (isset($this->properties)) {
                $content =  print_r($this->properties, true);
            } else {
                $content = 'No Props';
            }
            $chunk->setContent($content);
            $chunk->save();
        }

        $sendBulk = (bool) $this->getProperty('send_bulk', false);
        $sendSingle = (bool) $this->getProperty('single', false);

        if ($sendBulk) {
            $this->sendBulk();
        }

        if ($sendSingle) {
            $singleId = $this->getProperty('single_id', 'admin');
            if (empty($singleId)) {
                $this->setError($this->modx->lexicon('nf._no_single_id'));
            } else {
                $this->sendBulk($singleId);
            }
        }



        $results["status"] = empty($this->errors)? "Yes" : "No";
        $results["errors"] = $this->errors;
        $results["successMessages"] = $this ->successMessages;
        return $this->modx->toJSON($results);
    }

    protected function sendBulk($singleId = null) {
        $singleUser = $singleId !== NULL;
        $fp = NULL;
        $mx = NULL;
        $batchSize = $this->getProperty('batchSize', 25);
        $batchDelay = $this->getProperty('batchDelay', 1);
        $itemDelay = (float) $this->getProperty('itemDelay', .51);
        $profileAlias = $this->getProperty('profileAlias', 'Profile');
        $profileAlias = empty($profileAlias) ? 'Profile' : $profileAlias;
        $profileClass = $this->getProperty('profileClass', 'modUserProfile');
        $profileClass = empty($profileClass) ? 'modUserProfile' : $profileClass;
        $useMandrill = $this->getProperty('useMandrill', false);
        $emailText = $this->emailText;
        if (empty($emailText)) {
            $this->setError('No Email Text');
        }
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

        if ($useMandrill) {
            /** @var $mx MandrillX */
            require_once $this->modx->getOption('mandrillx.core.path', NULL,
                MODX_CORE_PATH) .
                'components/mandrillx/model/mandrillx/mandrillx.class.php';

            $apiKey = $this->modx->getOption('mandrill_api_key');
            if (empty($apiKey)) {
                $this->setError($this->modx->lexicon('nf.no_mandrill_api_key'));

                return false;
            } else {
                $this->properties['html'] = $this->emailText;
                $this->html2text->set_html($this->emailText);
                $text = $this->html2text->get_text();
                $this->properties['text'] = $text;
                unset($text);
                $mx = new MandrillX($this->modx, $apiKey, $this->properties);
                if (!$mx instanceof MandrillX) {
                    $this->setError($this->modx->lexicon('nf.no_mandrill'));

                    return false;
                }

                $mx->init();
                $this->userFields = $mx->getUserPlaceholders();
            }
        } else {
            $this->userFields = $this->getUserFields($emailText);
            /**
             * Initialize the modx Mailer
             */
                set_time_limit(0);
                $this->modx->getService('mail', 'mail.modPHPMailer');


                $this->modx->mail->set(modMail::MAIL_FROM, $this->getProperty('from_email'));
                $this->modx->mail->set(modMail::MAIL_FROM_NAME, $this->getProperty('from_name'));
                $this->modx->mail->set(modMail::MAIL_SENDER, $this->getProperty('mail_from'));
                $this->modx->mail->set(modMail::MAIL_SUBJECT, $this->getProperty('email_subject'));
                $this->modx->mail->address('reply-to', $this->getProperty('reply_to'));
                $this->modx->mail->header('reply-to:' . $this->getProperty('reply_to') );
                $this->modx->mail->mailer->IsHtml(true);
        }
        if ($useMandrill) {
            $this->logFile .= "(Mandrill)";
        }
        $fp = fopen($this->logFile, 'w');

        if (!$fp) {
            $this->setError($this->modx->lexicon('nf.could_not_open_log_file') . ': ' . $this->logFile);
        } else {
            fwrite($fp, "MESSAGE\n*****************************\n" .
                $this->emailText .
                "\n*****************************\n\n");

        }

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
        }
        if ($this->debug) {
            echo "<br>Total Count: " . $totalCount;
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
        $statusChunk = $this->modx->getObject('modChunk', array('name' => 'NfStatus'));
        $this->update(0, "", '', $statusChunk);
        $processMsg1 = $this->modx->lexicon('nf.processing_batch');
        $processMsg2 = $this->modx->lexicon('nf.users_emailed_in_batch');
        $finishedMsg = $this->modx->lexicon('nf.finished');
        while ($offset < $totalCount) {
            // sleep(4);  ???
            $i++;

            $c->limit($batchSize, $offset);
            // $c->leftJoin('modUserProfile', 'Profile', 'Profile.internalKey=modUser.id');
            $c->prepare();
            $users = $this->modx->getCollectionGraph($userClass, '{"' . $profileAlias . '":{}', $c);
            // echo "<br>User Count: " . count($users);
            $offset += $batchSize;

            if ($this->debug) {
                $msg = "\n\n<br />" . $i . "  Count: " . count($users) .
                    "\n<br />Offset: " . $offset . "\n<br />BatchSize: " .
                    $batchSize;
                echo($msg);
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
                /* Send the email */
                if ($useMandrill) {
                    /* This will not trigger an error because the message
                       is not sent here */
                    $this->addUserToMandrill($fields, $mx);
                } else {
                    /* Note: testMode is handled in sendMail */
                    if ($this->sendMail($fields)) {
                        if ($fp) {
                            $msg = $this->modx->lexicon('nf.successful_send_to') .
                                ': (' . $fields['name'] . ') ';
                            if (!empty($fields['userTags'])) {
                                $msg .= $this->modx->lexicon('nf.user_tags') .
                                ': ' . $fields['userTags'] . ') ';
                            }
                            $msg .= "\n";
                            fwrite($fp, $msg);
                        }
                    } else {
                        if ($fp) {
                            $msg = $this->modx->lexicon('nf.error_sending_to') .
                                $fields['email'] . ' (' .
                                $fields['name'] . ') ' .
                                "\n";
                            fwrite($fp, $msg);
                        }
                    }
                    sleep($itemDelay);

                }
                if ($this->debug) {
                    echo "\n" . $user->get('username') . ' -- ' . $profile->get('email');
                }
                $sentCount++;

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
            if ($useMandrill) {

                $results = $this->testMode
                    ? array()
                    : $mx->sendMessage();
                $mx->clearUsers();
                if ($mx->hasError()) {
                    $errors = $mx->getErrors();
                    $this->successMessages = array();
                    foreach ($errors as $error) {
                        $this->setError($error);
                    }
                }
                if ($this->debug) {
                    echo "\n<br />" . $this->modx->lexicon('nf.full_response') . "\n<br /><pre>" . print_r($results, true) . "</pre>\n";
                }
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
            if ($useMandrill) {
                $msg .= ' ' . $this->modx->lexicon('nf.using') . ' Mandrill';
            }
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
                $this->removeOldestFile($dir, $maxLogs);
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


    public function removeOldestFile($dir, $maxLogs) {
        $files = glob($dir . '/*.*');

        if (count($files) > $maxLogs) {
            array_multisort(
                array_map('filemtime', $files),
                SORT_NUMERIC,
                SORT_ASC,
                $files
            );

            unlink($files[0]);
        }
    }

    /**
     * Sends an individual email - not used if sending via Mandrill
     *
     * @param $fields array - fields for user placeholders.
     * @return bool - true on success; false on failure to mail.
     */
    public function sendMail($fields) {

        $content = $this->emailText;

        /* Get Fields used in Tpl */
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

    }

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
    protected function addUserToMandrill($fields, &$mx) {
        /** @var $mx MandrillX */
        if ($this->debug) {
            echo $this->modx->lexicon('nf.send_user_mandrill') .
                ': ' . $fields['username'];
        }
        $mx->addUser($fields);


    }

    public function setError($msg) {
        $this->errors[] = $msg;
    }

    public function setSuccess($msg) {
        $this->successMessages[] = $msg;
    }
}

return 'NfSendEmailProcessor';