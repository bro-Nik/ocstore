<?php
require_once('catalog/controller/trait/product.php');

class ControllerModalQuickview extends Controller {
	use \ProductInfo;

	public function index() {
		
		$setting_catalog_all = $this->config->get('revtheme_catalog_all');

		$data = array();

		$this->load->model('catalog/product');
		$this->load->language('revolution/revolution');

		$data['product_id'] = (int)$this->request->get['revproduct_id'] ?? 0;
		$product_info = $this->model_catalog_product->getProduct($data['product_id']);

		if ($product_info) {
			$data['heading'] = $product_info['meta_h1'] ?? $product_info['name'];
			$data['view_product_link'] = $this->url->link('product/product', 'product_id=' . $product_info['product_id']);
			$data['product_name'] = $product_info['name'];

			if (strlen($product_info['description']) > 20) {
				$data['description'] = html_entity_decode($product_info['description'], ENT_QUOTES, 'UTF-8');
			} else {
				$data['description'] = '';
			}

			$this->prepareProductImages($product_info, $data);
			$this->prepareProductPrice($product_info, $data);
			$this->prepareProductStikers($product_info, $data);
			$data['options'] = $this->prepareProductOptions($data['product_id']);
			$this->prepareProductOther($product_info, $data);
			$this->prepareProductTags($product_info, $data);
			$this->prepareProductReviews($product_info, $data);

			$this->response->setOutput($this->load->view('modals/quickview', $data));
		  
		} else {
			exit();
		}
	}
}
