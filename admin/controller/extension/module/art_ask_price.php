<?php
/*
@author	Artem Serbulenko
@link	http://cmsshop.com.ua
@link	https://opencartforum.com/profile/762296-bn174uk/
@email 	serfbots@gmail.com
*/
class ControllerExtensionModuleArtAskPrice extends Controller {
	private $error = array(); 
	
	public function index() {  
		$this->load->language('extension/module/art_ask_price');

		$this->document->setTitle($this->language->get('heading_title'));
		
		$this->load->model('setting/setting');
				
		if (($this->request->server['REQUEST_METHOD'] == 'POST') && $this->validate()) {
			$this->model_setting_setting->editSetting('module_art_ask_price', $this->request->post);

			$this->session->data['success'] = $this->language->get('text_success');
			
			if ($this->request->post['module_art_ask_price_apply']) {
				$this->response->redirect($this->url->link('extension/module/art_ask_price', 'user_token=' . $this->session->data['user_token'], true));
			}

			$this->response->redirect($this->url->link('marketplace/extension', 'user_token=' . $this->session->data['user_token'] . '&type=module', true));
		}
		
		$data = array();
		
		$data = $this->getList();
				
		if (isset($this->error['warning'])) {
			$data['error_warning'] = $this->error['warning'];
		} else {
			$data['error_warning'] = '';
		}
		
		if (isset($this->session->data['success'])) {
			$data['success'] = $this->session->data['success'];
			unset($this->session->data['success']);
		} else {
			$data['success'] = '';
		}
				
		$data['breadcrumbs'] = array();
		$data['breadcrumbs'][] = array(
			'text' => $this->language->get('text_home'),
			'href' => $this->url->link('common/dashboard', 'user_token=' . $this->session->data['user_token'], true),
			'separator' => false
		);
		$data['breadcrumbs'][] = array(
			'text' => $this->language->get('text_module'),
			'href' => $this->url->link('marketplace/extension', 'user_token=' . $this->session->data['user_token'] . '&type=module', true),
			'separator' => ' :: '
		);
		$data['breadcrumbs'][] = array(
			'text' => $this->language->get('heading_title'),
			'href' => $this->url->link('extension/module/art_ask_price', 'user_token=' . $this->session->data['user_token'], true),
			'separator' => ' :: '
		);

		$data['action'] = $this->url->link('extension/module/art_ask_price', 'user_token=' . $this->session->data['user_token'], true);
		$data['cancel'] = $this->url->link('marketplace/extension', 'user_token=' . $this->session->data['user_token'] . '&type=module', true);
		$data['delete'] = $this->url->link('extension/module/art_ask_price/delete', 'user_token=' . $this->session->data['user_token'], true);

		$data_mas = array(
			'status',
			'mail',
			'send_success',
			'price',
			'chat_id',
			'token'
		);

		$data_mas_text = array(
			'name',
			'phone',
			'email',
			'comment',
			'personal_data',
		);
		
		foreach ($data_mas as $key => $value) {
			if (isset($this->request->post[$value])) {
				$data['module_art_ask_price_'.$value] = $this->request->post['module_art_ask_price_'.$value];
			} else {
				$data['module_art_ask_price_'.$value] = $this->config->get('module_art_ask_price_'.$value);
			}
		}

		foreach ($data_mas_text as $key => $value) {
			if (isset($this->request->post['art_ask_price_'.$value])) {
				$data['module_art_ask_price_'.$value] = $this->request->post['module_art_ask_price_'.$value];
			} else {
				$data['module_art_ask_price_'.$value] = $this->config->get('module_art_ask_price_'.$value);
			}
			if (isset($this->request->post['art_ask_price_text_'.$value])) {
				$data['module_art_ask_price_text_'.$value] = $this->request->post['module_art_ask_price_text_'.$value];
			} else {
				$data['module_art_ask_price_text_'.$value] = $this->config->get('module_art_ask_price_text_'.$value);
			}
			/*if (isset($this->request->post['art_ask_price_sort_'.$value])) {
				$data['art_ask_price_sort_'.$value] = $this->request->post['art_ask_price_sort_'.$value];
			} else {
				$data['art_ask_price_sort_'.$value] = $this->config->get('art_ask_price_sort_'.$value);
			}*/
		}	

		$data['header'] = $this->load->controller('common/header');
		$data['column_left'] = $this->load->controller('common/column_left');
		$data['footer'] = $this->load->controller('common/footer');

		$this->response->setOutput($this->load->view('extension/module/art_ask_price', $data));
	}
	
	public function getList() {
		$this->load->model('extension/module/art_ask_price');
		
		if (isset($this->request->get['sort'])) {
			$sort = $this->request->get['sort'];
		} else {
			$sort = 'aap.date_added';
		}
		
		if (isset($this->request->get['order'])) {
			$order = $this->request->get['order'];
		} else {
			$order = 'DESC';
		}
		if (isset($this->request->get['page'])) {
			$page = $this->request->get['page'];
		} else {
			$page = 1;
		}
		
		$url = '';

		if (isset($this->request->get['sort'])) {
			$url .= '&sort=' . $this->request->get['sort'];
		}

		if (isset($this->request->get['order'])) {
			$url .= '&order=' . $this->request->get['order'];
		}
		
		if (isset($this->request->get['page'])) {
			$url .= '&page=' . $this->request->get['page'];
		}

		$data['art_ask_prices'] = array();

		$filter_data = array(
			'sort'  => $sort,
			'order' => $order,
			'start' => ($page - 1) * $this->config->get('config_limit_admin'),
			'limit' => $this->config->get('config_limit_admin')
		);
		
		$ask_price_total = $this->model_extension_module_art_ask_price->getTotalAskPrice();
		$data['art_ask_price_total'] = $ask_price_total;
		
		$results = $this->model_extension_module_art_ask_price->getAskPrice($filter_data);

    	foreach ($results as $result) {
			$data['module_art_ask_prices'][] = array(
				'ask_price_id'		=> $result['askprice_id'],
				'product_id'		=> $result['name'],
				'product_link'		=> HTTP_CATALOG .'index.php?route=product/product&product_id='.(int)$result['product_id'],
				'user'				=> $result['user'],
				'email'          	=> $result['email'],
				'phone'          	=> $result['phone'],
				'comment'          	=> $result['comment'],
				'date_added' 	 	=> $result['date_added'],
				'delete'			=> $this->url->link('extension/module/art_ask_price/delete', 'user_token=' . $this->session->data['user_token'] . '&askprice_id=' . $result['askprice_id'] . $url, true),
			);
		}

		$url = '';

		if ($order == 'ASC') {
			$url .= '&order=DESC';
		} else {
			$url .= '&order=ASC';
		}

		if (isset($this->request->get['page'])) {
			$url .= '&page=' . $this->request->get['page'];
		}
		
		$data['sort_user'] = $this->url->link('extension/module/art_ask_price', 'user_token=' . $this->session->data['user_token'] . '&sort=user' . $url, true);
		$data['sort_email'] = $this->url->link('extension/module/art_ask_price', 'user_token=' . $this->session->data['user_token'] . '&sort=email' . $url, true);
		$data['sort_phone'] = $this->url->link('extension/module/art_ask_price', 'user_token=' . $this->session->data['user_token'] . '&sort=phone' . $url, true);
		$data['sort_date_added'] = $this->url->link('extension/module/art_ask_price', 'user_token=' . $this->session->data['user_token'] . '&sort=date_added' . $url, true);
		$data['sort_askprice_id'] = $this->url->link('extension/module/art_ask_price', 'user_token=' . $this->session->data['user_token'] . '&sort=askprice_id' . $url, true);

		$url = '';

		if (isset($this->request->get['sort'])) {
			$url .= '&sort=' . $this->request->get['sort'];
		}
												
		if (isset($this->request->get['order'])) {
			$url .= '&order=' . $this->request->get['order'];
		}

		$pagination = new Pagination();
		$pagination->total = $ask_price_total;
		$pagination->page = $page;
		$pagination->limit = $this->config->get('config_limit_admin');
		$pagination->url = $this->url->link('extension/module/art_ask_price', 'user_token=' . $this->session->data['user_token'] . $url . '&page={page}', true);
		
		$data['pagination'] = $pagination->render();

		$data['results'] = sprintf($this->language->get('text_pagination'), ($ask_price_total) ? (($page - 1) * $this->config->get('config_limit_admin')) + 1 : 0, ((($page - 1) * $this->config->get('config_limit_admin')) > ($ask_price_total - $this->config->get('config_limit_admin'))) ? $ask_price_total : ((($page - 1) * $this->config->get('config_limit_admin')) + $this->config->get('config_limit_admin')), $ask_price_total, ceil($ask_price_total / $this->config->get('config_limit_admin')));

		$data['sort'] = $sort;
		$data['order'] = $order;

		return $data;
	}	

	public function delete() {
		$this->load->model('extension/module/art_ask_price');

		if (isset($this->request->post['selected']) && $this->validate()) {
			foreach ($this->request->post['selected'] as $askprice_id) {
				$this->model_extension_module_art_ask_price->deleteAskPrice($askprice_id);
			}
		} else {
			if (isset($this->request->get['askprice_id']) && $this->validate()) {
				$this->model_extension_module_art_ask_price->deleteAskPrice($this->request->get['askprice_id']);
			}
		}

		$this->response->redirect($this->url->link('extension/module/art_ask_price', 'user_token=' . $this->session->data['user_token'], true));
	
	}

	protected function validate() {
		if (!$this->user->hasPermission('modify', 'extension/module/art_ask_price')) {
			$this->error['warning'] = $this->language->get('error_permission');
		}	

		if (!$this->error) {
			return true;
		} else {
			return false;
		}	
	}

	public function install () {
		$this->load->model('extension/module/art_ask_price');
		$this->model_extension_module_art_ask_price->createTables();
	}
}