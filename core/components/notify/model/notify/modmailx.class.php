<?php

// use Mailgun\Mailgun;
/**
 * modMailX class file for Notify extra
 *
 * Copyright 2013-2017 Bob Ray <https://bobsguides.com>
 * Created on 03-18-2016
 *
 * Notify is free software; you can redistribute it and/or modify it under the
 * terms of the GNU General Public License as published by the Free Software
 * Foundation; either version 2 of the License, or (at your option) any later
 * version.
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


Class modMailX  implements MailService {
    protected $debug;
    protected $stars = MailService::STARS;
    protected $properties; /* ScriptProperties */
    /** @var $emailArray array - used internally to store recipients' email addresses */
    protected $emailArray = array();
    protected $messageText = '';
    protected $messageSubject = '';
    protected $headerFields = array();
    protected $mailFields = array();
    /** @var $recipientVariables array - array of keys and values for merge tags */
    protected $recipientVariables = array();
    protected $testMode = false;

    /** @var string $domain - Mailgun Sending domain - can be set to real domain or sandbox domain in calling function */
    protected $domain = '';
    /** @var $userPlaceholders array - array of user placeholders (merge tags) actually used in message: {{+pName}} */
    protected $userPlaceholders = array();
    /** @var  $modx modX */
    protected $modx;
    protected $errors = array();
    protected $apiKey = null;
    protected $logger = null;

    function __construct(&$modx, $props, $logger) {
        $this->modx =& $modx;
        $this->properties =& $props;
        $this->logger = $logger;
    }

    public function init() {
        $this->debug = $this->modx->getOption('nf_debug', null, false, true);
        $this->recipientVariables = array();
        $this->testMode = $this->modx->getOption('testMode', $this->properties, false);
        $this->logFile = $this->properties['logFile'];
        /* This class only */
        $this->modx->getService('mail', 'mail.modPHPMailer');
        return true;
    }

    public function clearUserData() {
        $this->toArray = array();
        $this->recipientVariables = array();
    }
    
    public function setError($msg) {
        $this->errors[] = $msg;
    }

    public function hasError() {
        return (!empty($this->errors));
    }

    public function getErrors() {
        return $this->errors;

    }
    /**
     * @param $domain string
     *
     * Call after init() to set a different domain
     *
     */
    public function setDomain($domain) {
        /* Not used by this class */
        $this->domain = $domain;
    }


    /**
     * @param $pArray
     *
     *  Sets the array of the actual placeholders used in the message (not their values).
     *  Sent from the processor and used in addUser().
     */

    public function setUserPlaceholders($pArray){
        $this->userPlaceholders = $pArray;
    }

    /* Custom Headers only, do not use for: to, reply-to, cc, or bcc */
    public function setHeaderFields($fields = array()) {
        foreach($fields as $k => $v) {
            $this->headerFields[$k] = $v;
        }
    }

    /**
     * Sets fields common to all messages - change keys to what the service expects
     * @param $fields array
     */
    public function setMailFields($fields) {
        $this->mailFields = array(
            'from' => $fields['from'],
            'subject' => $fields['subject'],
            'text' => $this->modx->getOption('text', $fields, ''),
            'html' => $this->modx->getOption('html', $fields, ''),
            'fromEmail' => $this->modx->getOption('fromEmail', $fields, ''),
            'fromName' => $this->modx->getOption('fromName', $fields, ''),
            'reply-to' => $this->modx->getOption('reply-to', $fields, ''),
        );
    }


    /**
     *
     * @param array $fields array() - array of email plus user merge variables:
     *
     *   array(
     *      'email' => 'JoeBlow@gmail.com',
     *      'first' => 'Joe',
     *      'last' => 'Blow',
     *   );
     *
     *   sets recipientVariables like this:
     *     $recipientVariables['JoeBlow@gmail.com'] =
     *         array(
     *             'first' => 'Joe',
     *             'last' => 'Blow',
     *         );
     *  Will be converted to this JSON before sent:
     *  'recipient-variables' = '{"JoeBlow@gmail.com":{"first":"Joe","last":"Blow"}}';
     *     Must match the merge variable placeholders in the Tpl chunk
     */
    public function addUser($fields = array()) {
        if ($this->debug) {
            $this->logger->write("\naddUser Fields: " . print_r($fields, true));
        }
        /* These will be set even if not used in message */
        $extraFields = array(
            'first',
            'last',
            'fullname',
            'username',
        );
        $email = $fields['email'];
        $userFields = $this->userPlaceholders;
        $userVariables = array();

        /* Unset unused user variables */
        foreach ($fields as $key => $value) {
            if (in_array($key, $userFields)) {
                $userVariables[$key] = $value;
            }
        }

        foreach($extraFields as $f) {
            if (isset($fields[$f]) && ! isset($userVariables[$f])) {
                $userVariables[$f] = $fields[$f];
            }
        }

        /* Array of emails */
        $this->emailArray[] = $email;

        /* Add recipient variables to $this->recipientVariables */
        if (!empty ($userVariables)) {
            $this->recipientVariables[$email] = $userVariables;
        }
    }

    /**
     * Convert double-curly-bracket tags to Mailgun-style tags
     * in HTML message text.
     *
     *  {{+tagName}} -> %recipient.tagName%
     *
     * There is no need to call this directly, as it is always called
     * automatically just before the message is sent.
     *
     * @param $text string - Text of message with {{+tagName}} tags.
     * @return string - Same text with tags converted to Mailgun-style
     *    tags: %recipient.tagName%
     */
    public function prepareTpl($text) {
        /* No changes. This class uses str_replace in the sendBatch loop */
        return $text;
    }


    /**
     * Gets the array of user placeholder names used in the tpl chunk
     *
     * @return array
     */
    public function getUserPlaceholders() {
        return $this->userPlaceholders;
    }

    /** Write sample message to top of log file (all but recipient list) */
    public function writeSampleMessage() {
        if ($this->modx->getCount('modChunk', array('name' => 'MyNfLogHeaderTpl'))) {
            $headerTpl = 'MyNfLogHeaderTpl';
        } else {
            $headerTpl = 'NfLogHeaderTpl';
        }
        if ($this->debug) {
            $this->logger->write("\nMAIL FIELDS: " . print_r($this->mailFields, true));
        }
        $to = array_keys($this->recipientVariables)[0];

        $body = $this->replacePlaceholders($this->mailFields['html'], $this->recipientVariables[$to]);

        $headerFields = array(
                'from' => $this->mailFields['from'],
                'to' => $to,
                'fullname' => reset($this->recipientVariables)['fullname'],
                'subject' => $this->mailFields['subject'],
                'body' => $body,
        );

        $this->logger->write($this->modx->getChunk($headerTpl, $headerFields));
    }
    public function sendBatch($batchNumber) {
        if ((int) $batchNumber === 1) {
            $this->writeSampleMessage();
        }
        $mFields = $this->mailFields;
        $success = true; // report success in testMode
        $this->logger->write($this->stars . "\nSENDING BATCH {$batchNumber}\n");
        $count = 1;
        foreach ($this->emailArray as $email) {

            $this->modx->mail->set(modMail::MAIL_SUBJECT, $mFields['subject']);
            $this->modx->mail->set(modMail::MAIL_FROM, $mFields['fromEmail']);
            $this->modx->mail->set(modMail::MAIL_FROM_NAME, $mFields['fromName']);
            $rt = $mFields['reply-to'];
            /* Parse reply-to in the style: Bob Ray <bob@gmail.com> */
            $name = null;
            if (strpos($rt, '<') !== false) {
                $rtArray = explode('<', $rt);
                if (count($rtArray) > 1) {
                    $name = trim($rtArray[0]);
                    $rt = trim($rtArray[1], ' <>');
                }
            }
            if (isset($this->properties['unitTest'])) {
                echo "\n Reply-to: " . $rt;
                echo "\n Reply-to-name: " . $name;
            }
            $name = empty($name) ? $mFields['fromName'] : $name;

            $this->modx->mail->address('reply-to', $rt, $name);
            unset ($rt, $name);
            if (!empty($this->headerFields)) {
                /* custom headers only */
                foreach ($this->headerFields as $k => $v) {
                    $this->modx->mail->header($k . ':' . $v);
                }
            }
            $userFields = $this->recipientVariables[$email];

            if ($this->debug) {
                $this->logger->write("\nRecipientFields: " . print_r($userFields, true));
            }
            $this->modx->mail->address('to', $email, $userFields['fullname']);
            $html = $this->replacePlaceholders($mFields['html'], $userFields);
            if ($this->debug) {
                $this->logger->write("\nHTML: " . $html);
            }
            $text = $this->replacePlaceholders($mFields['text'], $userFields);
            $this->modx->mail->set(modMail::MAIL_BODY, $html);
            $this->modx->mail->set(modMail::MAIL_BODY_TEXT, $text);

            $success = $this->testMode
                ? true
                : $this->modx->mail->send();

            if (!$success) {
                $this->setError($this->modx->mail->mailer->ErrorInfo);
            }

            $msg = $success? ' -- Success' : ' -- Failed';

            /* This happens once for each user */
            $this->logger->write("\nSending to " . $userFields['username'] . $msg);

            $this->modx->mail->reset();
        }

        if (isset($this->properties['unitTest'])) {
            $fields = array(
                'html' => $this->mailFields['html'],
                'text' => $this->mailFields['text'],
                'from_email' => $this->mailFields['fromEmail'],
                'from_name' => $this->mailFields['fromName'],
                'subject' => $this->mailFields['subject'],
                'headers' => $this->headerFields,
                'reply-to' => $this->mailFields['reply-to'],
                'cc' => $this->mailFields['cc'],
                'bcc' => $this->mailFields['bcc'],

            );
        }

         return $success;
    }


    /**
     * @param $tpl string - Message HTML or text
     * @param $placeholders array - Array of keys and values for placeholders
     * @return string - altered $tpl
     */
    public function replacePlaceholders($tpl, $placeholders) {
        if (! empty($placeholders)) {
            foreach ($placeholders as $k => $v) {
                $tpl = str_replace('{{+' . $k . '}}', $v, $tpl);
            }
        }

        return $tpl;
    }

    /**
     * Get a specific property.
     * @param string $k
     * @param mixed $default
     * @return mixed
     */
    public function getProperty($k, $default = null) {
        return array_key_exists($k, $this->properties) ? $this->properties[$k] : $default;
    }
}
