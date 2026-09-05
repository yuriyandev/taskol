<?php class ControllerList extends Controller {
    public function form() {
        $this->load->model('list');

        $data = array();

        $board_id = isset($_GET['board_id']) && $_GET['board_id'] ? $_GET['board_id'] : 0;
        $row = isset($_GET['row']) && $_GET['row'] ? $_GET['row'] : 0;
        $list_id = isset($_GET['list_id']) && $_GET['list_id'] ? $_GET['list_id'] : 0;

        $data['list_id'] = $list_id;
        $data['board_id'] = $board_id;
        $data['row'] = $row;

        if(isset($_POST) && $_POST) {
            $data = $_POST;
            if(isset($_GET['list_id']) && $_GET['list_id']) {
                $this->model_list->editList($_POST, $board_id, $_GET['list_id']);
            } else {
                $list_id = $this->model_list->addList($_POST, $board_id);
            }

            $json['success'] = true;

		    echo json_encode($json);
            exit;
        }

        if($list_id) {
            $data = $this->model_list->getList($list_id);
        }

		$this->response->output('list/form', $data);
    }

    public function delete() {
        $this->load->model('list');

        $json = array();
	
        $this->model_list->deleteList($_GET['list_id']);

        $json['success'] = true;
        echo json_encode($json);
    }

    public function sortList() {
        $this->load->model('list');

        $json = array();
        
        if(isset($_POST['list']) && is_array($_POST['list']) && !empty($_POST['list'])) {
            foreach($_POST['list'] as $sort_order => $list_id) {
                $this->model_list->changeListSortOrder($list_id, $sort_order);
            }
        }

        $json['success'] = true;
        echo json_encode($json);
    }
}