<?php 
require_once(DIR_SYSTEM . 'library/mail2.php');

class ControllerMonitorBackendApi extends Controller { 
	private $error = array();
 
	public function index() {
		$this->load->language('monitor/backend_api');

		$this->document->setTitle($this->language->get('heading_title'));
		
		$this->load->model('monitor/backend_api');
		 
		$this->getList();
	}

	public function insert() {
		$this->load->language('monitor/backend_api');

		$this->document->setTitle($this->language->get('heading_title'));
		
		$this->load->model('monitor/backend_api');
		
		if (($this->request->server['REQUEST_METHOD'] == 'POST') && $this->validateForm()) {
			$new_request_id = $this->model_monitor_backend_api->add($this->request->post);
            
            $url_params = $this->generatePagingURLParams();
            if ($new_request_id) {
                $this->session->data['success'] = $this->language->get('text_success');
                $url_params['request_id'] = $new_request_id;
            } else {
                $this->session->data['warning'] = "failed to generate invite code request";
            }
            
            $this->redirect($this->url->link('monitor/backend_api/update', http_build_query($url_params), 'SSL')); 
		}

		$this->getForm();
	}

	public function update() {
		$this->load->language('monitor/backend_api');

		$this->document->setTitle($this->language->get('heading_title'));
		
		$this->load->model('monitor/backend_api');
		
		if (($this->request->server['REQUEST_METHOD'] == 'POST') && $this->validateForm()) {
			$this->model_monitor_backend_api->edit($this->request->get['request_id'], $this->request->post);
			
			$this->session->data['success'] = $this->language->get('text_success');

            $url_params = $this->generatePagingURLParams();
			
			$this->redirect($this->url->link('monitor/backend_api', http_build_query($url_params), 'SSL'));
		}

		$this->getForm();
	}


	public function delete() {
		$this->load->language('monitor/backend_api');

		$this->document->setTitle($this->language->get('heading_title'));
		
		$this->load->model('monitor/backend_api');
		
		if (isset($this->request->post['selected']) && $this->validateDelete()) {
			foreach ($this->request->post['selected'] as $id) {
				$this->model_monitor_backend_api->delete($id);
			}

			$this->session->data['success'] = $this->language->get('text_success');

			$this->redirect($this->url->link('monitor/backend_api', null, 'SSL'));
		}

		$this->getList();
	}

    private function generateURLParams() {
        $params = array();
        
		if (isset($this->request->get['filter_requestId'])) {
            $params['filter_requestId'] = $this->request->get['filter_requestId'];
		}

		if (isset($this->request->get['filter_class'])) {
            $params['filter_class'] = $this->request->get['filter_class'];
		}

		if (isset($this->request->get['filter_name'])) {
            $params['filter_name'] = $this->request->get['filter_name'];
		}

		if (isset($this->request->get['filter_email'])) {
			$params['filter_email'] = $this->request->get['filter_email'];
		}

		if (isset($this->request->get['filter_company'])) {
			$params['filter_company'] = $this->request->get['filter_company'];
		}

		if (isset($this->request->get['filter_position'])) {
			$params['filter_position'] = $this->request->get['filter_position'];
		}

		if (isset($this->request->get['filter_code'])) {
			$params['filter_code'] = $this->request->get['filter_code'];
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
			'href'      => $this->url->link('monitor/backend_api', http_build_query($url_params), 'SSL'),
      		'separator' => ' :: '
   		);
									
        // query data
		$query_params = array(
                              'sort' => $sort,
                              'order' => $order,
                              'start' => ($page-1) * $this->config->get('config_admin_limit'),
                              'limit' => $this->config->get('config_admin_limit')
                              );
        
		$results = $this->model_monitor_backend_api->getList($query_params);

		$this->data['list'] = array();

		$this->load->model('tool/image');
 
		foreach ($results as $result) {
			$action = array();
			
            $this->data['list'][] = array_merge(
                $result, 
                array('selected' => isset($this->request->post['selected']) && in_array($result['zid'], $this->request->post['selected']), 
                      'action' => $action));
		}

        // render page list
        $page_url_params = $this->generateURLParams();

		$pagination = new Pagination();
		$pagination->total = $this->model_monitor_backend_api->getTotalCount($query_params);
		$pagination->page = $page;
		$pagination->limit = $this->config->get('config_admin_limit');
		$pagination->text = $this->language->get('text_pagination');
		$pagination->url = $this->url->link('account/invite_code_request', http_build_query($page_url_params) . "&page={page}", 'SSL');

		$this->data['pagination'] = $pagination->render();
		
        // output
		$this->template = 'monitor/backend_api_list.tpl';
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
			'href'      => $this->url->link('account/invite_code_request', http_build_query($this->generatePagingURLParams()), 'SSL'),
      		'separator' => ' :: '
   		);
		
		if (!isset($this->request->get['request_id'])) {
			$this->data['action'] = $this->url->link('account/invite_code_request/insert', null, 'SSL');
		} else {
			$this->data['action'] = $this->url->link('account/invite_code_request/update', http_build_query(array_merge( array('request_id' =>  $this->request->get['request_id'] ), $this->generatePagingURLParams())) , 'SSL');
            $this->data['action_issue_invite_code'] = $this->url->link('account/invite_code_request/issueInviteCode',  http_build_query(array_merge( array('request_id' =>  $this->request->get['request_id'] ), $this->generatePagingURLParams())), 'SSL');
            $this->data['action_issue_invite_code_with_email'] = $this->url->link('account/invite_code_request/issueInviteCode',  http_build_query(array_merge( array('request_id' =>  $this->request->get['request_id'], 'send_email' => '1' ), $this->generatePagingURLParams())), 'SSL');
            $this->data['action_send_notify_email'] = $this->url->link('account/invite_code_request/sendNotifyEmail',  http_build_query(array_merge( array('request_id' =>  $this->request->get['request_id'] ), $this->generatePagingURLParams())), 'SSL');
            $this->data['action_manual_notify'] = $this->url->link('account/invite_code_request/manualNotify',  http_build_query(array_merge( array('request_id' =>  $this->request->get['request_id'] ), $this->generatePagingURLParams())), 'SSL');


		}

		
		$this->data['cancel'] = $this->url->link('account/invite_code_request', http_build_query($this->generatePagingURLParams()), 'SSL');

		$this->data['token'] = $this->session->data['token'];


		$this->load->model('localisation/language');
		
		$this->data['languages'] = $this->model_localisation_language->getLanguages();

        // data
        if (isset($this->request->get['request_id']) && ($this->request->server['REQUEST_METHOD'] != 'POST')) {
            $item_info = $this->model_account_invite_code_request->getItem($this->request->get['request_id']);
        } else {
            $item_info = null;
        }

        $this->data['request_id'] = $this->getWithDefault($item_info, 'request_id', null);
        $this->data['class'] = $this->getWithDefault($item_info, 'class', '');
        $this->data['email'] = $this->getWithDefault($item_info, 'email', '');
        $this->data['name'] = $this->getWithDefault($item_info, 'name', '');
        $this->data['company'] = $this->getWithDefault($item_info, 'company', '');
        $this->data['position'] = $this->getWithDefault($item_info, 'position', '');
        $this->data['created_at'] = $this->getWithDefault($item_info, 'created_at', null);
        $this->data['issued_code'] = $this->getWithDefault($item_info, 'issued_code', null);
        $this->data['issued_at'] = $this->getWithDefault($item_info, 'issued_at', null);
        $this->data['notify_method'] = $this->getWithDefault($item_info, 'notify_method', null);
        $this->data['notify_time'] = $this->getWithDefault($item_info, 'notify_time', null);

		$this->template = 'account/invite_code_request_form.tpl';
		$this->children = array(
			'common/header',
			'common/footer'
		);
				
		$this->response->setOutput($this->render());
	}

	private function validateForm() {
		if (!$this->user->hasPermission('modify', 'account/invite_code_request')) {
			$this->error['warning'] = $this->language->get('error_permission');
		}
        
        $name = $this->request->post['name'];

        if ((utf8_strlen($name) < 1) || (utf8_strlen($name) > 255)) {
            $this->error['name'] = "name is invalid";
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
		if (!$this->user->hasPermission('modify', 'account/invite_code_request')) {
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
