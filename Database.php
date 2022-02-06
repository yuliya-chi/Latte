<?php
    class Database {
        private $dbHost = DB_HOST;
        private $dbName = DB_NAME;
        private $dbUser = DB_USER;
        private $dbPass = DB_PASS;

        private $statement;
        private $dbHandler;
        private $error;

        public function __construct() {
            $connectionString = 'mysql:host=' . $this->dbHost . ';dbname=' . $this->dbName;
            $options = array(
                PDO::ATTR_PERSISTENT => true,
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION
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

        public function setQuery($sql) {
            $this->statement = $this->dbHandler->prepare($sql);
        }

        public function bindQueryParameter($param, $value, $type = null) {
            switch(is_null($type)) {
                case is_bool($value):
                    $type = PDO::PARAM_BOOL;
                    break;
                case is_int($value):
                    $type = PDO::PARAM_INT;
                    break;
                case is_null($value):
                    $type = PDO::PARAM_NULL;
                    break;
                default:
                    $type = PDO::PARAM_STR;
            } 

            $this->statement->bindValue($param, $value, $type);
        }

        public function executeQuery() {
            return $this->statement->execute();
        }

        public function getSingleResult() {
            $this->executeQuery();
            return $this->statement->fetch(PDO::FETCH_OBJ);
        }

        public function getArrayResult() {
            $this->executeQuery();
            return $this->statement->fetchAll(PDO::FETCH_OBJ);
        }

        public function getAffectedRowsCount() {
            return $this->statement->rowCount();
        }
    }
