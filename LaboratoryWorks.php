<?php
    trait LaboratoryWorks {
        private $laboratoryWorkModel;

        public function laboratory_works() {
            $labs = $this->laboratoryWorkModel->getLaboratoryWorks();
            $data = [
                'labs' => $labs
            ];

            $this->view('admin_pages/labs_tasks/laboratory_works', $data);
        }

        public function create_lab() {
            $data = [
                'tiileEN' => '',
                'titleUA' => '',
                'startDate' => '',
                'endDate' => '',
                'maxMark' => '',
                'tiileENError' => '',
                'titleUAError' => '',
                'startDateError' => '',
                'endDateError' => '',
                'maxMarkError' => ''
            ];

            if($_SERVER['REQUEST_METHOD'] == 'POST') {
                $_POST = filter_input_array(INPUT_POST, FILTER_SANITIZE_STRING);
            
                $data = [
                    'titleEN' => trim($_POST['titleEN']),
                    'titleUA' => trim($_POST['titleUA']),
                    'startDate' => date("Y-m-d", strtotime(trim($_POST['startDate']))),
                    'endDate' => date("Y-m-d", strtotime(trim($_POST['endDate']))),
                    'maxMark' => trim((string)$_POST['maxMark']),
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
                    if($this->laboratoryWorkModel->insert($data)) {
                        header('location:' . URLROOT . '/admin_pages/labs_tasks/laboratory_works');
                    } else {
                        die("Something went wrong...");
                    }
                }
            }

            $this->view('admin_pages/labs_tasks/create_lab', $data);
        }

        public function delete_lab($lab_id) {
            if($_SERVER['REQUEST_METHOD'] == 'POST') {
                if($this->laboratoryWorkModel->delete($lab_id)) {
                    header('location:' . URLROOT . '/admin_pages/labs_tasks/laboratory_works');
                }
                else {
                    die("Something went wrong...");
                }    
            }
        }
    }
