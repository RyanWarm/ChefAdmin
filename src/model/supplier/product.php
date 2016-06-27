<?php

class ModelSupplierProduct extends Model {
    public function getProducts($data) {
        $sql = "SELECT p.*, pd.*, m.name as manufacturer_name, ps.quantity, ps.hold_quantity, ps.ordered_quantity, s.name as supplier FROM product p LEFT JOIN product_description pd ON (p.product_id = pd.product_id) LEFT JOIN product_supplier ps ON (p.product_id = ps.product_id) LEFT JOIN supplier s ON (ps.supplier_id = s.supplier_id) LEFT JOIN manufacturer m ON (p.manufacturer_id = m.manufacturer_id) ";
        
        $sql .= " WHERE ps.supplier_id = '" . (int)$data['supplier_id'] . "'";
        
        $sql .= " AND pd.language_id = 2 ";
		
        if (!empty($data['filter_name'])) {            
            $sql .= " AND LCASE(pd.name) LIKE '%" . $this->db->escape($data['filter_name']) . "%'";
        }
        
        if (!empty($data['filter_price'])) {
            $sql .= " AND p.price LIKE '" . $this->db->escape($data['filter_price']) . "%'";
        }
        
        if (isset($data['filter_status']) && !is_null($data['filter_status'])) {
            $sql .= " AND p.status = '" . (int)$data['filter_status'] . "'";
        }

        
        $sql .= " GROUP BY p.product_id";
		
        $sort_data = array(
                           'p.product_id',
                           'pd.name',
                           'p.model',
                           'p.category',
                           'p.price',
                           'ps.quantity',
                           'ps.hold_quantity',
                           'ps.ordered_quantity',
                           's.name',
                           'p.status',
                           'p.sort_order',
                           'p.date_modified'
                           );	
        
        if (isset($data['sort']) && in_array($data['sort'], $sort_data)) {
            $sql .= " ORDER BY " . $data['sort'];	
        } else {
            $data['sort'] = 'p.product_id';
            $sql .= " ORDER BY p.product_id";	
        }
		
        if (isset($data['order']) ) {
            $sql .= " " . $data['order'];
        } else {
            if ($data['sort'] == 'p.product_id')
                $sql .= " DESC";
            else
                $sql .= " ASC";
        }
		
        if (isset($data['start']) || isset($data['limit'])) {
            if ($data['start'] < 0) {
                $data['start'] = 0;
            }				

            if ($data['limit'] < 1) {
                $data['limit'] = 20;
            }	
			
            $sql .= " LIMIT " . (int)$data['start'] . "," . (int)$data['limit'];
        }	
        
        //error_log("query=" . $sql);

        $query = $this->db->query($sql);
        
        $results = $query->rows;
        
        return $results;
    }

    public function getTotalProducts($data) {
        $sql = "SELECT COUNT(*) as total FROM product p LEFT JOIN product_description pd ON (p.product_id = pd.product_id) LEFT JOIN product_supplier ps ON (p.product_id = ps.product_id) LEFT JOIN supplier s ON (ps.supplier_id = s.supplier_id) LEFT JOIN manufacturer m ON (p.manufacturer_id = m.manufacturer_id) ";
        
        $sql .= " WHERE ps.supplier_id = '" . (int)$data['supplier_id'] . "'";

        $sql .= " AND pd.language_id = 2 ";

        if (!empty($data['filter_name'])) {            
            $sql .= " AND LCASE(pd.name) LIKE '%" . $this->db->escape($data['filter_name']) . "%'";
        }
        
        if (!empty($data['filter_price'])) {
            $sql .= " AND p.price LIKE '" . $this->db->escape($data['filter_price']) . "%'";
        }
        
        if (isset($data['filter_status']) && !is_null($data['filter_status'])) {
            $sql .= " AND p.status = '" . (int)$data['filter_status'] . "'";
        }
        
        $query = $this->db->query($sql);
        
        return $query->row['total'];
    }

	public function editProduct($product_id, $data) {
        $this->load->model('tool/image');

        // $this->db->query("UPDATE product SET 

		if (isset($data['product_supplier']) && $data['product_supplier']['supplier_id'] != 0 ) {
            $value = $data['product_supplier'];
			$this->db->query("UPDATE product_supplier SET quantity = '" . (int)$value['quantity'] . "', date_modified = NOW() WHERE product_id = '" . (int)$product_id . "' AND supplier_id = '" . (int)$data['product_supplier']['supplier_id'] . "'");
		}

    }

    public function hasPermission($product_id, $supplier_id) {
        $query = $this->db->query("SELECT * FROM product_supplier WHERE product_id = '" . (int)$product_id . "' AND supplier_id = '" . (int)$supplier_id . "'");
        if ($query->row)
            return true;
        else
            return false;
    }

    
}

?>