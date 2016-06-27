<?php
class ModelCompanyCompany extends Model {
	public function add($data) {
		$query = "INSERT INTO company_info SET normalized_name='" . $data['normalized_name'] . "' ";

		if (isset($data['name'])) {
		    	$query .= ", name='" . $this->db->escape($data['name']) . "' ";
		}
		
		if(isset($data['image']) && strpos($data['image'], 'uc01_local/') !== false){
                        $link = $this->uploadImage($data['image']);
                        $query .= ", image='" . $this->db->escape($link) . "' ";
                }

		if (isset($data['employer_count'])) {
		    	$query .= ", employer_count='" . $this->db->escape($data['employer_count']) . "' ";
		}

		if (isset($data['follower_count'])) {
		    	$query .= ", follower_count='" . $this->db->escape($data['follower_count']) . "' ";
		}

		if (isset($data['url'])) {
		    	$query .= ", url='" . $this->db->escape($data['url']) . "' ";
		}

		if (isset($data['location'])) {
		    	$query .= ", location='" . $this->db->escape($data['location']) . "' ";
		}

		if (isset($data['category'])) {
		    	$query .= ", category='" . $this->db->escape($data['category']) . "' ";
		}

		if (isset($data['property'])) {
		    	$query .= ", property='" . $this->db->escape($data['property']) . "' ";
		}

		if (isset($data['scale'])) {
		    	$query .= ", scale='" . $this->db->escape($data['scale']) . "' ";
		}

		if (isset($data['cities'])) {
		    	$query .= ", cities='" . $this->db->escape($data['cities']) . "' ";
		}

		if (isset($data['email'])) {
		    	$query .= ", email='" . $this->db->escape($data['email']) . "' ";
		}

		$this->db->query($query);
                $request_id = $this->db->getLastId();

		//insert abstract_company
		$query = "INSERT INTO abstract_company SET normalized_name='" . $data['normalized_name'] . "' ";
		if (isset($data['overview']) && empty($data['overview_abstract'])) {
			$data['overview_abstract'] = $data['overview'];
		}
		if (isset($data['overview_abstract'])) {
		    	$query .= ", overview='" . $this->db->escape($data['overview']) . "' ";
		}
	
		if(isset($link)) {
                        $query .= ", image='" . $this->db->escape($link) . "' ";
                }
		$this->db->query($query);

		return $request_id;
	}
	
	public function uploadImage($localPath) {
		//uploaded new image
		$this->load->model('tool/image');
		$local_url = $this->model_tool_image->resize($localPath, 100, 100, 1);

		//preserve extension of file
		$info = pathinfo($local_url);
		$extension = $info['extension'];
		$md5_tag = md5_file($local_url);
		$final_local_url = $info['dirname'] . "/" . $md5_tag . "." . $extension;

		copy( $local_url, $final_local_url);

		// upload
		$output = null;
		$ch = curl_init('http://42.120.48.230/imagic/upload');

		curl_setopt($ch, CURLOPT_HEADER, 0);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($ch, CURLOPT_POST, true);
		$post = array(
			  "file"=>"@" . $final_local_url,
			  'folder' => 'recruit'
		);
		curl_setopt($ch, CURLOPT_POSTFIELDS, $post);
		$response = curl_exec($ch);

		$ret = json_decode($response, TRUE);
		$output = $ret['image_url'];
		$index = strpos($output, 'recruit/');
		$output = substr($output, $index+8);
		unlink($final_local_url);
			
		return $output;
	}

	public function edit($id, $data) {
		if( !empty($data['image']) && strpos($data['image'], 'recruit/') === false ) { 
			$link = $this->uploadImage($data['image']);	
			if( !empty($link) && $link != '' ) {
				$updateStr = "UPDATE company_info SET image = '" . $link . "' WHERE id = " . (int)$id;
				$this->db->query($updateStr);
			}
		} else if( empty($data['image']) ) {
			//reset image
			$updateStr = "UPDATE company_info SET image = '' WHERE id = " . (int)$id;
			$this->db->query($updateStr);
		}

		$updateStr = "UPDATE company_info SET name = '" . $this->db->escape($data['name']) . "'";
		
		if( !empty($data['employer_count']) ){
			$updateStr .= ", employer_count = " . $this->db->escape($data['employer_count']);
		}
		if( !empty($data['url']) ){
			$updateStr .= ", url = '" . $this->db->escape($data['url']) . "'";
		}
		if( !empty($data['email']) ){
			$updateStr .= ", email = '" . $this->db->escape($data['email']) . "'";
		}
		if( !empty($data['location']) ){
			$updateStr .= ", location = '" . $this->db->escape($data['location']) . "'";
		}
		if( !empty($data['category']) ){
			$updateStr .= ", category = '" . $this->db->escape($data['category']) . "'";
		}
		if( !empty($data['property']) ){
			$updateStr .= ", property = '" . $this->db->escape($data['property']) . "'";
		}
		if( !empty($data['scale']) ){
			$updateStr .= ", scale = '" . $this->db->escape($data['scale']) . "'";
		}
		if( !empty($data['cities']) ){
			$updateStr .= ", cities = '" . $this->db->escape($data['cities']) . "'";
		}
		if( !empty($data['overview']) ){
			$updateStr .= ", overview = '" . $this->db->escape($data['overview']) . "'";
		}
		$updateStr .= " WHERE id = " . (int)$id;

		$this->db->query($updateStr);

		//update overview for abstract_company
		if( !empty($data['normalized_name']) && !empty($data['overview_abstract']) ){
			$query = "SELECT * FROM abstract_company WHERE normalized_name = '" . $data['normalized_name'] . "'";
			$ac = $this->db->query($query);

			if( (empty($link) || $link == '') && $ac->num_rows == 1 ) {
				//try to use existed link
				$link = $ac->row['image'];
			}

			if( $ac->num_rows == 1 ){
				$query = "UPDATE abstract_company SET overview = '" . $data['overview_abstract'] . "', image = '" . $link . "' WHERE normalized_name = '" . $data['normalized_name'] . "'";
				$this->db->query($query);
			}else{
				$query = "INSERT INTO abstract_company (normalized_name,image,overview) values ('" . $data['normalized_name'] . "','" . $link . "','" . $data['overview_abstract'] . "')";
				$this->db->query($query);
			}
		}
       	}
	
	public function delete($image_id) {
		$this->db->query("DELETE FROM gift_image WHERE image_id = '" . (int)$image_id . "'");

		$this->db->query("DELETE FROM gift_image_to_tag WHERE image_id = '" . (int)$image_id . "'");

		$this->db->query("DELETE FROM gift_image_to_category WHERE image_id = '" . (int)$image_id . "'");
        
		$this->cache->delete('gift_image');
	} 

	public function getItem($id) {
        	$query = $this->db->query("SELECT ci.*, ac.overview as overview_abstract FROM company_info ci LEFT JOIN abstract_company ac ON (ci.normalized_name=ac.normalized_name) WHERE ci.id = '" .$this->db->escape($id) . "'");

        	return $query->row;
    	}

	public function getGiftImage($image_id) {
		$query = $this->db->query("SELECT * FROM gift_image WHERE image_id = '" . (int)$image_id . "'");
		
		return $query->row;
	} 
	
	private function createQueryString($params, $for_count) {
		if ($for_count) {
		    $sql = "SELECT count(*) as total ";
		} else {
		    $sql = "SELECT *";
		}

		$sql .= " FROM company_info WHERE TRUE"; 

		if (!empty($params['filter_id'])) {
		    $sql .= " AND LCASE(id) LIKE '%" . $this->db->escape($params['filter_id']) . "%'";
		}

		if (!empty($params['filter_name']) && $params['filter_name'] != 'all') {
		    $sql .= " AND LCASE(name) LIKE '%" . $this->db->escape($params['filter_name']) . "%'";
		}

		if (!empty($params['filter_location'])) {
		    $sql .= " AND LCASE(location) LIKE '%" . $this->db->escape($params['filter_location']) . "%'";
		}

		if (!empty($params['filter_domain'])) {
		    $sql .= " AND LCASE(category) LIKE '%" . $this->db->escape($params['filter_domain']) . "%'";
		}

		if (!empty($params['filter_category'])) {
		    $sql .= " AND LCASE(property) LIKE '%" . $this->db->escape($params['filter_category']) . "%'";
		}

		if (!empty($params['filter_cities'])) {
		    $sql .= " AND LCASE(cities) LIKE '%" . $this->db->escape($params['filter_cities']) . "%'";
		}

		if (empty($params['sort'])) {
		    $sql .= " ORDER BY id ASC";
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
