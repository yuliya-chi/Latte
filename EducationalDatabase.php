<?php
    class EducationalDatabase {
        private $dbHost = DB_HOST_EDC;
        private $dbName = DB_NAME_EDC;
        private $dbUser = DB_USER_EDC;
        private $dbPass = DB_PASS_EDC;

        private $dbHandler;
        private $error;

        public function __construct() {
            $connectionString = 'mysql:host=' . $this->dbHost . ';dbname=' . $this->dbName;
            $options = array(
                PDO::ATTR_EMULATE_PREPARES => false,
            );

            try {
                $this->dbHandler = new PDO(
                    $connectionString,
                    $this->dbUser,
                    $this->dbPass,
                    $options
                );
            } catch(PDOException $ex) {
                $this->error = $ex->getMessage();
            }
        }

        public function tryQuery($sql) {
            $queryRes = $this->dbHandler->prepare($sql);
            if($queryRes) {
                return true;
            } else {
                return $this->dbHandler->errorInfo();
            }
        }
    }
