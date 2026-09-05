<?php 
class ControllerMainHome extends Controller {
    public function index() {
		$this->load->model('board');
        $this->load->model('task');
		$this->load->model('list');
		$this->load->model('status');
		$this->load->model('task_schedule');

		$data = array();

		$stage_id = 1;

		$board_id = isset($_GET['board_id']) && $_GET['board_id'] ? $_GET['board_id'] : 0;
		$data['board_id'] = $board_id;

		$current_date = date('Y-m-d');
		$selected_date = isset($_GET['selected_date']) && $_GET['selected_date'] ? $_GET['selected_date'] : $current_date;

		$data['selected_date'] = $selected_date;

		$data['current_date'] = $current_date;

		$params = $_GET;
		$params['selected_date'] = $current_date;
		$data['current_date_href'] = 'index.php?' . http_build_query($params);

		$startOfWeek = new DateTime($selected_date);

		$dow = $startOfWeek->format('D');

		$startOfWeek->modify('monday this week');

		$data['selected_year'] = $startOfWeek->format('Y');
		$data['selected_month'] = $startOfWeek->format('F');

		$data['selected_week'] = array();

		for ($i = 0; $i < 7; $i++) {
			$params = $_GET;
			$params['selected_date'] = $startOfWeek->format('Y-m-d');

			$data['selected_week'][] = array(
				'dow' => $startOfWeek->format('D'),
				'day' => $startOfWeek->format('d'),
				'date' => $startOfWeek->format('Y-m-d'),
				'href' => 'index.php?' . http_build_query($params),
			);

			$startOfWeek->modify('+1 day');
		}

		$data['tasks'] = array();
		
		$task_filter = array(
			'board_id' => $board_id,
			'filter_status' => isset($this->session->data['filter_status']) ? $this->session->data['filter_status'] : null,
			'stage_id' => $stage_id,
			'date' => $selected_date,
			'day' => strtolower($dow),
			'sort' => 'ts.time_start',
			'order' => 'ASC',
		);

		$tasks = $this->model_task->getTasks($task_filter);

		if($tasks) {
			foreach($tasks as $task) {
				$list_info = $this->model_list->getList($task['list_id']);
				if($list_info) {
					$days = array();
					$time_start = '';
					$time_end = '';

        			$task_schedule = $this->model_task_schedule->getTaskSchedule($task['task_id']);
					foreach($task_schedule as $weekday) {
						$days[] = $this->lang->get('text_week_short_' . $weekday['weekday']);
					}

					$data['tasks'][] = array(
						'task_id' => $task['task_id'],
						'type' => $task['type'],
						'name' => $task['name'],
						'description' => $task['description'],
						'board_name' => $task['board_name'],
						'list_id' => $task['list_id'],
						'list' => $list_info['name'],
						'list_color' => $list_info['color'],
						'status' => $task['status'],
						'days' => $days ? implode(',', $days) : false,
						'time_start' => $task['time_start'] && $task['time_start'] != '00:00:00' ? date('H:i', strtotime($task['time_start'])) : false,
						'time_end' => $task['time_end'] && $task['time_end'] != '00:00:00' ? date('H:i', strtotime($task['time_end'])) : false,
						'date_start' => $task['date_start'],
						'date_added' => $task['date_added'],
						'date_modified' => $task['date_modified'],
					);
				}
			}
		}

		//print_r($data['tasks']);

		// Status history
		$data['task_report'] = array();
		$status_history_labels = array();
		$status_values = array();

		$filter_data = array(
			'board_id' => $board_id,
			'stage_id' => $stage_id,
			'date_start' => $data['selected_week'][0]['date'],
			'date_end' => $data['selected_week'][6]['date'],
		);
		
		$task_reports = $this->model_task->getTaskStatusHistoryByPeriod($filter_data);

		foreach($task_reports as $task_report) {
			if($task_report['period'] == $selected_date) {
				$data['task_report'] = $task_report;
			}

			$status_history_labels[] = date('d.m', strtotime($task_report['period']));
			$status_values[] = $task_report['percent_done'];
		}

		$status_history_values[] = array(
			'label' => '%',
			'data' => $status_values,
			'borderWidth' => 1,
			'backgroundColor' => '#ffffff46'
		);

		$data['status_history_labels'] = json_encode($status_history_labels);
        $data['status_history_values'] = json_encode($status_history_values);


		// Boards
		$params = $_GET;
		$params['board_id'] = 0;

		$data['boards'] = array(
			array(
				'board_id' => 0,
				'name' => 'All boards',
				'href' => 'index.php?' . http_build_query($params),
				'active' => !$board_id ? true : false
			)
		);

		$board_filter = array();
        $boards = $this->model_board->getBoards($board_filter);

		foreach($boards as $board) {
			$params = $_GET;
			$params['board_id'] = $board['board_id'];

			$board['href'] = 'index.php?' . http_build_query($params);

			$board['active'] = $board_id == $board['board_id'] ? true : false;

			$data['boards'][] = $board;
		}

		$data['statuses'] = $this->config->get('statuses');
		$data['filter_statuses'] = isset($this->session->data['filter_status']) ? $this->session->data['filter_status'] : array();

		$data['task_total'] = $this->model_task->getTaskTotal();
		$data['lists'] = $this->model_list->getLists();

		foreach($data['lists'] as $k => $v) {
			$data['lists'][$k]['task_total'] = $this->model_task->getTaskTotalByListId($v['list_id']);
		}


		$data['header'] = $this->load->controller('main/header');
		$data['footer'] = $this->load->controller('main/footer');

		$this->response->output('main/index', $data);
	}
	
	public function setFilter() {
		if(isset($_POST['filter']['status']) && !empty($_POST['filter']['status'])) {
			$this->session->data['filter_status'] = $_POST['filter']['status'];
		} else {
			unset($this->session->data['filter_status']);
		}

		echo json_encode(array('success' => true));
	}
}