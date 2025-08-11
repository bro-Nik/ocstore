<?php
require_once('catalog/controller/base/products_list.php');

class ControllerProductSpecial extends ControllerBaseProductsList {
	public function index() {
		$this->load->language('product/special');
		$this->load->model('catalog/product');
		$this->load->model('tool/image');

		// Получаем параметры запроса с проверкой noindex
    $params = $this->getParams();

		// Устанавливаем заголовок страницы
    $this->document->setTitle($this->language->get('heading_title'));

		$data['breadcrumbs'] = $this->prepareBreadcrumbs();
    $data['heading_title'] = $this->language->get('heading_title');

		// Получаем товары со скидками
    $filter_data = $this->prepareFilterData($params);
    $product_total = $this->model_catalog_product->getTotalProductSpecials();
    $results = $this->model_catalog_product->getProductSpecials($filter_data);
    $data['products'] = $this->prepareProducts($results);

		// Сортировка и лимиты
    $url = $this->buildUrl(['page']);
    $data['sorts'] = $this->getSorts($this->url->link('product/special', $url), true);
    $data['limits'] = $this->getLimits($this->url->link('product/special', $url));

    // Пагинация
    $data['pagination'] = $this->getPagination($product_total, $params['page'], $params['limit'], $this->url->link('product/special', $url));

		// Строка результатов
    $data['results'] = sprintf(
        $this->language->get('text_pagination'),
        ($product_total) ? (($params['page'] - 1) * $params['limit']) + 1 : 0,
        (($params['page'] - 1) * $params['limit']) > ($product_total - $params['limit']) ? $product_total : ((($params['page'] - 1) * $params['limit']) + $params['limit']),
        $product_total,
        ceil($product_total / $params['limit'])
    );

		// Canonical и prev/next ссылки
    $this->setCanonicalLinks('product/special', '', $params['page'], $product_total, $params['limit']);

		$data['sort'] = $params['sort'];
    $data['order'] = $params['order'];
    $data['limit'] = $params['limit'];

		$data = $this->addCommonTemplateData($data);

    $this->response->setOutput($this->load->view('product/special', $data));
	}

	protected function prepareBreadcrumbs() {
    $breadcrumbs = [
      [
        'text' => $this->language->get('text_home'),
        'href' => $this->url->link('common/home')
      ],
      [
        'text' => $this->language->get('heading_title'),
        'href' => $this->url->link('product/special')
      ]
    ];

    return $breadcrumbs;
  }
}
