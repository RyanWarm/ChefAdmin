<?php
class ModelAccountLinkedin extends Model {
	public function getItem($zid) {
        	$query = $this->db->query("SELECT * FROM industry_linkedin WHERE zid = '" .$this->db->escape($zid) . "'");
        
        	return $query->row;
    	}

	private function createQueryString($params, $for_count) {
        	if ($for_count) {
        		$sql = "SELECT count(*) as total";
        	} else {
        		$sql = "SELECT *"; 
       		}

		$sql .= " FROM industry_linkedin WHERE TRUE ";
		
		if (!empty($params['filter_score'])) {
		    $sql .= " AND LCASE(score) LIKE '%" . $this->db->escape($params['filter_score']) . "%'";
		}

		if (!empty($params['filter_category']) ) {
		    $sql .= " AND LCASE(category) LIKE '%" . $this->db->escape($params['filter_category']) . "%'";
		}

		if (!empty($params['filter_zid'])) {
		    $sql .= " AND LCASE(zid) LIKE '%" . $this->db->escape($params['filter_zid']) . "%'";
		}

		if (!empty($params['filter_status']) && $params['filter_status'] != 'all') {
		    $sql .= " AND LCASE(status) LIKE '%" . $this->db->escape($params['filter_status']) . "%'";
		}

		if (empty($params['sort'])) {
		    $sql .= " ORDER BY score DESC";
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

	public function edit($zid, $data) {
                if (isset($data['status'])) {
			$sql = "UPDATE industry_linkedin SET status = " . $data['status'] . " WHERE zid = '" . $zid . "'";
			$this->db->query($sql);
                }
	}

	public function getList($params = array()) {

        $sql = $this->createQueryString($params, FALSE);

        $query = $this->db->query($sql);

		return $query->rows;
	}
   	
	public function getStatusTypes() {
		$result = array();

                $result[] = array(
                        'status_value'       =>      '1',
                        'status_name'     =>      '未发送'
                );

                $result[] = array(
                        'status_value'       =>      '2',
                        'status_name'     =>      '已发送'
                );

                $result[] = array(
                        'status_value'       =>      '3',
                        'status_name'     =>      '已回复'
                );

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

		return $query->row['total'];
	}	
}
?>
