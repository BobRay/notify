<?php

/**
 * Class NfSendTweetProcessor
 *
 * (for LexiconHelper)
 * $modx->lexicon->load('notify:default');
 */
class NfSendTweetProcessor extends modProcessor {

    protected $props;
    protected $errors;
    protected $successMessages;
    protected $testMode;
    protected $debug;


    public function initialize() {
        if (isset($_SESSION['nf_config'])) {
            $config = $this->modx->fromJSON($_SESSION['nf_config']);
            $this->properties = array_merge($this->properties, $config);
        }
        $this->testMode = $this->getProperty('testMode',false);
        $this->debug = $this->getProperty('debug', false);
        return true;
    }

    public function checkPermissions() {
        $valid =  $this->modx->hasPermission('view_user');
        if (! $valid) {
            $this->setError($this->modx->lexicon('nf.no_view_user_permission'));
        }
        return $valid;
    }

    public function process($scriptProperties = array()) {


        if ($this->debug) {
            $chunk = $this->modx->getObject('modChunk', array('name' => 'Debug'));

            if (isset($this->properties)) {
                $content = 'Properties: ' . print_r($this->properties, true);
            } else {
                $content = 'No Props';
            }
            $chunk->setContent($content);
            $chunk->save();
        }


        $this->send_tweet();

        $results["status"] = empty($this->errors)? "Yes" : "No";
        $results["errors"] = $this->errors;
        $results["successMessages"] = $this ->successMessages;
        return $this->modx->toJSON($results);
    }

    protected function send_tweet() {

        require_once(MODX_CORE_PATH . 'components/notify/model/notify/twitteroauth.php');
        $consumer_key = $this->getProperty('twitterConsumerKey', NULL);
        if (!$consumer_key) {
            $this->setError($this->modx->lexicon('nf.twitter_consumer_key_not_set'));
        }
        $consumer_secret = $this->getProperty('twitterConsumerSecret', NULL);
        if (!$consumer_secret) {
            $this->setError($this->modx->lexicon('nf.twitter_consumer_secret_not_set'));
        }
        $oauth_token = $this->getProperty('twitterOauthToken', NULL);
        if (!$oauth_token) {
            $this->setError($this->modx->lexicon('nf.twitter_access_token_not_set'));
        }
        $oauth_secret = $this->getProperty('twitterOauthSecret', NULL);
        if (!$oauth_secret) {
            $this->setError($this->modx->lexicon('nf.twitter_access_token_secret_not_set'));
        }
        $msg = $this->getProperty('tweet_text', '');
        /*$msg = $this->tweetText;*/
        if (empty($msg)) {
            $this->setError($this->modx->lexicon('nf.tweet_field_is_empty'));
        } else {
            $tweet = new TwitterOAuth($consumer_key, $consumer_secret, $oauth_token, $oauth_secret);
            $response = $this->testMode
                ? array()
                : $tweet->post('statuses/update', array('status' => $msg));

            if ($response === NULL) {
                $this->setError($this->modx->lexicon('nf.unknown_error_using_twitter_api'));
            } elseif (isset($response->errors)) {
                $this->setError('<p>' . $this->modx->lexicon('nf.twitter_said_there_was_an_error') .
                    ': ' . $response->errors[0]->message . '</p><br />');
            } else {
                $this->setSuccess($this->modx->lexicon('nf.tweet_sent_successfully'));
            }
        }

    }

    public function setError($msg) {
        $this->errors[] = $msg;
    }

    public function setSuccess($msg) {
        $this->successMessages[] = $msg;
    }
}

return 'NfSendTweetProcessor';