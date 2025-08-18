<?php
require_once('catalog/controller/extension/module/validator.php');
require_once('catalog/controller/trait/form_handler.php');

class ControllerModalCallback extends Controller {
	use \ValidatorTrait, \FormHandlerTrait;

	const FIELDS = ['name', 'phone', 'comment', 'privacy_policy_confirmation'];
  const REQUIRED_FIELDS = ['phone', 'privacy_policy_confirmation'];

	public function index() {
		$this->language->load('revolution/revolution');
		$data = $this->getCommonData('contact');
		$this->response->setOutput($this->load->view('modals/callback', $data));
	}

	public function send() {
		$this->language->load('revolution/revolution');
		return $this->handleFormRequest($this->request->post, self::REQUIRED_FIELDS, 'contact');
	}

	protected function success(&$json, $data) {
		$this->load->model('revolution/revolution');
		$this->model_revolution_revolution->SendMail($data);

		$this->language->load('revolution/revolution');
		$json['html'] = $this->language->get('text_success_order_popupphone');
		return $json;
	}
}
