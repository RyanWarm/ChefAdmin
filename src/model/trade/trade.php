<?php
class ModelTradeTrade extends Model {
	public function add($data) {
		$query = "INSERT INTO trades SET open_id='" . $data['open_id'] . "' ";

		if (isset($data['alias'])) {
		    	$query .= ", alias='" . $this->db->escape($data['alias']) . "' ";
		}
		/**	
		if(isset($data['image']) && strpos($data['image'], 'uc01_local/') !== false){
                        $link = $this->uploadImage($data['image']);
                        $query .= ", image='" . $this->db->escape($link) . "' ";
                }
		*/
		if (isset($data['mobile'])) {
		    	$query .= ", mobile='" . $this->db->escape($data['mobile']) . "' ";
		}

		if (isset($data['community'])) {
		    	$query .= ", community='" . $this->db->escape($data['community']) . "' ";
		}

		if (isset($data['address'])) {
		    	$query .= ", address='" . $this->db->escape($data['address']) . "' ";
		}

		if (isset($data['join_time'])) {
		    	$query .= ", join_time='" . $this->db->escape($data['join_time']) . "' ";
		}

		if (isset($data['sex'])) {
		    	$query .= ", sex='" . $this->db->escape($data['sex']) . "' ";
		}

		if (isset($data['traded_num'])) {
		    	$query .= ", traded_num='" . $this->db->escape($data['traded_num']) . "' ";
		}

		if (isset($data['traded_money'])) {
		    	$query .= ", traded_money='" . $this->db->escape($data['traded_money']) . "' ";
		}

		if (isset($data['points'])) {
		    	$query .= ", points='" . $this->db->escape($data['points']) . "' ";
		}

		if (isset($data['avatar'])) {
		    	$query .= ", avatar='" . $this->db->escape($data['avatar']) . "' ";
		}

		$this->db->query($query);
                $request_id = $this->db->getLastId();

		//insert abstract_company
		/**
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
		*/
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
		/**
		if( !empty($data['image']) && strpos($data['image'], 'recruit/') === false ) { 
			$link = $this->uploadImage($data['image']);	
			if( !empty($link) && $link != '' ) {
				$updateStr = "UPDATE trades SET image = '" . $link . "' WHERE id = " . (int)$id;
				$this->db->query($updateStr);
			}
		} else if( empty($data['image']) ) {
			//reset image
			$updateStr = "UPDATE trades SET image = '' WHERE id = " . (int)$id;
			$this->db->query($updateStr);
		}
		*/
		$updateStr = "UPDATE trades SET order_num = " . $this->db->escape($data['order_num']);
		
		if( !empty($data['message']) ){
			$updateStr .= ", message = " . $this->db->escape($data['message']);
		}
		if( !empty($data['deliver_time']) ){
			$updateStr .= ", deliver_time = '" . $this->db->escape($data['deliver_time']) . "'";
		}
		$updateStr .= " WHERE id = " . (int)$id;

		$this->db->query($updateStr);

		//update overview for abstract_company
		/**
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
		*/
       	}
	
	public function delete($image_id) {
		$this->db->query("DELETE FROM gift_image WHERE image_id = '" . (int)$image_id . "'");

		$this->db->query("DELETE FROM gift_image_to_tag WHERE image_id = '" . (int)$image_id . "'");

		$this->db->query("DELETE FROM gift_image_to_category WHERE image_id = '" . (int)$image_id . "'");
        
		$this->cache->delete('gift_image');
	} 

	public function getItem($id) {
        	$query = $this->db->query("SELECT * FROM trades WHERE id = '" .$this->db->escape($id) . "'");

        	return $query->row;
    	}

	public function getGiftImage($image_id) {
		$query = $this->db->query("SELECT * FROM gift_image WHERE image_id = '" . (int)$image_id . "'");
		
		return $query->row;
	} 
	
	private function createQueryString($params, $for_count) {
		if ($for_count) {
		    $sql = "SELECT count(*) as total";
		} else {
		    $sql = "SELECT *";
		}

		$sql .= " FROM trades WHERE TRUE"; 

		if (!empty($params['filter_id'])) {
		    $sql .= " AND LCASE(id) LIKE '%" . $this->db->escape($params['filter_id']) . "%'";
		}

		if (!empty($params['filter_youzan_id']) && $params['filter_youzan_id'] != 'all') {
		    $sql .= " AND LCASE(youzan_id) LIKE '%" . $this->db->escape($params['filter_youzan_id']) . "%'";
		}

		if (!empty($params['filter_pay_type'])) {
		    $sql .= " AND LCASE(pay_type) LIKE '%" . $this->db->escape($params['filter_pay_type']) . "%'";
		}

		if (!empty($params['filter_message'])) {
		    $sql .= " AND LCASE(message) LIKE '%" . $this->db->escape($params['filter_message']) . "%'";
		}

		if (!empty($params['filter_status'])) {
		    $sql .= " AND LCASE(status) LIKE '%" . $this->db->escape($params['filter_status']) . "%'";
		}

		if (empty($params['sort']) || $for_count) {
		    $sql .= " ORDER BY id ASC";
		} else {
		    $sql .= " ORDER BY " . $params['sort'] . " DESC";
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
