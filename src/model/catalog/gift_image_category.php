<?php
class ModelCatalogGiftImageCategory extends Model {
	public function add($data) {
		$this->db->query("INSERT INTO gift_image_category SET name = '" . $this->db->escape($data['name']) . "', sort_order = '" . $this->db->escape($data['sort_order']) . "'");
        
		$id = $this->db->getLastId();
        
	}
	
	public function edit($id, $data) {
		$this->db->query("UPDATE gift_image_category SET  name = '" . $this->db->escape($data['name']) . "', sort_order = '" . $this->db->escape($data['sort_order']) . "' WHERE category_id = '" . (int)$id . "'");
        
        $this->db->query("DELETE FROM gift_image_to_category WHERE category_id = '" . (int)$id . "'");
        if (isset($data['gift_images'])) {
            $query_string = "INSERT INTO gift_image_to_category (image_id, category_id) values ";
			foreach ($data['gift_images'] as $image_id) {
                $query_string .= "('" . (int)$image_id . "', '" . (int)$id . "'), ";
            }
            if (count($data['gift_images']) > 0 ) {
                $query_string = substr($query_string, 0, strlen($query_string) - 2 );
                $this->db->query($query_string);
            }

        }

	}
	
	public function delete($id) {
		$this->db->query("DELETE FROM gift_image_category WHERE category_id = '" . (int)$id . "'");

		$this->db->query("DELETE FROM gift_image_to_category WHERE category_id = '" . (int)$id . "'");
        
	} 

	public function getCategory($id) {
		$query = $this->db->query("SELECT * FROM gift_image_category WHERE category_id = '" . (int)$id . "'");
		
		return $query->row;
	} 
	
	public function getCategories() {

        $data = array();
        
        $query = $this->db->query("SELECT c.*  FROM gift_image_category c ORDER BY c.sort_order, c.name ASC");
         
        //$query = $this->db->query("SELECT c.* FROM gift_image c ORDER BY c.name ASC ");
        /*
        foreach ($query->rows as $result) {
            $data[] = array(
                            'category_id' => $result['category_id'],
                            'name'        => $result['name'], //$this->getPath($result['card_id'], $this->config->get('config_language_id')),
                            'sort_order'       => $result['sort_order']
                            );
            
        }
    	*/
	
		return $query->rows;
	}
    
	public function getTotalCategories() {
      	$query = $this->db->query("SELECT COUNT(*) AS total FROM gift_image_category");
		
		return $query->row['total'];
	}	

	public function getGiftImagesByCategory($category_id) {
		$data = array();
		
		$query = $this->db->query("SELECT g.* FROM gift_image g LEFT JOIN gift_image_to_category gc ON (g.image_id = gc.image_id) WHERE gc.category_id = '" . (int)$category_id . "' ORDER BY g.name ASC ");
		
		return $query->rows;
	}

}
?>