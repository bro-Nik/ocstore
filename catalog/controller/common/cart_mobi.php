<?php
// *	My modifications

class ControllerCommonCartMobi extends Controller {
	public function index() {
		// $this->load->language('common/cart');

		// $data['setting_header_cart'] = $revtheme_header_cart = $this->config->get('revtheme_header_cart');
		// $data['setting_revtheme_header_menu'] = $this->config->get('revtheme_header_menu');
		// $revtheme_all_settings = $this->config->get('revtheme_all_settings');
		// $this->load->language('revolution/revolution');

		// $style = '';
		// $this->load->model('tool/image');
		// if ($data['setting_header_cart']['icontype']) {
		// if ($data['setting_header_cart']['icon'] == 'fa none') {
		// 	$style = ' hidden';
		// }
		// $image = '<i class="'.$data['setting_header_cart']['icon'].$style.'"></i>';
		// } else {
		// 	if (!$data['setting_header_cart']['image'] || $data['setting_header_cart']['image'] == 'no_image.png') {
		// 		$style = ' hidden';
		// 	}
		// 	$image = '<span class="heading_ico_image'.$style.'"><img src="'.$this->model_tool_image->resize($data['setting_header_cart']['image'], 21, 21).'" alt=""/></span>';
		// }
		// if ($revtheme_header_cart['cart_size'] == 'small') {
		// 	$heading_title = ($image . $this->language->get('text_rev_items_small'));
		// } else if ($revtheme_header_cart['cart_size'] == 'mini') {
		// 	$heading_title = ($image . $this->language->get('text_rev_items_mini'));
		// } else {
		// 	$heading_title = ($image . $this->language->get('text_rev_items'));
		// }
		// $config_image_cart_width = $this->config->get('theme_' . $this->config->get('config_theme') . '_image_cart_width');
		// $config_image_cart_height = $this->config->get('theme_' . $this->config->get('config_theme') . '_image_cart_height');
		// $product_settings = $this->config->get('revtheme_product_all');
		// $data['q_zavisimost'] = $product_settings['q_zavisimost'];
		$this->load->model('catalog/product');

		$total = 0;		
				
		// $data['text_items'] = sprintf($heading_title, $this->cart->countProducts() + (isset($this->session->data['vouchers']) ? count($this->session->data['vouchers']) : 0), $this->currency->format($total, $this->session->data['currency']));
		$data['text_items'] = sprintf($this->cart->countProducts() + (isset($this->session->data['vouchers']) ? count($this->session->data['vouchers']) : 0), $this->currency->format($total, $this->session->data['currency']));

		$data['cart'] = $this->url->link('checkout/cart');
		$data['checkout'] = $this->url->link(isset($this->config->get('revtheme_all_settings')['revcheckout_status']) && $this->config->get('revtheme_all_settings')['revcheckout_status'] ? 'revolution/revcheckout' : 'checkout/checkout', '', true);
		$data['cart_count'] = $this->cart->countProducts() + isset($this->session->data['vouchers']) ? count($this->session->data['vouchers']) : 0;
		// return $data['cart_count']

		return $this->load->view('common/cart_mobi', $data);

	}

	public function info() {
		$this->response->setOutput($this->index());
	}
}
