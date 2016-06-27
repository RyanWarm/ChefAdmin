<?php
class ModelCatalogCardCategory extends Model {
	public function addCardCategory($data) {
		$this->db->query("INSERT INTO " . DB_PREFIX . "card_category SET sort_order = '" . (int)$data['sort_order'] . "', date_modified = NOW(), date_added = NOW()");
	
		$card_category_id = $this->db->getLastId();

		if (isset($data['image'])) {
			$this->db->query("UPDATE " . DB_PREFIX . "card_category SET image = '" . $this->db->escape(html_entity_decode($data['image'], ENT_QUOTES, 'UTF-8')) . "' WHERE card_category_id = '" . (int)$card_category_id . "'");
		}
        
		foreach ($data['card_category_description'] as $language_id => $value) {
			$this->db->query("INSERT INTO " . DB_PREFIX . "card_category_description SET card_category_id = '" . (int)$card_category_id . "', language_id = '" . (int)$language_id . "', name = '" . $this->db->escape($value['name']) . "', description = '" . $this->db->escape($value['description']) . "'");
		}

		if (isset($data['card_category_occasion'])) {
			foreach ($data['card_category_occasion'] as $occasion_id) {
				$this->db->query("INSERT INTO " . DB_PREFIX . "occasion_to_card_category SET occasion_id = '" . (int)$occasion_id . "', card_category_id = '" . (int)$card_category_id . "'");
			}
		}
		

		$this->cache->delete('card_category');
	}
	
	public function editCardCategory($card_category_id, $data) {
		$this->db->query("UPDATE " . DB_PREFIX . "card_category SET sort_order = '" . (int)$data['sort_order'] . "', date_modified = NOW() WHERE card_category_id = '" . (int)$card_category_id . "'");

		$this->db->query("DELETE FROM " . DB_PREFIX . "card_category_description WHERE card_category_id = '" . (int)$card_category_id . "'");

		if (isset($data['image'])) {
			$this->db->query("UPDATE " . DB_PREFIX . "card_category SET image = '" . $this->db->escape(html_entity_decode($data['image'], ENT_QUOTES, 'UTF-8')) . "' WHERE card_category_id = '" . (int)$card_category_id . "'");
		}

		foreach ($data['card_category_description'] as $language_id => $value) {
			$this->db->query("INSERT INTO " . DB_PREFIX . "card_category_description SET card_category_id = '" . (int)$card_category_id . "', language_id = '" . (int)$language_id . "', name = '" . $this->db->escape($value['name']) . "', description = '" . $this->db->escape($value['description']) . "'");
		}

		$this->db->query("DELETE FROM " . DB_PREFIX . "occasion_to_card_category WHERE card_category_id = '" . (int)$card_category_id . "'");
		
		if (isset($data['card_category_occasion'])) {
			foreach ($data['card_category_occasion'] as $occasion_id) {
				$this->db->query("INSERT INTO " . DB_PREFIX . "occasion_to_card_category SET card_category_id = '" . (int)$card_category_id . "', occasion_id = '" . (int)$occasion_id . "'");
			}		
		}

		$this->cache->delete('card_category');
	}
	
	public function deleteCardCategory($card_category_id) {
		$this->db->query("DELETE FROM " . DB_PREFIX . "card_category WHERE card_category_id = '" . (int)$card_category_id . "'");
		$this->db->query("DELETE FROM " . DB_PREFIX . "card_category_description WHERE card_category_id = '" . (int)$card_category_id . "'");
        $this->db->query("DELETE FROM " . DB_PREFIX . "card_to_category WHERE card_category_id = '" . (int)$card_category_id . "'");
        $this->db->query("DELETE FROM " . DB_PREFIX . "occasion_to_card_category WHERE card_category_id = '" . (int)$card_category_id . "'");

		$this->cache->delete('card_category');
	} 

	public function getCardCategory($card_category_id) {
		$query = $this->db->query("SELECT DISTINCT *, (SELECT keyword FROM " . DB_PREFIX . "url_alias WHERE query = 'card_category_id=" . (int)$card_category_id . "') AS keyword FROM " . DB_PREFIX . "card_category WHERE card_category_id = '" . (int)$card_category_id . "'");
		
		return $query->row;
	} 
	
	public function getCardCategories() {
		$card_category_data = $this->cache->get('card_category.' . (int)$this->config->get('config_language_id'));
	
		if (!$card_category_data) {
			$card_category_data = array();
		
			$query = $this->db->query("SELECT * FROM " . DB_PREFIX . "card_category c LEFT JOIN " . DB_PREFIX . "card_category_description cd ON (c.card_category_id = cd.card_category_id) WHERE cd.language_id = '" . (int)$this->config->get('config_language_id') . "' ORDER BY c.sort_order, cd.name ASC");
		
			foreach ($query->rows as $result) {
				$card_category_data[] = array(
					'card_category_id' => $result['card_category_id'],
					'name'        => $result['name'], //$this->getPath($result['card_category_id'], $this->config->get('config_language_id')),
					'image'  	  => $result['image'],
                    'description' => $result['description'],
					'sort_order'  => $result['sort_order']
				);
                
                //$card_category_data = array_merge($card_category_data, $this->getCard_Categorys($result['card_category_id']));
			}	
	
			$this->cache->set('card_category.' . (int)$this->config->get('config_language_id'), $card_category_data);
		}
		
		return $card_category_data;
	}
    
	public function getCardCategoryDescriptions($card_category_id) {
		$card_category_description_data = array();
		
		$query = $this->db->query("SELECT * FROM " . DB_PREFIX . "card_category_description WHERE card_category_id = '" . (int)$card_category_id . "'");
		
		foreach ($query->rows as $result) {
			$card_category_description_data[$result['language_id']] = array(
				'name'             => $result['name'],
				'description'      => $result['description']
			);
		}
		
		return $card_category_description_data;
	}	
	
		
	public function getTotalCardCategories() {
      	$query = $this->db->query("SELECT COUNT(*) AS total FROM " . DB_PREFIX . "card_category");
		
		return $query->row['total'];
	}	

	public function getCategoryOccasions($card_category_id) {
		$data = array();
		
		$query = $this->db->query("SELECT * FROM " . DB_PREFIX . "occasion_to_card_category WHERE card_category_id = '" . (int)$card_category_id . "'");
		
		foreach ($query->rows as $result) {
			$data[] = $result['occasion_id'];
		}

		return $data;
	}
  

}
?>