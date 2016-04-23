<?php

use Mailgun\Mailgun;
/**
 * MailgunX class file for Notify extra
 *
 * Copyright 2013-2015 by Bob Ray <http://bobsguides.com>
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
    var $debug;
    
    var $properties; /* ScriptProperties */


    /** @var $mailgunPublicApiKey string - Used only for verifying emails, not for sending them */
    var $mailgunPublicApiKey = '';
    /** @var $emailArray array - used internally to store recipients' email addresses */
    var $emailArray = array();
    // var $messageHTML = '';
    var $messageText = '';
    var $messageSubject = '';
    var $messageFrom = '';
    var $messageFromName = '';
    var $messageReplyTo = '';
    var $headerFields = array();
    var $mailFields = array();
    /** @var $recipientVariables array - array of keys and values for merge tags */
    var $recipientVariables = array();
    var $testMode = false;

    /** @var string $domain - Mailgun Sending domain - can be set to real domain or sandbox domain in calling function */
    var $domain = '';
    /** @var $userPlaceholders array - array of user placeholders (merge tags) actually used in message: {{+pName}} */
    var $userPlaceholders = array();
    /** @var  $modx modX */
    var $modx;
    var $client = null; // instance of Mailgun class

    var $errors = array();
    var $apiKey = null;

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


    public function sendBatch() {

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
        $response = $this->sendMessage($this->domain, $fields);
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
