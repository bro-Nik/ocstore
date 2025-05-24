<?php
class ControllerExtensionModuleProdvar extends Controller { 	
	private $error = array();
	private $modpath = 'extension/module/prodvar'; 
	private $modtpl = 'extension/module/prodvar';
	private $prodvarfieldform = 'extension/module/prodvarfieldform';	
	private $modname = 'module_prodvar';
	private $modssl = true;
	private $token_str = '';
	private $modurl = 'marketplace/extension';
	private $modurltext = '';

	public function __construct($registry) {
		parent::__construct($registry);
		$this->token_str = 'user_token=' . $this->session->data['user_token'] . '&type=module';
 	} 
	
	public function index() {
		$data = $this->load->language($this->modpath);
		$this->modurltext = $this->language->get('text_extension');

		$this->document->setTitle($this->language->get('page_title'));

		$this->load->model('setting/setting');

		if (($this->request->server['REQUEST_METHOD'] == 'POST') && $this->validate()) {
			$this->model_setting_setting->editSetting($this->modname, $this->request->post);

			$this->session->data['success'] = $this->language->get('text_success');

			if(! (isset($this->request->post['svsty']) && $this->request->post['svsty'] == 1)) {
				$this->response->redirect($this->url->link($this->modurl, $this->token_str, $this->modssl));
			}
		}

		$data['heading_title'] = $this->language->get('heading_title');
 		$data['text_edit'] = $this->language->get('text_edit');
		$data['text_enabled'] = $this->language->get('text_enabled');
		$data['text_disabled'] = $this->language->get('text_disabled');
 		$data['entry_status'] = $this->language->get('entry_status');
  		$data['button_save'] = $this->language->get('button_save');
		$data['button_cancel'] = $this->language->get('button_cancel');

		if (isset($this->error['warning'])) {
			$data['error_warning'] = $this->error['warning'];
		} else {
			$data['error_warning'] = '';
		}

		$data['breadcrumbs'] = array();

		$data['breadcrumbs'][] = array(
			'text' => $this->language->get('text_home'),
			'href' => $this->url->link('common/dashboard', $this->token_str, $this->modssl)
		);
		
		$data['breadcrumbs'][] = array(
			'text' => $this->modurltext,
			'href' => $this->url->link($this->modurl, $this->token_str, $this->modssl)
		);

		$data['breadcrumbs'][] = array(
			'text' => $this->language->get('page_title'),
			'href' => $this->url->link($this->modpath, $this->token_str, $this->modssl)
		);

		$data['action'] = $this->url->link($this->modpath, $this->token_str, $this->modssl);
		
		$data['cancel'] = $this->url->link($this->modurl, $this->token_str , $this->modssl); 

		$data['user_token'] = $this->session->data['user_token'];
		
		$data[$this->modname.'_status'] = $this->setvalue($this->modname.'_status');
		
 		$data['modname'] = $this->modname;
  		  
		$data['header'] = $this->load->controller('common/header');
		$data['column_left'] = $this->load->controller('common/column_left');
		$data['footer'] = $this->load->controller('common/footer');

		$this->response->setOutput($this->load->view($this->modtpl, $data));
	}
	
	protected function setvalue($postfield) {
		if (isset($this->request->post[$postfield])) {
			$postfield_value = $this->request->post[$postfield];
		} else {
			$postfield_value = $this->config->get($postfield);
		} 	
 		return $postfield_value;
	}

	protected function validate() {
		if (!$this->user->hasPermission('modify', $this->modpath)) {
			$this->error['warning'] = $this->language->get('error_permission');
		}

		return !$this->error;
	}
	
	public function prodvarfieldform() { 		
		$data[$this->modname.'_status'] = $this->setvalue($this->modname.'_status');
		if($data[$this->modname.'_status']) { 
			
			$data = $this->load->language($this->modpath);
			
			if(substr(VERSION,0,3)>='3.0') { 
				$data['user_token'] = $this->session->data['user_token'];
			} else {
				$data['token'] = $this->session->data['token'];
			}
			
			$this->load->model('localisation/language');
			$languages = $this->model_localisation_language->getLanguages();
			foreach($languages as $language) {
				$imgsrc = "language/".$language['code']."/".$language['code'].".png";
				
				$data['languages'][] = array("language_id" => $language['language_id'], "name" => $language['name'], "imgsrc" => $imgsrc);
			} 
			
			if (isset($this->request->get['product_id'])) {
				$prodvar_data = $this->getprodvardata($this->request->get['product_id']);
			}
			
			if (isset($this->request->post['prodvar_title'])) {
				$data['prodvar_title'] = $this->request->post['prodvar_title'];
			} elseif (isset($this->request->get['product_id']) && $prodvar_data) {
				$data['prodvar_title'] = json_decode($prodvar_data['prodvar_title'], true);
			} else {
				$data['prodvar_title'] = '';
			} 
			
			if (isset($this->request->post['prodvar_product_str_id'])) {
				$products = $this->request->post['prodvar_product_str_id'];
			} elseif (isset($this->request->get['product_id']) && $prodvar_data) {
				$products = ($prodvar_data['prodvar_product_str_id']) ? explode(",", $prodvar_data['prodvar_product_str_id']) : array();
			} else {
				$products = array();
			}  
			
			$data['prodvar_product_str_ids'] = array();

			foreach ($products as $product_id) {
				$related_info = $this->model_catalog_product->getProduct($product_id);
	
				if ($related_info) {
					$data['prodvar_product_str_ids'][] = array(
						'product_id' => $related_info['product_id'],
						'name'       => $related_info['name']
					);
				}
			}
			
			return ($this->load->view($this->prodvarfieldform, $data));
		} 
	}
	
	public function saveprodvarform($product_id) {
		
		if($this->request->post['prodvar_product_str_id']) {

			$prodvar_data = $this->getprodvardata($product_id);
			if (isset($prodvar_data['prodvar_product_str_id'])&&$prodvar_data['prodvar_product_str_id']){
				$prodvar_exist = explode(',',$prodvar_data['prodvar_product_str_id']);
				foreach($prodvar_exist as $pid) {
					$this->db->query("DELETE FROM " . DB_PREFIX . "prodvar WHERE product_id = '" . (int)$pid . "'");
				}
			}

			if (isset($this->request->post['prodvar_title'])) {
				$prodvar_title = json_encode($this->request->post['prodvar_title'], true);
			} else {
				$prodvar_title = '';
			} 
			
			//$this->request->post['prodvar_product_str_id'][] = $product_id;
 			$product_prodvar = array_unique($this->request->post['prodvar_product_str_id']);

			$prodvar_product_str_id = implode(",", $product_prodvar);
			
			foreach($product_prodvar as $pid) {
 				$this->db->query("INSERT INTO " . DB_PREFIX . "prodvar SET product_id = '" . (int)$pid . "', `prodvar_title` = '" . $this->db->escape($prodvar_title) . "', `prodvar_product_str_id` = '" . $this->db->escape($prodvar_product_str_id) . "' ");
 			}
			
		} else {
			$prodvar_data = $this->getprodvardata($product_id);
			if (isset($prodvar_data['prodvar_product_str_id'])&&$prodvar_data['prodvar_product_str_id']){
				$product_prodvar = explode(',',$prodvar_data['prodvar_product_str_id']);
				foreach($product_prodvar as $pid) {
					$this->db->query("DELETE FROM " . DB_PREFIX . "prodvar WHERE product_id = '" . (int)$pid . "'");
				}
			}
		}	
	}
	
	public function getprodvardata($product_id) { 
		$query = $this->db->query("SELECT * FROM " . DB_PREFIX . "prodvar WHERE product_id = '" . (int)$product_id . "' limit 1");
		if($query->num_rows){
			return $query->row;
		} 
	}
	
	public function install() {
		$query = $this->db->query("SHOW TABLES LIKE '".DB_PREFIX."prodvar' ");
		if(!$query->num_rows){
			$this->db->query("CREATE TABLE IF NOT EXISTS `".DB_PREFIX."prodvar` (
			  `prodvar_id` int(11) NOT NULL AUTO_INCREMENT,
 			  `product_id` int(11),
			  `prodvar_title` text,
			  `prodvar_product_str_id` TEXT,
  			  PRIMARY KEY (`prodvar_id`)
			  ) ENGINE=MyISAM  DEFAULT CHARSET=utf8");
		}
	}
	public function uninstall() { 
		$this->db->query("DROP TABLE `".DB_PREFIX."prodvar` ");       
	}  
}