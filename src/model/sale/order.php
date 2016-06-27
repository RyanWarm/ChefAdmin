<?php
require_once(DIR_SYSTEM . 'library/notify_client.php');

function order_status_date_cmp($a, $b) {
    
}

class ModelSaleOrder extends Model {
	public function addOrder($data) {
		$this->load->model('setting/store');
		
		$store_info = $this->model_setting_store->getStore($data['store_id']);
		
		if ($store_info) {
			$store_name = $store_info['name'];
			$store_url = $store_info['url'];
		} else {
			$store_name = $this->config->get('config_name');
			$store_url = HTTP_CATALOG;
		}
		
		$this->load->model('setting/setting');
		
		$setting_info = $this->model_setting_setting->getSetting('setting', $data['store_id']);
			
		if (isset($setting_info['invoice_prefix'])) {
			$invoice_prefix = $setting_info['invoice_prefix'];
		} else {
			$invoice_prefix = $this->config->get('config_invoice_prefix');
		}
		
		$this->load->model('localisation/country');
		
		$this->load->model('localisation/zone');
		
		$country_info = $this->model_localisation_country->getCountry($data['shipping_country_id']);
		
		if ($country_info) {
			$shipping_country = $country_info['name'];
			$shipping_address_format = $country_info['address_format'];
		} else {
			$shipping_country = '';	
			$shipping_address_format = '{firstname} {lastname}' . "\n" . '{company}' . "\n" . '{address_1}' . "\n" . '{address_2}' . "\n" . '{city} {postcode}' . "\n" . '{zone}' . "\n" . '{country}';
		}	
		
		$zone_info = $this->model_localisation_zone->getZone($data['shipping_zone_id']);
		
		if ($zone_info) {
			$shipping_zone = $zone_info['name'];
		} else {
			$shipping_zone = '';			
		}	
					
		$country_info = $this->model_localisation_country->getCountry($data['payment_country_id']);
		
		if ($country_info) {
			$payment_country = $country_info['name'];
			$payment_address_format = $country_info['address_format'];			
		} else {
			$payment_country = '';	
			$payment_address_format = '{firstname} {lastname}' . "\n" . '{company}' . "\n" . '{address_1}' . "\n" . '{address_2}' . "\n" . '{city} {postcode}' . "\n" . '{zone}' . "\n" . '{country}';					
		}
	
		$zone_info = $this->model_localisation_zone->getZone($data['payment_zone_id']);
		
		if ($zone_info) {
			$payment_zone = $zone_info['name'];
		} else {
			$payment_zone = '';			
		}	

		$this->load->model('localisation/currency');

		$currency_info = $this->model_localisation_currency->getCurrencyByCode($this->config->get('config_currency'));
		
		if ($currency_info) {
			$currency_id = $currency_info['currency_id'];
			$currency_code = $currency_info['code'];
			$currency_value = $currency_info['value'];
		} else {
			$currency_id = 0;
			$currency_code = $this->config->get('config_currency');
			$currency_value = 1.00000;			
		}
      	
      	$this->db->query("INSERT INTO `" . DB_PREFIX . "order` SET invoice_prefix = '" . $this->db->escape($invoice_prefix) . "', store_id = '" . (int)$data['store_id'] . "', store_name = '" . $this->db->escape($store_name) . "',store_url = '" . $this->db->escape($store_url) . "', customer_id = '" . (int)$data['customer_id'] . "', customer_group_id = '" . (int)$data['customer_group_id'] . "', firstname = '" . $this->db->escape($data['firstname']) . "', lastname = '" . $this->db->escape($data['lastname']) . "', email = '" . $this->db->escape($data['email']) . "', telephone = '" . $this->db->escape($data['telephone']) . "', fax = '" . $this->db->escape($data['fax']) . "', shipping_firstname = '" . $this->db->escape($data['shipping_firstname']) . "', shipping_lastname = '" . $this->db->escape($data['shipping_lastname']) . "', shipping_company = '" . $this->db->escape($data['shipping_company']) . "', shipping_address_1 = '" . $this->db->escape($data['shipping_address_1']) . "', shipping_address_2 = '" . $this->db->escape($data['shipping_address_2']) . "', shipping_city = '" . $this->db->escape($data['shipping_city']) . "', shipping_postcode = '" . $this->db->escape($data['shipping_postcode']) . "', shipping_country = '" . $this->db->escape($shipping_country) . "', shipping_country_id = '" . (int)$data['shipping_country_id'] . "', shipping_zone = '" . $this->db->escape($shipping_zone) . "', shipping_zone_id = '" . (int)$data['shipping_zone_id'] . "', shipping_address_format = '" . $this->db->escape($shipping_address_format) . "', shipping_method = '" . $this->db->escape($data['shipping_method']) . "', shipping_code = '" . $this->db->escape($data['shipping_code']) . "', payment_firstname = '" . $this->db->escape($data['payment_firstname']) . "', payment_lastname = '" . $this->db->escape($data['payment_lastname']) . "', payment_company = '" . $this->db->escape($data['payment_company']) . "', payment_address_1 = '" . $this->db->escape($data['payment_address_1']) . "', payment_address_2 = '" . $this->db->escape($data['payment_address_2']) . "', payment_city = '" . $this->db->escape($data['payment_city']) . "', payment_postcode = '" . $this->db->escape($data['payment_postcode']) . "', payment_country = '" . $this->db->escape($payment_country) . "', payment_country_id = '" . (int)$data['payment_country_id'] . "', payment_zone = '" . $this->db->escape($payment_zone) . "', payment_zone_id = '" . (int)$data['payment_zone_id'] . "', payment_address_format = '" . $this->db->escape($payment_address_format) . "', payment_method = '" . $this->db->escape($data['payment_method']) . "', payment_code = '" . $this->db->escape($data['payment_code']) . "', comment = '" . $this->db->escape($data['comment']) . "', order_status_id = '" . (int)$data['order_status_id'] . "', affiliate_id  = '" . (int)$data['affiliate_id'] . "', language_id = '" . (int)$this->config->get('config_language_id') . "', currency_id = '" . (int)$currency_id . "', currency_code = '" . $this->db->escape($currency_code) . "', currency_value = '" . (float)$currency_value . "', date_added = NOW(), date_modified = NOW()");
      	
      	$order_id = $this->db->getLastId();
		
      	if (isset($data['order_product'])) {		
      		foreach ($data['order_product'] as $order_product) {	
      			$this->db->query("INSERT INTO " . DB_PREFIX . "order_product SET order_id = '" . (int)$order_id . "', product_id = '" . (int)$order_product['product_id'] . "', name = '" . $this->db->escape($order_product['name']) . "', model = '" . $this->db->escape($order_product['model']) . "', quantity = '" . (int)$order_product['quantity'] . "', price = '" . (float)$order_product['price'] . "', total = '" . (float)$order_product['total'] . "', tax = '" . (float)$order_product['tax'] . "', reward = '" . (int)$order_product['reward'] . "'");
			
				$order_product_id = $this->db->getLastId();
	
				if (isset($order_product['order_option'])) {
					foreach ($order_product['order_option'] as $order_option) {
						$this->db->query("INSERT INTO " . DB_PREFIX . "order_option SET order_id = '" . (int)$order_id . "', order_product_id = '" . (int)$order_product_id . "', product_option_id = '" . (int)$order_option['product_option_id'] . "', product_option_value_id = '" . (int)$order_option['product_option_value_id'] . "', name = '" . $this->db->escape($order_option['name']) . "', `value` = '" . $this->db->escape($order_option['value']) . "', `type` = '" . $this->db->escape($order_option['type']) . "'");
					}
				}
				
				if (isset($order_product['order_download'])) {
					foreach ($order_product['order_download'] as $order_download) {
						$this->db->query("INSERT INTO " . DB_PREFIX . "order_download SET order_id = '" . (int)$order_id . "', order_product_id = '" . (int)$order_product_id . "', name = '" . $this->db->escape($order_download['name']) . "', filename = '" . $this->db->escape($order_download['filename']) . "', mask = '" . $this->db->escape($order_download['mask']) . "', remaining = '" . (int)$order_download['remaining'] . "'");
					}
				}
			}
		}
		
		if (isset($data['order_voucher'])) {	
			foreach ($data['order_voucher'] as $order_voucher) {	
      			$this->db->query("INSERT INTO " . DB_PREFIX . "order_voucher SET order_id = '" . (int)$order_id . "', voucher_id = '" . (int)$order_voucher['voucher_id'] . "', description = '" . $this->db->escape($order_voucher['description']) . "', code = '" . $this->db->escape($order_voucher['code']) . "', from_name = '" . $this->db->escape($order_voucher['from_name']) . "', from_email = '" . $this->db->escape($order_voucher['from_email']) . "', to_name = '" . $this->db->escape($order_voucher['to_name']) . "', to_email = '" . $this->db->escape($order_voucher['to_email']) . "', voucher_theme_id = '" . (int)$order_voucher['voucher_theme_id'] . "', message = '" . $this->db->escape($order_voucher['message']) . "', amount = '" . (float)$order_voucher['amount'] . "'");
			
      			$this->db->query("UPDATE " . DB_PREFIX . "voucher SET order_id = '" . (int)$order_id . "' WHERE voucher_id = '" . (int)$order_voucher['voucher_id'] . "'");
			}
		}
		
		// Get the total
		$total = 0;
		
		if (isset($data['order_total'])) {		
      		foreach ($data['order_total'] as $order_total) {	
      			$this->db->query("INSERT INTO " . DB_PREFIX . "order_total SET order_id = '" . (int)$order_id . "', code = '" . $this->db->escape($order_total['code']) . "', title = '" . $this->db->escape($order_total['title']) . "', text = '" . $this->db->escape($order_total['text']) . "', `value` = '" . (float)$order_total['value'] . "', sort_order = '" . (int)$order_total['sort_order'] . "'");
			}
			
			$total += $order_total['value'];
		}
		 
		$this->db->query("UPDATE `" . DB_PREFIX . "order` SET total = '" . (float)$total . "' WHERE order_id = '" . (int)$order_id . "'"); 	
	}
	
	public function editOrder($order_id, $data) {
        $query = "UPDATE `". DB_PREFIX . "order` SET status_summary= '" . $data['status_summary'] . "' ";

        if (!empty($data['product_id'])) {
            $query .= ", product_id = '" . $data['product_id'] . "'";
        } else {
            $query .= ", product_id = NULL";
        }

        if (!empty($data['price'])) {
            $query .= ", price = '" . $data['price'] . "'";
        } else {
            $query .= ", price = NULL";
        }

        if (!empty($data['flavor_product_id'])) {
            $query .= ", flavor_product_id = '" . $data['flavor_product_id'] . "'";
        } else {
            $query .= ", flavor_product_id = NULL";
        }

        if (!empty($data['sender_id'])) {
            $query .= ", sender_id = '" . $data['sender_id'] . "'";
        } else {
            $query .= ", sender_id = NULL";
        }

        if (!empty($data['sender_name'])) {
            $query .= ", sender_name = '" . $data['sender_name'] . "'";
        } else {
            $query .= ", sender_name = NULL";
        }

        if (!empty($data['recipient_id'])) {
            $query .= ", recipient_id = '" . $data['recipient_id'] . "'";
        } else {
            $query .= ", recipient_id = NULL";
        }

        if (!empty($data['recipient_profile_id'])) {
            $query .= ", recipient_profile_id = '" . $data['recipient_profile_id'] . "'";
        } else {
            $query .= ", recipient_profile_id = NULL";
        }

        if (!empty($data['recipient_profile_network'])) {
            $query .= ", recipient_profile_network = '" . $data['recipient_profile_network'] . "'";
        } else {
            $query .= ", recipient_profile_network = NULL";
        }

        if (!empty($data['recipient_name'])) {
            $query .= ", recipient_name = '" . $data['recipient_name'] . "'";
        } else {
            $query .= ", recipient_name = NULL";
        }

        if (!empty($data['recipient_address_id'])) {
            $query .= ", recipient_address_id = '" . $data['recipient_address_id'] . "'";
        } else {
            $query .= ", recipient_address_id = NULL";
        }

        if (!empty($data['recipient_notification_date'])) {
            $query .= ", recipient_notification_date = '" . $data['recipient_notification_date'] . "'";
        } else {
            $query .= ", recipient_notification_date = NULL";
        }

        if (!empty($data['recipient_shipping_date'])) {
            $query .= ", recipient_shipping_date = '" . $data['recipient_shipping_date'] . "'";
        } else {
            $query .= ", recipient_shipping_date = NULL";
        }

        if (!empty($data['payment_method'])) {
            $query .= ", payment_method = '" . $data['payment_method'] . "'";
        } else {
            $query .= ", payment_method = NULL";
        }

        if (!empty($data['payment_info'])) {
            $query .= ", payment_info = '" . $data['payment_info'] . "'";
        } else {
            $query .= ", payment_info = NULL";
        }

        if (!empty($data['card_id'])) {
            $query .= ", card_id = '" . $data['card_id'] . "'";
        } else {
            $query .= ", card_id = NULL";
        }
        
        $query .= ", date_modified = NOW() WHERE order_id = '" . (int)$order_id . "'";

        $this->db->query($query);

	}

	public function editOrderStatus($order_id, $data) {
        foreach($data['order_statuses'] as $order_status) {
            $query = "UPDATE `". DB_PREFIX . "order_status` SET complete_reason = '" . $order_status['complete_reason'] . "' ";

            $query .= ", data_modified = NOW() WHERE order_id = '" . (int)$order_id . "' AND status = '" . $order_status['status'] . "'";
            
            $this->db->query($query);
        }
	}

	public function deleteOrder($order_id) {
		$order_query = $this->db->query("SELECT * FROM `" . DB_PREFIX . "order` WHERE order_status_id > '0' AND order_id = '" . (int)$order_id . "'");

		if ($order_query->num_rows) {
			$product_query = $this->db->query("SELECT * FROM " . DB_PREFIX . "order_product WHERE order_id = '" . (int)$order_id . "'");

			foreach($product_query->rows as $product) {
				$this->db->query("UPDATE `" . DB_PREFIX . "product` SET quantity = (quantity + " . (int)$product['quantity'] . ") WHERE product_id = '" . (int)$product['product_id'] . "' AND subtract = '1'");

				$option_query = $this->db->query("SELECT * FROM " . DB_PREFIX . "order_option WHERE order_id = '" . (int)$order_id . "' AND order_product_id = '" . (int)$product['order_product_id'] . "'");

				foreach ($option_query->rows as $option) {
					$this->db->query("UPDATE " . DB_PREFIX . "product_option_value SET quantity = (quantity + " . (int)$product['quantity'] . ") WHERE product_option_value_id = '" . (int)$option['product_option_value_id'] . "' AND subtract = '1'");
				}
			}
		}

		$this->db->query("DELETE FROM `" . DB_PREFIX . "order` WHERE order_id = '" . (int)$order_id . "'");
		$this->db->query("DELETE FROM " . DB_PREFIX . "order_product WHERE order_id = '" . (int)$order_id . "'");
      	$this->db->query("DELETE FROM " . DB_PREFIX . "order_option WHERE order_id = '" . (int)$order_id . "'");
		$this->db->query("DELETE FROM " . DB_PREFIX . "order_download WHERE order_id = '" . (int)$order_id . "'");
		$this->db->query("DELETE FROM " . DB_PREFIX . "order_voucher WHERE order_id = '" . (int)$order_id . "'");
      	$this->db->query("DELETE FROM " . DB_PREFIX . "order_total WHERE order_id = '" . (int)$order_id . "'");
		$this->db->query("DELETE FROM " . DB_PREFIX . "order_history WHERE order_id = '" . (int)$order_id . "'");
		$this->db->query("DELETE FROM " . DB_PREFIX . "order_fraud WHERE order_id = '" . (int)$order_id . "'");
		$this->db->query("DELETE FROM " . DB_PREFIX . "customer_transaction WHERE order_id = '" . (int)$order_id . "'");
		$this->db->query("DELETE FROM " . DB_PREFIX . "customer_reward WHERE order_id = '" . (int)$order_id . "'");
		$this->db->query("DELETE FROM " . DB_PREFIX . "affiliate_transaction WHERE order_id = '" . (int)$order_id . "'");
	}

	public function getOrder($order_id) {
		//$order_query = $this->db->query("SELECT *, (SELECT CONCAT(c.firstname, ' ', c.lastname) FROM " . DB_PREFIX . "customer c WHERE c.customer_id = o.customer_id) AS customer FROM `" . DB_PREFIX . "order` o WHERE o.order_id = '" . (int)$order_id . "'");
		$order_query = $this->db->query("SELECT * FROM `" . DB_PREFIX . "order`  WHERE order_id = '" . (int)$order_id . "'");

		if ($order_query->num_rows) {
			$reward = 0;
			
            return $order_query->row;

		} else {
			return false;
		}
	}
	
	public function getOrders($data = array()) {
		//$sql = "SELECT o.order_id, CONCAT(o.firstname, ' ', o.lastname) AS customer, (SELECT os.name FROM " . DB_PREFIX . "order_status os WHERE os.order_status_id = o.order_status_id') AS status, o.total, o.currency_code, o.currency_value, o.date_added, o.date_modified FROM `" . DB_PREFIX . "order` o";
        $sql = "SELECT o.order_id, o.short_track_code, o.pay_track_code, o.track_code, o.sender_name, o.recipient_name, o.status_summary, o.price, o.shipping_cost, o.supplier_error, o.date_added, o.date_modified, osp.status as place_purchase_status FROM `" . DB_PREFIX . "order` o LEFT JOIN order_status osp ON (o.order_id=osp.order_id AND (osp.status='place_purchase' OR osp.status='order_notification' ) AND osp.complete_time is NULL)  ";

        if (!empty($data['supplier_id'])) {
            $sql .= " LEFT JOIN product_supplier ps ON (o.product_id = ps.product_id) WHERE ps.supplier_id = '" . (int)$data['supplier_id'] . "' AND exists (SELECT status FROM order_status as os WHERE os.order_id = o.order_id AND os.status = 'place_purchase')";
        } else {
            $sql .= " WHERE TRUE ";
        }

        if (!empty($data['filter_order_status']) && $data['filter_order_status'] != "*" ) {
            $tmp = explode('.', $data['filter_order_status']);
            $status_name = $tmp[0];
            $complete_reason = $tmp[1];

            if ($complete_reason == "not_start") {
                $sql .= " AND not exists (SELECT * FROM order_status as os WHERE os.order_id = o.order_id AND os.status = '" . $status_name . "') ";
            } else if ($complete_reason == "not_complete") {
                $sql .= " AND exists (SELECT * FROM order_status as os WHERE os.order_id = o.order_id AND os.status = '" . $status_name . "' AND (os.complete_reason is NULL OR os.complete_time is NULL)) ";
            } else {
                $sql .= " AND exists (SELECT * FROM order_status as os WHERE os.order_id = o.order_id AND os.status = '" . $status_name . "' AND os.complete_reason = '" . $complete_reason . "') ";
            }
        } else {
            //$sql .= " AND o.order_id > 0 ";
        }


		if (isset($data['filter_order_status_summary']) && !is_null($data['filter_order_status_summary'])) {
			$sql .= " AND o.status_summary = '" . $data['filter_order_status_summary'] . "'";
		}

        if (!empty($data['filter_supplier_status']) && $data['filter_supplier_status'] != "*") {
            if ($data['filter_supplier_status'] == 'waiting') {
                $sql .= " AND exists (SELECT status FROM order_status as os WHERE os.order_id = o.order_id AND os.status = 'place_purchase' AND os.complete_reason is NULL) ";
            } elseif ($data['filter_supplier_status'] == 'complete') {
                $sql .= " AND exists (SELECT status FROM order_status as os WHERE os.order_id = o.order_id AND os.status = 'place_purchase' AND os.complete_reason = 'success') ";
            } elseif ($data['filter_supplier_status'] == 'aborted') {
                $sql .= " AND exists (SELECT status FROM order_status as os WHERE os.order_id = o.order_id AND os.status = 'place_purchase' AND os.complete_reason is not NULL AND os.complete_reason != 'success') ";
            }
        }

        if (isset($data['filter_supplier_error']) && $data['filter_supplier_error'] != "*") {
            $sql .= " AND o.supplier_error = '" . (int)$data['filter_supplier_error'] . "'";
        }

		if (!empty($data['filter_order_id'])) {
			$sql .= " AND o.order_id = '" . (int)$data['filter_order_id'] . "'";
		}

		if (!empty($data['filter_short_track_code'])) {
			$sql .= " AND o.short_track_code = '" . $data['filter_short_track_code'] . "'";
		}

		if (!empty($data['filter_pay_track_code'])) {
			$sql .= " AND o.pay_track_code = '" . $data['filter_pay_track_code'] . "'";
		}

		if (!empty($data['filter_sender_name'])) {
			$sql .= " AND o.sender_name LIKE '%" . $this->db->escape($data['filter_sender_name']) . "%'";
		}

		if (!empty($data['filter_recipient_name'])) {
			$sql .= " AND o.recipient_name LIKE '%" . $this->db->escape(($data['filter_recipient_name'])) . "%'";
		}

		if (!empty($data['filter_date_added'])) {
			$sql .= " AND DATE(o.date_added) = DATE('" . $this->db->escape($data['filter_date_added']) . "')";
		}

		if (!empty($data['filter_date_modified'])) {
			$sql .= " AND DATE(o.date_modified) = DATE('" . $this->db->escape($data['filter_date_modified']) . "')";
		}

		if (!empty($data['filter_price'])) {
			$sql .= " AND o.price = '" . (float)$data['filter_price'] . "'";
		}

		$sort_data = array(
			'o.order_id',
			'o.sender_name',
            'o.recipient_name',
			'o.status_summary',
			'o.date_added',
			'o.date_modified',
            'osp.status',
			'o.total'
		);

        if (isset($data['sort']) && $data['sort'] == 'osp.status') {
            $sql .= " ORDER BY " . $data['sort'] . " DESC, o.order_id ";
        } else {

            if (isset($data['sort']) && in_array($data['sort'], $sort_data)) {
                $sql .= " ORDER BY " . $data['sort'];
            } else {
                $sql .= " ORDER BY o.order_id";
            }
        }

		if (isset($data['order']) && ($data['order'] == 'DESC')) {
			$sql .= " DESC";
		} else {
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
        //var_dump($query->rows);
        //var_dump($data['filter_order_status']);

        /*
        if (!empty($data['filter_order_status']) && $data['filter_order_status'] != "*" ) {
            $tmp = explode('.', $data['filter_order_status']);
            $status_name = $tmp[0];
            $complete_reason = $tmp[1];

            $result = array();
            foreach ($query->rows as $row) {

                $status_row = $this->getOrderStatus($row['order_id'], $status_name);
                //var_dump($status_row);
                if (empty($status_row) && $complete_reason == 'not_start') {
                    $result []= $row;
                } else {
                    //var_dump($status_row);
                    //var_dump($row);

                    if ((!isset($status_row['complete_reason']) || empty($status_row['complete_reason']) 
                         || !isset($status_row['complete_time']) || empty($status_row['complete_time']))
                        && !empty($status_row['start_time'])
                        && $complete_reason == 'not_complete') {

                        $result []= $row;
                    } else {
                        if (isset($status_row['complete_reason']) && $status_row['complete_reason'] == $complete_reason) {
                            $result []= $row;
                        }
                    }
                    
                }
            }

            return $result;
        } else {
            return $query->rows;
        }
        */
        return $query->rows;
	}

	public function getOrderProducts($order_id) {
		$query = $this->db->query("SELECT * FROM " . DB_PREFIX . "order_product WHERE order_id = '" . (int)$order_id . "'");
		
		return $query->rows;
	}
	
	public function getOrderOption($order_id, $order_option_id) {
		$query = $this->db->query("SELECT * FROM " . DB_PREFIX . "order_option WHERE order_id = '" . (int)$order_id . "' AND order_option_id = '" . (int)$order_option_id . "'");

		return $query->row;
	}
	
	public function getOrderOptions($order_id, $order_product_id) {
		$query = $this->db->query("SELECT * FROM " . DB_PREFIX . "order_option WHERE order_id = '" . (int)$order_id . "' AND order_product_id = '" . (int)$order_product_id . "'");

		return $query->rows;
	}

	public function getOrderDownloads($order_id, $order_product_id) {
		$query = $this->db->query("SELECT * FROM " . DB_PREFIX . "order_download WHERE order_id = '" . (int)$order_id . "' AND order_product_id = '" . (int)$order_product_id . "'");

		return $query->rows;
	}
	
	public function getOrderVouchers($order_id) {
		$query = $this->db->query("SELECT * FROM " . DB_PREFIX . "order_voucher WHERE order_id = '" . (int)$order_id . "'");
		
		return $query->rows;
	}
	
	public function getOrderVoucherByVoucherId($voucher_id) {
      	$query = $this->db->query("SELECT * FROM `" . DB_PREFIX . "order_voucher` WHERE voucher_id = '" . (int)$voucher_id . "'");

		return $query->row;
	}
				
	public function getOrderTotals($order_id) {
		$query = $this->db->query("SELECT * FROM " . DB_PREFIX . "order_total WHERE order_id = '" . (int)$order_id . "' ORDER BY sort_order");

		return $query->rows;
	}

	public function getTotalOrders($data = array()) {
      	$sql = "SELECT COUNT(*) AS total FROM `" . DB_PREFIX . "order` as o ";

        if (!empty($data['supplier_id'])) {
            $sql .= " LEFT JOIN product_supplier ps ON (o.product_id = ps.product_id) WHERE ps.supplier_id = '" . (int)$data['supplier_id'] . "' AND exists (SELECT status FROM order_status as os WHERE os.order_id = o.order_id AND os.status = 'place_purchase')";
        } else {
            $sql .= " WHERE TRUE ";
        }

        if (!empty($data['filter_order_status']) && $data['filter_order_status'] != "*" ) {
            $tmp = explode('.', $data['filter_order_status']);
            $status_name = $tmp[0];
            $complete_reason = $tmp[1];

            if ($complete_reason == "not_start") {
                $sql .= " AND not exists (SELECT * FROM order_status as os WHERE os.order_id = o.order_id AND os.status = '" . $status_name . "') ";
            } else if ($complete_reason == "not_complete") {
                $sql .= " AND exists (SELECT * FROM order_status as os WHERE os.order_id = o.order_id AND os.status = '" . $status_name . "' AND (os.complete_reason is NULL OR os.complete_time is NULL)) ";
            } else {
                $sql .= " AND exists (SELECT * FROM order_status as os WHERE os.order_id = o.order_id AND os.status = '" . $status_name . "' AND os.complete_reason = '" . $complete_reason . "') ";
            }
        } else {
            // $sql .= " AND o.order_id >= 0 ";
        }

        if (!empty($data['filter_supplier_status']) && $data['filter_supplier_status'] != "*") {
            if ($data['filter_supplier_status'] == 'waiting') {
                $sql .= " AND exists (SELECT status FROM order_status as os WHERE os.order_id = o.order_id AND os.status = 'place_purchase' AND os.complete_reason is NULL) ";
            } elseif ($data['filter_supplier_status'] == 'complete') {
                $sql .= " AND exists (SELECT status FROM order_status as os WHERE os.order_id = o.order_id AND os.status = 'place_purchase' AND os.complete_reason = 'success') ";
            } elseif ($data['filter_supplier_status'] == 'aborted') {
                $sql .= " AND exists (SELECT status FROM order_status as os WHERE os.order_id = o.order_id AND os.status = 'place_purchase' AND os.complete_reason is not NULL AND os.complete_reason != 'success') ";
            }
        }
        
		if (isset($data['filter_order_status_summary']) && !is_null($data['filter_order_status_summary'])) {
			$sql .= " AND o.status_summary = '" . $data['filter_order_status_summary'] . "'";
		}

        if (isset($data['filter_supplier_error']) && $data['filter_supplier_error'] != "*") {
            $sql .= " AND o.supplier_error = '" . (int)$data['filter_supplier_error'] . "'";
        }

		if (!empty($data['filter_order_id'])) {
			$sql .= " AND o.order_id = '" . (int)$data['filter_order_id'] . "'";
		}
		if (!empty($data['filter_short_track_code'])) {
			$sql .= " AND o.short_track_code = '" . $data['filter_short_track_code'] . "'";
		}

		if (!empty($data['filter_pay_track_code'])) {
			$sql .= " AND o.pay_track_code = '" . $data['filter_pay_track_code'] . "'";
		}

		if (!empty($data['filter_sender_name'])) {
			$sql .= " AND o.sender_name LIKE '%" . $this->db->escape($data['filter_sender_name']) . "%'";
		}

		if (!empty($data['filter_recipient_name'])) {
			$sql .= " AND o.sender_name LIKE '%" . $this->db->escape($data['filter_recipient_name']) . "%'";
		}

		if (!empty($data['filter_date_added'])) {
			$sql .= " AND DATE(o.date_added) = DATE('" . $this->db->escape($data['filter_date_added']) . "')";
		}

		if (!empty($data['filter_date_modified'])) {
			$sql .= " AND DATE(o.date_modified) = DATE('" . $this->db->escape($data['filter_date_modified']) . "')";
		}

		if (!empty($data['filter_price'])) {
			$sql .= " AND o.price = '" . (float)$data['filter_price'] . "'";
		}

		$query = $this->db->query($sql);

		return $query->row['total'];
	}

	public function getTotalOrdersByStoreId($store_id) {
      	$query = $this->db->query("SELECT COUNT(*) AS total FROM `" . DB_PREFIX . "order` WHERE store_id = '" . (int)$store_id . "'");

		return $query->row['total'];
	}

	public function getTotalOrdersByOrderStatusId($order_status_id) {
      	$query = $this->db->query("SELECT COUNT(*) AS total FROM `" . DB_PREFIX . "order` WHERE order_status_id = '" . (int)$order_status_id . "' AND order_status_id > '0'");

		return $query->row['total'];
	}

	public function getTotalOrdersByLanguageId($language_id) {
      	$query = $this->db->query("SELECT COUNT(*) AS total FROM `" . DB_PREFIX . "order` WHERE language_id = '" . (int)$language_id . "' AND order_status_id > '0'");

		return $query->row['total'];
	}

	public function getTotalOrdersByCurrencyId($currency_id) {
      	$query = $this->db->query("SELECT COUNT(*) AS total FROM `" . DB_PREFIX . "order` WHERE currency_id = '" . (int)$currency_id . "' AND order_status_id > '0'");

		return $query->row['total'];
	}
	
	public function getTotalSales() {
      	$query = $this->db->query("SELECT SUM(total) AS total FROM `" . DB_PREFIX . "order` WHERE order_status_id > '0'");

		return $query->row['total'];
	}

	public function getTotalSalesByYear($year) {
      	$query = $this->db->query("SELECT SUM(total) AS total FROM `" . DB_PREFIX . "order` WHERE order_status_id > '0' AND YEAR(date_added) = '" . (int)$year . "'");

		return $query->row['total'];
	}

	public function createInvoiceNo($order_id) {
		$order_info = $this->getOrder($this->request->get['order_id']);
			
		if ($order_info && !$order_info['invoice_no']) {
			$query = $this->db->query("SELECT MAX(invoice_no) AS invoice_no FROM `" . DB_PREFIX . "order` WHERE invoice_prefix = '" . $this->db->escape($order_info['invoice_prefix']) . "'");
	
			if ($query->row['invoice_no']) {
				$invoice_no = $query->row['invoice_no'] + 1;
			} else {
				$invoice_no = 1;
			}
		
			$this->db->query("UPDATE `" . DB_PREFIX . "order` SET invoice_no = '" . (int)$invoice_no . "', invoice_prefix = '" . $this->db->escape($order_info['invoice_prefix']) . "' WHERE order_id = '" . (int)$order_id . "'");
			
			return $order_info['invoice_prefix'] . $invoice_no;
		}
	}
	
	public function addOrderHistory($order_id, $data) {
		$this->db->query("UPDATE `" . DB_PREFIX . "order` SET order_status_id = '" . (int)$data['order_status_id'] . "', date_modified = NOW() WHERE order_id = '" . (int)$order_id . "'");

		$this->db->query("INSERT INTO " . DB_PREFIX . "order_history SET order_id = '" . (int)$order_id . "', order_status_id = '" . (int)$data['order_status_id'] . "', notify = '" . (isset($data['notify']) ? (int)$data['notify'] : 0) . "', comment = '" . $this->db->escape(strip_tags($data['comment'])) . "', date_added = NOW()");

		$order_info = $this->getOrder($order_id);

		// Send out any gift voucher mails
		if ($this->config->get('config_complete_status_id') == $data['order_status_id']) {
			$this->load->model('sale/voucher');

			$results = $this->getOrderVouchers($order_id);
			
			foreach ($results as $result) {
				$this->model_sale_voucher->sendVoucher($result['voucher_id']);
			}
		}

      	if ($data['notify']) {
			$language = new Language($order_info['language_directory']);
			$language->load($order_info['language_filename']);
			$language->load('mail/order');

			$subject = sprintf($language->get('text_subject'), $order_info['store_name'], $order_id);

			$message  = $language->get('text_order') . ' ' . $order_id . "\n";
			$message .= $language->get('text_date_added') . ' ' . date($language->get('date_format_short'), strtotime($order_info['date_added'])) . "\n\n";
			
			$order_status_query = $this->db->query("SELECT * FROM " . DB_PREFIX . "order_status WHERE order_status_id = '" . (int)$data['order_status_id'] . "' AND language_id = '" . (int)$order_info['language_id'] . "'");
				
			if ($order_status_query->num_rows) {
				$message .= $language->get('text_order_status') . "\n";
				$message .= $order_status_query->row['name'] . "\n\n";
			}
			
			if ($order_info['customer_id']) {
				$message .= $language->get('text_link') . "\n";
				$message .= html_entity_decode($order_info['store_url'] . 'index.php?route=account/order/info&order_id=' . $order_id, ENT_QUOTES, 'UTF-8') . "\n\n";
			}
			
			if ($data['comment']) {
				$message .= $language->get('text_comment') . "\n\n";
				$message .= strip_tags(html_entity_decode($data['comment'], ENT_QUOTES, 'UTF-8')) . "\n\n";
			}

			$message .= $language->get('text_footer');

			$mail = new Mail();
			$mail->protocol = $this->config->get('config_mail_protocol');
			$mail->parameter = $this->config->get('config_mail_parameter');
			$mail->hostname = $this->config->get('config_smtp_host');
			$mail->username = $this->config->get('config_smtp_username');
			$mail->password = $this->config->get('config_smtp_password');
			$mail->port = $this->config->get('config_smtp_port');
			$mail->timeout = $this->config->get('config_smtp_timeout');
			$mail->setTo($order_info['email']);
			$mail->setFrom($this->config->get('config_email'));
			$mail->setSender($order_info['store_name']);
			$mail->setSubject(html_entity_decode($subject, ENT_QUOTES, 'UTF-8'));
			$mail->setText(html_entity_decode($message, ENT_QUOTES, 'UTF-8'));
			$mail->send();
		}
	}
		
	public function getOrderHistories($order_id, $start = 0, $limit = 10) {
		$query = $this->db->query("SELECT oh.date_added, os.name AS status, oh.comment, oh.notify FROM " . DB_PREFIX . "order_history oh LEFT JOIN " . DB_PREFIX . "order_status os ON oh.order_status_id = os.order_status_id WHERE oh.order_id = '" . (int)$order_id . "' AND os.language_id = '" . (int)$this->config->get('config_language_id') . "' ORDER BY oh.date_added ASC LIMIT " . (int)$start . "," . (int)$limit);

		return $query->rows;
	}
	
	public function getTotalOrderHistories($order_id) {
	  	$query = $this->db->query("SELECT COUNT(*) AS total FROM " . DB_PREFIX . "order_history WHERE order_id = '" . (int)$order_id . "'");

		return $query->row['total'];
	}	
		
	public function getTotalOrderHistoriesByOrderStatusId($order_status_id) {
	  	$query = $this->db->query("SELECT COUNT(*) AS total FROM " . DB_PREFIX . "order_history WHERE order_status_id = '" . (int)$order_status_id . "'");

		return $query->row['total'];
	}	
	
	public function getEmailsByProductsOrdered($products, $start, $end) {
		$implode = array();
		
		foreach ($products as $product_id) {
			$implode[] = "op.product_id = '" . $product_id . "'";
		}
		
		$query = $this->db->query("SELECT DISTINCT email FROM `" . DB_PREFIX . "order` o LEFT JOIN " . DB_PREFIX . "order_product op ON (o.order_id = op.order_id) WHERE (" . implode(" OR ", $implode) . ") AND o.order_status_id <> '0'");
	
		return $query->rows;
	}
	
	public function getTotalEmailsByProductsOrdered($products) {
		$implode = array();
		
		foreach ($products as $product_id) {
			$implode[] = "op.product_id = '" . $product_id . "'";
		}
				
		$query = $this->db->query("SELECT COUNT(DISTINCT email) AS total FROM `" . DB_PREFIX . "order` o LEFT JOIN " . DB_PREFIX . "order_product op ON (o.order_id = op.order_id) WHERE (" . implode(" OR ", $implode) . ") AND o.order_status_id <> '0'");
	
		return $query->row['total'];
	}

    public function getOrderStatuses($order_id) {
        $query = $this->db->query("SELECT * FROM `" . DB_PREFIX . "order_status` WHERE order_id = '" . (int)$order_id . "'");
  
        return $query->rows;
    }

    public function getLastModifiedStatus($order_id) {
        $query = $this->db->query("SELECT * FROM `" . DB_PREFIX . "order_status` WHERE order_id = '" . (int)$order_id . "' order by start_time desc");
        if (!$query->row) {
            $query->row = array();
        }

        return $query->row;
    }

    public function getOrderStatus($order_id, $status) {
        $query = $this->db->query("SELECT * FROM `" . DB_PREFIX . "order_status` WHERE order_id = '" . (int)$order_id . "' AND status = '" . $status . "'");
        if (count($query->rows) > 0)
            return $query->rows[0];
        else
            return array();
    }

    public function getRecipientNotifications($order_id) {
        $query = $this->db->query("SELECT * FROM `" . DB_PREFIX . "order_recipient_notification` WHERE order_id = '" . (int)$order_id . "'");
  
        return $query->rows;
    }

    public function getPaymentNotifications($order_id) {
        $query = $this->db->query("SELECT * FROM `" . DB_PREFIX . "order_payment_notification` WHERE order_id = '" . (int)$order_id . "'");
  
        return $query->rows;
    }

    public function getCardContent($order_id) {
        $query = $this->db->query("SELECT * FROM `" . DB_PREFIX . "order_card_content` WHERE order_id = '" . (int)$order_id . "'");
  
        return $query->row;
    }

    public function confirmInventory($order_id) {
        $this->db->query("UPDATE " . DB_PREFIX . "order_status SET complete_reason = 'success', complete_time = NOW(), log = concat(log, concat_ws(',', status, iteration, unix_timestamp(now())), ',admin', ';') WHERE order_id = '" . (int)$order_id . "' AND status = 'place_purchase'");
        

        $this->db->query("UPDATE `order` SET date_modified = NOW() WHERE order_id = '" . $order_id . "'");

        // update product quantity
        $this->db->query("UPDATE product_supplier ps, `order` as o SET ps.ordered_quantity = ps.ordered_quantity + 1, ps.hold_quantity = ps.hold_quantity - 1, ps.quantity = ps.quantity - 1 WHERE ps.product_id = o.product_id AND o.order_id = '" . (int)$order_id . "'");

        $nc = new NotifyClient(NOTIFY_SERVER_HOST,  NOTIFY_SERVER_PORT);
        $nc->init();
        $nc->push(3, 0, 1, $order_id);

    }

    public function cancelOrder($order_id) {
        // get place_purchase status
        $query = $this->db->query("SELECT * FROM order_status WHERE order_id = '" . (int)$order_id . "' AND status = 'place_purchase'");
        
        // if the order is not delivered, then we can restore the avaiable quantity
        if (!$query->row || empty($query->row['complete_reason'])) {
            $this->db->query("UPDATE product_supplier ps, `order` as o SET ps.hold_quantity = ps.hold_quantity - 1 WHERE ps.product_id = o.product_id AND o.order_id = '" . (int)$order_id . "'");
        }

        $this->db->query("UPDATE " . DB_PREFIX . "order_status SET complete_reason = 'aborted', complete_time = NOW(), log = concat(log, concat_ws(',', status, iteration, unix_timestamp(now())), ',admin', ';') WHERE order_id = '" . (int)$order_id . "' AND complete_reason IS NULL ");

        // insert an aborted close_order whenever it is already existing.
        $this->db->query("INSERT INTO " . DB_PREFIX . "order_status (order_id, status, start_time, complete_time, complete_reason, log) VALUES ('" . (int)$order_id . "', 'close_order', NOW(), NOW(), 'aborted', concat(log, concat_ws(',', status, iteration, unix_timestamp(now())), ',admin', ';'))");

        $this->db->query("UPDATE " . DB_PREFIX . "`order` SET status_summary = 'aborted', date_modified = NOW() WHERE order_id = '" . (int)$order_id . "'");
        
        $nc = new NotifyClient(NOTIFY_SERVER_HOST,  NOTIFY_SERVER_PORT);
        $nc->init();
        $nc->push(8, 0, 1, $order_id);
        
    }

    public function confirmReceiveGift($order_id) {
        $this->db->query("UPDATE " . DB_PREFIX . "order_status SET complete_reason = 'success', complete_time = NOW(), log = concat(log, concat_ws(',', status, iteration, unix_timestamp(now())), ',admin', ';') WHERE order_id = '" . (int)$order_id . "' AND status = 'order_notification' ");

        $this->db->query("UPDATE `order` SET date_modified = NOW() WHERE order_id = '" . $order_id . "'");

        $nc = new NotifyClient(NOTIFY_SERVER_HOST,  NOTIFY_SERVER_PORT);
        $nc->init();
        $nc->push(4, 0, 1, $order_id);

        // insert an aborted close_order whenever it is already existing.
        //$this->db->query("INSERT INTO " . DB_PREFIX . "order_status (order_id, status, start_time, complete_time, complete_reason, log) VALUES ('" . (int)$order_id . "', 'close_order', NOW(), NOW(), 'aborted', concat(log, concat_ws(',', status, iteration, unix_timestamp(now())), ',admin', ';'))");

        //$this->db->query("UPDATE " . DB_PREFIX . "`order` SET status_summary = 'aborted', date_modified = NOW() WHERE order_id = '" . (int)$order_id . "'");
        
    }

    public function saveShippingInfo($order_id, $data) {
        $this->db->query("UPDATE " . DB_PREFIX . " `order` SET shipping_method = '" . $data['shipping_method'] . "', shipping_code = '" . $data['shipping_code'] . "', shipping_cost = '" . $data['shipping_cost'] . "', date_modified = NOW() WHERE order_id = '" . (int)$order_id . "'");
        
    }

    public function reportSupplierError($order_id, $data) {
        $this->db->query("UPDATE " . DB_PREFIX . " `order` SET supplier_error = '1', date_modified = NOW() WHERE order_id = '" . (int)$order_id . "'");
    }

    public function clearSupplierError($order_id, $data) {
        $this->db->query("UPDATE " . DB_PREFIX . " `order` SET supplier_error = '0', date_modified = NOW() WHERE order_id = '" . (int)$order_id . "'");
    }

    public function getShippingHistory($order_id) {
        $query = $this->db->query("SELECT * FROM `order_ship_tracking` WHERE order_id = '" . (int)$order_id . "'");
        return $query->rows;
    }

    public function hasPermissionForSupplier($order_id, $supplier_id) {
        $query = $this->db->query("SELECT * FROM `order` o LEFT JOIN product_supplier ps ON (o.product_id = ps.product_id) WHERE o.order_id = '" . (int)$order_id . "' AND ps.supplier_id = '" . (int)$supplier_id . "'");
        if ($query->row)
            return true;
        else
            return false;
    }
    
    public function getOrderIdFromShortTrackCode($short_track_code) {
        $query = $this->db->query("SELECT order_id FROM `order` WHERE short_track_code = '" . $short_track_code . "'");
        if ($query->row) {
            return $query->row['order_id'];
        } else {
            return null;
        }
    }

    public function getSupplierOrderStatus($order_info, $order_statuses) {
        $supplier_status = "";
        if ( $order_info['status_summary'] == 'complete' ) {
            $supplier_status = 'complete';
        } elseif ( $order_info['status_summary'] == 'expired' || $order_info['status_summary'] == 'cancel' || $order_info['status_summary'] == 'aborted') {
            $supplier_status = 'aborted';
        } else {
            foreach ($order_statuses as $order_status) {
                if ($order_status['status'] == 'place_purchase') {
                    if ($order_status['complete_reason'] == 'success') {
                        $supplier_status = 'complete';
                    } elseif (empty($order_status['complete_reason'])) {
                        $supplier_status = 'waiting';
                    } else {
                        $supplier_status = 'aborted';
                    }
                }
            }
        }
        
        return $supplier_status;
    }

    public function getAllSupplierStatus() {
        return array('waiting', 'complete', 'aborted');
    }

    
}

?>