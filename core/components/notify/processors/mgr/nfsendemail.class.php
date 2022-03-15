<?php

/**
 * Class NfSendEmailProcessor
 *
 * (for LexiconHelper)
 * $modx->lexicon->load('notify:default');
 */

/** @var $modx modX */

// include_once MODX_CORE_PATH . 'model/modx/modprocessor.class.php';
include_once dirname(dirname(__DIR__)) . '/model/notify/mailservice.php';
include_once dirname(dirname(__DIR__)) . '/vendor/autoload.php';

if (class_exists('MODX\Revolution\Processors\Processor')) {
    abstract class DynamicProcessorParent extends MODX\Revolution\Processors\Processor {
    }
} else {
    abstract class DynamicProcessorParent extends modProcessor {
    }
}
class NfSendEmailProcessor extends DynamicProcessorParent {

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
    /** @var $mailService MailService */
    public $mailService = null;
    protected $mailServiceClass = '';
    protected $logger = null; // NotifyLog class
    protected $stars = MailService::STARS;
    protected $prefix;


    public function initialize() {
        $this->modx->lexicon->load('notify:default');
        if (isset($_SESSION['nf_config'])) {
            $config = $this->modx->fromJSON($_SESSION['nf_config']);
            $this->properties = array_merge($config, $this->properties);
        }
        $this->debug = $this->modx->getOption('nf_debug',null, false);

        $this->prefix = $this->modx->getVersionData()['version'] >= 3
                ? 'MODX\Revolution\\'
                : '';

        if ($this->debug) {
            $this->logger->write("\n" . $this->stars .  "\nProperties after merge\n" .
                    print_r($this->properties, true) . $this->stars . "\n");
        }
        unset ($t, $config);
        $this->testMode = $this->getProperty('testMode', false);
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
        $this->mailServiceClass = $this->getProperty('mailService', '');
        /* Backward Compatibility */
        if (empty($this->mailServiceClass)) {
           $this->mailServiceClass = 'modMailX';
        }

        $msLower = strtolower($this->mailServiceClass);
        $shortName = str_replace('x', '', $msLower);
        $filename = $this->modelPath . strtolower($this->mailServiceClass) . '.class.php';
        $apiKey = $this->modx->getOption($shortName . '_api_key', $this->properties, '');
        $this->properties['apiKey'] = $apiKey;
        if ($this->mailServiceClass != 'modMailX' && empty($apiKey)) {
            $this->setError('nf.missing_api_key');
        }
        if (!file_exists($filename)) {
            $this->setError($this->modx->lexicon('nf.processor_nf')
                    . $filename);
            return false;
        }
        $this->logger = new NotifyLog;
        $logFile = $this->corePath . 'notify-logs/' .
                $pageAlias . '--' . date('Y-m-d-h.i.s-a') .
                '(' . $this->mailServiceClass . ')-log.txt';
        if (! $this->logger->init($logFile)){
              $this->setError($this->modx->lexicon('nf.could_not_open_log_file'));
        }

        $this->properties['logFile'] = $this->logFile;
        $this->mailService = new $this->mailServiceClass($this->modx, $this->properties, $this->logger);
        if (!$this->mailService instanceof $this->mailServiceClass) {
            $this->setError($this->modx->lexicon('nf.failed_ms_instantation')
                    . $this->mailServiceClass);
            return false;
        }


        $this->mailService->init();
        if (!$this->sendMailFields()) {
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
    public function update($percent, $text1, $text2, &$pb_target) {

        $msg = $this->modx->toJSON(array(
                'percent' => $percent,
                'text1' => $text1,
                'text2' => $text2,
        ));

        /* use a chunk for the status "file" */

        $pb_target->setContent($msg);
        $pb_target->save();
    }

    public function checkPermissions() {
        $valid = $this->modx->hasPermission('view_user');
        if (!$valid) {
            $this->setError($this->modx->lexicon('nf.no_view_user_permission'));
        }
        return $valid;
    }

    public function process($scriptProperties = array()) {
        $retVal = true;
        if ($this->debug) {

            if (isset($this->properties)) {
                $content = print_r($this->properties, true);
            } else {
                $content = 'No Props';
            }
            $this->logger->write("\n" . $this->stars . "\n Content: " . $content . "\n" . $this->stars . "\n");

        }

        $sendBulk = (bool)$this->getProperty('send_bulk', false);
        $sendSingle = (bool)$this->getProperty('single', false);

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

        $results["status"] = empty($this->errors) ? "Yes" : "No";
        $results["errors"] = $this->errors;
        $results["successMessages"] = $this->successMessages;

        return $this->modx->toJSON($results);
    }

    public function sendMailFields() {
        $fields = array();
        $success = true;
        $fromName = $this->getProperty('from_name', '');
        $fromName = empty($fromName) ? $this->modx->getOption('site_name') : $fromName;
        $fromEmail = $this->getProperty('from_email', '');
        $fromEmail = empty($fromEmail) ? $this->modx->getOption('emailsender') : $fromEmail;
        $fields['html'] = $this->emailText;
        $fields['html'] = $this->mailService->prepareTpl($fields['html']);
        require_once $this->modelPath . 'html2text.php';
        $html2text = new \Html2Text\Html2Text($fields['html']);
        $fields['text'] = $html2text->getText();
        $fields['subject'] = $this->getProperty('email_subject', '');

        /* Used by MailgunX */
        $fields['from'] = $fromName . ' <' . $fromEmail . '>';

        /* Used by modMailX */
        $fields['fromEmail'] = $fromEmail;
        $fields['fromName'] = $fromName;

        $fields['reply-to'] = $this->getProperty('mailReplyTo', $this->modx->getOption('emailsender'));

        if (empty($fields['reply-to'])) {
            $fields['reply-to'] = $this->modx->getOption('emailsender');
        }

        if (empty($fields['html'])) {
            $this->setError(('nf.empty_message'));
            $success = false;
        }

        if (empty($fields['text'])) {
            $this->setError(('nf.empty_text'));
            $success = false;
        }

        if (empty($fields['subject'])) {
            $fields['subject'] = 'Update from ' . $this->modx->getOption('site_name');
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

    /**
     * Extra headers only. Do not include Reply-to, cc, or bcc
     */
    public function sendHeaderFields() {
        $headers = $this->modx->getOption('additionalHeaders', $this->properties, array(), true);
        if (!empty($headers)) {
            $headers = $this->modx->fromJSON($headers);
            /* Make sure fromJSON worked */
            if (!empty($headers)) {
                $this->mailService->setHeaderFields($headers);
            }
        }
    }

    protected function sendBulk($singleId = null) {
        $singleUser = $singleId !== NULL;
        // $fp = NULL;
        $batchSize = $this->getProperty('batchSize', 25);
        $batchDelay = $this->getProperty('batchDelay', 1);
        $itemDelay = (float)$this->getProperty('itemDelay', .51);
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
        $unSub = null;
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

        /* Remove oldest log files */
        $maxLogs = $this->getProperty('maxLogs', 5);
        $dir = $this->corePath . 'notify-logs';
        if ($maxLogs != '0') {
            $this->logger->removeOldFiles($dir, $maxLogs);
        }

        /* Select users to send to */
        $groups = $this->getProperty('groups');
        $groups = empty($groups)
                ? array()
                : explode(',', $groups);

        foreach ($groups as $key => $group) {
            $group = trim($group);
            if (!is_numeric($group)) {
                $grp = $this->modx->getObject($this->prefix . 'modUserGroup', array('name' => $group));
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
            if ($u = $this->modx->getObject($this->prefix . 'modUser', array('username' => $singleId))) {
                $c->where(array('username' => $singleId));
                unset($u);
            } elseif ($p = $this->modx->getObject($this->prefix . $profileClass, array('email' => $singleId))) {
                $c->where(array('id' => $p->get('internalKey')));
                unset($p);
            } else {
                $this->setError($this->modx->lexicon('nf.user_not_found'));
                return false;
            }


        } else {
            if (!empty($groups)) {
                $c->where(array(
                        'UserGroupMembers.user_group:IN' => $groups,
                        'active' => '1',
                ));
                $c->leftJoin('modUserGroupMember', 'UserGroupMembers');
            } else {
                $c->where(array(
                        'active' => '1',
                ));
            }
        }

        $c->prepare();
        $totalCount = $this->modx->getCount($this->prefix . 'modUser', $c);
        if (!$totalCount) {
            $this->setError($this->modx->lexicon('nf.no_recipients_to_send_to'));
            return false;
        }
        if ($this->debug) {
            $this->logger->write("\nTotal Count: " . $totalCount . "\n");
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
        $statusChunk = $this->modx->getObject($this->prefix . 'modChunk', array('name' => 'NfStatus'));
        $this->update(0, "", '', $statusChunk);
        $processMsg1 = $this->modx->lexicon('nf.processing_batch');
        $processMsg2 = $this->modx->lexicon('nf.users_emailed_in_batch');
        $finishedMsg = $this->modx->lexicon('nf.finished');

        /* Batch loop - sends all batches */
        while ($offset < $totalCount) {
            if ($this->debug) {

                $this->logger->write("\n ************* NEW BATCH -- Batch {$batchNumber}" . "\n");
            }
            if ($this->debug) {
                $this->logger->write("\nClearing User Data");
            }
            $this->mailService->clearUserData();
            if ($this->debug) {
                $this->logger->write("\nUser Data Cleared");
            }
            $i++;

            /* Get one batch's potential users */
            if ($this->debug) {
                $this->logger->write("\nDoing Query");
            }
            $c->limit($batchSize, $offset);
            // $c->leftJoin('modUserProfile', 'Profile', 'Profile.internalKey=modUser.id');
            $c->prepare();
            $users = $this->modx->getCollectionGraph($this->prefix . $userClass, '{"' . $profileAlias . '":{}', $c);
            if ($this->debug) {
                $this->logger->write("Query completed ");
            }
            $offset += $batchSize;

            if ($this->debug) {
                $this->logger->write("\nCount: " . count($users) . ' -- Offset: ' . $offset . " -- BatchSize: " . $batchSize);
            }
            $sentCount = 0;

            $requireAllTags = $this->getProperty('require_all_tags', false);
            $userNumber = 1;
            foreach ($users as $user) {
                /** @var $user modUser */
                /** @var $profile modUserProfile */
                $profile = $user->$profileAlias;
                $username = $user->get('username');
                if (empty($profile)) {
                    $this->modx->log(modX::LOG_LEVEL_ERROR, 'Profile is empty -- User: ' . $userNumber . 'Username: ' . $username);
                }

                if ($this->debug) {
                    $this->logger->write('\nQualifying users');
                }
                if (!empty($this->tags)) {
                    if (!$this->qualifyUser($profile, $username, $requireAllTags)) {
                        continue;
                    }
                }

                /* Make sure the profile is hydrated; prevents 500 error in $unSub->createUrl() */

                $x = $profile->toArray('', true);
                $z = $x;
                unset($x, $z);

                if ($this->debug) {
                    $this->logger->write('\nUser qualified');
                }
                /* Now we have a user to send to */
                $fields = array();
                $fields['userid'] = $user->get('id');
                $fields['username'] = $username;
                if ($this->debug) {
                    $this->logger->write("\nInjecting Unsubscribe URL-- User: " .
                            $userNumber . ' -- Username: ' . $username .
                            ' -- UnsubURL: ' . $unSubUrl);
                }
                if ($this->injectUnsubscribeUrl) {
                    if (!$unSub instanceof Unsubscribe) {
                        $this->modx->log(modX::LOG_LEVEL_ERROR, 'Unsubscribe not an instance of Unsubscribe');

                    }
                    $fields['unsubscribe_url'] = $unSub->createUrl($unSubUrl, $profile);
                    if ($this->debug) {
                        $this->logger->write("\nUnsub URL: " .
                                $fields['unsubscribe_url']);
                        $this->logger->write("\nUnsubscribe injected -- User: " .
                                $userNumber . ' -- Username: ' . $username);
                    }
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

                $this->mailService->addUser($fields);

                if ($this->debug) {
                    $this->logger->write("\n" . $user->get('username') . ' -- ' . $profile->get('email'));
                    $this->logger->write("\n" .
                            'Fields sent to addUser: ' .
                            print_r($fields, true) . "\n"
                    );
                }

                $sentCount++;
                $userNumber++;

            }
            if ($this->debug) {
                $this->logger->write("\nAll users added");
            }
            /* All users added, send batch */
            $percent = $stepSize * $batchNumber;

            $attempt = 1;
            $maxAttempts = 3;

            do {
                try {
                    $response = $this->mailService->sendBatch($batchNumber);
                    $code = $response->http_response_code;
                    if ($this->debug) {
                        $this->logger->write("\n    Response code: " . $code);
                    }
                    if ($code == 200 || $code == 421) {
                        if ($this->debug) {
                            $this->logger->write("\n    Success -- Batch {$batchNumber} -- Attempt: {$attempt}");
                        }
                        break;
                    } else {
                        if ($response === true) {
                            break;
                        }
                        /* No exception, but bad code */
                        if ($this->debug) {
                            $this->logger->write("\n    No Exception -- bad code -- Batch {$batchNumber} -- Attempt: {$attempt} -- Code: {$code}");
                        }
                        if ($attempt < $maxAttempts) {
                            throw new exception('Retrying');
                        }
                    }
                } catch (Exception $e) {
                    if ($this->debug) {
                        $this->logger->write("\nException -- Batch {$batchNumber} " . $e->getMessage() . " Attempt: {$attempt}");
                    }
                    sleep(1);
                    $attempt++;
                    continue;
                }
                break;
            } while ($attempt < $maxAttempts);


            if ($this->debug) {
                $this->logger->write("\n" . $this->modx->lexicon('nf.full_response') .
                        "\n" . print_r($response, true) . "\n");
            }

            /* If we still don't have a 200 response, record errors */
            if ($code !== 200 && $code !== 421) {
                if ($this->debug) {
                    $this->logger->write("\nEnd of batch error -- code: " . $code);
                }
                if ($this->mailService->hasError()) {
                    $errors = $this->mailService->getErrors();
                    foreach ($errors as $error) {
                        $this->setError($error);
                    }

                }
            }
            $percent = $stepSize * $batchNumber;

            $percent = ($percent >= 100) ? 99 : $percent;

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
        $this->update(99, '', '', $statusChunk);
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


    /** User placeholders actually used in Tpl chunk */
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

    public function setError($msg) {
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