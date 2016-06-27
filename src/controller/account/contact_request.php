<?php 
class ControllerAccountContactRequest extends Controller { 
	private $error = array();
 
	public function index() {
		$this->load->language('account/contact_request');

		$this->document->setTitle($this->language->get('heading_title'));
		
		$this->load->model('account/contact_request');
		 
		$this->getList();
	}

	public function insert() {
		$this->load->language('account/contact_request');

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
		$this->load->language('account/contact_request');

		$this->document->setTitle($this->language->get('heading_title'));
		
		$this->load->model('account/contact_request');
		
		if (($this->request->server['REQUEST_METHOD'] == 'POST') && $this->validateForm()) {
			$this->model_account_contact_request->edit($this->request->get['request_id'], $this->request->post);
			
			$this->session->data['success'] = $this->language->get('text_success');
			
            		$url_params = $this->generatePagingURLParams();
			$this->redirect($this->url->link('account/contact_request', http_build_query($url_params), 'SSL'));
		}

		$this->getForm();
	}

	public function changeStatus() {
		$this->load->language('account/contact_request');

                $this->document->setTitle($this->language->get('heading_title'));

                $this->load->model('account/contact_request');

                if (($this->request->server['REQUEST_METHOD'] == 'POST') && $this->validateForm()) {
                        $this->model_account_contact_request->edit($this->request->get['request_id'], $this->request->post);

                        $this->session->data['success'] = $this->language->get('text_success');

			if (isset($this->request->post['st_admin_response']) || isset($this->request->post['st_admin_response_retry'])) {
				$sent = $this->_sendNotifyEmail($this->request->get['request_id']);
				if( !$sent ) {
					//sent failed, reset admin_response status to expire
					$this->model_account_contact_request->resetAdminResponseStatus($this->request->get['request_id'], "Notification email to HR sent failed.");
				}
			}
	
                        $url_params = $this->generatePagingURLParams();
                        $this->redirect($this->url->link('account/contact_request/update', http_build_query(array_merge( array('request_id' => $this->request->get['request_id'] ),  $url_params)), 'SSL'));
                }

                $this->getForm();
	}

	private function _sendNotifyEmail($request_id) {
		$url = "http://uc04/weibo_recruit_dev/api/internal/email/send_contact_response/" . $request_id;
		$output = $this->_internalServiceCall($url);

		if ($output) {
			$output_obj = json_decode($output, TRUE);
			if ($output_obj['error'] == 'success') {
				error_log("send email for " . $request_id . " result: " . $output_obj['error']);
				return true;
			} else {
				error_log("send email failed: " . $output_obj['error']);
			}
		} else {
			error_log("send email failed, empty response");
		}

		return false;
	}

	private function _getLinkedinUrl($profile_id) {
		$url = "http://uc06:8082?cmd=linkedin_user_url&uid=".$profile_id;
		$output = $this->_internalServiceCall($url);

		if ($output) {
			$output_obj = json_decode($output, TRUE);
			error_log( "linkedin user link: " . print_r($output_obj,true) );
			return $output_obj;
		} else {
			error_log("get linkedin user link failed, empty response");
		}
	}

	private function _internalServiceCall($url) {
		$ch = curl_init($url);
                curl_setopt ($ch, CURLOPT_HEADER, 0);
                curl_setopt ($ch, CURLOPT_RETURNTRANSFER, true);
                $output = curl_exec($ch);
                curl_close($ch);

		return $output;
	}

	public function delete() {
		$this->load->language('account/contact_request');

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
        
		if (isset($this->request->get['filter_request_id'])) {
			$params['filter_request_id'] = $this->request->get['filter_request_id'];
		}

		if (isset($this->request->get['filter_requester_zid'])) {
			$params['filter_requester_zid'] = $this->request->get['filter_requester_zid'];
		}

		if (isset($this->request->get['filter_job_id'])) {
			$params['filter_job_id'] = $this->request->get['filter_job_id'];
		}

		if (isset($this->request->get['filter_target_network'])) {
			$params['filter_target_network'] = $this->request->get['filter_target_network'];
		}

		if (isset($this->request->get['filter_status'])) {
			$params['filter_status'] = $this->request->get['filter_status'];
		}

		if (isset($this->request->get['filter_created_at'])) {
			$params['filter_created_at'] = $this->request->get['filter_created_at'];
		}

		if (isset($this->request->get['filter_updated_at'])) {
			$params['filter_updated_at'] = $this->request->get['filter_updated_at'];
		}

		if (isset($this->request->get['filter_subject'])) {
			$params['filter_subject'] = $this->request->get['filter_subject'];
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

		$filter_requester_zid = $this->request->get('filter_requester_zid');
		$this->data['filter_requester_zid'] = $filter_requester_zid;

		$filter_job_id = $this->request->get('filter_job_id');
		$this->data['filter_job_id'] = $filter_job_id;

		$filter_request_id = $this->request->get('filter_request_id');
		$this->data['filter_request_id'] = $filter_request_id;

		$filter_target_network = $this->request->get('filter_target_network');
		$this->data['filter_target_network'] = $filter_target_network;

		$filter_status = $this->request->get('filter_status');
		$this->data['filter_status'] = $filter_status;

		$filter_created_at = $this->request->get('filter_created_at');
		$this->data['filter_created_at'] = $filter_created_at;

		$filter_updated_at = $this->request->get('filter_updated_at');
		$this->data['filter_updated_at'] = $filter_updated_at;

		$filter_subject = $this->request->get('filter_subject');
		$this->data['filter_subject'] = $filter_subject;

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
			'href'      => $this->url->link('account/contact_request', http_build_query($url_params), 'SSL'),
      			'separator' => ' :: '
   		);
									
		$this->data['insert'] = $this->url->link('catalog/gift_image/insert', http_build_query($url_params), 'SSL');
		$this->data['delete'] = $this->url->link('catalog/gift_image/delete', http_build_query($url_params), 'SSL');

        	// query data
		$query_params = array(
		      'filter_requester_zid' => $filter_requester_zid,
		      'filter_job_id' => $filter_job_id,
		      'filter_request_id' => $filter_request_id,
		      'filter_target_network' => $filter_target_network,
		      'filter_status' => $filter_status,
		      'filter_created_at' => $filter_created_at,
		      'filter_updated_at' => $filter_updated_at,
		      'filter_subject' => $filter_subject,
		      'sort' => $sort,
		      'order' => $order,
		      'start' => ($page-1) * $this->config->get('config_admin_limit'),
		      'limit' => $this->config->get('config_admin_limit')
                );
       		
		$statusTypes = $this->model_account_contact_request->getStatusTypes();
		$this->data['statusTypes'] = $statusTypes;
		 
 		$results = $this->model_account_contact_request->getList($query_params);

		$this->data['list'] = array();

		$this->load->model('tool/image');

		foreach ($results as $result) {
			$latestStatus = $this->model_account_contact_request->getLatestStatus($result['request_id']);
			$result['latest_status'] = $latestStatus['status'];

			$action = array();
			
			$action[] = array(
				'text' => $this->language->get('text_edit'),
				'href' => $this->url->link('account/contact_request/update', http_build_query( array_merge( array('request_id' => $result['request_id'] ),  $url_params)), 'SSL')
			);

            		$this->data['list'][] = array_merge(
			$result, 
			array('selected' => isset($this->request->post['selected']) && in_array($result['request_id'], $this->request->post['selected']), 
			      'action' => $action));
		}

		// render page list
		$page_url_params = $this->generateURL();

		$pagination = new Pagination();
		$pagination->total = $this->model_account_contact_request->getTotalCount($query_params);
		$pagination->page = $page;
		$pagination->limit = $this->config->get('config_admin_limit');
		$pagination->text = $this->language->get('text_pagination');
		$pagination->url = $this->url->link('account/contact_request', http_build_query($page_url_params) . "&page={page}", 'SSL');

		$this->data['pagination'] = $pagination->render();
		
        	// output
		$this->template = 'account/contact_request_list.tpl';
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
			'href'      => $this->url->link('account/contact_request', http_build_query($url_params), 'SSL'),
      			'separator' => ' :: '
   		);
		
		if (!isset($this->request->get['request_id'])) {
			$this->data['action'] = $this->url->link('account/contact_request/insert', null, 'SSL');
		} else {
			$this->data['action'] = $this->url->link('account/contact_request/update', http_build_query(array_merge( array('request_id' =>  $this->request->get['request_id'] ), $url_params)) , 'SSL');
		}
		
		$this->data['action_change_status'] = $this->url->link('account/contact_request/changeStatus', http_build_query(array_merge( array('request_id' =>  $this->request->get['request_id'] ), $url_params)) , 'SSL');

		$this->data['cancel'] = $this->url->link('account/contact_request', http_build_query($url_params), 'SSL');

		$this->data['token'] = $this->session->data['token'];


		$this->load->model('localisation/language');
		
		$this->data['languages'] = $this->model_localisation_language->getLanguages();
		
        	// data
        	if (isset($this->request->get['request_id']) && ($this->request->server['REQUEST_METHOD'] != 'POST')) {
            		$item_info = $this->model_account_contact_request->getItem($this->request->get['request_id']);
        	} else {
            		$item_info = null;
        	}

        	$this->data['request_id'] = $this->getWithDefault($item_info, 'request_id', null);
        	$this->data['requester_zid'] = $this->getWithDefault($item_info, 'requester_zid', '');        
       		$this->data['requester_comment'] = $this->getWithDefault($item_info, 'requester_comment', '');
       		$this->data['requester_job_id'] = $this->getWithDefault($item_info, 'requester_job_id', '');
        	$this->data['target_network'] = $this->getWithDefault($item_info, 'target_network', null);
        	$this->data['target_profile_id'] = $this->getWithDefault($item_info, 'target_profile_id', null);
		
		if ( $this->data['target_network'] == 'weibo' ) {
        		$this->data['target_url'] = 'http://www.weibo.com/u/' . $this->data['target_profile_id'];
		} elseif ( $this->data['target_network'] == 'linkedin' ) {
        		$this->data['target_url'] = $this->_getLinkedinUrl($this->data['target_profile_id']);
		} else {
        		$this->data['target_url'] = '';
		}

        	$this->data['target_email'] = $this->getWithDefault($item_info, 'target_email', null);
        	$this->data['target_phone'] = $this->getWithDefault($item_info, 'target_phone', null);
        	$this->data['target_comment'] = $this->getWithDefault($item_info, 'target_comment', null);
        	$this->data['target_resume'] = $this->getWithDefault($item_info, 'target_resume', null);
        	$this->data['target_transactions'] = $this->getWithDefault($item_info, 'target_transactions', null);
        	$this->data['created_at'] = $this->getWithDefault($item_info, 'created_at', null);
        	$this->data['updated_at'] = $this->getWithDefault($item_info, 'updated_at', null);
        	$this->data['track_code'] = $this->getWithDefault($item_info, 'track_code', null);
        	$this->data['requester_subject'] = $this->getWithDefault($item_info, 'requester_subject', null);
        	//$this->data['status'] = $this->getWithDefault($item_info, 'status', '');

		//$statusTypes = $this->model_account_contact_request->getStatusTypes();
		//$this->data['statusTypes'] = $statusTypes;
		$requestStatus = $this->model_account_contact_request->getStatus($this->request->get['request_id']);
		$this->data['request_statuses'] = $requestStatus;

		$this->template = 'account/contact_request_form.tpl';
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
