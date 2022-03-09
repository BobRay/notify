<?php

use Mailgun\Mailgun;
/**
 * MailgunX class file for Notify extra
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


Class MailgunX extends Mailgun implements MailService {
    protected  $debug;

    protected  $properties; /* ScriptProperties */

    protected $stars = MailService::STARS;

    /** @var $mailgunPublicApiKey string - Used only for verifying emails, not for sending them */
    protected  $mailgunPublicApiKey = '';
    /** @var $emailArray array - used internally to store recipients' email addresses */
    protected  $emailArray = array();
    // protected  $messageHTML = '';
    protected  $messageText = '';
    protected  $messageSubject = '';
    protected  $messageFrom = '';
    protected  $messageFromName = '';
    protected  $messageReplyTo = '';
    protected  $headerFields = array();
    protected  $mailFields = array();
    /** @var $recipientVariables array - array of keys and values for merge tags */
    protected $recipientVariables = array();
    protected $testMode = false;

    /** @var string $domain - Mailgun Sending domain - can be set to real domain or sandbox domain in calling function */
    protected $domain = '';
    /** @var $userPlaceholders array - array of user placeholders (merge tags) actually used in message: {{+pName}} */
    protected $userPlaceholders = array();
    /** @var  $modx modX */
    protected $modx;
    protected $client = null; // instance of Mailgun class

    protected $errors = array();
    protected $apiKey = null;

    protected $logger = null;

    function __construct(&$modx, $props, $logger = null) {
        $this->modx =& $modx;
        $this->properties =& $props;
        $this->logger = $logger;
        $this->apiKey = $this->modx->getOption('mailgun_api_key', $this->properties,
            $this->modx->getOption('mailgun_api_key', null), true);
        parent::__construct($this->apiKey);

    }

    public function init() {
        $this->logFile = $this->properties['logFile'];
        $useSandbox = $this->modx->getOption('mailgun.use_sandbox', $this->properties, false);
        if ($useSandbox) {

            $this->domain = $this->modx->getOption('mailgun.sandbox_domain', $this->properties,
                $this->modx->getOption('mailgun.sandbox_domain', null), true);

        } else {
            $this->domain = $this->modx->getOption('mailgun.domain', $this->properties,
                $this->modx->getOption('mailgun.domain', null), true);
        }
        if (empty($this->apiKey)) {
            $this->setError($this->modx->lexicon('nf.no_mailgun_api_key'));
            return false;
        } elseif (empty($this->domain)) {
            $this->setError($this->modx->lexicon('nf.no_mailgun_domain'));
            return false;
        }

        $this->debug = $this->modx->getOption('nf_debug', null, false);

        if ($this->debug) {
            $this->logger->write("\n ********** In MailgunX class **********\n");
        }

        $this->domain = empty($this->domain) ? MODX_HTTP_HOST : $this->domain;
        $this->mailgunPublicApiKey = $value = $this->modx->getOption('mailgun.public_api_key', $this->properties);

        $this->emailArray = $this->modx->getOption('emailArray', $this->properties, array());

        $this->recipientVariables = array();
        $this->testMode = $this->modx->getOption('testMode', $this->properties, false);
        return true;
    }

    public function clearUserData() {
        $this->emailArray = array();
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

    public function setHeaderFields($fields = array()) {

        foreach($fields as $k => $v) {
            $this->headerFields['h:' . $k] = $v;
        }
    }

    /**
     * Sets fields common to all messages - change keys to what the service expects
     * @param $fields array
     */
    public function setMailFields($fields) {
        if ($this->debug) {
            $this->logger->write("\n" . $this->stars . "\nFields sent to setMailFields: " . print_r($fields, true) . "\n" . $this->stars . "\n");
        }
        $this->mailFields = array(
            'from' => $fields['from'],
            'subject' => $fields['subject'],
            'text' => $fields['text'],
            'html' => $fields['html'],
        );
        $this->mailFields['h:Reply-To'] = $fields['reply-to'];
    }


    /**
     *  Create $recipientVariables array with fields actually used in Tpl
     *  for all users.
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
            $this->logger->write("\n" . $this->stars . "\nFields sent to addUser: " . print_r($fields, true) . "\n" . $this->stars . "\n");
        }
        $email = $fields['email'];
        $userFields = $this->userPlaceholders;
        /* Add username */
        if (!in_array('username', $userFields )) {
            $userFields[] = 'username';
        }
        $userVariables = array();

        /* Unset unused user variables */
        foreach ($fields as $key => $value) {
            if (in_array($key, $userFields)) {
                $userVariables[$key] = $value;
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
        /*$chunk = $modx->getObject('modChunk', array('name' => $tpl));
        $text = $chunk->getContent();*/
        $text = str_replace('{{+', '%recipient.', $text);
        $text = str_replace('}}', '%', $text);
        
        if ($this->debug) {
            $this->logger->write("\n" . $this->stars . "\nPrepared Tpl\n" . $text . $this->stars . "\n" );
        }
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


    public function sendBatch($batchNumber) {

        $to = array_keys($this->recipientVariables)[0];

        if ((int) $batchNumber === 1) {

            $sampleMessage = "Sample Message:" .
                    "\nFrom: " . $this->mailFields['from'] .
                    "\nTo: " . $to .
                    "\nSubject: " . $this->mailFields['subject'];

            $sampleMessage .= "\nMessage Body: \n" .
                    $this->replacePlaceholders($this->mailFields['html'], $this->recipientVariables[$to]);

            $this->logger->write($sampleMessage . "\n");
        }

        $fields = $this->mailFields;

        if ($this->debug) {
            $this->logger->write("\n" . $this->stars . "\nmailFields: " .
                    print_r($this->mailFields, true) . $this->stars . "\n");
        }

        $fields['to'] = implode(', ', $this->emailArray);

        if (! empty($this->headerFields)) {
            $fields = array_merge($fields, $this->headerFields);
        }
        if ($this->debug) {
            $this->logger->write("\n" . $this->stars . "\nRecipientVariables: \n" . print_r($this->recipientVariables, true) . "\n" . $this->stars . "\n");
        }

        $fields['recipient-variables'] = $this->modx->toJSON($this->recipientVariables);

        if ($this->testMode) {
            $fields['o:testmode'] = true;
        }

        if ($this->debug) {
            $this->logger->write("\n" . $this->stars . "\nFinal Fields\n" . print_r($fields, true) . "\n" . $this->stars . "\n");
        }
        /* Uses parent class sendMessage() */
        try {
            $response = $this->sendMessage($this->domain, $fields);
        } catch (Exception $e) {
            try { //retry
                $response = $this->sendMessage($this->domain, $fields);
            } catch (Exception $e) {
                $this->modx->log(modX::LOG_LEVEL_ERROR, '[MailgunX] ' . $e->getMessage());
            }
        }

        $sendList = "\n" . $this->stars . "\nSending to:\n";
        $list = $this->recipientVariables;
        foreach ($list as $key => $value) {
            $sendList .= "\n" . $list[$key]['username'] . " (" . $key . ")";
        }

        if ($this->debug) {
            $this->logger->write("\n" . $this->stars . "\nRESPONSE: " .
                    print_r($response, true) . "\n" . $this->stars . "\n");
        }

        $this->logger->write($sendList);

        /* report success or failure of batch */
        $code = $response->http_response_code;
        if ($code == 200 || $code == 421) {
            $msg = "\n\nBatch {$batchNumber} sent successfully\n" . $this->stars . "\n";
        } else {
            $msg = "\n\nSending batch {$batchNumber} failed with code {$code}\n" .
                    $this->stars . "\n";
        }
        $this->logger->write($msg);

        if (isset($this->properties['unitTest'])) {
            echo "\n MailgunX Message: " . print_r($fields, true) . "\n";
        }

        return $response;
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

    public function replacePlaceholders($tpl, $placeholders) {
        if ($this->debug) {
            $this->logger->write("\n" . $this->stars . "\nTPL: " . $tpl . "\n");
            $this->logger->write("\n***\nplaceholders: " .
                    print_r($placeholders, true) . "\n" . $this->stars . "\n");
        }
        foreach ($placeholders as $k => $v) {
            $tpl = str_replace('%recipient.' . $k . '%', $v, $tpl);
        }

        return $tpl;
    }
}
