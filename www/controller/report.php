<?php class ControllerReport extends Controller {
    public function view() {
        $this->load->model('board');
        $this->load->model('task');

        $template = 'report/view';

        $data = array();

        $board_id = isset($_GET['board_id']) && $_GET['board_id'] ? $_GET['board_id'] : 0;
		$data['board_id'] = $board_id;

        $period = isset($_GET['period']) && $_GET['period'] ? $_GET['period'] : 'week';
		$data['period'] = $period;

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

        // Periods
        $params = $_GET;
        unset($params['period']);

        $data['reports'] = array(
            'day' => array(
                'name' => 'Day',
                'href' => 'index.php?period=day&' . http_build_query($params),
            ),
            'week' => array(
                'name' => 'Week',
                'href' => 'index.php?period=week&' . http_build_query($params),
            ),
            'month' => array(
                'name' => 'Month',
                'href' => 'index.php?period=month&' . http_build_query($params),
            ),
            'year' => array(
                'name' => 'Year',
                'href' => 'index.php?period=year&' . http_build_query($params),
            ),
            // 'custom' => array(
            //     'name' => 'Custom',
            //     'href' => 'index.php?period=custom&' . http_build_query($params),
            // ),
        );

        // Reports
        $date_start = date("Y-m-d");
        $date_end = false;

        if($period == 'day') {
            $date_start = date("Y-m-d");
        }
        if($period == 'week') {
            $week = new DateTime(date("Y-m-d"));

            $week->modify('monday this week');
            $date_start = $week->format('Y-m-d');

            $week->modify('sunday this week');
            $date_end = $week->format('Y-m-d');
        }
        if($period == 'month') {
            $month = new DateTime(date("Y-m-d"));

            $month->modify('first day of');
            $date_start = $month->format('Y-m-d');

            $month->modify('last day of');
            $date_end = $month->format('Y-m-d');
        }
        if($period == 'year') {
            $year = new DateTime(date("Y-m-d"));

            $year->modify('first day of January this year');
            $date_start = $year->format('Y-m-d');

            $year->modify('last day of December this year');
            $date_end = $year->format('Y-m-d');
        }

        $filter_data = array(
			'board_id' => $board_id,
			'date_start' => $date_start,
			'date_end' => $date_end,
            'period' => $period,
		);

		$period_reports = $this->model_task->getTaskStatusHistoryByPeriod($filter_data);

        //print_r($period_reports);

        $status_history_labels = array();
		$status_values = array();

		foreach($period_reports as $k => $period_report) {
            if($period == 'year') {
                $period_report['period'] = $this->lang->get('text_month_short_' . $period_report['period']);
            }
			$status_history_labels[] = $period_report['period'];
			$status_values[] = $period_report['percent_done'];
		}

		$status_history_values[] = array(
			'label' => '%',
			'data' => $status_values,
			'borderWidth' => 1,
			'backgroundColor' => '#1f453e8f'
		);

		$data['status_history_labels'] = json_encode($status_history_labels);
        $data['status_history_values'] = json_encode($status_history_values);

        foreach($period_reports as $k => $period_report) {
            if($period == 'year') {
                $period_reports[$k]['period'] = $this->lang->get('text_month_' . $period_report['period']);
            }
        }
        $data['period_reports'] = $period_reports;

        $task_reports = $this->model_task->getTaskStatusHistoryByTask($filter_data);
        $data['task_reports'] = $task_reports;

        $data['header'] = $this->load->controller('main/header');
		$data['footer'] = $this->load->controller('main/footer');

		$this->response->output($template, $data);
    }
}
