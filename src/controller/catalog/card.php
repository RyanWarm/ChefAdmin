<?php 
class ControllerCatalogCard extends Controller { 
	private $error = array();
 
	public function index() {
		$this->load->language('catalog/card');

		$this->document->setTitle($this->language->get('heading_title'));
		
		$this->load->model('catalog/card');
		 
		$this->getList();
	}

	public function insert() {
		$this->load->language('catalog/card');

		$this->document->setTitle($this->language->get('heading_title'));
		
		$this->load->model('catalog/card');
		
		if (($this->request->server['REQUEST_METHOD'] == 'POST') && $this->validateForm()) {
			$this->model_catalog_card->addCard($this->request->post);

			$this->session->data['success'] = $this->language->get('text_success');
			
			$this->redirect($this->url->link('catalog/card', 'token=' . $this->session->data['token'], 'SSL')); 
		}

		$this->getForm();
	}

	public function update() {
		$this->load->language('catalog/card');

		$this->document->setTitle($this->language->get('heading_title'));
		
		$this->load->model('catalog/card');
		
		if (($this->request->server['REQUEST_METHOD'] == 'POST') && $this->validateForm()) {
			$this->model_catalog_card->editCard($this->request->get['card_id'], $this->request->post);
			
			$this->session->data['success'] = $this->language->get('text_success');
			
			$this->redirect($this->url->link('catalog/card', 'token=' . $this->session->data['token'], 'SSL'));
		}

		$this->getForm();
	}

	public function delete() {
		$this->load->language('catalog/card');

		$this->document->setTitle($this->language->get('heading_title'));
		
		$this->load->model('catalog/card');
		
		if (isset($this->request->post['selected']) && $this->validateDelete()) {
			foreach ($this->request->post['selected'] as $card_id) {
				$this->model_catalog_card->deleteCard($card_id);
			}

			$this->session->data['success'] = $this->language->get('text_success');

			$this->redirect($this->url->link('catalog/card', 'token=' . $this->session->data['token'], 'SSL'));
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
			'href'      => $this->url->link('catalog/card', 'token=' . $this->session->data['token'], 'SSL'),
      		'separator' => ' :: '
   		);
									
		$this->data['insert'] = $this->url->link('catalog/card/insert', 'token=' . $this->session->data['token'], 'SSL');
		$this->data['delete'] = $this->url->link('catalog/card/delete', 'token=' . $this->session->data['token'], 'SSL');
		
		$this->data['cards'] = array();

 		$results = $this->model_catalog_card->getCards();
		$this->load->model('tool/image');
 
		foreach ($results as $result) {
			$action = array();
			
			$action[] = array(
				'text' => $this->language->get('text_edit'),
				'href' => $this->url->link('catalog/card/update', 'token=' . $this->session->data['token'] . '&card_id=' . $result['card_id'], 'SSL')
			);

			if ($result['orig_image'] && file_exists(DIR_IMAGE . $result['orig_image'])) {
				$image = $this->model_tool_image->resize($result['orig_image'], 80, 80);
			} else {
				$image = $this->model_tool_image->resize('no_image.jpg', 40, 40);
			}
            
            error_log($image);

			$this->data['cards'][] = array(
				'card_id' => $result['card_id'],
                'image'   => $image,
				'name'        => $result['name'],
                'theme_id'    => $result['theme_id'],
                'default_body' => $result['default_body'],
                'category'    => $result['category'],
				'sort_order'  => $result['sort_order'],
                'status'      => $result['status'],
				'selected'    => isset($this->request->post['selected']) && in_array($result['card_id'], $this->request->post['selected']),
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
		
		$this->template = 'catalog/card_list.tpl';
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
			'href'      => $this->url->link('common/home', 'token=' . $this->session->data['token'], 'SSL'),
      		'separator' => false
   		);

   		$this->data['breadcrumbs'][] = array(
       		'text'      => $this->language->get('heading_title'),
			'href'      => $this->url->link('catalog/card', 'token=' . $this->session->data['token'], 'SSL'),
      		'separator' => ' :: '
   		);
		
		if (!isset($this->request->get['card_id'])) {
			$this->data['action'] = $this->url->link('catalog/card/insert', 'token=' . $this->session->data['token'], 'SSL');
		} else {
			$this->data['action'] = $this->url->link('catalog/card/update', 'token=' . $this->session->data['token'] . '&card_id=' . $this->request->get['card_id'], 'SSL');
		}
		
		$this->data['cancel'] = $this->url->link('catalog/card', 'token=' . $this->session->data['token'], 'SSL');

		$this->data['token'] = $this->session->data['token'];

		if (isset($this->request->get['card_id']) && ($this->request->server['REQUEST_METHOD'] != 'POST')) {
      		$card_info = $this->model_catalog_card->getCard($this->request->get['card_id']);
    	}
		
		$this->load->model('localisation/language');
		
		$this->data['languages'] = $this->model_localisation_language->getLanguages();

		if (isset($this->request->post['name'])) {
			$this->data['name'] = $this->request->post['name'];
		} elseif (!empty($card_info)) {
			$this->data['name'] = $card_info['name'];
		} else {
			$this->data['name'] = "";
		}
        
        /*
		if (isset($this->request->post['template'])) {
			$this->data['template'] = $this->request->post['template'];
		} elseif (!empty($card_info)) {
			$this->data['template'] = $card_info['template'];
		} else {
			$this->data['template'] = "";
		}
        */

		if (isset($this->request->post['bg_color'])) {
			$this->data['bg_color'] = $this->request->post['bg_color'];

		} elseif (!empty($card_info)) {
			$this->data['bg_color'] = $card_info['bg_color'];
		} else {
			$this->data['bg_color'] = '';
        }

		if (isset($this->request->post['theme_id'])) {
			$this->data['theme_id'] = $this->request->post['theme_id'];
		} elseif (!empty($card_info)) {
			$this->data['theme_id'] = $card_info['theme_id'];
		} else {
			$this->data['theme_id'] = 1;
        }

		if (isset($this->request->post['default_body'])) {
			$this->data['default_body'] = $this->request->post['default_body'];
		} elseif (!empty($card_info)) {
			$this->data['default_body'] = $card_info['default_body'];
		} else {
			$this->data['default_body'] = '';
        }

		if (isset($this->request->post['orig_image'])) {
			$this->data['orig_image'] = $this->request->post['orig_image'];

		} elseif (!empty($card_info)) {
			$this->data['orig_image'] = $card_info['orig_image'];
		} else {
			$this->data['orig_image'] = '';
		}
        
		$this->load->model('tool/image');
		
		if (!empty($card_info) && $card_info['orig_image'] && file_exists(DIR_IMAGE . $card_info['orig_image'])) {
			$this->data['thumb'] = $this->model_tool_image->resize($card_info['orig_image'], 100, 100);
		} else {
			$this->data['thumb'] = $this->model_tool_image->resize('no_image.jpg', 100, 100);
		}

		$this->data['no_image'] = $this->model_tool_image->resize('no_image.jpg', 100, 100);

		$cards = $this->model_catalog_card->getCards();

		// Remove own id from list
		if (!empty($card_info)) {
			foreach ($cards as $key => $card) {
				if ($card['card_id'] == $card_info['card_id']) {
					unset($cards[$key]);
				}
			}
		}

		$this->data['cards'] = $cards;	   
				
		if (isset($this->request->post['sort_order'])) {
			$this->data['sort_order'] = $this->request->post['sort_order'];
		} elseif (!empty($card_info)) {
			$this->data['sort_order'] = $card_info['sort_order'];
		} else {
			$this->data['sort_order'] = 0;
		}
		
		if (isset($this->request->post['status'])) {
			$this->data['status'] = $this->request->post['status'];
		} elseif (!empty($card_info)) {
			$this->data['status'] = $card_info['status'];
		} else {
			$this->data['status'] = 0;
		}
        
        $this->load->model('catalog/card_category');

        $this->data['card_categories'] = array();

        
        $this->data['card_categories'] = $this->model_catalog_card_category->getCardCategories();
        //var_dump($this->data['tags']);

        if (isset($this->request->post['the_card_category'])) {
			$this->data['the_card_category'] = $this->request->post['the_card_category'];
		} elseif (isset($this->request->get['card_id'])) {
			$this->data['the_card_category'] = $this->model_catalog_card->getCardCategories($this->request->get['card_id']);
		} else {
			$this->data['the_card_category'] = array();
		}		
        
        
		$this->template = 'catalog/card_form.tpl';
		$this->children = array(
			'common/header',
			'common/footer'
		);
				
		$this->response->setOutput($this->render());
	}

	private function validateForm() {
		if (!$this->user->hasPermission('modify', 'catalog/card')) {
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
		if (!$this->user->hasPermission('modify', 'catalog/card')) {
			$this->error['warning'] = $this->language->get('error_permission');
		}
 
		if (!$this->error) {
			return true; 
		} else {
			return false;
		}
	}

    public static function getCardThemeImage($theme_id) {
        if ($theme_id <= 1 || $theme_id > 5)
            $theme_id = 1;
        
        $theme_list = array( "1"=>"web/top_01-56223260f1b0d2f30313ff7e545e300e.jpg",
                             "2"=>"web/top_02-581895bc9284b4c05ae684bc894dccdb.jpg",
                             "3"=>"web/top_03-fd14c5b5300fe2d1bb527b65980c8559.jpg",
                             "4"=>"web/top_04-7a605783321004d331c2f840a472058f.jpg",
                             "5"=>"web/top_05-02d76f78b2e562697dc758fea81cb118.jpg"
                             );
        return $theme_list[$theme_id];

    }
}
?>