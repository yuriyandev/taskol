<?php 
class ModelStatus extends Model {
    public function getStatuses() {
		$statuses = $this->db->query("SELECT * FROM " . DB_PREFIX . "status ORDER BY sort_order ASC");

		return $statuses->rows;
	}
}