<?php 
class ControllerAccountInviteCode extends Controller { 
	private $error = array();
 
	public function index() {
		$this->load->language('account/invite_code');

		$this->document->setTitle($this->language->get('heading_title'));
		
		$this->load->model('account/invite_code');
		 
		$this->getList();
	}

	public function insert() {
		$this->load->language('account/invite_code');

		$this->document->setTitle($this->language->get('heading_title'));
		
		$this->load->model('account/invite_code');
		
		if (($this->request->server['REQUEST_METHOD'] == 'POST') && $this->validateForm()) {
			$this->model_account_invite_code->add($this->request->post);

			$this->session->data['success'] = $this->language->get('text_success');
			
			$this->redirect($this->url->link('account/invite_code', null, 'SSL')); 
		}

		$this->getForm();
	}

	public function update() {
		$this->load->language('account/invite_code');

		$this->document->setTitle($this->language->get('heading_title'));
		
		$this->load->model('account/invite_code');
		
		if (($this->request->server['REQUEST_METHOD'] == 'POST') && $this->validateForm()) {
			$this->model_catalog_invite_code->edit($this->request->get['code'], $this->request->post);
			
			$this->session->data['success'] = $this->language->get('text_success');
			
			$this->redirect($this->url->link('account/invite_code', null, 'SSL'));
		}

		$this->getForm();
	}

	public function delete() {
		$this->load->language('account/invite_code');

		$this->document->setTitle($this->language->get('heading_title'));
		
		$this->load->model('account/invite_code');
		
		if (isset($this->request->post['selected']) && $this->validateDelete()) {
			foreach ($this->request->post['selected'] as $image_id) {
				$this->model_account_invite_code->delete($image_id);
			}

			$this->session->data['success'] = $this->language->get('text_success');

			$this->redirect($this->url->link('account/invite_code', null, 'SSL'));
		}

		$this->getList();
	}

    	private function generateURLParams() {
        	$params = array();
        
		if (isset($this->request->get['filter_code'])) {
            		$params['filter_code'] = $this->request->get['filter_code'];
		}

		if (isset($this->request->get['filter_regAccount'])) {
			$params['filter_regAccount'] = $this->request->get['filter_regAccount'];
		}

		if (isset($this->request->get['filter_reqId'])) {
			$params['filter_reqId'] = $this->request->get['filter_reqId'];
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
        $params = $this->generateURLParams();

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

        $filter_code = $this->request->get('filter_code');
        $this->data['filter_code'] = $filter_code;

        $filter_regAccount = $this->request->get('filter_regAccount');
        $this->data['filter_regAccount'] = $filter_regAccount;

        $filter_reqId = $this->request->get('filter_reqId');
        $this->data['filter_reqId'] = $filter_reqId;

        $sort = $this->request->get('sort');
		
        $order = $this->request->get('order');

        $page = $this->request->get('page', 1);

        $url_params = $this->generatePagingURLParams();
		
   		$this->data['breadcrumbs'] = array();

   		$this->data['breadcrumbs'][] = array(
       		'text'      => $this->language->get('text_home'),
			'href'      => $this->url->link('common/home', 'SSL'),
      		'separator' => false
   		);

   		$this->data['breadcrumbs'][] = array(
       		'text'      => $this->language->get('heading_title'),
			'href'      => $this->url->link('account/invite_code', http_build_query($url_params), 'SSL'),
      		'separator' => ' :: '
   		);
									
		$this->data['insert'] = $this->url->link('account/invite_code/insert', http_build_query($url_params), 'SSL');
		$this->data['delete'] = $this->url->link('account/invite_code/delete', http_build_query($url_params), 'SSL');

        // query data
		$query_params = array(
                              'filter_code' => $filter_code, 
                              'filter_regAccount' => $filter_regAccount,
                              'filter_reqId' => $filter_reqId,
                              'sort' => $sort,
                              'order' => $order,
                              'start' => ($page-1) * $this->config->get('config_admin_limit'),
                              'limit' => $this->config->get('config_admin_limit')
                              );
        
 		$results = $this->model_account_invite_code->getList($query_params);

		$this->data['list'] = array();

		$this->load->model('tool/image');
 
		foreach ($results as $result) {
			$action = array();
			
			$action[] = array(
				'text' => $this->language->get('text_edit'),
				'href' => $this->url->link('account/invite_code/update', http_build_query( array_merge( array('code' => $result['code'] ),  $url_params)), 'SSL')
			);
            
            $this->data['list'][] = array_merge(
                $result, 
                array('selected' => isset($this->request->post['selected']) && in_array($result['code'], $this->request->post['selected']), 
                      'action' => $action));
		}

        // render page list
        $page_url_params = $this->generateURLParams();

		$pagination = new Pagination();
		$pagination->total = $this->model_account_invite_code->getTotalCount($query_params);
		$pagination->page = $page;
		$pagination->limit = $this->config->get('config_admin_limit');
		$pagination->text = $this->language->get('text_pagination');
		$pagination->url = $this->url->link('account/invite_code', http_build_query($page_url_params) . "&page={page}", 'SSL');

		$this->data['pagination'] = $pagination->render();
		
        // output
		$this->template = 'account/invite_code_list.tpl';
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

  		$this->data['breadcrumbs'] = array();

   		$this->data['breadcrumbs'][] = array(
       		'text'      => $this->language->get('text_home'),
			'href'      => $this->url->link('common/home', '', 'SSL'),
      		'separator' => false
   		);

   		$this->data['breadcrumbs'][] = array(
       		'text'      => $this->language->get('heading_title'),
			'href'      => $this->url->link('account/invite_code', http_build_query($this->generatePagingURLParams()), 'SSL'),
      		'separator' => ' :: '
   		);
		
		if (!isset($this->request->get['code'])) {
			$this->data['action'] = $this->url->link('account/invite_code/insert', null, 'SSL');
		} else {
			$this->data['action'] = $this->url->link('account/invite_code/update', http_build_query(array_merge( array('code' =>  $this->request->get['code'] ), $this->generatePagingURLParams())) , 'SSL');
		}

		
		$this->data['cancel'] = $this->url->link('account/invite_code', http_build_query($this->generatePagingURLParams()), 'SSL');

		$this->data['token'] = $this->session->data['token'];


		$this->load->model('localisation/language');
		
		$this->data['languages'] = $this->model_localisation_language->getLanguages();

        // data
        if (isset($this->request->get['code']) && ($this->request->server['REQUEST_METHOD'] != 'POST')) {
            $item_info = $this->model_account_invite_code->getItem($this->request->get['code']);
        } else {
            $item_info = null;
        }

        $this->data['code'] = $this->getWithDefault($item_info, 'code', null);
        $this->data['class'] = $this->getWithDefault($item_info, 'class', '');        
        $this->data['request_id'] = $this->getWithDefault($item_info, 'request_id', '');
        $this->data['assignee'] = $this->getWithDefault($item_info, 'assignee', '');
        $this->data['created_at'] = $this->getWithDefault($item_info, 'created_at', '');
        $this->data['assigned_at'] = $this->getWithDefault($item_info, 'assigned_at', '');
        $this->data['updated_at'] = $this->getWithDefault($item_info, 'updated_at', '');

		$this->template = 'account/invite_code_form.tpl';
		$this->children = array(
			'common/header',
			'common/footer'
		);
				
		$this->response->setOutput($this->render());
	}

	private function validateForm() {
		if (!$this->user->hasPermission('modify', 'catalog/gift_image')) {
			$this->error['warning'] = $this->language->get('error_permission');
		}

        $name = $this->request->post['name'];

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
