<?php
/**
 * MandrillX class file for MandrillX extra
 *
 * Copyright 2013 by Bob Ray <http://bobsguides.com>
 * Created on 02-04-2014
 *
 * MandrillX is free software; you can redistribute it and/or modify it under the
 * terms of the GNU General Public License as published by the Free Software
 * Foundation; either version 2 of the License, or (at your option) any later
 * version.
 *
 * MandrillX is distributed in the hope that it will be useful, but WITHOUT ANY
 * WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR
 * A PARTICULAR PURPOSE. See the GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License along with
 * MandrillX; if not, write to the Free Software Foundation, Inc., 59 Temple
 * Place, Suite 330, Boston, MA 02111-1307 USA
 *
 * @package mandrillx
 */

// require_once dirname(dirname(__FILE__)) . '/mandrill/src/Mandrill.php';

class MandrillX extends Mandrill{
    /** @var $modx modX */
    public $modx;
    /** @var $properties array */
    public $properties;
    /** @var $message array - master array to sent to Mandrill */
    protected $message;

    protected $subaccount;
    protected $headers;
    protected $important;
    protected $track_opens;
    protected $track_clicks;    
    protected $auto_html;
    protected $auto_text;
    protected $inline_css;
    protected $url_strip_qs;
    protected $preserve_recipients;
    protected $view_content_link;
    protected $bcc_address;
    protected $tracking_domain;
    protected $signing_domain;
    protected $return_path_domain;
    protected $merge;
    protected $subject;
    protected $from_email;
    protected $errors;
    protected $to;
    protected $global_merge_vars;
    protected $merge_vars;
    protected $userPlaceholders;
    protected $testMode;





    function __construct(&$modx, &$props = array()) {
        /** @var $modx modX */
        $this->modx =& $modx;
        $this->properties =& $props;
        $apiKey = $this->modx->getOption('mandrill_api_key', $this->properties,
            $this->modx->getOption('mandrill_api_key', null), true);
        parent::__construct($apiKey);
    }
    public function init() {
        $config = $this->properties;
        $this->message = array();
        $this->errors = array();
        $this->to = array();
        $this->global_merge_vars = array();
        $this->merge_vars = array();
        $this->message['html'] = '';
        $this->message['text'] = '';

        $this->message['merge'] = true;
        $subject = $this->modx->getOption('subject', $config, '');
        $this->message['subject'] =  !empty($subject)? $subject : 'Update from ' . $this->modx->getOption('site_name');
        $from = $this->modx->getOption('from_email', $config,'');
        $this->message['from_email'] = !empty($from)? $from: $this->modx->getOption('emailsender');

        $from_name = $this->modx->getOption('from_name', $config, '');
        $this->message['from_name'] = !empty($from_name)
            ? $from_name
            : $this->modx->getOption('site_name');

        /* HTML message body and Text version can be sent
         * in the config array, but can also be set directly
         * with setHTML() and setText();
         */
           
        $html = $this->modx->getOption('html', $config, '');
        if (! empty($html)) {
            $this->setHTML($html);
        }
        $text = $this->modx->getOption('text', $config, '');
        if (!empty($text)) {
            $this->setText($text);
        }
        $this->testMode = (bool)$this->modx->getOption('testMode', $config, false);
        $this->message['important'] = (bool) $this->modx->getOption('important', $config, false);
        
        /* subaccount must exist at mandrillapp.com or the send will fail; defaults to test */
        $this->message['subaccount'] = $this->modx->getOption('subaccount', $config, 'test');
        
        /* reply-to header is set automatically. 
         * Use this for any additional headers, 
         * but be sure to include 'reply-to */
        $headers = $this->modx->getOption('headers', $config, '');
        $this->message['headers'] = $this->getHeaders($headers);

        /* All these can be set at mandrillapp.com and omitted from the properties.
         * If set to null, the values at Mandrill will be used. signing_domain default
         * to mandrillapp.com */
        $this->message['track_opens'] = $this->modx->getOption('track_opens', $config, null);
        $this->message['track_clicks'] = $this->modx->getOption('track_clicks', $config, null);
        $this->message['auto_html'] = $this->modx->getOption('auto_html', $config, null);
        $this->message['auto_text'] = $this->modx->getOption('auto_text', $config, null);
        $this->message['inline_css'] = $this->modx->getOption('inline_css', $config, null);
        $this->message['url_strip_qs'] = $this->modx->getOption('url_strip_qs', $config, null);
        $this->message['preserve_recipients'] = $this->modx->getOption('preserve_recipients', $config, null);
        $this->message['view_content_link'] = $this->modx->getOption('view_content_link', $config, null);
        $this->message['bcc_address'] = $this->modx->getOption('bcc_address', $config, null);
        $this->message['tracking_domain'] = $this->modx->getOption('tracking_domain', $config, null);
        $this->message['signing_domain'] = $this->modx->getOption('signing_domain', $config, null);
        $this->message['return_path_domain'] = $this->modx->getOption('return_path_domain', $config, null);

    }

    /**
     * Convert double-curly-bracket tags to Mandrill-style tags
     * in HTML message text.
     *
     *  {{+tagName}} -> *|tagName|*
     *
     * There is no need to call this directory, as it is always called
     * automatically just before the message is sent.
     *
     * @param $text string - Text of message with {{+tagName}} tags.
     * @return string - Same text with tags converted to Mandrill-style
     *    tags: *|tagName|*
     */
    public function prepareTpl($text) {
        /*$chunk = $modx->getObject('modChunk', array('name' => $tpl));
        $text = $chunk->getContent();*/
        $text = str_replace('{{+', '*|', $text);
        $text = str_replace('}}', '|*', $text);
        return $text;
    }

    public function setHTML($html) {
        $this->message['html'] = $html;
        $this->setUserPlaceholders($html);
    }

    public function setText($text) {
        $this->message['text'] = $text;
    }

    /**
     * return an array of extra headers based on $headers property:
     * 'Reply-to:you@yourdomain.com,header2:somevalue';
     * @param $headers
     *
     * if $headers is empty, returns 'Reply-To => emailsender system setting'
     *
     * @return array
     */
    protected function getHeaders($headers) {
        $h = array();
        if (empty($headers)) {
            $h = array(
              'Reply-To' => $this->modx->getOption('emailsender'),
            );
        } else {
            $pairs = explode(',', $headers);
            $hasReplyTo = false;
            foreach($pairs as $pair) {
                if (empty($pair)) {
                    continue;
                }
                $couple = explode(':', $pair);
                if (! isset($couple[1]))  {
                    $this->setError($this->modx->lexicon('nf_malformed_header'));
                    return array(); /* error - no headers */
                } else {
                    $h[trim($couple[0])] = trim($couple[1]);
                    if (stristr($couple[0], 'reply-to')) {
                        $hasReplyTo = true;
                    }
                }
            }
            /* Add reply-to if it's not there */
            if (! $hasReplyTo) {
                $h['Reply-To'] = $this->modx->getOption('emailsender');
            }

        }
        return $h;
    }

    protected function setError($msg) {
        $this->errors[] = $msg;
    }

    public function getErrors() {
        return $this->errors;
    }
    public function hasError() {
        return !empty($this->errors);
    }
    
    /** 
     * Clears user and merge arrays for batch sending.
     * Options and global merge variables are preserved. 
     */
    public function clearUserData() {
        $this->to = array();
        $this->merge_vars = array();
    }

    public function sendBatch() {
        $this->message['to'] = $this->to;
        $this->message['merge_vars'] = $this->merge_vars;
        $this->message['global_merge_vars'] = $this->global_merge_vars;
        /*$this->message['html'] = $this->prepareTpl($this->message['html']);
        if( !empty($this->message['text'])) {
            $this->message['text'] = $this->prepareTpl($this->message['text']);
        }*/

        /* Calls parent class send() */
        if (! $this->testMode) {
            $retVal = 'Error';
            try {
                $retVal = $this->messages->send($this->message);
            } catch (Exception $e) {

                $this->SetError($e->getMessage());
            }
        } else {
            $retVal = 'OK';
        }
        return $retVal;
    }

    /**
     * @param $fields array - array of key/value pairs for global
     * merge variables (these are the same for every email)
     */
    public function setGlobalMergeVars ($fields) {
        foreach($fields as $key => $value) {
            $key = strtoupper($key);
            $this->global_merge_vars[] = array(
                'name' => $key,
                'content' => $value,
            );
        }
        
    }

    /**
     * Add a user to the $to and $mergeVars array
     * 'email' and 'name' are the only required fields.
     *
     * Placeholders in the message itself should not
     * use those keys.
     *
     * @param $fields
     */
    public function addUser($fields) {
        $vars = array();
        $this->to[] = array(
            'email' => $fields['email'],
            'name' => $fields['name'],
            'type' => 'to',
        );
        foreach($fields as $key => $value) {
            if ($key == 'name' || $key == 'email') {
                continue;
            }
            $vars[] = array(
                'name' => strtoupper($key),
                'content' => $value,
            );
        }
        $this->merge_vars[] = array(
            'rcpt' => $fields['email'],
            'vars' => $vars,

        );
    }

    /**
     *  Sets the array of user placeholder names used in the tpl chunk
     *
     * @param $tpl
     */
    public function setUserPlaceholders($pArray) {
        $this->userPlaceholders = $pArray;
    }

    /**
     * Gets the array of user placeholder names used in the tpl chunk
     *
     * @return array
     */
    public function getUserPlaceholders() {
        return $this->userPlaceholders;
    }
    /* Not used in this class*/
    public function setDomain($domain) {
        $this->domain = $domain;
    }

    public function setMailFields() {

    }

    public function setHeaderFields() {

    }

}