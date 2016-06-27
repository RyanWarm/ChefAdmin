<?php 
class ControllerCatalogGiftImageCategory extends Controller { 
	private $error = array();
 
	public function index() {
		$this->load->language('catalog/gift_image_category');

		$this->document->setTitle($this->language->get('heading_title'));
		
		$this->load->model('catalog/gift_image_category');
		 
		$this->getList();
	}

	public function insert() {
		$this->load->language('catalog/gift_image_category');

		$this->document->setTitle($this->language->get('heading_title'));
		
		$this->load->model('catalog/gift_image_category');
		
		if (($this->request->server['REQUEST_METHOD'] == 'POST') && $this->validateForm()) {
			$this->model_catalog_gift_image_category->add($this->request->post);

			$this->session->data['success'] = $this->language->get('text_success');
			
			$this->redirect($this->url->link('catalog/gift_image_category', '', 'SSL')); 
		}

		$this->getForm();
	}

	public function update() {
		$this->load->language('catalog/gift_image_category');

		$this->document->setTitle($this->language->get('heading_title'));
		
		$this->load->model('catalog/gift_image_category');
		
		if (($this->request->server['REQUEST_METHOD'] == 'POST') && $this->validateForm()) {
			$this->model_catalog_gift_image_category->edit($this->request->get['category_id'], $this->request->post);
			
			$this->session->data['success'] = $this->language->get('text_success');
			
			$this->redirect($this->url->link('catalog/gift_image_category', '', 'SSL'));
		}

		$this->getForm();
	}

	public function delete() {
		$this->load->language('catalog/gift_image_category');

		$this->document->setTitle($this->language->get('heading_title'));
		
		$this->load->model('catalog/gift_image_category');
		
		if (isset($this->request->post['selected']) && $this->validateDelete()) {
			foreach ($this->request->post['selected'] as $id) {
				$this->model_catalog_gift_image_category->delete($id);
			}

			$this->session->data['success'] = $this->language->get('text_success');

			$this->redirect($this->url->link('catalog/gift_image_category', '', 'SSL'));
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
			'href'      => $this->url->link('catalog/gift_image_category', 'token=' . $this->session->data['token'], 'SSL'),
      		'separator' => ' :: '
   		);
        
		$this->data['insert'] = $this->url->link('catalog/gift_image_category/insert', '', 'SSL');
		$this->data['delete'] = $this->url->link('catalog/gift_image_category/delete', '', 'SSL');
		
		$this->data['gift_image_categories'] = array();

 		$results = $this->model_catalog_gift_image_category->getCategories();
 
		foreach ($results as $result) {
			$action = array();
			
			$action[] = array(
				'text' => $this->language->get('text_edit'),
				'href' => $this->url->link('catalog/gift_image_category/update', 'category_id=' . $result['category_id'], 'SSL')
			);

            $this->data['gift_image_categories'][] = array_merge(
                $result, 
                array('selected' => isset($this->request->post['selected']) && in_array($result['category_id'], $this->request->post['selected']), 
                      'action' => $action));
		}

        // UI text
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
		
		$this->template = 'catalog/gift_image_category_list.tpl';
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
			'href'      => $this->url->link('catalog/gift_image_category', '', 'SSL'),
      		'separator' => ' :: '
   		);
		
		if (!isset($this->request->get['category_id'])) {
			$this->data['action'] = $this->url->link('catalog/gift_image_category/insert', '', 'SSL');
		} else {
			$this->data['action'] = $this->url->link('catalog/gift_image_category/update', 'category_id=' . $this->request->get['category_id'], 'SSL');
		}
		
		$this->data['cancel'] = $this->url->link('catalog/gift_image_category', '', 'SSL');

		$this->data['token'] = $this->session->data['token'];

		if (isset($this->request->get['category_id']) && ($this->request->server['REQUEST_METHOD'] != 'POST')) {
      		$info = $this->model_catalog_gift_image_category->getCategory($this->request->get['category_id']);
    	}
		
		$this->load->model('localisation/language');
		
		$this->data['languages'] = $this->model_localisation_language->getLanguages();

        if (!empty($info)) {
			$this->data['name'] = $info['name'];
		} else {
			$this->data['name'] = "";
		}
        
		if (!empty($info)) {
			$this->data['sort_order'] = $info['sort_order'];
		} else {
			$this->data['sort_order'] = "";
		}

        if (!empty($info)) {
            $this->data['gift_images'] = $this->model_catalog_gift_image_category->getGiftImagesByCategory($info['category_id']);
            foreach ( $this->data['gift_images'] as &$gift_image) {
                $gift_image['edit_link'] = $this->url->link('catalog/gift_image/update', 'image_id=' . $gift_image['image_id'], 'SSL');
            }

        } else {
            $this->data['gift_images'] = array();
        }

		$this->template = 'catalog/gift_image_category_form.tpl';
		$this->children = array(
			'common/header',
			'common/footer'
		);
				
		$this->response->setOutput($this->render());
	}

	private function validateForm() {
		if (!$this->user->hasPermission('modify', 'catalog/gift_image_category')) {
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
		if (!$this->user->hasPermission('modify', 'catalog/gift_image_category')) {
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