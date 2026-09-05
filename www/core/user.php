<?php 
class User {
    private $user_id;
	private $user_email;

    public function __construct($registry) {
        $this->db = $registry->get('db');
		$this->session = $registry->get('session');

		if (isset($this->session->data['user_id'])) {
			$user = $this->db->query("SELECT * FROM " . DB_PREFIX . "user WHERE user_id = '" . (int)$this->session->data['user_id'] . "'");

			if ($user->num_rows) {
				$this->login($this->session->data['user_id']);
			} else {
				$this->logout();
			}
		}
	}

    public function login($user_id) {
		$user = $this->db->query("SELECT * FROM " . DB_PREFIX . "user WHERE user_id = '" . (int)$user_id . "'");

		$this->session->data['user_id'] = $user->row['user_id'];
        $this->user_id =  $user->row['user_id'];
		$this->user_email =  $user->row['email'];
	}

	public function logout() {
		unset($this->session->data['user_id']);

		$this->user_id = '';
	}

    public function getId() {
		return $this->user_id;
	}

	public function getEmail() {
		return $this->user_email;
	}
}