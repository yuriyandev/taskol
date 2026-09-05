<?php 
class ModelUser extends Model {
	public function checkUser($data) {
		$user = $data['user'];

		$user = $this->db->query("SELECT user_id FROM " . DB_PREFIX . "user WHERE email = '" . $this->db->escape($user['email']) . "' AND password = '" . $this->db->escape(md5($user['password'])) . "'");

		if($user->num_rows) {
			return $user->row['user_id'];
		} else {
			return false;
		}
	}

	public function checkUserByEmail($data) {
		$user = $data['user'];
		
		$user = $this->db->query("SELECT user_id FROM " . DB_PREFIX . "user WHERE email = '" . $this->db->escape($user['email']) . "'");

		if($user->num_rows) {
			return true;
		} else {
			return false;
		}
	}

    public function getUsers() {
		$groups = $this->db->query("SELECT * FROM " . DB_PREFIX . "user ORDER BY name ASC");

		return $groups->rows;
	}

	public function getUser($user_id) {
		$user = $this->db->query("SELECT * FROM " . DB_PREFIX . "user WHERE user_id = '" . (int)$user_id . "'");

		return $user->row;
	}

	public function editUser($data, $user_id) {
		$json = array();

		$user = $data['user'];

		$this->db->query("UPDATE " . DB_PREFIX . "user SET name='" . $this->db->escape($user['name']) . "', email='" . $this->db->escape($user['email']) . "', password = '" . $this->db->escape(md5($user['password'])) . "', date_modified = NOW()  WHERE user_id = '" . (int)$user_id . "' ");
	}

	public function addUser($data) {
		$json = array();

		$user = $data['user'];

		$this->db->query("INSERT INTO " . DB_PREFIX . "user SET name='" . $this->db->escape($user['name']) . "', email='" . $this->db->escape($user['email']) . "', date_added = NOW(), password = '" . $this->db->escape(md5($user['password'])) . "', date_modified = NOW() ");

		$user_id = $this->db->getLastId();

		return $user_id;
	}

	public function deleteUser($user_id) {
		$groups = $this->db->query("DELETE FROM " . DB_PREFIX . "user WHERE user_id = '" . (int)$user_id . "' ");
	}
}