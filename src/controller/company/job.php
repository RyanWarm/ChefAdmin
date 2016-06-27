<?php 
class ControllerCompanyJob extends Controller { 
	private $error = array();
 
	public function index() {
		$this->load->language('company/job');

		$this->document->setTitle($this->language->get('heading_title'));
		
		$this->load->model('company/job');
		 
		$this->getList();
	}

	public function insert() {
		$this->load->language('company/job');

		$this->document->setTitle($this->language->get('heading_title'));
		
		$this->load->model('company/job');
		
		if (($this->request->server['REQUEST_METHOD'] == 'POST') && $this->validateForm()) {
			$this->model_company_job->add($this->request->post);

			$this->session->data['success'] = $this->language->get('text_success');
			
			$this->redirect($this->url->link('company/job', 'token=' . $this->session->data['token'], 'SSL')); 
		}

		$this->getForm();
	}

	public function update() {
		$this->load->language('company/job');

		$this->document->setTitle($this->language->get('heading_title'));
		
		$this->load->model('company/job');
		
		if (($this->request->server['REQUEST_METHOD'] == 'POST') && $this->validateForm()) {
			$this->model_company_job->edit($this->request->get['id'], $this->request->post);
			
			$this->session->data['success'] = $this->language->get('text_success');
			
            		$url_params = $this->generatePagingURLParams();
			$this->redirect($this->url->link('company/job', http_build_query($url_params), 'SSL'));
		}

		$this->getForm();
	}

	public function delete() {
		$this->load->language('company/job');

		$this->document->setTitle($this->language->get('heading_title'));
		
		$this->load->model('company/job');
		
		if (isset($this->request->post['selected']) && $this->validateDelete()) {
			foreach ($this->request->post['selected'] as $id) {
				$this->model_company_job($id);
			}

			$this->session->data['success'] = $this->language->get('text_success');

			$this->redirect($this->url->link('company/job', 'token=' . $this->session->data['token'], 'SSL'));
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

		if (isset($this->request->get['filter_companyName'])) {
			$params['filter_companyName'] = $this->request->get['filter_companyName'];
		}

		if (isset($this->request->get['filter_date'])) {
			$params['filter_date'] = $this->request->get['filter_date'];
		}

		if (isset($this->request->get['filter_edu'])) {
			$params['filter_edu'] = $this->request->get['filter_edu'];
		}

		if (isset($this->request->get['filter_type'])) {
			$params['filter_type'] = $this->request->get['filter_type'];
		}

		if (isset($this->request->get['filter_role'])) {
			$params['filter_role'] = $this->request->get['filter_role'];
		}

		if (isset($this->request->get['filter_location'])) {
			$params['filter_location'] = $this->request->get['filter_location'];
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

		$filter_companyName = $this->request->get('filter_companyName');
		$this->data['filter_companyName'] = $filter_companyName;

		$filter_date = $this->request->get('filter_date');
		$this->data['filter_date'] = $filter_date;

		$filter_edu = $this->request->get('filter_edu');
		$this->data['filter_edu'] = $filter_edu;

		$filter_type = $this->request->get('filter_type');
		$this->data['filter_type'] = $filter_type;

		$filter_role = $this->request->get('filter_role');
		$this->data['filter_role'] = $filter_role;

		$filter_location = $this->request->get('filter_location');
		$this->data['filter_location'] = $filter_location;

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
			'href'      => $this->url->link('company/job', http_build_query($url_params), 'SSL'),
      			'separator' => ' :: '
   		);
									
		$this->data['insert'] = $this->url->link('company/job/insert', http_build_query($url_params), 'SSL');
		$this->data['delete'] = $this->url->link('company/job/delete', http_build_query($url_params), 'SSL');

        // query data
		$query_params = array(
                              'filter_id' => $filter_id,
                              'filter_name' => $filter_name,
                              'filter_location' => $filter_location,
                              'filter_companyName' => $filter_companyName,
                              'filter_date' => $filter_date,
                              'filter_edu' => $filter_edu,
                              'filter_type' => $filter_type,
                              'filter_role' => $filter_role,
                              'filter_location' => $filter_location,
                              'sort' => $sort,
                              'order' => $order,
                              'start' => ($page-1) * $this->config->get('config_admin_limit'),
                              'limit' => $this->config->get('config_admin_limit')
                              );
       		
 		$results = $this->model_company_job->getList($query_params);

		$this->data['list'] = array();

		$this->load->model('tool/image');
 
		foreach ($results as $result) {
			$action = array();
			
			$action[] = array(
				'text' => $this->language->get('text_edit'),
				'href' => $this->url->link('company/job/update', http_build_query( array_merge( array('id' => $result['id'] ),  $url_params)), 'SSL')
			);

            $this->data['list'][] = array_merge(
                $result, 
                array('selected' => isset($this->request->post['selected']) && in_array($result['zid'], $this->request->post['selected']), 
                      'action' => $action));
		}

        // render page list
        $page_url_params = $this->generateURL();

		$pagination = new Pagination();
		$pagination->total = $this->model_company_job->getTotalCount($query_params);
		$pagination->page = $page;
		$pagination->limit = $this->config->get('config_admin_limit');
		$pagination->text = $this->language->get('text_pagination');
		$pagination->url = $this->url->link('company/job', http_build_query($page_url_params) . "&page={page}", 'SSL');

		$this->data['pagination'] = $pagination->render();
		
        // output
		$this->template = 'company/job_list.tpl';
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
			'href'      => $this->url->link('company/job', http_build_query($url_params), 'SSL'),
      			'separator' => ' :: '
   		);
		
		if (!isset($this->request->get['id'])) {
			$this->data['action'] = $this->url->link('company/job/insert', null, 'SSL');
		} else {
			$this->data['action'] = $this->url->link('company/job/update', http_build_query(array_merge( array('id' =>  $this->request->get['id'] ), $this->generatePagingURLParams())) , 'SSL');
		}

		
		$this->data['cancel'] = $this->url->link('company/job', http_build_query($url_params), 'SSL');

		$this->data['token'] = $this->session->data['token'];


		$this->load->model('localisation/language');
		
		$statusTypes = $this->model_company_job->getStatusTypes();
		$this->data['statusTypes'] = $statusTypes;
		
		$this->data['languages'] = $this->model_localisation_language->getLanguages();

        	// data
        	if (isset($this->request->get['id']) && ($this->request->server['REQUEST_METHOD'] != 'POST')) {
            		$item_info = $this->model_company_job->getItem($this->request->get['id']);
        	} else {
            		$item_info = null;
        	}

        	$this->data['id'] = $this->getWithDefault($item_info, 'id', null);
        	$this->data['company_id'] = $this->getWithDefault($item_info, 'company_id', '');        
       		$this->data['title'] = $this->getWithDefault($item_info, 'title', '');
       		$this->data['location'] = $this->getWithDefault($item_info, 'location', '');
        	$this->data['create_date'] = $this->getWithDefault($item_info, 'create_date', '');
        	$this->data['edu_background'] = $this->getWithDefault($item_info, 'edu_background', '');
       		$this->data['job_type'] = $this->getWithDefault($item_info, 'job_type', '');
       		$this->data['management_exp'] = $this->getWithDefault($item_info, 'management_exp', '');
       		$this->data['work_exp'] = $this->getWithDefault($item_info, 'work_exp', '');
       		$this->data['category'] = $this->getWithDefault($item_info, 'category', '');
       		$this->data['url'] = $this->getWithDefault($item_info, 'url', '');
       		$this->data['headcount'] = $this->getWithDefault($item_info, 'headcount', '');
       		$this->data['email'] = $this->getWithDefault($item_info, 'email', '');
       		$this->data['description'] = $this->getWithDefault($item_info, 'description', '');
       		$this->data['exp_year_min'] = $this->getWithDefault($item_info, 'exp_year_min', '');
       		$this->data['exp_year_max'] = $this->getWithDefault($item_info, 'exp_year_max', '');
       		$this->data['salary_min'] = $this->getWithDefault($item_info, 'salary_min', '');
       		$this->data['salary_max'] = $this->getWithDefault($item_info, 'salary_max', '');
       		$this->data['department'] = $this->getWithDefault($item_info, 'department', '');
       		$this->data['status'] = $this->getWithDefault($item_info, 'status', '');
       		$this->data['expire_time'] = $this->getWithDefault($item_info, 'expire_time', '');

		$this->template = 'company/job_form.tpl';
		$this->children = array(
			'common/header',
			'common/footer'
		);
				
		$this->response->setOutput($this->render());
	}

	private function validateForm() {
		if (!$this->user->hasPermission('modify', 'company/job')) {
			$this->error['warning'] = $this->language->get('error_permission');
		}

		$name = $this->request->post['title'];

		if ((utf8_strlen($name) < 1) || (utf8_strlen($name) > 255)) {
		    $this->error['name'] = $this->language->get('error_name');
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
