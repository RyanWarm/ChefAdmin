<?php
class ModelCatalogProduct extends Model {
    private function randString($length)
    {
        $charset='ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789';

        $str = '';
        $count = strlen($charset);
        while ($length--) {
            $str .= $charset[mt_rand(0, $count-1)];
        }
        return $str;
    }

	public function addProduct($data) {

		$this->db->query("INSERT INTO " . DB_PREFIX . "product SET manufacturer_id = '" . (int)$data['manufacturer_id'] . "', price = '" . (float)$data['price'] . "', base_price = '" . (float)$data['base_price'] . "', status = '" . (int)$data['status'] . "', sort_order = '" . (int)$data['sort_order'] . "', score= '" . (float)$data['score'] . "', product_type= '" . $data['product_type']. "', date_added = NOW(), date_modified = NOW()");
		
		$product_id = $this->db->getLastId();

        $salt = $this->randString(8);
        $track_code = md5($product_id . $salt . time() );
        $short_track_code = substr(md5($track_code), 0, 8);
        $this->db->query("UPDATE product SET salt = '" . $salt . "', track_code = '" . $track_code . "', short_track_code = '" . $short_track_code . "' WHERE product_id = '" . (int)$product_id . "'");
        
        $this->load->model('tool/image');

		if (isset($data['orig_image'])) {
            $this->resizeProductImage($product_id, $data);
		}

        if (isset($data['feed_image'])) {
            $this->db->query("UPDATE " . DB_PREFIX . "product SET feed_image = '" . $this->db->escape(html_entity_decode($data['feed_image'], ENT_QUOTES, 'UTF-8')) . "' WHERE product_id = '" . (int)$product_id . "'");

            $image = $this->model_tool_image->resize_product_image($data['feed_image'], 200, 200);

			$this->db->query("UPDATE " . DB_PREFIX . "product SET feed_small_image = '" . $this->db->escape(html_entity_decode($image, ENT_QUOTES, 'UTF-8')) . "' WHERE product_id = '" . (int)$product_id . "'");

        }

		foreach ($data['product_description'] as $language_id => $value) {
			$this->db->query("INSERT INTO " . DB_PREFIX . "product_description SET product_id = '" . (int)$product_id . "', language_id = '" . (int)$language_id . "', name = '" . $this->db->escape($value['name']) . "', description = '" . $this->db->escape($value['description']) . "', review = '" . $this->db->escape($value['review']) . "', recommend_reason = '" . $this->db->escape($value['recommend_reason']) . "', sexy_name = '" . $this->db->escape($value['sexy_name']) . "', short_name = '" . $this->db->escape($value['short_name']) . "', coupons_rule='" . $this->db->escape($value['coupons_rule']) . "'");
		}

		if (isset($data['product_supplier']) && $data['product_supplier']['supplier_id'] != 0 ) {
            $value = $data['product_supplier'];
			$this->db->query("INSERT INTO " . DB_PREFIX . "product_supplier SET product_id = '" . (int)$product_id . "', supplier_id = '" . $value['supplier_id'] . "', supplier_product_url = '" . $this->db->escape($value['supplier_product_url']) . "', supplier_product_price = '" . $value['supplier_product_price'] . "', quantity = '" . $value['quantity'] . "', ordered_quantity = '" . $value['ordered_quantity'] . "', hold_quantity = '" . $value['hold_quantity'] . "', date_added = NOW(), date_modified = NOW() ");
		}
				
		if (isset($data['product_image'])) {
            $this->load->model('tool/image');

			foreach ($data['product_image'] as $product_image) {
				$this->db->query("INSERT INTO " . DB_PREFIX . "product_image SET product_id = '" . (int)$product_id . "', orig_image = '" . $this->db->escape(html_entity_decode($product_image['orig_image'], ENT_QUOTES, 'UTF-8')) . "', sort_order = '" . (int)$product_image['sort_order'] . "'");

                $product_image_id = $this->db->getLastId();
                
                $this->resizeProductDetailImage($product_image_id, $product_image);
                /*
                $image = $this->model_tool_image->resize_product_image($product_image['orig_image'], 800, 800);

				$this->db->query("UPDATE " . DB_PREFIX . "product_image SET image = '" . $this->db->escape($image, ENT_QUOTES, 'UTF-8') . "' WHERE product_image_id = '" . (int)$product_image_id . "'" );


                $mid_image = $this->model_tool_image->resize_product_image($product_image['orig_image'], 300, 300);
                if (!empty($mid_image)) {
                    $this->db->query("UPDATE " . DB_PREFIX . "product_image SET mid_image = '" . $this->db->escape($mid_image) . "' WHERE product_image_id = '" . (int)$product_image_id . "'");
                }
                
                $small_image = $this->model_tool_image->resize_product_image($product_image['orig_image'], 150, 150);
                if (!empty($small_image)) {
                    $this->db->query("UPDATE " . DB_PREFIX . "product_image SET small_image = '" . $this->db->escape($small_image) . "' WHERE product_image_id = '" . (int)$product_image_id . "'");
                }
                */
			}

		}
		
		if (isset($data['product_download'])) {
			foreach ($data['product_download'] as $download_id) {
				$this->db->query("INSERT INTO " . DB_PREFIX . "product_to_download SET product_id = '" . (int)$product_id . "', download_id = '" . (int)$download_id . "'");
			}
		}
		
		if (isset($data['product_category'])) {
			foreach ($data['product_category'] as $category_id) {
				$this->db->query("INSERT INTO " . DB_PREFIX . "product_to_category SET product_id = '" . (int)$product_id . "', category_id = '" . (int)$category_id . "'");
			}
		}

		if (isset($data['product_tag'])) {
			foreach ($data['product_tag'] as $tag_id) {
				$this->db->query("INSERT INTO " . DB_PREFIX . "product_to_tag SET product_id = '" . (int)$product_id . "', tag_id = '" . (int)$tag_id . "'");
			}
		}
		
		if (isset($data['product_related'])) {
			foreach ($data['product_related'] as $related_id) {
				$this->db->query("DELETE FROM " . DB_PREFIX . "product_related WHERE product_id = '" . (int)$product_id . "' AND related_id = '" . (int)$related_id . "'");
				$this->db->query("INSERT INTO " . DB_PREFIX . "product_related SET product_id = '" . (int)$product_id . "', related_id = '" . (int)$related_id . "'");
				$this->db->query("DELETE FROM " . DB_PREFIX . "product_related WHERE product_id = '" . (int)$related_id . "' AND related_id = '" . (int)$product_id . "'");
				$this->db->query("INSERT INTO " . DB_PREFIX . "product_related SET product_id = '" . (int)$related_id . "', related_id = '" . (int)$product_id . "'");
			}
		}

		if (isset($data['product_reward'])) {
			foreach ($data['product_reward'] as $customer_group_id => $product_reward) {
				$this->db->query("INSERT INTO " . DB_PREFIX . "product_reward SET product_id = '" . (int)$product_id . "', customer_group_id = '" . (int)$customer_group_id . "', points = '" . (int)$product_reward['points'] . "'");
			}
		}

		if (isset($data['product_layout'])) {
			foreach ($data['product_layout'] as $store_id => $layout) {
				if ($layout['layout_id']) {
					$this->db->query("INSERT INTO " . DB_PREFIX . "product_to_layout SET product_id = '" . (int)$product_id . "', store_id = '" . (int)$store_id . "', layout_id = '" . (int)$layout['layout_id'] . "'");
				}
			}
		}
        /*		
		foreach ($data['product_tag'] as $language_id => $value) {
			if ($value) {
				$tags = explode(',', $value);
				
				foreach ($tags as $tag) {
					$this->db->query("INSERT INTO " . DB_PREFIX . "product_tag SET product_id = '" . (int)$product_id . "', language_id = '" . (int)$language_id . "', tag = '" . $this->db->escape(trim($tag)) . "'");
				}
			}
		}
		*/
				
		if ($data['keyword']) {
			$this->db->query("INSERT INTO " . DB_PREFIX . "url_alias SET query = 'product_id=" . (int)$product_id . "', keyword = '" . $this->db->escape($data['keyword']) . "'");
		}
						
		$this->cache->delete('product');
	}

    private function resizeProductImage($product_id, $data) {
		if (isset($data['orig_image'])) {
            $this->db->query("UPDATE " . DB_PREFIX . "product SET orig_image = '" . $this->db->escape(html_entity_decode($data['orig_image'], ENT_QUOTES, 'UTF-8')) . "' WHERE product_id = '" . (int)$product_id . "'");

            $image = $this->model_tool_image->resize_product_image($data['orig_image'], 800, 800);

			$this->db->query("UPDATE " . DB_PREFIX . "product SET image = '" . $this->db->escape(html_entity_decode($image, ENT_QUOTES, 'UTF-8')) . "' WHERE product_id = '" . (int)$product_id . "'");

            $mid_image = $this->model_tool_image->resize_product_image($data['orig_image'], 300, 300);
            if (!empty($mid_image)) {
                $this->db->query("UPDATE " . DB_PREFIX . "product SET mid_image = '" . $this->db->escape($mid_image) . "' WHERE product_id = '" . (int)$product_id . "'");
            }
                
            $small_image = $this->model_tool_image->resize_product_image($data['orig_image'], 150, 150);
            if (!empty($small_image)) {
                $this->db->query("UPDATE " . DB_PREFIX . "product SET small_image = '" . $this->db->escape($small_image) . "' WHERE product_id = '" . (int)$product_id . "'");
            }
                    
		}
    
    }

    private function resizeProductDetailImage($product_image_id, $product_image) {
        $image = $this->model_tool_image->resize_product_image($product_image['orig_image'], 800, 800);

        $this->db->query("UPDATE " . DB_PREFIX . "product_image SET image = '" . $this->db->escape($image, ENT_QUOTES, 'UTF-8') . "' WHERE product_image_id = '" . (int)$product_image_id . "'" );


        $mid_image = $this->model_tool_image->resize_product_image($product_image['orig_image'], 300, 300);
        if (!empty($mid_image)) {
            $this->db->query("UPDATE " . DB_PREFIX . "product_image SET mid_image = '" . $this->db->escape($mid_image) . "' WHERE product_image_id = '" . (int)$product_image_id . "'");
        }
        
        $small_image = $this->model_tool_image->resize_product_image($product_image['orig_image'], 150, 150);
        if (!empty($small_image)) {
            $this->db->query("UPDATE " . DB_PREFIX . "product_image SET small_image = '" . $this->db->escape($small_image) . "' WHERE product_image_id = '" . (int)$product_image_id . "'");
        }
        
    }

	public function editProduct($product_id, $data) {
        $this->load->model('tool/image');

        $time_start = microtime_float();

        $t1 = microtime_float();

        $this->db->query("UPDATE " . DB_PREFIX . "product SET manufacturer_id = '" . (int)$data['manufacturer_id'] . "', price = '" . (float)$data['price'] . "', base_price = '" . (float)$data['base_price'] . "', status = '" . (int)$data['status'] . "', sort_order = '" . (int)$data['sort_order'] . "', score = '" . (float)$data['score'] . "', group_count = '" . (int)$data['group_count'] . "', product_type = '" . $data['product_type'] . "', date_modified = NOW() WHERE product_id = '" . (int)$product_id . "'");

        $t2 = microtime_float();
        error_log("time 1: " . ($t2 - $t1));

        if ($data['image_updated'] == 1) {

            $t1 = microtime_float();
            
            $this->resizeProductImage($product_id, $data);
        
            $t2 = microtime_float();
            error_log("time 2: " . ($t2 - $t1));
        }

        if (isset($data['feed_image'])) {
            $this->db->query("UPDATE " . DB_PREFIX . "product SET feed_image = '" . $this->db->escape(html_entity_decode($data['feed_image'], ENT_QUOTES, 'UTF-8')) . "' WHERE product_id = '" . (int)$product_id . "'");
            $image = $this->model_tool_image->resize_product_image($data['feed_image'], 200, 200);

			$this->db->query("UPDATE " . DB_PREFIX . "product SET feed_small_image = '" . $this->db->escape(html_entity_decode($image, ENT_QUOTES, 'UTF-8')) . "' WHERE product_id = '" . (int)$product_id . "'");

        }

        $t1 = microtime_float();

		$this->db->query("DELETE FROM " . DB_PREFIX . "product_description WHERE product_id = '" . (int)$product_id . "'");

		foreach ($data['product_description'] as $language_id => $value) {
            //error_log("producgt name=" . $value['name']);

			$this->db->query("INSERT INTO " . DB_PREFIX . "product_description SET product_id = '" . (int)$product_id . "', language_id = '" . (int)$language_id . "', name = '" . $this->db->escape($value['name']) . "', meta_keyword = '" . $this->db->escape($value['meta_keyword']) . "', meta_description = '" . $this->db->escape($value['meta_description']) . "', description = '" . $this->db->escape($value['description']) . "', review = '" . $this->db->escape($value['review']) . "', recommend_reason = '" . $this->db->escape($value['recommend_reason']) . "', sexy_name = '" . $this->db->escape($value['sexy_name']) . "', short_name = '" . $this->db->escape($value['short_name']) . "', coupons_rule = '" . $this->db->escape($value['coupons_rule']) . "' " );
		}

        $t2 = microtime_float();
        error_log("time 3: " . ($t2 - $t1));
        
        $t1 = microtime_float();

        $this->db->query("DELETE FROM " . DB_PREFIX . "product_supplier WHERE product_id = '" . $product_id . "'");

		if (isset($data['product_supplier']) && $data['product_supplier']['supplier_id'] != 0 ) {
            $value = $data['product_supplier'];
			$this->db->query("INSERT INTO " . DB_PREFIX . "product_supplier SET product_id = '" . (int)$product_id . "', supplier_id = '" . $value['supplier_id'] . "', supplier_product_url = '" . $this->db->escape($value['supplier_product_url']) . "', supplier_product_price = '" . $value['supplier_product_price'] . "', quantity = '" . $value['quantity'] . "', ordered_quantity = '" . $value['ordered_quantity'] . "', hold_quantity = '" . $value['hold_quantity'] . "', date_modified = NOW() ");
		}

        $t2 = microtime_float();
        error_log("time 4: " . ($t2 - $t1));


        $t1 = microtime_float();

        if ($data['image_updated'] == 1) {
            
            $this->db->query("DELETE FROM " . DB_PREFIX . "product_image WHERE product_id = '" . (int)$product_id . "'");
            
            if (isset($data['product_image'])) {
                $this->load->model('tool/image');
                
                foreach ($data['product_image'] as $product_image) {
                    $this->db->query("INSERT INTO " . DB_PREFIX . "product_image SET product_id = '" . (int)$product_id . "', orig_image = '" . $this->db->escape(html_entity_decode($product_image['orig_image'], ENT_QUOTES, 'UTF-8')) . "', sort_order = '" . (int)$product_image['sort_order'] . "'");
                    
                    $product_image_id = $this->db->getLastId();
                    
                    $this->resizeProductDetailImage($product_image_id, $product_image);
                }
            }
        }
        
        $t2 = microtime_float();
        error_log("time 5: " . ($t2 - $t1));

        $t1 = microtime_float();

		$this->db->query("DELETE FROM " . DB_PREFIX . "product_to_category WHERE product_id = '" . (int)$product_id . "'");

		if (isset($data['product_category'])) {
            $query_string = "INSERT INTO product_to_category (product_id, category_id) VALUES ";
            
			foreach ($data['product_category'] as $category_id) {
                $query_string .= "('" . (int)$product_id . "', '" . (int)$category_id . "'), ";
            }

            if (count($data['product_category']) > 0) {
                $query_string = substr($query_string, 0, strlen($query_string) - 2);
                $this->db->query($query_string);
            }

            //$this->db->query("INSERT INTO " . DB_PREFIX . "product_to_category SET product_id = '" . (int)$product_id . "', category_id = '" . (int)$category_id . "'");
					
		}

        $t2 = microtime_float();
        error_log("time 6: " . ($t2 - $t1));

        $t1 = microtime_float();
                  
		$this->db->query("DELETE FROM " . DB_PREFIX . "product_to_tag WHERE product_id = '" . (int)$product_id . "'");
		
		if (isset($data['product_tag'])) {
            $query_string = "INSERT INTO product_to_tag (product_id, tag_id) values ";
			foreach ($data['product_tag'] as $tag_id) {
                $query_string .= "('" . (int)$product_id . "', '" . (int)$tag_id . "'), ";
            }
            if (count($data['product_tag']) > 0 ) {
                $query_string = substr($query_string, 0, strlen($query_string) - 2 );
                $this->db->query($query_string);
            }
            
            //$this->db->query("INSERT INTO " . DB_PREFIX . "product_to_tag SET product_id = '" . (int)$product_id . "', tag_id = '" . (int)$tag_id . "'");
            
		}

        $t2 = microtime_float();
        error_log("time 7: " . ($t2 - $t1));

        $t1 = microtime_float();

		$this->db->query("DELETE FROM " . DB_PREFIX . "product_to_group WHERE product_id = '" . (int)$product_id . "'");
		
		if (isset($data['product_group'])) {
			foreach ($data['product_group'] as $product_group_id) {
				$this->db->query("INSERT INTO " . DB_PREFIX . "product_to_group SET product_id = '" . (int)$product_id . "', product_group_id = '" . (int)$product_group_id . "'");
			}		
		}
        $t2 = microtime_float();
        error_log("time 8: " . ($t2 - $t1));


        $time_spend = microtime_float() - $time_start;
        
        error_log("time: " . $time_spend);
				
		$this->cache->delete('product');
	}
	
	public function copyProduct($product_id) {
		$query = $this->db->query("SELECT DISTINCT * FROM " . DB_PREFIX . "product p LEFT JOIN " . DB_PREFIX . "product_description pd ON (p.product_id = pd.product_id) WHERE p.product_id = '" . (int)$product_id . "' AND pd.language_id = '" . (int)$this->config->get('config_language_id') . "'");
		
		if ($query->num_rows) {
			$data = array();
			
			$data = $query->row;
			
			$data['keyword'] = '';

			$data['status'] = '0';
						
			$data = array_merge($data, array('product_attribute' => $this->getProductAttributes($product_id)));
			$data = array_merge($data, array('product_description' => $this->getProductDescriptions($product_id)));			
			$data = array_merge($data, array('product_discount' => $this->getProductDiscounts($product_id)));
			$data = array_merge($data, array('product_image' => $this->getProductImages($product_id)));		
			$data = array_merge($data, array('product_option' => $this->getProductOptions($product_id)));
			$data = array_merge($data, array('product_related' => $this->getProductRelated($product_id)));
			$data = array_merge($data, array('product_reward' => $this->getProductRewards($product_id)));
			$data = array_merge($data, array('product_special' => $this->getProductSpecials($product_id)));
			//$data = array_merge($data, array('product_tag' => $this->getProductTags($product_id)));
			$data = array_merge($data, array('product_category' => $this->getProductCategories($product_id)));
			$data = array_merge($data, array('product_tag' => $this->getProductTags($product_id)));
			$data = array_merge($data, array('product_download' => $this->getProductDownloads($product_id)));
			$data = array_merge($data, array('product_layout' => $this->getProductLayouts($product_id)));
			$data = array_merge($data, array('product_store' => $this->getProductStores($product_id)));
			
			$this->addProduct($data);
		}
	}
	
	public function deleteProduct($product_id) {
        $this->db->query("DELETE FROM " . DB_PREFIX . "product WHERE product_id = '" . (int)$product_id . "'");
		$this->db->query("DELETE FROM " . DB_PREFIX . "product_attribute WHERE product_id = '" . (int)$product_id . "'");
		$this->db->query("DELETE FROM " . DB_PREFIX . "product_discount WHERE product_id = '" . (int)$product_id . "'");
		$this->db->query("DELETE FROM " . DB_PREFIX . "product_description WHERE product_id = '" . (int)$product_id . "'");
		$this->db->query("DELETE FROM " . DB_PREFIX . "product_image WHERE product_id = '" . (int)$product_id . "'");
		$this->db->query("DELETE FROM " . DB_PREFIX . "product_option WHERE product_id = '" . (int)$product_id . "'");
		$this->db->query("DELETE FROM " . DB_PREFIX . "product_option_value WHERE product_id = '" . (int)$product_id . "'");
		$this->db->query("DELETE FROM " . DB_PREFIX . "product_related WHERE product_id = '" . (int)$product_id . "'");
		$this->db->query("DELETE FROM " . DB_PREFIX . "product_related WHERE related_id = '" . (int)$product_id . "'");
		$this->db->query("DELETE FROM " . DB_PREFIX . "product_reward WHERE product_id = '" . (int)$product_id . "'");
		$this->db->query("DELETE FROM " . DB_PREFIX . "product_special WHERE product_id = '" . (int)$product_id . "'");
		//$this->db->query("DELETE FROM " . DB_PREFIX . "product_tag WHERE product_id='" . (int)$product_id. "'");
		$this->db->query("DELETE FROM " . DB_PREFIX . "product_to_category WHERE product_id = '" . (int)$product_id . "'");
		$this->db->query("DELETE FROM " . DB_PREFIX . "product_to_tag WHERE product_id = '" . (int)$product_id . "'");
		$this->db->query("DELETE FROM " . DB_PREFIX . "product_to_download WHERE product_id = '" . (int)$product_id . "'");
		$this->db->query("DELETE FROM " . DB_PREFIX . "product_to_layout WHERE product_id = '" . (int)$product_id . "'");
		$this->db->query("DELETE FROM " . DB_PREFIX . "product_to_store WHERE product_id = '" . (int)$product_id . "'");
		$this->db->query("DELETE FROM " . DB_PREFIX . "review WHERE product_id = '" . (int)$product_id . "'");
		
		$this->db->query("DELETE FROM " . DB_PREFIX . "url_alias WHERE query = 'product_id=" . (int)$product_id. "'");
        $this->db->query("DELETE FROM " . DB_PREFIX . "product_to_group WHERE product_id = '" . (int)$product_id . "'");

		$this->cache->delete('product');
	}
	
	public function getProduct($product_id) {
		$query = $this->db->query("SELECT DISTINCT *, (SELECT keyword FROM " . DB_PREFIX . "url_alias WHERE query = 'product_id=" . (int)$product_id . "') AS keyword FROM " . DB_PREFIX . "product p LEFT JOIN " . DB_PREFIX . "product_description pd ON (p.product_id = pd.product_id) WHERE p.product_id = '" . (int)$product_id . "' AND pd.language_id = '" . (int)$this->config->get('config_language_id')  . "'");
        
		return $query->row;
	}
	

	public function getProducts($data = array()) {
		if ($data) {
			$sql = "SELECT p.*, pd.*, m.name as manufacturer_name, ps.quantity, s.name as supplier, GROUP_CONCAT(cd.name) as category_name FROM " . DB_PREFIX . "product p LEFT JOIN " . DB_PREFIX . "product_description pd ON (p.product_id = pd.product_id) LEFT JOIN product_supplier ps ON (p.product_id = ps.product_id) LEFT JOIN supplier s ON (ps.supplier_id = s.supplier_id) LEFT JOIN manufacturer m ON (p.manufacturer_id = m.manufacturer_id) LEFT JOIN product_to_category p2c ON (p.product_id = p2c.product_id) LEFT JOIN category c ON (c.category_id = p2c.category_id) LEFT JOIN category_description cd ON (c.category_id = cd.category_id) ";

            $sql .= " WHERE TRUE";

            if (!empty($data['filter_category']) && $data['filter_category'] != '*') {
                $sql .= " AND c.category_id = '" . (int)$data['filter_category'] . "' ";
            }

			$sql .= " AND pd.language_id = '" . (int)$this->config->get('config_language_id') . "'"; 

            if (!empty($data['product_type'])) {
                $sql .= " AND product_type = '" . $data['product_type'] . "' ";
            }

			if (!empty($data['filter_name'])) {
				$sql .= " AND LCASE(pd.name) LIKE '%" . $this->db->escape($data['filter_name']) . "%'";
			}

			if (!empty($data['filter_short_track_code'])) {

				$sql .= " AND short_track_code = '" . $this->db->escape($data['filter_short_track_code']) . "'";
			}

			if (!empty($data['filter_model'])) {
				$sql .= " AND LCASE(p.model) LIKE '" . $this->db->escape($data['filter_model']) . "%'";
			}
			
			if (!empty($data['filter_price'])) {
				$sql .= " AND p.price LIKE '" . $this->db->escape($data['filter_price']) . "%'";
			}

            /*			
			if (isset($data['filter_quantity']) && !is_null($data['filter_quantity'])) {
				$sql .= " AND p.quantity = '" . $this->db->escape($data['filter_quantity']) . "'";
			}
            */
			
			if (isset($data['filter_status']) && !is_null($data['filter_status'])) {
				$sql .= " AND p.status = '" . (int)$data['filter_status'] . "'";
			}
					

            if (isset($data['filter_supplier']) && !is_null($data['filter_supplier'])) {
                $sql .= " AND s.name = '" . $data['filter_supplier'] . "'";
            }

			if (!empty($data['filter_category_id'])) {
				if (!empty($data['filter_sub_category'])) {
					$implode_data = array();
					
					$implode_data[] = "category_id = '" . (int)$data['filter_category_id'] . "'";
					
					$this->load->model('catalog/category');
					
					$categories = $this->model_catalog_category->getCategories($data['filter_category_id']);
					
					foreach ($categories as $category) {
						$implode_data[] = "p2c.category_id = '" . (int)$category['category_id'] . "'";
					}
					
					$sql .= " AND (" . implode(' OR ', $implode_data) . ")";			
				} else {
					$sql .= " AND p2c.category_id = '" . (int)$data['filter_category_id'] . "'";
				}
			}

			if (!empty($data['filter_tag_id'])) {
                $sql .= " AND p2o.tag_id = '" . (int)$data['filter_tag_id'] . "'";
			}
			
			$sql .= " GROUP BY p.product_id";
						
			$sort_data = array(
                'p.product_id',
				'pd.name',
				'p.model',
				'p.category',
				'p.price',
				/*'p.quantity',*/
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
			
			$query = $this->db->query($sql);
		
		    $results = $query->rows;
            /*
            foreach ($results as $key => $row) {
                if (empty($row['track_code'])) {
                    $salt = $this->randString(8);
                    $track_code = md5($row['product_id'] . $salt . time() );
                    $short_track_code = substr(md5($track_code), 0, 8);
                    $this->db->query("UPDATE product SET salt = '" . $salt . "', track_code = '" . $track_code . "', short_track_code = '" . $short_track_code . "' WHERE product_id = '" . (int)$row['product_id'] . "'");

                    $results[$key]['short_track_code'] = $short_track_code;
                }
            }
            */

            /*
            foreach ($results as $key => $row) {
                $query = $this->db->query("SELECT DISTINCT * FROM product_supplier ps LEFT JOIN supplier s on ps.supplier_id = s.supplier_id WHERE product_id = '" . $row['product_id'] . "'");
                if ($query->row) {
                    $results[$key]['quantity'] = $query->row['quantity'];
                    $results[$key]['supplier'] = $query->row['name'];
                } else {
                    $results[$key]['quantity'] = "no supplier";
                    $results[$key]['supplier'] = "no supplier";
                }
            }
            */


            /*
            $this->load->model('tool/image');
            
            foreach ($results as $row) {
                       
                $this->resizeProductImage($row['product_id'], $row);

                $query = $this->db->query("SELECT * from product_image where product_id = '" . $row['product_id'] . "'");
                foreach ($query->rows as $image_row) {
                    $this->resizeProductDetailImage($image_row['product_image_id'], $image_row);
                }
                
            }
            */


            return $results;
		} else {
			$product_data = $this->cache->get('product.' . (int)$this->config->get('config_language_id'));
		
			if (!$product_data) {
				$query = $this->db->query("SELECT * FROM " . DB_PREFIX . "product p LEFT JOIN " . DB_PREFIX . "product_description pd ON (p.product_id = pd.product_id) WHERE pd.language_id = '" . (int)$this->config->get('config_language_id') . "' ORDER BY pd.name ASC");
	
				$product_data = $query->rows;
			
				$this->cache->set('product.' . (int)$this->config->get('config_language_id'), $product_data);
			}	
	
			return $product_data;
		}
	}
	
	public function getProductsByCategoryId($category_id) {
		$query = $this->db->query("SELECT * FROM " . DB_PREFIX . "product p LEFT JOIN " . DB_PREFIX . "product_description pd ON (p.product_id = pd.product_id) LEFT JOIN " . DB_PREFIX . "product_to_category p2c ON (p.product_id = p2c.product_id) WHERE pd.language_id = '" . (int)$this->config->get('config_language_id') . "' AND p2c.category_id = '" . (int)$category_id . "' ORDER BY pd.name ASC");
								  
		return $query->rows;
	} 

	public function getProductsByTagId($tag_id) {
		$query = $this->db->query("SELECT * FROM " . DB_PREFIX . "product p LEFT JOIN " . DB_PREFIX . "product_description pd ON (p.product_id = pd.product_id) LEFT JOIN " . DB_PREFIX . "product_to_tag p2o ON (p.product_id = p2o.product_id) WHERE pd.language_id = '" . (int)$this->config->get('config_language_id') . "' AND p2o.tag_id = '" . (int)$tag_id . "' ORDER BY pd.name ASC");
								  
		return $query->rows;
	} 
	
	public function getProductDescriptions($product_id) {
		$product_description_data = array();
		
		$query = $this->db->query("SELECT * FROM " . DB_PREFIX . "product_description WHERE product_id = '" . (int)$product_id . "'");
		
		foreach ($query->rows as $result) {
			$product_description_data[$result['language_id']] = array(
				'name'             => $result['name'],
				'description'      => $result['description'],
				'meta_keyword'     => $result['meta_keyword'],
				'meta_description' => $result['meta_description'],
				'review'           => $result['review'],
				'recommend_reason' => $result['recommend_reason'],
                'coupons_rule'     => $result['coupons_rule'],
				'sexy_name'        => $result['sexy_name'],
                'short_name'       => $result['short_name']
			);
		}
		
		return $product_description_data;
	}

	public function getProductSupplier($product_id) {
		$query = $this->db->query("SELECT * FROM " . DB_PREFIX . "product_supplier WHERE product_id = '" . (int)$product_id . "'");
        return $query->row;
	}

	public function getProductAttributes($product_id) {
		$product_attribute_data = array();
		
		$product_attribute_query = $this->db->query("SELECT pa.attribute_id, ad.name FROM " . DB_PREFIX . "product_attribute pa LEFT JOIN " . DB_PREFIX . "attribute a ON (pa.attribute_id = a.attribute_id) LEFT JOIN " . DB_PREFIX . "attribute_description ad ON (a.attribute_id = ad.attribute_id) WHERE pa.product_id = '" . (int)$product_id . "' AND ad.language_id = '" . (int)$this->config->get('config_language_id') . "' GROUP BY pa.attribute_id");
		
		foreach ($product_attribute_query->rows as $product_attribute) {
			$product_attribute_description_data = array();
			
			$product_attribute_description_query = $this->db->query("SELECT * FROM " . DB_PREFIX . "product_attribute WHERE product_id = '" . (int)$product_id . "' AND attribute_id = '" . (int)$product_attribute['attribute_id'] . "'");
			
			foreach ($product_attribute_description_query->rows as $product_attribute_description) {
				$product_attribute_description_data[$product_attribute_description['language_id']] = array('text' => $product_attribute_description['text']);
			}
			
			$product_attribute_data[] = array(
				'attribute_id'                  => $product_attribute['attribute_id'],
				'name'                          => $product_attribute['name'],
				'product_attribute_description' => $product_attribute_description_data
			);
		}
		
		return $product_attribute_data;
	}
	
	public function getProductOptions($product_id) {
		$product_option_data = array();
		
		$product_option_query = $this->db->query("SELECT * FROM " . DB_PREFIX . "product_option po LEFT JOIN `" . DB_PREFIX . "option` o ON (po.option_id = o.option_id) LEFT JOIN " . DB_PREFIX . "option_description od ON (o.option_id = od.option_id) WHERE po.product_id = '" . (int)$product_id . "' AND od.language_id = '" . (int)$this->config->get('config_language_id') . "' ORDER BY o.sort_order");
		
		foreach ($product_option_query->rows as $product_option) {
			if ($product_option['type'] == 'select' || $product_option['type'] == 'radio' || $product_option['type'] == 'checkbox' || $product_option['type'] == 'image') {
				$product_option_value_data = array();	
				
				$product_option_value_query = $this->db->query("SELECT * FROM " . DB_PREFIX . "product_option_value pov LEFT JOIN " . DB_PREFIX . "option_value ov ON (pov.option_value_id = ov.option_value_id) LEFT JOIN " . DB_PREFIX . "option_value_description ovd ON (ov.option_value_id = ovd.option_value_id) WHERE pov.product_option_id = '" . (int)$product_option['product_option_id'] . "' AND ovd.language_id = '" . (int)$this->config->get('config_language_id') . "' ORDER BY ov.sort_order");
				
				foreach ($product_option_value_query->rows as $product_option_value) {
					$product_option_value_data[] = array(
						'product_option_value_id' => $product_option_value['product_option_value_id'],
						'option_value_id'         => $product_option_value['option_value_id'],
						'name'                    => $product_option_value['name'],
						'image'                   => $product_option_value['image'],
						'quantity'                => $product_option_value['quantity'],
						'subtract'                => $product_option_value['subtract'],
						'price'                   => $product_option_value['price'],
						'price_prefix'            => $product_option_value['price_prefix'],
						'points'                  => $product_option_value['points'],
						'points_prefix'           => $product_option_value['points_prefix'],						
						'weight'                  => $product_option_value['weight'],
						'weight_prefix'           => $product_option_value['weight_prefix']					
					);
				}
				
				$product_option_data[] = array(
					'product_option_id'    => $product_option['product_option_id'],
					'option_id'            => $product_option['option_id'],
					'name'                 => $product_option['name'],
					'type'                 => $product_option['type'],
					'product_option_value' => $product_option_value_data,
					'required'             => $product_option['required']
				);				
			} else {
				$product_option_data[] = array(
					'product_option_id' => $product_option['product_option_id'],
					'option_id'         => $product_option['option_id'],
					'name'              => $product_option['name'],
					'type'              => $product_option['type'],
					'option_value'      => $product_option['option_value'],
					'required'          => $product_option['required']
				);				
			}
		}	
		
		return $product_option_data;
	}
	
	public function getProductImages($product_id) {
		$query = $this->db->query("SELECT * FROM " . DB_PREFIX . "product_image WHERE product_id = '" . (int)$product_id . "'");
		
		return $query->rows;
	}
	
	public function getProductDiscounts($product_id) {
		$query = $this->db->query("SELECT * FROM " . DB_PREFIX . "product_discount WHERE product_id = '" . (int)$product_id . "' ORDER BY quantity, priority, price");
		
		return $query->rows;
	}
	
	public function getProductSpecials($product_id) {
		$query = $this->db->query("SELECT * FROM " . DB_PREFIX . "product_special WHERE product_id = '" . (int)$product_id . "' ORDER BY priority, price");
		
		return $query->rows;
	}
	
	public function getProductRewards($product_id) {
		$product_reward_data = array();
		
		$query = $this->db->query("SELECT * FROM " . DB_PREFIX . "product_reward WHERE product_id = '" . (int)$product_id . "'");
		
		foreach ($query->rows as $result) {
			$product_reward_data[$result['customer_group_id']] = array('points' => $result['points']);
		}
		
		return $product_reward_data;
	}
		
	public function getProductDownloads($product_id) {
		$product_download_data = array();
		
		$query = $this->db->query("SELECT * FROM " . DB_PREFIX . "product_to_download WHERE product_id = '" . (int)$product_id . "'");
		
		foreach ($query->rows as $result) {
			$product_download_data[] = $result['download_id'];
		}
		
		return $product_download_data;
	}

	public function getProductStores($product_id) {
		$product_store_data = array();
		
		$query = $this->db->query("SELECT * FROM " . DB_PREFIX . "product_to_store WHERE product_id = '" . (int)$product_id . "'");

		foreach ($query->rows as $result) {
			$product_store_data[] = $result['store_id'];
		}
		
		return $product_store_data;
	}

	public function getProductLayouts($product_id) {
		$product_layout_data = array();
		
		$query = $this->db->query("SELECT * FROM " . DB_PREFIX . "product_to_layout WHERE product_id = '" . (int)$product_id . "'");
		
		foreach ($query->rows as $result) {
			$product_layout_data[$result['store_id']] = $result['layout_id'];
		}
		
		return $product_layout_data;
	}
		
	public function getProductCategories($product_id) {
		$product_category_data = array();
		
		$query = $this->db->query("SELECT * FROM " . DB_PREFIX . "product_to_category WHERE product_id = '" . (int)$product_id . "'");
		
		foreach ($query->rows as $result) {
			$product_category_data[] = $result['category_id'];
		}

		return $product_category_data;
	}

	public function getProductTags($product_id) {
		$product_tag_data = array();
		
		$query = $this->db->query("SELECT * FROM " . DB_PREFIX . "product_to_tag WHERE product_id = '" . (int)$product_id . "'");
		
		foreach ($query->rows as $result) {
			$product_tag_data[] = $result['tag_id'];
		}

		return $product_tag_data;
	}

	public function getProductGroups($product_id) {
		$product_data = array();
		
		$query = $this->db->query("SELECT * FROM " . DB_PREFIX . "product_to_group WHERE product_id = '" . (int)$product_id . "'");
		
		foreach ($query->rows as $result) {
			$product_data[] = $result['product_group_id'];
		}

		return $product_data;
	}

	public function getProductRelated($product_id) {
		$product_related_data = array();
		
		$query = $this->db->query("SELECT * FROM " . DB_PREFIX . "product_related WHERE product_id = '" . (int)$product_id . "'");
		
		foreach ($query->rows as $result) {
			$product_related_data[] = $result['related_id'];
		}
		
		return $product_related_data;
	}
    /*	
	public function getProductTags($product_id) {
		$product_tag_data = array();
		
		$query = $this->db->query("SELECT * FROM " . DB_PREFIX . "product_tag WHERE product_id = '" . (int)$product_id . "'");
		
		$tag_data = array();
		
		foreach ($query->rows as $result) {
			$tag_data[$result['language_id']][] = $result['tag'];
		}
		
		foreach ($tag_data as $language => $tags) {
			$product_tag_data[$language] = implode(',', $tags);
		}
		
		return $product_tag_data;
	}
    */

	
	public function getTotalProducts($data = array()) {
        $sql = "SELECT COUNT(*) AS total FROM ( SELECT p.*, GROUP_CONCAT(cd.name) as category_name FROM product p LEFT JOIN " . DB_PREFIX . "product_description pd ON (p.product_id = pd.product_id) LEFT JOIN product_supplier ps ON (p.product_id = ps.product_id) LEFT JOIN supplier s ON (ps.supplier_id = s.supplier_id) LEFT JOIN product_to_category p2c ON (p.product_id = p2c.product_id) LEFT JOIN category c ON (c.category_id = p2c.category_id) LEFT JOIN category_description cd ON (c.category_id = cd.category_id) ";
        //$sql = "SELECT COUNT(*) as total FROM product p LEFT JOIN " . DB_PREFIX . "product_description pd ON (p.product_id = pd.product_id) LEFT JOIN product_supplier ps ON (p.product_id = ps.product_id) LEFT JOIN supplier s ON (ps.supplier_id = s.supplier_id) LEFT JOIN product_to_category p2c ON (p.product_id = p2c.product_id) LEFT JOIN category c ON (c.category_id = p2c.category_id) LEFT JOIN category_description cd ON (c.category_id = cd.category_id) ";
        $sql .= " WHERE TRUE";

        if (!empty($data['filter_category']) && $data['filter_category'] != '*') {
            $sql .= " AND c.category_id = '" . (int)$data['filter_category'] . "' ";
        }

		$sql .= " AND pd.language_id = '" . (int)$this->config->get('config_language_id') . "' ";
        
        if (!empty($data['product_type'])) {
            $sql .= " AND product_type = '" . $data['product_type'] . "' ";
        }

        //AND p.product_type = 'shipping_gift'";		 
		 			
		if (!empty($data['filter_name'])) {
			$sql .= " AND LCASE(pd.name) LIKE '%" . $this->db->escape($data['filter_name']) . "%'";
		}

        if (!empty($data['filter_short_track_code'])) {
            
            $sql .= " AND short_track_code = '" . $this->db->escape($data['filter_short_track_code']) . "'";
        }
        
		if (!empty($data['filter_model'])) {
			$sql .= " AND LCASE(p.model) LIKE '" . $this->db->escape($data['filter_model']) . "%'";
		}

		if (!empty($data['filter_price'])) {
			$sql .= " AND p.price LIKE '" . $this->db->escape($data['filter_price']) . "%'";
		}

		/*
		if (isset($data['filter_quantity']) && !is_null($data['filter_quantity'])) {
			$sql .= " AND p.quantity = '" . $this->db->escape($data['filter_quantity']) . "'";
		}
		*/

		if (isset($data['filter_status']) && !is_null($data['filter_status'])) {
			$sql .= " AND p.status = '" . (int)$data['filter_status'] . "'";
		}

        if (isset($data['filter_supplier']) && !is_null($data['filter_supplier'])) {
            $sql .= " AND s.name = '" . $data['filter_supplier'] . "'";
        }

		if (!empty($data['filter_category_id'])) {
			if (!empty($data['filter_sub_category'])) {
				$implode_data = array();
				
				$implode_data[] = "p2c.category_id = '" . (int)$data['filter_category_id'] . "'";
				
				$this->load->model('catalog/category');
				
				$categories = $this->model_catalog_category->getCategories($data['filter_category_id']);
				
				foreach ($categories as $category) {
					$implode_data[] = "p2c.category_id = '" . (int)$category['category_id'] . "'";
				}
				
				$sql .= " AND (" . implode(' OR ', $implode_data) . ")";			
			} else {
				$sql .= " AND p2c.category_id = '" . (int)$data['filter_category_id'] . "'";
			}
		}

		if (!empty($data['filter_tag_id'])) {
            $sql .= " AND p2o.tag_id = '" . (int)$data['filter_tag_id'] . "'";
		}

        $sql .= " GROUP BY p.product_id";

        $sql .= ") as g1 ";

		$query = $this->db->query($sql);
		
		return $query->row['total'];
	}	
	
	public function getTotalProductsByTaxClassId($tax_class_id) {
		$query = $this->db->query("SELECT COUNT(*) AS total FROM " . DB_PREFIX . "product WHERE tax_class_id = '" . (int)$tax_class_id . "'");

		return $query->row['total'];
	}
		
	public function getTotalProductsByStockStatusId($stock_status_id) {
		$query = $this->db->query("SELECT COUNT(*) AS total FROM " . DB_PREFIX . "product WHERE stock_status_id = '" . (int)$stock_status_id . "'");

		return $query->row['total'];
	}
	
	public function getTotalProductsByWeightClassId($weight_class_id) {
		$query = $this->db->query("SELECT COUNT(*) AS total FROM " . DB_PREFIX . "product WHERE weight_class_id = '" . (int)$weight_class_id . "'");

		return $query->row['total'];
	}
	
	public function getTotalProductsByLengthClassId($length_class_id) {
		$query = $this->db->query("SELECT COUNT(*) AS total FROM " . DB_PREFIX . "product WHERE length_class_id = '" . (int)$length_class_id . "'");

		return $query->row['total'];
	}

	public function getTotalProductsByDownloadId($download_id) {
		$query = $this->db->query("SELECT COUNT(*) AS total FROM " . DB_PREFIX . "product_to_download WHERE download_id = '" . (int)$download_id . "'");
		
		return $query->row['total'];
	}
	
	public function getTotalProductsByManufacturerId($manufacturer_id) {
		$query = $this->db->query("SELECT COUNT(*) AS total FROM " . DB_PREFIX . "product WHERE manufacturer_id = '" . (int)$manufacturer_id . "'");

		return $query->row['total'];
	}
	
	public function getTotalProductsByAttributeId($attribute_id) {
		$query = $this->db->query("SELECT COUNT(*) AS total FROM " . DB_PREFIX . "product_attribute WHERE attribute_id = '" . (int)$attribute_id . "'");

		return $query->row['total'];
	}	
	
	public function getTotalProductsByOptionId($option_id) {
		$query = $this->db->query("SELECT COUNT(*) AS total FROM " . DB_PREFIX . "product_option WHERE option_id = '" . (int)$option_id . "'");

		return $query->row['total'];
	}	
	
	public function getTotalProductsByLayoutId($layout_id) {
		$query = $this->db->query("SELECT COUNT(*) AS total FROM " . DB_PREFIX . "product_to_layout WHERE layout_id = '" . (int)$layout_id . "'");

		return $query->row['total'];
	}			
}
?>