<?php
trait ProductInfo {
	protected function prepareProductImages($product_info, &$data) {
		$this->load->model('tool/image');
		$this->load->model('catalog/product');

		$image = $product_info['image'] ? $product_info['image'] : 'no_image.png';
		$data['thumb']      = $this->model_tool_image->resize($image, $this->config->get('theme_' . $this->config->get('config_theme') . '_image_thumb_width'), $this->config->get('theme_' . $this->config->get('config_theme') . '_image_thumb_height'));
		$data['additional'] = $this->model_tool_image->resize($image, $this->config->get('theme_' . $this->config->get('config_theme') . '_image_additional_width'), $this->config->get('theme_' . $this->config->get('config_theme') . '_image_additional_height'));
		$data['popup'] 		  = $this->model_tool_image->resize($image, $this->config->get('theme_' . $this->config->get('config_theme') . '_image_popup_width'), $this->config->get('theme_' . $this->config->get('config_theme') . '_image_popup_height'));
		$data['thumb_small'] = $this->model_tool_image->resize($image, $this->config->get('theme_' . $this->config->get('config_theme') . '_image_additional_width'), $this->config->get('theme_' . $this->config->get('config_theme') . '_image_additional_height'));

    $images = [];
    $results = $this->model_catalog_product->getProductImages($data['product_id']);
    
    foreach ($results as $result) {
      $images[] = [
        'thumb' => $this->model_tool_image->resize($result['image'], $this->config->get('theme_' . $this->config->get('config_theme') . '_image_thumb_width'), $this->config->get('theme_' . $this->config->get('config_theme') . '_image_thumb_height')),
        'additional' => $this->model_tool_image->resize($result['image'], $this->config->get('theme_' . $this->config->get('config_theme') . '_image_additional_width'), $this->config->get('theme_' . $this->config->get('config_theme') . '_image_additional_height')),
        'popup' => $this->model_tool_image->resize($result['image'], $this->config->get('theme_' . $this->config->get('config_theme') . '_image_popup_width'), $this->config->get('theme_' . $this->config->get('config_theme') . '_image_popup_height')),
        'video' => $result['video']
      ];
    }
		$data['images'] = $images;
	}

	protected function prepareProductPrice($product_info, &$data) {
		$data['price'] = round($product_info['price'], 2);
		$data['special_price'] = $product_info['special'] ? round($product_info['special'] , 2) : false;

		$this->load->model('revolution/revolution');
		$special = $this->model_revolution_revolution->getProductSpecialData($data['product_id']);
		if (is_array($special) && isset($special['date_end']) && $special['date_end'] && time() < strtotime($special['date_end'])) {
			$this->load->language('revolution/revolution');
			$data['special_end'] = $special['date_end'];
			$data['text_countdown'] = $this->language->get('text_countdown');
		} else {
			$data['special_end'] = false;
		}
  }
	protected function prepareProductStikers($product_info, &$data) {
		$data['stiker_ean'] = $product_info['ean'];
		$data['stiker_jan'] = $product_info['jan'];
		$data['stiker_isbn'] = $product_info['isbn'];
  }

	protected function prepareProductReviews($product_info, &$data) {
		$data['review_count'] = (int)$product_info['reviews'];
		$data['answer_count'] = (int)$this->model_revolution_revolution->gettotalanswers($data['product_id']);

		$data['reviews_number'] = $reviews_number = (int)$product_info['reviews'];
		function getcartword($number, $suffix) {
			$keys = array(2, 0, 1, 1, 1, 2);
			$mod = $number % 100;
			$suffix_key = ($mod > 7 && $mod < 20) ? 2: $keys[min($mod % 10, 5)];
			return $suffix[$suffix_key];
		}
  }

	protected function prepareProductOptions($product_info, &$data) {
		$options = array();
		foreach ($this->model_catalog_product->getProductOptions($data['product_id']) as $option) {
			$product_option_value_data = array();

			foreach ($option['product_option_value'] as $option_value) {
				if (!$option_value['subtract'] || ($option_value['quantity'] > 0)) {
					if ((($this->config->get('config_customer_price') && $this->customer->isLogged()) || !$this->config->get('config_customer_price')) && (float)$option_value['price']) {
						$price = round($option_value['price'], 2);
					} else {
						$price = false;
					}

					$product_option_value_data[] = array(
						'product_option_value_id' => $option_value['product_option_value_id'],
						'option_value_id'         => $option_value['option_value_id'],
						'name'                    => $option_value['name'],
							'quantity' => $option_value['quantity'],
							'model' => $option_value['model'],
						'image'                   => $this->model_tool_image->resize($option_value['image'], 50, 50),
						'price'                   => $price,
						'price_prefix'            => $option_value['price_prefix']
					);
				}
			}

			$options[] = array(
				'product_option_id'    => $option['product_option_id'],
				'product_option_value' => $product_option_value_data,
				'option_id'            => $option['option_id'],
				'name'                 => $option['name'],
				'type'                 => $option['type'],
				'value'                => $option['value'],
				'required'             => $option['required']
			);
		}
		$data['options'] = $options;
	}

	protected function prepareProductOther($product_info, &$data) {
    $this->load->model('revolution/revolution');
		$this->load->model('catalog/product');

		$data['sklad_status'] = $product_info['stock_status'];
		$data['quantity'] = $product_info['quantity'];
		$data['minimum'] = $product_info['minimum'] ? $product_info['minimum'] : 1;
		$data['manufacturer'] = $product_info['manufacturer'];
		$data['manufacturers'] = $this->url->link('product/manufacturer/info', 'manufacturer_id=' . $product_info['manufacturer_id']);
		$data['sku'] = $product_info['sku'];
		$data['text_minimum'] = sprintf($this->language->get('text_minimum'), $product_info['minimum']);
		$data['brand'] = $this->model_revolution_revolution->get_pr_brand($data['product_id']);
		$data['options_buy'] = $product_info['options_buy'];
		$data['recurrings'] = $this->model_catalog_product->getProfiles($data['product_id']);
		$data['mpn'] = $product_info['mpn'];
		$data['share'] = $this->url->link('product/product', 'product_id=' . $data['product_id']);

		$data['rating'] = number_format($product_info['rating'], 1);
		$data['revtheme_product_all_attribute_group'] = $this->config->get('revtheme_product_all_attribute_group');
		$data['attribute_groups'] = $this->model_catalog_product->getProductAttributes($data['product_id']);

		// Данные для статистики
    $data['counter_data'] = [ 'type' => 'product', 'id' => $data['product_id'] ];
  }

	protected function prepareProductTags($product_info, &$data) {
		$data['tags'] = array();

		if ($product_info['tag']) {
			$tags = explode(',', $product_info['tag']);

			foreach ($tags as $tag) {
				$data['tags'][] = array(
					'tag'  => trim($tag),
					'href' => $this->url->link('product/search', 'tag=' . trim($tag))
				);
			}
		}
  }

	protected function prepareProductTabs($product_info, &$data) {
    $this->load->model('revolution/revolution');

		$data['tab_info'] = $this->model_revolution_revolution->getproductTabs($this->request->get['product_id']);
		$data['product_tabs'] = array();	
		$tabresults = $this->model_revolution_revolution->getproducttab($this->request->get['product_id']);
		foreach($tabresults as $result_tab){
			$data['product_tabs'][] = array(
				'product_tab_id' => $result_tab['product_tab_id'],
				'title'          => $result_tab['heading'],
				'description'    => html_entity_decode($result_tab['description'], ENT_QUOTES, 'UTF-8')
			);
		}
  }
}
