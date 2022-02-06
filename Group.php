<?php
    class Group {
        private $db;

        public function __construct() {
            $this->db = new Database;  
        }

        public function getAllGroups() {
            $this->db->setQuery('SELECT * FROM `groups`');

            $resultRows = $this->db->getArrayResult();
            return $resultRows;
        }

        public function getGroupById($id) {
            $this->db->setQuery('SELECT * 
                                FROM `groups` 
                                WHERE group_id = :group_id');

            $this->db->bindQueryParameter(':group_id', $id);

            $resultRow = $this->db->getSingleResult();
            return $resultRow;
        }

        public function getGroupByNumber($number) {
            $this->db->setQuery('SELECT * 
                                FROM `groups` 
                                WHERE number = :number');
            
            $this->db->bindQueryParameter(':number', $number);

            $resultRow = $this->db->getSingleResult();
            return $resultRow;
        }

        public function getGroupsWithSolutionsByLabYear($year) {
            $this->db->setQuery('SELECT g.*
                                FROM `groups` AS g
                                JOIN `users` AS u ON g.group_id = u.group_id
                                JOIN `student_solutions` AS ss ON u.user_id = ss.user_id
                                JOIN `laboratory_works` AS lw ON ss.lab_id = lw.lab_id AND YEAR(lw.end_date) = :year
                                GROUP BY g.group_id');

            $this->db->bindQueryParameter(':year', $year);

            $resultRows = $this->db->getArrayResult();
            return $resultRows;
        }
    }
