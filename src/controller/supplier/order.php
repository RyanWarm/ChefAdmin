<?php
class ControllerSupplierOrder extends Controller {
	private $error = array();

  	public function index() {
		$this->load->language('sale/order');

		$this->document->setTitle($this->language->get('heading_title'));

		$this->load->model('sale/order');

        if (isset($this->request->get['order_id'])) {
            die("Invalid parameter: order_id");
        }
        
        if (isset($this->request->get['short_track_code'])) {
            $this->request->get['order_id'] = $this->model_sale_order->getOrderIdFromShortTrackCode($this->request->get['short_track_code']);
        } 

        if (isset($this->request->get['order_id']) && $this->user->getSupplierId() && !$this->model_sale_order->hasPermissionForSupplier($this->request->get['order_id'], $this->user->getSupplierId())) {
            return $this->forward('error/permission');
        }

    	$this->getList();
  	}

    private function generateURL() {
        $url = "";

		if (isset($this->request->get['filter_order_id'])) {
			$url .= '&filter_order_id=' . $this->request->get['filter_order_id'];
		}
		
		if (isset($this->request->get['filter_sender_name'])) {
			$url .= '&filter_sender_name=' . $this->request->get['filter_sender_name'];
		}

		if (isset($this->request->get['filter_recipient_name'])) {
			$url .= '&filter_recipient_name=' . $this->request->get['filter_recipient_name'];
		}
											
		if (isset($this->request->get['filter_order_status_summary'])) {
			$url .= '&filter_order_status_summary=' . $this->request->get['filter_order_status_summary'];
		}
		
		if (isset($this->request->get['filter_price'])) {
			$url .= '&filter_price=' . $this->request->get['filter_price'];
		}

        if (isset($this->request->get['filter_order_status'])) {
            $url .= '&filter_order_status=' . $this->request->get['filter_order_status'];
        }

        if (isset($this->request->get['filter_supplier_status'])) {
            $url .= '&filter_supplier_status=' . $this->request->get['filter_supplier_status'];
        }

        if (isset($this->request->get['filter_supplier_error'])) {
            $url .= '&filter_supplier_error=' . $this->request->get['filter_supplier_error'];
        }
		
		if (isset($this->request->get['filter_date_added'])) {
			$url .= '&filter_date_added=' . $this->request->get['filter_date_added'];
		}
		
		if (isset($this->request->get['filter_date_modified'])) {
			$url .= '&filter_date_modified=' . $this->request->get['filter_date_modified'];
		}

        return $url;
    }

    private function generatePagingURL() {
        $url = $this->generateURL();

		if (isset($this->request->get['sort'])) {
			$url .= '&sort=' . $this->request->get['sort'];
		}

		if (isset($this->request->get['order'])) {
			$url .= '&order=' . $this->request->get['order'];
		}
		
		if (isset($this->request->get['page'])) {
			$url .= '&page=' . $this->request->get['page'];
		}
	
        return $url;
    }

    
  	public function update() {
		$this->load->language('sale/order');

		$this->document->setTitle($this->language->get('heading_title'));

		$this->load->model('sale/order');

        if (isset($this->request->get['order_id'])) {
            die("Invalid parameter: order_id");
        }

        if (isset($this->request->get['short_track_code'])) {
            $this->request->get['order_id'] = $this->model_sale_order->getOrderIdFromShortTrackCode($this->request->get['short_track_code']);
        } 
    	
        if (isset($this->request->get['order_id']) && $this->user->getSupplierId() && !$this->model_sale_order->hasPermissionForSupplier($this->request->get['order_id'], $this->user->getSupplierId())) {
            return $this->forward('error/permission');
        }

		if (($this->request->server['REQUEST_METHOD'] == 'POST') && $this->validateForm()) {
		}
		
    	$this->getForm();
  	}
    

  	public function updateOrderStatus() {
		$this->load->language('sale/order');

		$this->document->setTitle($this->language->get('heading_title'));

		$this->load->model('sale/order');

        if (isset($this->request->get['order_id'])) {
            die("Invalid parameter: order_id");
        }

        if (isset($this->request->get['short_track_code'])) {
            $this->request->get['order_id'] = $this->model_sale_order->getOrderIdFromShortTrackCode($this->request->get['short_track_code']);
        }

        if (isset($this->request->get['order_id']) && $this->user->getSupplierId() && !$this->model_sale_order->hasPermissionForSupplier($this->request->get['order_id'], $this->user->getSupplierId())) {
            return $this->forward('error/permission');
        }

		if (($this->request->server['REQUEST_METHOD'] == 'POST')) {
            $action = $this->request->get['action'];
            if ($action == 'confirm_inventory') {
                $this->model_sale_order->confirmInventory($this->request->get['order_id']);
            }

			$this->session->data['success'] = $this->language->get('text_success');
	  
			$url = $this->generatePagingURL();

			$this->redirect($this->url->link('supplier/order/update', 'token=' . $this->session->data['token'] . '&short_track_code=' . $this->request->get['short_track_code'] . $url, 'SSL'));
		}
		
    	$this->getForm();
  	}

  	public function saveShippingInfo() {
		$this->load->language('sale/order');

		$this->document->setTitle($this->language->get('heading_title'));

		$this->load->model('sale/order');

        if (isset($this->request->get['order_id'])) {
            die("Invalid parameter: order_id");
        }

        if (isset($this->request->get['short_track_code'])) {
            $this->request->get['order_id'] = $this->model_sale_order->getOrderIdFromShortTrackCode($this->request->get['short_track_code']);
        }

        if (isset($this->request->get['order_id']) && $this->user->getSupplierId() && !$this->model_sale_order->hasPermissionForSupplier($this->request->get['order_id'], $this->user->getSupplierId())) {
            return $this->forward('error/permission');
        }

		if (($this->request->server['REQUEST_METHOD'] == 'POST')) {
            $this->model_sale_order->saveShippingInfo($this->request->get['order_id'], $this->request->post);

			$this->session->data['success'] = $this->language->get('text_success');
	  
			$url = $this->generatePagingURL();

			$this->redirect($this->url->link('supplier/order/update', 'token=' . $this->session->data['token'] . '&short_track_code=' . $this->request->get['short_track_code'] . $url, 'SSL'));
		}
		
    	$this->getForm();
  	}
	
  	public function reportSupplierError() {
		$this->load->language('sale/order');

		$this->document->setTitle($this->language->get('heading_title'));

		$this->load->model('sale/order');

        if (isset($this->request->get['order_id'])) {
            die("Invalid parameter: order_id");
        }

        if (isset($this->request->get['short_track_code'])) {
            $this->request->get['order_id'] = $this->model_sale_order->getOrderIdFromShortTrackCode($this->request->get['short_track_code']);
        }

        if (isset($this->request->get['order_id']) && $this->user->getSupplierId() && !$this->model_sale_order->hasPermissionForSupplier($this->request->get['order_id'], $this->user->getSupplierId())) {
            return $this->forward('error/permission');
        }

		if (($this->request->server['REQUEST_METHOD'] == 'POST')) {
            $this->model_sale_order->reportSupplierError($this->request->get['order_id'], $this->request->post);

			$this->session->data['success'] = $this->language->get('text_success');
	  
			$url = $this->generatePagingURL();

			$this->redirect($this->url->link('supplier/order/update', 'token=' . $this->session->data['token'] . '&short_track_code=' . $this->request->get['short_track_code'] . $url, 'SSL'));
		}
		
    	$this->getForm();
  	}

  	private function getList() {
		if (isset($this->request->get['filter_order_id'])) {
			$filter_order_id = $this->request->get['filter_order_id'];
		} else {
			$filter_order_id = null;
		}

		if (isset($this->request->get['filter_sender_name'])) {
			$filter_sender_name = $this->request->get['filter_sender_name'];
		} else {
			$filter_sender_name = null;
		}

		if (isset($this->request->get['filter_recipient_name'])) {
			$filter_recipient_name = $this->request->get['filter_recipient_name'];
		} else {
			$filter_recipient_name = null;
		}


		if (isset($this->request->get['filter_order_status_summary'])) {
			$filter_order_status_summary = $this->request->get['filter_order_status_summary'];
		} else {
			$filter_order_status_summary = null;
		}

		if (isset($this->request->get['filter_order_status'])) {
			$filter_order_status = $this->request->get['filter_order_status'];
		} else {
			$filter_order_status = null;
		}

		if (isset($this->request->get['filter_supplier_status'])) {
			$filter_supplier_status = $this->request->get['filter_supplier_status'];
		} else {
			$filter_supplier_status = null;
		}

		if (isset($this->request->get['filter_supplier_error'])) {
			$filter_supplier_error = $this->request->get['filter_supplier_error'];
		} else {
			$filter_supplier_error = null;
		}
		
		if (isset($this->request->get['filter_price'])) {
			$filter_price = $this->request->get['filter_price'];
		} else {
			$filter_price = null;
		}
		
		if (isset($this->request->get['filter_date_added'])) {
			$filter_date_added = $this->request->get['filter_date_added'];
		} else {
			$filter_date_added = null;
		}
		
		if (isset($this->request->get['filter_date_modified'])) {
			$filter_date_modified = $this->request->get['filter_date_modified'];
		} else {
			$filter_date_modified = null;
		}

		if (isset($this->request->get['sort'])) {
			$sort = $this->request->get['sort'];
		} else {
			$sort = 'o.order_id';
		}

		if (isset($this->request->get['order'])) {
			$order = $this->request->get['order'];
		} else {
			$order = 'DESC';
		}
		
		if (isset($this->request->get['page'])) {
			$page = $this->request->get['page'];
		} else {
			$page = 1;
		}
				
		$url = $this->generatePagingURL();

  		$this->data['breadcrumbs'] = array();

   		$this->data['breadcrumbs'][] = array(
       		'text'      => $this->language->get('text_home'),
			'href'      => $this->url->link('common/home', 'token=' . $this->session->data['token'], 'SSL'),
      		'separator' => false
   		);

   		$this->data['breadcrumbs'][] = array(
       		'text'      => $this->language->get('heading_title'),
			'href'      => $this->url->link('supplier/order', 'token=' . $this->session->data['token'] . $url, 'SSL'),
      		'separator' => ' :: '
   		);

		$this->data['orders'] = array();

		$data = array(
			'filter_order_id'        => $filter_order_id,
			'filter_sender_name'	     => $filter_sender_name,
			'filter_recipient_name'	     => $filter_recipient_name,
			//'filter_order_status_id' => $filter_order_status_id,
			'filter_order_status_summary' => $filter_order_status_summary,
			'filter_order_status' => $filter_order_status,
			'filter_price'           => $filter_price,
            'filter_supplier_status' => $filter_supplier_status,
            'filter_supplier_error' => $filter_supplier_error,
			'filter_date_added'      => $filter_date_added,
			'filter_date_modified'   => $filter_date_modified,
			'sort'                   => $sort,
			'order'                  => $order,
			'start'                  => ($page - 1) * $this->config->get('config_admin_limit'),
			'limit'                  => $this->config->get('config_admin_limit'),
            'supplier_id'            => $this->user->getSupplierId()
            
		);

		$order_total = $this->model_sale_order->getTotalOrders($data);

		$results = $this->model_sale_order->getOrders($data);

    	foreach ($results as $result) {
			$action = array();
            /*		
			$action[] = array(
				'text' => $this->language->get('text_view'),
				'href' => $this->url->link('sale/order/info', 'token=' . $this->session->data['token'] . '&order_id=' . $result['order_id'] . $url, 'SSL')
			);
			*/

			if (strtotime($result['date_added']) > strtotime('-' . (int)$this->config->get('config_order_edit') . ' day')) {
				$action[] = array(
					'text' => $this->language->get('text_edit'),
					'href' => $this->url->link('supplier/order/update', 'token=' . $this->session->data['token'] . '&short_track_code=' . $result['short_track_code'] . $url, 'SSL')
				);
			}
            
            $status_summary = $result['status_summary'];
            $order_statuses = $this->model_sale_order->getOrderStatuses($result['order_id']);
            $supplier_status = $this->model_sale_order->getSupplierOrderStatus($result, $order_statuses);

			$this->data['orders'][] = array(
                'short_track_code' => $result['short_track_code'],
				'order_id'      => $result['order_id'],
				'sender_name'      => $result['sender_name'],
				'recipient_name'      => $result['recipient_name'],
				'status'        => $status_summary,
				'price'         => $result['price'], //$this->currency->format($result['price'], $result['currency_code'], $result['currency_value']),
                'shipping_cost' => $result['shipping_cost'],
                'order_statuses'=> $order_statuses,
                'last_start_status' => $this->model_sale_order->getLastModifiedStatus($result['order_id']),
				'date_added'    => date($this->language->get('date_format_short'), strtotime($result['date_added'])),
				'date_modified' => date($this->language->get('date_format_short'), strtotime($result['date_modified'])),
				'selected'      => isset($this->request->post['selected']) && in_array($result['order_id'], $this->request->post['selected']),
                'supplier_status' => $supplier_status,
                'supplier_error' => $result['supplier_error'],
				'action'        => $action
			);

		}

                
		$this->data['heading_title'] = $this->language->get('heading_title');

		$this->data['text_no_results'] = $this->language->get('text_no_results');
		$this->data['text_missing'] = $this->language->get('text_missing');

		$this->data['column_order_id'] = $this->language->get('column_order_id');
    	$this->data['column_sender_name'] = $this->language->get('column_sender_name');
		$this->data['column_status'] = $this->language->get('column_status');
		$this->data['column_total'] = $this->language->get('column_total');
		$this->data['column_date_added'] = $this->language->get('column_date_added');
		$this->data['column_date_modified'] = $this->language->get('column_date_modified');
		$this->data['column_action'] = $this->language->get('column_action');

		$this->data['button_invoice'] = $this->language->get('button_invoice');
		$this->data['button_insert'] = $this->language->get('button_insert');
		$this->data['button_delete'] = $this->language->get('button_delete');
		$this->data['button_filter'] = $this->language->get('button_filter');
        
        //$this->data['text_supplier_status'] = array(''=>'', 'waiting'=>'待处理', 'complete'=>'已完成');

		$this->data['token'] = $this->session->data['token'];
		
		if (isset($this->error['warning'])) {
			$this->data['error_warning'] = $this->error['warning'];
		} else {
			$this->data['error_warning'] = '';
		}

		if (isset($this->session->data['success'])) {
			$this->data['success'] = $this->session->data['success'];

			unset($this->session->data['success']);
		} else {
			$this->data['success'] = '';
		}

        $url = $this->generateURL();

		if ($order == 'ASC') {
			$url .= '&order=DESC';
		} else {
			$url .= '&order=ASC';
		}

		if (isset($this->request->get['page'])) {
			$url .= '&page=' . $this->request->get['page'];
		}

		$this->data['sort_order'] = $this->url->link('supplier/order', 'token=' . $this->session->data['token'] . '&sort=o.order_id' . $url, 'SSL');
		$this->data['sort_sender_name'] = $this->url->link('supplier/order', 'token=' . $this->session->data['token'] . '&sort=sender_name' . $url, 'SSL');
		$this->data['sort_recipient_name'] = $this->url->link('supplier/order', 'token=' . $this->session->data['token'] . '&sort=recipient_name' . $url, 'SSL');
		$this->data['sort_status_summary'] = $this->url->link('supplier/order', 'token=' . $this->session->data['token'] . '&sort=status_summary' . $url, 'SSL');
		$this->data['sort_status'] = $this->url->link('supplier/order', 'token=' . $this->session->data['token'] . '&sort=status' . $url, 'SSL');
		$this->data['sort_total'] = $this->url->link('supplier/order', 'token=' . $this->session->data['token'] . '&sort=o.total' . $url, 'SSL');
		$this->data['sort_date_added'] = $this->url->link('supplier/order', 'token=' . $this->session->data['token'] . '&sort=o.date_added' . $url, 'SSL');
		$this->data['sort_date_modified'] = $this->url->link('supplier/order', 'token=' . $this->session->data['token'] . '&sort=o.date_modified' . $url, 'SSL');

		$url = $this->generateURL();

		if (isset($this->request->get['sort'])) {
			$url .= '&sort=' . $this->request->get['sort'];
		}

		if (isset($this->request->get['order'])) {
			$url .= '&order=' . $this->request->get['order'];
		}

		$pagination = new Pagination();
		$pagination->total = $order_total;
		$pagination->page = $page;
		$pagination->limit = $this->config->get('config_admin_limit');
		$pagination->text = $this->language->get('text_pagination');
		$pagination->url = $this->url->link('supplier/order', 'token=' . $this->session->data['token'] . $url . '&page={page}', 'SSL');

		$this->data['pagination'] = $pagination->render();

		$this->data['filter_order_id'] = $filter_order_id;
		$this->data['filter_sender_name'] = $filter_sender_name;
		$this->data['filter_recipient_name'] = $filter_recipient_name;
		//$this->data['filter_order_status_id'] = $filter_order_status_id;
		$this->data['filter_order_status_summary'] = $filter_order_status_summary;
		$this->data['filter_order_status'] = $filter_order_status;
        $this->data['filter_supplier_status'] = $filter_supplier_status;
        $this->data['filter_supplier_error'] = $filter_supplier_error;

		$this->data['filter_price'] = $filter_price;
		$this->data['filter_date_added'] = $filter_date_added;
		$this->data['filter_date_modified'] = $filter_date_modified;

		//$this->load->model('localisation/order_status');

    	//$this->data['order_statuses'] = $this->model_localisation_order_status->getOrderStatuses();

        $this->data['all_status_summary'] = $this->model_sale_order->getAllEnum('order', 'status_summary');
        $this->data['all_status_name'] = $this->model_sale_order->getAllEnum('order_status', 'status');

        $this->data['all_complete_reason'] = $this->model_sale_order->getAllEnum('order_status', 'complete_reason');

        $this->data['all_supplier_status'] = $this->model_sale_order->getAllSupplierStatus();

		$this->data['sort'] = $sort;
		$this->data['order'] = $order;

		$this->template = 'supplier/order_list.tpl';
		$this->children = array(
			'common/header',
			'common/footer'
		);
		
		$this->response->setOutput($this->render());
  	}

  	private function getForm() {
		$this->load->model('sale/customer');
        
		$this->data['heading_title'] = $this->language->get('heading_title');
		 
		$this->data['text_no_results'] = $this->language->get('text_no_results');  
		$this->data['text_default'] = $this->language->get('text_default');
		$this->data['text_select'] = $this->language->get('text_select');
		$this->data['text_none'] = $this->language->get('text_none');
		$this->data['text_wait'] = $this->language->get('text_wait');
		$this->data['text_product'] = $this->language->get('text_product');
		$this->data['text_voucher'] = $this->language->get('text_voucher');
		$this->data['text_order'] = $this->language->get('text_order');
		
		$this->data['entry_store'] = $this->language->get('entry_store');
		$this->data['entry_customer'] = $this->language->get('entry_customer');
		$this->data['entry_customer_group'] = $this->language->get('entry_customer_group');
		$this->data['entry_firstname'] = $this->language->get('entry_firstname');
		$this->data['entry_lastname'] = $this->language->get('entry_lastname');
		$this->data['entry_email'] = $this->language->get('entry_email');
		$this->data['entry_telephone'] = $this->language->get('entry_telephone');
		$this->data['entry_fax'] = $this->language->get('entry_fax');
		$this->data['entry_order_status'] = $this->language->get('entry_order_status');
		$this->data['entry_comment'] = $this->language->get('entry_comment');	
		$this->data['entry_affiliate'] = $this->language->get('entry_affiliate');
		$this->data['entry_address'] = $this->language->get('entry_address');
		$this->data['entry_company'] = $this->language->get('entry_company');
		$this->data['entry_address_1'] = $this->language->get('entry_address_1');
		$this->data['entry_address_2'] = $this->language->get('entry_address_2');
		$this->data['entry_city'] = $this->language->get('entry_city');
		$this->data['entry_postcode'] = $this->language->get('entry_postcode');
		$this->data['entry_zone'] = $this->language->get('entry_zone');
		$this->data['entry_zone_code'] = $this->language->get('entry_zone_code');
		$this->data['entry_country'] = $this->language->get('entry_country');		
		$this->data['entry_product'] = $this->language->get('entry_product');
		$this->data['entry_option'] = $this->language->get('entry_option');
		$this->data['entry_quantity'] = $this->language->get('entry_quantity');
		$this->data['entry_to_name'] = $this->language->get('entry_to_name');
		$this->data['entry_to_email'] = $this->language->get('entry_to_email');
		$this->data['entry_from_name'] = $this->language->get('entry_from_name');
		$this->data['entry_from_email'] = $this->language->get('entry_from_email');
		$this->data['entry_theme'] = $this->language->get('entry_theme');	
		$this->data['entry_message'] = $this->language->get('entry_message');
		$this->data['entry_amount'] = $this->language->get('entry_amount');
		$this->data['entry_shipping'] = $this->language->get('entry_shipping');
		$this->data['entry_payment'] = $this->language->get('entry_payment');
		$this->data['entry_voucher'] = $this->language->get('entry_voucher');
		$this->data['entry_coupon'] = $this->language->get('entry_coupon');
		$this->data['entry_reward'] = $this->language->get('entry_reward');

		$this->data['column_product'] = $this->language->get('column_product');
		$this->data['column_model'] = $this->language->get('column_model');
		$this->data['column_quantity'] = $this->language->get('column_quantity');
		$this->data['column_price'] = $this->language->get('column_price');
		$this->data['column_total'] = $this->language->get('column_total');
			
		$this->data['button_save'] = $this->language->get('button_save');
		$this->data['button_cancel'] = $this->language->get('button_cancel');
		$this->data['button_add_product'] = $this->language->get('button_add_product');
		$this->data['button_add_voucher'] = $this->language->get('button_add_voucher');
		$this->data['button_update_total'] = $this->language->get('button_update_total');
		$this->data['button_remove'] = $this->language->get('button_remove');
		$this->data['button_upload'] = $this->language->get('button_upload');

		$this->data['tab_order'] = $this->language->get('tab_order');
		$this->data['tab_sender'] = $this->language->get('tab_sender');
		$this->data['tab_recipient'] = $this->language->get('tab_recipient');
		$this->data['tab_payment'] = $this->language->get('tab_payment');
		$this->data['tab_shipping'] = $this->language->get('tab_shipping');
		$this->data['tab_product'] = $this->language->get('tab_product');
		$this->data['tab_voucher'] = $this->language->get('tab_voucher');
		$this->data['tab_total'] = $this->language->get('tab_total');

		$this->data['token'] = $this->session->data['token'];
		
		if (isset($this->request->get['order_id'])) {
			$this->data['order_id'] = $this->request->get['order_id'];
		} else {
			$this->data['order_id'] = 0;
		}

 		if (isset($this->error['warning'])) {
			$this->data['error_warning'] = $this->error['warning'];
		} else {
			$this->data['error_warning'] = '';
		}
		
 		if (isset($this->error['firstname'])) {
			$this->data['error_firstname'] = $this->error['firstname'];
		} else {
			$this->data['error_firstname'] = '';
		}

 		if (isset($this->error['lastname'])) {
			$this->data['error_lastname'] = $this->error['lastname'];
		} else {
			$this->data['error_lastname'] = '';
		}
		
 		if (isset($this->error['email'])) {
			$this->data['error_email'] = $this->error['email'];
		} else {
			$this->data['error_email'] = '';
		}
		
 		if (isset($this->error['telephone'])) {
			$this->data['error_telephone'] = $this->error['telephone'];
		} else {
			$this->data['error_telephone'] = '';
		}

 		if (isset($this->error['shipping_firstname'])) {
			$this->data['error_shipping_firstname'] = $this->error['shipping_firstname'];
		} else {
			$this->data['error_shipping_firstname'] = '';
		}

 		if (isset($this->error['shipping_lastname'])) {
			$this->data['error_shipping_lastname'] = $this->error['shipping_lastname'];
		} else {
			$this->data['error_shipping_lastname'] = '';
		}
				
		if (isset($this->error['shipping_address_1'])) {
			$this->data['error_shipping_address_1'] = $this->error['shipping_address_1'];
		} else {
			$this->data['error_shipping_address_1'] = '';
		}
		
		if (isset($this->error['shipping_city'])) {
			$this->data['error_shipping_city'] = $this->error['shipping_city'];
		} else {
			$this->data['error_shipping_city'] = '';
		}
		
		if (isset($this->error['shipping_postcode'])) {
			$this->data['error_shipping_postcode'] = $this->error['shipping_postcode'];
		} else {
			$this->data['error_shipping_postcode'] = '';
		}
		
		if (isset($this->error['shipping_country'])) {
			$this->data['error_shipping_country'] = $this->error['shipping_country'];
		} else {
			$this->data['error_shipping_country'] = '';
		}
		
		if (isset($this->error['shipping_zone'])) {
			$this->data['error_shipping_zone'] = $this->error['shipping_zone'];
		} else {
			$this->data['error_shipping_zone'] = '';
		}
				
 		if (isset($this->error['payment_firstname'])) {
			$this->data['error_payment_firstname'] = $this->error['payment_firstname'];
		} else {
			$this->data['error_payment_firstname'] = '';
		}

 		if (isset($this->error['payment_lastname'])) {
			$this->data['error_payment_lastname'] = $this->error['payment_lastname'];
		} else {
			$this->data['error_payment_lastname'] = '';
		}
				
		if (isset($this->error['payment_address_1'])) {
			$this->data['error_payment_address_1'] = $this->error['payment_address_1'];
		} else {
			$this->data['error_payment_address_1'] = '';
		}
		
		if (isset($this->error['payment_city'])) {
			$this->data['error_payment_city'] = $this->error['payment_city'];
		} else {
			$this->data['error_payment_city'] = '';
		}
		
		if (isset($this->error['payment_postcode'])) {
			$this->data['error_payment_postcode'] = $this->error['payment_postcode'];
		} else {
			$this->data['error_payment_postcode'] = '';
		}
		
		if (isset($this->error['payment_country'])) {
			$this->data['error_payment_country'] = $this->error['payment_country'];
		} else {
			$this->data['error_payment_country'] = '';
		}
		
		if (isset($this->error['payment_zone'])) {
			$this->data['error_payment_zone'] = $this->error['payment_zone'];
		} else {
			$this->data['error_payment_zone'] = '';
		}
				
		$url = $this->generatePagingURL();

		$this->data['breadcrumbs'] = array();

		$this->data['breadcrumbs'][] = array(
			'text'      => $this->language->get('text_home'),
			'href'      => $this->url->link('common/home', 'token=' . $this->session->data['token'], 'SSL'),
			'separator' => false
		);

		$this->data['breadcrumbs'][] = array(
			'text'      => $this->language->get('heading_title'),
			'href'      => $this->url->link('supplier/order', 'token=' . $this->session->data['token'] . $url, 'SSL'),				
			'separator' => ' :: '
		);

		if (!isset($this->request->get['order_id'])) {
			$this->data['action'] = $this->url->link('supplier/order/insert', 'token=' . $this->session->data['token'] . $url, 'SSL');
		} else {
			$this->data['action'] = $this->url->link('supplier/order/update', 'token=' . $this->session->data['token'] . '&short_track_code=' . $this->request->get['short_track_code'] . $url, 'SSL');
		}
		
		$this->data['cancel'] = $this->url->link('supplier/order', 'token=' . $this->session->data['token'] . $url, 'SSL');

    	if (isset($this->request->get['order_id']) && ($this->request->server['REQUEST_METHOD'] != 'POST')) {
      		$order_info = $this->model_sale_order->getOrder($this->request->get['order_id']);
    	}


        // product
        if (isset($this->request->post['product_id'])) {
            $this->data['product_id'] = $this->request->post['product_id'];
        } elseif (!empty ($order_info)) {
            $this->data['product_id'] = $order_info['product_id'];
            $this->data['product_edit_link'] = htmlspecialchars_decode($this->url->link('supplier/product/update', 'token=' . $this->session->data['token'] . '&product_id=' . $order_info['product_id'], 'SSL'));

        } else {
            $this->data['product_id'] = '';
        }

        if (isset($this->request->post['product_group_id'])) {
            $this->data['product_group_id'] = $this->request->post['product_group_id'];
        } elseif (!empty ($order_info)) {
            $this->data['product_group_id'] = $order_info['product_group_id'];
        } else {
            $this->data['product_group_id'] = '';
        }


        if (isset($this->request->post['flavor_product_id'])) {
            $this->data['flavor_product_id'] = $this->request->post['flavor_product_id'];
        } elseif (!empty ($order_info)) {
            $this->data['flavor_product_id'] = $order_info['flavor_product_id'];
            $this->data['flavor_product_edit_link'] = htmlspecialchars_decode($this->url->link('catalog/product/update', 'token=' . $this->session->data['token'] . '&product_id=' . $order_info['flavor_product_id'], 'SSL'));

        } else {
            $this->data['flavor_product_id'] = '';
        }

        if (isset($this->request->post['flavor_product_group_id'])) {
            $this->data['flavor_product_group_id'] = $this->request->post['flavor_product_group_id'];
        } elseif (!empty ($order_info)) {
            $this->data['flavor_product_group_id'] = $order_info['flavor_product_group_id'];
        } else {
            $this->data['flavor_product_group_id'] = '';
        }



        // fill sender
						
		if (isset($this->request->post['sender_id'])) {
			$this->data['sender_id'] = $this->request->post['sender_id'];
		} elseif (!empty($order_info)) {
			$this->data['sender_id'] = $order_info['sender_id'];
		} else {
			$this->data['sender_id'] = '';
		}
				
    	if (isset($this->request->post['sender_name'])) {
      		$this->data['sender_name'] = $this->request->post['sender_name'];
		} elseif (!empty($order_info)) { 
			$this->data['sender_name'] = $order_info['sender_name'];
		} else {
      		$this->data['sender_name'] = '';
    	}


        // recipient
        $this->load->model('user/user');

        if (isset($this->request->post['recipient_id'])) {
            $recipient_id = $this->request->post['recipient_id'];
            $this->data['recipient_id'] = $recipient_id;
        } elseif ( !empty($order_info) ) {
            $recipient_id = $order_info['recipient_id'];
            $this->data['recipient_id'] = $recipient_id;
        } else {
            $this->data['recipient_id'] = '';
        }

        if (isset($this->request->post['recipient_profile_id'])) {
            $this->data['recipient_profile_id'] = $this->request->post['recipient_profile_id'];
        } elseif (!empty ($order_info)) {
            $this->data['recipient_profile_id'] = $order_info['recipient_profile_id'];
        } else {
            $this->data['recipient_profile_id'] = '';
        }

        if (isset($this->request->post['recipient_profile_network'])) {
            $this->data['recipient_profile_network'] = $this->request->post['recipient_profile_network'];
        } elseif (!empty ($order_info)) {
            $this->data['recipient_profile_network'] = $order_info['recipient_profile_network'];
        } else {
            $this->data['recipient_profile_network'] = '';
        }

        if (isset($this->request->post['recipient_name'])) {
            $this->data['recipient_name'] = $this->request->post['recipient_name'];
        } elseif (!empty ($order_info)) {
            $this->data['recipient_name'] = $order_info['recipient_name'];
        } else {
            $this->data['recipient_name'] = '';
        }

        // recipient address
        if (isset($this->request->post['recipient_address_id'])) {
            $this->data['recipient_address_id'] = $this->request->post['recipient_address_id'];
        } elseif (!empty ($order_info)) {
            $this->data['recipient_address_id'] = $order_info['recipient_address_id'];
        } else {
            $this->data['recipient_address_id'] = '';
        }

        if ($this->data['recipient_id'] != "") {
            $this->data["addresses"] = $this->model_sale_customer->getAddresses($this->data['recipient_id']);
        }

        if ($this->data['recipient_address_id'] != '') {
            $address = $this->model_sale_customer->getAddress($this->data['recipient_address_id']);
        }
        

        if (isset($this->request->post['recipient_address'])) {
            $this->data['recipient_address'] = $this->request->post['recipient_address'];
        } elseif (!empty($order_info)) {
            $this->data['recipient_address'] = $this->model_sale_customer->getAddress($this->data['recipient_address_id']);
        } else {
            $this->data['recipient_address'] = array();
        }

        // recipient info
        if (isset($this->request->post['recipient_notifcation_date'])) {
            $this->data['recipient_notification_date'] = $this->request->post['recipient_notifcation_date'];
        } elseif (!empty ($order_info)) {
            $this->data['recipient_notification_date'] = $order_info['recipient_notification_date'];
        } else {
            $this->data['recipient_notification_date'] = '';
        }
        
        if (isset($this->request->post['recipient_shipping_date'])) {
            $this->data['recipient_shipping_date'] = $this->request->post['recipient_shipping_date'];
        } elseif (!empty ($order_info)) {
            $this->data['recipient_shipping_date'] = $order_info['recipient_shipping_date'];
        } else {
            $this->data['recipient_shipping_date'] = '';
        }

        // payment info
        if (isset($this->request->post['payment_method'])) {
            $this->data['payment_method'] = $this->request->post['payment_method'];
        } elseif (!empty($order_info)) {
            $this->data['payment_method'] = $order_info['payment_method'];
        } else {
            $this->data['payment_method'] = "";
        }

        if (isset($this->request->post['payment_info'])) {
            $this->data['payment_info'] = $this->request->post['payment_info'];
        } elseif (!empty($order_info)) {
            $this->data['payment_info'] = $order_info['payment_info'];
        } else {
            $this->data['payment_info'] = "";
        }

        // card
        if (isset($this->request->post['card_id'])) {
            $this->data['card_id'] = $this->request->post['card_id'];
        } elseif (!empty($order_info)) {
            $this->data['card_id'] = $order_info['card_id'];
        } else {
            $this->data['card_id'] = "";
        }

        if (isset($this->request->post['order_card_content'])) {
            $this->data['order_card_content'] = $this->request->post['order_card_content'];
        } elseif (!empty($order_info)) {
            $this->data['order_card_content'] = $this->model_sale_order->getCardContent($order_info['order_id']);
        } else {
            $this->data['order_card_content'] = array();
        }
        
        // price
        if (isset($this->request->post['price'])) {
            $this->data['price'] = $this->request->post['price'];
        } elseif (!empty($order_info)) {
            $this->data['price'] = $order_info['price'];
        } else {
            $this->data['price'] = "";
        }

        // status
        if (isset($this->request->post['status_summary'])) {
            $this->data['status_summary'] = $this->request->post['status_summary'];
        } elseif (!empty($order_info)) {
            $this->data['status_summary'] = $order_info['status_summary'];
        } else {
            $this->data['status_summary'] = 'place_order';
        }
        
        $this->data['all_status_summary'] = $this->model_sale_order->getAllEnum('order', 'status_summary');
        $this->data['all_supplier_status'] = $this->model_sale_order->getAllSupplierStatus();
        $this->data['track_code'] = $order_info['track_code'];
        $this->data['short_track_code'] = $order_info['short_track_code'];
        $this->data['pay_track_code'] = $order_info['pay_track_code'];
        $this->data['supplier_error'] = $order_info['supplier_error'];

        // status table
        if (!empty($order_info)) {
            $order_statuses = $this->model_sale_order->getOrderStatuses($order_info['order_id']);
            $this->data['order_statuses'] = $order_statuses;

            $this->data['supplier_status'] = $this->model_sale_order->getSupplierOrderStatus($order_info, $order_statuses);

            $this->data['show_cancel_order'] = true;
            $this->data['show_confirm_inventory'] = false;
            $this->data['show_cancel_purchase'] = false;
            $this->data['show_confirm_receive_gift'] = false;
            $this->data['show_save_shipping_info'] = false;

            $this->data['confirm_inventory_action'] = htmlspecialchars_decode($this->url->link('supplier/order/updateOrderStatus', 'token=' . $this->session->data['token'] . '&short_track_code=' . $this->request->get['short_track_code'] . '&action=confirm_inventory' . $url, 'SSL'));
            $this->data['cancel_purchase_action'] = htmlspecialchars_decode($this->url->link('supplier/order/updateOrderStatus', 'token=' . $this->session->data['token'] . '&short_track_code=' . $this->request->get['short_track_code'] . '&action=cancel_purchase' . $url, 'SSL'));
            $this->data['cancel_order_action'] = htmlspecialchars_decode($this->url->link('supplier/order/updateOrderStatus', 'token=' . $this->session->data['token'] . '&short_track_code=' . $this->request->get['short_track_code'] . '&action=cancel_order' . $url, 'SSL'));
            $this->data['confirm_receive_gift_action'] = htmlspecialchars_decode($this->url->link('supplier/order/updateOrderStatus', 'token=' . $this->session->data['token'] . '&short_track_code=' . $this->request->get['short_track_code'] . '&action=confirm_receive_gift' . $url, 'SSL'));
            //error_log("aniluke, action=" . $this->data['action']);

            if ($order_info['status_summary'] != 'start') {
                $this->data['show_cancel_order'] = false;
            }

            foreach ($this->data['order_statuses'] as $order_status) {
                if ($order_info['status_summary'] == 'start' && 
                    $order_status['status'] == 'place_purchase' &&
                    !empty($order_status['start_time']) &&
                    (empty($order_status['complete_time']) || empty($order_status['complete_reason']))) {
                    $this->data['show_confirm_inventory'] = true;
                    $this->data['show_save_shipping_info'] = true;
                    $this->data['show_cancel_purchase'] = true;

                }

                if ($order_info['status_summary'] == 'start' && 
                    $order_status['status'] == 'order_notification' &&
                    !empty($order_status['start_time']) &&
                    (empty($order_status['complete_time']) || empty($order_status['complete_reason']))) {
                    $this->data['show_confirm_receive_gift'] = true;
                }
                
                if ($order_status['status'] == 'close_order' && 
                    !empty($order_status['complete_time']) &&
                    !empty($order_status['complete_reason'])) {
                    $this->data['show_cancel_order'] = false;
                }
            }
        }

        $this->data['all_status_name'] = $this->model_sale_order->getAllEnum('order_status', 'status');

        $this->data['all_complete_reason'] = $this->model_sale_order->getAllEnum('order_status', 'complete_reason');

        // notifications
        if (!empty($order_info)) {
            $this->data['recipient_notifications'] = $this->model_sale_order->getRecipientNotifications($order_info['order_id']);
        }

        if (!empty($order_info)) {
            $this->data['payment_notifications'] = $this->model_sale_order->getPaymentNotifications($order_info['order_id']);
        }

        // shipping
        $this->data['all_shipping_method'] = $this->model_sale_order->getAllEnum('order', 'shipping_method');
        
        if (!empty($order_info)) {
            $this->data['shipping_method'] = $order_info['shipping_method'];
            $this->data['shipping_code'] = $order_info['shipping_code'];
            $this->data['shipping_cost'] = $order_info['shipping_cost'];
            
            $this->data['shipping_history'] = $this->model_sale_order->getShippingHistory($order_info['order_id']);
        }

        $this->data['save_shipping_info_action'] = htmlspecialchars_decode($this->url->link('supplier/order/saveShippingInfo', 'token=' . $this->session->data['token'] . '&short_track_code=' . $this->request->get['short_track_code'] . $url, 'SSL'));

        $this->data['enable_save_shipping'] = true;

        $this->data['report_supplier_error_action'] = htmlspecialchars_decode($this->url->link('supplier/order/reportSupplierError', 'token=' . $this->session->data['token'] . '&short_track_code=' . $this->request->get['short_track_code'] . $url, 'SSL'));

        foreach( $this->data['order_statuses'] as $order_status) {
            if ($this->data['status_summary'] != 'start' || (
                $order_status['status'] == 'place_purchase' && 
                $order_status['complete_reason'] != null )) {

                $this->data['enable_save_shipping'] = false;
                break;
            }
        }

            
        // thanks note
        if (!empty($order_info)) {
            $this->data['thanks_note'] = $order_info['thanks_note'];
        }

		$this->template = 'supplier/order_form.tpl';
		$this->children = array(
                            'common/header',
                            'common/footer'
		);
		
		$this->response->setOutput($this->render());
  	}
	
  	private function validateForm() {
    	if (!$this->user->hasPermission('modify', 'supplier/order')) {
      		$this->error['warning'] = $this->language->get('error_permission');
    	}
		
		if ($this->error && !isset($this->error['warning'])) {
			$this->error['warning'] = $this->language->get('error_warning');
		}

		if (!$this->error) {
	  		return true;
		} else {
	  		return false;
		}
  	}    
	
   	private function validateDelete() {
    	if (!$this->user->hasPermission('modify', 'supplier/order')) {
			$this->error['warning'] = $this->language->get('error_permission');
    	}

		if (!$this->error) {
	  		return true;
		} else {
	  		return false;
		}
  	}
	
}
?>