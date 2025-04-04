<?php
class ControllerRevolutionRevpopuplogin extends Controller {
	
	public function index() {

		$data = array();

		$this->load->language('revolution/revolution');
		$data['forgotten'] = $this->url->link('account/forgotten', '', 'SSL');

		$this->response->setOutput($this->load->view('revolution/revpopuplogin', $data));

	}
	
	public function login() {
		$json = array();
		
		$this->load->model('account/customer');
		$this->load->language('common/header');
		$this->load->language('revolution/revolution');
		
		$text_account = $this->language->get('text_account');
		$account = $this->url->link('account/account', '', 'SSL');
		
		if (isset($this->request->post['email'])) {
			$mail = $this->request->post['email'];
		} else {
			$mail = '';
		}
		
		if (isset($this->request->post['password'])) {
			$password = $this->request->post['password'];
		} else {
			$password = '';
		}

		if (!$this->customer->login($mail, $password)) {
			$json['error'] = $this->language->get('text_account_warning');
		} else {
			$json['output'] = $this->language->get('text_account_login_uspeh');
			$json['output_button1'] = $account;
			$json['output_button2'] = $text_account;
		}

		if (!$json) {
			$data['email'] = $this->request->post['email'];
			$data['password'] = $this->request->post['password'];		
		}

		if (!$json) {
			unset($this->session->data['guest']);
			unset($this->session->data['shipping_country_id']);	
			unset($this->session->data['shipping_zone_id']);	
			unset($this->session->data['shipping_postcode']);
			unset($this->session->data['payment_country_id']);	
			unset($this->session->data['payment_zone_id']);	
		}
		
		$this->response->addHeader('Content-Type: application/json');
		$this->response->setOutput(json_encode($json));
		
	}

}