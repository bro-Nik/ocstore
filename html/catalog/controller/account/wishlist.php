<?php
class ControllerAccountWishList extends Controller {
	public function index() {
		
		// $this->load->language('revolution/revolution');
		$this->load->language('account/wishlist');
		$this->load->model('account/wishlist');
		$this->load->model('catalog/product');
		$this->load->model('tool/image');	
		$this->load->model('revolution/revolution');
		
		$settings = $data['settings'] = $this->config->get('revtheme_all_settings');
		$data['predzakaz_button'] = $this->config->get('revtheme_predzakaz')['status'];
		$data['text_predzakaz'] = $this->config->get('revtheme_predzakaz')['notify_status'] ? $this->language->get('text_predzakaz_notify') : $this->language->get('text_predzakaz');

		if (isset($this->request->get['remove'])) {
			
			if ($this->customer->isLogged()) {
				$this->model_account_wishlist->deleteWishlist($this->request->get['remove']);
				$key = array_search($this->request->get['remove'], $this->session->data['wishlist']);
				if ($key !== false) {
					unset($this->session->data['wishlist'][$key]);
				}
			} else {
				$key = array_search($this->request->get['remove'], $this->session->data['wishlist']);
				if ($key !== false) {
					unset($this->session->data['wishlist'][$key]);
				}	
			}
			
			$this->session->data['success'] = $this->language->get('text_remove');
			$this->response->redirect($this->url->link('account/wishlist'));
		}

		$this->document->setTitle($this->language->get('heading_title'));

		$data['breadcrumbs'] = array();

		$data['breadcrumbs'][] = array(
			'text' => $this->language->get('text_home'),
			'href' => $this->url->link('common/home')
		);

		$data['breadcrumbs'][] = array(
			'text' => $this->language->get('text_account'),
			'href' => $this->url->link('account/account', '', 'SSL')
		);

		$data['breadcrumbs'][] = array(
			'text' => $this->language->get('heading_title'),
			'href' => $this->url->link('account/wishlist')
		);
		
		$data['heading_title'] = $this->language->get('heading_title');

		$currency = $this->session->data['currency'];
		$config_image_wishlist_width = $this->config->get('theme_' . $this->config->get('config_theme') . '_image_wishlist_width');
		$config_image_wishlist_height = $this->config->get('theme_' . $this->config->get('config_theme') . '_image_wishlist_height');
		
		$data['text_empty'] = $this->language->get('text_empty');

		$data['column_image'] = $this->language->get('column_image');
		$data['column_name'] = $this->language->get('column_name');
		$data['column_model'] = $this->language->get('column_model');
		$data['column_sku'] = $this->language->get('column_sku');
		$data['column_stock'] = $this->language->get('column_stock');
		$data['column_price'] = $this->language->get('column_price');
		$data['column_action'] = $this->language->get('column_action');

		$data['button_continue'] = $this->language->get('button_continue');
		$data['button_cart'] = $this->language->get('button_cart');
		$data['button_remove'] = $this->language->get('button_remove');

		$wishlist_register = array();
		
		if ($this->customer->isLogged()) {
			$customer_id = $this->customer->isLogged();
				if (isset($this->session->data['wishlist'])) {
					foreach ($this->session->data['wishlist'] as $key => $product_id) {
						$this->model_revolution_revolution->updateWishlistRegister($product_id, $customer_id);
					}
				}
			$wishlist_register = $this->model_account_wishlist->getWishlist();
		} else {
			$wishlist_register = false;
		}

		if (isset($this->session->data['success'])) {
			$data['success'] = $this->session->data['success'];

			unset($this->session->data['success']);
		} else {
			$data['success'] = '';
		}

		$data['products'] = array();

		if (!isset($this->session->data['wishlist'])) {
			$this->session->data['wishlist'] = array();
		}
		
		if ($wishlist_register) {
		
			foreach ($wishlist_register as $result) {
				
				$product_info = $this->model_catalog_product->getProduct($result['product_id']);
				
				if ($product_info) {
					if ($product_info['image']) {
						$image = $this->model_tool_image->resize($product_info['image'], $config_image_wishlist_width, $config_image_wishlist_height);
					} else {
						$image = false;
					}

					if ($this->config->get('revtheme_product_all')['ed_izm']) {
						$ed_izm = $product_info['isbn'];
					} else {
						$ed_izm = false;
					}

					if ($product_info['quantity'] <= 0) {
						$stock = $product_info['stock_status'];
					} elseif ($this->config->get('config_stock_display')) {
						if ($ed_izm) {
							$stock = $product_info['quantity'];
						} else {
							$stock = $product_info['quantity'] . ' ' . $this->language->get('text_shtuki');
						}
					} else {
						$stock = $this->language->get('text_product_instock');
					}

					if (($this->config->get('config_customer_price') && $this->customer->isLogged()) || !$this->config->get('config_customer_price')) {
						$price = $this->currency->format($this->tax->calculate($product_info['price'], $product_info['tax_class_id'], $this->config->get('config_tax')), $currency);
					} else {
						$price = false;
					}

					if ((float)$product_info['special']) {
						$special = $this->currency->format($this->tax->calculate($product_info['special'], $product_info['tax_class_id'], $this->config->get('config_tax')), $currency);
					} else {
						$special = false;
					}

					$data['products'][] = array(
						'ed_izm' 	 => $ed_izm,
						'product_id' => $product_info['product_id'],
						'thumb'      => $image,
						'name'       => $product_info['name'],
						'model'      => $product_info['model'],
						'sku'        => $product_info['sku'],
						'stock'      => $stock,
						'price'      => $price,
						'special'    => $special,
						'href'       => $this->url->link('product/product', 'product_id=' . $product_info['product_id']),
						'remove'     => $this->url->link('account/wishlist', 'remove=' . $product_info['product_id']),
						'quantity'   => $product_info['quantity']
					);
				} else {						
						$this->model_account_wishlist->deleteWishlist($result['product_id']);
				}
			}
		} else {
			
			foreach ($this->session->data['wishlist'] as $key => $product_id) {

				$product_info = $this->model_catalog_product->getProduct($product_id);
				
				if ($product_info) {
					if ($product_info['image']) {
						$image = $this->model_tool_image->resize($product_info['image'], $config_image_wishlist_width, $config_image_wishlist_height);
					} else {
						$image = false;
					}

					if ($this->config->get('revtheme_product_all')['ed_izm']) {
						$ed_izm = $product_info['isbn'];
					} else {
						$ed_izm = false;
					}

					if ($product_info['quantity'] <= 0) {
						$stock = $product_info['stock_status'];
					} elseif ($this->config->get('config_stock_display')) {
						if ($ed_izm) {
							$stock = $product_info['quantity'];
						} else {
							$stock = $product_info['quantity'] . ' ' . $this->language->get('text_shtuki');
						}
					} else {
						$stock = $this->language->get('text_product_instock');
					}

					if (($this->config->get('config_customer_price') && $this->customer->isLogged()) || !$this->config->get('config_customer_price')) {
						$price = $this->currency->format($this->tax->calculate($product_info['price'], $product_info['tax_class_id'], $this->config->get('config_tax')), $currency);
					} else {
						$price = false;
					}

					if ((float)$product_info['special']) {
						$special = $this->currency->format($this->tax->calculate($product_info['special'], $product_info['tax_class_id'], $this->config->get('config_tax')), $currency);
					} else {
						$special = false;
					}

					$data['products'][] = array(
						'ed_izm' 	 => $ed_izm,
						'product_id' => $product_info['product_id'],
						'thumb'      => $image,
						'name'       => $product_info['name'],
						'model'      => $product_info['model'],
						'sku'        => $product_info['sku'],
						'stock'      => $stock,
						'price'      => $price,
						'special'    => $special,
						'href'       => $this->url->link('product/product', 'product_id=' . $product_info['product_id']),
						'remove'     => $this->url->link('account/wishlist', 'remove=' . $product_info['product_id']),
						'quantity'   => $product_info['quantity']
					);
				} else {
					unset($this->session->data['wishlist'][$product_id]);
				}
			}
			
		}
		
		$data['continue'] = $this->url->link('account/account', '', 'SSL');

		$data['column_left'] = $this->load->controller('common/column_left');
		$data['column_right'] = $this->load->controller('common/column_right');
		$data['content_top'] = $this->load->controller('common/content_top');
		$data['content_bottom'] = $this->load->controller('common/content_bottom');
		$data['footer'] = $this->load->controller('common/footer');
		$data['header'] = $this->load->controller('common/header');
		
		$this->response->setOutput($this->load->view('account/wishlist', $data));
	}

	public function add() {
		$this->load->language('account/wishlist');
		$this->load->language('revolution/revolution');

		$json = array();

		if (isset($this->request->post['product_id'])) {
			$product_id = $this->request->post['product_id'];
		} else {
			$product_id = 0;
		}

		$this->load->model('catalog/product');

		if (!isset($this->session->data['wishlist'])) {
			$this->session->data['wishlist'] = array();
		}
		
		$product_info = $this->model_catalog_product->getProduct($product_id);
		
		if ($product_info) {
			$this->load->model('account/wishlist');
			if (!in_array($product_id, $this->session->data['wishlist'])) {
				if ($this->customer->isLogged()) {
					$this->model_account_wishlist->addWishlist($this->request->post['product_id']);
				}
				$this->session->data['wishlist'][$product_id] = $product_id;
				$json['success'] = sprintf($this->language->get('text_success'), $this->url->link('product/product', 'product_id=' . (int)$this->request->post['product_id']), $product_info['name'], $this->url->link('account/wishlist'));
				$json['class_wishlist'] = 'in_wishlist';
				$json['button_wishlist'] = $this->language->get('button_wishlist_out');
			} else {
				unset($this->session->data['wishlist'][$product_id]);
				$json['success'] = sprintf($this->language->get('text_unsuccess'), $this->url->link('product/product', 'product_id=' . (int)$this->request->post['product_id']), $product_info['name'], $this->url->link('account/wishlist'));
				$json['class_wishlist'] = '';
				$json['button_wishlist'] = $this->language->get('button_wishlist');
			}
			
			if ($this->customer->isLogged()) {
				$this->model_account_wishlist->deleteWishlist($product_id);
				$json['total'] = count($this->session->data['wishlist'] ?? []);
			} else {
				$json['total'] = count($this->session->data['wishlist'] ?? []);
			}

		}
		
		$json['title'] = $this->language->get('heading_title');

		$this->response->addHeader('Content-Type: application/json');
		$this->response->setOutput(json_encode($json));
		
	}
}
