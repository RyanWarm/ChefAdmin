<?php
class ModelMonitorBackendApi extends Model {
	public function add($data) {
	}
	
	public function edit($request_id, $data) {

	}
	

	public function delete($request_id) {
	} 
	
    private function createQueryString($params, $for_count) {
        if ($for_count) {
            $sql = "SELECT count(*) as total ";
        } else {
            $sql = "SELECT * ";
        }

        $sql .= " FROM apitest_status WHERE TRUE ";


        if (isset($params['filter_task_name'])) {
            $sql .= " AND task_name = '" . $this->db->escape($params['filter_task_name']) . "'";
        }


        if (!$for_count) {
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
