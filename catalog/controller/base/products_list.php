<?php
require_once('catalog/controller/trait/template.php');
require_once('catalog/controller/base/product_cart.php');

abstract class ControllerBaseProductsList extends ControllerBaseProductCart {
	use \TemplateTrait;

  protected function getPagination($total, $page, $limit, $url) {
    $this->addOCFilterParams($url);

    $pagination = new Pagination();
    $pagination->total = $total;
    $pagination->page = $page;
    $pagination->limit = $limit;
    $pagination->url = $url . '&page={page}';
    
    return $pagination->render();
  }

  protected function getSorts($url) {
    $this->addOCFilterParams($url);
    $this->load->language('product/product');
      
    // $sorts = [
    //   'p.sort_order-ASC'  => $this->language->get('text_default'),
    //   'pd.name-ASC'       => $this->language->get('text_name_asc'),
    //   'pd.name-DESC'      => $this->language->get('text_name_desc'),
    //   'p.price-ASC'       => $this->language->get('text_price_asc'),
    //   'p.price-DESC'      => $this->language->get('text_price_desc'),
    //   'rating-DESC'       => $this->language->get('text_rating_desc'),
    //   'rating-ASC'        => $this->language->get('text_rating_asc')
    // ];

    $sorts = [
      'p.price-ASC'       => 'Сначала недорогие',
      'p.price-DESC'      => 'Сначала дорогие',
      'p.viewed-ASC'          => 'Сначала популярные',
      'pd.name-ASC'       => 'По названию (по возрастанию)',
      'pd.name-DESC'      => 'По названию (по убыванию)'
    ];

    $data['sorts'] = [];
    foreach ($sorts as $key => $value) {
      $data['sorts'][] = [
        'text'  => $value,
        'value' => $key,
        'href'  => $this->url->link('product/category', $url . '&sort=' . explode('-', $key)[0] . '&order=' . explode('-', $key)[1])
      ];
    }

    return $data['sorts'];
  }

  // protected function getLimits($url) {
  //   $this->addOCFilterParams($url);
  //
  //   $limits = array_unique([
  //     $this->config->get('theme_' . $this->config->get('config_theme') . '_product_limit'),
  //     25, 50, 75, 100
  //   ]);
  //       
  //   sort($limits);
  //       
  //   $data['limits'] = [];
  //   foreach ($limits as $value) {
  //     $data['limits'][] = [
  //       'text'  => $value,
  //       'value' => $value,
  //       'href'  => $this->url->link('product/category', $url . '&limit=' . $value)
  //     ];
  //   }
  //       
  //   return $data['limits'];
  // }

  protected function buildUrl($exclude = []) {
    $url = '';
    $params = ['filter', 'sort', 'order', 'limit'];
    
    foreach ($params as $param) {
      if (!in_array($param, $exclude) && isset($this->request->get[$param])) {
        $url .= '&' . $param . '=' . $this->request->get[$param];
      }
    }
    
    return $url;
  }

  protected function getParams($defaults = []) {
    $disallow_params = explode("\r\n", $this->config->get('config_noindex_disallow_params'));
    
    $params = [
      'filter' => $defaults['filter'] ?? '',
      'sort'   => $defaults['sort'] ?? 'p.price',
      'order'  => $defaults['order'] ?? 'ASC',
      'page'   => 1,
      'limit'  => $this->config->get('theme_' . $this->config->get('config_theme') . '_product_limit')
    ];
    
    // Обработка каждого параметра
    $result = [];
    
    foreach ($params as $key => $default_value) {
      if (isset($this->request->get[$key])) {
        $value = $this->request->get[$key];
        $result[$key] = ($key === 'page' || $key === 'limit') ? (int)$value : $value;
        
        if (!in_array($key, $disallow_params, true) && $this->config->get('config_noindex_status')) {
          $this->document->setRobots('noindex,follow');
        }
      } else {
        $result[$key] = $default_value;
      }
    }
    
    return $result;
  }

  protected function setMetaData($page_info, $page) {
    $base_title = $page_info['meta_title'] ?: $page_info['name'];
    if ($page == 1) {
      $this->document->setTitle($base_title);
      $this->document->setDescription($page_info['meta_description']);
    } else {
      $this->document->setTitle($base_title . ' | Страница ' . $page);
      $this->document->setDescription($page_info['meta_description'] . ' | Страница ' . $page);
    }
    $this->document->setKeywords($page_info['meta_keyword']);
  }

  protected function initOCFilter(&$data, $product_total) {
    if ($this->registry->get('ocfilter') && $this->ocfilter->startup()) {
      $this->ocfilter->api->setProductListControllerData($data, $product_total);
    }
  }

  protected function addOCFilterParams($url) {
    if (isset($url) && $this->registry->get('ocfilter') && $this->ocfilter->startup() && $this->ocfilter->api->isSelected()) {
      $url .= '&' . $this->ocfilter->api->getParamsIndex() . '=' . $this->ocfilter->api->getParamsString();

      if (isset($this->request->get['ocfilter_placement'])) {
        $url .= '&ocfilter_placement=' . $this->request->get['ocfilter_placement'];
      }
    }
  }

  protected function prepareFilterData(array $params, array $filters = []) {
    $filter_data = [
      'sort'   => $params['sort'],
      'order'  => $params['order'],
      'start'  => ($params['page'] - 1) * $params['limit'],
      'limit'  => $params['limit']
    ];

    // Добавляем дополнительные фильтры (если переданы)
    foreach ($filters as $key => $value) {
      if ($value !== null) {
        $filter_data[$key] = $value;
      }
    }

    // OCFilter: автоматически добавляем подкатегории, если включено
    if ($this->registry->get('ocfilter') && $this->ocfilter->startup() && $this->ocfilter->api->useSubCategory()) {
      $filter_data['filter_sub_category'] = true;
    }

    return $filter_data;
  }

  protected function noindexCheck($obj) {
		if ($obj['noindex'] <= 0 && $this->config->get('config_noindex_status')) {
			$this->document->setRobots('noindex,follow');
		}
  }

  protected function setTitleDescription(&$data, $obj, $page) {
		$data['heading_title'] = $obj['meta_h1'] ?? $obj['name'];
		if ($page > 1) {
			$data['heading_title'] = $data['heading_title'] . ' - Страница ' . $page;
		} else {
      $data['description'] = html_entity_decode($obj['description'] ?? '', ENT_QUOTES, 'UTF-8');
		}
  }

  protected function getImage($obj) {
    $image = '';
    if ($obj['image']) {
      $image = $this->model_tool_image->resize($obj['image'], 
        $this->config->get('theme_' . $this->config->get('config_theme') . '_image_category_width'), 
        $this->config->get('theme_' . $this->config->get('config_theme') . '_image_category_height'));
      $this->document->setOgImage($image);
    }
    return $image;
  }

  protected function setCanonicalLinks($route, $params, $page, $total, $limit) {
    if ($page == 1) {
      $this->document->addLink($this->url->link($route, $params), 'canonical');
    } elseif ($page == 2) {
      $this->document->addLink($this->url->link($route, $params), 'prev');
    } else {
      $this->document->addLink($this->url->link($route, $params . '&page=' . ($page - 1)), 'prev');
    }

    if ($limit && ceil($total / $limit) > $page) {
      $this->document->addLink($this->url->link($route, $params . '&page=' . ($page + 1)), 'next');
    }
  }

  protected function getPopularProducts($filters, $limit = 10) {
    $this->load->model('catalog/product');
    $this->load->model('catalog/category');
    
    $filter_data = [
      'sort'  => 'p.viewed',
      'order' => 'DESC',
      'start' => 0,
      'limit' => $limit,
      'filter_quantity' => 1 // Только товары в наличии
    ];

    foreach ($filters as $key => $value) {
      if ($value !== null) {
        $filter_data[$key] = $value;
      }
    }

    // Получаем все вложенные категории
    if (isset($filter_data['filter_category_id'])) {
      $filter_data['filter_sub_category'] = true;
    }

    $products = $this->model_catalog_product->getProducts($filter_data);
    $result = '';
    
    if ($products) {
      $prepared = $this->prepareProductsData($products);
      $prepared['id'] = 'slider_popular_products';
      $prepared['title'] = 'Популярные товары';
      $result = $this->load->view('product/carousel_product', $prepared);
      
    }
    
    return $result;
  }

	protected function getNewProducts($filters, $limit = 10) {
    $this->load->model('catalog/product');
    
    // Рассчитываем дату 6 месяцев назад
    $sixMonthsAgo = date('Y-m-d', strtotime('-6 months'));
    
    $filter_data = array(
        'filter_date_added'      => $sixMonthsAgo, // Фильтр по дате добавления
        'sort'                   => 'p.date_added', // Сортировка по новизне
        'order'                  => 'DESC',         // Сначала самые новые
        'start'                  => 0,
        'limit'                  => $limit
    );

    // Добавляем дополнительные фильтры (если переданы)
    foreach ($filters as $key => $value) {
      if ($value !== null) {
        $filter_data[$key] = $value;
      }
    }

    $result = '';
    $new_products = $this->model_catalog_product->getProducts($filter_data);
    
    if ($new_products) {
        $products = $this->prepareProductsData($new_products);
        $products['id'] = 'slider_new_products';
        $products['title'] = 'Новинки';
        $result = $this->load->view('product/carousel_product', $products);
    }
    
    return $result;
	}

	protected function ErrorPage() {
    $this->document->setTitle($this->language->get('text_error'));
    $this->response->addHeader($this->request->server['SERVER_PROTOCOL'] . ' 404 Not Found');
    $data['heading_title'] = $this->language->get('text_error');
    $data['text_error'] = $this->language->get('text_error');
		// $data['breadcrumbs'] = $this->prepareBreadcrumbs();
		$data['continue'] = $this->url->link('common/home');

		$this->response->addHeader($this->request->server['SERVER_PROTOCOL'] . ' 404 Not Found');

    $this->addCommonTemplateData($data);

    $this->response->setOutput($this->load->view('error/not_found', $data));
	}
}

