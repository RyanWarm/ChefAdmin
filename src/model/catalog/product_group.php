<?php
class ModelCatalogProductGroup extends Model {
	public function addProductGroup($data) {
		$this->db->query("INSERT INTO " . DB_PREFIX . "product_group SET sort_order = '" . (int)$data['sort_order'] . "', let_them_choose = '" . (int)$data['let_them_choose'] . "', date_modified = NOW(), date_added = NOW()");

        $this->load->model('tool/image');
	
		$product_group_id = $this->db->getLastId();

		if (isset($data['orig_image'])) {
			$this->db->query("UPDATE " . DB_PREFIX . "product_group SET orig_image = '" . $this->db->escape(html_entity_decode($data['orig_image'], ENT_QUOTES, 'UTF-8')) . "' WHERE product_group_id = '" . (int)$product_group_id . "'");

            $image = $this->model_tool_image->resize_product_image($data['orig_image'], 800, 800);

			$this->db->query("UPDATE " . DB_PREFIX . "product_group SET image = '" . $this->db->escape(html_entity_decode($image, ENT_QUOTES, 'UTF-8')) . "' WHERE product_group_id = '" . (int)$product_group_id . "'");

            $mid_image = $this->model_tool_image->resize_product_image($data['orig_image'], 300, 300);

            if (!empty($mid_image)) {
                $this->db->query("UPDATE " . DB_PREFIX . "product_group SET mid_image = '" . $this->db->escape($mid_image) . "' WHERE product_group_id = '" . (int)$product_group_id . "'");
            }
                
            $small_image = $this->model_tool_image->resize_product_image($data['orig_image'], 150, 150);
            if (!empty($small_image)) {
                $this->db->query("UPDATE " . DB_PREFIX . "product_group SET small_image = '" . $this->db->escape($small_image) . "' WHERE product_group_id = '" . (int)$product_group_id . "'");
            }

		}
        
		foreach ($data['product_group_description'] as $language_id => $value) {
			$this->db->query("INSERT INTO " . DB_PREFIX . "product_group_description SET product_group_id = '" . (int)$product_group_id . "', language_id = '" . (int)$language_id . "', name = '" . $this->db->escape($value['name']) . "', description = '" . $this->db->escape($value['description']) . "', sexy_name = '" . $this->db->escape($value['sexy_name']) . "'");

		}

		$this->cache->delete('product_group');
	}

    private function resizeProductImage($product_group_id, $data) {
        $this->db->query("UPDATE " . DB_PREFIX . "product_group SET orig_image = '" . $this->db->escape(html_entity_decode($data['orig_image'], ENT_QUOTES, 'UTF-8')) . "' WHERE product_group_id = '" . (int)$product_group_id . "'");

        $image = $this->model_tool_image->resize_product_image($data['orig_image'], 800, 800);

        $this->db->query("UPDATE " . DB_PREFIX . "product_group SET image = '" . $this->db->escape(html_entity_decode($image, ENT_QUOTES, 'UTF-8')) . "' WHERE product_group_id = '" . (int)$product_group_id . "'");

        $mid_image = $this->model_tool_image->resize_product_image($data['orig_image'], 300, 300);
        if (!empty($mid_image)) {
            $this->db->query("UPDATE " . DB_PREFIX . "product_group SET mid_image = '" . $this->db->escape($mid_image) . "' WHERE product_group_id = '" . (int)$product_group_id . "'");
        }
        
        $small_image = $this->model_tool_image->resize_product_image($data['orig_image'], 150, 150);
        if (!empty($small_image)) {
            $this->db->query("UPDATE " . DB_PREFIX . "product_group SET small_image = '" . $this->db->escape($small_image) . "' WHERE product_group_id = '" . (int)$product_group_id . "'");
        }
    
    }

	public function editProductGroup($product_group_id, $data) {
        $this->load->model('tool/image');

		$this->db->query("UPDATE " . DB_PREFIX . "product_group SET sort_order = '" . (int)$data['sort_order'] . "', let_them_choose = '" . $data['let_them_choose'] . "', date_modified = NOW() WHERE product_group_id = '" . (int)$product_group_id . "'");

		$this->db->query("DELETE FROM " . DB_PREFIX . "product_group_description WHERE product_group_id = '" . (int)$product_group_id . "'");

		if (isset($data['orig_image'])) {
            $this->resizeProductImage($product_group_id, $data);
		}

		foreach ($data['product_group_description'] as $language_id => $value) {
			$this->db->query("INSERT INTO " . DB_PREFIX . "product_group_description SET product_group_id = '" . (int)$product_group_id . "', language_id = '" . (int)$language_id . "', name = '" . $this->db->escape($value['name']) . "', description = '" . $this->db->escape($value['description']) . "', sexy_name = '" . $this->db->escape($value['sexy_name']) . "'");
		}

		$this->cache->delete('product_group');
	}
	
	public function deleteProductGroup($product_group_id) {
		$this->db->query("DELETE FROM " . DB_PREFIX . "product_group WHERE product_group_id = '" . (int)$product_group_id . "'");
		$this->db->query("DELETE FROM " . DB_PREFIX . "product_group_description WHERE product_group_id = '" . (int)$product_group_id . "'");
        $this->db->query("DELETE FROM " . DB_PREFIX . "product_to_group WHERE product_group_id = '" . (int)$product_group_id . "'");

		$this->cache->delete('product_group');
	} 

	public function getProductGroup($product_group_id) {
		$query = $this->db->query("SELECT DISTINCT *, (SELECT keyword FROM " . DB_PREFIX . "url_alias WHERE query = 'product_group_id=" . (int)$product_group_id . "') AS keyword FROM " . DB_PREFIX . "product_group WHERE product_group_id = '" . (int)$product_group_id . "'");
		
		return $query->row;
	} 
	
	public function getProductGroups() {
		$product_group_data = $this->cache->get('product_group.' . (int)$this->config->get('config_language_id'));
	
		if (!$product_group_data) {
			$product_group_data = array();
		
			$query = $this->db->query("SELECT c.*, cd.*, count(p.product_id) as product_count FROM " . DB_PREFIX . "product_group c LEFT JOIN " . DB_PREFIX . "product_group_description cd ON (c.product_group_id = cd.product_group_id) LEFT JOIN product_to_group pg ON (c.product_group_id = pg.product_group_id) LEFT JOIN product p ON (pg.product_id = p.product_id AND p.status = 1)  WHERE cd.language_id = '" . (int)$this->config->get('config_language_id') . "' GROUP BY c.product_group_id ORDER BY c.product_group_id DESC");
		
			foreach ($query->rows as $result) {
				$product_group_data[] = array(
					'product_group_id' => $result['product_group_id'],
					'name'        => $result['name'], //$this->getPath($result['product_group_id'], $this->config->get('config_language_id')),
                    'orig_image'  => $result['orig_image'],
					'image'  	  => $result['image'],
					'sort_order'  => $result['sort_order'],
                    'product_count' => $result['product_count'],
                    'let_them_choose' => $result['let_them_choose']
				);
                
                //$this->load->model('tool/image');
                //$this->resizeProductImage($result['product_group_id'], $result);
                

                //$product_group_data = array_merge($product_group_data, $this->getProduct_Groups($result['product_group_id']));
			}	
	
			$this->cache->set('product_group.' . (int)$this->config->get('config_language_id'), $product_group_data);
		}
		
		return $product_group_data;
	}
    
	public function getProductGroupDescriptions($product_group_id) {
		$product_group_description_data = array();
		
		$query = $this->db->query("SELECT * FROM " . DB_PREFIX . "product_group_description WHERE product_group_id = '" . (int)$product_group_id . "'");
		
		foreach ($query->rows as $result) {
			$product_group_description_data[$result['language_id']] = array(
				'name'             => $result['name'],
				'description'      => $result['description'],
                'sexy_name'        => $result['sexy_name']
			);
		}
		
		return $product_group_description_data;
	}	
	
		
	public function getTotalProductGroups() {
      	$query = $this->db->query("SELECT COUNT(*) AS total FROM " . DB_PREFIX . "product_group");
		
		return $query->row['total'];
	}	
  
    public function getProductGroupProducts($product_group_id) {
        $product_group_data = array();
		
		$query = $this->db->query("SELECT * FROM " . DB_PREFIX . "product p LEFT JOIN " . DB_PREFIX . "product_description pd ON (p.product_id = pd.product_id) LEFT JOIN " . DB_PREFIX . "product_to_group p2g ON (p.product_id = p2g.product_id) WHERE pd.language_id = '" . (int)$this->config->get('config_language_id') . "' AND p2g.product_group_id = '" . $product_group_id . "' ORDER BY p.status DESC, pd.name ASC");
		
		foreach ($query->rows as $result) {
			$product_group_data[] = $result;
		}

		return $product_group_data;

    }


}
?>