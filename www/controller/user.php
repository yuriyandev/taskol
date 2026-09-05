<?php class ControllerUser extends Controller {
    public function list() {
        $this->load->model('user');
        
        $data['users'] = $this->model_user->getUsers();

        $data['header'] = $this->load->controller('main/header');
		$data['footer'] = $this->load->controller('main/footer');

		$this->response->output('user/list', $data);
    }

    public function form() {
        $this->load->model('user');

        $data = array();

        $user_id = isset($_GET['user_id']) && $_GET['user_id'] ? $_GET['user_id'] : 0;

        if(isset($_POST) && $_POST) {
            $data = $_POST;
            if(isset($_GET['user_id']) && $_GET['user_id']) {
                $this->model_user->editUser($_POST, $_GET['user_id']);

                $json['success'] = true;
            } else {
                $json['error'] = true;
            }

		    echo json_encode($json);
            exit;
        }

        if($user_id) {
            $data = $this->model_user->getUser($user_id);
        }

        $data['user_id'] = $user_id;

		$this->response->output('user/form', $data);
    }

    // public function delete() {
    //     $this->load->model('user');

    //     $json = array();
	
    //     $this->model_user->deleteUser($_GET['user_id']);

    //     $json['success'] = true;
    //     echo json_encode($json);
    // }

    public function register() {
        $this->load->model('user');

        if($this->user->getId()) {
            $this->response->redirect(get_url('main/home'));
        }

        $data = array();

        if(isset($_POST) && $_POST) {
            $json = array();

            $this->validateRegister($json);

            if(!isset($json['error'])) {
                if(!$this->model_user->checkUserByEmail($_POST)) {
                    $user_id = $this->model_user->addUser($_POST);

                    $this->user->login($user_id);

                    $json['redirect'] = get_url('main/home');
                } else {
                    $json['error'] = $this->lang->data['error_user_email_duplicate'];
                }
            }

            echo json_encode($json, 1);
            exit;
        }
        
        $data['header'] = $this->load->controller('main/header');
		$data['footer'] = $this->load->controller('main/footer');

		$this->response->output('user/register', $data);
    }

    public function login() {
        $this->load->model('user');

        if($this->user->getId()) {
            $this->response->redirect(get_url('main/home'));
        }

        $data = array();

        if(isset($_POST) && $_POST) {
            $json = array();

            $this->validateLogin($json);
            
            if(!isset($json['error'])) {
                if($user_id = $this->model_user->checkUser($_POST)) {
                    $this->user->login($user_id);

                    $json['redirect'] = get_url('main/home');
                } else {
                    $json['error'] = $this->lang->data['error_user_not_exist'];
                }
            }

            echo json_encode($json, 1);
            exit;
        }
        
        $data['header'] = $this->load->controller('main/header');
		$data['footer'] = $this->load->controller('main/footer');

		$this->response->output('user/login', $data);
    }

    public function logout() {
        $this->user->logout();

        $json['success'] = true;
        
        $this->response->redirect(get_url('user/login'));
    }

    protected function validateLogin(&$json) {
        if(!isset($_POST['user']['password']) || !$_POST['user']['password']) {
            $json['error'] = $this->lang->data['error_user_password_empty'];
        }

        if(!isset($_POST['user']['email']) || !$_POST['user']['email']) {
            $json['error'] = $this->lang->data['error_user_email_empty'];
        }
    }

    protected function validateRegister(&$json) {
        if(!isset($_POST['user']['password']) || !$_POST['user']['password']) {
            $json['error'] = $this->lang->data['error_user_password_empty'];
        }

        if(!isset($_POST['user']['email']) || !$_POST['user']['email']) {
            $json['error'] = $this->lang->data['error_user_email_empty'];
        }

        if(!isset($_POST['user']['name']) || !$_POST['user']['name']) {
            $json['error'] = $this->lang->data['error_user_name_empty'];
        }
    }
}