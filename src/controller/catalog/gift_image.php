<?php 
class ControllerCatalogGiftImage extends Controller { 
	private $error = array();
 
	public function index() {
		$this->load->language('catalog/gift_image');

		$this->document->setTitle($this->language->get('heading_title'));
		
		$this->load->model('catalog/gift_image');
		 
		$this->getList();
	}

	public function insert() {
		$this->load->language('catalog/gift_image');

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
		$this->load->language('catalog/gift_image');

		$this->document->setTitle($this->language->get('heading_title'));
		
		$this->load->model('catalog/gift_image');
		
		if (($this->request->server['REQUEST_METHOD'] == 'POST') && $this->validateForm()) {
			$this->model_catalog_gift_image->edit($this->request->get['image_id'], $this->request->post);
			
			$this->session->data['success'] = $this->language->get('text_success');
			
			$this->redirect($this->url->link('catalog/gift_image', 'token=' . $this->session->data['token'], 'SSL'));
		}

		$this->getForm();
	}

	public function delete() {
		$this->load->language('catalog/gift_image');

		$this->document->setTitle($this->language->get('heading_title'));
		
		$this->load->model('catalog/gift_image');
		
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
		$url = '';
        
		if (isset($this->request->get['filter_name'])) {
			$url .= '&filter_name=' . $this->request->get['filter_name'];
		}

		if (isset($this->request->get['filter_category'])) {
			$url .= '&filter_category=' . $this->request->get['filter_category'];
		}

		if (isset($this->request->get['sort'])) {
			$url .= '&sort=' . $this->request->get['sort'];
		}

		if (isset($this->request->get['order'])) {
			$url .= '&order=' . $this->request->get['order'];
		}
        
        return $url;
    }

	private function getList() {
        $this->config->set('config_admin_limit', 10);

		if (isset($this->request->get['filter_name'])) {
			$filter_name = $this->request->get['filter_name'];
		} else {
			$filter_name = null;
		}

        $this->data['filter_name'] = $filter_name;

		if (isset($this->request->get['filter_category'])) {
			$filter_category = $this->request->get['filter_category'];
		} else {
			$filter_category = null;
		}

        $this->data['filter_category'] = $filter_category;

        // prepare url
		if (isset($this->request->get['sort'])) {
			$sort = $this->request->get['sort'];
		} else {
			$sort = 'p.product_id';
		}
		
		if (isset($this->request->get['order'])) {
			$order = $this->request->get['order'];
		} else {
            if ($sort == 'p.product_id')
                $order = 'DESC';
            else
                $order = "ASC";
		}

		if (isset($this->request->get['page'])) {
			$page = $this->request->get['page'];
		} else {
			$page = 1;
		}

        $url = $this->generateURL();
		
		if (isset($this->request->get['page'])) {
			$url .= '&page=' . $this->request->get['page'];
		}

   		$this->data['breadcrumbs'] = array();

   		$this->data['breadcrumbs'][] = array(
       		'text'      => $this->language->get('text_home'),
			'href'      => $this->url->link('common/home', 'token=' . $this->session->data['token'], 'SSL'),
      		'separator' => false
   		);

   		$this->data['breadcrumbs'][] = array(
       		'text'      => $this->language->get('heading_title'),
			'href'      => $this->url->link('catalog/gift_image', 'token=' . $this->session->data['token'] . $url, 'SSL'),
      		'separator' => ' :: '
   		);
									
		$this->data['insert'] = $this->url->link('catalog/gift_image/insert', 'null' . $url, 'SSL');
		$this->data['delete'] = $this->url->link('catalog/gift_image/delete', 'null' . $url, 'SSL');

        // query params
		$query_params = array(
                              'filter_name' => $filter_name, 
                              'filter_category' => $filter_category,
                              'sort' => $sort,
                              'order' => $order,
                              'start' => ($page-1) * $this->config->get('config_admin_limit'),
                              'limit' => $this->config->get('config_admin_limit')
                              );
        

        $t1 = microtime_float();

 		$results = $this->model_catalog_gift_image->getGiftImages($query_params);
        $t2 = microtime_float();
        error_log("time 1: " . ($t2 - $t1));

		$this->data['gift_images'] = array();

		$this->load->model('tool/image');
 
		foreach ($results as $result) {
			$action = array();
			
			$action[] = array(
				'text' => $this->language->get('text_edit'),
				'href' => $this->url->link('catalog/gift_image/update', 'image_id=' . $result['image_id'] . $url, 'SSL')
			);

            $this->data['gift_images'][] = array_merge(
                $result, 
                array('selected' => isset($this->request->post['selected']) && in_array($result['image_id'], $this->request->post['selected']), 
                      'action' => $action));
		}

        $this->load->model('catalog/gift_image_category');
		$this->data['gift_image_categories'] = $this->model_catalog_gift_image_category->getCategories();

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

        $url = $this->generateURL();

		$pagination = new Pagination();
		$pagination->total = $this->model_catalog_gift_image->getTotalGiftImages($query_params);
		$pagination->page = $page;
		$pagination->limit = $this->config->get('config_admin_limit');
		$pagination->text = $this->language->get('text_pagination');
		$pagination->url = $this->url->link('catalog/gift_image', $url . '&page={page}', 'SSL');
			
		$this->data['pagination'] = $pagination->render();
		
		$this->template = 'catalog/gift_image_list.tpl';
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
			'href'      => $this->url->link('catalog/gift_image', '', 'SSL'),
      		'separator' => ' :: '
   		);
		
		if (!isset($this->request->get['image_id'])) {
			$this->data['action'] = $this->url->link('catalog/gift_image/insert', 'token=' . $this->session->data['token'], 'SSL');
		} else {
			$this->data['action'] = $this->url->link('catalog/gift_image/update', 'token=' . $this->session->data['token'] . '&image_id=' . $this->request->get['image_id'], 'SSL');
		}
		
		$this->data['cancel'] = $this->url->link('catalog/gift_image', 'token=' . $this->session->data['token'], 'SSL');

		$this->data['token'] = $this->session->data['token'];

		if (isset($this->request->get['image_id']) && ($this->request->server['REQUEST_METHOD'] != 'POST')) {
      		$gift_image_info = $this->model_catalog_gift_image->getGiftImage($this->request->get['image_id']);
    	}
		
		$this->load->model('localisation/language');
		
		$this->data['languages'] = $this->model_localisation_language->getLanguages();

        if (!empty($gift_image_info)) {
			$this->data['name'] = $gift_image_info['name'];
		} else {
			$this->data['name'] = "";
		}
        
		if (!empty($gift_image_info)) {
			$this->data['image'] = $gift_image_info['image'];
		} else {
			$this->data['image'] = '';
		}

		if (!empty($gift_image_info)) {
			$this->data['animate'] = $gift_image_info['animate'];
		} else {
			$this->data['animate'] = '';
		}

		if (!empty($gift_image_info)) {
			$this->data['wish'] = $gift_image_info['wish'];
		} else {
			$this->data['wish'] = "";
		}

        /*
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
		*/
				
		if (!empty($gift_image_info)) {
			$this->data['score'] = $gift_image_info['score'];
		} else {
			$this->data['score'] = 0;
		}
        
		$this->load->model('catalog/tag');
				
		$this->data['tags'] = $this->model_catalog_tag->getTags();
		
		if (!empty($gift_image_info)) {
			$this->data['product_tag'] = $this->model_catalog_gift_image->getProductTags($gift_image_info['image_id']);
		} else {
			$this->data['product_tag'] = array();
		}		

		$this->load->model('catalog/gift_image_category');
				
		$this->data['gift_image_categories'] = $this->model_catalog_gift_image_category->getCategories();
		
		if (!empty($gift_image_info)) {
			$this->data['gift_image_category'] = $this->model_catalog_gift_image->getGiftImageCategories($gift_image_info['image_id']);
		} else {
			$this->data['gift_image_category'] = array();
		}		

		$this->template = 'catalog/gift_image_form.tpl';
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