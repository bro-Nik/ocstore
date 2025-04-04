<?php  
/*
@author	Artem Serbulenko
@link	http://cmsshop.com.ua
@link	https://opencartforum.com/profile/762296-bn174uk/
@email 	serfbots@gmail.com
*/
class ControllerExtensionModuleArtAskPrice extends Controller {
	public function index() {

		$this->load->language('extension/module/art_ask_price');

		if ($this->request->server['REQUEST_METHOD'] == 'POST') {
			
			$data = array();

			if (isset($this->request->post['art_ask_price_product_id'])) {
				$data['product_id'] = (int)$this->request->post['art_ask_price_product_id'];			
			} else {
				$data['product_id'] = 0;
			}

			if (isset($this->request->post['art_ask_price_product_name'])) {
				$data['product_name'] = $this->request->post['art_ask_price_product_name'];			
			} else {
				$data['product_name'] = '';
			}
			
			$data_mas = array(
				'name',
				'phone',
				'email',
				'comment',
				'personal_data',
			);

			foreach ($data_mas as $key) {
				if (isset($this->request->post['art_ask_price_'.$key])) {
					$data[$key] = $this->request->post['art_ask_price_'.$key];			
				} else {
					$data[$key] = '';
				}
			}
			
			$json = json_decode($this->validForm($this->request->post));

			if (!isset($json->error)) {
				$this->load->model('extension/module/art_ask_price');
				$this->model_extension_module_art_ask_price->addAskPrice($data);
				$json['success'] = html_entity_decode($this->language->get('text_success'));
			}
		}
		
		$this->response->addHeader('Content-Type: application/json');
		$this->response->setOutput(json_encode($json));
	}

	public function success() {
		$this->load->language('extension/module/art_ask_price');
			
		$data['text_title'] = $this->language->get('text_title');
		$data['text_success'] = $this->language->get('text_success');
		if(!empty($this->config->get('module_art_ask_price_send_success'))){
			$data['text_success'] = $this->config->get('module_art_ask_price_send_success');
		}
		$this->response->setOutput($this->load->view('extension/module/art_ask_price_success', $data));
	}

	protected function validForm($data){
		
		$this->load->language('extension/module/art_ask_price');

		$json = array();

		if ($this->config->get('module_art_ask_price_email') == 2) {
			if (utf8_strlen($data['art_ask_price_email']) > 96 || !filter_var($data['art_ask_price_email'], FILTER_VALIDATE_EMAIL)) {
				$json['error']['email'] = $this->language->get('error_email');
			}
		}

		if ($this->config->get('module_art_ask_price_name') == 2) {
			if (utf8_strlen($data['art_ask_price_name']) <= 0) {
				$json['error']['name'] = $this->language->get('error_name');
			}
		}

		if ($this->config->get('module_art_ask_price_comment') == 2) {
			if (utf8_strlen($data['art_ask_price_comment']) < 10 || (utf8_strlen($data['art_ask_price_comment']) > 1000)) {
				$json['error']['comment'] = $this->language->get('error_comment');
			}
		}
		
		if ($this->config->get('module_art_ask_price_phone') == 2) {
			if ((utf8_strlen($data['art_ask_price_phone']) < 3) || (utf8_strlen($data['art_ask_price_phone']) > 32)) {
				$json['error']['phone'] = $this->language->get('error_phone');
			}
		}

		return json_encode($json);
	}
		
	public function getForm() {
			
		if ($this->config->get('module_art_ask_price_status')) {
			$data = array();

			$this->load->model('catalog/product');

			$this->load->language('extension/module/art_ask_price');

			$data['product_id'] = $this->request->get['product_id'];
			$data['text_title'] = $this->language->get('text_title');
			$data['text_desc'] = $this->language->get('text_desc');
			$data['button_submit'] = $this->language->get('button_submit');	

			$product_info = $this->model_catalog_product->getProduct($data['product_id']);

			if (!empty($product_info['meta_h1'])) {	
				$data['product_name'] = $product_info['meta_h1'];
			} else {
				$data['product_name'] = $product_info['name'];
			}
			
			$data_mas = array(
				'name',
				'phone',
				'email',
				'comment',
				'personal_data',
			);

			$form_input = array();
			$form = array();
			$customer_logged = false;

			foreach ($data_mas as $key) {
				$form_input[$key] = $this->config->get('module_art_ask_price_'.$key) ? $this->config->get('module_art_ask_price_'.$key) : 0;
			}

			if ($this->customer->isLogged()) {
				$customer_logged = true;
			}
					
			foreach ($form_input as $key => $value) {
	   			if($value){
	   				$text = $this->config->get('module_art_ask_price_text_'.$key);
	   				$glyphicon = $this->language->get('text_glyphicon_'.$key);
	   				//$sort = $this->config->get('module_art_ask_price_sort_'.$key) != '' ? $this->config->get('module_art_ask_price_sort_'.$key) : '0';
	   				if(strlen($text) > 0){
						$title_text = $text;
					}else{
						$title_text = $this->language->get('text_'.$key);
					}
					$val = '';
					if($customer_logged){
						switch ($key) {
							case 'name':
								$val =  $this->customer->getFirstName();
								break;
							case 'phone':
								$val =  $this->customer->getTelephone();
								break;
							case 'email':
								$val =  $this->customer->getEmail();
								break;
						}
					}
					$form[$key] = array(
						'title' 	=> $title_text,
						//'sort'		=> $sort,
						'name'		=> $key,
						'required' 	=> $value,
						'glyphicon'	=> $glyphicon,
						'val'		=> $val,
					);
	   			}
		   	}

		   	$data['art_ask_price_form'] = $form;

			$this->response->setOutput($this->load->view('extension/module/art_ask_price', $data));
		}
	}
}