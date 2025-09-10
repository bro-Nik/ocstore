<?php
require_once('catalog/controller/base/product_cart.php');
require_once(DIR_SYSTEM . 'library/trait/cache.php');

class ControllerProductModuleSimilarProducts extends ControllerBaseProductCart {
	use \CacheTrait;

    public function index() {
        $product_id =$this->request->get['revproduct_id'];

		$cache_key = 'product.similarProducts.' . $product_id;
		$cache = $this->getCache($cache_key);
        if ($cache !== false) {
            $this->response->setOutput($cache);
            return;
        }

        if ($product_id) {
            $this->load->model('setting/setting');
            $all_settings = $this->model_setting_setting->getSetting('catalog') ?? [];
            $setting = $all_settings['catalog_similar_products'] ?? [];

            $this->load->model('catalog/module/similar_products');

            $data['products'] = array();
            $filter_data = array(
                'limit'               => $setting['limit'],
                'delimiter'           => $setting['delimiter'],
                'price_percent'       => $setting['price_percent'],
                'diff'                => $setting['diff'],
                'use_excluded_attributes' => isset($setting['use_excluded_attributes']) ? $setting['use_excluded_attributes'] : 1,
                'excluded_attributes' => isset($setting['excluded_attributes']) ? $setting['excluded_attributes'] : array(),
                'product_id'          => $product_id,
            );

            $results = $this->model_catalog_module_similar_products->getProductSimilar($filter_data);
            $data = $this->prepareProductsData($results, $setting);

            $result = null;
            if ($results) {
                $data['id'] = 'product_related';
                $result = $this->load->view('product/carousel_product', $data);
            }
            
            $this->setCache($cache_key, $result, 108000);
            if ($result) {
                $this->response->setOutput($result);
            }
        }
    }
}
