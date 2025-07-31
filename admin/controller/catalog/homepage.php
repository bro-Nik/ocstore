<?php
class ControllerCatalogHomepage extends Controller {
    private $error = array();

    public function index() {
        $this->load->language('catalog/homepage');

        $this->document->setTitle($this->language->get('heading_title'));

        $this->load->model('setting/setting');

        if (($this->request->server['REQUEST_METHOD'] == 'POST') && $this->validate()) {

		    $this->load->model('tool/image');


		    $data['placeholder'] = $this->model_tool_image->resize('no_image.png', 100, 100);

            // Обработка слайдшоу:
            $slideshow_data = [
                'status'            => $this->request->post['slideshow']['status'] ?? '',
                'fullscreen'            => $this->request->post['slideshow']['fullscreen'] ?? '',
                'slides_in_line'    => $this->request->post['slideshow']['slides_in_line'] ?? '',
                'width'             => $this->request->post['slideshow']['width'] ?? '',
                'height'            => $this->request->post['slideshow']['height'] ?? '',
                'slides'            => array(),
            ];

            if (!empty($this->request->post['slide']) && is_array($this->request->post['slide'])) {
                foreach ($this->request->post['slide'] as $key => $slide) {
                    $slideshow_data['slides'][] = [
                        'image'       => $slide['image'],
                        'title'       => $slide['title'] ?? '',
                        'description' => $slide['description'] ?? '',
                        'link_title'  => $slide['link_title'] ?? '',
                        'link'        => $slide['link'] ?? '',
                        'sort_order'  => $slide['sort_order'] ?? 0
                    ];
                }
            }
            // Обработка рекомендаций
            $recommendations_data = [
                'status'            => $this->request->post['recommendations']['status'] ?? '',
                'width'             => $this->request->post['recommendations']['width'] ?? '',
                'height'            => $this->request->post['recommendations']['height'] ?? '',
                'slides'            => array(),
            ];

            if (!empty($this->request->post['recommendation']) && is_array($this->request->post['recommendation'])) {
                foreach ($this->request->post['recommendation'] as $key => $slide) {
                    $recommendations_data['slides'][] = [
                        'image'       => $slide['image'],
                        'title'       => $slide['title'] ?? '',
                        'description' => $slide['description'] ?? '',
                        'link_title'  => $slide['link_title'] ?? '',
                        'link'        => $slide['link'] ?? '',
                        'sort_order'  => $slide['sort_order'] ?? 0
                    ];
                }
            }


			$this->load->model('extension/module/related_categories');
			$this->model_extension_module_related_categories->saveRelatedCategories('homepage', $this->request->post);

            // Обработка слайдеров1
            $sliders1_data = [
                'status'            => $this->request->post['sliders_1']['status'] ?? '',
                'title'             => $this->request->post['sliders_1']['title'] ?? '',
            ];

            for ($i = 1; $i <= 4; $i++) {
                $slider_data = $this->request->post['slider_' . $i] ?? array();
                
                $sliders1_data['slider_' . $i] = array(
                    'status' => $slider_data['status'] ?? 0,
                    'title' => $slider_data['title'] ?? '',
                    'url_all' => $slider_data['url_all'] ?? '',
                    'limit' => $slider_data['limit'] ?? 5,
                    'category_id' => $slider_data['category_id'] ?? 0,
                    'manufacturer_id' => $slider_data['manufacturer_id'] ?? 0,
                    'sort' => $slider_data['sort'] ?? 'p.date_added',
                    'autoscroll' => $slider_data['autoscroll'] ?? 0
                );
                
                // Обработка выбранных товаров для featured
                if (isset($slider_data['featured']) && is_array($slider_data['featured'])) {
                    $sliders1_data['slider_' . $i]['featured'] = array();
                    
                    $this->load->model('catalog/product');
                    foreach ($slider_data['featured'] as $product_id) {
                        $product_info = $this->model_catalog_product->getProduct($product_id);
                        if ($product_info) {
                            $sliders1_data['slider_' . $i]['featured'][] = array(
                                'product_id' => $product_id,
                                'name' => $product_info['name']
                            );
                        }
                    }
                }
            }

            // Обработка блога
            $blog_data = array(
                'status' => isset($this->request->post['blog']['status']) ?? 0,
                'title' => isset($this->request->post['blog']['title']) ?? 'Новости',
                'url_all_text' => isset($this->request->post['blog']['url_all_text']) ?? '',
                'url_all' => isset($this->request->post['blog']['url_all']) ?? '',
                'blog_category_id' => isset($this->request->post['blog']['blog_category_id']) ?? 0,
                'news_limit' => isset($this->request->post['blog']['news_limit']) ?? 5,
                'desc_limit' => isset($this->request->post['blog']['desc_limit']) ?? 200,
                'image_status' => isset($this->request->post['blog']['image_status']) ?? 1,
                'image_width' => isset($this->request->post['blog']['image_width']) ?? 200,
                'image_height' => isset($this->request->post['blog']['image_height']) ?? 200
            );

            // Сохраняем настройки
            $this->model_setting_setting->editSetting('home', array(
                'home_slideshow' => $slideshow_data,
                'home_recommendations' => $recommendations_data,
                'home_sliders1' => $sliders1_data,
                'home_blog' => $blog_data
            ));

            $this->session->data['success'] = 'Готово!';

            $this->response->redirect($this->url->link('catalog/homepage', 'user_token=' . $this->session->data['user_token'], true));
        }

        $data['heading_title'] = $this->language->get('heading_title');
        $data['user_token'] = $this->session->data['user_token'];

        
        // Добавьте другие языковые переменные
        
        if (isset($this->error['warning'])) {
            $data['error_warning'] = $this->error['warning'];
        } else {
            $data['error_warning'] = '';
        }

        // Хлебные крошки
        $data['breadcrumbs'] = array();
        $data['breadcrumbs'][] = array(
            'text' => $this->language->get('text_home'),
            'href' => $this->url->link('common/dashboard', 'user_token=' . $this->session->data['user_token'], true)
        );
        $data['breadcrumbs'][] = array(
            'text' => $this->language->get('text_catalog'),
            'href' => $this->url->link('catalog/category', 'user_token=' . $this->session->data['user_token'], true)
        );
        $data['breadcrumbs'][] = array(
            'text' => $this->language->get('heading_title'),
            'href' => $this->url->link('catalog/homepage', 'user_token=' . $this->session->data['user_token'], true)
        );

        $data['action'] = $this->url->link('catalog/homepage', 'user_token=' . $this->session->data['user_token'], true);
        $data['cancel'] = $this->url->link('common/dashboard', 'user_token=' . $this->session->data['user_token'], true);

        // Загрузка текущих настроек
        $settings = $this->model_setting_setting->getSetting('home');
		// $settings = $this->config->get('blog');

        $data['homepage'] = $this->model_setting_setting->getSetting('homepage');

        $data['header'] = $this->load->controller('common/header');
        $data['column_left'] = $this->load->controller('common/column_left');
        $data['footer'] = $this->load->controller('common/footer');

        // Слайдшоу
		$data['slideshow'] = $settings['home_slideshow'];
        if ($data['slideshow'] && $data['slideshow']['slides']) {
            foreach ($data['slideshow']['slides'] as &$slide) {
			    $slide['thumb'] = $this->model_tool_image->resize($slide['image'] ?? 'no_image.png', 100, 100);
            }
            unset($slide);
        }

        // Рекомендации
		$data['recommendations'] = $settings['home_recommendations'];
        if ($data['recommendations'] && $data['recommendations']['slides']) {
            foreach ($data['recommendations']['slides'] as &$slide) {
			    $slide['thumb'] = $this->model_tool_image->resize($slide['image'] ?? 'no_image.png', 100, 100);
            }
            unset($slide);
        }

        // Рекомендуемое
		$data['related_categories'] = $this->load->controller('extension/module/related_categories/getRelatedCategoriesForm', 'homepage');

        // Слайдеры1
		$data['sliders_1'] = $settings['home_sliders1'];

        // Новости
		$data['blog'] = $settings['home_blog'];

		$this->load->model('catalog/manufacturer');
        $data['manufacturers'] = $this->model_catalog_manufacturer->getManufacturers();
		$this->load->model('catalog/category');
        $data['categories'] = $this->model_catalog_category->getCategories();

		$this->load->model('blog/category');
		$categories = $this->model_blog_category->getAllCategories();
		$data['blog_categories'] = $this->model_blog_category->getCategories($categories);

        $this->response->setOutput($this->load->view('catalog/homepage', $data));
    }

    protected function validate() {
        if (!$this->user->hasPermission('modify', 'catalog/homepage')) {
            $this->error['warning'] = $this->language->get('error_permission');
        }

        return !$this->error;
    }
}
