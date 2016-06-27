<?php
class ModelCompanyJob extends Model {
	public function add($data) {
		$this->db->query("INSERT INTO job_info SET name = '" . $this->db->escape($data['name']) . "', wish = '" . $this->db->escape($data['wish']) . "'");
	}
	
	public function edit($id, $data) {
		$updateStr = "UPDATE job_info SET company_id = '" . $this->db->escape($data['company_id']) . "'";
		
		if( !empty($data['title']) ){
			$updateStr .= ", title = '" . $this->db->escape($data['title']) . "'";
		}
		if( !empty($data['location']) ){
			$updateStr .= ", location = '" . $this->db->escape($data['location']) . "'";
		}
		if( !empty($data['work_exp']) ){
			$updateStr .= ", work_exp = '" . $this->db->escape($data['work_exp']) . "'";
		}
		if( !empty($data['management_exp']) ){
			$updateStr .= ", management_exp = '" . $this->db->escape($data['management_exp']) . "'";
		}
		if( !empty($data['edu_background']) ){
			$updateStr .= ", edu_background = '" . $this->db->escape($data['edu_background']) . "'";
		}
		if( !empty($data['headcount']) ){
			$updateStr .= ", headcount = " . $this->db->escape($data['headcount']);
		}
		if( !empty($data['category']) ){
			$updateStr .= ", category = " . $this->db->escape($data['category']);
		}
		if( !empty($data['job_type']) ){
			$updateStr .= ", job_type = '" . $this->db->escape($data['job_type']) . "'";
		}
		if( !empty($data['url']) ){
			$updateStr .= ", url = '" . $this->db->escape($data['url']) . "'";
		}
		if( !empty($data['email']) ){
			$updateStr .= ", email = '" . $this->db->escape($data['email']) . "'";
		}
		if( !empty($data['exp_year_min']) ){
			$updateStr .= ", exp_year_min = " . $this->db->escape($data['exp_year_min']);
		}
		if( !empty($data['salary_min']) ){
			$updateStr .= ", salary_min = " . $this->db->escape($data['salary_min']);
		}
		if( !empty($data['salary_max']) ){
			$updateStr .= ", salary_max = " . $this->db->escape($data['salary_max']);
		}
		if( !empty($data['department']) ){
			$updateStr .= ", department = '" . $this->db->escape($data['department']) . "'";
		}
		if( !empty($data['status']) ){
			$updateStr .= ", status = '" . $this->db->escape($data['status']) . "'";
		}
		if( !empty($data['description']) ){
			$updateStr .= ", description = '" . $this->db->escape($data['description']) . "'";
		}
		$updateStr .= " WHERE id = " . (int)$id;

		$this->db->query($updateStr);
       	}
	
	public function delete($image_id) {
		$this->db->query("DELETE FROM gift_image WHERE image_id = '" . (int)$image_id . "'");

		$this->db->query("DELETE FROM gift_image_to_tag WHERE image_id = '" . (int)$image_id . "'");

		$this->db->query("DELETE FROM gift_image_to_category WHERE image_id = '" . (int)$image_id . "'");
        
		$this->cache->delete('gift_image');
	} 

	public function getItem($id) {
        	$query = $this->db->query("SELECT * FROM job_info WHERE id = '" .$this->db->escape($id) . "'");
        
        	return $query->row;
    	}

	public function getStatusTypes(){
		$query = $this->db->query("SHOW COLUMNS FROM job_info LIKE 'status'");
		
		$enum = $query->row['Type'];

		$enum_arr = explode("('",$enum);
    		$enum = $enum_arr[1];
    		$enum_arr = explode("')",$enum);
    		$enum = $enum_arr[0];
    		$enum_arr = explode("','",$enum);
	
		return $enum_arr;
	}

	private function createQueryString($params, $for_count) {
		if ($for_count) {
		    $sql = "SELECT count(*) as total ";
		} else {
		    $sql = "SELECT ji.*, ci.normalized_name, jrt.role_name";
		}

		$sql .= " FROM job_info ji LEFT JOIN company_info ci ON (ji.company_id=ci.id) LEFT JOIN job_role_tax jrt ON (ji.category=jrt.id)  WHERE TRUE"; 

		if (!empty($params['filter_id'])) {
		    $sql .= " AND LCASE(ji.id) LIKE '%" . $this->db->escape($params['filter_id']) . "%'";
		}

		if (!empty($params['filter_name']) && $params['filter_name'] != 'all') {
		    $sql .= " AND LCASE(title) LIKE '%" . $this->db->escape($params['filter_name']) . "%'";
		}

		if (!empty($params['filter_companyName'])) {
		    $sql .= " AND LCASE(ci.normalized_name) LIKE '%" . $this->db->escape($params['filter_companyName']) . "%'";
		}

		if (!empty($params['filter_date'])) {
		    $sql .= " AND LCASE(create_date) LIKE '%" . $this->db->escape($params['filter_date']) . "%'";
		}

		if (!empty($params['filter_edu'])) {
		    $sql .= " AND LCASE(edu_background) LIKE '%" . $this->db->escape($params['filter_edu']) . "%'";
		}

		if (!empty($params['filter_type'])) {
		    $sql .= " AND LCASE(job_type) LIKE '%" . $this->db->escape($params['filter_type']) . "%'";
		}

		if (!empty($params['filter_role'])) {
		    $sql .= " AND LCASE(jrt.role_name) LIKE '%" . $this->db->escape($params['filter_role']) . "%'";
		}

		if (!empty($params['filter_location'])) {
		    $sql .= " AND LCASE(ji.location) LIKE '%" . $this->db->escape($params['filter_location']) . "%'";
		}

		if (empty($params['sort'])) {
		    $sql .= " ORDER BY ji.id ASC";
		}


		if (!$for_count) {
		    if (isset($params['start']) || isset($params['limit'])) {
			if ($params['start'] < 0) {
			    $params['start'] = 0;
			}				
			
			if ($params['limit'] < 1) {
			    $params['limit'] = 20;
			}
			
			$sql .= " LIMIT " . (int)$params['start'] . "," . (int)$params['limit'];
		    }
		}	

		return $sql;

	}

	public function getList($params = array()) {
		$sql = $this->createQueryString($params, FALSE);

        	$query = $this->db->query($sql);

		return $query->rows;
	}
    
	public function getTotalCount($params = array()) {
		$sql = $this->createQueryString($params, TRUE);

		$query = $this->db->query($sql);

		return $query->row['total'];
	}	


}
?>
