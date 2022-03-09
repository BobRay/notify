<?php

    interface MailService {
        const STARS = "********************************************************************************";
        /* Sets $this->modx, $this->properties, $this->logger */
        public function __construct(&$modx, $properties, NotifyLog $logger);

        /* Initialize data fields */
        public function init();

        /* Clears User Data between batches */
        public function clearUserData();

        /* Adds message to $this->errors */
        public function setError($msgString);

        /* Returns $this->errors */
        public function getErrors();

        /* Returns ! empty($this->errors) */
        public function hasError();

        /* Allows setting $this->domain from outside class - often unused */
        public function setDomain($domain);

        /* Allows setting userPlaceholders (interior part of merge tags) from outside - often set in the processor */
        /**
         * @param $phArray
         * @return mixed
         */
        public function setUserPlaceholders($phArray);

        /* Adds a user and the user fields in preparation for sendBatch */
        public function addUser($fields);

        /* Converts merge field tags to the form used by the mail service */
        function prepareTpl($tplString);

        /* Returns array of user placeholders (interior part of merge-tags) */
        public function getUserPlaceholders();

        /* Send a batch of emails through the service */
        public function sendBatch($batchNumber);

        /* Set mail fields common to all messages line from, subject, etc.) in $this->mailFields */
        public function setMailFields($fields);

        /* Sets header fields like Reply-To in $this->headerFields */
        public function setHeaderFields($fields);

        public function getProperty($k, $default = null);
    }
