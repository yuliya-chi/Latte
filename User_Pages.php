<?php
    require('LaboratoryWorks_Student.php');
    require('Tasks_Student.php');
    require('Results.php');
    require('DatabaseStructures_Student.php');

    class User_Pages extends Controller {
        use LaboratoryWorks_Student;
        use Tasks_Student;
        use Results;
        use DatabaseStructures_Student;

        public function __construct() {
            $this->redirectOnNoAcces();

            $this->laboratoryWorkModel = $this->model('LaboratoryWork');
            $this->taskModel = $this->model('Task');
            $this->solutionsModel = $this->model('Solution');
            $this->databaseStructureModel = $this->model('DatabaseStructure');
            $this->taskCheckModel = $this->model('TaskCheck');
        }

        private function redirectOnNoAcces() {
            if(!checkIsUserLoggeIn()) {
                exit(header('location:' . URLROOT . '/users_auth/login'));
            }
        }

        public function database_structure() {
            $titles = $this->databaseStructureModel->getTablesTitles();
            $descriptions = $this->databaseStructureModel->getTablesDescriptions();
            $images = $this->databaseStructureModel->getableStructureImagesSrc();

            $data = [
                'titles' => $titles,
                'descriptions' => $descriptions,
                'images' => $images
            ];
            
            $this->view('user_pages/database_structure', $data);
        }
    }
