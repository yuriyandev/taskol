<?php class ControllerTask extends Controller {
    public function form() {
        $this->load->model('task');
        $this->load->model('board');
        $this->load->model('list');
        $this->load->model('comment');
        $this->load->model('cover');

        $data = array();

        $task_id = isset($_GET['task_id']) && $_GET['task_id'] ? $_GET['task_id'] : 0;
        $list_id = isset($_GET['list_id']) && $_GET['list_id'] ? $_GET['list_id'] : 0;

        if(isset($_POST) && $_POST) {
            $data = $_POST;
            if(isset($_GET['task_id']) && $_GET['task_id']) {
                $this->model_task->editTask($_POST, $_GET['task_id']);
            } else {
                $task_id = $this->model_task->addTask($_POST, $list_id);
            }

            $json['task_info'] = $this->model_task->getTask($task_id);

            if($json['task_info']['date_end'] && $json['task_info']['date_end'] != '0000-00-00 00:00:00') {
                $json['task_info']['date_end'] = date('d-m-Y', strtotime($json['task_info']['date_end']));
            } else {
                $json['task_info']['date_end'] = '';
            }

		    echo json_encode($json);
            exit;
        }

        if($task_id) {
            $data = $this->model_task->getTask($task_id);
            
            $data['members'] = $data['members'] ? json_decode($data['members'], 1) : '';

            $data['description'] = nl2br($data['description']);

            $data['check_lists'] = $this->getCheckLists($task_id);

            if($data['date_start'] == '0000-00-00') {
                $data['date_start'] = date("Y-m-d");
            }

            $data['schedule_exceptions'] = $this->getTaskScheduleExceptions($task_id);
        }

        $data['task_id'] = $task_id;
        $data['list_id'] = $list_id ? $list_id : $data['list_id'];
        $data['user_id'] = $this->user->getId();

        $this->load->model('task_schedule');
        $task_schedule = $this->model_task_schedule->getTaskSchedule($task_id);

        $data['schedule_days'] = array();

        foreach($task_schedule as $weekday) {
            $data['schedule_days'][$weekday['weekday']] = $weekday;
        }

		$data['boards'] = $this->model_board->getBoards();
        $data['lists'] = $this->model_list->getLists();

        $data['statuses'] = $this->config->get('statuses');
        $data['stages'] = $this->config->get('stages');
        
        $data['covers'] = $this->model_cover->getCovers();

		$this->response->output('task/form', $data);
    }

    public function delete() {
        $this->load->model('task');

        $json = array();
	
        $this->model_task->deleteTask($_GET['task_id']);

        $json['success'] = true;
        echo json_encode($json);
    }

    public function changeTaskList() {
        $this->load->model('task');

        $json = array();

        if(isset($_GET['task_id']) && $_GET['task_id'] && isset($_GET['list_id']) && $_GET['list_id']) {
            $this->model_task->changeTaskList($_GET['task_id'], $_GET['list_id']);

            $json['success'] = true;
        } else {
            $json['error'] = true;
        }

        echo json_encode($json);
    }

    public function changeTaskSortOrder() {
        $this->load->model('task');

        $json = array();

        if(isset($_GET['task_id']) && $_GET['task_id'] && isset($_GET['sort_order'])) {
            $this->model_task->changeTaskSortOrder($_GET['task_id'], $_GET['sort_order']);

            $json['success'] = true;
        } else {
            $json['error'] = true;
        }

        echo json_encode($json);
    }

    public function getComments() {
        if(isset($_GET['task_id']) && $_GET['task_id']) {
            $this->load->model('comment');

            $data['task_id'] = $_GET['task_id'];
            $data['user_id'] = $this->user->getId();

            $data['comments'] = $this->model_comment->getComments($_GET['task_id']);

            $this->response->output('task/comments', $data);
        }
    }

    public function saveComment() {
        $this->load->model('comment');

        $json = array();

        if(isset($_GET['task_id']) && $_GET['task_id']) {
            if(isset($_GET['comment_id']) && $_GET['comment_id']) {
                if($this->model_comment->checkCommentUserId($_GET['comment_id'])) {
                    $this->model_comment->editComment($_POST['text'], $_GET['comment_id']);
                    $json['success'] = true;
                } else {
                    $json['error'] = true;
                }
            } else {
                $this->model_comment->addComment($_POST['text'], $_GET['task_id']);
                $json['success'] = true;
            }
        } else {
            $json['error'] = true;
        }

        echo json_encode($json);
    }

    public function deleteComment() {
        $this->load->model('comment');

        $json = array();
	
        if(isset($_GET['comment_id']) && $_GET['comment_id']) {
            if($this->model_comment->checkCommentUserId($_GET['comment_id'])) {
                $this->model_comment->deleteComment($_GET['comment_id']);

                $json['success'] = true;
            } else {
                $json['error'] = true;
            }
        } else {
            $json['error'] = true;
        }

        echo json_encode($json);
    }

    public function getCheckLists($task_id = 0) {
        $this->load->model('check_list');

        if($task_id) {
            $data['task_id'] = $task_id;
            $data['check_lists'] = $this->model_check_list->getCheckLists($task_id);

            return $this->response->render('task/check_list', $data);
        }

        if(isset($_GET['task_id']) && $_GET['task_id']) {
            $data['task_id'] = $_GET['task_id'];
            $data['check_lists'] = $this->model_check_list->getCheckLists($_GET['task_id']);

            $this->response->output('task/check_list', $data);
        }
    }

    public function sortCheckList() {
        $this->load->model('check_list');

        $json = array();
        
        if(isset($_POST['check_list']) && is_array($_POST['check_list']) && !empty($_POST['check_list'])) {
            foreach($_POST['check_list'] as $sort_order => $check_list_id) {
                $this->model_check_list->changeCheckListSortOrder($check_list_id, $sort_order);
            }
        }

        $json['success'] = true;
        echo json_encode($json);
    }

    public function sortCheckListItems() {
        $this->load->model('check_list');

        $json = array();
        
        if(isset($_POST['check_list_items']) && is_array($_POST['check_list_items']) && !empty($_POST['check_list_items'])) {
            foreach($_POST['check_list_items'] as $sort_order => $check_list_items_id) {
                $this->model_check_list->changeCheckListItemsSortOrder($check_list_items_id, $sort_order);
            }
        }

        $json['success'] = true;
        echo json_encode($json);
    }

    public function addTaskStatusHistory() {
        $this->load->model('task');

        $json = array();

        if(!isset($_GET['task_id']) || !$_GET['task_id']) {
            $json['error'] = true;
        }

        if(!isset($_GET['date_added']) || !$_GET['date_added']) {
            $json['error'] = true;
        }
	
        if(!$json['error']) {
            $task_id = $_GET['task_id'];
            $status_id = isset($_GET['status_id']) && $_GET['status_id'] ? $_GET['status_id'] : 0;
            $date_added = $_GET['date_added'];

            $this->model_task->addTaskStatusHistory($task_id, $status_id, $date_added);

            $json['success'] = true;
        }

        echo json_encode($json);
    }

    public function getTaskScheduleExceptions($task_id = 0) {
        $this->load->model('task_schedule');

        if($task_id) {
            $data['task_id'] = $task_id;
            $data['schedule_exceptions'] = $this->model_task_schedule->getTaskScheduleExceptions($task_id);
            
            return $this->response->render('task/schedule_exception', $data);
        }

        if(isset($_GET['task_id']) && $_GET['task_id']) {
            $data['task_id'] = $_GET['task_id'];
            $data['schedule_exceptions'] = $this->model_task_schedule->getTaskScheduleExceptions($_GET['task_id']);

            $this->response->output('task/schedule_exception', $data);
        }
    }
}