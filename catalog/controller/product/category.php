<?php

require_once('catalog/controller/base/products_list.php');
require_once('catalog/controller/trait/template.php');

class ControllerProductCategory extends ControllerBaseProductsList {
	use \TemplateTrait;

  public function index() {
    $this->load->language('product/category');
    $this->load->model('catalog/category');
    $this->load->model('catalog/product');
    $this->load->model('tool/image');

    // Получаем параметры запроса с проверкой noindex
    $params = $this->getParams();

		$category_id = 0;
		if (isset($this->request->get['path'])) {
			$parts = explode('_', (string)$this->request->get['path']);
			$category_id = (int)array_pop($parts);
		}

    // Рекомендуемые категории
    // $data['related_categories'] = $this->load->controller('extension/module/related_categories/getRelatedCategories', 'category_id=' . $category_id);
    
    // Популярные категории
    $data['popular_subcategories'] = $this->getPopularSubcategories($category_id);
    
    // Рекомендуемые услуги
		$data['services'] = $this->getRelatedServices($category_id);

    $category_info = $this->model_catalog_category->getCategory($category_id);
		$data['breadcrumbs'] = $this->prepareBreadcrumbs($category_info);

    if ($category_info) {
		  // Данные для статистики
      $data['counter_data'] = [ 'type' => 'category', 'id' => $category_id ];

			$this->noindexCheck($category_info);
			$this->setTitleDescription($data, $category_info, $params['page']);
      $this->setMetaData($category_info, $params['page']);
      $data['thumb'] = $this->getImage($category_info);

      // Подкатегории
    	$categories = $this->model_catalog_category->getCategories($category_id);
			// Если категория одна - редирект на неё
			// if (count($categories) == 1) {
   //      $this->response->redirect($this->url->link('product/category', 'path=' . $categories[0]['category_id']));
   //    }

      // Если несколько категорий - показываем их список
      if (count($categories)) {
      	$data['categories'] = $this->getSubCategories($categories);

 				// Жесткое отключение OCFilter
        if ($this->registry->has('ocfilter')) {
          $this->registry->set('ocfilter', null); // Уничтожаем экземпляр
        }
        unset($this->session->data['ocfilter']);

        // Добавляем заголовок для списка категорий
        $data['text_categories'] = $this->language->get('text_categories');

        // Рекомендуемые товары
        $data['recommended_products'] = $this->load->controller('extension/module/featured_product');
        $data['featured_articles'] = $this->load->controller('extension/module/featured_article');
				$data['popular_products'] = $this->getPopularProducts(['filter_category_id' => $category_id]);
				$data['new_products'] = $this->getNewProducts(['filter_category_id' => $category_id]);
			}
			// Если нет категорий - показываем стандартный список товаров
			else {
      	// Товары
      	$filter_data = $this->prepareFilterData($params, [
    			'filter_category_id' => $category_id,
    			'filter_filter'     => $params['filter'] // фильтры по характеристикам
				]);
      	$product_total = $this->model_catalog_product->getTotalProducts($filter_data);
      	$results = $this->model_catalog_product->getProducts($filter_data);
      	$data['products'] = $this->prepareProducts($results, []);

				$this->initOCFilter($data, $product_total);

      	// Сортировка и лимиты
      	$url = $this->buildUrl(['filter', 'limit']);
      	$data['sorts'] = $this->getSorts($this->url->link('product/category', 'path=' . $this->request->get['path'] . $url));
      	$data['limits'] = $this->getLimits($this->url->link('product/category', 'path=' . $this->request->get['path'] . $url));

      	// Пагинация
      	$data['pagination'] = $this->getPagination(
        	$product_total,
        	$params['page'],
        	$params['limit'],
        	$this->url->link('product/category', 'path=' . $this->request->get['path'] . $url)
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
      	$this->setCanonicalLinks('product/category', 'path=' . $category_id, $params, $params['page'], $product_total, $params['limit']);

      	$data['sort'] = $params['sort'];
      	$data['order'] = $params['order'];
      	$data['limit'] = $params['limit'];
			}

      $this->addCommonTemplateData($data);

      $this->response->setOutput($this->load->view('product/category', $data));
    } else {
			$this->ErrorPage();
    }
  }

	protected function getSubCategories($results) {
    
    $categories = [];
    foreach ($results as $result) {
      $categories[] = [
        'name' => $result['name'],
        'thumb' => $this->model_tool_image->resize($result['image'], 
                  $this->config->get('theme_' . $this->config->get('config_theme') . '_image_category_width'), 
                  $this->config->get('theme_' . $this->config->get('config_theme') . '_image_category_height')),
        'href' => $this->url->link('product/category', 'path=' . $this->request->get['path'] . '_' . $result['category_id']),
        'filters'=> $this->getPopularFilters($result['category_id'])
      ];
    }
    
    return $categories;
	}

	protected function prepareBreadcrumbs($category_info) {
    $breadcrumbs = array();
    $breadcrumbs[] = array(
      'text' => $this->language->get('text_home'),
      'href' => $this->url->link('common/home')
    );

    // Обработка path для категорий
    $category_path = '';
    
    if (isset($this->request->get['path'])) {
      $parts = explode('_', (string)$this->request->get['path']);
      
      $current_path = '';
      foreach ($parts as $path_id) {
        $current_path = $current_path ? $current_path . '_' . (int)$path_id : (int)$path_id;
        $category = $this->model_catalog_category->getCategory($path_id);
        
        if ($category) {
          $breadcrumbs[] = array(
            'text' => $category['name'],
            'href' => $this->url->link('product/category', 'path=' . $current_path . $this->buildUrl(['path']))
          );
        }
      }
    }
		return $breadcrumbs;
	}

	protected function getRelatedServices($category_id) {
    $services = array();
    $services_ids = $this->model_catalog_category->getServiceRelated($category_id);
    
    if ($services_ids) {
      $this->load->model('blog/article');
      
      foreach ($services_ids as $article_id) {
        $article_info = $this->model_blog_article->getArticle($article_id);
        
        if ($article_info) {
          $services[] = array(
            'article_id'  => $article_info['article_id'],
            'name'        => $article_info['name'],
            'image'       => $article_info['image'] ? $this->model_tool_image->resize($article_info['image'], 
                            $this->config->get('theme_' . $this->config->get('config_theme') . '_image_category_width'), 
                            $this->config->get('theme_' . $this->config->get('config_theme') . '_image_category_height')) : '',
            'description' => utf8_substr(strip_tags(html_entity_decode($article_info['description'], ENT_QUOTES, 'UTF-8')), 0, 100) . '..',
            'href'        => $this->url->link('blog/article', 'article_id=' . $article_info['article_id'])
          );
        }
      }
    }
		return $services;
	}

	public function getPopularFilters($category_id, $limit = 4) {
    $results = $this->model_catalog_category->getPopularFilters($category_id, $limit);

    $pages = array();
    foreach ($results as $result) {
      $pages[] = array(
        'name' => $result['name'],
        'href' => $this->url->link('product/category', 'path=' . $category_id . '&filter_ocfilter=' . $result['keyword'])
      );
    }
    return $pages;
	}

	public function getPopularSubcategories($category_id) {

    $data['categories'] = array();
    $this->load->model('catalog/category');
    $this->load->model('extension/module/ocfilter');
    // $this->load->model('extension/module/related_categories');

    $categories = $this->model_catalog_category->getPopularSubcategories($category_id, 5);
    
    foreach ($categories as $category) {
      $category_info = $this->model_catalog_category->getCategory($category['category_id']);
      
      if ($category_info) {
            
        // Загрузка изображения категории
        if ($category_info['image']) {
            $image = $this->model_tool_image->resize($category_info['image'], 300, 300);
        } else {
            $image = $this->model_tool_image->resize('placeholder.png', 300, 300);
        }
        
        $filters = $this->getPopularFilters($category['category_id']);
        if ($filters) {
          $data['categories'][] = array(
              'category_id' => $category_info['category_id'],
              'name' => $category_info['name'],
              'image' => $image,
              'href' => $this->url->link('product/category', 'path=' . $category_info['category_id']),
              'filters'=> $filters
          );
        }
      }
    }
    return $this->load->view('extension/module/related_categories', $data);
  }
}
