<?php
class ModelRevolutionHelper extends Model {

	public function getStore($store_id) {
		$query = $this->db->query("SELECT DISTINCT * FROM " . DB_PREFIX . "store WHERE store_id = '" . (int)$store_id . "'");
		return $query->row;
	}

	public function getStores($data = array()) {
		$store_data = $this->cache->get('store');
		if (!$store_data) {
			$query = $this->db->query("SELECT * FROM " . DB_PREFIX . "store ORDER BY url");
			$store_data = $query->rows;
			$this->cache->set('store', $store_data);
		}
		return $store_data;
	}
	
	public function getCats() {
		$query = $this->db->query("SELECT * FROM " . DB_PREFIX . "category ORDER BY sort_order");
		return $query->rows;
	}
	
	public function productNotify($product_id, $quantity){
		$this->language->load('revolution/revtheme');
		$this->load->model('catalog/product');
		$product = $this->model_catalog_product->getProduct($product_id);

		if ($product['quantity'] == '0' && $quantity > 0) {
			$emails = array();
			$emails = $this->db->query("SELECT email FROM " . DB_PREFIX . "revproductnotify WHERE product_id = '" . (int)$product_id . "' AND status = 1");	

			$email_subject = sprintf($this->language->get('text_rev_notify'), $product['name'], html_entity_decode($this->config->get('config_name'), ENT_QUOTES, 'UTF-8'));
			$email_text ='<!DOCTYPE html PUBLIC "-//W3C//DTD HTML 4.01//EN" "http://www.w3.org/TR/1999/REC-html401-19991224/strict.dtd"><html><head><meta http-equiv="Content-Type" content="text/html; charset=utf-8"></head><body>';
			$email_text .= sprintf($this->language->get('text_rev_notify_text'), HTTP_CATALOG.'index.php?route=product/product&product_id='.$product_id, $product['name']);
			$email_text .='</body></html>';
			foreach ($emails->rows as $email) {
				$mail = new Mail();
				$mail->protocol = $this->config->get('config_mail_protocol');
				$mail->parameter = $this->config->get('config_mail_parameter');
				$mail->smtp_hostname = $this->config->get('config_mail_smtp_hostname');
				$mail->smtp_username = $this->config->get('config_mail_smtp_username');
				$mail->smtp_password = html_entity_decode($this->config->get('config_mail_smtp_password'), ENT_QUOTES, 'UTF-8');
				$mail->smtp_port = $this->config->get('config_mail_smtp_port');
				$mail->smtp_timeout = $this->config->get('config_mail_smtp_timeout');
				$mail->setTo($email);
				$mail->setFrom($this->config->get('config_email'));
				$mail->setSender(html_entity_decode($this->config->get('config_name'), ENT_QUOTES, 'UTF-8'));
				$mail->setSubject(html_entity_decode($email_subject, ENT_QUOTES, 'UTF-8'));
				$mail->setHtml(html_entity_decode($email_text, ENT_QUOTES, 'UTF-8'));
				$mail->send();
			}

			$this->db->query("DELETE FROM " . DB_PREFIX . "revproductnotify WHERE product_id = '" . (int)$product_id . "'");
		}
	}

}