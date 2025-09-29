<?php
require_once('catalog/controller/base/product_cart.php');

class ControllerExtensionModuleSliderTabs extends ControllerBaseProductCart {
    public function index($settings) {
        if (!$settings || !$settings['status']) {
            return;
        }
        $this->load->model('catalog/product');
        
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
                    'sort' => $slider_settings['sort'],
                    'filter_quantity' => true,
                );
                
                if ($slider_settings['category_id'] == 'featured' && !empty($slider_settings['featured'])) {
                    // Режим "Выборочные товары"
                    $products_ids = array_column($slider_settings['featured'], 'product_id');
                    $filter_data['filter_product_ids'] = $products_ids;
                    $results = $this->model_catalog_product->getProducts($filter_data);

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
                
                $data[$slider_key] = $this->prepareProductsData($results, $slider_settings);
            }
        }
        
        return $this->load->view('extension/module/slider_tabs', $data);
    }
}
