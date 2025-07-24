<?php
class ControllerCatalogHomepage extends Controller {
    private $error = array();

    public function index() {
        $this->load->language('catalog/homepage');

        $this->document->setTitle($this->language->get('heading_title'));

        $this->load->model('setting/setting');

        if (($this->request->server['REQUEST_METHOD'] == 'POST') && $this->validate()) {
			$this->load->model('extension/module/related_categories');
			$this->model_extension_module_related_categories->saveRelatedCategories('homepage', $this->request->post);

            // Подготовка данных блога для сохранения в JSON
            $blog_data = array(
                'status' => isset($this->request->post['blog']['status']) ? (int)$this->request->post['blog']['status'] : 0,
                'title' => isset($this->request->post['blog']['title']) ? $this->request->post['blog']['title'] : 'Новости',
                'url_all_text' => isset($this->request->post['blog']['url_all_text']) ? $this->request->post['blog']['url_all_text'] : '',
                'url_all' => isset($this->request->post['blog']['url_all']) ? $this->request->post['blog']['url_all'] : '',
                'blog_category_id' => isset($this->request->post['blog']['blog_category_id']) ? (int)$this->request->post['blog']['blog_category_id'] : 0,
                'news_limit' => isset($this->request->post['blog']['news_limit']) ? (int)$this->request->post['blog']['news_limit'] : 5,
                'desc_limit' => isset($this->request->post['blog']['desc_limit']) ? (int)$this->request->post['blog']['desc_limit'] : 200,
                'image_status' => isset($this->request->post['blog']['image_status']) ? (int)$this->request->post['blog']['image_status'] : 1,
                'image_width' => isset($this->request->post['blog']['image_width']) ? (int)$this->request->post['blog']['image_width'] : 200,
                'image_height' => isset($this->request->post['blog']['image_height']) ? (int)$this->request->post['blog']['image_height'] : 200
            );

            // Сохраняем настройки
            $this->model_setting_setting->editSetting('home', array('home_blog' => $blog_data));

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

        // Рекомендуемое
		$data['related_categories'] = $this->load->controller('extension/module/related_categories/getRelatedCategoriesForm', 'homepage');

        // Новости
		$data['blog'] = $settings['home_blog'];

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
