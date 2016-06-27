<?php 
class ControllerCatalogProductGroup extends Controller { 
	private $error = array();
 
	public function index() {
		$this->load->language('catalog/product_group');

		$this->document->setTitle($this->language->get('heading_title'));
		
		$this->load->model('catalog/product_group');
		 
		$this->getList();
	}

	public function insert() {
		$this->load->language('catalog/product_group');

		$this->document->setTitle($this->language->get('heading_title'));
		
		$this->load->model('catalog/product_group');
		
		if (($this->request->server['REQUEST_METHOD'] == 'POST') && $this->validateForm()) {
			$this->model_catalog_product_group->addProductGroup($this->request->post);

			$this->session->data['success'] = $this->language->get('text_success');
			
			$this->redirect($this->url->link('catalog/product_group', 'token=' . $this->session->data['token'], 'SSL')); 
		}

		$this->getForm();
	}

	public function update() {
		$this->load->language('catalog/product_group');

		$this->document->setTitle($this->language->get('heading_title'));
		
		$this->load->model('catalog/product_group');
		
		if (($this->request->server['REQUEST_METHOD'] == 'POST') && $this->validateForm()) {
			$this->model_catalog_product_group->editProductGroup($this->request->get['product_group_id'], $this->request->post);
			
			$this->session->data['success'] = $this->language->get('text_success');
			
			$this->redirect($this->url->link('catalog/product_group', 'token=' . $this->session->data['token'], 'SSL'));
		}

		$this->getForm();
	}

	public function delete() {
		$this->load->language('catalog/product_group');

		$this->document->setTitle($this->language->get('heading_title'));
		
		$this->load->model('catalog/product_group');
		
		if (isset($this->request->post['selected']) && $this->validateDelete()) {
			foreach ($this->request->post['selected'] as $product_group_id) {
				$this->model_catalog_product_group->deleteProductGroup($product_group_id);
			}

			$this->session->data['success'] = $this->language->get('text_success');

			$this->redirect($this->url->link('catalog/product_group', 'token=' . $this->session->data['token'], 'SSL'));
		}

		$this->getList();
	}

	private function getList() {
   		$this->data['breadcrumbs'] = array();

   		$this->data['breadcrumbs'][] = array(
       		'text'      => $this->language->get('text_home'),
			'href'      => $this->url->link('common/home', 'token=' . $this->session->data['token'], 'SSL'),
      		'separator' => false
   		);

   		$this->data['breadcrumbs'][] = array(
       		'text'      => $this->language->get('heading_title'),
			'href'      => $this->url->link('catalog/product_group', 'token=' . $this->session->data['token'], 'SSL'),
      		'separator' => ' :: '
   		);
									
		$this->data['insert'] = $this->url->link('catalog/product_group/insert', 'token=' . $this->session->data['token'], 'SSL');
		$this->data['delete'] = $this->url->link('catalog/product_group/delete', 'token=' . $this->session->data['token'], 'SSL');
		
		$this->data['product_groups'] = array();

 		$results = $this->model_catalog_product_group->getProductGroups();
 
		foreach ($results as $result) {
			$action = array();
			
			$action[] = array(
				'text' => $this->language->get('text_edit'),
				'href' => $this->url->link('catalog/product_group/update', 'token=' . $this->session->data['token'] . '&product_group_id=' . $result['product_group_id'], 'SSL')
			);
					
			$this->data['product_groups'][] = array(
				'product_group_id' => $result['product_group_id'],
				'name'        => $result['name'],
                'let_them_choose' => $result['let_them_choose'],
				'sort_order'  => $result['sort_order'],
                'product_count' => $result['product_count'],
                //'status'      => $result['status'],
				'selected'    => isset($this->request->post['selected']) && in_array($result['product_group_id'], $this->request->post['selected']),
				'action'      => $action
			);
		}
		
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
		
		$this->template = 'catalog/product_group_list.tpl';
		$this->children = array(
			'common/header',
			'common/footer'
		);

                        
		$this->response->setOutput($this->render());
	}

	private function getForm() {
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
			$this->data['error_name'] = array();
		}

  		$this->data['breadcrumbs'] = array();

   		$this->data['breadcrumbs'][] = array(
       		'text'      => $this->language->get('text_home'),
			'href'      => $this->url->link('common/home', 'token=' . $this->session->data['token'], 'SSL'),
      		'separator' => false
   		);

   		$this->data['breadcrumbs'][] = array(
       		'text'      => $this->language->get('heading_title'),
			'href'      => $this->url->link('catalog/product_group', 'token=' . $this->session->data['token'], 'SSL'),
      		'separator' => ' :: '
   		);
		
		if (!isset($this->request->get['product_group_id'])) {
			$this->data['action'] = $this->url->link('catalog/product_group/insert', 'token=' . $this->session->data['token'], 'SSL');
		} else {
			$this->data['action'] = $this->url->link('catalog/product_group/update', 'token=' . $this->session->data['token'] . '&product_group_id=' . $this->request->get['product_group_id'], 'SSL');
		}
		
		$this->data['cancel'] = $this->url->link('catalog/product_group', 'token=' . $this->session->data['token'], 'SSL');

		$this->data['token'] = $this->session->data['token'];

		if (isset($this->request->get['product_group_id']) && ($this->request->server['REQUEST_METHOD'] != 'POST')) {
      		$product_group_info = $this->model_catalog_product_group->getProductGroup($this->request->get['product_group_id']);
    	}
		
		$this->load->model('localisation/language');
		
		$this->data['languages'] = $this->model_localisation_language->getLanguages();

		if (isset($this->request->post['product_group_description'])) {
			$this->data['product_group_description'] = $this->request->post['product_group_description'];
		} elseif (isset($this->request->get['product_group_id'])) {
			$this->data['product_group_description'] = $this->model_catalog_product_group->getProductGroupDescriptions($this->request->get['product_group_id']);
		} else {
			$this->data['product_group_description'] = array();
		}

		$product_groups = $this->model_catalog_product_group->getProductGroups();

		// Remove own id from list
		if (!empty($product_group_info)) {
			foreach ($product_groups as $key => $product_group) {
				if ($product_group['product_group_id'] == $product_group_info['product_group_id']) {
					unset($product_groups[$key]);
				}
			}
		}

		$this->data['product_groups'] = $product_groups;	   
				
		if (isset($this->request->post['sort_order'])) {
			$this->data['sort_order'] = $this->request->post['sort_order'];
		} elseif (!empty($product_group_info)) {
			$this->data['sort_order'] = $product_group_info['sort_order'];
		} else {
			$this->data['sort_order'] = 0;
		}

		if (isset($this->request->post['let_them_choose'])) {
			$this->data['let_them_choose'] = $this->request->post['let_them_choose'];
		} elseif (!empty($product_group_info)) {
			$this->data['let_them_choose'] = $product_group_info['let_them_choose'];
		} else {
			$this->data['let_them_choose'] = 0;
		}
		
        
		if (isset($this->request->post['orig_image'])) {
			$this->data['orig_image'] = $this->request->post['orig_image'];
		} elseif (!empty($product_group_info)) {
			$this->data['orig_image'] = $product_group_info['orig_image'];
		} else {
			$this->data['orig_image'] = '';
		}
        
		$this->load->model('tool/image');
		
		if (!empty($product_group_info) && $product_group_info['orig_image'] && file_exists(DIR_IMAGE . $product_group_info['orig_image'])) {
			$this->data['thumb'] = $this->model_tool_image->resize($product_group_info['orig_image'], 100, 100);
		} else {
			$this->data['thumb'] = $this->model_tool_image->resize('no_image.jpg', 100, 100);
		}

		$this->data['no_image'] = $this->model_tool_image->resize('no_image.jpg', 100, 100);

		if (isset($this->request->get['product_group_id'])) {
			$this->data['products'] = $this->model_catalog_product_group->getProductGroupProducts($this->request->get['product_group_id']);
            
            
            foreach ( $this->data['products'] as &$product) {
                $product['edit_link'] = $this->url->link('catalog/product/update', 'token=' . $this->session->data['token'] . '&product_id=' . $product['product_id'], 'SSL');
            }
		} else {
			$this->data['products'] = array();
		}		

		$this->template = 'catalog/product_group_form.tpl';
		$this->children = array(
			'common/header',
			'common/footer'
		);
				
		$this->response->setOutput($this->render());
	}

	private function validateForm() {
		if (!$this->user->hasPermission('modify', 'catalog/product_group')) {
			$this->error['warning'] = $this->language->get('error_permission');
		}

		foreach ($this->request->post['product_group_description'] as $language_id => $value) {
			if ((utf8_strlen($value['name']) < 1) || (utf8_strlen($value['name']) > 255)) {
				$this->error['name'][$language_id] = $this->language->get('error_name');
			}
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
		if (!$this->user->hasPermission('modify', 'catalog/product_group')) {
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