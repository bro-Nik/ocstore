<?php
class ModelExtensionTotalSomDiscount extends Model {
	public function getTotal($total) {
		$this->load->language('extension/total/som_discount');
		$this->load->model('extension/module/special_offer');

		$discount_offers = array();
		$offers = array();
		$products = $this->cart->getProducts();
		foreach ($products as $product) {
			$special_offer_id = $this->model_extension_module_special_offer->getProductSpecialOfferID($product['product_id']);
			if (!empty($special_offer_id)) {
				$special_offer_info = $this->model_extension_module_special_offer->getSpecialOffer($special_offer_id);

				if ($special_offer_info['offer_type'] == 3) {
					$this->log->write(print_r($product,true));
					$temp=intval($product['quantity']/$special_offer_info['product_quantity']);
					$discount = intval($product['quantity']/$special_offer_info['product_quantity']) * $product['price'] * $special_offer_info['percent'] / 100;

					if (array_key_exists($special_offer_id, $discount_offers)) {
						$discount_offers[$special_offer_id]['sum'] += $discount;
					} else {
						$discount_offers[$special_offer_id]['sum'] = $discount;
						$discount_offers[$special_offer_id]['offer_id'] = $special_offer_id;
						$discount_offers[$special_offer_id]['offer_name'] = $special_offer_info['name'];
						$discount_offers[$special_offer_id]['offer_type'] = $special_offer_info['offer_type'];
					}
				}

				if ($special_offer_info['offer_type'] == 6) {

					$prices = array_fill(0, $product['quantity'], $product['price']);
					if (array_key_exists($special_offer_id, $discount_offers)) {
						$discount_offers[$special_offer_id]['prices'] = array_merge($discount_offers[$special_offer_id]['prices'], $prices);
					} else {
						$discount_offers[$special_offer_id]['prices'] = $prices;
						$discount_offers[$special_offer_id]['offer_id'] = $special_offer_id;
						$discount_offers[$special_offer_id]['offer_name'] = $special_offer_info['name'];
						$discount_offers[$special_offer_id]['offer_type'] = $special_offer_info['offer_type'];
						$discount_offers[$special_offer_id]['product_quantity'] = $special_offer_info['product_quantity'];
						$discount_offers[$special_offer_id]['discount_percent'] = $special_offer_info['percent'];
					}
				}

				if ($special_offer_info['offer_type'] == 7) {
					$prices = array_fill(0, $product['quantity'], $product['price']);
					if (array_key_exists($special_offer_id, $discount_offers)) {
						$discount_offers[$special_offer_id]['qty'] += $product['quantity'];
						$discount_offers[$special_offer_id]['sum'] += $product['total'];
					} else {
						$discount_offers[$special_offer_id]['qty'] = $product['quantity'];
						$discount_offers[$special_offer_id]['sum'] = $product['total'];
						$discount_offers[$special_offer_id]['offer_id'] = $special_offer_id;
						$discount_offers[$special_offer_id]['offer_name'] = $special_offer_info['name'];
						$discount_offers[$special_offer_id]['offer_type'] = $special_offer_info['offer_type'];
						$discount_offers[$special_offer_id]['product_quantity'] = $special_offer_info['product_quantity'];
						$discount_offers[$special_offer_id]['product_sum'] = $special_offer_info['product_sum'];
						$discount_offers[$special_offer_id]['discount_percent'] = $special_offer_info['percent'];
					}
				}

				if ($special_offer_info['offer_type'] == 4) {
					if (array_key_exists($special_offer_id, $offers)) {
						$offers[$special_offer_id]['qty'] += $product['quantity'];
						$offers[$special_offer_id]['sum'] += $product['total'];
					} else {
						$offers[$special_offer_id]['qty'] = $product['quantity'];
						$offers[$special_offer_id]['sum'] = $product['total'];
						$offers[$special_offer_id]['offer_id'] = $special_offer_id;
						$offers[$special_offer_id]['offer_type'] = $special_offer_info['offer_type'];
						$offers[$special_offer_id]['product_quantity'] = $special_offer_info['product_quantity'];
						$offers[$special_offer_id]['product_sum'] = $special_offer_info['product_sum'];
						$offers[$special_offer_id]['discount_percent'] = $special_offer_info['percent'];
						$offers[$special_offer_id]['offer_name'] = $special_offer_info['name'];
					}
				}

			}
		}

		foreach ($discount_offers as $discount_offer) {
			if (($discount_offer['offer_type'] == 3)  && ($discount_offer['sum'] > 0)) {
				$total['totals'][] = array(
					'code'       => 'som_discount',
					'title'      => $this->language->get('text_som_discount') . ' &laquo;' . $discount_offer['offer_name'] . '&raquo;',
					'value'      => -$discount_offer['sum'],
					'sort_order' => $this->config->get('som_discount_sort_order')
				);

				$total['total'] -= $discount_offer['sum'];
			}

			if (($discount_offer['offer_type'] == 7)  && ($discount_offer['qty']>=$discount_offer['product_quantity']) && ($discount_offer['sum'] >=$discount_offer['product_sum'])) {
				$offer_sum_discount = $discount_offer['sum'] * $discount_offer['discount_percent'] / 100;
				if ($offer_sum_discount > 0) {
					$total['totals'][] = array(
						'code'       => 'som_discount',
						'title'      => $this->language->get('text_som_discount') . ' &laquo;' . $discount_offer['offer_name'] . '&raquo;',
							'value'      => - $offer_sum_discount,
							'sort_order' => $this->config->get('som_discount_sort_order')
					);

					$total['total'] -= $offer_sum_discount;
				}
			}

			if (($discount_offer['offer_type'] == 6)  && ($discount_offer['product_quantity'] > 0)) {
				rsort($discount_offer['prices']);
				$discount_sum = 0;
				for ($i = $discount_offer['product_quantity']-1, $size = count($discount_offer['prices']); $i < $size; $i+=$discount_offer['product_quantity']) {
					$discount_sum += $discount_offer['prices'][$i]*$discount_offer['discount_percent']/100;
				}

				if ($discount_sum > 0) {
					$total['totals'][] = array(
						'code'       => 'som_discount',
						'title'      => $this->language->get('text_som_discount') . ' &laquo;' . $discount_offer['offer_name'] . '&raquo;',
						'value'      => -$discount_sum,
						'sort_order' => $this->config->get('som_discount_sort_order')
					);

					$total['total'] -= $discount_sum;
				}
			}

		}

		$cart_discount = 0;
		foreach ($offers as $offer) {
			if ($offer['qty']>=$offer['product_quantity'] && $offer['sum']>=$offer['product_sum']) {
				if ($cart_discount < $offer['discount_percent']) {
					$cart_discount = $offer['discount_percent'];
					$offer_name = $offer['offer_name'];
				}
			}
		}

		if ($cart_discount > 0) {
			$sum_cart_discount = $total['total'] * $cart_discount / 100;
			$total['totals'][] = array(
				'code'       => 'som_discount',
				'title'      => $this->language->get('text_som_discount') . '&laquo;' . $offer_name . '&raquo;',
				'value'      => - $sum_cart_discount,
				'sort_order' => $this->config->get('som_discount_sort_order')
			);

			$total['total'] -= $sum_cart_discount;
		}
	}
}
