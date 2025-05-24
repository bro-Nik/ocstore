<?php
/*
@author	Artem Serbulenko
@link	https://cmsshop.com.ua
@link	https://opencartforum.com/profile/762296-bn174uk/
@email 	serfbots@gmail.com
*/
class ModelExtensionModuleArtAskPrice extends Model {
	public function addAskPrice($data) {
								
		if ($this->request->server['REQUEST_METHOD'] == 'POST') {
			
			$this->db->query("INSERT INTO " . DB_PREFIX . "art_ask_price SET email = '" . $this->db->escape($data['email']) . "', phone = '" . $this->db->escape($data['phone']) . "', product_id = '" . (int)$data['product_id'] . "', comment = '" . $this->db->escape($data['comment']) . "', user = '" . $this->db->escape($data['name']) . "',date_added = NOW()");
							
			$this->load->language('extension/module/art_ask_price');
			$subject = sprintf($this->config->get('config_name')) .': '. sprintf($this->language->get('text_subject')) . ' "' . $data['product_name'] . '"';
			
			$text = sprintf($this->language->get('text_header')) . "\n\n";
			$text .= sprintf($this->language->get('text_product_name')) .'  '. $this->db->escape($data['product_name']) . "\n";
			$text .= sprintf($this->language->get('text_link'))  . '  ' . $this->url->link('product/product', 'product_id=' . (int)$data['product_id']) . "\n";
			if(!empty($data['name'])){
				$text .= sprintf($this->language->get('text_name')) . '  ' . $this->db->escape($data['name']) . "\n";
			}
			if(!empty($data['phone'])){
				$text .= sprintf($this->language->get('text_phone')) . '  ' . $this->db->escape($data['phone']) . "\n";
			}
			if(!empty($data['email'])){
				$text .= sprintf($this->language->get('text_email')) . '  ' . $this->db->escape($data['email']) ."\n";
			}
			if(!empty($data['comment'])){
				$text .= sprintf($this->language->get('text_comment')) . '  ' . $this->db->escape($data['comment']) ."\n";
			}
			if(!empty($this->config->get('module_art_ask_price_mail'))){
				$mail = new Mail($this->config->get('config_mail_engine'));
				$mail->parameter = $this->config->get('config_mail_parameter');
				$mail->smtp_hostname = $this->config->get('config_mail_smtp_hostname');
				$mail->smtp_username = $this->config->get('config_mail_smtp_username');
				$mail->smtp_password = html_entity_decode($this->config->get('config_mail_smtp_password'), ENT_QUOTES, 'UTF-8');
				$mail->smtp_port = $this->config->get('config_mail_smtp_port');
				$mail->smtp_timeout = $this->config->get('config_mail_smtp_timeout'); 

				$mail->setFrom($this->config->get('config_email'));
				$mail->setSender(html_entity_decode($this->config->get('config_name'), ENT_QUOTES, 'UTF-8'));
				$mail->setSubject(html_entity_decode($subject, ENT_QUOTES, 'UTF-8'));
				$mail->setText(html_entity_decode($text, ENT_QUOTES, 'UTF-8'));
				
				$emails = explode(',', $this->config->get('module_art_ask_price_mail'));

				foreach ($emails as $email) {
					if ($email && filter_var($email, FILTER_VALIDATE_EMAIL)) {
						$mail->setTo($email);
						$mail->send();
					}
				}
			}

			$chat_ids = $this->config->get('module_art_ask_price_chat_id');

			if (!empty($chat_ids)) {

	        	$chat_ids = explode(",", $chat_ids);
	   			$message = strip_tags(str_replace('\n', PHP_EOL , $text));

	    		foreach ($chat_ids as $chat_id) {
		        	$this->sendNotification($message, $chat_id);      		
		        }
			}
		}
	}

	public function sendNotification($message, $chat_id) {
    	$link = 'https://api.telegram.org/bot';
    
        $bot_token = $this->config->get('module_art_ask_price_token');
        $sendToTelegram = $link . $bot_token;

        $chat_id = trim($chat_id);
        $message = strip_tags($message, '<b><a><i>');        

		$params = array(
		    'chat_id' => $chat_id,
		    'text' => $message,
		    'parse_mode' =>'html',
		    'disable_web_page_preview' => true,
		);

		$ch = curl_init($sendToTelegram . '/sendMessage');
		curl_setopt($ch, CURLOPT_HEADER, false);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($ch, CURLOPT_POST, 1);
		curl_setopt($ch, CURLOPT_POSTFIELDS, ($params));
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
		curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 5);
		curl_setopt($ch, CURLOPT_MAXREDIRS, 10);
        curl_setopt($ch, CURLOPT_TIMEOUT, 20);
		curl_exec($ch);
    }
}