<?php

/**
 * Class getTaskStatsProcessor
 */
class NfSendProcessor extends modProcessor {



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

    public function process($scriptProperties = array()) {
    /*include "c:/xampp/htdocs/addons/core/model/modx/modx.class.php";

    $modx = new modX();
    $modx->initialize('mgr');*/

    $config = $this->modx->fromJSON($_SESSION['nf_config']);
    $this->properties = array_merge($this->properties, $config);
    $chunk = $this->modx->getObject('modChunk', array('name' => 'Debug'));

    if (isset($this->properties)) {
        $content =  "x" . print_r($this->properties, true);
    } else {
        $content = 'No Post';
    }
        
    if (isset($scriptProperties)) {
        $content .= "\nscriptProperties: " . print_r($scriptProperties, true);
    } else {
        $content .= "\nNo scriptProperties";
    }
    if (isset($config)) {
        $content .= "\nconfig: " . print_r($config, true);
    } else {
        $content .= "\nNo config";
    }
    $chunk->setContent($content);
    $chunk->save();
    $statusChunk = $this->modx->getObject('modChunk', array('name' => 'NfStatus'));
    // $msg1 = $_POST['groups'];

    $msg1 = $this->getProperty('groups', 'No Groups');
    $msg2 = $this->getProperty('tags', 'No Tags');
    for ($i = 1; $i <= 120; $i++) {

       $this->update($i, $msg1, $msg2, $statusChunk);
       set_time_limit(0);
       usleep(100000);
    }

    sleep(3);
    $this->update(0, 'Starting', '', $statusChunk);
    }
}

return 'NfSendProcessor';