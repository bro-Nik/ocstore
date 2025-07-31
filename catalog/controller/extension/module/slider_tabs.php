<?php
class ControllerExtensionModuleSliderTabs extends Controller {
    public function index() {
        $this->load->model('setting/setting');
        $this->load->model('tool/image');
        $this->load->model('catalog/product');
        
        // Загрузка всех настроек
        $settings = $this->model_setting_setting->getSetting('home');
		$settings = $settings['home_sliders1'];
        // $settings = $settings['']
		    // $data['sliders_1'] = $settings['home_sliders1'];
        $data = $settings;
        
        for ($i = 1; $i <= 4; $i++) {
            $slider_key = 'slider_' . $i;
            $data[$slider_key] = array();
            
            if (!empty($settings[$slider_key]['status'])) {
                // Подготовка данных слайдера
                $slider_settings = $settings[$slider_key];
                $data[$slider_key] = array(
                    'title' => $slider_settings['title'],
                    'url_all' => $slider_settings['url_all'],
                    'autoscroll' => $slider_settings['autoscroll'],
                    'products' => array()
                );
                
                // Получаем товары в зависимости от настроек
                $filter_data = array(
                    'limit' => $slider_settings['limit'],
                    'sort' => $slider_settings['sort']
                );
                
                if ($slider_settings['category_id'] == 'featured' && !empty($slider_settings['featured'])) {
                    // Режим "Выборочные товары"
                    $product_ids = array_column($slider_settings['featured'], 'product_id');
                    $filter_data['filter_product_ids'] = $product_ids;
                    $results = $this->model_catalog_product->getProductsByIds($filter_data);
                } else {
                    // Режим категории или все товары
                    if ($slider_settings['category_id'] > 0) {
                        $filter_data['filter_category_id'] = $slider_settings['category_id'];
                    }
                    if ($slider_settings['manufacturer_id'] > 0) {
                        $filter_data['filter_manufacturer_id'] = $slider_settings['manufacturer_id'];
                    }
                    $results = $this->model_catalog_product->getProducts($filter_data);
                }
                
                // Формируем данные товаров
                foreach ($results as $result) {
                    if (is_file(DIR_IMAGE . $result['image'])) {
                        $image = $this->model_tool_image->resize($result['image'], 300, 300);
                    } else {
                        $image = $this->model_tool_image->resize('placeholder.png', 300, 300);
                    }

		            $this->load->model('revolution/revolution');
					$description = $this->model_revolution_revolution->getAttrText($result['product_id']);
				
				    $product_in_cart = false;
					$products_in_cart = $this->cart->getProducts();
					foreach ($products_in_cart as $product_cart) {
						if ($product_cart['product_id'] == $result['product_id']) {
							$product_in_cart = true;
						}
					}
					$compare_class = '';
				    if (isset($this->session->data['compare'])) {
					    if (in_array($result['product_id'], $this->session->data['compare'])) {
						    $compare_class = 'in-compare';
					    }
				    }

				    $wishlist_class = '';
				    if (isset($this->session->data['wishlist'])) {
					    if (in_array($result['product_id'], $this->session->data['wishlist'])) {
						    $wishlist_class = 'in-wishlist';
					    }
				    }
                    
                    // ToDo переделать по нормальному статус склада
                    $data[$slider_key]['products'][] = array(
                        'product_id' => $result['product_id'],
                        'thumb' => $image,
                        'name' => $result['name'],
                        'rating' => $result['rating'],
                        'price' => $this->currency->format($result['price'], $this->session->data['currency']),
                        'price_number' => $result['price'],
                        'stiker_ean' => $result['ean'],
                        'stiker_jan' => $result['jan'],
                        'stiker_isbn' => $result['isbn'],
				        'stiker_sklad_status' => $result['stock_status'],
                        'quantity' => $result['quantity'],
                        'product_in_cart' => $product_in_cart,
                        'compare_class' => $compare_class,
                        'wishlist_class' => $wishlist_class,
                        'description' => $description,
                        'href' => $this->url->link('product/product', 'product_id=' . $result['product_id'])
                    );
                }
            }
        }
        
        return $this->load->view('extension/module/slider_tabs', $data);
    }
}
