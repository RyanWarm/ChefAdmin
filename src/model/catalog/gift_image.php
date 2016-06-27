<?php
class ModelCatalogGiftImage extends Model {
	public function add($data) {
		$this->db->query("INSERT INTO gift_image SET name = '" . $this->db->escape($data['name']) . "', wish = '" . $this->db->escape($data['wish']) . "'");
        
		$image_id = $this->db->getLastId();
        
        if (isset($data['image'])) {
            $this->db->query("UPDATE gift_image SET image = '" . $this->db->escape($data['image']) . "' WHERE image_id = '" . (int)$image_id . "'");
        }

        if (isset($data['animate'])) {
            $this->db->query("UPDATE gift_image SET animate = '" . $this->db->escape($data['animate']) . "' WHERE image_id = '" . (int)$image_id . "'");
        }

        /*
        if (isset($data['orig_image'])) {
            $this->resizeCardImage($card_id, $data);
        }
        */

        if (isset($data['product_tag'])) {
			foreach ($data['product_tag'] as $tag_id) {
				$this->db->query("INSERT INTO gift_image_to_tag SET image_id = '" . (int)$image_id . "', tag_id = '" . (int)$tag_id . "'");
			}
		}

        if (isset($data['gift_image_category'])) {
			foreach ($data['gift_image_category'] as $category_id) {
				$this->db->query("INSERT INTO gift_image_to_category SET image_id = '" . (int)$image_id . "', category_id = '" . (int)$category_id . "'");
			}
		}

		$this->cache->delete('gift_image');
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

	public function edit($image_id, $data) {
		$this->db->query("UPDATE gift_image SET  name = '" . $this->db->escape($data['name']) . "', wish = '" . $this->db->escape($data['wish']) . "' WHERE image_id = '" . (int)$image_id . "'");
        
        if (isset($data['image'])) {
            $this->db->query("UPDATE gift_image SET image = '" . $this->db->escape($data['image']) . "' WHERE image_id = '" . (int)$image_id . "'");
        }

        if (isset($data['animate'])) {
            $this->db->query("UPDATE gift_image SET animate = '" . $this->db->escape($data['animate']) . "' WHERE image_id = '" . (int)$image_id . "'");
        }

        /*
		$this->db->query("DELETE FROM " . DB_PREFIX . "card_to_category WHERE card_id = '" . (int)$card_id . "'");
		
		if (isset($data['the_card_category'])) {
			foreach ($data['the_card_category'] as $category_id) {
				$this->db->query("INSERT INTO " . DB_PREFIX . "card_to_category SET card_id = '" . (int)$card_id . "', card_category_id = '" . (int)$category_id . "'");
			}		
		}
		*/

		$this->db->query("DELETE FROM " . DB_PREFIX . "gift_image_to_tag WHERE image_id = '" . (int)$image_id . "'");
		
		if (isset($data['product_tag'])) {
            $query_string = "INSERT INTO gift_image_to_tag (image_id, tag_id) values ";
			foreach ($data['product_tag'] as $tag_id) {
                $query_string .= "('" . (int)$image_id . "', '" . (int)$tag_id . "'), ";
            }
            if (count($data['product_tag']) > 0 ) {
                $query_string = substr($query_string, 0, strlen($query_string) - 2 );
                $this->db->query($query_string);
            }
					
		}

		$this->db->query("DELETE FROM " . DB_PREFIX . "gift_image_to_category WHERE image_id = '" . (int)$image_id . "'");
		
		if (isset($data['gift_image_category'])) {
            $query_string = "INSERT INTO gift_image_to_category (image_id, category_id) values ";
			foreach ($data['gift_image_category'] as $category_id) {
                $query_string .= "('" . (int)$image_id . "', '" . (int)$category_id . "'), ";
            }
            if (count($data['gift_image_category']) > 0 ) {
                $query_string = substr($query_string, 0, strlen($query_string) - 2 );
                $this->db->query($query_string);
            }
					
		}

		$this->cache->delete('gift_image');
	}
	
	public function delete($image_id) {
		$this->db->query("DELETE FROM gift_image WHERE image_id = '" . (int)$image_id . "'");

		$this->db->query("DELETE FROM gift_image_to_tag WHERE image_id = '" . (int)$image_id . "'");

		$this->db->query("DELETE FROM gift_image_to_category WHERE image_id = '" . (int)$image_id . "'");
        
		$this->cache->delete('gift_image');
	} 

	public function getGiftImage($image_id) {
		$query = $this->db->query("SELECT * FROM gift_image WHERE image_id = '" . (int)$image_id . "'");
		
		return $query->row;
	} 
	
	public function getGiftImages($params = array()) {

        $output = array();
        
        $sql = "SELECT g.*, GROUP_CONCAT(td.name) as tag_name FROM ( SELECT g2.*, GROUP_CONCAT(c.name) as category_name FROM gift_image g2 LEFT JOIN gift_image_to_category gc ON (g2.image_id = gc.image_id) LEFT JOIN gift_image_category c ON (gc.category_id = c.category_id) ";

        if (!empty($params['filter_category']) && $params['filter_category'] != '*' ) {
            $sql .= " WHERE c.category_id = '" . (int)$params['filter_category'] . "' ";
        }
        
        $sql .= " GROUP BY g2.image_id";

        $sql .= ") as g LEFT JOIN gift_image_to_tag gt ON (g.image_id = gt.image_id) LEFT JOIN tag_description td ON (gt.tag_id = td.tag_id) ";

        //$sql = "SELECT g.*, GROUP_CONCAT(c.name) as category_name  FROM gift_image g LEFT JOIN gift_image_to_category gc ON (g.image_id = gc.image_id) LEFT JOIN gift_image_category c ON (gc.category_id = c.category_id) ";

        $sql .= " WHERE TRUE ";

        if (!empty($params['filter_name'])) {
            $sql .= " AND LCASE(g.name) LIKE '%" . $this->db->escape($params['filter_name']) . "%'";
        }

        $sql .= " GROUP BY g.image_id";

        $sql .= " ORDER BY g.image_id DESC";

        if (isset($params['start']) || isset($params['limit'])) {
            if ($params['start'] < 0) {
                $params['start'] = 0;
            }				
            
            if ($params['limit'] < 1) {
                $params['limit'] = 20;
            }
			
            $sql .= " LIMIT " . (int)$params['start'] . "," . (int)$params['limit'];
        }	
        
        $query = $this->db->query($sql);

        foreach ($query->rows as $result) {
            $output[] = array(
                              'image_id' => $result['image_id'],
                              'name'        => $result['name'], //$this->getPath($result['card_id'], $this->config->get('config_language_id')),
                              'image'       => $result['image'],
                              'animate'     => $result['animate'],
                              'score'       => $result['score'],
                              'tag_name'    => $result['tag_name'],
                              'category_name' => $result['category_name'],
                              'wish'        => $result['wish']
                              );
            
            //$this->resizeCardImage($result['card_id'], $result);            
        }
    	
		return $output;
	}
    
	public function getTotalGiftImages($params = array()) {
        $sql = "SELECT COUNT(*) as total FROM ( SELECT COUNT(*) as total FROM ( SELECT g2.*, GROUP_CONCAT(c.name) as category_name FROM gift_image g2 LEFT JOIN gift_image_to_category gc ON (g2.image_id = gc.image_id) LEFT JOIN gift_image_category c ON (gc.category_id = c.category_id)";

        if (!empty($params['filter_category']) && $params['filter_category'] != '*' ) {
            $sql .= " WHERE c.category_id = '" . (int)$params['filter_category'] . "' ";
        }
        
        $sql .= " GROUP BY g2.image_id";

        $sql .= ") as g LEFT JOIN gift_image_to_tag gt ON (g.image_id = gt.image_id) LEFT JOIN tag_description td ON (gt.tag_id = td.tag_id) ";

        $sql .= " WHERE TRUE ";

        if (!empty($params['filter_name'])) {
            $sql .= " AND LCASE(g.name) LIKE '%" . $this->db->escape($params['filter_name']) . "%'";
        }

        $sql .= " GROUP BY g.image_id";

        $sql .= ") as g3";

      	$query = $this->db->query($sql);



		return $query->row['total'];
	}	

	public function getProductTags($image_id) {
		$product_tag_data = array();
		
		$query = $this->db->query("SELECT * FROM " . DB_PREFIX . "gift_image_to_tag WHERE image_id = '" . (int)$image_id . "'");
		
		foreach ($query->rows as $result) {
			$product_tag_data[] = $result['tag_id'];
		}

		return $product_tag_data;
	}

	public function getGiftImageCategories($image_id) {
		$data = array();
		
		$query = $this->db->query("SELECT * FROM gift_image_to_category WHERE image_id = '" . (int)$image_id . "'");
		
		foreach ($query->rows as $result) {
			$data[] = $result['category_id'];
		}

		return $data;
	}


}
?>