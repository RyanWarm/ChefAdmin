<?php
class ModelAccountInviteCode extends Model {
	public function add($data) {
        $query = "INSERT INTO invite_code SET created_at=now() ";

        $code = md5(time() . rand(1, 1000000) . 'haha');
        $code_num = hexdec(substr($code, 0, 8));
        $code_num = $code_num % 100000000;
        $code_str = strval($code_num);

        $final_code = substr($code_str, 0, 8);

        $query .= ", code='" . $final_code . "' ";

        if (isset($data['class'])) {
            $query .= ", class='" . $data['class'] . "' ";
        }
        
        error_log('add invite code: ' . $query);

        if ($this->db->query($query)) {
            $this->cache->delete('invite_code');
            return $final_code;
        }

        return null;
	}
	
	public function edit($invite_code, $data) {
        $query = "UPDATE invite_code SET updated_at=now() ";
        if (isset($data['request_id'])) {
            $query .= ", request_id=" . (int)$data['request_id'];
        }

        $query .= " WHERE code='" . $this->db->escape($invite_code) . "' ";
        $this->db->query($query);

        error_log($query);

		$this->cache->delete('invite_code');
	}
	
	public function delete($image_id) {
		$this->db->query("DELETE FROM gift_image WHERE image_id = '" . (int)$image_id . "'");

		$this->db->query("DELETE FROM gift_image_to_tag WHERE image_id = '" . (int)$image_id . "'");

		$this->db->query("DELETE FROM gift_image_to_category WHERE image_id = '" . (int)$image_id . "'");
        
		$this->cache->delete('gift_image');
	} 

	public function getItem($code) {
		$query = $this->db->query("SELECT * FROM invite_code WHERE code = '" .$this->db->escape($code) . "'");
		
		return $query->row;
	} 
	
    private function createQueryString($params, $for_count) {
        if ($for_count) {
            $sql = "SELECT count(*) as total ";
        } else {
            $sql = "SELECT * ";
        }

        $sql .= " FROM invite_code WHERE TRUE ";
        
	if (!empty($params['filter_code'])) {
            $sql .= " AND LCASE(code) LIKE '%" . $this->db->escape($params['filter_code']) . "%'";
        }

	if (!empty($params['filter_regAccount'])) {
            $sql .= " AND LCASE(assignee) LIKE '%" . $this->db->escape($params['filter_regAccount']) . "%'";
        }

	if (!empty($params['filter_reqId'])) {
            $sql .= " AND LCASE(request_id) LIKE '%" . $this->db->escape($params['filter_reqId']) . "%'";
        }

        if (empty($params['sort'])) {
            $sql .= " ORDER BY created_at DESC";
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
