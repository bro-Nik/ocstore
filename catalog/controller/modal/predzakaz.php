<?php
require_once('catalog/controller/extension/module/validator.php');
require_once('catalog/controller/trait/form_handler.php');

class ControllerModalPredzakaz extends Controller {
	use \ValidatorTrait, \FormHandlerTrait;

	const FIELDS = ['name', 'phone', 'comment', 'privacy_policy_confirmation'];
  const REQUIRED_FIELDS = ['phone', 'privacy_policy_confirmation'];

	public function index() {

		$this->load->model('catalog/product');
		$this->load->language('revolution/revolution');

		$data = $this->getCommonData('contact');
		
		$product_id = (int)$this->request->get['revproduct_id'] ?? 0;
		$product_info = $this->model_catalog_product->getProduct($product_id);
		$data['product_id'] = $product_id;

		if ($product_info) {
			$data['heading'] = $this->language->get('text_predzakaz');
			$data['product_name'] = $product_info['name'];
			$data['price'] = $product_info['price'];
			$data['special_price'] = $product_info['special'];
			$data['quantity'] = $product_info['quantity'];

			if ($product_info['quantity'] <= 0) {
				$data['error'] = $this->language->get('text_zakaz_not') . $product_info['stock_status'] . '.';
			} else {
				$data['error'] = '';
			}

			$this->load->model('tool/image');

			if ($product_info['image']) {
				$data['thumb'] = $this->model_tool_image->resize($product_info['image'], 80, 80);
			} else {
				$data['thumb'] = $this->model_tool_image->resize("placeholder.png", 80, 80);
			}
				
			$this->response->setOutput($this->load->view('modals/predzakaz', $data));
		} else {
			$this->response->redirect($this->url->link(isset($this->config->get('revtheme_all_settings')['revcheckout_status']) && $this->config->get('revtheme_all_settings')['revcheckout_status'] ? 'revolution/revcheckout' : 'checkout/checkout'));
		}
	}

	public function send() {
		return $this->handleFormRequest($this->request->post, self::REQUIRED_FIELDS);
	}

	protected function success(&$json, $data) {
		$this->language->load('revolution/revolution');
		$this->load->model('revolution/revolution');
		$this->load->model('catalog/product');

		$this->model_revolution_revolution->addProductNotify($data);
		$json['html'] = $this->language->get('text_success_notify');

		return $json;
	}
}
