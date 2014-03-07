<?php

/**
 * Class getTaskStatsProcessor
 */
class NfSendProcessor extends modProcessor {

    protected $props;
    protected $errors;
    protected $successMessages;
    protected $testMode;


    public function initialize() {
        $config = $this->modx->fromJSON($_SESSION['nf_config']);
        $this->properties = array_merge($this->properties, $config);
        $this->testMode = $this->getProperty('testMode',false);
        $this->setCheckbox('send_tweet');
        $this->setCheckbox('send_bulk');
        $this->setCheckbox('require_all_tags');
        $this->setCheckbox('single');

        return true;
    }
    /**
     * @param $percent int
     * @param $text1 string
     * @param $text2 string
     * @param $pb_target modChunk
     */
    public  function update($percent, $text1, $text2, &$pb_target) {


        $msg = $this->modx->toJSON(array(
            'percent' => $percent,
            'text1'   => $text1,
            'text2'   => $text2,
        ));

            /*return $this->modx->toJSON(array(
                'success' => true,
                'stats'   => $stats
            ));*/

        /* use a chunk for the status "file" */

        $pb_target->setContent($msg);
        $pb_target->save();
    }

    public function checkPermissions() {
        $valid =  $this->modx->hasPermission('view_user');
        if (! $valid) {
            $this->setError($this->modx->lexicon('nf.no_view_user_permission~~User does not have view_user permission'));
        }
        return $valid;
    }

    public function process($scriptProperties = array()) {


        $chunk = $this->modx->getObject('modChunk', array('name' => 'Debug'));

        if (isset($this->properties)) {
            $content =  print_r($this->properties, true);
        } else {
            $content = 'No Props';
        }
        $chunk->setContent($content);
        $chunk->save();

        $sendBulk = (bool) $this->getProperty('send_bulk', false);

        if ($sendBulk) {
            $this->sendBulk();
        }

        /* sleep(3); */
        $this->errors[] = 'Dummy Error msg 1';
        $this->errors[] = 'Dummy Error msg 2';
        $this->successMessages[] = 'Dummy Success msg 1';
        $this->successMessages[] = 'Dummy Success msg 2';

        $results["status"] = empty($this->errors)? "Yes" : "No";
        $results["errors"] = $this->errors;
        $results["successMessages"] = $this ->successMessages;
        return $this->modx->toJSON($results);
    }

    protected function sendBulk() {
        $statusChunk = $this->modx->getObject('modChunk', array('name' => 'NfStatus'));

        $msg1 = $this->getProperty('groups', 'No Groups');
        $msg2 = $this->getProperty('tags', 'No Tags');
        for ($i = 1; $i <= 120; $i++) {

            $this->update($i, $msg1, $msg2, $statusChunk);
            set_time_limit(0);
            usleep(100000);
        }
        $this->update(0, 'Starting', '', $statusChunk);


    }
    protected function send_single() {

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
            /* This will get recent tweets */
            /* $response = $tweet->get('statuses/user_timeline', array('screen_name' => 'BobRay')); */

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

return 'NfSendProcessor';