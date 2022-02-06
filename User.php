<?php
    class User {
        private $db;

        public function __construct() {
            $this->db = new Database;  
        }

        public function getAdmins() {
            $this->db->setQuery('SELECT *
                                FROM `users`
                                WHERE is_admin = :is_admin AND processed = :processed');
                                
            $this->db->bindQueryParameter(':is_admin', 1);
            $this->db->bindQueryParameter(':processed', 1);

            $resultRows = $this->db->getArrayResult();
            return $resultRows;
        }

        public function getStudentsWithGroupOnProcessed($processed) {
            $this->db->setQuery('SELECT u.*, g.number AS group_number 
                                FROM `users` AS u 
                                LEFT JOIN `groups` AS g ON u.group_id = g.group_id 
                                WHERE u.is_admin = :is_admin AND u.processed = :processed');
                                
            $this->db->bindQueryParameter(':is_admin', 0);
            $this->db->bindQueryParameter(':processed', $processed);

            $resultRows = $this->db->getArrayResult();
            return $resultRows;
        }

        public function getUserById($id) {
            $this->db->setQuery('SELECT *
                                FROM `users`
                                WHERE user_id = :user_id');
                                
            $this->db->bindQueryParameter(':user_id', $id);

            $resultRow = $this->db->getSingleResult();
            return $resultRow;
        }

        public function login($data) {
            $this->db->setQuery('SELECT * 
                                FROM `users` 
                                WHERE username = :username');

            $this->db->bindQueryParameter(':username', $data['username']);
            
            $resultRow = $this->db->getSingleResult();
            $hashedPassword = $resultRow->password;
            $is_processed = boolval($resultRow->processed);
            if(password_verify($data['password'], $hashedPassword)) {
                if($is_processed) {
                    return $resultRow;
                } else {
                    return NOT_PROCESSED;
                }
            } else {
                return INCORRECT_CREDENTIALS;
            }
        }

        public function register($data) {
            $this->db->setQuery('INSERT INTO `users` (username, password, name, surname, group_id, is_admin, processed)
                                            VALUES (:username, :password, :name, :surname, :group_id, :is_admin, :processed)');

            $this->db->bindQueryParameter(':username', $data['username']);
            $this->db->bindQueryParameter(':password', $data['password']);
            $this->db->bindQueryParameter(':name', $data['name']);
            $this->db->bindQueryParameter(':surname', $data['surname']);
            $this->db->bindQueryParameter(':group_id', $data['group_id']);
            $this->db->bindQueryParameter(':is_admin', 0);
            $this->db->bindQueryParameter(':processed', 0);

            return $this->db->executeQuery();
        }

        public function update($data) {
            $this->db->setQuery('UPDATE `users`
                                SET username = :username,
                                    name = :name,
                                    surname = :surname,
                                    group_id = :group_id
                                WHERE user_id = :user_id');

            $this->db->bindQueryParameter(':username', $data['username']);
            $this->db->bindQueryParameter(':name', $data['name']);
            $this->db->bindQueryParameter(':surname', $data['surname']);
            $this->db->bindQueryParameter(':group_id', $data['group_id']);
            $this->db->bindQueryParameter(':user_id', $data['id']);

            return $this->db->executeQuery();
        }
        
        public function accept($id) {
            $this->db->setQuery('UPDATE `users`
                                SET processed = :processed
                                WHERE user_id = :user_id');

            $this->db->bindQueryParameter(':processed', 1);
            $this->db->bindQueryParameter(':user_id', $id);

            return $this->db->executeQuery();
        }

        public function makeAdmin($id) {
            $this->db->setQuery('UPDATE `users`
                                SET group_id = :group_id,
                                    is_admin = :is_admin
                                WHERE user_id = :user_id');

            $this->db->bindQueryParameter(':group_id', NULL);
            $this->db->bindQueryParameter(':is_admin', 1);
            $this->db->bindQueryParameter(':user_id', $id);

            return $this->db->executeQuery();
        }

        public function delete($id) {
            $this->db->setQuery('DELETE FROM `users`
                                WHERE user_id = :user_id');

            $this->db->bindQueryParameter(':user_id', $id);

            return $this->db->executeQuery();
        }
    }
