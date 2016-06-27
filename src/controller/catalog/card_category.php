<?php 
class ControllerCatalogCardCategory extends Controller { 
	private $error = array();
 
	public function index() {
		$this->load->language('catalog/card_category');

		$this->document->setTitle($this->language->get('heading_title'));
		
		$this->load->model('catalog/card_category');
		 
		$this->getList();
	}

	public function insert() {
		$this->load->language('catalog/card_category');

		$this->document->setTitle($this->language->get('heading_title'));
		
		$this->load->model('catalog/card_category');
		
		if (($this->request->server['REQUEST_METHOD'] == 'POST') && $this->validateForm()) {
			$this->model_catalog_card_category->addCardCategory($this->request->post);

			$this->session->data['success'] = $this->language->get('text_success');
			
			$this->redirect($this->url->link('catalog/card_category', 'token=' . $this->session->data['token'], 'SSL')); 
		}

		$this->getForm();
	}

	public function update() {
		$this->load->language('catalog/card_category');

		$this->document->setTitle($this->language->get('heading_title'));
		
		$this->load->model('catalog/card_category');
		
		if (($this->request->server['REQUEST_METHOD'] == 'POST') && $this->validateForm()) {
			$this->model_catalog_card_category->editCardCategory($this->request->get['card_category_id'], $this->request->post);
			
			$this->session->data['success'] = $this->language->get('text_success');
			
			$this->redirect($this->url->link('catalog/card_category', 'token=' . $this->session->data['token'], 'SSL'));
		}

		$this->getForm();
	}

	public function delete() {
		$this->load->language('catalog/card_category');

		$this->document->setTitle($this->language->get('heading_title'));
		
		$this->load->model('catalog/card_category');
		
		if (isset($this->request->post['selected']) && $this->validateDelete()) {
			foreach ($this->request->post['selected'] as $card_category_id) {
				$this->model_catalog_card_category->deleteCardCategory($card_category_id);
			}

			$this->session->data['success'] = $this->language->get('text_success');

			$this->redirect($this->url->link('catalog/card_category', 'token=' . $this->session->data['token'], 'SSL'));
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
			'href'      => $this->url->link('catalog/card_category', 'token=' . $this->session->data['token'], 'SSL'),
      		'separator' => ' :: '
   		);
									
		$this->data['insert'] = $this->url->link('catalog/card_category/insert', 'token=' . $this->session->data['token'], 'SSL');
		$this->data['delete'] = $this->url->link('catalog/card_category/delete', 'token=' . $this->session->data['token'], 'SSL');
		
		$this->data['card_categories'] = array();

 		$results = $this->model_catalog_card_category->getCardCategories();
 
		foreach ($results as $result) {
			$action = array();
			
			$action[] = array(
				'text' => $this->language->get('text_edit'),
				'href' => $this->url->link('catalog/card_category/update', 'token=' . $this->session->data['token'] . '&card_category_id=' . $result['card_category_id'], 'SSL')
			);
					
			$this->data['card_categories'][] = array(
				'card_category_id' => $result['card_category_id'],
				'name'        => $result['name'],
				'sort_order'  => $result['sort_order'],
				'selected'    => isset($this->request->post['selected']) && in_array($result['card_category_id'], $this->request->post['selected']),
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
		
		$this->template = 'catalog/card_category_list.tpl';
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
			'href'      => $this->url->link('catalog/card_category', 'token=' . $this->session->data['token'], 'SSL'),
      		'separator' => ' :: '
   		);
		
		if (!isset($this->request->get['card_category_id'])) {
			$this->data['action'] = $this->url->link('catalog/card_category/insert', 'token=' . $this->session->data['token'], 'SSL');
		} else {
			$this->data['action'] = $this->url->link('catalog/card_category/update', 'token=' . $this->session->data['token'] . '&card_category_id=' . $this->request->get['card_category_id'], 'SSL');
		}
		
		$this->data['cancel'] = $this->url->link('catalog/card_category', 'token=' . $this->session->data['token'], 'SSL');

		$this->data['token'] = $this->session->data['token'];

		if (isset($this->request->get['card_category_id']) && ($this->request->server['REQUEST_METHOD'] != 'POST')) {
      		$card_category_info = $this->model_catalog_card_category->getCardCategory($this->request->get['card_category_id']);
    	}
		
		$this->load->model('localisation/language');
		
		$this->data['languages'] = $this->model_localisation_language->getLanguages();

		if (isset($this->request->post['card_category_description'])) {
			$this->data['card_category_description'] = $this->request->post['card_category_description'];
		} elseif (isset($this->request->get['card_category_id'])) {
			$this->data['card_category_description'] = $this->model_catalog_card_category->getCardCategoryDescriptions($this->request->get['card_category_id']);
		} else {
			$this->data['card_category_description'] = array();
		}

		$card_categories = $this->model_catalog_card_category->getCardCategories();

		// Remove own id from list
		if (!empty($card_category_info)) {
			foreach ($card_categories as $key => $card_category) {
				if ($card_category['card_category_id'] == $card_category_info['card_category_id']) {
					unset($card_categories[$key]);
				}
			}
		}

		$this->data['card_categories'] = $card_categories;	   
				
		if (isset($this->request->post['sort_order'])) {
			$this->data['sort_order'] = $this->request->post['sort_order'];
		} elseif (!empty($card_category_info)) {
			$this->data['sort_order'] = $card_category_info['sort_order'];
		} else {
			$this->data['sort_order'] = 0;
		}
		
        $this->load->model('catalog/occasion');

        $this->data['occasions'] = $this->model_catalog_occasion->getOccasions();

		if (isset($this->request->post['card_category_occasion'])) {
			$this->data['card_category_occasion'] = $this->request->post['card_category_occasion'];
		} elseif (isset($this->request->get['card_category_id'])) {
			$this->data['card_category_occasion'] = $this->model_catalog_card_category->getCategoryOccasions($this->request->get['card_category_id']);
		} else {
			$this->data['card_category_occasion'] = array();
		}
        
		if (isset($this->request->post['image'])) {
			$this->data['image'] = $this->request->post['image'];

		} elseif (!empty($card_info)) {
			$this->data['image'] = $card_info['image'];
		} else {
			$this->data['image'] = '';
		}

		$this->load->model('tool/image');
		
		if (!empty($card_category_info) && $card_category_info['image'] && file_exists(DIR_IMAGE . $card_category_info['image'])) {
			$this->data['thumb'] = $this->model_tool_image->resize($card_category_info['image'], 100, 100);
		} else {
			$this->data['thumb'] = $this->model_tool_image->resize('no_image.jpg', 100, 100);
		}

		$this->data['no_image'] = $this->model_tool_image->resize('no_image.jpg', 100, 100);

		$this->template = 'catalog/card_category_form.tpl';
		$this->children = array(
			'common/header',
			'common/footer'
		);
				
		$this->response->setOutput($this->render());
	}

	private function validateForm() {
		if (!$this->user->hasPermission('modify', 'catalog/card_category')) {
			$this->error['warning'] = $this->language->get('error_permission');
		}

		foreach ($this->request->post['card_category_description'] as $language_id => $value) {
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
		if (!$this->user->hasPermission('modify', 'catalog/card_category')) {
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