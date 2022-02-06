<?php 
    class Core {
        protected $currentController = 'Users_Auth';
        protected $currentMethod = 'login';
        protected $params = [];

        public function __construct() {
            $url = $this->getUrl();
            // Getting controller name as a first array element.
            if($url)
            {
                $controller = ucwords($url[0], "_");
                if(file_exists('../app/controllers/' . $controller . '.php')) {
                    $this->currentController = $controller;
                    unset($url[0]);
                }
            }

            require '../app/controllers/' . $this->currentController . '.php';
            $this->currentController = new $this->currentController;
            // Getting method name by iterating over remaining array elements and searching for existing name.
            // Strict naming rules for files and controller`s methods!
            // Array elements remaining after searching for method become parameters of the method.
            if($url)
            {
                $urlCount = count($url);
                for($i = 1; $i <= $urlCount; $i++) {
                    $method = $url[$i];
                    if(method_exists($this->currentController, $method)) {
                        $this->currentMethod = $method;
                        unset($url[$i]);
                        break;
                    }
                    unset($url[$i]);
                }
            
                $this->params = array_values($url);
            }
            call_user_func_array([$this->currentController, $this->currentMethod], $this->params);
        }
        
        public function getUrl() {
            if(isset($_GET['url'])) {
                $url = rtrim($_GET['url'], '/');
                $url = filter_var($url, FILTER_SANITIZE_URL);
                $url = explode('/', $url);
                return $url;
            }
        }
    }
