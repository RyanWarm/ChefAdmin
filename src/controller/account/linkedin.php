<?php 
class ControllerAccountLinkedin extends Controller { 
	private $error = array();
 
	public function index() {
		$this->load->language('account/linkedin');

		$this->document->setTitle($this->language->get('heading_title'));
		
		$this->load->model('account/linkedin');
		 
		$this->getList();
	}

	public function insert() {
		$this->load->language('account/linkedin');

		$this->document->setTitle($this->language->get('heading_title'));
		
		$this->load->model('catalog/gift_image');
		
		if (($this->request->server['REQUEST_METHOD'] == 'POST') && $this->validateForm()) {
			$this->model_catalog_gift_image->add($this->request->post);

			$this->session->data['success'] = $this->language->get('text_success');
			
			$this->redirect($this->url->link('catalog/gift_image', 'token=' . $this->session->data['token'], 'SSL')); 
		}

		$this->getForm();
	}

	public function update() {
		$this->load->language('account/linkedin');

		$this->document->setTitle($this->language->get('heading_title'));
		
		$this->load->model('account/linkedin');
		
		if (($this->request->server['REQUEST_METHOD'] == 'POST') && $this->validateForm()) {
			$this->model_account_linkedin->edit($this->request->get['zid'], $this->request->post);
			
			$this->session->data['success'] = $this->language->get('text_success');
			
            		$url_params = $this->generatePagingURLParams();
			$this->redirect($this->url->link('account/linkedin', http_build_query($url_params), 'SSL'));
		}

		$this->getForm();
	}

	public function changeStatus() {
		$this->load->model('account/linkedin');
		
		$this->model_account_linkedin->edit($this->request->get['zid'], array('status' => 2));

		$url_params = $this->generatePagingURLParams();
		$this->redirect($this->url->link('account/linkedin', http_build_query($url_params), 'SSL'));
	
		$this->getList();
	}

	public function delete() {
		$this->load->language('account/linkedin');

		$this->document->setTitle($this->language->get('heading_title'));
		
		$this->load->model('account/account');
		
		if (isset($this->request->post['selected']) && $this->validateDelete()) {
			foreach ($this->request->post['selected'] as $image_id) {
				$this->model_catalog_gift_image->delete($image_id);
			}

			$this->session->data['success'] = $this->language->get('text_success');

			$this->redirect($this->url->link('catalog/gift_image', 'token=' . $this->session->data['token'], 'SSL'));
		}

		$this->getList();
	}

    	private function generateURL() {
        	$params = array();
        
		if (isset($this->request->get['filter_score'])) {
			$params['filter_score'] = $this->request->get['filter_score'];
		}

		if (isset($this->request->get['filter_category'])) {
			$params['filter_category'] = $this->request->get['filter_category'];
		}

		if (isset($this->request->get['filter_zid'])) {
			$params['filter_zid'] = $this->request->get['filter_zid'];
		}

		if (isset($this->request->get['filter_status'])) {
			$params['filter_status'] = $this->request->get['filter_status'];
		}

		if (isset($this->request->get['sort'])) {
           		 $params['sort'] = $this->request->get['sort'];
		}

		if (isset($this->request->get['order'])) {
            		$params['order'] = $this->request->get['order'];
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

		$filter_score = $this->request->get('filter_score');
		$this->data['filter_score'] = $filter_score;

		$filter_status = $this->request->get('filter_status');
		$this->data['filter_status'] = $filter_status;

		$filter_zid = $this->request->get('filter_zid');
		$this->data['filter_zid'] = $filter_zid;

		$filter_category = $this->request->get('filter_category');
		$this->data['filter_category'] = $filter_category;

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
			'href'      => $this->url->link('account/linkedin', http_build_query($url_params), 'SSL'),
      			'separator' => ' :: '
   		);
									
		$this->data['insert'] = $this->url->link('catalog/gift_image/insert', http_build_query($url_params), 'SSL');
		$this->data['delete'] = $this->url->link('catalog/gift_image/delete', http_build_query($url_params), 'SSL');

        	// query data
		$query_params = array(
		      'filter_score' => $filter_score,
		      'filter_status' => $filter_status,
		      'filter_zid' => $filter_zid,
		      'filter_category' => $filter_category,
		      'sort' => $sort,
		      'order' => $order,
		      'start' => ($page-1) * $this->config->get('config_admin_limit'),
		      'limit' => $this->config->get('config_admin_limit')
                );
       		
		$statusTypes = $this->model_account_linkedin->getStatusTypes();
		$this->data['statusTypes'] = $statusTypes;
		 
 		$results = $this->model_account_linkedin->getList($query_params);

		$this->data['list'] = array();

		$this->load->model('tool/image');

		foreach ($results as $result) {
			$action = array();
			
			$action[] = array(
				'text' => $this->language->get('text_edit'),
				'href' => $this->url->link('account/linkedin/update', http_build_query( array_merge( array('zid' => $result['zid'] ),  $url_params)), 'SSL')
			);

			$result['status_name'] = $this->model_account_linkedin->getStatusName($result['status']);
			$result['changeStatus'] = $this->url->link('account/linkedin/changeStatus', http_build_query( array_merge( array('zid' => $result['zid']), $url_params ) ), 'SSL');
            		$this->data['list'][] = array_merge(
			$result, 
			array('selected' => isset($this->request->post['selected']) && in_array($result['zid'], $this->request->post['selected']), 
			      'action' => $action));
		}

		// render page list
		$page_url_params = $this->generateURL();

		$pagination = new Pagination();
		$pagination->total = $this->model_account_linkedin->getTotalCount($query_params);
		$pagination->page = $page;
		$pagination->limit = $this->config->get('config_admin_limit');
		$pagination->text = $this->language->get('text_pagination');
		$pagination->url = $this->url->link('account/linkedin', http_build_query($page_url_params) . "&page={page}", 'SSL');

		$this->data['pagination'] = $pagination->render();
		
        	// output
		$this->template = 'account/linkedin_list.tpl';
		$this->children = array(
			'common/header',
			'common/footer'
		);

		$this->response->setOutput($this->render());
	}

	private function getForm() {
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
	
		$this->data['text_select_all'] = $this->language->get('text_select_all');
		$this->data['text_unselect_all'] = $this->language->get('text_unselect_all');
	
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
			'href'      => $this->url->link('account/linkedin', http_build_query($url_params), 'SSL'),
      			'separator' => ' :: '
   		);
		
		if (!isset($this->request->get['zid'])) {
			$this->data['action'] = $this->url->link('account/linkedin/insert', null, 'SSL');
		} else {
			$this->data['action'] = $this->url->link('account/linkedin/update', http_build_query(array_merge( array('zid' =>  $this->request->get['zid'] ), $url_params)) , 'SSL');
		}

		
		$this->data['cancel'] = $this->url->link('account/linkedin', http_build_query($url_params), 'SSL');

		$this->data['token'] = $this->session->data['token'];


		$this->load->model('localisation/language');
		
		$this->data['languages'] = $this->model_localisation_language->getLanguages();
		
        	// data
        	if (isset($this->request->get['zid']) && ($this->request->server['REQUEST_METHOD'] != 'POST')) {
            		$item_info = $this->model_account_linkedin->getItem($this->request->get['zid']);
        	} else {
            		$item_info = null;
        	}

        	$this->data['zid'] = $this->getWithDefault($item_info, 'zid', null);
        	$this->data['score'] = $this->getWithDefault($item_info, 'score', '');        
       		$this->data['link'] = $this->getWithDefault($item_info, 'link', '');
       		$this->data['category'] = $this->getWithDefault($item_info, 'category', '');
        	$this->data['status'] = $this->getWithDefault($item_info, 'status', '');

		$statusTypes = $this->model_account_linkedin->getStatusTypes();
		$this->data['statusTypes'] = $statusTypes;

		$this->template = 'account/linkedin_form.tpl';
		$this->children = array(
			'common/header',
			'common/footer'
		);
				
		$this->response->setOutput($this->render());
	}

	private function validateForm() {
		if (!$this->user->hasPermission('modify', 'account/account')) {
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
