<?php
require_once('catalog/controller/trait/cache.php');

class ControllerExtensionModuleProdvar extends Controller {	
	use \CacheTrait;

	private $error = array();
	private $modpath = 'extension/module/prodvar'; 
	private $modtpl = 'extension/module/prodvar'; 
	private $modname = 'module_prodvar';
	private $modssl = true;
	private $langid = 0;
	
	public function __construct($registry) {
		parent::__construct($registry);
		$this->langid = (int)$this->config->get('config_language_id');
 	} 
	
	public function index() {
		$cache_key = 'product.prodvar.' . $this->request->get['product_id'];
		$cache = $this->getCache($cache_key);
    if ($cache !== false) {
      return $cache;
    }

		$data['prodvar_status'] = $this->setvalue($this->modname.'_status');
		if ($data['prodvar_status'] && isset($this->request->get['product_id'])) {
			
			$this->load->model('tool/image');
			$this->load->model('catalog/product');
			
			$url = '';

			if (isset($this->request->get['path'])) {
				$url .= '&path=' . $this->request->get['path'];
			}

			if (isset($this->request->get['filter'])) {
				$url .= '&filter=' . $this->request->get['filter'];
			}

			if (isset($this->request->get['manufacturer_id'])) {
				$url .= '&manufacturer_id=' . $this->request->get['manufacturer_id'];
			}

			if (isset($this->request->get['search'])) {
				$url .= '&search=' . $this->request->get['search'];
			}

			if (isset($this->request->get['tag'])) {
				$url .= '&tag=' . $this->request->get['tag'];
			}

			if (isset($this->request->get['description'])) {
				$url .= '&description=' . $this->request->get['description'];
			}

			if (isset($this->request->get['category_id'])) {
				$url .= '&category_id=' . $this->request->get['category_id'];
			}

			if (isset($this->request->get['sub_category'])) {
				$url .= '&sub_category=' . $this->request->get['sub_category'];
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

			if (isset($this->request->get['limit'])) {
				$url .= '&limit=' . $this->request->get['limit'];
			}
		
			$prodvar_data = $this->getprodvardata($this->request->get['product_id']);
 			
			// $data['prodvar_title'] = json_decode($prodvar_data['prodvar_title'], true);
 		// 	$data['prodvar_title'] = $data['prodvar_title'][$this->langid];
			// 
			// $products = ($prodvar_data['prodvar_product_str_id']) ? explode(",", $prodvar_data['prodvar_product_str_id']) : array();
			//
		// Проверяем, существует ли $prodvar_data и является ли массивом
			if (!empty($prodvar_data) && isset($prodvar_data['prodvar_title'])) {
				$decoded_title = json_decode($prodvar_data['prodvar_title'], true);
				// Проверяем успешность декодирования и наличие нужного языка
				$data['prodvar_title'] = ($decoded_title && isset($decoded_title[$this->langid])) 
					? $decoded_title[$this->langid] 
					: ''; // или значение по умолчанию
			} else {
				$data['prodvar_title'] = ''; // или значение по умолчанию
			}

// Обрабатываем список продуктов с проверкой на null/пустоту
$products = (!empty($prodvar_data['prodvar_product_str_id'])) 
    ? explode(",", $prodvar_data['prodvar_product_str_id']) 
    : array();	// if(!empty($products)) $products = array_diff($products, array($this->request->get['product_id']));
			
			$data['products'] = array();
			 			
			foreach ($products as $product_id) {
				$result = $this->model_catalog_product->getProduct($product_id);
	
				if ($result) {				
  						
					$data['products'][] = array(
						'product_id'  => $result['product_id'],
						'upc' 	  => $result['upc'],
						'name'        => $result['name'],
						'class'       => ($this->request->get['product_id'] == $result['product_id']) ? 'active' : '',
						'href' 		  => $this->url->link('product/product', $url . '&product_id=' . $product_id, $this->modssl)
					); 
				}
			}
			
			$result = $this->load->view($this->modtpl, $data);
    	$this->setCache($cache_key, $result, 108000);
			return $result;
 		} 
	}
	
	private function setvalue($postfield) {
		return $this->config->get($postfield);
	}
	
	public function getprodvardata($product_id) { 
		$query = $this->db->query("SELECT * FROM " . DB_PREFIX . "prodvar WHERE product_id = '" . (int)$product_id . "' limit 1");
		if($query->num_rows){
			return $query->row;
		} 
	}
}
