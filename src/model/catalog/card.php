<?php
class ModelCatalogCard extends Model {
	public function addCard($data) {
		$this->db->query("INSERT INTO " . DB_PREFIX . "card SET sort_order = '" . (int)$data['sort_order'] . "', status = '" . (int)$data['status'] . "', name = '" . $this->db->escape($data['name']) . "', bg_color = '" . $data['bg_color'] . "', default_body = '" . $this->db->escape($data['default_body']) . "', theme_id = '" . $data['theme_id'] . "', date_modified = NOW(), date_added = NOW()");
	
		$card_id = $this->db->getLastId();

        if (isset($data['orig_image'])) {
            $this->resizeCardImage($card_id, $data);
        }

		if (isset($data['the_card_category'])) {
			foreach ($data['the_card_category'] as $category_id) {
				$this->db->query("INSERT INTO " . DB_PREFIX . "card_to_category SET card_id = '" . (int)$card_id . "', card_category_id = '" . (int)$category_id . "'");
			}
		}

        
		$this->cache->delete('card');
	}
	
    private function resizeCardImage($card_id, $data) {
        $this->db->query("UPDATE " . DB_PREFIX . "card SET orig_image = '" . $this->db->escape(html_entity_decode($data['orig_image'], ENT_QUOTES, 'UTF-8')) . "' WHERE card_id = '" . (int)$card_id . "'");

        $this->load->model('tool/image');

        $image = $this->model_tool_image->resize_card_image($data['orig_image'], 320, 413);

        //if (!empty($image)) {
        //error_log("image=" . $image);

        $this->db->query("UPDATE " . DB_PREFIX . "card SET image = '" . $this->db->escape($image) . "' WHERE card_id = '" . (int)$card_id . "'");
            //}
    }

	public function editCard($card_id, $data) {
		$this->db->query("UPDATE " . DB_PREFIX . "card SET sort_order = '" . (int)$data['sort_order'] . "', status = '" . (int)$data['status'] . "', name = '" . $this->db->escape($data['name']) . "', bg_color = '" . $data['bg_color'] . "', default_body = '" . $this->db->escape($data['default_body']) . "', theme_id = '" . $data['theme_id'] . "', date_modified = NOW() WHERE card_id = '" . (int)$card_id . "'");
        

        if (isset($data['orig_image'])) {
            $this->resizeCardImage($card_id, $data);
        }

		$this->db->query("DELETE FROM " . DB_PREFIX . "card_to_category WHERE card_id = '" . (int)$card_id . "'");
		
		if (isset($data['the_card_category'])) {
			foreach ($data['the_card_category'] as $category_id) {
				$this->db->query("INSERT INTO " . DB_PREFIX . "card_to_category SET card_id = '" . (int)$card_id . "', card_category_id = '" . (int)$category_id . "'");
			}		
		}
		
		$this->cache->delete('card');
	}
	
	public function deleteCard($card_id) {
		$this->db->query("DELETE FROM " . DB_PREFIX . "card WHERE card_id = '" . (int)$card_id . "'");
        
		$this->db->query("DELETE FROM " . DB_PREFIX . "card_to_category WHERE card_id = '" . (int)$card_id . "'");

		$this->cache->delete('card');
	} 

	public function getCard($card_id) {
		$query = $this->db->query("SELECT DISTINCT *, (SELECT keyword FROM " . DB_PREFIX . "url_alias WHERE query = 'card_id=" . (int)$card_id . "') AS keyword FROM " . DB_PREFIX . "card WHERE card_id = '" . (int)$card_id . "'");
		
		return $query->row;
	} 
	
	public function getCards() {
		$card_data = $this->cache->get('card');
	
		if (!$card_data) {
			$card_data = array();

			$query = $this->db->query("SELECT c.*, ccd.name as category FROM " . DB_PREFIX . "card c LEFT JOIN card_to_category cc ON c.card_id = cc.card_id LEFT JOIN card_category_description ccd ON cc.card_category_id = ccd.card_category_id ORDER BY c.name ASC");
            
			foreach ($query->rows as $result) {
				$card_data[] = array(
					'card_id' => $result['card_id'],
					'name'        => $result['name'], //$this->getPath($result['card_id'], $this->config->get('config_language_id')),
					'status'  	  => $result['status'],
                    'theme_id'    => $result['theme_id'],
                    'default_body'=> $result['default_body'],
                    'category'    => $result['category'],
					'sort_order'  => $result['sort_order'],
                    'template'    => $result['template'],
                    'bg_color'    => $result['bg_color'],
                    'image'       => $result['image'],
                    'orig_image'  => $result['orig_image']
				);
                
			}	
	
			$this->cache->set('card', $card_data);
		}
		
		return $card_data;
	}
    
	public function getTotalCards() {
      	$query = $this->db->query("SELECT COUNT(*) AS total FROM " . DB_PREFIX . "card");
		
		return $query->row['total'];
	}	
    
    public function getCardCategories($card_id) {
		$data = array();
		
		$query = $this->db->query("SELECT * FROM " . DB_PREFIX . "card_to_category WHERE card_id = '" . (int)$card_id . "'");
		
		foreach ($query->rows as $result) {
			$data[] = $result['card_category_id'];
		}

		return $data;
	}

}
?>