<?php

/**
 * Class getTaskStatsProcessor
 */
class NfSendEmailProcessor extends modProcessor {

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
        $sendSingle = (bool) $this->getProperty('single', false);

        if ($sendBulk) {
            $this->sendBulk();
        }

        if ($sendSingle) {
            $singleId = $this->getProperty('single_id', 'admin');
            $this->sendBulk($singleId);
        }



        $results["status"] = empty($this->errors)? "Yes" : "No";
        $results["errors"] = $this->errors;
        $results["successMessages"] = $this ->successMessages;
        return $this->modx->toJSON($results);
    }

    protected function sendBulk($singleId = null) {
        if (!empty($singleId)) {
            $this->setSuccess('Sent Single Email to ' . $singleId);
            return;
        }
        $statusChunk = $this->modx->getObject('modChunk', array('name' => 'NfStatus'));

        $msg1 = $this->getProperty('groups', 'No Groups');
        $msg2 = $this->getProperty('tags', 'No Tags');
        for ($i = 1; $i <= 120; $i++) {

            $this->update($i, $msg1, $msg2, $statusChunk);
            set_time_limit(0);
            usleep(100000);
        }
        $this->update(0, 'Starting', '', $statusChunk);

        $this->setSuccess('Email Sent Successfully to 23 Users');

    }


    public function setError($msg) {
        $this->errors[] = $msg;
    }

    public function setSuccess($msg) {
        $this->successMessages[] = $msg;
    }
}

return 'NfSendEmailProcessor';