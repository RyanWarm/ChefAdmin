<?php 
class ControllerCatalogOccasion extends Controller { 
	private $error = array();
 
	public function index() {
		$this->load->language('catalog/occasion');

		$this->document->setTitle($this->language->get('heading_title'));
		
		$this->load->model('catalog/occasion');
		 
		$this->getList();
	}

	public function insert() {
		$this->load->language('catalog/occasion');

		$this->document->setTitle($this->language->get('heading_title'));
		
		$this->load->model('catalog/occasion');
		
		if (($this->request->server['REQUEST_METHOD'] == 'POST') && $this->validateForm()) {
			$this->model_catalog_occasion->addOccasion($this->request->post);

			$this->session->data['success'] = $this->language->get('text_success');
			
			$this->redirect($this->url->link('catalog/occasion', 'token=' . $this->session->data['token'], 'SSL')); 
		}

		$this->getForm();
	}

	public function update() {
		$this->load->language('catalog/occasion');

		$this->document->setTitle($this->language->get('heading_title'));
		
		$this->load->model('catalog/occasion');
		
		if (($this->request->server['REQUEST_METHOD'] == 'POST') && $this->validateForm()) {
			$this->model_catalog_occasion->editOccasion($this->request->get['occasion_id'], $this->request->post);
			
			$this->session->data['success'] = $this->language->get('text_success');
			
			$this->redirect($this->url->link('catalog/occasion', 'token=' . $this->session->data['token'], 'SSL'));
		}

		$this->getForm();
	}

	public function delete() {
		$this->load->language('catalog/occasion');

		$this->document->setTitle($this->language->get('heading_title'));
		
		$this->load->model('catalog/occasion');
		
		if (isset($this->request->post['selected']) && $this->validateDelete()) {
			foreach ($this->request->post['selected'] as $occasion_id) {

				$this->model_catalog_occasion->deleteOccasion($occasion_id);
			}

			$this->session->data['success'] = $this->language->get('text_success');

			$this->redirect($this->url->link('catalog/occasion', 'token=' . $this->session->data['token'], 'SSL'));
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
			'href'      => $this->url->link('catalog/occasion', 'token=' . $this->session->data['token'], 'SSL'),
      		'separator' => ' :: '
   		);
									
		$this->data['insert'] = $this->url->link('catalog/occasion/insert', 'token=' . $this->session->data['token'], 'SSL');
		$this->data['delete'] = $this->url->link('catalog/occasion/delete', 'token=' . $this->session->data['token'], 'SSL');
		
		$this->data['occasions'] = array();

 		$results = $this->model_catalog_occasion->getOccasions(0);
 
		foreach ($results as $result) {
			$action = array();
			
			$action[] = array(
				'text' => $this->language->get('text_edit'),
				'href' => $this->url->link('catalog/occasion/update', 'token=' . $this->session->data['token'] . '&occasion_id=' . $result['occasion_id'], 'SSL')
			);
					
			$this->data['occasions'][] = array(
				'occasion_id' => $result['occasion_id'],
				'name'        => $result['name'],
                'tag_count' => $result['tag_count'],
				'sort_order'  => $result['sort_order'],
                'status'      => $result['status'],
				'selected'    => isset($this->request->post['selected']) && in_array($result['occasion_id'], $this->request->post['selected']),
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
		
		$this->template = 'catalog/occasion_list.tpl';
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
			'href'      => $this->url->link('catalog/occasion', 'token=' . $this->session->data['token'], 'SSL'),
      		'separator' => ' :: '
   		);
		
		if (!isset($this->request->get['occasion_id'])) {
			$this->data['action'] = $this->url->link('catalog/occasion/insert', 'token=' . $this->session->data['token'], 'SSL');
		} else {
			$this->data['action'] = $this->url->link('catalog/occasion/update', 'token=' . $this->session->data['token'] . '&occasion_id=' . $this->request->get['occasion_id'], 'SSL');
		}
		
		$this->data['cancel'] = $this->url->link('catalog/occasion', 'token=' . $this->session->data['token'], 'SSL');

		$this->data['token'] = $this->session->data['token'];

		if (isset($this->request->get['occasion_id']) && ($this->request->server['REQUEST_METHOD'] != 'POST')) {
      		$occasion_info = $this->model_catalog_occasion->getOccasion($this->request->get['occasion_id']);
    	}
		
		$this->load->model('localisation/language');
		
		$this->data['languages'] = $this->model_localisation_language->getLanguages();

		if (isset($this->request->post['occasion_description'])) {
			$this->data['occasion_description'] = $this->request->post['occasion_description'];
		} elseif (isset($this->request->get['occasion_id'])) {
			$this->data['occasion_description'] = $this->model_catalog_occasion->getOccasionDescriptions($this->request->get['occasion_id']);
		} else {
			$this->data['occasion_description'] = array();
		}

		$occasions = $this->model_catalog_occasion->getOccasions(0);

		// Remove own id from list
		if (!empty($occasion_info)) {
			foreach ($occasions as $key => $occasion) {
				if ($occasion['occasion_id'] == $occasion_info['occasion_id']) {
					unset($occasions[$key]);
				}
			}
		}

		$this->data['occasions'] = $occasions;	   
				
		if (isset($this->request->post['sort_order'])) {
			$this->data['sort_order'] = $this->request->post['sort_order'];
		} elseif (!empty($occasion_info)) {
			$this->data['sort_order'] = $occasion_info['sort_order'];
		} else {
			$this->data['sort_order'] = 0;
		}
		
		if (isset($this->request->post['status'])) {
			$this->data['status'] = $this->request->post['status'];
		} elseif (!empty($occasion_info)) {
			$this->data['status'] = $occasion_info['status'];
		} else {
			$this->data['status'] = 0;
		}
        
        $this->load->model('catalog/tag');

        $this->data['tags'] = $this->model_catalog_tag->getTags();
        //var_dump($this->data['tags']);

        if (isset($this->request->post['occasion_tag'])) {
			$this->data['occasion_tag'] = $this->request->post['occasion_tag'];
		} elseif (isset($this->request->get['occasion_id'])) {
			$this->data['occasion_tag'] = $this->model_catalog_occasion->getOccasionTags($this->request->get['occasion_id']);
		} else {
			$this->data['occasion_tag'] = array();
		}		

		if (isset($this->request->post['parent_id'])) {
			$this->data['parent_id'] = $this->request->post['parent_id'];
		} elseif (!empty($occasion_info)) {
			$this->data['parent_id'] = $occasion_info['parent_id'];
		} else {
			$this->data['parent_id'] = 0;
		}

		if (isset($this->request->post['image'])) {
			$this->data['image'] = $this->request->post['image'];
		} elseif (!empty($occasion_info)) {
			$this->data['image'] = $occasion_info['image'];
		} else {
			$this->data['image'] = '';
		}
		
		$this->load->model('tool/image');

		if (!empty($occasion_info) && $occasion_info['image'] && file_exists(DIR_IMAGE . $occasion_info['image'])) {
			$this->data['thumb'] = $this->model_tool_image->resize($occasion_info['image'], 100, 100);
		} else {
			$this->data['thumb'] = $this->model_tool_image->resize('no_image.jpg', 100, 100);
		}
		
		$this->data['no_image'] = $this->model_tool_image->resize('no_image.jpg', 100, 100);

        if (isset($this->request->post['happen_time'])) {
            $this->data['happen_time'] = $this->request->post['happen_time'];
        } elseif (!empty($occasion_info)) {
            $tmp = explode(" ", $occasion_info['happen_time']);
            
            $this->data['happen_time'] = $tmp[0];
            
        } else {
            $this->data['happen_time'] = '';
        }

        if (isset($this->request->post['end_time'])) {
            $this->data['end_time'] = $this->request->post['end_time'];
        } elseif (!empty($occasion_info)) {
            $tmp = explode(" ", $occasion_info['end_time']);
            $this->data['end_time'] = $tmp[0];
        } else {
            $this->data['end_time'] = '';
        }
        
        if (isset($this->request->post['promote_days'])) {
            $this->data['promote_days'] = $this->request->post['promote_days'];
        } elseif (!empty($occasion_info)) {
            $this->data['promote_days'] = $occasion_info['promote_days'];
        } else {
            $this->data['promote_days'] = 0;
        }


		$this->template = 'catalog/occasion_form.tpl';
		$this->children = array(
			'common/header',
			'common/footer'
		);
				
		$this->response->setOutput($this->render());
	}

	private function validateForm() {
		if (!$this->user->hasPermission('modify', 'catalog/occasion')) {
			$this->error['warning'] = $this->language->get('error_permission');
		}

		foreach ($this->request->post['occasion_description'] as $language_id => $value) {
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
		if (!$this->user->hasPermission('modify', 'catalog/occasion')) {
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