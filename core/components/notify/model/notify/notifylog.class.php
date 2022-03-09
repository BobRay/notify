<?php

class NotifyLog {
    public $logFile; //full path to notify log.


    function __construct() {

    }

    /** Sets full path to log file and creates it.
     * Default format: {$core_path}components/notify/notify-logs/{$pageAlias}--2022-03-07-06.11.31pm (MailgunX).txt
     * @param string $filePath -- full path as above;
     * @return bool -- true on success, else false
     */

    public function init($filePath) {
        $success = false;
         $fp = fopen($filePath, 'w');
         if ($fp) {
             $this->logFile = $filePath;
             $success = true;
             fclose($fp);
         }

         return $success;
    }

    /** Write $msg to log file */
    public function write($msg) {
        $fp = fopen($this->logFile, 'a');
        if ($fp) {
            fwrite($fp, $msg);
            fclose($fp);
        }
    }

    public function removeOldFiles($dir, $maxLogs) {
        /*if ($this->debug) {
            $this->modx->log(modX::LOG_LEVEL_ERROR, 'Removing old files');
        }*/
        $files = glob($dir . '/*.*');

        $over = count($files) - $maxLogs;

        if ($over > 0) {
            array_multisort(
                    array_map('filemtime', $files),
                    SORT_NUMERIC,
                    SORT_ASC,
                    $files
            );
            for ($i = 0; $i < $over; $i++) {
               /* if ($this->debug) {
                    $this->modx->log(modX::LOG_LEVEL_ERROR, 'Removing an old file');
                }*/
                unlink($files[$i]);
            }
        }
        /*if ($this->debug) {
            $this->modx->log(modX::LOG_LEVEL_ERROR, 'All old files removed');
        }*/
    }



}
