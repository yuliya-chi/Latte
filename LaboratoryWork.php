<?php
    class LaboratoryWork {
        private $db;

        public function __construct() {
            $this->db = new Database;  
        }

        public function getLaboratoryWorks() {
            $this->db->setQuery('SELECT * 
                                FROM `laboratory_works`
                                ORDER BY lab_id');

            $resultRows = $this->db->getArrayResult();
            return $resultRows;
        }

        public function getLaboratoryWorkById($lab_id) {
            $this->db->setQuery('SELECT * 
                                FROM `laboratory_works`
                                WHERE lab_id = :lab_id');

            $this->db->bindQueryParameter(':lab_id', $lab_id);

            $resultRow = $this->db->getSingleResult();
            return $resultRow;
        }

        public function getLaboratoryWorksByYear($year) {
            $this->db->setQuery('SELECT * 
                                FROM `laboratory_works`
                                WHERE YEAR(end_date) = :year
                                ORDER BY end_date');

            $this->db->bindQueryParameter(':year', $year);

            $resultRow = $this->db->getSingleResult();
            return $resultRow;
        }

        public function insert($data) {
            $this->db->setQuery('INSERT INTO `laboratory_works` (title_en, title_ua, start_date, end_date, max_mark)
                                            VALUES (:title_en, :title_ua, :start_date, :end_date, :max_mark)');

            $this->db->bindQueryParameter(':title_en', $data['titleEN']);
            $this->db->bindQueryParameter(':title_ua', $data['titleUA']);
            $this->db->bindQueryParameter(':start_date', $data['startDate']);
            $this->db->bindQueryParameter(':end_date', $data['endDate']);
            $this->db->bindQueryParameter(':max_mark', floatval($data['maxMark']));

            return $this->db->executeQuery();
        }

        public function update($data) {
            $this->db->setQuery('UPDATE `laboratory_works`
                                SET title_en = :title_en,
                                    title_ua = :title_ua,
                                    start_date = :start_date,
                                    end_date = :end_date,
                                    max_mark = :max_mark
                                WHERE lab_id = :lab_id');

            $this->db->bindQueryParameter(':title_en', $data['titleEN']);
            $this->db->bindQueryParameter(':title_ua', $data['titleUA']);
            $this->db->bindQueryParameter(':start_date', $data['startDate']);
            $this->db->bindQueryParameter(':end_date', $data['endDate']);
            $this->db->bindQueryParameter(':max_mark', floatval($data['maxMark']));
            $this->db->bindQueryParameter(':lab_id', $data['lab_id']);

            return $this->db->executeQuery();
        }

        public function delete($id) {
            $this->db->setQuery('DELETE
                                FROM `laboratory_works`
                                WHERE lab_id = :lab_id');

            $this->db->bindQueryParameter(':lab_id', $id);

            return $this->db->executeQuery();
        }
    }
