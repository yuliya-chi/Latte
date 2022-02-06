<?php
    require('LaboratoryWorks.php');
    require('Tasks.php');
    require('Users.php');
    require('Groups.php');
    require('Solutions.php');
    require('DatabaseStructures.php');

    class Admin_Pages extends Controller {
        use LaboratoryWorks;
        use Tasks;
        use Users;
        use Groups;
        use Solutions;
        use DatabaseStructures;

        public function __construct() {
            $this->redirectOnNoAcces();

            $this->laboratoryWorkModel = $this->model('LaboratoryWork');
            $this->taskModel = $this->model('Task');
            $this->userModel = $this->model('User');
            $this->groupModel = $this->model('Group');
            $this->solutionsModel = $this->model('Solution');
            $this->databaseStructureModel = $this->model('DatabaseStructure');
        }

        private function redirectOnNoAcces() {
            if(!checkIsAdminLoggeIn()) {
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

            $this->view('admin_pages/database_structure', $data);
        }
    } 
