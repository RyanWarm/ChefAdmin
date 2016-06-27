<?php 
class ControllerCatalogTag extends Controller { 
	private $error = array();
 
	public function index() {
		$this->load->language('catalog/tag');

		$this->document->setTitle($this->language->get('heading_title'));
		
		$this->load->model('catalog/tag');
		 
		$this->getList();
	}

	public function insert() {
		$this->load->language('catalog/tag');

		$this->document->setTitle($this->language->get('heading_title'));
		
		$this->load->model('catalog/tag');
		
		if (($this->request->server['REQUEST_METHOD'] == 'POST') && $this->validateForm()) {
			$this->model_catalog_tag->addTag($this->request->post);

			$this->session->data['success'] = $this->language->get('text_success');
			
			$this->redirect($this->url->link('catalog/tag', 'token=' . $this->session->data['token'], 'SSL')); 
		}

		$this->getForm();
	}

	public function update() {
		$this->load->language('catalog/tag');

		$this->document->setTitle($this->language->get('heading_title'));
		
		$this->load->model('catalog/tag');
		
		if (($this->request->server['REQUEST_METHOD'] == 'POST') && $this->validateForm()) {
			$this->model_catalog_tag->editTag($this->request->get['tag_id'], $this->request->post);
			
			$this->session->data['success'] = $this->language->get('text_success');
			
			$this->redirect($this->url->link('catalog/tag', 'token=' . $this->session->data['token'], 'SSL'));
		}

		$this->getForm();
	}

	public function delete() {
		$this->load->language('catalog/tag');

		$this->document->setTitle($this->language->get('heading_title'));
		
		$this->load->model('catalog/tag');
		
		if (isset($this->request->post['selected']) && $this->validateDelete()) {
			foreach ($this->request->post['selected'] as $tag_id) {
				$this->model_catalog_tag->deleteTag($tag_id);
			}

			$this->session->data['success'] = $this->language->get('text_success');

			$this->redirect($this->url->link('catalog/tag', 'token=' . $this->session->data['token'], 'SSL'));
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
			'href'      => $this->url->link('catalog/tag', 'token=' . $this->session->data['token'], 'SSL'),
      		'separator' => ' :: '
   		);
									
		$this->data['insert'] = $this->url->link('catalog/tag/insert', 'token=' . $this->session->data['token'], 'SSL');
		$this->data['delete'] = $this->url->link('catalog/tag/delete', 'token=' . $this->session->data['token'], 'SSL');
		
		$this->data['tags'] = array();

 		$results = $this->model_catalog_tag->getTags(0);
 
		foreach ($results as $result) {
			$action = array();
			
			$action[] = array(
				'text' => $this->language->get('text_edit'),
				'href' => $this->url->link('catalog/tag/update', 'token=' . $this->session->data['token'] . '&tag_id=' . $result['tag_id'], 'SSL')
			);
					
			$this->data['tags'][] = array(
				'tag_id' => $result['tag_id'],
				'name'        => $result['name'],
                'product_count' => $result['product_count'],
				'sort_order'  => $result['sort_order'],
				'selected'    => isset($this->request->post['selected']) && in_array($result['tag_id'], $this->request->post['selected']),
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
		
		$this->template = 'catalog/tag_list.tpl';
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
			'href'      => $this->url->link('catalog/tag', 'token=' . $this->session->data['token'], 'SSL'),
      		'separator' => ' :: '
   		);
		
		if (!isset($this->request->get['tag_id'])) {
			$this->data['action'] = $this->url->link('catalog/tag/insert', 'token=' . $this->session->data['token'], 'SSL');
		} else {
			$this->data['action'] = $this->url->link('catalog/tag/update', 'token=' . $this->session->data['token'] . '&tag_id=' . $this->request->get['tag_id'], 'SSL');
		}
		
		$this->data['cancel'] = $this->url->link('catalog/tag', 'token=' . $this->session->data['token'], 'SSL');

		$this->data['token'] = $this->session->data['token'];

		if (isset($this->request->get['tag_id']) && ($this->request->server['REQUEST_METHOD'] != 'POST')) {
      		$tag_info = $this->model_catalog_tag->getTag($this->request->get['tag_id']);
    	}
		
		$this->load->model('localisation/language');
		
		$this->data['languages'] = $this->model_localisation_language->getLanguages();

		if (isset($this->request->post['tag_description'])) {
			$this->data['tag_description'] = $this->request->post['tag_description'];
		} elseif (isset($this->request->get['tag_id'])) {
			$this->data['tag_description'] = $this->model_catalog_tag->getTagDescriptions($this->request->get['tag_id']);
		} else {
			$this->data['tag_description'] = array();
		}

		$tags = $this->model_catalog_tag->getTags(0);

		// Remove own id from list
		if (!empty($tag_info)) {
			foreach ($tags as $key => $tag) {
				if ($tag['tag_id'] == $tag_info['tag_id']) {
					unset($tags[$key]);
				}
			}
		}

		$this->data['tags'] = $tags;

		if (isset($this->request->post['parent_id'])) {
			$this->data['parent_id'] = $this->request->post['parent_id'];
		} elseif (!empty($tag_info)) {
			$this->data['parent_id'] = $tag_info['parent_id'];
		} else {
			$this->data['parent_id'] = 0;
		}

		
		$this->load->model('setting/store');
        		
		$this->data['stores'] = $this->model_setting_store->getStores();
		
		if (isset($this->request->post['tag_store'])) {
			$this->data['tag_store'] = $this->request->post['tag_store'];
		} elseif (isset($this->request->get['tag_id'])) {
			$this->data['tag_store'] = $this->model_catalog_tag->getTagStores($this->request->get['tag_id']);
		} else {
			$this->data['tag_store'] = array(0);
		}			
	   

		if (isset($this->request->post['keyword'])) {
			$this->data['keyword'] = $this->request->post['keyword'];
		} elseif (!empty($tag_info)) {
			$this->data['keyword'] = $tag_info['keyword'];
		} else {
			$this->data['keyword'] = '';
		}

		if (isset($this->request->post['image'])) {
			$this->data['image'] = $this->request->post['image'];
		} elseif (!empty($tag_info)) {
			$this->data['image'] = $tag_info['image'];
		} else {
			$this->data['image'] = '';
		}
		
		$this->load->model('tool/image');

		if (!empty($tag_info) && $tag_info['image'] && file_exists(DIR_IMAGE . $tag_info['image'])) {
			$this->data['thumb'] = $this->model_tool_image->resize($tag_info['image'], 100, 100);
		} else {
			$this->data['thumb'] = $this->model_tool_image->resize('no_image.jpg', 100, 100);
		}
		
		$this->data['no_image'] = $this->model_tool_image->resize('no_image.jpg', 100, 100);
		
		if (isset($this->request->post['top'])) {
			$this->data['top'] = $this->request->post['top'];
		} elseif (!empty($tag_info)) {
			$this->data['top'] = $tag_info['top'];
		} else {
			$this->data['top'] = 0;
		}
		
		if (isset($this->request->post['column'])) {
			$this->data['column'] = $this->request->post['column'];
		} elseif (!empty($tag_info)) {
			$this->data['column'] = $tag_info['column'];
		} else {
			$this->data['column'] = 1;
		}
				
		if (isset($this->request->post['sort_order'])) {
			$this->data['sort_order'] = $this->request->post['sort_order'];
		} elseif (!empty($tag_info)) {
			$this->data['sort_order'] = $tag_info['sort_order'];
		} else {
			$this->data['sort_order'] = 0;
		}
		
		if (isset($this->request->post['status'])) {
			$this->data['status'] = $this->request->post['status'];
		} elseif (!empty($tag_info)) {
			$this->data['status'] = $tag_info['status'];
		} else {
			$this->data['status'] = 1;
		}
        
		if (isset($this->request->post['score'])) {
			$this->data['score'] = $this->request->post['score'];
		} elseif (!empty($tag_info)) {
			$this->data['score'] = $tag_info['score'];
		} else {
			$this->data['score'] = 1;
		}
        
		if (isset($this->request->post['tag_layout'])) {
			$this->data['_layout'] = $this->request->post['tag_layout'];
		} elseif (isset($this->request->get['tag_id'])) {
			$this->data['tag_layout'] = $this->model_catalog_tag->getTagLayouts($this->request->get['tag_id']);
		} else {
			$this->data['tag_layout'] = array();
		}

		$this->load->model('design/layout');
		
		$this->data['layouts'] = $this->model_design_layout->getLayouts();

        if (isset($this->request->get['tag_id'])) {
            $this->data['products'] = $this->model_catalog_tag->getTagProducts($this->request->get['tag_id']);

            foreach ( $this->data['products'] as &$product) {
                $product['edit_link'] = $this->url->link('catalog/product/update', 'token=' . $this->session->data['token'] . '&product_id=' . $product['product_id'], 'SSL');
            }

        } else {
            $this->data['products'] = array();
        }
						
		$this->template = 'catalog/tag_form.tpl';
		$this->children = array(
			'common/header',
			'common/footer'
		);
				
		$this->response->setOutput($this->render());
	}

	private function validateForm() {
		if (!$this->user->hasPermission('modify', 'catalog/tag')) {
			$this->error['warning'] = $this->language->get('error_permission');
		}

		foreach ($this->request->post['tag_description'] as $language_id => $value) {
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
		if (!$this->user->hasPermission('modify', 'catalog/tag')) {
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