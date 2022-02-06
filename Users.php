<?php
    trait Users {
        private $userModel;

        public function new_users() {
            $users = $this->userModel->getStudentsWithGroupOnProcessed(0);
            $groups = $this->groupModel->getAllGroups();
            $data = [
                'users' => $users,
                'groups' => $groups
            ];

            $this->view('admin_pages/users/new_users', $data);
        }

        public function current_users() {
            $admins = $this->userModel->getAdmins();
            $students = $this->userModel->getStudentsWithGroupOnProcessed(1);
            $groups = $this->groupModel->getAllGroups();
            $data = [
                'admins' => $admins,
                'students' => $students,
                'groups' => $groups
            ];

            $this->view('admin_pages/users/current_users', $data);
        }

        public function update_user($id) {
            $user = $this->userModel->getUserById($id);
            $group = $this->groupModel->getGroupById($user->group_id);
            $groups = $this->groupModel->getAllGroups();

            $data = [
                'id' => $id,
                'user' => $user,
                'group' => $group, 
                'groups' => $groups
            ];

            if($_SERVER['REQUEST_METHOD'] == 'POST') {
                $_POST = filter_input_array(INPUT_POST, FILTER_SANITIZE_STRING);
                $group = $this->groupModel->getGroupByNumber(trim($_POST['group']));

                $data = [
                    'id' => $id,
                    'username' => trim($_POST['username']),
                    'name' => trim($_POST['name']),
                    'surname' => trim($_POST['surname']),
                    'group_id' => $group->group_id,
                    'groups' => $groups,
                    'usernameError' => '',
                    'nameError' => '',
                    'surnameError' => '',
                ];

                $usernameRule = "/^[a-zA-Z0-9_@.]{3,32}$/";
                $nameRule = "/^[a-zA-Zа-яА-ЯёЁґҐіІїЇєЄ]*$/u";

                if(!$data['username']) {
                    $data['usernameError'] = 'Enter the username, please.';
                } elseif(!preg_match($usernameRule, $data['username'])) {
                    $data['usernameError'] = 'Username must be at least 3 and no longer than 32 characters,'.
                    ' may contain letters, numbers, underscore, at sign and dot.';
                }

                if(!$data['name']) {
                    $data['nameError'] = 'Enter the name, please.';
                } elseif(!preg_match($nameRule, $data['name'])) {
                    $data['name'] = 'Name can only contain letters (English/Russian/Ukrainian).';
                }

                if(!$data['surname']) {
                    $data['surnameError'] = 'Enter the surname, please.';
                } elseif(!preg_match($nameRule, $data['surname'])) {
                    $data['surnameError'] = 'Surname can only contain letters (English/Russian/Ukrainian).';
                }
                
                if(!$data['nameError'] && !$data['surnameError'] && !$data['usernameError']) {
                    if($this->userModel->update(array_slice($data, 0, 5))) {
                        header('location:' . URLROOT . '/admin_pages/users/current_users');
                    } else {
                        die('Something went wrong.');
                    }
                }
            }

            $this->view('admin_pages/users/update_user', $data);
        }

        public function delete_user($id) {
            if($_SERVER['REQUEST_METHOD'] == 'POST') {
                if($this->userModel->delete($id)) {
                    header('location:' . URLROOT . '/admin_pages/users/current_users');
                }
                else {
                    die("Something went wrong...");
                }    
            }
        }

        public function make_admin($id) {
            if($_SERVER['REQUEST_METHOD'] == 'POST') {
                if(!$_POST['is_admin']) {
                    $this->userModel->makeAdmin($id);

                    header('location:' . URLROOT . '/admin_pages/users/current_users');
                }
            }
        }

        public function acceptNewUsers() {
            if($_SERVER['REQUEST_METHOD'] == 'POST') {
                if($_POST['selectedIds'] != '[]') {
                    $selectedIds = json_decode($_POST['selectedIds']);
                    foreach($selectedIds as $id) {
                        $this->userModel->accept($id);
                    }
                }
            }
            
            header('location:' . URLROOT . '/admin_pages/users/new_users');
        }

        public function declineNewUsers() {
            if($_SERVER['REQUEST_METHOD'] == 'POST') {
                if($_POST['selectedIds'] != '[]') {
                    $selectedIds = json_decode($_POST['selectedIds']);
                    foreach($selectedIds as $id) {
                        $this->userModel->delete($id);
                    }
                }
            }
            
            header('location:' . URLROOT . '/admin_pages/users/new_users');
        }
    }
