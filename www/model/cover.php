<?php 
class ModelCover extends Model {
    public function getCovers() {
        $covers = array();

		$covers_query = $this->db->query("SELECT * FROM " . DB_PREFIX . "cover WHERE 1 ORDER BY cover_id ASC");

        if($covers_query->num_rows) {
            foreach($covers_query->rows as $row) {
                $covers[] = $row;
            }
        }

		return $covers;
	}

    public function getCover($cover_id) {
		$cover_query = $this->db->query("SELECT * FROM " . DB_PREFIX . "cover WHERE cover_id = '" . (int)$cover_id . "' ");

        if($cover_query->num_rows) {
            return $cover_query->row;
        }

		return false;
	}
}