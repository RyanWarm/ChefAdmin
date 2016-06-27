<?php
class ModelCatalogSupplier extends Model {
	public function addSupplier($data) {
		$this->db->query("INSERT INTO " . DB_PREFIX . "supplier SET name = '" . $this->db->escape($data['name']) . "', url = '" . $this->db->escape($data['url']) . "', `contact_person` = '" . $this->db->escape($data['contact_person']) . "', phone = '" . $data['phone'] . "',email = '" . $this->db->escape($data['email']) . "', date_modified = NOW(), date_added = NOW()");
	
		$supplier_id = $this->db->getLastId();
		
		$this->cache->delete('supplier');
	}
	
	public function editSupplier($supplier_id, $data) {
		$this->db->query("UPDATE " . DB_PREFIX . "supplier SET name = '" . $this->db->escape($data['name']) . "', url = '" . $this->db->escape($data['url']) . "', `contact_person` = '" . $this->db->escape($data['contact_person']) . "', phone = '" . $data['phone'] . "',email = '" . $this->db->escape($data['email']) . "', date_modified = NOW() WHERE supplier_id = '" . $supplier_id . "'");

		$this->cache->delete('supplier');
	}
	
	public function deleteSupplier($supplier_id) {
		$this->db->query("DELETE FROM " . DB_PREFIX . "supplier WHERE supplier_id = '" . (int)$supplier_id . "'");

		$this->cache->delete('supplier');
	} 

	public function getSupplier($supplier_id) {
		$query = $this->db->query("SELECT DISTINCT * FROM supplier WHERE supplier_id = '" . (int)$supplier_id . "'");
		
        $supplier_result = $query->row;
        
        if ($supplier_result) {
            $query = $this->db->query("SELECT * FROM supplier_shipping_cost WHERE supplier_id = '" . (int)$supplier_id . "'");
            $supplier_result['shipping_cost'] = $query->rows;
        }
        
        return $supplier_result;
	} 
	
	public function getSuppliers() {
		$data = $this->cache->get('supplier');
	
		if (!$data) {
			$data = array();
		
			$query = $this->db->query("SELECT * FROM " . DB_PREFIX . "supplier ORDER BY name ASC");
		
            
            $data = $query->rows;
			$this->cache->set('supplier', $query->rows);
		}
		
		return $data;
	}
    
}
?>
