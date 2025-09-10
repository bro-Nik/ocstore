<?php
require_once(DIR_SYSTEM . 'library/trait/module_settings.php');

class ControllerCatalogHomepage extends Controller {
  use TraitModuleSettings;

  private $error = array();

    public function index() {
        $this->load->language('catalog/homepage');
        $this->document->setTitle($this->language->get('heading_title'));

        $this->load->model('setting/setting');

        if (($this->request->server['REQUEST_METHOD'] == 'POST') && $this->validate()) {
            $this->processForm();
        }

        $this->setupTemplateData();
    }

    protected function processForm() {
        $this->load->model('tool/image');
        $data['placeholder'] = $this->model_tool_image->resize('no_image.png', 100, 100);

        $this->processMainData();
        $this->processSlideshowData();
        $this->processRecommendationsData();
        $this->processSlidersData('sliders_1');
        $this->processSlidersData('sliders_2');
        $this->processBlogData();
        $this->processAboutStoreData();
        $this->processStoreReviewData();
        $this->processViewedProductsData();

        $this->load->model('extension/module/related_categories');
        $this->model_extension_module_related_categories->saveRelatedCategories('homepage', $this->request->post);

        $this->session->data['success'] = 'Готово!';
        $this->response->redirect($this->url->link('catalog/homepage', 'user_token=' . $this->session->data['user_token'], true));
    }

    protected function processMainData() {
        $data = [
            'h1' => $this->request->post['main']['h1'] ?? '',
        ];
        $this->saveSettings('home_main', $data);
    }

    protected function processSlideshowData() {
        $data = [
            'status' => $this->request->post['slideshow']['status'] ?? '',
            'fullscreen' => $this->request->post['slideshow']['fullscreen'] ?? '',
            'slides_in_line' => $this->request->post['slideshow']['slides_in_line'] ?? '',
            'width' => $this->request->post['slideshow']['width'] ?? '',
            'height' => $this->request->post['slideshow']['height'] ?? '',
            'slides' => []
        ];

        if (!empty($this->request->post['slide']) && is_array($this->request->post['slide'])) {
            foreach ($this->request->post['slide'] as $slide) {
                $data['slides'][] = [
                    'image' => $slide['image'],
                    'title' => $slide['title'] ?? '',
                    'description' => $slide['description'] ?? '',
                    'link_title' => $slide['link_title'] ?? '',
                    'link' => $slide['link'] ?? '',
                    'sort_order' => $slide['sort_order'] ?? 0
                ];
            }
        }
        $this->saveSettings('home_slideshow', $data);
    }

    protected function processRecommendationsData() {
        $data = [
            'status' => $this->request->post['recommendations']['status'] ?? '',
            'width' => $this->request->post['recommendations']['width'] ?? '',
            'height' => $this->request->post['recommendations']['height'] ?? '',
            'slides' => []
        ];

        if (!empty($this->request->post['recommendation']) && is_array($this->request->post['recommendation'])) {
            foreach ($this->request->post['recommendation'] as $slide) {
                $data['slides'][] = [
                    'image' => $slide['image'],
                    'title' => $slide['title'] ?? '',
                    'description' => $slide['description'] ?? '',
                    'link_title' => $slide['link_title'] ?? '',
                    'link' => $slide['link'] ?? '',
                    'sort_order' => $slide['sort_order'] ?? 0
                ];
            }
        }
        $this->saveSettings('home_recommendations', $data);
    }

    protected function processSlidersData($sliders_key) {
        $data = [
            'status' => $this->request->post[$sliders_key]['status'] ?? '',
            'title' => $this->request->post[$sliders_key]['title'] ?? '',
        ];

        $this->load->model('catalog/product');

        for ($i = 1; $i <= 4; $i++) {
            $slider_data = $this->request->post[$sliders_key . '_slider_' . $i] ?? [];
            
            $data['slider_' . $i] = [
                'status' => $slider_data['status'] ?? 0,
                'title' => $slider_data['title'] ?? '',
                'url_all' => $slider_data['url_all'] ?? '',
                'limit' => $slider_data['limit'] ?? 5,
                'category_id' => $slider_data['category_id'] ?? 0,
                'manufacturer_id' => $slider_data['manufacturer_id'] ?? 0,
                'sort' => $slider_data['sort'] ?? 'p.date_added',
                'autoscroll' => $slider_data['autoscroll'] ?? 0,
                'featured' => []
            ];
            
            if (!empty($slider_data['featured']) && is_array($slider_data['featured'])) {
                foreach ($slider_data['featured'] as $product_id) {
                    $product_info = $this->model_catalog_product->getProduct($product_id);
                    if ($product_info) {
                        $data['slider_' . $i]['featured'][] = [
                            'product_id' => $product_id,
                            'name' => $product_info['name']
                        ];
                    }
                }
            }
        }
        $this->saveSettings('home_' . $sliders_key, $data);
    }

    protected function processBlogData() {
        $data = [
            'status' => $this->request->post['blog']['status'] ?? 0,
            'title' => $this->request->post['blog']['title'] ?? 'Новости',
            'url_all_text' => $this->request->post['blog']['url_all_text'] ?? '',
            'url_all' => $this->request->post['blog']['url_all'] ?? '',
            'blog_category_id' => $this->request->post['blog']['blog_category_id'] ?? 0,
            'news_limit' => $this->request->post['blog']['news_limit'] ?? 5,
            'desc_limit' => $this->request->post['blog']['desc_limit'] ?? 200,
            'image_status' => $this->request->post['blog']['image_status'] ?? 1,
            'image_width' => $this->request->post['blog']['image_width'] ?? 200,
            'image_height' => $this->request->post['blog']['image_height'] ?? 200
        ];
        $this->saveSettings('home_blog', $data);
    }

    protected function processAboutStoreData() {
        $data = [
            'status' => $this->request->post['aboutstore']['status'] ?? 0,
            'title' => $this->request->post['aboutstore']['title'] ?? 'О магазине',
            'description' => $this->request->post['aboutstore']['description'] ?? ''
        ];
        $this->saveSettings('home_aboutstore', $data);
    }

    protected function processStoreReviewData() {
        $data = [
            'status' => $this->request->post['storereview']['status'] ?? 0,
            'title' => $this->request->post['storereview']['title'] ?? 'Отзывы наших клиентов',
            'button_all' => $this->request->post['storereview']['button_all'] ?? 0,
            'button_all_text' => $this->request->post['storereview']['button_all_text'] ?? 'Читать все отзывы',
            'limit' => $this->request->post['storereview']['limit'] ?? 5,
            'order' => $this->request->post['storereview']['order'] ?? 0,
            'limit_text' => $this->request->post['storereview']['limit_text'] ?? 200
        ];
        $this->saveSettings('home_storereview', $data);
    }

    protected function processViewedProductsData() {
        $data = [
            'status' => $this->request->post['viewed_products']['status'] ?? 0,
            'title' => $this->request->post['viewed_products']['title'] ?? 'Вы смотрели',
            'limit' => $this->request->post['viewed_products']['limit'] ?? 5
        ];
        $this->saveSettings('home_viewed_products', $data);
    }

    protected function setupTemplateData() {
        $data = [];
		$settings = $this->getSettingsByPrefix('home');
        if (!$settings) {
            $this->migrateSettings();
        }

        // Основные данные
        $data['heading_title'] = $this->language->get('heading_title');
        $data['user_token'] = $this->session->data['user_token'];
        $data['error_warning'] = $this->error['warning'] ?? '';
        
        // Хлебные крошки
        $data['breadcrumbs'] = $this->getBreadcrumbs();
        
        // URL-адреса
        $data['action'] = $this->url->link('catalog/homepage', 'user_token=' . $this->session->data['user_token'], true);
        $data['cancel'] = $this->url->link('common/dashboard', 'user_token=' . $this->session->data['user_token'], true);
        
        // Загрузка компонентов
        $data['header'] = $this->load->controller('common/header');
        $data['column_left'] = $this->load->controller('common/column_left');
        $data['footer'] = $this->load->controller('common/footer');
        
        // Данные модулей
        $data['main'] = $settings['home_main'] ?? [];
        $data['slideshow'] = $this->prepareSlideshowData($settings['home_slideshow'] ?? []);
        $data['recommendations'] = $this->prepareRecommendationsData($settings['home_recommendations'] ?? []);
        $data['sliders_1'] = $settings['home_sliders_1'] ?? [];
        $data['sliders_2'] = $settings['home_sliders_2'] ?? [];
        $data['blog'] = $settings['home_blog'] ?? [];
        $data['related_categories'] = $this->load->controller('extension/module/related_categories/getRelatedCategoriesForm', 'homepage');
        $data['aboutstore'] = $settings['home_aboutstore'] ?? [];
        $data['storereview'] = $settings['home_storereview'] ?? [];
        $data['viewed_products'] = $settings['home_viewed_products'] ?? [];
        
        // Списки категорий и производителей
        $this->load->model('catalog/manufacturer');
        $this->load->model('catalog/category');
        $this->load->model('blog/category');
        
        $data['manufacturers'] = $this->model_catalog_manufacturer->getManufacturers();
        $data['categories'] = $this->model_catalog_category->getCategories();
        
        $categories = $this->model_blog_category->getAllCategories();
        $data['blog_categories'] = $this->model_blog_category->getCategories($categories);

        $this->response->setOutput($this->load->view('catalog/homepage', $data));
    }

    protected function prepareSlideshowData($slideshow) {
        if (!empty($slideshow['slides'])) {
            foreach ($slideshow['slides'] as &$slide) {
                $slide['thumb'] = $this->model_tool_image->resize($slide['image'] ?? 'no_image.png', 100, 100);
            }
            unset($slide);
        }
        return $slideshow;
    }

    protected function prepareRecommendationsData($recommendations) {
        if (!empty($recommendations['slides'])) {
            foreach ($recommendations['slides'] as &$slide) {
                $slide['thumb'] = $this->model_tool_image->resize($slide['image'] ?? 'no_image.png', 100, 100);
            }
            unset($slide);
        }
        return $recommendations;
    }

    protected function getBreadcrumbs() {
        return [
            [
                'text' => $this->language->get('text_home'),
                'href' => $this->url->link('common/dashboard', 'user_token=' . $this->session->data['user_token'], true)
            ],
            [
                'text' => $this->language->get('text_catalog'),
                'href' => $this->url->link('catalog/category', 'user_token=' . $this->session->data['user_token'], true)
            ],
            [
                'text' => $this->language->get('heading_title'),
                'href' => $this->url->link('catalog/homepage', 'user_token=' . $this->session->data['user_token'], true)
            ]
        ];
    }

    protected function validate() {
        if (!$this->user->hasPermission('modify', 'catalog/homepage')) {
            $this->error['warning'] = $this->language->get('error_permission');
        }
        return !$this->error;
    }

    protected function migrateSettings() {
        print_r('Start of migrate');
        $this->load->model('setting/setting');
        $s = $this->model_setting_setting->getSetting('home');

        $this->saveSettings('home_main', $s['home_main']);
        $this->saveSettings('home_slideshow', $s['home_slideshow']);
        $this->saveSettings('home_recommendations', $s['home_recommendations']);
        $this->saveSettings('home_slider_1', $s['home_slider_1'] ?? []);
        $this->saveSettings('home_slider_2', $s['home_slider_2'] ?? []);
        $this->saveSettings('home_blog', $s['home_blog']);
        $this->saveSettings('home_aboutstore', $s['home_aboutstore']);
        $this->saveSettings('home_storereview', $s['home_storereview']);
        $this->saveSettings('home_viewed_products', $s['home_viewed_products']);
        print_r('End of migrate');
    }
}
