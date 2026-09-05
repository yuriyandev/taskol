<?php class ControllerBoard extends Controller {
    public function view() {
        $this->load->model('board');
        $this->load->model('list');
        $this->load->model('task');

        $template = 'board/list';

        $data = array();
    
        $filter = array();

        if(isset($_GET['board_id'])) {
            if($this->model_board->getBoard($_GET['board_id'])) {
                $filter['board_id'] = $_GET['board_id'];
                $data['lists'] = $this->model_list->getlists($filter);

                $tasks = $this->model_task->getTasks();
                $data['tasks'] = array();
                foreach($tasks as $task) {
                    $data['tasks'][$task['list_id']][] = $task;
                }

                $data['board_id'] = $_GET['board_id'];

                $template = 'board/view';
            }
        }
        
        $data['rows_count'] = isset($_GET['board_id']) && isset($this->session->data['rows_count'][$_GET['board_id']]) ? $this->session->data['rows_count'][$_GET['board_id']] : 1;

        $data['header'] = $this->load->controller('main/header');
		$data['footer'] = $this->load->controller('main/footer');

        $filter = array();

        $data['boards'] = $this->model_board->getBoards($filter);

		$this->response->output($template, $data);
    }

    public function form() {
        $this->load->model('board');

        $data = array();

        $board_id = isset($_GET['board_id']) && $_GET['board_id'] ? $_GET['board_id'] : 0;

        if(isset($_POST) && $_POST) {
            $data = $_POST;
            if(isset($_GET['board_id']) && $_GET['board_id']) {
                $this->model_board->editBoard($_POST, $_GET['board_id']);
            } else {
                $board_id = $this->model_board->addBoard($_POST);
            }

            $json['success'] = true;

		    echo json_encode($json);
            exit;
        }

        if($board_id) {
            $data = $this->model_board->getBoard($board_id);

            $data['members'] = $data['members'] ? json_decode($data['members'], 1) : '';
        }

        $data['board_id'] = $board_id;

        $tpl = 'board/form';

		$this->response->output($tpl, $data);
    }

    public function delete() {
        $this->load->model('board');

        $json = array();
	
        $this->model_board->deleteBoard($_GET['board_id']);

        $json['success'] = true;
        echo json_encode($json);
    }

    public function sortBoard() {
        $this->load->model('board');

        $json = array();
        
        if(isset($_POST['board']) && is_array($_POST['board']) && !empty($_POST['board'])) {
            foreach($_POST['board'] as $sort_order => $board_id) {
                $this->model_board->changeBoardSortOrder($board_id, $sort_order);
            }
        }

        $json['success'] = true;
        echo json_encode($json);
    }

    public function setPrivateMode() {
        $this->session->data['private'] = isset($_GET['private']) ? $_GET['private'] : 1;

        $json['success'] = true;
        echo json_encode($json);
    }

    public function setRowsCount() {
        $json = array();

        if(isset($_POST['rows_count']) && isset($_POST['board_id'])) {
            $this->session->data['rows_count'][$_POST['board_id']] = $_POST['rows_count'];
        }

        $json['success'] = true;
        echo json_encode($json);
    }
}