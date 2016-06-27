<?php
class ModelAccountContactRequest extends Model {
	public function getItem($id) {
		//insert admin_process status for new edited request
		$sql = "SELECT * FROM contact_request_status WHERE request_id = '" . $this->db->escape($id) . "' and status = 'admin_process'";
		$query = $this->db->query($sql);
		if( $query->num_rows == 0 ){
			$sql = "INSERT INTO contact_request_status SET status = 'admin_process', request_id = '" . $this->db->escape($id) . "', start_time = now()";
			$this->db->query($sql);
		}

        	$query = $this->db->query("SELECT * FROM contact_request WHERE request_id = '" . $this->db->escape($id) . "'");
        
        	return $query->row;
    	}

	public function getStatus($id) {
		$query = $this->db->query("SELECT * FROM contact_request_status WHERE request_id = '" . $this->db->escape($id) . "' ORDER BY start_time ASC");

		return $query->rows;
	}

	public function getLatestStatus($id) {
		$query = $this->db->query("SELECT * FROM contact_request_status WHERE request_id = '" . $this->db->escape($id) . "' ORDER BY start_time DESC");
		
		return $query->row;
	}

	private function createQueryString($params, $for_count) {
        	if ($for_count) {
			$sql = "SELECT count(*) as total FROM (SELECT count(cr.request_id) as tmp_id";
        	} else {
        		$sql = "SELECT *"; 
       		}

		$sql .= " FROM contact_request cr LEFT JOIN contact_request_status crs ON cr.request_id=crs.request_id WHERE TRUE ";
		
		if (!empty($params['filter_request_id'])) {
		    $sql .= " AND LCASE(cr.request_id) LIKE '%" . $this->db->escape($params['filter_request_id']) . "%'";
		}

		if (!empty($params['filter_requester_zid']) ) {
		    $sql .= " AND LCASE(requester_zid) LIKE '%" . $this->db->escape($params['filter_requester_zid']) . "%'";
		}

		if (!empty($params['filter_job_id'])) {
		    $sql .= " AND LCASE(requester_job_id) LIKE '%" . $this->db->escape($params['filter_job_id']) . "%'";
		}

		if (!empty($params['filter_target_network'])) {
		    $sql .= " AND LCASE(target_network) LIKE '%" . $this->db->escape($params['filter_target_network']) . "%'";
		}

		if (!empty($params['filter_created_at'])) {
		    $sql .= " AND LCASE(created_at) LIKE '%" . $this->db->escape($params['filter_created_at']) . "%'";
		}

		if (!empty($params['filter_updated_at'])) {
		    $sql .= " AND LCASE(updated_at) LIKE '%" . $this->db->escape($params['filter_updated_at']) . "%'";
		}

		if (!empty($params['filter_subject'])) {
		    $sql .= " AND LCASE(requester_subject) LIKE '%" . $this->db->escape($params['filter_subject']) . "%'";
		}

		if (!empty($params['filter_status']) && $params['filter_status'] != 'all') {
		    $st = split( '\.', $params['filter_status'] );
		    $sql .= " AND LCASE(status) LIKE '%" . $this->db->escape($st[0]) . "%'";
		    $sql .= " AND LCASE(complete_reason) LIKE '%" . $this->db->escape($st[1]) . "%'";
		}

		if (empty($params['sort'])) {
		    $sql .= " GROUP BY cr.request_id ORDER BY cr.request_id DESC";
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
		} else {
			$sql .= " ) as tmp";
		}

		return $sql;
	    
	}

	public function edit($id, $data) {
		$sql = "UPDATE contact_request SET target_email = '" . $data['target_email'] . "'";
		if ( isset($data['target_phone']) ) {
			$sql .= " , target_phone = '" . $data['target_phone'] . "'";
		}		
		if ( isset($data['target_comment']) ) {
			$sql .= " , target_comment = '" . $data['target_comment'] . "'";
		}		
		if ( isset($data['target_resume']) ) {
			$sql .= " , target_resume = '" . $data['target_resume'] . "'";
		}		
		if ( isset($data['target_transactions']) ) {
			$sql .= " , target_transactions = '" . $data['target_transactions'] . "'";
		}
		$sql .= " WHERE request_id = " .$id;
		$this->db->query($sql);	

		// update status table
		if ( isset($data['st_admin_process_comment']) && $data['st_admin_process_comment']=='状态日志' ) $data['st_admin_process_comment'] = '';
		if ( isset($data['st_admin_response_comment']) && $data['st_admin_response_comment']=='状态日志' ) $data['st_admin_response_comment'] = '';
		if ( isset($data['st_close_comment']) && $data['st_close_comment']=='状态日志' ) $data['st_close_comment'] = '';
                if (isset($data['st_admin_process'])) {
			$sql = "UPDATE contact_request_status SET complete_time = now(), complete_reason = 'expire', comment = '" . $data['st_admin_process_comment'] . "' WHERE request_id = " . $id . " and status = 'admin_process'";
			$this->db->query($sql);
			return;
                }
                if (isset($data['st_admin_response'])) {
			$comment = empty($data['st_admin_response_comment']) ? 'Notification email sent successfully.' : $data['st_admin_response_comment'];
			$sql = "UPDATE contact_request_status SET complete_time = now(), complete_reason = 'success' WHERE request_id = " . $id . " and status = 'admin_process'";
			$this->db->query($sql);

			$sql = "INSERT INTO contact_request_status SET request_id = " . $this->db->escape($id) . ", status = 'admin_response', start_time = now(), comment = '" . $comment . "'";
			$this->db->query($sql);
			return;
                }
                if (isset($data['st_admin_response_retry'])) {
			$sql = "UPDATE contact_request_status SET complete_time = now(), complete_reason = 'success', comment = 'Notification email sent.' WHERE request_id = " . $id . " and status = 'admin_response'";
			$this->db->query($sql);
			return;
                }
                if (isset($data['st_close'])) {
			$sql = "UPDATE contact_request_status SET complete_time = now(), complete_reason = 'success' WHERE request_id = " . $id . " and status = 'admin_response'";
			$this->db->query($sql);

			$sql = "INSERT INTO contact_request_status SET request_id = " . $this->db->escape($id) . ", status = 'close', start_time = now(), comment = '" . $data['st_close_comment'] . "'";
			$this->db->query($sql);
			return;
                }
	}
	
	public function resetAdminResponseStatus($id, $log) {
		$sql = "UPDATE contact_request_status SET complete_time = now(), complete_reason = 'expire', comment = '" . $log . "' WHERE request_id = " . $id . " and status = 'admin_response'";
                $this->db->query($sql);
	}

	public function getList($params = array()) {

        	$sql = $this->createQueryString($params, FALSE);

        	$query = $this->db->query($sql);

		return $query->rows;
	}
   	
	public function getStatusTypes() {
		$result = array();
		$st_names = array( 'request', 'admin_process', 'target_fill', 'admin_response', 'close' );
		$st_values = array( 'success', 'expire', 'abort' );

		foreach ( $st_names as $name ) {
			foreach ( $st_values as $value ) {
				$result[] = $name . "." . $value;
			}
		}

                return $result;
	}

	public function getStatusName($type) {
		switch ($type){
			case 1:
				return '未发送';
			case 2:
				return '已发送';
			case 3:
				return '已回复';
			default:
				return '未知';
		}
	}
 
	public function getTotalCount($params = array()) {
        	$sql = $this->createQueryString($params, TRUE);

      		$query = $this->db->query($sql);

		if( isset($query->row['total']) ) {
			return $query->row['total'];
		} else {
			return 0;
		}
	}	
}
?>
