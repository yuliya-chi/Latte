<?php
    trait Tasks {
        private $taskModel;

        public function lab_data($lab_id) {
            $lab = $this->laboratoryWorkModel->getLaboratoryWorkById($lab_id);
            $tasks = $this->taskModel->getTasksByLab($lab_id);
            $data = [
                'lab_id' => $lab_id,
                'lab_title' => $lab->title_en,
                'lab' => $lab,
                'tasks' => $tasks
            ];

            if($_SERVER['REQUEST_METHOD'] == 'POST') {
                $_POST = filter_input_array(INPUT_POST, FILTER_SANITIZE_STRING);
            
                $data = [
                    'lab_id' => $lab_id,
                    'titleEN' => trim($_POST['titleEN']),
                    'titleUA' => trim($_POST['titleUA']),
                    'startDate' => date("Y-m-d", strtotime(trim($_POST['startDate']))),
                    'endDate' => date("Y-m-d", strtotime(trim($_POST['endDate']))),
                    'maxMark' => trim((string)$_POST['maxMark']),
                    'lab_title' => $lab->title_en,
                    'tasks' => $tasks,
                    'titleENError' => '',
                    'titleUAError' => '',
                    'startDateError' => '',
                    'endDateError' => '',
                    'maxMarkError' => ''
                ];

                $markRule = "/^[0-9.]{1,5}$/";

                if(!$data['titleEN']) {
                    $data['titleENError'] = 'Enter the title (EN), please.';
                }

                if(!$data['titleUA']) {
                    $data['titleUAError'] = 'Enter the title (UA), please.';
                }

                if($data['startDate'] == '1970-01-01') {
                    $data['startDateError'] = 'Enter the start date, please.';
                }

                if($data['endDate'] == '1970-01-01') {
                    $data['endDateError'] = 'Enter the end date, please.';
                } elseif($data['startDate'] > $data['endDate']) {
                    $data['endDateError'] = 'End date should be lower than start date.';
                }

                if($data['maxMark'] === '') {
                    $data['maxMarkError'] = 'Enter the max mark value, please.';
                 }elseif(!preg_match($markRule, $data['maxMark'])) {
                    $data['maxMarkError'] = 'Max mark must be an integer or floating point number.';
                }

                if(!$data['titleENError'] && !$data['titleUAError'] &&
                    !$data['startDateError'] && !$data['endDateError'] &&
                    !$data['maxMarkError']) {
                    if($this->laboratoryWorkModel->update(array_slice($data, 0, 6))) {
                        header('location:' . URLROOT . '/admin_pages/labs_tasks/laboratory_works');
                    } else {
                        die("Something went wrong...");
                    }
                }
            }

            $this->view('admin_pages/labs_tasks/lab_data', $data);
        }

        public function create_task($lab_id) {
            $lab = $this->laboratoryWorkModel->getLaboratoryWorkById($lab_id);

            $data = [
                'number' => '',
                'exerciseEN' => '',
                'exerciseUA' => '',
                'difficulty' => '',
                'attempts' => '',
                'lab_id' => $lab_id,
                'lab_title' => $lab->title_en,
                'numberError' => '',
                'exerciseENError' => '',
                'exerciseUAError' => '',
                'difficultyError' => '',
                'attemptsError' => ''
            ];

            if($_SERVER['REQUEST_METHOD'] == 'POST') {
                $_POST = filter_input_array(INPUT_POST, FILTER_SANITIZE_STRING);

                $data = [
                    'number' => trim($_POST['number']),
                    'exerciseEN' => trim($_POST['exerciseEN']),
                    'exerciseUA' => trim($_POST['exerciseUA']),
                    'difficulty' => trim($_POST['difficulty']),
                    'attempts' => trim($_POST['attempts']),
                    'lab_id' => $lab_id,
                    'lab_title' => $lab->title_en,
                    'numberError' => '',
                    'exerciseENError' => '',
                    'exerciseUAError' => '',
                    'difficultyError' => '',
                    'attemptsError' => ''
                ];

                $numberRule = "/^[0-9.]{1,20}$/";
                $difficultyAttemptsRule = "/^[1-9]{1}$/u";

                if(!$data['number']) {
                    $data['numberError'] = 'Enter the number, please.';
                } elseif(!preg_match($numberRule, $data['number'])) {
                    $data['numberError'] = 'Number must consist of numbers (integer/floating point).';
                }

                if(!$data['exerciseEN']) {
                    $data['exerciseENError'] = 'Enter the exerise (EN), please.';
                }

                if(!$data['exerciseUA']) {
                    $data['exerciseUAError'] = 'Enter the exerise (UA), please.';
                }

                if(!$data['difficulty']) {
                    $data['difficultyError'] = 'Enter the difficulty, please.';
                } elseif(!preg_match($difficultyAttemptsRule, $data['difficulty'])) {
                    $data['difficultyError'] = 'Difficulty value must be a not null digit (1-9).';
                }

                if(!$data['attempts']) {
                    $data['attemptsError'] = 'Enter the attempts, please.';
                } elseif(!preg_match($difficultyAttemptsRule, $data['attempts'])) {
                    $data['attemptsError'] = 'Attempts value must be a not null digit (1-9).';
                }
                
                if(!$data['numberError'] && !$data['exerciseENError'] && 
                    !$data['exerciseUAError'] && !$data['difficultyError'] &&
                    !$data['attemptsError']) {
                    if($this->taskModel->insert(array_slice($data, 0, 6))) {
                        header('location:' . URLROOT . '/admin_pages/labs_tasks/lab_data/' . $lab_id);
                    } else {
                        die('Something went wrong.');
                    }
                }
            }

            $this->view('admin_pages/labs_tasks/create_task', $data);
        }

        public function update_task($lab_id, $task_id) {
            $task = $this->taskModel->getTaskById($task_id);

            $data = [
                'task_id' => $task_id,
                'task_number' => $task->number,
                'task' => $task,
                'lab_id' => $lab_id
            ];

            if($_SERVER['REQUEST_METHOD'] == 'POST') {
                $_POST = filter_input_array(INPUT_POST, FILTER_SANITIZE_STRING);

                $data = [
                    'task_id' => $task_id,
                    'number' => trim($_POST['number']),
                    'exerciseEN' => trim($_POST['exerciseEN']),
                    'exerciseUA' => trim($_POST['exerciseUA']),
                    'difficulty' => trim($_POST['difficulty']),
                    'attempts' => trim($_POST['attempts']),
                    'task_number' => $task->number,
                    'lab_id' => $lab_id,
                    'numberError' => '',
                    'exerciseENError' => '',
                    'exerciseUAError' => '',
                    'difficultyError' => '',
                    'attemptsError' => ''
                ];

                $numberRule = "/^[0-9.]{1,20}$/";
                $difficultyAttemptsRule = "/^[1-9]{1}$/u";

                if(!$data['number']) {
                    $data['numberError'] = 'Enter the number, please.';
                } elseif(!preg_match($numberRule, $data['number'])) {
                    $data['numberError'] = 'Number must consist of numbers (integer/floating point).';
                }

                if(!$data['exerciseEN']) {
                    $data['exerciseENError'] = 'Enter the exerise (EN), please.';
                }

                if(!$data['exerciseUA']) {
                    $data['exerciseUAError'] = 'Enter the exerise (UA), please.';
                }

                if(!$data['difficulty']) {
                    $data['difficultyError'] = 'Enter the difficulty, please.';
                } elseif(!preg_match($difficultyAttemptsRule, $data['difficulty'])) {
                    $data['difficultyError'] = 'Difficulty value must be a not null digit (1-9).';
                }

                if(!$data['attempts']) {
                    $data['attemptsError'] = 'Enter the attempts, please.';
                } elseif(!preg_match($difficultyAttemptsRule, $data['attempts'])) {
                    $data['attemptsError'] = 'Attempts value must be a not null digit (1-9).';
                }
                
                if(!$data['numberError'] && !$data['exerciseENError'] && 
                    !$data['exerciseUAError'] && !$data['difficultyError'] &&
                    !$data['attemptsError']) {
                    if($this->taskModel->update(array_slice($data, 0, 6))) {
                        header('location:' . URLROOT . '/admin_pages/labs_tasks/lab_data/' . $lab_id);
                    } else {
                        die('Something went wrong.');
                    }
                }
            }

            $this->view('admin_pages/labs_tasks/update_task', $data);
        }

        public function delete_task($lab_id, $task_id) {
            if($_SERVER['REQUEST_METHOD'] == 'POST') {
                if($this->taskModel->delete($task_id)) {
                    header('location:' . URLROOT . '/admin_pages/labs_tasks/lab_data/' . $lab_id);
                }
                else {
                    die("Something went wrong...");
                }    
            }
        }
    }
