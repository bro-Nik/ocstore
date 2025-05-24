<?php
class ControllerRevolutionRevsubscribe extends Controller {
	public function index() {

		$this->load->language('revolution/revsubscribe');
		$this->load->language('revolution/revolution');

		$setting = $this->config->get('revtheme_footer_subscribe');

		if (!$setting['status']) {
			return false;
		}
		
		$data['revsubscribe_title'] = $setting[$this->config->get('config_language_id')]['title'];
		$data['revsubscribe_text'] = $setting[$this->config->get('config_language_id')]['text'];
		$data['language_id'] = $this->config->get('config_language_id');
		$data['store_id'] = $this->config->get('config_store_id');
		
		$data['revsubscribe_error1'] = $this->language->get('revsubscribe_error1');
      	$data['revsubscribe_error2'] = $this->language->get('revsubscribe_error2');
		$data['text_revsubscribe_email_tooltip'] = $this->language->get('text_revsubscribe_email_tooltip');
		$data['text_revsubscribe_spasibo'] = $this->language->get('text_revsubscribe_spasibo');
		$data['text_product_oshibka'] = $this->language->get('text_product_oshibka');
		
		$data['revsubscribe_subscribe_now'] = $this->language->get('revsubscribe_subscribe_now');

		return $this->load->view('revolution/revsubscribe', $data);

	}
	
	public function subscribecustomer() { 
		if (isset($this->request->post['revs_email'])) {
			$this->language->load('revolution/revsubscribe');
			$check_query = $this->db->query("SELECT * FROM `" . DB_PREFIX . "revsubscribe`
				WHERE `email`='".$this->db->escape($this->request->post['revs_email'])."'");
			if (!$check_query->row) {
				$run_query = $this->db->query("
				INSERT INTO `" . DB_PREFIX . "revsubscribe`
				(email, date_created, language_id, store_id)
				VALUES ('".$this->db->escape($this->request->post['revs_email'])."', NOW(), '".(int)$this->request->post['language_id']."', '".(int)$this->request->post['store_id']."')
				");
				$setting_footer_subscribe = $this->config->get('revtheme_footer_subscribe');
				if ($setting_footer_subscribe[$this->config->get('config_language_id')]['text_uspeh']) {
					if ($run_query) echo $setting_footer_subscribe[$this->config->get('config_language_id')]['text_uspeh'];
				} else {
					if ($run_query) echo $this->language->get('revsubscribe_success');
				}
			} else {
				echo $this->language->get('revsubscribe_duplicate');
			}
		}
	}
	
	public function unsubscribecustomer() {
		$this->language->load('revolution/revsubscribe');
		$this->language->load('revolution/revsubscribe');
		if (isset($this->request->get['email'])) {
			$email = base64_decode($this->request->get['email']);
			
			$this->document->setTitle($this->language->get('revsubscribe_unsubscribe'));
			$data['heading_title'] = $this->language->get('revsubscribe_unsubscribe');

			$data['breadcrumbs'] = array();
			$data['breadcrumbs'][] = array(
				'text' => $this->language->get('text_home'),
				'href' => $this->url->link('common/home')
			);
			$data['breadcrumbs'][] = array(
				'text' => $this->language->get('revsubscribe_unsubscribe'),
				'href' => $this->url->link('checkout/success')
			);

			$data['column_left'] = $this->load->controller('common/column_left');
			$data['column_right'] = $this->load->controller('common/column_right');
			$data['content_top'] = $this->load->controller('common/content_top');
			$data['content_bottom'] = $this->load->controller('common/content_bottom');
			$data['footer'] = $this->load->controller('common/footer');
			$data['header'] = $this->load->controller('common/header');

			$run_query = $this->db->query("DELETE FROM `" . DB_PREFIX . "revsubscribe`
				WHERE `email` = '".$this->db->escape($email)."'");
			if ($run_query)	{
				$data['text_message'] = $this->language->get('revsubscribe_unsubscribe');
			}
			
			$this->response->setOutput($this->load->view('common/success', $data));
		}
	}

}