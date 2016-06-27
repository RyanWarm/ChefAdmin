<?php
class ModelCatalogOccasion extends Model {
    private function expandDateToTime($date) {
        if (!empty($date))
            return $date . " 00:00:00";
        else
            return date("Y-m-d 00:00:00", time() + 24*60*60);
    }

	public function addOccasion($data) {
		$this->db->query("INSERT INTO " . DB_PREFIX . "occasion SET sort_order = '" . (int)$data['sort_order'] . "', status = '" . (int)$data['status'] . "', parent_id = '" . (int)$data['parent_id'] . "', happen_time = '" . $this->expandDateToTime($data['happen_time']) . "', end_time = '" . $this->expandDateToTime($data['end_time']) . "', promote_days = '" . (int)$data['promote_days'] . "', date_modified = NOW(), date_added = NOW()");
	
		$occasion_id = $this->db->getLastId();
        
		if (isset($data['image'])) {
			$this->db->query("UPDATE " . DB_PREFIX . "occasion SET image = '" . $this->db->escape(html_entity_decode($data['image'], ENT_QUOTES, 'UTF-8')) . "' WHERE occasion_id = '" . (int)$occasion_id . "'");
		}

		foreach ($data['occasion_description'] as $language_id => $value) {
			$this->db->query("INSERT INTO " . DB_PREFIX . "occasion_description SET occasion_id = '" . (int)$occasion_id . "', language_id = '" . (int)$language_id . "', name = '" . $this->db->escape($value['name']) . "', description = '" . $this->db->escape($value['description']) . "'");
		}

		if (isset($data['occasion_tag'])) {
			foreach ($data['occasion_tag'] as $tag_id) {
				$this->db->query("INSERT INTO " . DB_PREFIX . "occasion_to_tag SET occasion_id = '" . (int)$occasion_id . "', tag_id = '" . (int)$tag_id . "'");
			}
		}
		
		$this->cache->delete('occasion');
	}
	
	public function editOccasion($occasion_id, $data) {
		$this->db->query("UPDATE " . DB_PREFIX . "occasion SET sort_order = '" . (int)$data['sort_order'] . "', status = '" . (int)$data['status'] . "', parent_id = '" . (int)$data['parent_id'] . "', happen_time = '" . $this->expandDateToTime($data['happen_time']) . "', end_time = '" . $this->expandDateToTime($data['end_time']) . "', promote_days = '" . (int)$data['promote_days'] . "', date_modified = NOW() WHERE occasion_id = '" . (int)$occasion_id . "'");

		if (isset($data['image'])) {
			$this->db->query("UPDATE " . DB_PREFIX . "occasion SET image = '" . $this->db->escape(html_entity_decode($data['image'], ENT_QUOTES, 'UTF-8')) . "' WHERE occasion_id = '" . (int)$occasion_id . "'");
		}

		$this->db->query("DELETE FROM " . DB_PREFIX . "occasion_description WHERE occasion_id = '" . (int)$occasion_id . "'");

		foreach ($data['occasion_description'] as $language_id => $value) {
			$this->db->query("INSERT INTO " . DB_PREFIX . "occasion_description SET occasion_id = '" . (int)$occasion_id . "', language_id = '" . (int)$language_id . "', name = '" . $this->db->escape($value['name']) . "', description = '" . $this->db->escape($value['description']) . "'");
		}

		$this->db->query("DELETE FROM " . DB_PREFIX . "occasion_to_tag WHERE occasion_id = '" . (int)$occasion_id . "'");
		
		if (isset($data['occasion_tag'])) {
			foreach ($data['occasion_tag'] as $tag_id) {
				$this->db->query("INSERT INTO " . DB_PREFIX . "occasion_to_tag SET occasion_id = '" . (int)$occasion_id . "', tag_id = '" . (int)$tag_id . "'");
			}		
		}
		
		$this->cache->delete('occasion');
	}
	
	public function deleteOccasion($occasion_id) {
		$this->db->query("DELETE FROM " . DB_PREFIX . "occasion WHERE occasion_id = '" . (int)$occasion_id . "'");
		$this->db->query("DELETE FROM " . DB_PREFIX . "occasion_description WHERE occasion_id = '" . (int)$occasion_id . "'");
        
		$this->db->query("DELETE FROM " . DB_PREFIX . "occasion_to_tag WHERE occasion_id = '" . (int)$occasion_id . "'");
		$this->db->query("DELETE FROM " . DB_PREFIX . "occasion_to_card_category WHERE occasion_id = '" . (int)$occasion_id . "'");

		$query = $this->db->query("SELECT occasion_id FROM " . DB_PREFIX . "occasion WHERE parent_id = '" . (int)$occasion_id . "'");

		foreach ($query->rows as $result) {
			$this->deleteOccasion($result['occasion_id']);
		}

		$this->cache->delete('occasion');
	} 

	public function getOccasion($occasion_id) {
		$query = $this->db->query("SELECT DISTINCT *, (SELECT keyword FROM " . DB_PREFIX . "url_alias WHERE query = 'occasion_id=" . (int)$occasion_id . "') AS keyword FROM " . DB_PREFIX . "occasion WHERE occasion_id = '" . (int)$occasion_id . "'");

		return $query->row;
	} 
	
	public function getOccasions($parent_id = 0) {
		$occasion_data = $this->cache->get('occasion.' . (int)$this->config->get('config_language_id'). '.' . (int)$parent_id);
	
		if (!$occasion_data) {
			$occasion_data = array();
		
			$query = $this->db->query("SELECT DISTINCT c.*, cd.*, count(pc.tag_id) as tag_count FROM " . DB_PREFIX . "occasion c LEFT JOIN " . DB_PREFIX . "occasion_description cd ON (c.occasion_id = cd.occasion_id) LEFT JOIN occasion_to_tag pc ON (c.occasion_id = pc.occasion_id) WHERE c.parent_id = '" . (int)$parent_id ."' AND cd.language_id = '" . (int)$this->config->get('config_language_id') . "' GROUP BY c.occasion_id ORDER BY c.sort_order, cd.name ASC");
		
			foreach ($query->rows as $result) {
				$occasion_data[] = array(
					'occasion_id' => $result['occasion_id'],
					'name'        => /* $result['name'], */ $this->getPath($result['occasion_id'], $this->config->get('config_language_id')),
                    'tag_count' => $result['tag_count'],
					'status'  	  => $result['status'],
					'sort_order'  => $result['sort_order']
				);
                
                $occasion_data = array_merge($occasion_data, $this->getOccasions($result['occasion_id']));
			}	
	
			$this->cache->set('occasion.' . (int)$this->config->get('config_language_id') . '.' . (int)$parent_id, $occasion_data);
		}
		
		return $occasion_data;
	}
    
	public function getPath($occasion_id) {
		$query = $this->db->query("SELECT name, parent_id FROM " . DB_PREFIX . "occasion c LEFT JOIN " . DB_PREFIX . "occasion_description cd ON (c.occasion_id = cd.occasion_id) WHERE c.occasion_id = '" . (int)$occasion_id . "' AND cd.language_id = '" . (int)$this->config->get('config_language_id') . "' ORDER BY c.sort_order, cd.name ASC");
		
		if ($query->row['parent_id']) {
			return $this->getPath($query->row['parent_id'], $this->config->get('config_language_id')) . $this->language->get('text_separator') . $query->row['name'];
		} else {
			return $query->row['name'];
		}
	}

	public function getOccasionDescriptions($occasion_id) {
		$occasion_description_data = array();
		
		$query = $this->db->query("SELECT * FROM " . DB_PREFIX . "occasion_description WHERE occasion_id = '" . (int)$occasion_id . "'");
		
		foreach ($query->rows as $result) {
			$occasion_description_data[$result['language_id']] = array(
				'name'             => $result['name'],
				'description'      => $result['description']
			);
		}
		
		return $occasion_description_data;
	}	
	
		
	public function getTotalOccasions() {
      	$query = $this->db->query("SELECT COUNT(*) AS total FROM " . DB_PREFIX . "occasion");
		
		return $query->row['total'];
	}	
    
    public function getOccasionTags($occasion_id) {
		$occasion_tag_data = array();
		
		$query = $this->db->query("SELECT * FROM " . DB_PREFIX . "occasion_to_tag WHERE occasion_id = '" . (int)$occasion_id . "'");
		
		foreach ($query->rows as $result) {
			$occasion_tag_data[] = $result['tag_id'];
		}

		return $occasion_tag_data;
	}
}

?>