<?php
    require(APPROOT . '/libraries/Excel/PHPExcel.php');

    trait Solutions {
        private $solutionsModel;

        public function new_solutions() {
            $solutions = $this->solutionsModel->getNewStudentsSolutions();
            $groups = $this->groupModel->getAllGroups();
            $data = [
                'solutions' => $solutions,
                'groups' => $groups
            ];

            $this->view('admin_pages/solutions/new_solutions', $data);
        }

        public function new_solution($solution_id) {
            $solution = $this->solutionsModel->getNewStudentsSolutionById($solution_id);
            $data = [
                'id' => $solution_id,
                'solution' => $solution,
                'markError' => ''
            ];

            if($_SERVER['REQUEST_METHOD'] == 'POST') {
                $_POST = filter_input_array(INPUT_POST, FILTER_SANITIZE_STRING);
                
                $data = [
                    'id' => $solution_id,
                    'mark' => trim((string)$_POST['mark']),
                    'comment' => trim($_POST['comment']),
                    'solution' => $solution,
                    'markError' => ''
                ];

                $markRule = "/^[0-9.]{1,5}$/";

                if($data['mark'] === '') {
                    $data['markError'] = 'Enter the mark value, please.';
                } elseif(!preg_match($markRule, $data['mark'])) {
                    $data['markError'] = 'Mark must be a positive integer or floating point number.';
                } elseif(floatval($data['mark']) > $data['solution']->difficulty) {
                    $data['markError'] = 'Mark can not be bigger then maximum value.';
                }

                if(!$data['markError']) {
                    if($this->solutionsModel->saveConfirm(array_slice($data, 0, 3))) {
                        header('location:' . URLROOT . '/admin_pages/solutions/new_solutions');
                    } else {
                        die("Something went wrong...");
                    }
                }
            }

            $this->view('admin_pages/solutions/new_solution', $data);
        }

        public function checked_solutions() {
            $solutions = $this->solutionsModel->getCheckedStudentsSolutions();
            $groups = $this->groupModel->getAllGroups();
            $data = [
                'solutions' => $solutions,
                'groups' => $groups
            ];
            $this->view('admin_pages/solutions/checked_solutions', $data);
        }

        public function correct_solutions() {
            $solutions = $this->solutionsModel->getCorrectSolutions();
            $data = [
                'solutions' => $solutions
            ];

            $this->view('admin_pages/solutions/correct_solutions', $data);
        }

        public function confirmNewAnswers() {
            if($_SERVER['REQUEST_METHOD'] == 'POST') {
                if($_POST['selectedIds'] != '[]') {
                    $selectedIds = json_decode($_POST['selectedIds']);
                    foreach($selectedIds as $id) {
                        $this->solutionsModel->confirm($id);
                    }
                }
            }

            header('location:' . URLROOT . '/admin_pages/solutions/new_solutions');
        }

        public function disproveNewAnswers() {
            if($_SERVER['REQUEST_METHOD'] == 'POST') {
                if($_POST['selectedIds'] != '[]') {
                    $selectedIds = json_decode($_POST['selectedIds']);
                    foreach($selectedIds as $id) {
                        $this->solutionsModel->disprove($id);
                    }
                }
            }
            
            header('location:' . URLROOT . '/admin_pages/solutions/new_solutions');
        }

        public function disproveSolution($id) {
            if($_SERVER['REQUEST_METHOD'] == 'POST') {
                if($this->solutionsModel->delete($id)) {
                    header('location:' . URLROOT . '/admin_pages/solutions/new_solutions');
                }
                else {
                    die("Something went wrong...");
                }    
            }
        }

        public function excel_export() {
            if($_SERVER['REQUEST_METHOD'] == 'POST') {
                $_POST = filter_input_array(INPUT_POST, FILTER_SANITIZE_STRING);
                
                $data = [
                    'year' => trim($_POST['year'])
                ];

                $labsByYear = $this->laboratoryWorkModel->getLaboratoryWorksByYear($data['year']);
                $groupsWithSolutionByYear = $this->groupModel->getGroupsWithSolutionsByLabYear($data['year']);

                $phpExcel = new PHPExcel;
                $currentSheetIndex = 0;
                
                foreach($groupsWithSolutionByYear as $group) {
                    $newSheet = $phpExcel->createSheet();

                    $group_id = $group->group_id;
                    $group_number = $group->number;
                    $currentSheet = $phpExcel->setActiveSheetIndex($currentSheetIndex)->setTitle($group_number);
                    $startRowIndex = 1;
                    $startColumnIndex = 0;
                    $columnsNames = ['№', 'Initials and Surname of a Student'];
                    $currentColumnIndex = $startColumnIndex;

                    foreach($columnsNames as $columnName) {
                        $currentSheet->setCellValueByColumnAndRow($currentColumnIndex, $startRowIndex, $columnName);
                        $currentSheet->getStyleByColumnAndRow($currentColumnIndex, $startRowIndex)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
                        $currentColumnIndex++;
                    }

                    $currentSheet->getColumnDimension('A')->setAutoSize(false);
                    $currentSheet->getColumnDimension('A')->setWidth('5');
                    $currentSheet->getColumnDimension('B')->setAutoSize(true);

                    $labsArray = [];
                    $labIndex = 0;
                    foreach($labsByYear as $lab) {
                        $lab_id = $lab->lab_id;
                        $lab_title = $lab->title_en;
                        $currentSheet->setCellValueByColumnAndRow($currentColumnIndex, $startRowIndex, $lab_title);
                        $columnName = $currentSheet->getCellByColumnAndRow($currentColumnIndex, $startRowIndex)->getColumn();
                        //$rowName = $currentSheet->getCellByColumnAndRow($currentColumnIndex, $startRowIndex)->getRow();
                        $currentSheet->getStyleByColumnAndRow($currentColumnIndex, $startRowIndex)->getAlignment()->setWrapText(true);
                        $currentSheet->getColumnDimension($columnName)->setAutoSize(true);

                        $labsArray[$labIndex] = ["id" => $lab_id, "title" => $lab_title, "currentColumnIndex" => $currentColumnIndex];
                        $labIndex++;
                        $currentColumnIndex++;
                    }

                    $currentSheetIndex++;
                }

                header('Content-Type: application/vnd.ms-excel');
                header('Content-Disposition: attachment; filename="' . $data['year'] . '.xls"');
                header('Cache-Control: max-age=0');
                $excelWriter = PHPExcel_IOFactory::createWriter($phpExcel, 'Excel5');
                $excelWriter->save('php://output');
            }
        }
    }
