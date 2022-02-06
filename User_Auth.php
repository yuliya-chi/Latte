<?php
    class Users_Auth extends Controller {
        private $userModel;
        private $groupModel;

        public function __construct() {
            $this->userModel = $this->model('User');
            $this->groupModel = $this->model('Group');
        }

        private function createSession($user) {
            $_SESSION['user_id'] = $user->user_id;
            $_SESSION['username'] = $user->username;
            $_SESSION['is_admin'] = $user->is_admin;
        }

        public function login() {
            $data = [
                'username' => '',
                'password' => '',
                'usernameError' => '',
                'passwordError' => ''
            ];

            if($_SERVER['REQUEST_METHOD'] == 'POST') {
                $_POST = filter_input_array(INPUT_POST, FILTER_SANITIZE_STRING);
                $data = [
                    'username' => trim($_POST['username']),
                    'password' => trim($_POST['password']),
                    'usernameError' => '',
                    'passwordError' => ''
                ];

                if(!$data['username']) {
                    $data['usernameError'] = 'Enter the username, please.';
                }

                if(!$data['password']) {
                    $data['passwordError'] = 'Enter the password, please.';
                }

                if(!$data['usernameError'] && !$data['passwordError']) {
                    $loggedInUser = $this->userModel->login(array_slice($data, 0, 2));

                    if($loggedInUser == NOT_PROCESSED) {
                        $data['passwordError'] = 'You need to wait for confirmation of registration from the teacher.';
                    }
                    if($loggedInUser == INCORRECT_CREDENTIALS) {
                        $data['passwordError'] = 'Username or password is incorrect. Please try again.';
                    }
                    if(!$data['passwordError']) {
                        $this->createSession($loggedInUser);
                        if(isAdmin()) {
                            header('location:' . URLROOT . '/admin_pages/labs_tasks/laboratory_works');
                        } else {
                            header('location:' . URLROOT . '/user_pages/main');
                        }
                    }
                }
            }

            $this->view('users_auth/login', $data);
        }

        public function logout() {
            unset($_SESSION['user_id']);
            unset($_SESSION['username']);
            unset($_SESSION['is_admin']);
            header('location:' . URLROOT . '/users_auth/login');
        }

        public function register() {
            $groups = $this->groupModel->getAllGroups();

            $data = [
                'name' => '',
                'surname' => '',
                'group_id' => '',
                'username' => '',
                'password' => '',
                'repassword' => '',
                'groups' => $groups,
                'nameError' => '',
                'surnameError' => '',
                'usernameError' => '',
                'passwordError' => '',
                'repasswordError' => ''
            ];

            if($_SERVER['REQUEST_METHOD'] == 'POST') {
                $_POST = filter_input_array(INPUT_POST, FILTER_SANITIZE_STRING);
                $group = $this->groupModel->getGroupByNumber(trim($_POST['group']));

                $data = [
                    'name' => trim($_POST['name']),
                    'surname' => trim($_POST['surname']),
                    'group_id' => $group->group_id,
                    'username' => trim($_POST['username']),
                    'password' => trim($_POST['password']),
                    'repassword' => trim($_POST['repassword']),
                    'groups' => $groups,
                    'nameError' => '',
                    'surnameError' => '',
                    'usernameError' => '',
                    'passwordError' => '',
                    'repasswordError' => ''
                ];

                $nameRule = "/^[a-zA-Zа-яА-ЯёЁґҐіІїЇєЄ]*$/u";
                $usernameRule = "/^[a-zA-Z0-9_@.]{3,32}$/";
                //$passwordRule = "/^(?=.{8,32})(?=.*[a-z])(?=.*[A-Z])(?=.*[!@#$&*])(?=.*[0-9])$/";
                $passwordRule = "/^(?=\S{8,32})(?=\S*[a-z])(?=\S*[A-Z])(?=\S*[0-9])\S*$/";

                if(!$data['name']) {
                    $data['nameError'] = 'Enter the name, please.';
                } elseif(!preg_match($nameRule, $data['name'])) {
                    $data['nameError'] = 'Name can only contain letters (English/Russian/Ukrainian).';
                }

                if(!$data['surname']) {
                    $data['surnameError'] = 'Enter the surname, please.';
                } elseif(!preg_match($nameRule, $data['surname'])) {
                    $data['surnameError'] = 'Surname can only contain letters (English/Russian/Ukrainian).';
                }

                if(!$data['username']) {
                    $data['usernameError'] = 'Enter the username, please.';
                } elseif(!preg_match($usernameRule, $data['username'])) {
                    $data['usernameError'] = 'Username must be at least 3 and no longer than 32 characters,'.
                    ' may contain letters, numbers, underscore, at sign and dot.';
                }

                if(!$data['password']) {
                    $data['passwordError'] = 'Enter the password, please.';
                } elseif(!preg_match($passwordRule, $data['password'])) {
                    $data['passwordError'] = ' Password must be at least 8 and no longer than 32 characters,' . 
                    ' contain uppercase and lowercase letters and numbers';
                }//and special symbols (!, @, #, $, &, *).

                if(!$data['repassword']) {
                    $data['repasswordError'] = 'Confirm the password, please.';
                } else {
                    if($data['password'] !== $data['repassword']) {
                        $data['repasswordError'] = 'Passwords do not match.';
                    }
                }
                
                if(!$data['nameError'] && !$data['surnameError'] &&
                 !$data['usernameError'] && !$data['passwordError'] &&
                 !$data['repasswordError']) {
                    $data['password'] = password_hash($data['password'], PASSWORD_DEFAULT);
                    $data['repassword'] = $data['password'];
                    if($this->userModel->register($data)) {
                        header('location:' . URLROOT . '/users_auth/login');
                    } else {
                        die('Something went wrong.');
                    }
                }
            }

            $this->view('users_auth/register', $data);
        }
    }
