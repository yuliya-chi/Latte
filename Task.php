<?php
    class Task {
        private $db;

        public function __construct() {
            $this->db = new Database;  
        }

        public function getTaskById($id) {
            $this->db->setQuery('SELECT *
                                FROM `tasks`
                                WHERE task_id = :task_id');

            $this->db->bindQueryParameter(':task_id', $id);

            $resultRow = $this->db->getSingleResult();
            return $resultRow;
        }

        public function getTasksByLab($lab_id) {
            $this->db->setQuery('SELECT *
                                FROM `tasks`
                                WHERE lab_id = :lab_id
                                ORDER BY task_id');

            $this->db->bindQueryParameter(':lab_id', $lab_id);

            $resultRows = $this->db->getArrayResult();
            return $resultRows;
        }

        public function getNextAndPreviousTasksIds($lab_id, $task_id) {
            $this->db->setQuery('SELECT COALESCE((SELECT task_id 
                                                    FROM `tasks` 
                                                    WHERE lab_id = :lab_id AND task_id < :task_id 
                                                    ORDER BY task_id DESC 
                                                    LIMIT 1), 0) AS nptask_id
                                UNION
                                SELECT COALESCE((SELECT task_id 
                                                    FROM `tasks` 
                                                    WHERE lab_id = :lab_id AND task_id > :task_id 
                                                    ORDER BY task_id 
                                                    LIMIT 1), 0) AS nptask_id');

            $this->db->bindQueryParameter(':lab_id', $lab_id);
            $this->db->bindQueryParameter(':task_id', $task_id);

            $resultRows = $this->db->getArrayResult();
            return $resultRows;
        }

        public function insert($data) {
            $this->db->setQuery('INSERT INTO `tasks` (number, exercise_en, exercise_ua, difficulty, attempts, lab_id)
                                            VALUES (:number, :exercise_en, :exercise_ua, :difficulty, :attempts, :lab_id)');

            $this->db->bindQueryParameter(':number', $data['number']);
            $this->db->bindQueryParameter(':exercise_en', $data['exerciseEN']);
            $this->db->bindQueryParameter(':exercise_ua', $data['exerciseUA']);
            $this->db->bindQueryParameter(':difficulty', $data['difficulty']);
            $this->db->bindQueryParameter(':attempts', $data['attempts']);
            $this->db->bindQueryParameter(':lab_id', $data['lab_id']);

            return $this->db->executeQuery();
        }

        public function update($data) {
            $this->db->setQuery('UPDATE `tasks`
                                SET number = :number,
                                    exercise_en = :exercise_en,
                                    exercise_ua = :exercise_ua,
                                    difficulty = :difficulty,
                                    attempts = :attempts
                                WHERE task_id = :task_id');

            $this->db->bindQueryParameter(':number', $data['number']);
            $this->db->bindQueryParameter(':exercise_en', $data['exerciseEN']);
            $this->db->bindQueryParameter(':exercise_ua', $data['exerciseUA']);
            $this->db->bindQueryParameter(':difficulty', $data['difficulty']);
            $this->db->bindQueryParameter(':attempts', $data['attempts']);
            $this->db->bindQueryParameter(':task_id', $data['task_id']);

            return $this->db->executeQuery();
        }

        public function delete($id) {
            $this->db->setQuery('DELETE
                                FROM `tasks`
                                WHERE task_id = :task_id');

            $this->db->bindQueryParameter(':task_id', $id);

            return $this->db->executeQuery();
        }
    }
