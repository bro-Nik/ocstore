<?php
require_once(DIR_SYSTEM . 'library/trait/module_settings.php');

class ControllerCatalogSettings extends Controller {
    use TraitModuleSettings;

    private $error = array();
    private $key = 'catalog';

    public function index() {
        $this->load->model('setting/setting');

        if (($this->request->server['REQUEST_METHOD'] == 'POST') && $this->validate()) {
            $this->processForm();
        }

        $this->setupTemplateData();
    }

    protected function processForm() {
        $this->processSimilarProducts();
        $this->processFeaturedArticles();

        $this->session->data['success'] = 'Готово!';
        $this->response->redirect($this->url->link('catalog/settings', 'user_token=' . $this->session->data['user_token'], true));
    }

    protected function processSimilarProducts() {
        $fields = $this->request->post['similar_products'] ?? [];
        $data = [
            'title' => $fields['title'] ?? '',
            'limit' => $fields['limit'] ?? '',
            'diff' => $fields['diff'] ?? '',
            'delimiter' => $fields['delimiter'] ?? '',
            'price_percent' => $fields['price_percent'] ?? '',
            'use_excluded_attributes' => $fields['use_excluded_attributes'] ?? '',
            'category' => $fields['category'] ?? '',
            'status' => $fields['status'] ?? '',
            'excluded_attributes' => $fields['excluded_attributes'] ?? [],
        ];
        $this->saveSettings('similar_products', $data);
    }

    protected function processFeaturedArticles() {
        $fields = $this->request->post['featured_articles'] ?? [];
        $data = [
            'title' => $fields['title'] ?? '',
            'limit' => $fields['limit'] ?? '',
            'width' => $fields['width'] ?? '',
            'height' => $fields['height'] ?? '',
            'status' => $fields['status'] ?? '',
        ];
        $this->saveSettings('featured_articles', $data);
    }

    protected function setupTemplateData() {
        $data = [];
        $settings = $this->model_setting_setting->getSetting($this->key);

        // Основные данные
        $data['heading_title'] = 'Настройки каталога';
        $data['user_token'] = $this->session->data['user_token'];
        $data['error_warning'] = $this->error['warning'] ?? '';
        
        // Хлебные крошки
        $data['breadcrumbs'] = $this->getBreadcrumbs();
        
        // URL-адреса
        $data['action'] = $this->url->link('catalog/settings', 'user_token=' . $this->session->data['user_token'], true);
        $data['cancel'] = $this->url->link('common/dashboard', 'user_token=' . $this->session->data['user_token'], true);
        
        // Загрузка компонентов
        $data['header'] = $this->load->controller('common/header');
        $data['column_left'] = $this->load->controller('common/column_left');
        $data['footer'] = $this->load->controller('common/footer');
        
        // Данные модулей
        $data['similar_products'] = $this->getSettings('similar_products');
        $data['featured_articles'] = $this->getSettings('featured_articles');
        
        // Списки категорий и производителей
        $this->load->model('catalog/manufacturer');
        $this->load->model('catalog/category');
        $this->load->model('catalog/attribute');
        // $this->load->model('blog/category');
        
        $data['attributes'] = $this->model_catalog_attribute->getAttributes();
        $data['manufacturers'] = $this->model_catalog_manufacturer->getManufacturers();
        $data['categories'] = $this->model_catalog_category->getCategories();
        
        // $categories = $this->model_blog_category->getAllCategories();
        // $data['blog_categories'] = $this->model_blog_category->getCategories($categories);

        $this->response->setOutput($this->load->view('catalog/settings', $data));
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
                'text' => 'Каталог',
                'href' => $this->url->link('catalog/category', 'user_token=' . $this->session->data['user_token'], true)
            ],
            [
                'text' => 'Настройки',
                'href' => $this->url->link('catalog/settings', 'user_token=' . $this->session->data['user_token'], true)
            ]
        ];
    }

    protected function validate() {
        return true;
        if (!$this->user->hasPermission('modify', 'catalog/settings')) {
            $this->error['warning'] = $this->language->get('error_permission');
        }
        return !$this->error;
    }
}
