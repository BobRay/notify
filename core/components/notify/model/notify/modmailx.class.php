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


Class modMailX  implements MailService {
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
    }

    public function init() {
        $this->emailArray = $this->modx->getOption('emailArray', $this->properties, array());
        $this->recipientVariables = array();
        $this->testMode = $this->modx->getOption('testMode', $this->properties, false);

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

    public function setHeaderFields($fields = array()) {

        foreach($fields as $k => $v) {
            $this->headerFields[] = $k . ':' . $v;

        }
    }

    /**
     * Sets fields common to all messages - change keys to what the service expects
     * @param $fields array
     */
    public function setMailFields($fields) {
        $this->mailFields = array(
            'from' => $this->modx->getOption('from', $fields, ''),
            'subject' => $this->modx->getOption('subject',$fields, ''),
            'text' => $this->modx->getOption('text', $fields, ''),
            'html' => $this->modx->getOption('html', $fields, ''),
            'fromEmail' => $this->modx->getOption('fromEmail', $fields, ''),
            'fromName' => $this->modx->getOption('fromName', $fields, ''),
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

        /* These will be set even if not used in message */
        $extraFields = array(
            'first',
            'last',
            'fullname',
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


    public function sendBatch() {

        foreach ($this->emailArray as $email) {

            if (!empty($this->headerFields)) {
                foreach ($this->headerFields as $k => $v) {
                    $this->modx->mail->header($v);
                }
            }
            $html = $this->replacePlaceholders($this->mailFields['html'], $this->recipientVariables[$email]);
            $text = $this->replacePlaceholders($this->mailFields['text'], $this->recipientVariables[$email]);
            $this->modx->mail->set(modMail::MAIL_BODY, $html);
            $this->modx->mail->set(modMail::MAIL_BODY_TEXT, $text);
            $this->modx->mail->set(modMail::MAIL_SUBJECT, $this->mailFields['subject']);
            $this->modx->mail->set(modMail::MAIL_FROM, $this->mailFields['fromEmail']);
            $this->modx->mail->set(modMail::MAIL_FROM_NAME, $this->mailFields['fromName']);
            $this->modx->mail->address('to', $email, $this->recipientVariables[$email]['fullname']);

            $success = $this->testMode
                ? true
                : $this->modx->mail->send();

            if (!$success) {
                $this->setError($this->modx->mail->mailer->ErrorInfo);
            }
            $this->modx->mail->reset();
        }

         return true;
        // $tag = str_replace(array('/', ' '), array('-', '_'), strftime('%c'));
    }


    /**
     * @param $tpl string - Message HTML or text
     * @param $placeholders array - Array of keys and values for placeholders
     * @return string - altered $tpl
     */
    public function replacePlaceholders($tpl, $placeholders) {
        foreach( $placeholders as $k => $v) {
            $tpl = str_replace('{{+' . $k . '}}', $v, $tpl);
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
