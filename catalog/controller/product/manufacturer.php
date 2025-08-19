<?php

require_once('catalog/controller/base/products_list.php');

class ControllerProductManufacturer extends ControllerBaseProductsList {
	public function index() {
		$this->load->language('product/manufacturer');
		$this->load->model('catalog/manufacturer');

		$this->document->setTitle($this->language->get('heading_title'));

		$data['breadcrumbs'] = $this->prepareBreadcrumbs();
		$data['categories'] = $this->getManufacturersByAlphabet();

    $data = $this->addCommonTemplateData($data);

		$this->response->setOutput($this->load->view('product/manufacturer_list', $data));
	}

	public function info() {
		$this->load->language('product/manufacturer');
		$this->load->model('catalog/manufacturer');
		$this->load->model('catalog/product');
    $this->load->model('catalog/category');
		$this->load->model('tool/image');

		// Получаем параметры запроса с проверкой noindex
    $params = $this->getParams();

		$manufacturer_id = isset($this->request->get['manufacturer_id']) ? (int)$this->request->get['manufacturer_id'] : 0;
		$manufacturer_info = $this->model_catalog_manufacturer->getManufacturer($manufacturer_id);
		$data['breadcrumbs'] = $this->prepareBreadcrumbs($manufacturer_info);

		if ($manufacturer_info) {
		  // Данные для статистики
      $data['counter_data'] = [ 'type' => 'manufacturer', 'id' => $manufacturer_id ];

			$this->noindexCheck($manufacturer_info);
			$this->setTitleDescription($data, $manufacturer_info, $params['page']);
      $this->setMetaData($manufacturer_info, $params['page']);
      $data['thumb'] = $this->getImage($manufacturer_info);

      // Подкатегории
			$manufacturer_categories = $this->model_catalog_manufacturer->getManufacturerCategories($manufacturer_id);
			// Если категория одна - редирект на неё
			if (count($manufacturer_categories) == 1) {
        $this->response->redirect($this->url->link('product/category', 'path=' . $manufacturer_categories[0]['category_id'] . '&manufacturer_id=' . $manufacturer_id));
      }

      // Если у производителя несколько категорий - показываем их список
      if (count($manufacturer_categories) > 1) {
      	$data['categories'] = $this->getSubCategories($manufacturer_categories, $manufacturer_id);

 				// Жесткое отключение OCFilter
        if ($this->registry->has('ocfilter')) {
          $this->registry->set('ocfilter', null); // Уничтожаем экземпляр
        }
        unset($this->session->data['ocfilter']);
            
        // Добавляем заголовок для списка категорий
        $data['text_categories'] = $this->language->get('text_categories');

        // Популярные товары
				$data['popular_products'] = $this->getPopularProducts(['filter_manufacturer_id' => $manufacturer_id]);

        // Популярные товары
				$data['new_products'] = $this->getNewProducts(['filter_manufacturer_id' => $manufacturer_id]);

				// Отзывы
        $data['reviews'] = $this->getManufacturerReviews($manufacturer_id, 10);
			
			}
			// Если нет категорий - показываем стандартный список товаров
			else {
      	// Товары
				$filter_data = $this->prepareFilterData($params, ['filter_manufacturer_id' => $manufacturer_id]);
				$product_total = $this->model_catalog_product->getTotalProducts($filter_data);
      	$results = $this->model_catalog_product->getProducts($filter_data);
      	$products_data = $this->prepareProductsData($results, []);
      	// $data['products'] = $products_data['products'];
      	$data['products'] = [];

				$this->initOCFilter($data, $product_total);

				// Сортировка и лимиты
      	$url = $this->buildUrl();
      	$data['sorts'] = $this->getSorts($this->url->link('product/manufacturer/info', 'manufacturer_id=' . $manufacturer_id . $url));
      	$data['limits'] = $this->getLimits($this->url->link('product/manufacturer/info', 'manufacturer_id=' . $manufacturer_id . $url));

				// Пагинация
      	$data['pagination'] = $this->getPagination(
        	$product_total,
        	$params['page'],
        	$params['limit'],
        	$this->url->link('product/manufacturer/info', 'manufacturer_id=' . $manufacturer_id . $url)
      	);

				// Строка результатов
      	$data['results'] = sprintf(
        	$this->language->get('text_pagination'),
        	($product_total) ? (($params['page'] - 1) * $params['limit']) + 1 : 0,
        	(($params['page'] - 1) * $params['limit']) > ($product_total - $params['limit']) ? $product_total : ((($params['page'] - 1) * $params['limit']) + $params['limit']),
        	$product_total,
        	ceil($product_total / $params['limit'])
      	);

				// Canonical и prev/next ссылки
      	$this->setCanonicalLinks('product/manufacturer/info', 'manufacturer_id=' . $manufacturer_id, $params['page'], $product_total, $params['limit']);

      	$data['sort'] = $params['sort'];
      	$data['order'] = $params['order'];
      	$data['limit'] = $params['limit'];
      }

      $data = $this->addCommonTemplateData($data);

			$this->response->setOutput($this->load->view('product/manufacturer_info', $data));
		} else {
			$this->ErrorPage();
		}
	}

	protected function prepareBreadcrumbs($manufacturer_info=null) {
		$breadcrumbs = [
      [
        'text' => $this->language->get('text_home'),
        'href' => $this->url->link('common/home')
      ],
      [
        'text' => $this->language->get('text_brand'),
        'href' => $this->url->link('product/manufacturer')
      ]
    ];

		if ($manufacturer_info) {
			$breadcrumbs[] = 
				[
          'text' => $manufacturer_info['name'],
          'href' => $this->url->link('product/manufacturer/info', 'manufacturer_id=' . $manufacturer_info['manufacturer_id'])
        ];
		}

		return $breadcrumbs;
	}

	protected function getManufacturersByAlphabet() {
    $results = $this->model_catalog_manufacturer->getManufacturers();
    $manufacturers = [];

    foreach ($results as $result) {
      $key = is_numeric(utf8_substr($result['name'], 0, 1)) ? '0 - 9' : utf8_substr(utf8_strtoupper($result['name']), 0, 1);

      if (!isset($manufacturers[$key])) {
        $manufacturers[$key]['name'] = $key;
      }

      $manufacturers[$key]['manufacturer'][] = [
        'name' => $result['name'],
        'href' => $this->url->link('product/manufacturer/info', 'manufacturer_id=' . $result['manufacturer_id'])
      ];
    }

    return $manufacturers;
  }

	protected function getSubCategories($manufacturer_categories, $manufacturer_id) {
    $categories = array();
    foreach ($manufacturer_categories as $category) {
      $category_data = array(
        'name' => $category['name'],
        'href' => $this->url->link('product/category', 'path=' . $category['category_id'] . '&manufacturer_id=' . $manufacturer_id)
      );
        
      // Добавляем изображение только если оно есть
      if (!empty($category['image']) && file_exists(DIR_IMAGE . $category['image'])) {
        $category_data['thumb'] = $this->model_tool_image->resize(
          $category['image'],
          $this->config->get('theme_' . $this->config->get('config_theme') . '_image_category_width'),
          $this->config->get('theme_' . $this->config->get('config_theme') . '_image_category_height')
        );
      }
      
      $categories[] = $category_data;
    }
    
    return $categories;
	}

	protected function getManufacturerReviews($manufacturer_id, $limit) {
		$reviews = $this->model_catalog_manufacturer->getManufacturerReviews($manufacturer_id, $limit);
		$reviews_data = array();
		$reviews_data['reviews'] = array();
		$reviews_data['title'] = 'Отзывы на товары';
        
    foreach ($reviews as $review) {
      $reviews_data['reviews'][] = array(
        'author'     => $review['author'],
        'text'       => nl2br($review['text']),
        'rating'     => (int)$review['rating'],
        'date_added' => date($this->language->get('date_format_short'), strtotime($review['date_added'])),
        'product_name'    => $review['product_name'],
        'product_href'=> $this->url->link('product/product', 'product_id=' . $review['product_id']),
        'product_image' 		 => $this->model_tool_image->resize(
          $review['product_image'],
          $this->config->get('theme_' . $this->config->get('config_theme') . '_image_category_width'),
          $this->config->get('theme_' . $this->config->get('config_theme') . '_image_category_height')
        )
      );
    }
		if ($reviews_data['reviews']) {
	  	return $this->load->view('product/partials/slider_reviews', $reviews_data);
		}
	}

}
