<?php
    class Solution {
        private $db;

        public function __construct() {
            $this->db = new Database;  
        }
        
        public function getNewStudentsSolutions() {
            $this->db->setQuery('SELECT ss.s_solution_id AS s_solution_id, ss.answer AS answer, ss.mark AS mark, ss.comment AS comment, t.number AS task_number,
                                        t.difficulty AS difficulty, lw.title_en AS lab_title, u.name AS name, u.surname AS surname, g.number AS group_number
                                FROM `student_solutions` AS ss
                                LEFT JOIN `tasks` AS t ON ss.task_id = t.task_id
                                LEFT JOIN `laboratory_works` AS lw ON ss.lab_id = lw.lab_id
                                LEFT JOIN `users` AS u ON ss.user_id = u.user_id
                                LEFT JOIN `groups` AS g ON u.group_id = g.group_id
                                WHERE ss.confirmed = :confirmed
                                ORDER BY answer_date ASC');

            $this->db->bindQueryParameter(':confirmed', 0);

            $resultRows = $this->db->getArrayResult();
            return $resultRows;
        }

        public function getNewStudentsSolutionById($id) {
            $this->db->setQuery('SELECT ss.s_solution_id AS s_solution_id, ss.answer AS answer, ss.answer_date AS answer_date, ss.mark AS mark,
                                        ss.comment AS comment, t.number AS task_number, t.exercise_en AS exercise, t.difficulty AS difficulty, 
                                        lw.title_en AS lab_title, u.name AS name, u.surname AS surname, g.number AS group_number
                                FROM `student_solutions` AS ss
                                LEFT JOIN `tasks` AS t ON ss.task_id = t.task_id
                                LEFT JOIN `laboratory_works` AS lw ON ss.lab_id = lw.lab_id
                                LEFT JOIN `users` AS u ON ss.user_id = u.user_id
                                LEFT JOIN `groups` AS g ON u.group_id = g.group_id
                                WHERE ss.s_solution_id = :solution_id AND ss.confirmed = :confirmed');

            $this->db->bindQueryParameter(':solution_id', $id);
            $this->db->bindQueryParameter(':confirmed', 0);

            $resultRow = $this->db->getSingleResult();
            return $resultRow;
        }
        
        public function getCheckedStudentsSolutions() {
            $this->db->setQuery('SELECT ss.answer AS answer, ss.mark AS mark, ss.comment AS comment, t.number AS task_number, t.difficulty AS difficulty, 
                                        lw.title_en AS lab_title, u.name AS name, u.surname AS surname, g.number AS group_number
                                FROM `student_solutions` AS ss
                                LEFT JOIN `tasks` AS t ON ss.task_id = t.task_id
                                LEFT JOIN `laboratory_works` AS lw ON ss.lab_id = lw.lab_id
                                LEFT JOIN `users` AS u ON ss.user_id = u.user_id
                                LEFT JOIN `groups` AS g ON u.group_id = g.group_id
                                WHERE ss.confirmed = :confirmed
                                ORDER BY answer_date ASC');

            $this->db->bindQueryParameter(':confirmed', 1);

            $resultRows = $this->db->getArrayResult();
            return $resultRows;
        }

        public function getCorrectSolutions() {
            $this->db->setQuery('SELECT ts.*, t.number AS task_number, t.exercise_en AS task_content, lw.title_en AS lab_title
                                FROM `teacher_solutions` AS ts
                                LEFT JOIN `tasks` AS t ON ts.task_id = t.task_id
                                LEFT JOIN `laboratory_works` AS lw ON ts.lab_id = lw.lab_id');

            $resultRows = $this->db->getArrayResult();
            return $resultRows;
        }

        public function getCorrectSolutionByTaskId($task_id) {
            $this->db->setQuery('SELECT *
                                FROM `teacher_solutions`
                                WHERE task_id = :task_id');

            $this->db->bindQueryParameter(':task_id', $task_id);

            $resultRow = $this->db->getSingleResult();
            return $resultRow;
        }

        public function getStudentAttemptSolutions($task_id, $user_id) {
            $this->db->setQuery('SELECT ss.answer AS answer, ss.answer_date AS answer_date, ss.mark AS mark,
                                        ss.comment AS comment, ss.confirmed AS confirmed
                                FROM `student_solutions` AS ss
                                LEFT JOIN `tasks` AS t ON ss.task_id = t.task_id
                                LEFT JOIN `users` AS u ON ss.user_id = u.user_id
                                WHERE t.task_id = :task_id AND ss.user_id = :user_id
                                ORDER BY answer_date ASC');

            $this->db->bindQueryParameter(':task_id', $task_id);
            $this->db->bindQueryParameter(':user_id', $user_id);
            
            $resultRows = $this->db->getArrayResult();
            return $resultRows;
        }

        public function getStudentAttemptsOnTaskCount($task_id, $user_id) {
            $this->db->setQuery('SELECT COUNT(*) AS solutions_count
                                FROM `student_solutions` AS ss
                                LEFT JOIN `tasks` AS t ON ss.task_id = t.task_id
                                WHERE t.task_id = :task_id AND ss.user_id = :user_id');

            $this->db->bindQueryParameter(':task_id', $task_id);
            $this->db->bindQueryParameter(':user_id', $user_id);

            $rowsCount = $this->db->getSingleResult();
            return $rowsCount;
        }

        public function getTasksAttemptsLeft($lab_id, $user_id) {
            $this->db->setQuery('SELECT t.attempts - COUNT(ss.task_id) AS attempts_left
                                FROM `student_solutions` AS ss
                                RIGHT JOIN `tasks` AS t ON ss.task_id = t.task_id
                                WHERE t.lab_id = :lab_id AND (ss.user_id = :user_id OR ss.user_id IS NULL)
                                GROUP BY t.task_id
                                ORDER BY t.task_id');
                    
            $this->db->bindQueryParameter(':lab_id', $lab_id);
            $this->db->bindQueryParameter(':user_id', $user_id);

            $resultRows = $this->db->getArrayResult();
            return $resultRows;
        }

        public function getTasksBestScores($lab_id, $user_id) {
            $this->db->setQuery('SELECT COALESCE(MAX(ss.mark), "0") AS best_score
                                FROM `student_solutions` AS ss
                                RIGHT JOIN `tasks` AS t ON ss.task_id = t.task_id
                                WHERE t.lab_id = :lab_id AND (ss.user_id = :user_id OR ss.user_id IS NULL)
                                GROUP BY t.task_id
                                ORDER BY t.task_id');
                    
            $this->db->bindQueryParameter(':lab_id', $lab_id);
            $this->db->bindQueryParameter(':user_id', $user_id);

            $resultRows = $this->db->getArrayResult();
            return $resultRows;
        } 

        public function getLabsCurrentMarks($user_id) {
            $this->db->setQuery('SELECT COALESCE(ROUND(max_mark * (ss.sum_marks/t.sum_difficulty), 1), 0) AS current_mark
                                FROM `laboratory_works` AS lw
                                LEFT JOIN (
                                    SELECT lab_id, SUM(difficulty) AS sum_difficulty
                                    FROM `tasks`
                                    GROUP BY lab_id
                                ) AS t ON lw.lab_id = t.lab_id
                                LEFT JOIN (
                                    SELECT lab_id, SUM(ROUND(mark, 2)) AS sum_marks
                                    FROM `student_solutions`
                                    WHERE confirmed = :confirmed AND user_id = :user_id
                                    GROUP BY lab_id
                                ) AS ss ON lw.lab_id = ss.lab_id
                                ORDER BY lw.lab_id');

            $this->db->bindQueryParameter(':user_id', $user_id);
            $this->db->bindQueryParameter(':confirmed', 1);

            $resultRows = $this->db->getArrayResult();
            return $resultRows;
        }

        public function insert($data) {
            $this->db->setQuery('INSERT INTO `student_solutions` (answer, answer_date, user_id, lab_id, task_id, mark, comment, confirmed)
                                            VALUES (:answer, :answer_date, :user_id, :lab_id, :task_id, :mark, :comment, :confirmed)');

            $this->db->bindQueryParameter(':answer', $data['answer']);
            $this->db->bindQueryParameter(':answer_date', date('Y-m-d H:i:s'));
            $this->db->bindQueryParameter(':user_id', $data['user_id']);
            $this->db->bindQueryParameter(':lab_id', $data['lab_id']);
            $this->db->bindQueryParameter(':task_id', $data['task_id']);
            $this->db->bindQueryParameter(':mark', floatval($data['mark']));
            $this->db->bindQueryParameter(':comment', $data['comment']);
            $this->db->bindQueryParameter(':confirmed', 0);

            return $this->db->executeQuery();
        }

        public function confirm($id) {
            $this->db->setQuery('UPDATE `student_solutions`
                                SET confirmed = :confirmed
                                WHERE s_solution_id = :s_solution_id');

            $this->db->bindQueryParameter(':confirmed', 1);
            $this->db->bindQueryParameter(':s_solution_id', $id);

            return $this->db->executeQuery();
        }

        public function saveConfirm($data) {
            $this->db->setQuery('UPDATE `student_solutions`
                                SET comment = :comment,
                                    mark = :mark,
                                    confirmed = :confirmed
                                WHERE s_solution_id = :s_solution_id');

            $this->db->bindQueryParameter(':comment', $data['comment']);
            $this->db->bindQueryParameter(':mark', floatval($data['mark']));
            $this->db->bindQueryParameter(':confirmed', 1);
            $this->db->bindQueryParameter(':s_solution_id', $data['id']);

            return $this->db->executeQuery();
        }

        public function delete($id) {
            $this->db->setQuery('DELETE
                                FROM `student_solutions`
                                WHERE s_solution_id = :s_solution_id');

            $this->db->bindQueryParameter(':s_solution_id', $id);

            return $this->db->executeQuery();
        }
    }
