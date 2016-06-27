<?php 
class ControllerCatalogSupplier extends Controller { 
	private $error = array();
 
	public function index() {
		$this->load->language('catalog/supplier');

		$this->document->setTitle($this->language->get('heading_title'));
		
		$this->load->model('catalog/supplier');
		 
		$this->getList();
	}

	public function insert() {
		$this->load->language('catalog/supplier');

		$this->document->setTitle($this->language->get('heading_title'));
		
		$this->load->model('catalog/supplier');
		
		if (($this->request->server['REQUEST_METHOD'] == 'POST') && $this->validateForm()) {
			$this->model_catalog_supplier->addSupplier($this->request->post);

			$this->session->data['success'] = $this->language->get('text_success');
			
			$this->redirect($this->url->link('catalog/supplier', 'token=' . $this->session->data['token'], 'SSL')); 
		}

		$this->getForm();
	}

	public function update() {
		$this->load->language('catalog/supplier');

		$this->document->setTitle($this->language->get('heading_title'));
		
		$this->load->model('catalog/supplier');
		
		if (($this->request->server['REQUEST_METHOD'] == 'POST') && $this->validateForm()) {
			$this->model_catalog_supplier->editSupplier($this->request->get['supplier_id'], $this->request->post);
			
			$this->session->data['success'] = $this->language->get('text_success');
			
			$this->redirect($this->url->link('catalog/supplier', 'token=' . $this->session->data['token'], 'SSL'));
		}

		$this->getForm();
	}

	public function delete() {
		$this->load->language('catalog/supplier');

		$this->document->setTitle($this->language->get('heading_title'));
		
		$this->load->model('catalog/supplier');
		
		if (isset($this->request->post['selected']) && $this->validateDelete()) {
			foreach ($this->request->post['selected'] as $supplier_id) {
				$this->model_catalog_supplier->deleteSupplier($supplier_id);
			}

			$this->session->data['success'] = $this->language->get('text_success');

			$this->redirect($this->url->link('catalog/supplier', 'token=' . $this->session->data['token'], 'SSL'));
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
			'href'      => $this->url->link('catalog/supplier', 'token=' . $this->session->data['token'], 'SSL'),
      		'separator' => ' :: '
   		);
									
		$this->data['insert'] = $this->url->link('catalog/supplier/insert', 'token=' . $this->session->data['token'], 'SSL');
		$this->data['delete'] = $this->url->link('catalog/supplier/delete', 'token=' . $this->session->data['token'], 'SSL');
		
		$this->data['suppliers'] = array();

 		$results = $this->model_catalog_supplier->getSuppliers();
 
		foreach ($results as $result) {
			$action = array();
			
			$action[] = array(
				'text' => $this->language->get('text_edit'),
				'href' => $this->url->link('catalog/supplier/update', 'token=' . $this->session->data['token'] . '&supplier_id=' . $result['supplier_id'], 'SSL')
			);
					
			$this->data['suppliers'][] = array(
				'supplier_id' => $result['supplier_id'],
				'name'        => $result['name'],
                'status'      => $result['status'],
				'selected'    => isset($this->request->post['selected']) && in_array($result['supplier_id'], $this->request->post['selected']),
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
		
		$this->template = 'catalog/supplier_list.tpl';
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
			'href'      => $this->url->link('catalog/supplier', 'token=' . $this->session->data['token'], 'SSL'),
      		'separator' => ' :: '
   		);
		
		if (!isset($this->request->get['supplier_id'])) {
			$this->data['action'] = $this->url->link('catalog/supplier/insert', 'token=' . $this->session->data['token'], 'SSL');
		} else {
			$this->data['action'] = $this->url->link('catalog/supplier/update', 'token=' . $this->session->data['token'] . '&supplier_id=' . $this->request->get['supplier_id'], 'SSL');
		}
		
		$this->data['cancel'] = $this->url->link('catalog/supplier', 'token=' . $this->session->data['token'], 'SSL');

		$this->data['token'] = $this->session->data['token'];

		if (isset($this->request->get['supplier_id']) && ($this->request->server['REQUEST_METHOD'] != 'POST')) {
      		$supplier_info = $this->model_catalog_supplier->getSupplier($this->request->get['supplier_id']);
    	}
		
		$this->load->model('localisation/language');
		
		$this->data['languages'] = $this->model_localisation_language->getLanguages();

		if (isset($this->request->post['name'])) {
			$this->data['name'] = $this->request->post['name'];
		} elseif (!empty($supplier_info)) {
			$this->data['name'] = $supplier_info['name'];
		} else {
			$this->data['name'] = "";
		}
        
        /*
		if (isset($this->request->post['template'])) {
			$this->data['template'] = $this->request->post['template'];
		} elseif (!empty($supplier_info)) {
			$this->data['template'] = $supplier_info['template'];
		} else {
			$this->data['template'] = "";
		}
        */

		if (isset($this->request->post['url'])) {
			$this->data['url'] = $this->request->post['url'];
		} elseif (!empty($supplier_info)) {
			$this->data['url'] = $supplier_info['url'];
		} else {
			$this->data['url'] = '';
        }

		if (isset($this->request->post['contact_person'])) {
			$this->data['contact_person'] = $this->request->post['contact_person'];
		} elseif (!empty($supplier_info)) {
			$this->data['contact_person'] = $supplier_info['contact_person'];
		} else {
			$this->data['contact_person'] = '';
        }

		if (isset($this->request->post['phone'])) {
			$this->data['phone'] = $this->request->post['phone'];

		} elseif (!empty($supplier_info)) {
			$this->data['phone'] = $supplier_info['phone'];
		} else {
			$this->data['phone'] = '';
		}

		if (isset($this->request->post['email'])) {
			$this->data['email'] = $this->request->post['email'];

		} elseif (!empty($supplier_info)) {
			$this->data['email'] = $supplier_info['email'];
		} else {
			$this->data['email'] = '';
		}
        
				
		if (isset($this->request->post['status'])) {
			$this->data['status'] = $this->request->post['status'];
		} elseif (!empty($supplier_info)) {
			$this->data['status'] = $supplier_info['status'];
		} else {
			$this->data['status'] = 0;
		}

        if (isset($this->request->post['shipping_cost'])) {
            $this->data['shipping_cost'] = $this->request->post['shipping_cost'];
        } elseif (!empty($supplier_info)) {
            $this->data['shipping_cost'] = $supplier_info['shipping_cost'];
        } else {
            $this->data['shipping_cost'] = 0;
        }
        
        
		$this->template = 'catalog/supplier_form.tpl';
		$this->children = array(
			'common/header',
			'common/footer'
		);
				
		$this->response->setOutput($this->render());
	}

	private function validateForm() {
		if (!$this->user->hasPermission('modify', 'catalog/supplier')) {
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
		if (!$this->user->hasPermission('modify', 'catalog/supplier')) {
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