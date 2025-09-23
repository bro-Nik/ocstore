<?php
abstract class ControllerBaseProductCart extends Controller {
    protected function prepareProductsData($products, $settings=[]) {

    $data = array();
		if ($settings) {
			if (!$settings['status'] or !$products) {
				return [];
			}
			$data['title'] = $settings['title'] ?? '';
		}
		
		$data['products'] = $this->prepareProducts($products);
        
    return $data;
  }

  protected function prepareProducts($products_rows) {
		$products = array();

		$this->load->language('revolution/revolution');
    $this->load->model('catalog/category');
    $this->load->model('catalog/product');
		$this->load->model('tool/image');
		$this->load->model('revolution/revolution');

		foreach ($products_rows as $product_info) {
			
			$image = $product_info['image'] ? $product_info['image'] : 'placeholder.png';
			$image = $this->model_tool_image->resize($image, $this->config->get('theme_' . $this->config->get('config_theme') . '_image_product_width'), $this->config->get('theme_' . $this->config->get('config_theme') . '_image_product_height'));

			$products[] = array(
				'stiker_ean' => $product_info['ean'],
				'stiker_jan' => $product_info['jan'],
				'stiker_isbn' => $product_info['isbn'],
				'sklad_status' => $product_info['stock_status'],
				'quantity' => $product_info['quantity'],
				'model' => $product_info['model'],
				'product_id'  => $product_info['product_id'],
				'thumb'       => $image,
				'name'        => $product_info['name'],
				'manufacturer' => $product_info['manufacturer'],
				'description' => $this->model_revolution_revolution->getAttrText($product_info['product_id']),
				'price'       => round($product_info['price'], 2),
				'special_price' => $product_info['special'] ? round($product_info['special'] , 2) : false,
				'minimum'     => $product_info['minimum'] > 0 ? $product_info['minimum'] : 1,
				'rating'      => number_format($product_info['rating'], 1),
				'reviews'      => number_format($product_info['reviews'], 1),
				'href'        => $this->url->link('product/product', 'product_id=' . $product_info['product_id'])
			);
		}
		return $products;
	}
}
