<?php 
class ControllerCatalogLotto extends Controller {
	private $error = array(); 
     
  	public function index() {

		$this->load->language('catalog/lotto');
    	
		$this->document->setTitle($this->language->get('heading_title')); 
		
		$this->load->model('catalog/lotto');
		
		$this->getList();
  	}
  
  	public function insert() {
    	$this->load->language('catalog/lotto');

    	$this->document->setTitle($this->language->get('heading_title')); 
		
		$this->load->model('catalog/lotto');
		
    	if (($this->request->server['REQUEST_METHOD'] == 'POST') && $this->validateForm()) {
			$this->model_catalog_lotto->addLotto($this->request->post);
	  		
			$this->session->data['success'] = $this->language->get('text_success');
	  
			$url = '';
			
			if (isset($this->request->get['filter_name'])) {
				$url .= '&filter_name=' . $this->request->get['filter_name'];
			}

			if (isset($this->request->get['filter_short_track_code'])) {
				$url .= '&filter_short_track_code=' . $this->request->get['filter_short_track_code'];
			}
		
			if (isset($this->request->get['filter_model'])) {
				$url .= '&filter_model=' . $this->request->get['filter_model'];
			}
			
			if (isset($this->request->get['filter_price'])) {
				$url .= '&filter_price=' . $this->request->get['filter_price'];
			}
			
			if (isset($this->request->get['filter_quantity'])) {
				$url .= '&filter_quantity=' . $this->request->get['filter_quantity'];
			}
			
			if (isset($this->request->get['filter_status'])) {
				$url .= '&filter_status=' . $this->request->get['filter_status'];
			}
					
			if (isset($this->request->get['sort'])) {
				$url .= '&sort=' . $this->request->get['sort'];
			}

			if (isset($this->request->get['order'])) {
				$url .= '&order=' . $this->request->get['order'];
			}

			if (isset($this->request->get['page'])) {
				$url .= '&page=' . $this->request->get['page'];
			}
			
			$this->redirect($this->url->link('catalog/lotto', 'token=' . $this->session->data['token'] . $url, 'SSL'));
    	}
	
    	$this->getForm();
  	}

  	public function update() {
    	$this->load->language('catalog/lotto');

    	$this->document->setTitle($this->language->get('heading_title'));
		
		$this->load->model('catalog/lotto');
	
    	if (($this->request->server['REQUEST_METHOD'] == 'POST') && $this->validateForm()) {

			$this->model_catalog_lotto->editLotto($this->request->get['lotto_id'], $this->request->post);
			
			$this->session->data['success'] = $this->language->get('text_success');
			
			$url = '';
			
			if (isset($this->request->get['filter_name'])) {
				$url .= '&filter_name=' . $this->request->get['filter_name'];
			}
		
			if (isset($this->request->get['filter_short_track_code'])) {
				$url .= '&filter_short_track_code=' . $this->request->get['filter_short_track_code'];
			}

			if (isset($this->request->get['filter_model'])) {
				$url .= '&filter_model=' . $this->request->get['filter_model'];
			}
			
			if (isset($this->request->get['filter_price'])) {
				$url .= '&filter_price=' . $this->request->get['filter_price'];
			}
			
			if (isset($this->request->get['filter_quantity'])) {
				$url .= '&filter_quantity=' . $this->request->get['filter_quantity'];
			}	
		
			if (isset($this->request->get['filter_status'])) {
				$url .= '&filter_status=' . $this->request->get['filter_status'];
			}
					
			if (isset($this->request->get['sort'])) {
				$url .= '&sort=' . $this->request->get['sort'];
			}

			if (isset($this->request->get['order'])) {
				$url .= '&order=' . $this->request->get['order'];
			}

			if (isset($this->request->get['page'])) {
				$url .= '&page=' . $this->request->get['page'];
			}
			
			$this->redirect($this->url->link('catalog/lotto/update', 'token=' . $this->session->data['token'] . $url . '&lotto_id=' . $this->request->get['lotto_id'], 'SSL'));
		}

    	$this->getForm();
  	}

  	public function delete() {

    	$this->load->language('catalog/lotto');

    	$this->document->setTitle($this->language->get('heading_title'));
		
		$this->load->model('catalog/lotto');
		
		if (isset($this->request->post['selected']) && $this->validateDelete()) {
			foreach ($this->request->post['selected'] as $lotto_id) {
				$this->model_catalog_lotto->deleteLotto($lotto_id);
	  		}

			$this->session->data['success'] = $this->language->get('text_success');
			
			$url = '';
			
			if (isset($this->request->get['filter_name'])) {
				$url .= '&filter_name=' . $this->request->get['filter_name'];
			}
		
			if (isset($this->request->get['filter_short_track_code'])) {
				$url .= '&filter_short_track_code=' . $this->request->get['filter_short_track_code'];
			}

			if (isset($this->request->get['filter_model'])) {
				$url .= '&filter_model=' . $this->request->get['filter_model'];
			}
			
			if (isset($this->request->get['filter_price'])) {
				$url .= '&filter_price=' . $this->request->get['filter_price'];
			}
			
			if (isset($this->request->get['filter_quantity'])) {
				$url .= '&filter_quantity=' . $this->request->get['filter_quantity'];
			}	
		
			if (isset($this->request->get['filter_status'])) {
				$url .= '&filter_status=' . $this->request->get['filter_status'];
			}
					
			if (isset($this->request->get['sort'])) {
				$url .= '&sort=' . $this->request->get['sort'];
			}

			if (isset($this->request->get['order'])) {
				$url .= '&order=' . $this->request->get['order'];
			}

			if (isset($this->request->get['page'])) {
				$url .= '&page=' . $this->request->get['page'];
			}
			
			$this->redirect($this->url->link('catalog/lotto', 'token=' . $this->session->data['token'] . $url, 'SSL'));
		}

    	$this->getList();
  	}

  	public function copy() {
    	$this->load->language('catalog/lotto');

    	$this->document->setTitle($this->language->get('heading_title'));
		
		$this->load->model('catalog/lotto');
		
		if (isset($this->request->post['selected']) && $this->validateCopy()) {
			foreach ($this->request->post['selected'] as $lotto_id) {
				$this->model_catalog_lotto->copyLotto($lotto_id);
	  		}

			$this->session->data['success'] = $this->language->get('text_success');
			
			$url = '';
			
			if (isset($this->request->get['filter_name'])) {
				$url .= '&filter_name=' . $this->request->get['filter_name'];
			}
		
			if (isset($this->request->get['filter_model'])) {
				$url .= '&filter_model=' . $this->request->get['filter_model'];
			}
			
			if (isset($this->request->get['filter_price'])) {
				$url .= '&filter_price=' . $this->request->get['filter_price'];
			}
			
			if (isset($this->request->get['filter_quantity'])) {
				$url .= '&filter_quantity=' . $this->request->get['filter_quantity'];
			}	
		
			if (isset($this->request->get['filter_status'])) {
				$url .= '&filter_status=' . $this->request->get['filter_status'];
			}
					
			if (isset($this->request->get['sort'])) {
				$url .= '&sort=' . $this->request->get['sort'];
			}

			if (isset($this->request->get['order'])) {
				$url .= '&order=' . $this->request->get['order'];
			}

			if (isset($this->request->get['page'])) {
				$url .= '&page=' . $this->request->get['page'];
			}
			
			$this->redirect($this->url->link('catalog/lotto', 'token=' . $this->session->data['token'] . $url, 'SSL'));
		}

    	$this->getList();
  	}
	
  	private function getList() {				
		if (isset($this->request->get['filter_name'])) {
			$filter_name = $this->request->get['filter_name'];
		} else {
			$filter_name = null;
		}

		if (isset($this->request->get['filter_short_track_code'])) {
			$filter_short_track_code = $this->request->get['filter_short_track_code'];
		} else {
			$filter_short_track_code = null;
		}

		if (isset($this->request->get['filter_model'])) {
			$filter_model = $this->request->get['filter_model'];
		} else {
			$filter_model = null;
		}
		
		if (isset($this->request->get['filter_price'])) {
			$filter_price = $this->request->get['filter_price'];
		} else {
			$filter_price = null;
		}

		if (isset($this->request->get['filter_quantity'])) {
			$filter_quantity = $this->request->get['filter_quantity'];
		} else {
			$filter_quantity = null;
		}

		if (isset($this->request->get['filter_status'])) {
			$filter_status = $this->request->get['filter_status'];
		} else {
			$filter_status = null;
		}

		if (isset($this->request->get['sort'])) {
			$sort = $this->request->get['sort'];
		} else {
			$sort = 'p.lotto_id';
		}
		
		if (isset($this->request->get['order'])) {
			$order = $this->request->get['order'];
		} else {
            if ($sort == 'p.lotto_id')
                $order = 'DESC';
            else
                $order = "ASC";
		}
		
		if (isset($this->request->get['page'])) {
			$page = $this->request->get['page'];
		} else {
			$page = 1;
		}
						
		$url = '';
						
		if (isset($this->request->get['filter_name'])) {
			$url .= '&filter_name=' . $this->request->get['filter_name'];
		}

		
		if (isset($this->request->get['filter_short_track_code'])) {
			$url .= '&filter_short_track_code=' . $this->request->get['filter_short_track_code'];
		}
		
		if (isset($this->request->get['filter_model'])) {
			$url .= '&filter_model=' . $this->request->get['filter_model'];
		}
		
		if (isset($this->request->get['filter_price'])) {
			$url .= '&filter_price=' . $this->request->get['filter_price'];
		}
		
		if (isset($this->request->get['filter_quantity'])) {
			$url .= '&filter_quantity=' . $this->request->get['filter_quantity'];
		}		

		if (isset($this->request->get['filter_status'])) {
			$url .= '&filter_status=' . $this->request->get['filter_status'];
		}
						
		if (isset($this->request->get['sort'])) {
			$url .= '&sort=' . $this->request->get['sort'];
		}

		if (isset($this->request->get['order'])) {
			$url .= '&order=' . $this->request->get['order'];
		}
		
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
			'href'      => $this->url->link('catalog/lotto', 'token=' . $this->session->data['token'] . $url, 'SSL'),       		
      		'separator' => ' :: '
   		);
		
		$this->data['insert'] = $this->url->link('catalog/lotto/insert', 'token=' . $this->session->data['token'] . $url, 'SSL');
		$this->data['copy'] = $this->url->link('catalog/lotto/copy', 'token=' . $this->session->data['token'] . $url, 'SSL');	
		$this->data['delete'] = $this->url->link('catalog/lotto/delete', 'token=' . $this->session->data['token'] . $url, 'SSL');
    	
		$this->data['lottos'] = array();
        

		$data = array(
			'filter_name'	  => $filter_name, 
            'filter_short_track_code' => $filter_short_track_code, 
			'filter_model'	  => $filter_model,
			'filter_price'	  => $filter_price,
			'filter_quantity' => $filter_quantity,
			'filter_status'   => $filter_status,
			'sort'            => $sort,
			'order'           => $order,
			'start'           => ($page - 1) * $this->config->get('config_admin_limit'),
			'limit'           => $this->config->get('config_admin_limit')
		);
		
		$this->load->model('tool/image');
		
		$lotto_total = $this->model_catalog_lotto->getTotalLottos($data);
			
		$results = $this->model_catalog_lotto->getLottos($data);
				    	
		foreach ($results as $result) {
			$action = array();
			
			$action[] = array(
				'text' => $this->language->get('text_edit'),
				'href' => $this->url->link('catalog/lotto/update', 'token=' . $this->session->data['token'] . '&lotto_id=' . $result['lotto_id'] . $url, 'SSL')
			);

			if ($result['image'] && file_exists(DIR_IMAGE . $result['image'])) {
				$image = $this->model_tool_image->resize($result['image'], 40, 40);
			} else {
				$image = $this->model_tool_image->resize('no_image.jpg', 40, 40);
			}
	
			$special = false;
			
			$lotto_specials = $this->model_catalog_lotto->getLottoSpecials($result['lotto_id']);
			
			foreach ($lotto_specials  as $lotto_special) {
				if (($lotto_special['date_start'] == '0000-00-00' || $lotto_special['date_start'] > date('Y-m-d')) && ($lotto_special['date_end'] == '0000-00-00' || $lotto_special['date_end'] < date('Y-m-d'))) {
					$special = $lotto_special['price'];
			
					break;
				}
			}
	
      		$this->data['lottos'][] = array(
				'lotto_id' => $result['lotto_id'],
				'name'       => $result['name'],
                'sexy_name'  => $result['sexy_name'],
                'manufacturer' => $result['manufacturer_name'],
				'model'      => $result['model'],
                //'category'   => $result['category_id'],
				'price'      => $result['price'],
                'base_price' => $result['base_price'],
				'special'    => $special,
				'image'      => $image,
				'quantity'   => $result['quantity'],
                'supplier'   => $result['supplier'],
                'date_modified' => $result['date_modified'],
				'status'     => $result['status'], //($result['status'] ? $this->language->get('text_enabled') : $this->language->get('text_disabled')),
				'selected'   => isset($this->request->post['selected']) && in_array($result['lotto_id'], $this->request->post['selected']),
                'short_track_code' => $result['short_track_code'],
				'action'     => $action
			);
            
    	}

		$this->data['heading_title'] = $this->language->get('heading_title');		
				
		$this->data['text_enabled'] = $this->language->get('text_enabled');		
		$this->data['text_disabled'] = $this->language->get('text_disabled');		
		$this->data['text_no_results'] = $this->language->get('text_no_results');		
		$this->data['text_image_manager'] = $this->language->get('text_image_manager');		
			
		$this->data['column_image'] = $this->language->get('column_image');		
		$this->data['column_name'] = $this->language->get('column_name');		
		$this->data['column_model'] = $this->language->get('column_model');		
		$this->data['column_category'] = $this->language->get('column_category');		
		$this->data['column_price'] = $this->language->get('column_price');		
		$this->data['column_quantity'] = $this->language->get('column_quantity');		
		$this->data['column_status'] = $this->language->get('column_status');		
		$this->data['column_action'] = $this->language->get('column_action');		
				
		$this->data['button_copy'] = $this->language->get('button_copy');		
		$this->data['button_insert'] = $this->language->get('button_insert');		
		$this->data['button_delete'] = $this->language->get('button_delete');		
		$this->data['button_filter'] = $this->language->get('button_filter');
		 
 		$this->data['token'] = $this->session->data['token'];
		
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

		$url = '';

		if (isset($this->request->get['filter_name'])) {
			$url .= '&filter_name=' . $this->request->get['filter_name'];
		}

		if (isset($this->request->get['filter_short_track_code'])) {
			$url .= '&filter_short_track_code=' . $this->request->get['filter_short_track_code'];
		}
		
		
		if (isset($this->request->get['filter_model'])) {
			$url .= '&filter_model=' . $this->request->get['filter_model'];
		}
		
		if (isset($this->request->get['filter_price'])) {
			$url .= '&filter_price=' . $this->request->get['filter_price'];
		}
		
		if (isset($this->request->get['filter_quantity'])) {
			$url .= '&filter_quantity=' . $this->request->get['filter_quantity'];
		}
		
		if (isset($this->request->get['filter_status'])) {
			$url .= '&filter_status=' . $this->request->get['filter_status'];
		}
								
		if ($order == 'ASC') {
			$url .= '&order=DESC';
		} else {
			$url .= '&order=ASC';
		}

		if (isset($this->request->get['page'])) {
			$url .= '&page=' . $this->request->get['page'];
		}

		$this->data['sort_id'] = $this->url->link('catalog/lotto', 'token=' . $this->session->data['token'] . '&sort=p.lotto_id' . $url, 'SSL');
		$this->data['sort_name'] = $this->url->link('catalog/lotto', 'token=' . $this->session->data['token'] . '&sort=pd.name' . $url, 'SSL');
		$this->data['sort_model'] = $this->url->link('catalog/lotto', 'token=' . $this->session->data['token'] . '&sort=p.model' . $url, 'SSL');
		$this->data['sort_category'] = $this->url->link('catalog/lotto', 'token=' . $this->session->data['token'] . '&sort=p.category' . $url, 'SSL');
		$this->data['sort_price'] = $this->url->link('catalog/lotto', 'token=' . $this->session->data['token'] . '&sort=p.price' . $url, 'SSL');
        /*		$this->data['sort_quantity'] = $this->url->link('catalog/lotto', 'token=' . $this->session->data['token'] . '&sort=p.quantity' . $url, 'SSL');*/
		$this->data['sort_supplier'] = $this->url->link('catalog/lotto', 'token=' . $this->session->data['token'] . '&sort=s.name' . $url, 'SSL');
		$this->data['sort_status'] = $this->url->link('catalog/lotto', 'token=' . $this->session->data['token'] . '&sort=p.status' . $url, 'SSL');
		$this->data['sort_order'] = $this->url->link('catalog/lotto', 'token=' . $this->session->data['token'] . '&sort=p.sort_order' . $url, 'SSL');
		$this->data['sort_date_modified'] = $this->url->link('catalog/lotto', 'token=' . $this->session->data['token'] . '&sort=p.date_modified' . $url, 'SSL');
		
		$url = '';

		if (isset($this->request->get['filter_name'])) {
			$url .= '&filter_name=' . $this->request->get['filter_name'];
		}

		if (isset($this->request->get['filter_short_track_code'])) {
			$url .= '&filter_short_track_code=' . $this->request->get['filter_short_track_code'];
		}
		
		
		if (isset($this->request->get['filter_model'])) {
			$url .= '&filter_model=' . $this->request->get['filter_model'];
		}
		
		if (isset($this->request->get['filter_price'])) {
			$url .= '&filter_price=' . $this->request->get['filter_price'];
		}
		
		if (isset($this->request->get['filter_quantity'])) {
			$url .= '&filter_quantity=' . $this->request->get['filter_quantity'];
		}

		if (isset($this->request->get['filter_status'])) {
			$url .= '&filter_status=' . $this->request->get['filter_status'];
		}

		if (isset($this->request->get['sort'])) {
			$url .= '&sort=' . $this->request->get['sort'];
		}
												
		if (isset($this->request->get['order'])) {
			$url .= '&order=' . $this->request->get['order'];
		}
				
		$pagination = new Pagination();
		$pagination->total = $lotto_total;
		$pagination->page = $page;
		$pagination->limit = $this->config->get('config_admin_limit');
		$pagination->text = $this->language->get('text_pagination');
		$pagination->url = $this->url->link('catalog/lotto', 'token=' . $this->session->data['token'] . $url . '&page={page}', 'SSL');
			
		$this->data['pagination'] = $pagination->render();
	
		$this->data['filter_name'] = $filter_name;
        $this->data['filter_short_track_code'] = $filter_short_track_code;
		$this->data['filter_model'] = $filter_model;
		$this->data['filter_price'] = $filter_price;
		$this->data['filter_quantity'] = $filter_quantity;
		$this->data['filter_status'] = $filter_status;
		
		$this->data['sort'] = $sort;
		$this->data['order'] = $order;

		$this->template = 'catalog/lotto_list.tpl';
		$this->children = array(
			'common/header',
			'common/footer'
		);
				
		$this->response->setOutput($this->render());
  	}

  	private function getForm() {
    	$this->data['heading_title'] = $this->language->get('heading_title');
 
    	$this->data['text_enabled'] = $this->language->get('text_enabled');
    	$this->data['text_disabled'] = $this->language->get('text_disabled');
    	$this->data['text_none'] = $this->language->get('text_none');
    	$this->data['text_yes'] = $this->language->get('text_yes');
    	$this->data['text_no'] = $this->language->get('text_no');
		$this->data['text_select_all'] = $this->language->get('text_select_all');
		$this->data['text_unselect_all'] = $this->language->get('text_unselect_all');
		$this->data['text_plus'] = $this->language->get('text_plus');
		$this->data['text_minus'] = $this->language->get('text_minus');
		$this->data['text_default'] = $this->language->get('text_default');
		$this->data['text_image_manager'] = $this->language->get('text_image_manager');
		$this->data['text_browse'] = $this->language->get('text_browse');
		$this->data['text_clear'] = $this->language->get('text_clear');
		$this->data['text_option'] = $this->language->get('text_option');
		$this->data['text_option_value'] = $this->language->get('text_option_value');
		$this->data['text_select'] = $this->language->get('text_select');
		$this->data['text_none'] = $this->language->get('text_none');
		$this->data['text_percent'] = $this->language->get('text_percent');
		$this->data['text_amount'] = $this->language->get('text_amount');

		$this->data['entry_name'] = $this->language->get('entry_name');
		$this->data['entry_meta_description'] = $this->language->get('entry_meta_description');
		$this->data['entry_meta_keyword'] = $this->language->get('entry_meta_keyword');
		$this->data['entry_description'] = $this->language->get('entry_description');
		$this->data['entry_review'] = $this->language->get('entry_review');
        //error_log("entry_review1=" . $this->language->get('entry_review'));

		$this->data['entry_recommend_reason'] = $this->language->get('entry_recommend_reason');
		$this->data['entry_store'] = $this->language->get('entry_store');
		$this->data['entry_keyword'] = $this->language->get('entry_keyword');
    	$this->data['entry_model'] = $this->language->get('entry_model');
		$this->data['entry_sku'] = $this->language->get('entry_sku');
		$this->data['entry_upc'] = $this->language->get('entry_upc');
		$this->data['entry_location'] = $this->language->get('entry_location');
		$this->data['entry_minimum'] = $this->language->get('entry_minimum');
		$this->data['entry_manufacturer'] = $this->language->get('entry_manufacturer');
    	$this->data['entry_shipping'] = $this->language->get('entry_shipping');
    	$this->data['entry_date_available'] = $this->language->get('entry_date_available');
    	$this->data['entry_quantity'] = $this->language->get('entry_quantity');
		$this->data['entry_stock_status'] = $this->language->get('entry_stock_status');
    	$this->data['entry_price'] = $this->language->get('entry_price');
		$this->data['entry_tax_class'] = $this->language->get('entry_tax_class');
		$this->data['entry_points'] = $this->language->get('entry_points');
		$this->data['entry_option_points'] = $this->language->get('entry_option_points');
		$this->data['entry_subtract'] = $this->language->get('entry_subtract');
    	$this->data['entry_weight_class'] = $this->language->get('entry_weight_class');
    	$this->data['entry_weight'] = $this->language->get('entry_weight');
		$this->data['entry_dimension'] = $this->language->get('entry_dimension');
		$this->data['entry_length'] = $this->language->get('entry_length');
    	$this->data['entry_image'] = $this->language->get('entry_image');
    	$this->data['entry_download'] = $this->language->get('entry_download');
    	$this->data['entry_category'] = $this->language->get('entry_category');
    	$this->data['entry_tag'] = $this->language->get('entry_tag');
		$this->data['entry_related'] = $this->language->get('entry_related');
		$this->data['entry_attribute'] = $this->language->get('entry_attribute');
		$this->data['entry_text'] = $this->language->get('entry_text');
		$this->data['entry_option'] = $this->language->get('entry_option');
		$this->data['entry_option_value'] = $this->language->get('entry_option_value');
		$this->data['entry_required'] = $this->language->get('entry_required');
		$this->data['entry_sort_order'] = $this->language->get('entry_sort_order');
		$this->data['entry_status'] = $this->language->get('entry_status');
		$this->data['entry_customer_group'] = $this->language->get('entry_customer_group');
		$this->data['entry_date_start'] = $this->language->get('entry_date_start');
		$this->data['entry_date_end'] = $this->language->get('entry_date_end');
		$this->data['entry_priority'] = $this->language->get('entry_priority');
		//$this->data['entry_tag'] = $this->language->get('entry_tag');
		$this->data['entry_customer_group'] = $this->language->get('entry_customer_group');
		$this->data['entry_reward'] = $this->language->get('entry_reward');
		$this->data['entry_layout'] = $this->language->get('entry_layout');
				
    	$this->data['button_save'] = $this->language->get('button_save');
    	$this->data['button_cancel'] = $this->language->get('button_cancel');
		$this->data['button_add_attribute'] = $this->language->get('button_add_attribute');
		$this->data['button_add_option'] = $this->language->get('button_add_option');
		$this->data['button_add_option_value'] = $this->language->get('button_add_option_value');
		$this->data['button_add_discount'] = $this->language->get('button_add_discount');
		$this->data['button_add_special'] = $this->language->get('button_add_special');
		$this->data['button_add_image'] = $this->language->get('button_add_image');
		$this->data['button_remove'] = $this->language->get('button_remove');
		
    	$this->data['tab_general'] = $this->language->get('tab_general');
    	$this->data['tab_data'] = $this->language->get('tab_data');
		$this->data['tab_attribute'] = $this->language->get('tab_attribute');
		$this->data['tab_option'] = $this->language->get('tab_option');		
		$this->data['tab_discount'] = $this->language->get('tab_discount');
		$this->data['tab_special'] = $this->language->get('tab_special');
    	$this->data['tab_image'] = $this->language->get('tab_image');		
		$this->data['tab_links'] = $this->language->get('tab_links');
		$this->data['tab_reward'] = $this->language->get('tab_reward');
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

 		if (isset($this->error['meta_description'])) {
			$this->data['error_meta_description'] = $this->error['meta_description'];
		} else {
			$this->data['error_meta_description'] = array();
		}		
   
   		if (isset($this->error['description'])) {
			$this->data['error_description'] = $this->error['description'];
		} else {
			$this->data['error_description'] = array();
		}	
		
   		if (isset($this->error['model'])) {
			$this->data['error_model'] = $this->error['model'];
		} else {
			$this->data['error_model'] = '';
		}		
     	
		if (isset($this->error['date_available'])) {
			$this->data['error_date_available'] = $this->error['date_available'];
		} else {
			$this->data['error_date_available'] = '';
		}	

		$url = '';

		if (isset($this->request->get['filter_name'])) {
			$url .= '&filter_name=' . $this->request->get['filter_name'];
		}

		if (isset($this->request->get['filter_short_track_code'])) {
			$url .= '&filter_short_track_code=' . $this->request->get['filter_short_track_code'];
		}
		
		
		if (isset($this->request->get['filter_model'])) {
			$url .= '&filter_model=' . $this->request->get['filter_model'];
		}
		
		if (isset($this->request->get['filter_price'])) {
			$url .= '&filter_price=' . $this->request->get['filter_price'];
		}
		
		if (isset($this->request->get['filter_quantity'])) {
			$url .= '&filter_quantity=' . $this->request->get['filter_quantity'];
		}	
		
		if (isset($this->request->get['filter_status'])) {
			$url .= '&filter_status=' . $this->request->get['filter_status'];
		}
								
		if (isset($this->request->get['sort'])) {
			$url .= '&sort=' . $this->request->get['sort'];
		}

		if (isset($this->request->get['order'])) {
			$url .= '&order=' . $this->request->get['order'];
		}
		
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
			'href'      => $this->url->link('catalog/lotto', 'token=' . $this->session->data['token'] . $url, 'SSL'),
      		'separator' => ' :: '
   		);
									
		if (!isset($this->request->get['lotto_id'])) {
			$this->data['action'] = $this->url->link('catalog/lotto/insert', 'token=' . $this->session->data['token'] . $url, 'SSL');
		} else {
			$this->data['action'] = $this->url->link('catalog/lotto/update', 'token=' . $this->session->data['token'] . '&lotto_id=' . $this->request->get['lotto_id'] . $url, 'SSL');
		}
		
        
		$this->data['cancel'] = $this->url->link('catalog/lotto', 'token=' . $this->session->data['token'] . $url, 'SSL');

		$this->data['token'] = $this->session->data['token'];

		if (isset($this->request->get['lotto_id']) && ($this->request->server['REQUEST_METHOD'] != 'POST')) {
      		$lotto_info = $this->model_catalog_lotto->getLotto($this->request->get['lotto_id']);
    	}

		$this->load->model('localisation/language');
		
		$this->data['languages'] = $this->model_localisation_language->getLanguages();
		
		if (isset($this->request->post['lotto_description'])) {
			$this->data['lotto_description'] = $this->request->post['lotto_description'];
		} elseif (isset($this->request->get['lotto_id'])) {
			$this->data['lotto_description'] = $this->model_catalog_lotto->getLottoDescriptions($this->request->get['lotto_id']);
		} else {
			$this->data['lotto_description'] = array();
		}

		$this->load->model('catalog/supplier');
				
		$this->data['suppliers'] = $this->model_catalog_supplier->getSuppliers();

		if (isset($this->request->post['lotto_supplier'])) {
			$this->data['lotto_supplier'] = $this->request->post['lotto_supplier'];
		} elseif (isset($this->request->get['lotto_id'])) {
			$this->data['lotto_supplier'] = $this->model_catalog_lotto->getLottoSupplier($this->request->get['lotto_id']);
            if (!$this->data['lotto_supplier']) {
                $this->data['lotto_supplier'] = array(
                                                        'supplier_lotto_url' => '',
                                                        'supplier_lotto_price' => 0,
                                                        'quantity' => 0,
                                                        'hold_quantity' => 0,
                                                        'ordered_quantity' => 0);
                
            }
		} else {
			$this->data['lotto_supplier'] = array(
                                                    'supplier_lotto_url' => '',
                                                    'supplier_lotto_price' => 0,
                                                    'quantity' => 0,
                                                    'hold_quantity' => 0,
                                                    'ordered_quantity' => 0);
		}
		
		if (isset($this->request->post['model'])) {
      		$this->data['model'] = $this->request->post['model'];
    	} elseif (!empty($lotto_info)) {
			$this->data['model'] = $lotto_info['model'];
		} else {
      		$this->data['model'] = '';
    	}

		if (isset($this->request->post['sku'])) {
      		$this->data['sku'] = $this->request->post['sku'];
    	} elseif (!empty($lotto_info)) {
			$this->data['sku'] = $lotto_info['sku'];
		} else {
      		$this->data['sku'] = '';
    	}
		
		if (isset($this->request->post['upc'])) {
      		$this->data['upc'] = $this->request->post['upc'];
    	} elseif (!empty($lotto_info)) {
			$this->data['upc'] = $lotto_info['upc'];
		} else {
      		$this->data['upc'] = '';
    	}
				
		if (isset($this->request->post['location'])) {
      		$this->data['location'] = $this->request->post['location'];
    	} elseif (!empty($lotto_info)) {
			$this->data['location'] = $lotto_info['location'];
		} else {
      		$this->data['location'] = '';
    	}

		$this->load->model('setting/store');
		
		$this->data['stores'] = $this->model_setting_store->getStores();
		
		if (isset($this->request->post['lotto_store'])) {
			$this->data['lotto_store'] = $this->request->post['lotto_store'];
		} elseif (isset($this->request->get['lotto_id'])) {
			$this->data['lotto_store'] = $this->model_catalog_lotto->getLottoStores($this->request->get['lotto_id']);
		} else {
			$this->data['lotto_store'] = array(0);
		}	
		
		if (isset($this->request->post['keyword'])) {
			$this->data['keyword'] = $this->request->post['keyword'];
		} elseif (!empty($lotto_info)) {
			$this->data['keyword'] = $lotto_info['keyword'];
		} else {
			$this->data['keyword'] = '';
		}


		if (isset($this->request->post['orig_image'])) {
			$this->data['orig_image'] = $this->request->post['orig_image'];
		} elseif (!empty($lotto_info)) {
			$this->data['orig_image'] = $lotto_info['orig_image'];
		} else {
			$this->data['orig_image'] = '';
		}
		
		$this->load->model('tool/image');
		
		if (!empty($lotto_info) && $lotto_info['orig_image'] && file_exists(DIR_IMAGE . $lotto_info['orig_image'])) {
			$this->data['thumb'] = $this->model_tool_image->resize($lotto_info['orig_image'], 100, 100);
		} else {
			$this->data['thumb'] = $this->model_tool_image->resize('no_image.jpg', 100, 100);
		}
	
		$this->load->model('catalog/manufacturer');
		
    	$this->data['manufacturers'] = $this->model_catalog_manufacturer->getManufacturers();

    	if (isset($this->request->post['manufacturer_id'])) {
      		$this->data['manufacturer_id'] = $this->request->post['manufacturer_id'];
		} elseif (!empty($lotto_info)) {
			$this->data['manufacturer_id'] = $lotto_info['manufacturer_id'];
		} else {
      		$this->data['manufacturer_id'] = 0;
    	} 
		
    	if (isset($this->request->post['shipping'])) {
      		$this->data['shipping'] = $this->request->post['shipping'];
    	} elseif (!empty($lotto_info)) {
      		$this->data['shipping'] = $lotto_info['shipping'];
    	} else {
			$this->data['shipping'] = 1;
		}
		
    	if (isset($this->request->post['price'])) {
      		$this->data['price'] = $this->request->post['price'];
    	} elseif (!empty($lotto_info)) {
			$this->data['price'] = $lotto_info['price'];
		} else {
      		$this->data['price'] = '';
    	}

    	if (isset($this->request->post['base_price'])) {
      		$this->data['base_price'] = $this->request->post['base_price'];
    	} elseif (!empty($lotto_info)) {
			$this->data['base_price'] = $lotto_info['base_price'];
		} else {
      		$this->data['base_price'] = '';
    	}
		
		$this->load->model('localisation/tax_class');
		
		$this->data['tax_classes'] = $this->model_localisation_tax_class->getTaxClasses();
    	
		if (isset($this->request->post['tax_class_id'])) {
      		$this->data['tax_class_id'] = $this->request->post['tax_class_id'];
    	} elseif (!empty($lotto_info)) {
			$this->data['tax_class_id'] = $lotto_info['tax_class_id'];
		} else {
      		$this->data['tax_class_id'] = 0;
    	}
		      	
		if (isset($this->request->post['date_available'])) {
       		$this->data['date_available'] = $this->request->post['date_available'];
		} elseif (!empty($lotto_info)) {
			$this->data['date_available'] = date('Y-m-d', strtotime($lotto_info['date_available']));
		} else {
			$this->data['date_available'] = date('Y-m-d', time() - 86400);
		}
        /*											
    	if (isset($this->request->post['quantity'])) {
      		$this->data['quantity'] = $this->request->post['quantity'];
    	} elseif (!empty($lotto_info)) {
      		$this->data['quantity'] = $lotto_info['quantity'];
    	} else {
			$this->data['quantity'] = 1;
		}
		*/

		if (isset($this->request->post['minimum'])) {
      		$this->data['minimum'] = $this->request->post['minimum'];
    	} elseif (!empty($lotto_info)) {
      		$this->data['minimum'] = $lotto_info['minimum'];
    	} else {
			$this->data['minimum'] = 1;
		}
		
		if (isset($this->request->post['subtract'])) {
      		$this->data['subtract'] = $this->request->post['subtract'];
    	} elseif (!empty($lotto_info)) {
      		$this->data['subtract'] = $lotto_info['subtract'];
    	} else {
			$this->data['subtract'] = 1;
		}
		
		if (isset($this->request->post['sort_order'])) {
      		$this->data['sort_order'] = $this->request->post['sort_order'];
    	} elseif (!empty($lotto_info)) {
      		$this->data['sort_order'] = $lotto_info['sort_order'];
    	} else {
			$this->data['sort_order'] = 1;
		}

		$this->load->model('localisation/stock_status');
		
		$this->data['stock_statuses'] = $this->model_localisation_stock_status->getStockStatuses();
    	
		if (isset($this->request->post['stock_status_id'])) {
      		$this->data['stock_status_id'] = $this->request->post['stock_status_id'];
    	} elseif (!empty($lotto_info)) {
      		$this->data['stock_status_id'] = $lotto_info['stock_status_id'];
    	} else {
			$this->data['stock_status_id'] = $this->config->get('config_stock_status_id');
		}
				
    	if (isset($this->request->post['status'])) {
      		$this->data['status'] = $this->request->post['status'];
    	} elseif (!empty($lotto_info)) {
			$this->data['status'] = $lotto_info['status'];
		} else {
      		$this->data['status'] = 0;
    	}

    	if (isset($this->request->post['weight'])) {
      		$this->data['weight'] = $this->request->post['weight'];
		} elseif (!empty($lotto_info)) {
			$this->data['weight'] = $lotto_info['weight'];
    	} else {
      		$this->data['weight'] = '';
    	} 
		
		$this->load->model('localisation/weight_class');
		
		$this->data['weight_classes'] = $this->model_localisation_weight_class->getWeightClasses();
    	
		if (isset($this->request->post['weight_class_id'])) {
      		$this->data['weight_class_id'] = $this->request->post['weight_class_id'];
    	} elseif (!empty($lotto_info)) {
      		$this->data['weight_class_id'] = $lotto_info['weight_class_id'];
		} else {
      		$this->data['weight_class_id'] = $this->config->get('config_weight_class_id');
    	}
		
		if (isset($this->request->post['length'])) {
      		$this->data['length'] = $this->request->post['length'];
    	} elseif (!empty($lotto_info)) {
			$this->data['length'] = $lotto_info['length'];
		} else {
      		$this->data['length'] = '';
    	}
		
		if (isset($this->request->post['width'])) {
      		$this->data['width'] = $this->request->post['width'];
		} elseif (!empty($lotto_info)) {	
			$this->data['width'] = $lotto_info['width'];
    	} else {
      		$this->data['width'] = '';
    	}
		
		if (isset($this->request->post['height'])) {
      		$this->data['height'] = $this->request->post['height'];
		} elseif (!empty($lotto_info)) {
			$this->data['height'] = $lotto_info['height'];
    	} else {
      		$this->data['height'] = '';
    	}

		$this->load->model('localisation/length_class');
		
		$this->data['length_classes'] = $this->model_localisation_length_class->getLengthClasses();
    	
		if (isset($this->request->post['length_class_id'])) {
      		$this->data['length_class_id'] = $this->request->post['length_class_id'];
    	} elseif (!empty($lotto_info)) {
      		$this->data['length_class_id'] = $lotto_info['length_class_id'];
    	} else {
      		$this->data['length_class_id'] = $this->config->get('config_length_class_id');
		}

		if (isset($this->request->post['lotto_attribute'])) {
			$this->data['lotto_attributes'] = $this->request->post['lotto_attribute'];
		} elseif (isset($this->request->get['lotto_id'])) {
			$this->data['lotto_attributes'] = $this->model_catalog_lotto->getLottoAttributes($this->request->get['lotto_id']);
		} else {
			$this->data['lotto_attributes'] = array();
		}
		
		$this->load->model('catalog/option');
		
		if (isset($this->request->post['lotto_option'])) {
			$lotto_options = $this->request->post['lotto_option'];
		} elseif (isset($this->request->get['lotto_id'])) {
			$lotto_options = $this->model_catalog_lotto->getLottoOptions($this->request->get['lotto_id']);			
		} else {
			$lotto_options = array();
		}			
		
		$this->data['lotto_options'] = array();
			
		foreach ($lotto_options as $lotto_option) {
			if ($lotto_option['type'] == 'select' || $lotto_option['type'] == 'radio' || $lotto_option['type'] == 'checkbox' || $lotto_option['type'] == 'image') {
				$lotto_option_value_data = array();
				
				foreach ($lotto_option['lotto_option_value'] as $lotto_option_value) {
					$lotto_option_value_data[] = array(
						'lotto_option_value_id' => $lotto_option_value['lotto_option_value_id'],
						'option_value_id'         => $lotto_option_value['option_value_id'],
						'quantity'                => $lotto_option_value['quantity'],
						'subtract'                => $lotto_option_value['subtract'],
						'price'                   => $lotto_option_value['price'],
						'price_prefix'            => $lotto_option_value['price_prefix'],
						'points'                  => $lotto_option_value['points'],
						'points_prefix'           => $lotto_option_value['points_prefix'],						
						'weight'                  => $lotto_option_value['weight'],
						'weight_prefix'           => $lotto_option_value['weight_prefix']	
					);						
				}
				
				$this->data['lotto_options'][] = array(
					'lotto_option_id'    => $lotto_option['lotto_option_id'],
					'lotto_option_value' => $lotto_option_value_data,
					'option_id'            => $lotto_option['option_id'],
					'name'                 => $lotto_option['name'],
					'type'                 => $lotto_option['type'],
					'required'             => $lotto_option['required']
				);				
			} else {
				$this->data['lotto_options'][] = array(
					'lotto_option_id' => $lotto_option['lotto_option_id'],
					'option_id'         => $lotto_option['option_id'],
					'name'              => $lotto_option['name'],
					'type'              => $lotto_option['type'],
					'option_value'      => $lotto_option['option_value'],
					'required'          => $lotto_option['required']
				);				
			}
		}
		
		$this->data['option_values'] = array();
		
		foreach ($lotto_options as $lotto_option) {
			if ($lotto_option['type'] == 'select' || $lotto_option['type'] == 'radio' || $lotto_option['type'] == 'checkbox' || $lotto_option['type'] == 'image') {
				if (!isset($this->data['option_values'][$lotto_option['option_id']])) {
					$this->data['option_values'][$lotto_option['option_id']] = $this->model_catalog_option->getOptionValues($lotto_option['option_id']);
				}
			}
		}
		
		$this->load->model('sale/customer_group');
		
		$this->data['customer_groups'] = $this->model_sale_customer_group->getCustomerGroups();
		
		if (isset($this->request->post['lotto_discount'])) {
			$this->data['lotto_discounts'] = $this->request->post['lotto_discount'];
		} elseif (isset($this->request->get['lotto_id'])) {
			$this->data['lotto_discounts'] = $this->model_catalog_lotto->getLottoDiscounts($this->request->get['lotto_id']);
		} else {
			$this->data['lotto_discounts'] = array();
		}

		if (isset($this->request->post['lotto_special'])) {
			$this->data['lotto_specials'] = $this->request->post['lotto_special'];
		} elseif (isset($this->request->get['lotto_id'])) {
			$this->data['lotto_specials'] = $this->model_catalog_lotto->getLottoSpecials($this->request->get['lotto_id']);
		} else {
			$this->data['lotto_specials'] = array();
		}
		
		if (isset($this->request->post['lotto_image'])) {
			$lotto_images = $this->request->post['lotto_image'];
		} elseif (isset($this->request->get['lotto_id'])) {
			$lotto_images = $this->model_catalog_lotto->getLottoImages($this->request->get['lotto_id']);
		} else {
			$lotto_images = array();
		}
		
		$this->data['lotto_images'] = array();
		
		foreach ($lotto_images as $lotto_image) {
			if ($lotto_image['orig_image'] && file_exists(DIR_IMAGE . $lotto_image['orig_image'])) {
				$image = $lotto_image['orig_image'];
			} else {
				$image = 'no_image.jpg';
			}
			
			$this->data['lotto_images'][] = array(
				'orig_image'      => $image,
				'thumb'      => $this->model_tool_image->resize($image, 100, 100),
				'sort_order' => $lotto_image['sort_order']
			);
		}

		$this->data['no_image'] = $this->model_tool_image->resize('no_image.jpg', 100, 100);

		$this->load->model('catalog/download');
		
		$this->data['downloads'] = $this->model_catalog_download->getDownloads();
		
		if (isset($this->request->post['lotto_download'])) {
			$this->data['lotto_download'] = $this->request->post['lotto_download'];
		} elseif (isset($this->request->get['lotto_id'])) {
			$this->data['lotto_download'] = $this->model_catalog_lotto->getLottoDownloads($this->request->get['lotto_id']);
		} else {
			$this->data['lotto_download'] = array();
		}		
		
		$this->load->model('catalog/category');
				
		$this->data['categories'] = $this->model_catalog_category->getCategories(0);
		
		if (isset($this->request->post['lotto_category'])) {
			$this->data['lotto_category'] = $this->request->post['lotto_category'];
		} elseif (isset($this->request->get['lotto_id'])) {
			$this->data['lotto_category'] = $this->model_catalog_lotto->getLottoCategories($this->request->get['lotto_id']);
		} else {
			$this->data['lotto_category'] = array();
		}

		$this->load->model('catalog/tag');
				
		$this->data['tags'] = $this->model_catalog_tag->getTags();
		
		if (isset($this->request->post['lotto_tag'])) {
			$this->data['lotto_tag'] = $this->request->post['lotto_tag'];
		} elseif (isset($this->request->get['lotto_id'])) {
			$this->data['lotto_tag'] = $this->model_catalog_lotto->getLottoTags($this->request->get['lotto_id']);
		} else {
			$this->data['lotto_tag'] = array();
		}		

		$this->load->model('catalog/lotto_group');
        
		$this->data['product_groups'] = $this->model_catalog_product_group->getProductGroups();
		
		if (isset($this->request->post['product_group'])) {
			$this->data['product_group'] = $this->request->post['product_group'];
		} elseif (isset($this->request->get['product_id'])) {
			$this->data['product_group'] = $this->model_catalog_product->getProductGroups($this->request->get['product_id']);
		} else {
			$this->data['product_group'] = array();
		}
		
		if (isset($this->request->post['product_related'])) {
			$products = $this->request->post['product_related'];
		} elseif (isset($this->request->get['product_id'])) {		
			$products = $this->model_catalog_product->getProductRelated($this->request->get['product_id']);
		} else {
			$products = array();
		}
	
		$this->data['product_related'] = array();
		
		foreach ($products as $product_id) {
			$related_info = $this->model_catalog_product->getProduct($product_id);
			
			if ($related_info) {
				$this->data['product_related'][] = array(
					'product_id' => $related_info['product_id'],
					'name'       => $related_info['name']
				);
			}
		}

    	if (isset($this->request->post['points'])) {
      		$this->data['points'] = $this->request->post['points'];
    	} elseif (!empty($product_info)) {
			$this->data['points'] = $product_info['points'];
		} else {
      		$this->data['points'] = '';
    	}
						
		if (isset($this->request->post['product_reward'])) {
			$this->data['product_reward'] = $this->request->post['product_reward'];
		} elseif (isset($this->request->get['product_id'])) {
			$this->data['product_reward'] = $this->model_catalog_product->getProductRewards($this->request->get['product_id']);
		} else {
			$this->data['product_reward'] = array();
		}
		
		if (isset($this->request->post['product_layout'])) {
			$this->data['product_layout'] = $this->request->post['product_layout'];
		} elseif (isset($this->request->get['product_id'])) {
			$this->data['product_layout'] = $this->model_catalog_product->getProductLayouts($this->request->get['product_id']);
		} else {
			$this->data['product_layout'] = array();
		}

        if (isset($this->request->post['score'])) {
            $this->data['score'] = $this->request->post['score'];
        } elseif (!empty($product_info)) {
            $this->data['score'] = $product_info['score'];
        } else {
            $this->data['score'] = '';
        }

        if (isset($this->request->post['sexy_name'])) {
            $this->data['sexy_name'] = $this->request->post['sexy_name'];
        } elseif (!empty($product_info)) {
            $this->data['sexy_name'] = $product_info['sexy_name'];
        } else {
            $this->data['sexy_name'] = '';
        }

        if (!empty($product)) {
            $this->data['short_track_code'] = $product_info['short_track_code'];
        }

		$this->load->model('design/layout');
		
		$this->data['layouts'] = $this->model_design_layout->getLayouts();
										
		$this->template = 'catalog/product_form.tpl';
		$this->children = array(
			'common/header',
			'common/footer'
		);
				
		$this->response->setOutput($this->render());
  	} 
	
  	private function validateForm() { 
    	if (!$this->user->hasPermission('modify', 'catalog/product')) {
      		$this->error['warning'] = $this->language->get('error_permission');
    	}

    	foreach ($this->request->post['product_description'] as $language_id => $value) {
      		if ((utf8_strlen($value['name']) < 1) || (utf8_strlen($value['name']) > 255)) {
        		$this->error['name'][$language_id] = $this->language->get('error_name');
      		}
    	}
        /*		
    	if ((utf8_strlen($this->request->post['model']) < 1) || (utf8_strlen($this->request->post['model']) > 64)) {
      		$this->error['model'] = $this->language->get('error_model');
    	}
		*/

        if (!empty($this->request->post['product_supplier']['supplier_product_url']) &&
            !(strpos($this->request->post['product_supplier']['supplier_product_url'], "http://") === 0)) {
            $this->error['name'][$language_id] = "供应商网址不合法，必须以http://开头";
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
    	if (!$this->user->hasPermission('modify', 'catalog/product')) {
      		$this->error['warning'] = $this->language->get('error_permission');  
    	}
		
		if (!$this->error) {
	  		return true;
		} else {
	  		return false;
		}
  	}
  	
  	private function validateCopy() {
    	if (!$this->user->hasPermission('modify', 'catalog/product')) {
      		$this->error['warning'] = $this->language->get('error_permission');  
    	}
		
		if (!$this->error) {
	  		return true;
		} else {
	  		return false;
		}
  	}
		
	public function autocomplete() {
		$json = array();
		
		if (isset($this->request->get['filter_name']) || isset($this->request->get['filter_model']) || isset($this->request->get['filter_category_id']) || isset($this->request->get['filter_category_id'])) {
			$this->load->model('catalog/product');
			
			if (isset($this->request->get['filter_name'])) {
				$filter_name = $this->request->get['filter_name'];
			} else {
				$filter_name = '';
			}

			if (isset($this->request->get['filter_short_track_code'])) {
				$filter_short_track_code = $this->request->get['filter_short_track_code'];
			} else {
				$filter_short_track_code = '';
			}
			

			if (isset($this->request->get['filter_model'])) {
				$filter_model = $this->request->get['filter_model'];
			} else {
				$filter_model = '';
			}
						
			if (isset($this->request->get['filter_category_id'])) {
				$filter_category_id = $this->request->get['filter_category_id'];
			} else {
				$filter_category_id = '';
			}

			if (isset($this->request->get['filter_sub_category'])) {
				$filter_sub_category = $this->request->get['filter_sub_category'];
			} else {
				$filter_sub_category = '';
			}

			if (isset($this->request->get['filter_tag_id'])) {
				$filter_tag_id = $this->request->get['filter_tag_id'];
			} else {
				$filter_tag_id = '';
			}
			
			
			if (isset($this->request->get['limit'])) {
				$limit = $this->request->get['limit'];	
			} else {
				$limit = 20;	
			}			
						
			$data = array(
				'filter_name'         => $filter_name,
                'filter_short_track_code' => $filter_short_track_code,
				'filter_model'        => $filter_model,
				'filter_category_id'  => $filter_category_id,
				'filter_sub_category' => $filter_sub_category,
				'filter_tag_id'  => $filter_tag_id,
				'start'               => 0,
				'limit'               => $limit
			);
			
			$results = $this->model_catalog_product->getProducts($data);
			
			foreach ($results as $result) {
				$option_data = array();
				
				$product_options = $this->model_catalog_product->getProductOptions($result['product_id']);	
				
				foreach ($product_options as $product_option) {
					if ($product_option['type'] == 'select' || $product_option['type'] == 'radio' || $product_option['type'] == 'checkbox' || $product_option['type'] == 'image') {
						$option_value_data = array();
					
						foreach ($product_option['product_option_value'] as $product_option_value) {
							$option_value_data[] = array(
								'product_option_value_id' => $product_option_value['product_option_value_id'],
								'option_value_id'         => $product_option_value['option_value_id'],
								'name'                    => $product_option_value['name'],
								'price'                   => (float)$product_option_value['price'] ? $this->currency->format($product_option_value['price'], $this->config->get('config_currency')) : false,
								'price_prefix'            => $product_option_value['price_prefix']
							);	
						}
					
						$option_data[] = array(
							'product_option_id' => $product_option['product_option_id'],
							'option_id'         => $product_option['option_id'],
							'name'              => $product_option['name'],
							'type'              => $product_option['type'],
							'option_value'      => $option_value_data,
							'required'          => $product_option['required']
						);	
					} else {
						$option_data[] = array(
							'product_option_id' => $product_option['product_option_id'],
							'option_id'         => $product_option['option_id'],
							'name'              => $product_option['name'],
							'type'              => $product_option['type'],
							'option_value'      => $product_option['option_value'],
							'required'          => $product_option['required']
						);				
					}
				}
					
				$json[] = array(
					'product_id' => $result['product_id'],
					'name'       => html_entity_decode($result['name'], ENT_QUOTES, 'UTF-8'),	
					'model'      => $result['model'],
					'option'     => $option_data,
					'price'      => $result['price']
				);	
			}
		}
        
		$this->response->setOutput(json_encode($json));
	}
}
?>