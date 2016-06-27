<?php
class ModelAccountAccount extends Model {
	public function add($data) {
		$this->db->query("INSERT INTO gift_image SET name = '" . $this->db->escape($data['name']) . "', wish = '" . $this->db->escape($data['wish']) . "'");
        
		$image_id = $this->db->getLastId();
        
        if (isset($data['image'])) {
            $this->db->query("UPDATE gift_image SET image = '" . $this->db->escape($data['image']) . "' WHERE image_id = '" . (int)$image_id . "'");
        }

        if (isset($data['animate'])) {
            $this->db->query("UPDATE gift_image SET animate = '" . $this->db->escape($data['animate']) . "' WHERE image_id = '" . (int)$image_id . "'");
        }

        /*
        if (isset($data['orig_image'])) {
            $this->resizeCardImage($card_id, $data);
        }
        */

        if (isset($data['product_tag'])) {
			foreach ($data['product_tag'] as $tag_id) {
				$this->db->query("INSERT INTO gift_image_to_tag SET image_id = '" . (int)$image_id . "', tag_id = '" . (int)$tag_id . "'");
			}
		}

        if (isset($data['gift_image_category'])) {
			foreach ($data['gift_image_category'] as $category_id) {
				$this->db->query("INSERT INTO gift_image_to_category SET image_id = '" . (int)$image_id . "', category_id = '" . (int)$category_id . "'");
			}
		}

		$this->cache->delete('gift_image');
	}
	
    private function resizeCardImage($card_id, $data) {
        $this->db->query("UPDATE " . DB_PREFIX . "card SET orig_image = '" . $this->db->escape(html_entity_decode($data['orig_image'], ENT_QUOTES, 'UTF-8')) . "' WHERE card_id = '" . (int)$card_id . "'");

        $this->load->model('tool/image');

        $image = $this->model_tool_image->resize_card_image($data['orig_image'], 320, 413);

        //if (!empty($image)) {
        //error_log("image=" . $image);

        $this->db->query("UPDATE " . DB_PREFIX . "card SET image = '" . $this->db->escape($image) . "' WHERE card_id = '" . (int)$card_id . "'");
            //}
    }

	public function edit($zid, $data) {
		if (isset($data['account_roles'])) {
			$currentRoles = $this->getAccountRoles($zid);
			$addRoles = array();
			$deleteRoles = array();
		    
			foreach ($data['account_roles'] as $role_id) {
				if( !in_array($role_id, $currentRoles) ){
					$addRoles[] = $role_id;
				}
		    	}

			foreach ($currentRoles as $role_id) {
				if( !in_array($role_id, $data['account_roles']) ){
					$deleteRoles[] = $role_id;
				}
		    	}
			
			$query_string = "INSERT INTO account_role VALUES ";
			if( count($addRoles) > 0 ){
				foreach ( $addRoles as $role_id ){
					$query_string .= "(" . (int)$zid . ", " . (int)$role_id . ", now()), ";
				}
				$query_string = substr($query_string, 0, strlen($query_string)-2);
				$query_string .= ";";
				$this->db->query($query_string);
			}

			if( count($deleteRoles) > 0 ){
				foreach ( $deleteRoles as $role_id ){
					$query_string = "DELETE FROM account_role WHERE ";
					$query_string .= "zid = " . (int)$zid . " and role_id = " . (int)$role_id . ";";
					$this->db->query($query_string);
				}
			}
		}

		// update cached counters
		$mc = new Memcache;
		$mc->connect('uc04', 11211);
		
		$s_op = 'search_query';
		$p_op = 'profile_view';	
		$h = date('H');
		$d = date('d');
		$hKey_search = '/:' . $s_op . ':zid:' . $zid . ':h' . $h;
		$hKey_profile = '/:' . $p_op . ':zid:' . $zid . ':h' . $h;
		$dKey_search = '/:' . $s_op . ':zid:' . $zid . ':d' . $d;
		$dKey_profile = '/:' . $p_op . ':zid:' . $zid . ':d' . $d;
		$mc->set($hKey_search, array((int)$data['hs_count'], time(), 3600), 0, 3600);
		$mc->set($hKey_profile, array((int)$data['hp_count'], time(), 3600), 0, 3600);
		$mc->set($dKey_search, array((int)$data['ds_count'], time(), 24*3600), 0, 24*3600);
		$mc->set($dKey_profile, array((int)$data['dp_count'], time(), 24*3600), 0, 24*3600);

		$mc->close();
	}
	
	public function delete($image_id) {
		$this->db->query("DELETE FROM gift_image WHERE image_id = '" . (int)$image_id . "'");

		$this->db->query("DELETE FROM gift_image_to_tag WHERE image_id = '" . (int)$image_id . "'");

		$this->db->query("DELETE FROM gift_image_to_category WHERE image_id = '" . (int)$image_id . "'");
        
		$this->cache->delete('gift_image');
	} 

	public function getItem($zid) {
        	$query = $this->db->query("SELECT a.*, an.network, an.profile_id, ic.code FROM account a LEFT JOIN account_network an ON (a.zid=an.zid) LEFT JOIN invite_code ic ON (a.zid = ic.assignee) WHERE a.zid = '" .$this->db->escape($zid) . "'");
        
        	return $query->row;
    	}

	public function getCachedCounters($zid) {
		$mc = new Memcache;
		$mc->connect('uc04', 11211);
		
		$s_op = 'search_query';
		$p_op = 'profile_view';	
		$h = date('H');
		$d = date('d');
		$hKey_search = '/:' . $s_op . ':zid:' . $zid . ':h' . $h;
		$hKey_profile = '/:' . $p_op . ':zid:' . $zid . ':h' . $h;
		$dKey_search = '/:' . $s_op . ':zid:' . $zid . ':d' . $d;
		$dKey_profile = '/:' . $p_op . ':zid:' . $zid . ':d' . $d;

		$result = array();
		$value = $mc->get($hKey_search);
		$result['hs'] = (!empty($value) && is_array($value)) ? $value[0] : 0;
		
		$value = $mc->get($hKey_profile);
		$result['hp'] = (!empty($value) && is_array($value)) ? $value[0] : 0;
		
		$value = $mc->get($dKey_search);
		$result['ds'] = (!empty($value) && is_array($value)) ? $value[0] : 0;
		
		$value = $mc->get($dKey_profile);
		$result['dp'] = (!empty($value) && is_array($value)) ? $value[0] : 0;
		$mc->close();

		return $result;
	}

	public function getClassTypes(){
		$query = $this->db->query("SHOW COLUMNS FROM account LIKE 'class'");
		
		$enum = $query->row['Type'];

		$enum_arr = explode("('",$enum);
    		$enum = $enum_arr[1];
    		$enum_arr = explode("')",$enum);
    		$enum = $enum_arr[0];
    		$enum_arr = explode("','",$enum);
	
		return $enum_arr;
	}

	public function getAccountRoles($account_id) {
		$account_role_data = array();
		
		$query = $this->db->query("SELECT * FROM account_role WHERE zid = " . (int)$account_id);
		
		foreach ($query->rows as $result) {
			$account_role_data[] = $result['role_id'];
		}

		return $account_role_data;
	}

	public function getRoles() {
		$role_data = array();

		$role_data[] = array(
			'role_id'	=>	'1',
			'role_name'	=>	'Common User'
		);

		$role_data[] = array(
			'role_id'	=>	'2',
			'role_name'	=>	'VIP User'
		);
	
		$role_data[] = array(
			'role_id'	=>	'3',
			'role_name'	=>	'Common HR'
		);

		$role_data[] = array(
			'role_id'	=>	'4',
			'role_name'	=>	'VIP HR'
		);

		return $role_data;
	}

	public function getGiftImage($image_id) {
		$query = $this->db->query("SELECT * FROM gift_image WHERE image_id = '" . (int)$image_id . "'");
		
		return $query->row;
	} 
	
    private function createQueryString($params, $for_count) {
        if ($for_count) {
            $sql = "SELECT count(*) as total FROM (SELECT count(a.zid) as tmp_id";
        } else {
            $sql = "SELECT a.*, GROUP_CONCAT(an.network) as network, GROUP_CONCAT(an.profile_id) as profile_id, ic.code";
        }

        $sql .= " FROM account a LEFT JOIN account_network an ON (a.zid=an.zid) LEFT JOIN invite_code ic ON (a.zid = ic.assignee) WHERE TRUE ";
        
        if (!empty($params['filter_code'])) {
            $sql .= " AND LCASE(ic.code) LIKE '%" . $this->db->escape($params['filter_code']) . "%'";
        }

        if (!empty($params['filter_class']) && $params['filter_class'] != 'all') {
            $sql .= " AND LCASE(a.class) LIKE '%" . $this->db->escape($params['filter_class']) . "%'";
        }

        if (!empty($params['filter_zid'])) {
            $sql .= " AND LCASE(a.zid) LIKE '%" . $this->db->escape($params['filter_zid']) . "%'";
        }

        if (!empty($params['filter_email'])) {
            $sql .= " AND LCASE(a.email) LIKE '%" . $this->db->escape($params['filter_email']) . "%'";
        }

        if (!empty($params['filter_profile'])) {
            $sql .= " AND LCASE(an.profile_id) LIKE '%" . $this->db->escape($params['filter_profile']) . "%'";
        }

        if (empty($params['sort'])) {
            $sql .= " GROUP BY a.zid ORDER BY a.zid DESC";
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
		$sql .= ") AS tmp";
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
