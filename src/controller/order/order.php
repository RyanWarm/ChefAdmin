<?php 
class ControllerOrderOrder extends Controller { 
	private $error = array();
 
	public function index() {
		$this->load->language('order/order');

		$this->document->setTitle($this->language->get('heading_title'));
		
		$this->load->model('order/order');
		 
		$this->getList();
	}

	public function insert() {
		$this->load->language('order/order');

		$this->document->setTitle($this->language->get('heading_title'));
		
		$this->load->model('order/order');
		
		if (($this->request->server['REQUEST_METHOD'] == 'POST') && $this->validateForm()) {
			$this->model_order_order->add($this->request->post);

			$this->session->data['success'] = $this->language->get('text_success');
			
			$this->redirect($this->url->link('order/order', 'token=' . $this->session->data['token'], 'SSL')); 
		}

		$this->getForm();
	}

	public function update() {
		$this->load->language('order/order');

		$this->document->setTitle($this->language->get('heading_title'));
		
		$this->load->model('order/order');
		
		if (($this->request->server['REQUEST_METHOD'] == 'POST') && $this->validateForm()) {
			$this->model_order_order->edit($this->request->get['id'], $this->request->post);
			
			$this->session->data['success'] = $this->language->get('text_success');
			
            		$url_params = $this->generatePagingURLParams();
			$this->redirect($this->url->link('order/order', http_build_query($url_params), 'SSL'));
		}

		$this->getForm(1);
	}

	public function delete() {
		$this->load->language('order/order');

		$this->document->setTitle($this->language->get('heading_title'));
		
		$this->load->model('order/order');
		
		if (isset($this->request->post['selected']) && $this->validateDelete()) {
			foreach ($this->request->post['selected'] as $order_id) {
				$this->model_order_order($order_id);
			}

			$this->session->data['success'] = $this->language->get('text_success');

			$this->redirect($this->url->link('order/order', 'token=' . $this->session->data['token'], 'SSL'));
		}

		$this->getList();
	}

	private function generateURL() {
		$params = array();
        
		if (isset($this->request->get['filter_id'])) {
			$params['filter_id'] = $this->request->get['filter_id'];
		}

		if (isset($this->request->get['filter_name'])) {
			$params['filter_name'] = $this->request->get['filter_name'];
		}

		if (isset($this->request->get['filter_state'])) {
			$params['filter_state'] = $this->request->get['filter_state'];
		}

		if (isset($this->request->get['filter_message'])) {
			$params['filter_message'] = $this->request->get['filter_message'];
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

		$filter_name = $this->request->get('filter_name');
		$this->data['filter_name'] = $filter_name;

		$filter_state = $this->request->get('filter_state');
		$this->data['filter_state'] = $filter_state;

		$filter_message = $this->request->get('filter_message');
		$this->data['filter_message'] = $filter_message;

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
			'href'      => $this->url->link('order/order', http_build_query($url_params), 'SSL'),
      			'separator' => ' :: '
   		);
									
		$this->data['insert'] = $this->url->link('order/order/insert', http_build_query($url_params), 'SSL');
		$this->data['delete'] = $this->url->link('order/order/delete', http_build_query($url_params), 'SSL');

        	// query data
		$query_params = array(
                              'filter_id' => $filter_id,
                              'filter_name' => $filter_name,
                              'filter_state' => $filter_state,
                              'filter_message' => $filter_message,
                              'sort' => $sort,
                              'order' => $order,
                              'start' => ($page-1) * $this->config->get('config_admin_limit'),
                              'limit' => $this->config->get('config_admin_limit')
                              );
       		
 		$results = $this->model_order_order->getList($query_params);

		$this->data['list'] = array();

		$this->load->model('tool/image');
 
		foreach ($results as $result) {
			$action = array();
			
			$action[] = array(
				'text' => $this->language->get('text_edit'),
				'href' => $this->url->link('order/order/update', http_build_query( array_merge( array('id' => $result['id'] ),  $url_params)), 'SSL')
			);

            		$this->data['list'][] = array_merge(
                		$result, 
                		array('selected' => isset($this->request->post['selected']) && in_array($result['zid'], $this->request->post['selected']), 
                      		'action' => $action));
		}

        // render page list
        $page_url_params = $this->generateURL();

		$pagination = new Pagination();
		$pagination->total = $this->model_order_order->getTotalCount($query_params);
		$pagination->page = $page;
		$pagination->limit = $this->config->get('config_admin_limit');
		$pagination->text = $this->language->get('text_pagination');
		$pagination->url = $this->url->link('order/order', http_build_query($page_url_params) . "&page={page}", 'SSL');

		$this->data['pagination'] = $pagination->render();
		
        // output
		$this->template = 'order/order_list.tpl';
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
			'href'      => $this->url->link('order/order', http_build_query($url_params), 'SSL'),
      			'separator' => ' :: '
   		);
		
		if (!isset($this->request->get['id'])) {
			$this->data['action'] = $this->url->link('order/order/insert', null, 'SSL');
		} else {
			$this->data['action'] = $this->url->link('order/order/update', http_build_query(array_merge( array('id' =>  $this->request->get['id'] ), $url_params)) , 'SSL');
		}

		
		$this->data['cancel'] = $this->url->link('order/order', http_build_query($url_params), 'SSL');

		$this->data['token'] = $this->session->data['token'];


		$this->load->model('localisation/language');
		
		$this->data['languages'] = $this->model_localisation_language->getLanguages();

        	// data
        	if (isset($this->request->get['id']) && ($fromInternal == 1 || $this->request->server['REQUEST_METHOD'] != 'POST')) {
            		$item_info = $this->model_order_order->getItem($this->request->get['id']);
        	} else {
            		$item_info = null;
        	}

        	$this->data['id'] = $this->getWithDefault($item_info, 'id', null);
        	$this->data['name'] = $this->getWithDefault($item_info, 'name', '');        
        	$this->data['normalized_name'] = $this->getWithDefault($item_info, 'normalized_name', '');        
       		$this->data['overview'] = $this->getWithDefault($item_info, 'overview', '');
       		$this->data['overview_abstract'] = $this->getWithDefault($item_info, 'overview_abstract', '');
       		$this->data['employer_count'] = $this->getWithDefault($item_info, 'employer_count', '');
        	$this->data['url'] = $this->getWithDefault($item_info, 'url', '');
        	$this->data['email'] = $this->getWithDefault($item_info, 'email', '');
        	$this->data['location'] = $this->getWithDefault($item_info, 'location', '');
       		$this->data['category'] = $this->getWithDefault($item_info, 'category', '');
       		$this->data['property'] = $this->getWithDefault($item_info, 'property', '');
       		$this->data['scale'] = $this->getWithDefault($item_info, 'scale', '');
       		$this->data['cities'] = $this->getWithDefault($item_info, 'cities', '');

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

		$this->template = 'order/order_form.tpl';
		$this->children = array(
			'common/header',
			'common/footer'
		);
				
		$this->response->setOutput($this->render());
	}

	private function validateForm() {
		if (!$this->user->hasPermission('modify', 'order/order')) {
			$this->error['warning'] = $this->language->get('error_permission');
		}

		$name = $this->request->post['name'];

		if ((utf8_strlen($name) < 1) || (utf8_strlen($name) > 255)) {
		    $this->error['name'] = $this->language->get('error_name');
		}
		
		$eNum = $this->request->post['employer_count'];
		if ( !empty($eNum) && preg_match("/[^\d-]/",$eNum) ) {
		    $this->error['warning'] = 'employer count must be a number.';
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
