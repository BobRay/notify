<?php
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

require_once dirname(dirname(__FILE__)) . '/mailgun/vendor/autoload.php';

use Mailgun\Mailgun;

Class MailgunX extends Mailgun {
    var $debug;
    
    var $props; /* ScriptProperties */
    
    /** @var $mailgunPublicApiKey string - Used only for verifying emails, not for sending them */
    var $mailgunPublicApiKey = '';
    /** @var $emailArray array - used internally to store recipients' email addresses */
    var $emailArray = array();
    var $messageHTML = '';
    var $messageText = '';
    var $messageSubject = '';
    var $messageFrom = '';
    var $messageFromName = '';
    var $messageReplyTo = '';
    /** @var $recipientVariables array - array of keys and values for merge tags */
    var $recipientVariables = array();
    var $testMode = false;

    /** @var string $domain - Mailgun Sending domain - can be set to real domain or sandbox domain in calling function */
    var $domain = '';
    /** @var $userPlaceholders array - array of user placeholders (merge tags) actually used in message: {{+pName}} */
    var $userPlaceholders = array();
    /** @var  $modx modX */
    var $modx;

    var $errors = array();


    function __construct(&$modx, $apiKey, &$props) {
        $this->modx =& $modx;
        $this->props = &$props;
        parent::__construct($apiKey);
    }

    public function init() {

        $this->debug = $this->modx->getOption('mailgun.debug', $this->props, false);
        if ($this->debug) {
            $this->modx->log(modX::LOG_LEVEL_ERROR, "\n **********  MailgunX class **********\n");
        }
        $this->domain = $this->modx->getOption('domain', $this->props, '');

        $this->domain = empty($this->domain) ? MODX_HTTP_HOST : $this->domain;
        $this->mailgunPublicApiKey = $value = $this->modx->getOption('mailgun.public_api_key', $this->props);
        $this->messageHTML = $this->modx->getOption('html', $this->props, '');
        $this->messageText = $this->modx->getOption('text', $this->props, '');
        $this->messageSubject = $this->modx->getOption('mail_subject', $this->props, '');
        $this->messageFrom = $this->modx->getOption('from_email', $this->props, '');
        $this->messageSubject = $this->modx->getOption("mail_subject", $this->props, '');
        $this->messageReplyTo = $this->modx->getOption('reply_to', $this->props, '');
        $this->messageFromName = $this->modx->getOption('from_name', $this->props, '');
        $this->emailArray = $this->modx->getOption('emailArray', $this->props, array());
        $this->setUserPlaceholders($this->messageHTML);
        $this->recipientVariables = array();
        $this->testMode = $this->modx->getOption('testMode', $this->props, false);
    }

    public function clearUserData() {
        $this->toArray = array();
        $this->recipientVariables = array();
    }

    public function hasError() {
        return (empty($this->errors));
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
    public function setdomain($domain) {
        $this->domain = $domain;
    }


    /**
     * @param $email string - User's naked email address (<email> doesn't work)
     * @param array $fields array() - array of user merge variables:
     *     array(
     *         'first' => 'Bob',
     *         'last' => 'Ray',
     *     );
     *
     *     Must match the merge variable placeholders in the Tpl chunk
     */
    public function addUser($email, $fields = array()) {
        $userFields = $this->userPlaceholders;
        $userVariables = array();

        /* Unset unused user variables */
        foreach ($fields as $key => $value) {
            if (in_array($key, $userFields)) {
                $userVariables[$key] = $value;
            }
        }


        $this->emailArray[] = $email;
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
    public function _prepareTpl($text) {
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
     *  Sets the array of user placeholder names used in the tpl chunk
     *
     * @param $tpl
     */
    public function setUserPlaceholders($tpl) {

        if ($this->debug) {
            $this->modx->log(modX::LOG_LEVEL_ERROR, "\n\n TPL in setUserPlaceholders\n" . $tpl);
        }
        $pattern = '#\{\{\+([a-zA-Z_\-]+?)\}\}#';
        preg_match_all($pattern, $tpl, $matches);

        $this->userPlaceholders = isset($matches[1])
            ? $matches[1]
            : array();
        if ($this->debug) {
            $this->modx->log(modX::LOG_LEVEL_ERROR, "\n\nUser Placeholders\n" .  print_r($this->userPlaceholders, true));
        }
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
        $this->messageHTML = $this->_prepareTpl($this->messageHTML);
        if (empty($this->messageText) && (!empty($this->messageHTML))) {
            $this->messageText = strip_tags(preg_replace('/<br\s?\/?>/i', "\r\n", $this->messageHTML));
        } else {
            $this->messageText = $this->_prepareTpl($this->messageText);
        }
        $tag = str_replace(array('/', ' '), array('-', '_'), strftime('%c'));

        $fields = array(
            'to' => implode(', ', $this->emailArray),
            'from' => $this->messageFrom,
            'subject' => $this->messageSubject,
            'text' => $this->messageText,
            'html' => $this->messageHTML,
            'recipient-variables' => $this->modx->toJSON($this->recipientVariables),
            'o:tag' => array($tag),
            'h:Reply-To' => $this->messageReplyTo,
        );

        if ($this->testMode) {
            $fields['o:testmode'] = true;
        }
        if ($this->debug) {
            $this->modx->log(modX::LOG_LEVEL_ERROR, "\n\nFinal Fields\n"  . print_r($fields, true));
        }
        /* Uses parent class sendMessage() */

        $response = $this->sendMessage($this->domain, $fields);
        return $response;

    }
}
