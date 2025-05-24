<?php
class ControllerRevolutionRevcheckoutShipPay extends Controller {
	private $error = array();

	public function index() {
		
		$this->load->language('revolution/revcheckoutshippay');
		$this->document->setTitle($this->language->get('heading_title'));
		
		$data['heading_title'] = $this->language->get('heading_title');
		$data['text_select_all'] = $this->language->get('text_select_all');
		$data['text_unselect_all'] = $this->language->get('text_unselect_all');
		$data['button_save'] = $this->language->get('button_save');
		
		$data['action'] = $this->url->link('revolution/revcheckoutshippay', 'user_token=' . $this->session->data['user_token'], 'SSL');
		
		if ($this->request->server['REQUEST_METHOD'] == 'POST' && $this->validate()) {
			$this->load->model('setting/setting');
			$this->model_setting_setting->editSetting('revtheme_revcheckoutshippay', $this->request->post);
			$this->session->data['success'] = $this->language->get('save_text');
			$this->response->redirect($this->url->link('revolution/revcheckoutshippay', 'user_token=' . $this->session->data['user_token'], 'SSL'));
		}

		if (isset($this->error['warning'])) {
			$data['error_warning'] = $this->error['warning'];
		} else {
			$data['error_warning'] = '';
		}
		
		$data['breadcrumbs'] = array();

		$data['breadcrumbs'][] = array(
			'text' => $this->language->get('text_home'),
			'href' => $this->url->link('common/dashboard', 'user_token=' . $this->session->data['user_token'], 'SSL')
		);

		$data['breadcrumbs'][] = array(
			'text' => $this->language->get('text_revtheme'),
			'href' => $this->url->link('revolution/revtheme', 'user_token=' . $this->session->data['user_token'], 'SSL')
		);

		$data['breadcrumbs'][] = array(
			'text' => $this->language->get('heading_title'),
			'href' => $this->url->link('revolution/revcheckoutshippay', 'user_token=' . $this->session->data['user_token'], 'SSL')
		);

		$data['cancel'] = $this->url->link('revolution/revtheme', 'user_token=' . $this->session->data['user_token'], 'SSL');
		
		if (isset($this->request->post['revtheme_revcheckoutshippay'])) {
			$data['revtheme_revcheckoutshippay'] = $this->request->post['revtheme_revcheckoutshippay'];
		} elseif ($this->config->get('revtheme_revcheckoutshippay')) {
			$data['revtheme_revcheckoutshippay'] = $this->config->get('revtheme_revcheckoutshippay');
		}
		else {
			$data['revtheme_revcheckoutshippay'] = array();
		}

		$this->load->model('setting/extension');
		$shipping_extensions = $this->model_setting_extension->getInstalled('shipping');
        $tmp = array();
        foreach ($shipping_extensions as $extension) {
            if ($this->config->get('shipping_' . $extension . '_status')) {
                $tmp[] = $extension;
            }
        }
        $shipping_extensions = $tmp;
        $data['shipping_extensions'] = array();
		$files = glob(DIR_CATALOG . 'model/extension/shipping/*.php');
        if ($files) {
            foreach ($files as $file) {
                $extension = basename($file, '.php');
                if (in_array($extension, $shipping_extensions)) {
					$this->load->language('extension/shipping/' . $extension);
                    $data['shipping_extensions'][] = array(
						'name' => $this->language->get('heading_title'),
						'cod'  => $extension
					);
                }
            }
        }
		
		$payment_extensions = $this->model_setting_extension->getInstalled('payment');
        $tmp = array();
        foreach ($payment_extensions as $extension) {
            if ($this->config->get('payment_' . $extension . '_status')) {
                $tmp[] = $extension;
            }
        }
        $payment_extensions = $tmp;
        $data['payment_extensions'] = array();
		$files = glob(DIR_CATALOG . 'model/extension/payment/*.php');
        if ($files) {
            foreach ($files as $file) {
                $extension = basename($file, '.php');
                if (in_array($extension, $payment_extensions)) {
					$this->load->language('extension/payment/' . $extension);
					$data['payment_extensions'][] = array(
						'name' => $this->language->get('heading_title'),
						'cod'  => $extension
					);
                }
            }
        }
		
		if (isset($this->session->data['success'])) {
			$data['success'] = $this->session->data['success'];

			unset($this->session->data['success']);
		} else {
			$data['success'] = '';
		}

		$data['header'] = $this->load->controller('common/header');
		$data['column_left'] = $this->load->controller('common/column_left');
		$data['footer'] = $this->load->controller('common/footer');

		$this->response->setOutput($this->load->view('revolution/revcheckoutshippay', $data));
		
	}

	protected function validate() {
		if (!$this->user->hasPermission('modify', 'revolution/revcheckoutshippay')) {
			$this->error['warning'] = $this->language->get('error_permission');
		}

		return !$this->error;
	}
}