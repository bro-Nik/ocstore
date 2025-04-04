<?php
class ControllerExtensionPaymentRevpay1 extends Controller {
	public function index() {

		$data['continue'] = $this->url->link('checkout/success');

		return $this->load->view('extension/payment/revpay1', $data);
	}

	public function confirm() {
		if ($this->session->data['payment_method']['code'] == 'revpay1') {
			$this->load->model('checkout/order');

			$this->model_checkout_order->addOrderHistory($this->session->data['order_id'], $this->config->get('payment_revpay1_order_status_id'));
		}
	}
}
