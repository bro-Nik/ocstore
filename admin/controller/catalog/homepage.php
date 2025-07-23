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

            $this->session->data['success'] = $this->language->get('text_success');

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
        $data['homepage'] = $this->model_setting_setting->getSetting('homepage');

        $data['header'] = $this->load->controller('common/header');
        $data['column_left'] = $this->load->controller('common/column_left');
        $data['footer'] = $this->load->controller('common/footer');

		$data['related_categories'] = $this->load->controller('extension/module/related_categories/getRelatedCategoriesForm', 'homepage');

        $this->response->setOutput($this->load->view('catalog/homepage', $data));
    }

	public function edit() {
		// $this->load->language('catalog/category');
		//
		// $this->document->setTitle($this->language->get('heading_title'));

		// $this->load->model('catalog/category');

		if (($this->request->server['REQUEST_METHOD'] == 'POST') && $this->validateForm()) {
			$this->load->model('extension/module/related_categories');
			$this->model_extension_module_related_categories->saveRelatedCategories('category_id=' . $this->request->get['category_id'], $this->request->post);

			$this->model_catalog_category->saveServiceRelated($this->request->get['category_id'], $this->request->post);

			$this->model_catalog_category->editCategory($this->request->get['category_id'], $this->request->post);

			$this->session->data['success'] = $this->language->get('text_success');

			$url = '';

			if (isset($this->request->get['sort'])) {
				$url .= '&sort=' . $this->request->get['sort'];
			}

			if (isset($this->request->get['order'])) {
				$url .= '&order=' . $this->request->get['order'];
			}

			if (isset($this->request->get['page'])) {
				$url .= '&page=' . $this->request->get['page'];
			}

			$this->response->redirect($this->url->link('catalog/category', 'user_token=' . $this->session->data['user_token'] . $url, true));
		}

		$this->index();
	}

    protected function validate() {
        if (!$this->user->hasPermission('modify', 'catalog/homepage')) {
            $this->error['warning'] = $this->language->get('error_permission');
        }

        return !$this->error;
    }
}
