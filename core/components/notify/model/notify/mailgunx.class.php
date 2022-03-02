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


// For future autoloader
// require_once dirname(dirname(__FILE__)) . '/mailgun/vendor/autoload.php';



Class MailgunX extends Mailgun implements MailService {
    protected  $debug;
    
    protected  $properties; /* ScriptProperties */


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

    function __construct(&$modx, $props) {
        $this->modx =& $modx;
        $this->properties =& $props;
        $this->apiKey = $this->modx->getOption('mailgun_api_key', $this->properties,
            $this->modx->getOption('mailgun_api_key', null), true);
        parent::__construct($this->apiKey);

    }

    public function init() {

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

        $this->debug = $this->modx->getOption('mailgun.debug', $this->properties, false);
        if ($this->debug) {
            $this->modx->log(modX::LOG_LEVEL_ERROR, "\n **********  MailgunX class **********\n");
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
        $this->mailFields = array(
            'from' => $fields['from'],
            'subject' => $fields['subject'],
            'text' => $fields['text'],
            'html' => $fields['html'],
        );
        $this->mailFields['h:Reply-To'] = $fields['reply-to'];
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
        $email = $fields['email'];
        $userFields = $this->userPlaceholders;
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
            $this->modx->log(modX::LOG_LEVEL_ERROR, "\n\nPrepared Tpl\n" . $text);
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

        $tag = str_replace(array('/', ' '), array('-', '_'), strftime('%c'));

        $fields = $this->mailFields;
        $fields['to'] = implode(', ', $this->emailArray);
        if (! empty($this->headerFields)) {
            $fields = array_merge($fields, $this->headerFields);
        }
        $fields['recipient-variables'] = $this->modx->toJSON($this->recipientVariables);

        if ($this->testMode) {
            $fields['o:testmode'] = true;
        }
        if ($this->debug) {
            $this->modx->log(modX::LOG_LEVEL_ERROR, "\n\nFinal Fields\n"  . print_r($fields, true));
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
}
