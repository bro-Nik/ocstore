<?php
require_once('catalog/controller/base/products_list.php');
require_once('catalog/controller/trait/template.php');

class ControllerProductSearch extends ControllerBaseProductsList {
	use \TemplateTrait;

	public function index() {
		$this->load->language('product/search');
		$this->load->model('catalog/category');
		$this->load->model('catalog/product');
		$this->load->model('tool/image');

		// Получаем параметры запроса
    $params = $this->getParams();

		$search = $this->request->get['search'] ?? '';
    $tag = $this->request->get['tag'] ?? $search;
    $description = $this->request->get['description'] ?? '';
    $category_id = (int)($this->request->get['category_id'] ?? 0);
    $sub_category = $this->request->get['sub_category'] ?? '';

		// Устанавливаем заголовок страницы
    if ($search) {
      $this->document->setTitle($this->language->get('heading_title') . ' - ' . $search);
    } elseif ($tag) {
      $this->document->setTitle($this->language->get('heading_title') . ' - ' . $this->language->get('heading_tag') . $tag);
    } else {
      $this->document->setTitle($this->language->get('heading_title'));
    }
		
		$this->document->setRobots('noindex,follow');

		$data['breadcrumbs'] = $this->prepareBreadcrumbs();
		$data['heading_title'] = $search ? $this->language->get('heading_title') . ' - ' . $search : $this->language->get('heading_title');

		// 3-уровневое меню категорий
    $data['categories'] = $this->getThreeLevelCategories();

		// Данные товаров
		$data['products'] = array();
		$product_total = 0;

		if ($search || $tag) {
      // Фильтры для поиска
      $filter_data = $this->prepareFilterData($params, [
        'filter_name' => $search,
        'filter_tag' => $tag,
        'filter_description' => $description,
        'filter_category_id' => $category_id,
        'filter_sub_category' => $sub_category
      ]);

      $product_total = $this->model_catalog_product->getTotalProducts($filter_data);
      $results = $this->model_catalog_product->getProducts($filter_data);
      $data['products'] = $this->prepareProducts($results);

			// $this->initOCFilter($data, $product_total);

      // Сортировка
      $url = $this->buildSearchUrl(['sort', 'order', 'page']);
      $data['sorts'] = $this->getSorts($this->url->link('product/search', $url));

      // Лимиты
      // $data['limits'] = $this->getLimits($this->url->link('product/search', $url));

      // Пагинация
      $data['pagination'] = $this->getPagination(
          $product_total,
          $params['page'],
          $params['limit'],
          $this->url->link('product/search', $url)
      );

      // Строка результатов
      $data['results'] = sprintf(
          $this->language->get('text_pagination'),
          ($product_total) ? (($params['page'] - 1) * $params['limit']) + 1 : 0,
          (($params['page'] - 1) * $params['limit']) > ($product_total - $params['limit']) ? $product_total : ((($params['page'] - 1) * $params['limit']) + $params['limit']),
          $product_total,
          ceil($product_total / $params['limit'])
      );

      // Логирование поиска для клиентов
      if ($search && $this->config->get('config_customer_search')) {
          $this->logCustomerSearch($search, $category_id, $sub_category, $description, $product_total);
      }
    }

		// Передаем параметры поиска в шаблон
    $data['search'] = $search;
    $data['description'] = $description;
    $data['category_id'] = $category_id;
    $data['sub_category'] = $sub_category;

    $data['sort'] = $params['sort'];
    $data['order'] = $params['order'];
    $data['limit'] = $params['limit'];

    $this->addCommonTemplateData($data);

		$this->response->setOutput($this->load->view('product/search', $data));
	}

	protected function prepareBreadcrumbs() {
    $breadcrumbs = [
      [
        'text' => $this->language->get('text_home'),
        'href' => $this->url->link('common/home')
      ],
      [
        'text' => $this->language->get('heading_title'),
        'href' => $this->url->link('product/search', $this->buildSearchUrl())
      ]
    ];

    return $breadcrumbs;
  }

	protected function buildSearchUrl($exclude = []) {
    $url = '';
    $params = ['search', 'tag', 'description', 'category_id', 'sub_category', 'sort', 'order', 'limit', 'page'];
    
    foreach ($params as $param) {
      if (!in_array($param, $exclude) && isset($this->request->get[$param])) {
        $value = $this->request->get[$param];
        $url .= '&' . $param . '=' . rawurlencode(is_array($value) ? implode(',', $value) : $value);
      }
    }
    
    return $url;
  }

	protected function getThreeLevelCategories() {
    $categories = [];
    $categories_1 = $this->model_catalog_category->getCategories(0);

    foreach ($categories_1 as $category_1) {
      $level_2_data = [];
      $categories_2 = $this->model_catalog_category->getCategories($category_1['category_id']);

      foreach ($categories_2 as $category_2) {
        $level_3_data = [];
        $categories_3 = $this->model_catalog_category->getCategories($category_2['category_id']);

        foreach ($categories_3 as $category_3) {
          $level_3_data[] = [
            'category_id' => $category_3['category_id'],
            'name' => $category_3['name'],
          ];
        }

        $level_2_data[] = [
            'category_id' => $category_2['category_id'],
            'name' => $category_2['name'],
            'children' => $level_3_data
        ];
      }

      $categories[] = [
          'category_id' => $category_1['category_id'],
          'name' => $category_1['name'],
          'children' => $level_2_data
      ];
    }

    return $categories;
  }

	protected function logCustomerSearch($search, $category_id, $sub_category, $description, $product_total) {
    $this->load->model('account/search');

    $search_data = [
      'keyword' => $search,
      'category_id' => $category_id,
      'sub_category' => $sub_category,
      'description' => $description,
      'products' => $product_total,
      'customer_id' => $this->customer->isLogged() ? $this->customer->getId() : 0,
      'ip' => $this->request->server['REMOTE_ADDR'] ?? ''
    ];

    $this->model_account_search->addSearch($search_data);
  }
}
