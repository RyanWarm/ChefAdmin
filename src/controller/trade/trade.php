<?php 
class ControllerTradeTrade extends Controller { 
	private $error = array();
 
	public function index() {
		$this->load->language('trade/trade');

		$this->document->setTitle($this->language->get('heading_title'));
		
		$this->load->model('trade/trade');
		 
		$this->getList();
	}

	public function insert() {
		$this->load->language('trade/trade');

		$this->document->setTitle($this->language->get('heading_title'));
		
		$this->load->model('trade/trade');
		
		if (($this->request->server['REQUEST_METHOD'] == 'POST') && $this->validateForm()) {
			$this->model_trade_trade->add($this->request->post);

			$this->session->data['success'] = $this->language->get('text_success');
			
			$this->redirect($this->url->link('trade/trade', 'token=' . $this->session->data['token'], 'SSL')); 
		}

		$this->getForm();
	}

	public function update() {
		$this->load->language('trade/trade');

		$this->document->setTitle($this->language->get('heading_title'));
		
		$this->load->model('trade/trade');
		
		if (($this->request->server['REQUEST_METHOD'] == 'POST') && $this->validateForm()) {
			$this->model_trade_trade->edit($this->request->get['id'], $this->request->get['order_num'], $this->request->post);
			
			$this->session->data['success'] = $this->language->get('text_success');
			
            		$url_params = $this->generatePagingURLParams();
			$this->redirect($this->url->link('trade/trade', http_build_query($url_params), 'SSL'));
		}

		$this->getForm(1);
	}

	public function delete() {
		$this->load->language('trade/trade');

		$this->document->setTitle($this->language->get('heading_title'));
		
		$this->load->model('trade/trade');
		
		if (isset($this->request->post['selected']) && $this->validateDelete()) {
			foreach ($this->request->post['selected'] as $trade_id) {
				$this->model_trade_trade($trade_id);
			}

			$this->session->data['success'] = $this->language->get('text_success');

			$this->redirect($this->url->link('trade/trade', 'token=' . $this->session->data['token'], 'SSL'));
		}

		$this->getList();
	}

	private function generateURL() {
		$params = array();
        
		if (isset($this->request->get['filter_id'])) {
			$params['filter_id'] = $this->request->get['filter_id'];
		}

		if (isset($this->request->get['filter_youzan_id'])) {
			$params['filter_youzan_id'] = $this->request->get['filter_youzan_id'];
		}

		if (isset($this->request->get['filter_pay_type'])) {
			$params['filter_pay_type'] = $this->request->get['filter_pay_type'];
		}

		if (isset($this->request->get['filter_deliver_time'])) {
			$params['filter_deliver_time'] = $this->request->get['filter_deliver_time'];
		}

		if (isset($this->request->get['filter_message'])) {
			$params['filter_message'] = $this->request->get['filter_message'];
		}

		if (isset($this->request->get['filter_address'])) {
			$params['filter_address'] = $this->request->get['filter_address'];
		}

		if (isset($this->request->get['sort'])) {
			$params['sort'] = $this->request->get['sort'];
		}

        	return $params;
    	}

	private function generatePagingURLParams() {
        	$params = $this->generateURL();

		if (isset($this->request->get['page'])) {
			$params['page'] = $this->request->get['page'];
		}
	
        	return $params;
    	}

	private function getList() {
        // UI
		$this->data['heading_title'] = $this->language->get('heading_title');

		$this->data['text_no_results'] = $this->language->get('text_no_results');

		$this->data['column_name'] = $this->language->get('column_name');
		$this->data['column_sort_order'] = $this->language->get('column_sort_order');
		$this->data['column_action'] = $this->language->get('column_action');

		$this->data['button_insert'] = $this->language->get('button_insert');
		$this->data['button_delete'] = $this->language->get('button_delete');
 
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

		// prepare url and header 

		$this->config->set('config_admin_limit', 20);

		$filter_id = $this->request->get('filter_id');
		$this->data['filter_id'] = $filter_id;

		$filter_youzan_id = $this->request->get('filter_youzan_id');
		$this->data['filter_youzan_id'] = $filter_youzan_id;

		$filter_pay_type = $this->request->get('filter_pay_type');
		$this->data['filter_pay_type'] = $filter_pay_type;

		$filter_deliver_time = $this->request->get('filter_deliver_time');
		$this->data['filter_deliver_time'] = $filter_deliver_time;

		$filter_message = $this->request->get('filter_message');
		$this->data['filter_message'] = $filter_message;

		$filter_address = $this->request->get('filter_address');
		$this->data['filter_address'] = $filter_address;

		$sort = $this->request->get('sort');
			
		$order = $this->request->get('order');

		$page = $this->request->get('page', 1);

		$url_params = $this->generateURL();
			
		if (isset($this->request->get['page'])) {
		    $url_params['page'] = $this->request->get['page'];
		}

   		$this->data['breadcrumbs'] = array();

   		$this->data['breadcrumbs'][] = array(
       			'text'      => $this->language->get('text_home'),
			'href'      => $this->url->link('common/home', 'SSL'),
      			'separator' => false
   		);

   		$this->data['breadcrumbs'][] = array(
       			'text'      => $this->language->get('heading_title'),
			'href'      => $this->url->link('trade/trade', http_build_query($url_params), 'SSL'),
      			'separator' => ' :: '
   		);
									
		$this->data['insert'] = $this->url->link('trade/trade/insert', http_build_query($url_params), 'SSL');
		$this->data['delete'] = $this->url->link('trade/trade/delete', http_build_query($url_params), 'SSL');

        	// query data
		$query_params = array(
                              'filter_id' => $filter_id,
                              'filter_youzan_id' => $filter_youzan_id,
                              'filter_pay_type' => $filter_pay_type,
                              'filter_deliver_time' => $filter_deliver_time,
                              'filter_message' => $filter_message,
                              'filter_address' => $filter_address,
                              'sort' => $sort,
                              'order' => $order,
                              'start' => ($page-1) * $this->config->get('config_admin_limit'),
                              'limit' => $this->config->get('config_admin_limit')
                              );
       		
 		$results = $this->model_trade_trade->getList($query_params);

		$this->data['list'] = array();

		$this->load->model('tool/image');
		$this->load->model('customer/customer');
		$this->load->model('order/order');
 
		foreach ($results as $result) {
			$action = array();
			
			$action[] = array(
				'text' => $this->language->get('text_edit'),
				'href' => $this->url->link('trade/trade/update', http_build_query( array_merge( array('id' => $result['id'] ),  $url_params)), 'SSL')
			);

            		$user_info = $this->model_customer_customer->getItem($result['youzan_id']);
            		$order_info = $this->model_order_order->getListByTid($result['tid']);

            		$this->data['list'][] = array_merge(
                		$result, 
                		array('selected' => isset($this->request->post['selected']) && in_array($result['zid'], $this->request->post['selected']), 
                      			'action' => $action,
					'alias' => $user_info['alias'],
					'mobile' => $user_info['mobile'],
					'orders' => $order_info
				));
		}

		// render page list
		$page_url_params = $this->generateURL();

		$pagination = new Pagination();
		$pagination->total = $this->model_trade_trade->getTotalCount($query_params);
		$pagination->page = $page;
		$pagination->limit = $this->config->get('config_admin_limit');
		$pagination->text = $this->language->get('text_pagination');
		$pagination->url = $this->url->link('trade/trade', http_build_query($page_url_params) . "&page={page}", 'SSL');

		$this->data['pagination'] = $pagination->render();
		
		// output
		$this->template = 'trade/trade_list.tpl';
		$this->children = array(
			'common/header',
			'common/footer'
		);

		$this->response->setOutput($this->render());
	}

	private function getForm($fromInternal=0) {
		// UI text
		$this->data['heading_title'] = $this->language->get('heading_title');

		$this->data['text_none'] = $this->language->get('text_none');
		$this->data['text_default'] = $this->language->get('text_default');
		$this->data['text_image_manager'] = $this->language->get('text_image_manager');
		$this->data['text_browse'] = $this->language->get('text_browse');
		$this->data['text_clear'] = $this->language->get('text_clear');		
		$this->data['text_enabled'] = $this->language->get('text_enabled');
    		$this->data['text_disabled'] = $this->language->get('text_disabled');
		$this->data['text_percent'] = $this->language->get('text_percent');
		$this->data['text_amount'] = $this->language->get('text_amount');
				
		$this->data['entry_name'] = $this->language->get('entry_name');
		$this->data['entry_meta_keyword'] = $this->language->get('entry_meta_keyword');
		$this->data['entry_meta_description'] = $this->language->get('entry_meta_description');
		$this->data['entry_description'] = $this->language->get('entry_description');
		$this->data['entry_store'] = $this->language->get('entry_store');
		$this->data['entry_keyword'] = $this->language->get('entry_keyword');
		$this->data['entry_parent'] = $this->language->get('entry_parent');
		$this->data['entry_image'] = $this->language->get('entry_image');
		$this->data['entry_top'] = $this->language->get('entry_top');
		$this->data['entry_column'] = $this->language->get('entry_column');		
		$this->data['entry_sort_order'] = $this->language->get('entry_sort_order');
		$this->data['entry_status'] = $this->language->get('entry_status');
		$this->data['entry_layout'] = $this->language->get('entry_layout');
		
		$this->data['button_save'] = $this->language->get('button_save');
		$this->data['button_cancel'] = $this->language->get('button_cancel');

    		$this->data['tab_general'] = $this->language->get('tab_general');
    		$this->data['tab_data'] = $this->language->get('tab_data');
		$this->data['tab_design'] = $this->language->get('tab_design');
		
 		if (isset($this->error['warning'])) {
			$this->data['error_warning'] = $this->error['warning'];
		} else {
			$this->data['error_warning'] = '';
		}
	
 		if (isset($this->error['name'])) {
			$this->data['error_name'] = $this->error['name'];
		} else {
			$this->data['error_name'] = '';
		}

		$url_params = $this->generatePagingURLParams();
		
  		$this->data['breadcrumbs'] = array();

   		$this->data['breadcrumbs'][] = array(
       			'text'      => $this->language->get('text_home'),
			'href'      => $this->url->link('common/home', '', 'SSL'),
      			'separator' => false
   		);

   		$this->data['breadcrumbs'][] = array(
       			'text'      => $this->language->get('heading_title'),
			'href'      => $this->url->link('trade/trade', http_build_query($url_params), 'SSL'),
      			'separator' => ' :: '
   		);
		
		$this->data['cancel'] = $this->url->link('trade/trade', http_build_query($url_params), 'SSL');

		$this->data['token'] = $this->session->data['token'];

		/**
		$this->load->model('localisation/language');
		
		$this->data['languages'] = $this->model_localisation_language->getLanguages();
		**/
        	// data
        	if (isset($this->request->get['id']) && ($fromInternal == 1 || $this->request->server['REQUEST_METHOD'] != 'POST')) {
            		$item_info = $this->model_trade_trade->getItem($this->request->get['id']);
        	} else {
            		$item_info = null;
        	}

        	$this->data['id'] = $this->getWithDefault($item_info, 'id', null);
        	$this->data['youzan_id'] = $this->getWithDefault($item_info, 'youzan_id', '');        
        	$this->data['order_num'] = $this->getWithDefault($item_info, 'order_num', '');        
       		$this->data['pay_type'] = $this->getWithDefault($item_info, 'pay_type', '');
       		$this->data['post_fee'] = $this->getWithDefault($item_info, 'post_fee', '');
       		$this->data['payment'] = $this->getWithDefault($item_info, 'payment', '');
        	$this->data['discount'] = $this->getWithDefault($item_info, 'discount', '');
        	$this->data['total_fee'] = $this->getWithDefault($item_info, 'total_fee', '');
        	$this->data['message'] = $this->getWithDefault($item_info, 'message', '');
       		$this->data['status'] = $this->getWithDefault($item_info, 'status', '');
       		$this->data['consign_time'] = $this->getWithDefault($item_info, 'consign_time', '');
       		$this->data['deliver_time'] = $this->getWithDefault($item_info, 'deliver_time', '');

		if (!isset($this->request->get['id'])) {
			$this->data['action'] = $this->url->link('trade/trade/insert', null, 'SSL');
		} else {
			$this->data['action'] = $this->url->link('trade/trade/update', http_build_query(array_merge( array('id' =>  $this->request->get['id'], 'order_num' => $this->data['order_num'] ), $url_params)) , 'SSL');
		}

		$this->data['no_image'] = 'no_image.jpg';
        	$image_url = $this->getWithDefault($item_info, 'image', '');
		if ( strpos($image_url, 'upload') !== false ){
			//upload directory
        		$this->data['image'] = $image_url;
		}else{
			//recruit directory
			if( empty($image_url) ){
				$this->data['image'] = $this->data['no_image'];
			}else{
				$this->data['image'] = 'recruit/' . $image_url;
			}
		}

		$this->template = 'trade/trade_form.tpl';
		$this->children = array(
			'common/header',
			'common/footer'
		);
				
		$this->response->setOutput($this->render());
	}

	private function validateForm() {
		/**
		if (!$this->user->hasPermission('modify', 'trade/trade')) {
			$this->error['warning'] = $this->language->get('error_permission');
		}

		$name = $this->request->post['name'];

		if ((utf8_strlen($name) < 1) || (utf8_strlen($name) > 255)) {
		    $this->error['name'] = $this->language->get('error_name');
		}
		**/
		$dTime = $this->request->post['deliver_time'];
		if ( !empty($dTime) && !preg_match("/(\d{4})-(\d{2})-(\d{2}) (\d{2}):(\d{2}):(\d{2})/",$dTime) ) {
		    $this->error['warning'] = '错误的时间格式，实例：2016-06-28 08:00:00';
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
		if (!$this->user->hasPermission('modify', 'catalog/gift_image')) {
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
