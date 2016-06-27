<?php
class ModelCatalogTag extends Model {
	public function addTag($data) {
		$this->db->query("INSERT INTO " . DB_PREFIX . "tag SET parent_id = '" . (int)$data['parent_id'] . "', `top` = '" . (isset($data['top']) ? (int)$data['top'] : 0) . "', `column` = '" . (int)$data['column'] . "', sort_order = '" . (int)$data['sort_order'] . "', status = '" . (int)$data['status'] . "', score = '" . (float)$data['score'] . "', date_modified = NOW(), date_added = NOW()");
	
		$tag_id = $this->db->getLastId();
		
		if (isset($data['image'])) {
			$this->db->query("UPDATE " . DB_PREFIX . "tag SET image = '" . $this->db->escape(html_entity_decode($data['image'], ENT_QUOTES, 'UTF-8')) . "' WHERE tag_id = '" . (int)$tag_id . "'");
		}
		
		foreach ($data['tag_description'] as $language_id => $value) {
			$this->db->query("INSERT INTO " . DB_PREFIX . "tag_description SET tag_id = '" . (int)$tag_id . "', language_id = '" . (int)$language_id . "', name = '" . $this->db->escape($value['name']) . "', meta_keyword = '" . $this->db->escape($value['meta_keyword']) . "', meta_description = '" . $this->db->escape($value['meta_description']) . "', description = '" . $this->db->escape($value['description']) . "'");
		}
		
		if (isset($data['tag_store'])) {
			foreach ($data['tag_store'] as $store_id) {
				$this->db->query("INSERT INTO " . DB_PREFIX . "tag_to_store SET tag_id = '" . (int)$tag_id . "', store_id = '" . (int)$store_id . "'");
			}
		}

		if (isset($data['tag_layout'])) {
			foreach ($data['tag_layout'] as $store_id => $layout) {
				if ($layout['layout_id']) {
					$this->db->query("INSERT INTO " . DB_PREFIX . "tag_to_layout SET tag_id = '" . (int)$tag_id . "', store_id = '" . (int)$store_id . "', layout_id = '" . (int)$layout['layout_id'] . "'");
				}
			}
		}
						
		if ($data['keyword']) {
			$this->db->query("INSERT INTO " . DB_PREFIX . "url_alias SET query = 'tag_id=" . (int)$tag_id . "', keyword = '" . $this->db->escape($data['keyword']) . "'");
		}
		
		$this->cache->delete('tag');
	}
	
	public function editTag($tag_id, $data) {
		$this->db->query("UPDATE " . DB_PREFIX . "tag SET parent_id = '" . (int)$data['parent_id'] . "',`top` = '" . (isset($data['top']) ? (int)$data['top'] : 0) . "', `column` = '" . (int)$data['column'] . "', sort_order = '" . (int)$data['sort_order'] . "', status = '" . (int)$data['status'] . "', score = '" . (float)$data['score'] . "', date_modified = NOW() WHERE tag_id = '" . (int)$tag_id . "'");

		if (isset($data['image'])) {
			$this->db->query("UPDATE " . DB_PREFIX . "tag SET image = '" . $this->db->escape(html_entity_decode($data['image'], ENT_QUOTES, 'UTF-8')) . "' WHERE tag_id = '" . (int)$tag_id . "'");
		}

		$this->db->query("DELETE FROM " . DB_PREFIX . "tag_description WHERE tag_id = '" . (int)$tag_id . "'");

		foreach ($data['tag_description'] as $language_id => $value) {
			$this->db->query("INSERT INTO " . DB_PREFIX . "tag_description SET tag_id = '" . (int)$tag_id . "', language_id = '" . (int)$language_id . "', name = '" . $this->db->escape($value['name']) . "', meta_keyword = '" . $this->db->escape($value['meta_keyword']) . "', meta_description = '" . $this->db->escape($value['meta_description']) . "', description = '" . $this->db->escape($value['description']) . "'");
		}
		
		$this->db->query("DELETE FROM " . DB_PREFIX . "tag_to_store WHERE tag_id = '" . (int)$tag_id . "'");
		
		if (isset($data['tag_store'])) {		
			foreach ($data['tag_store'] as $store_id) {
				$this->db->query("INSERT INTO " . DB_PREFIX . "tag_to_store SET tag_id = '" . (int)$tag_id . "', store_id = '" . (int)$store_id . "'");
			}
		}

		$this->db->query("DELETE FROM " . DB_PREFIX . "tag_to_layout WHERE tag_id = '" . (int)$tag_id . "'");

		if (isset($data['tag_layout'])) {
			foreach ($data['tag_layout'] as $store_id => $layout) {
				if ($layout['layout_id']) {
					$this->db->query("INSERT INTO " . DB_PREFIX . "tag_to_layout SET tag_id = '" . (int)$tag_id . "', store_id = '" . (int)$store_id . "', layout_id = '" . (int)$layout['layout_id'] . "'");
				}
			}
		}
						
		$this->db->query("DELETE FROM " . DB_PREFIX . "url_alias WHERE query = 'tag_id=" . (int)$tag_id. "'");
		
		if ($data['keyword']) {
			$this->db->query("INSERT INTO " . DB_PREFIX . "url_alias SET query = 'tag_id=" . (int)$tag_id . "', keyword = '" . $this->db->escape($data['keyword']) . "'");
		}
		
        $this->db->query("DELETE FROM product_to_tag WHERE tag_id = '" . (int)$tag_id . "'");
        if (isset($data['products'])) {
            $query_string = "INSERT INTO product_to_tag (product_id, tag_id) values ";
			foreach ($data['products'] as $product_id) {
                $query_string .= "('" . (int)$product_id . "', '" . (int)$tag_id . "'), ";
            }
            if (count($data['products']) > 0 ) {
                $query_string = substr($query_string, 0, strlen($query_string) - 2 );
                $this->db->query($query_string);
            }

        }

		$this->cache->delete('tag');
	}
	
	public function deleteTag($tag_id) {
		$this->db->query("DELETE FROM " . DB_PREFIX . "tag WHERE tag_id = '" . (int)$tag_id . "'");
		$this->db->query("DELETE FROM " . DB_PREFIX . "tag_description WHERE tag_id = '" . (int)$tag_id . "'");
		$this->db->query("DELETE FROM " . DB_PREFIX . "tag_to_store WHERE tag_id = '" . (int)$tag_id . "'");
		$this->db->query("DELETE FROM " . DB_PREFIX . "tag_to_layout WHERE tag_id = '" . (int)$tag_id . "'");
		$this->db->query("DELETE FROM " . DB_PREFIX . "url_alias WHERE query = 'tag_id=" . (int)$tag_id . "'");

        
		$query = $this->db->query("SELECT tag_id FROM " . DB_PREFIX . "tag WHERE parent_id = '" . (int)$tag_id . "'");

		foreach ($query->rows as $result) {
			$this->deleteTag($result['tag_id']);
		}

		$this->db->query("DELETE FROM " . DB_PREFIX . "product_to_tag WHERE tag_id = '" . (int)$tag_id . "'");

		$this->db->query("DELETE FROM " . DB_PREFIX . "occasion_to_tag WHERE tag_id = '" . (int)$tag_id . "'");
		
		$this->cache->delete('tag');
	} 

	public function getTag($tag_id) {
		$query = $this->db->query("SELECT DISTINCT *, (SELECT keyword FROM " . DB_PREFIX . "url_alias WHERE query = 'tag_id=" . (int)$tag_id . "') AS keyword FROM " . DB_PREFIX . "tag WHERE tag_id = '" . (int)$tag_id . "'");
		
		return $query->row;
	} 
	
	public function getTags($parent_id = 0) {
		//$tag_data = $this->cache->get('tag.' . (int)$this->config->get('config_language_id'). '.' . (int)$parent_id);
        $tag_data = null;

		if (!$tag_data) {
			$tag_data = array();

			$query = $this->db->query("SELECT DISTINCT c.*, cd.*, count(p.product_id) as product_count FROM " . DB_PREFIX . "tag c LEFT JOIN " . DB_PREFIX . "tag_description cd ON (c.tag_id = cd.tag_id) LEFT JOIN product_to_tag pt ON (c.tag_id = pt.tag_id) LEFT JOIN product p ON (pt.product_id = p.product_id and p.status = 1) WHERE c.parent_id = '" . (int)$parent_id . "' AND cd.language_id = '" . (int)$this->config->get('config_language_id') . "'  group by c.tag_id ORDER BY c.sort_order, cd.name ASC");
            
			foreach ($query->rows as $result) {

				$tag_data[] = array(
					'tag_id' => $result['tag_id'],
					'name'        => $this->getPath($result['tag_id'], $this->config->get('config_language_id')),
                    'product_count' => $result['product_count'],
					'status'  	  => $result['status'],
					'sort_order'  => $result['sort_order']
				);
                
                $tag_data = array_merge($tag_data, $this->getTags($result['tag_id']));
			}	
	
			$this->cache->set('tag.' . (int)$this->config->get('config_language_id') . '.' . (int)$parent_id, $tag_data);
		}
		
		return $tag_data;
	}
    
	public function getPath($tag_id) {
		$query = $this->db->query("SELECT name, parent_id FROM " . DB_PREFIX . "tag c LEFT JOIN " . DB_PREFIX . "tag_description cd ON (c.tag_id = cd.tag_id) WHERE c.tag_id = '" . (int)$tag_id . "' AND cd.language_id = '" . (int)$this->config->get('config_language_id') . "' ORDER BY c.sort_order, cd.name ASC");
		
		if ($query->row['parent_id']) {
			return $this->getPath($query->row['parent_id'], $this->config->get('config_language_id')) . $this->language->get('text_separator') . $query->row['name'];
		} else {
			return $query->row['name'];
		}
	}

	public function getTagDescriptions($tag_id) {
		$tag_description_data = array();
		
		$query = $this->db->query("SELECT * FROM " . DB_PREFIX . "tag_description WHERE tag_id = '" . (int)$tag_id . "'");
		
		foreach ($query->rows as $result) {
			$tag_description_data[$result['language_id']] = array(
				'name'             => $result['name'],
				'meta_keyword'     => $result['meta_keyword'],
				'meta_description' => $result['meta_description'],
				'description'      => $result['description']
			);
		}
		
		return $tag_description_data;
	}	
	
	public function getTagStores($tag_id) {
		$tag_store_data = array();
		
		$query = $this->db->query("SELECT * FROM " . DB_PREFIX . "tag_to_store WHERE tag_id = '" . (int)$tag_id . "'");

		foreach ($query->rows as $result) {
			$tag_store_data[] = $result['store_id'];
		}
		
		return $tag_store_data;
	}

	public function getTagLayouts($tag_id) {
		$tag_layout_data = array();
		
		$query = $this->db->query("SELECT * FROM " . DB_PREFIX . "tag_to_layout WHERE tag_id = '" . (int)$tag_id . "'");
		
		foreach ($query->rows as $result) {
			$tag_layout_data[$result['store_id']] = $result['layout_id'];
		}
		
		return $tag_layout_data;
	}
		
	public function getTotalTags() {
      	$query = $this->db->query("SELECT COUNT(*) AS total FROM " . DB_PREFIX . "tag");
		
		return $query->row['total'];
	}	
		
	public function getTotalTagsByImageId($image_id) {
      	$query = $this->db->query("SELECT COUNT(*) AS total FROM " . DB_PREFIX . "tag WHERE image_id = '" . (int)$image_id . "'");
		
		return $query->row['total'];
	}

	public function getTotalTagsByLayoutId($layout_id) {
		$query = $this->db->query("SELECT COUNT(*) AS total FROM " . DB_PREFIX . "tagy_to_layout WHERE layout_id = '" . (int)$layout_id . "'");

		return $query->row['total'];
	}		

    public function getTagProducts($tag_id) {
		$query = $this->db->query("SELECT * FROM " . DB_PREFIX . "product p LEFT JOIN " . DB_PREFIX . "product_description pd ON (p.product_id = pd.product_id) LEFT JOIN " . DB_PREFIX . "product_to_tag p2g ON (p.product_id = p2g.product_id) WHERE pd.language_id = '" . (int)$this->config->get('config_language_id') . "' AND p2g.tag_id = '" . $tag_id . "' ORDER BY p.status DESC, pd.name ASC");

        return $query->rows;
    }

}
?>
