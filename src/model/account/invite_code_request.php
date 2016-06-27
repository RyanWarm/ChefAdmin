<?php
class ModelAccountInviteCodeRequest extends Model {
	public function add($data) {
		$query = "INSERT INTO invite_code_request SET created_at=now() ";
        $query .= ", class='" . $data['class'] . "' ";
        $query .= ", email='" . $this->db->escape($data['email']) . "' ";

        if (isset($data['name'])) {
            $query .= ", name='" . $this->db->escape($data['name']) . "' ";
        }

        if (isset($data['company'])) {
            $query .= ", company='" . $this->db->escape($data['company']) . "' ";
        }

        if (isset($data['position'])) {
            $query .= ", position='" . $this->db->escape($data['position']) . "' ";
        }

        $this->db->query($query);

		$request_id = $this->db->getLastId();
        
		$this->cache->delete('invite_code_request');

        return $request_id;
	}
	
	public function edit($request_id, $data) {
		$query =  "UPDATE invite_code_request SET updated_at = now() ";

        if (isset($data['email'])) {
            $query .= ", email='" . $this->db->escape($data['email']) . "' ";
        }

        if (isset($data['name'])) {
            $query .= ", name='" . $this->db->escape($data['name']) . "' ";
        }

        if (isset($data['company'])) {
            $query .= ", company='" . $this->db->escape($data['company']) . "' ";
        }

        if (isset($data['position'])) {
            $query .= ", position='" . $this->db->escape($data['position']) . "' ";
        }

        if (isset($data['notify_method'])) {
            $query .= ", notify_method='" . $this->db->escape($data['notify_method']) . "' ";
            $query .= ", notify_time=now() ";
        }

        $query .= " WHERE request_id=" . (int)$request_id ;

        $this->db->query($query);

		$this->cache->delete('invite_code_request');
	}
	
    public function issueInviteCode($request_id, $invite_code) {
        $query =  "UPDATE invite_code_request SET issued_at=now(), issued_code='" . $this->db->escape($invite_code) . "' ";
        $query .= " WHERE request_id = " . (int)$request_id;

        $ret = $this->db->query($query);
        
        $this->cache->delete('invite_code_request');
        return $ret;
    }

	public function delete($request_id) {


        $this->db->query("UPDATE invite_code_request SET deleted=1 WHERE request_id=" . (int)$request_id );
        // TODO add operation history

		$this->cache->delete('invite_code_request');
	} 

	public function getItem($request_id) {
		$query = $this->db->query("SELECT * FROM invite_code_request WHERE request_id = '" . (int)$request_id . "'");
		
		return $query->row;
	} 
	
    private function createQueryString($params, $for_count) {
        if ($for_count) {
            $sql = "SELECT count(*) as total ";
        } else {
            $sql = "SELECT * ";
        }

        $sql .= " FROM invite_code_request WHERE TRUE ";

        if (isset($params['filter_deleted'])) {
            $sql .= " AND deleted = " . (int)$params['filter_deleted'];
        }

        if (isset($params['filter_requestId'])) {
            $sql .= " AND LCASE(request_id) LIKE '%" . $this->db->escape($params['filter_requestId']) . "%'";
        }

        if (isset($params['filter_class'])) {
            $sql .= " AND LCASE(class) LIKE '%" . $this->db->escape($params['filter_class']) . "%'";
        }

        if (isset($params['filter_code'])) {
            $sql .= " AND LCASE(issued_code) LIKE '%" . $this->db->escape($params['filter_code']) . "%'";
        }

        if (isset($params['filter_email'])) {
            $sql .= " AND LCASE(email) LIKE '%" . $this->db->escape($params['filter_email']) . "%'";
        }

        if (isset($params['filter_name'])) {
            $sql .= " AND LCASE(name) LIKE '%" . $this->db->escape($params['filter_name']) . "%'";
        }

        if (isset($params['filter_company'])) {
            $sql .= " AND LCASE(company) LIKE '%" . $this->db->escape($params['filter_company']) . "%'";
        }

        if (isset($params['filter_position'])) {
            $sql .= " AND LCASE(position) LIKE '%" . $this->db->escape($params['filter_position']) . "%'";
        }

        if (empty($params['sort'])) {
            $sql .= " ORDER BY request_id DESC";
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
