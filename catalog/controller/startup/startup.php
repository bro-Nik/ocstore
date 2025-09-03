<?php
require_once('catalog/controller/trait/cache.php');

class ControllerStartupStartup extends Controller {
	use \CacheTrait;

	public function __isset($key) {
		// To make sure that calls to isset also support dynamic properties from the registry
		// See https://www.php.net/manual/en/language.oop5.overloading.php#object.isset
		if ($this->registry) {
			if ($this->registry->get($key)!==null) {
				return true;
			}
		}
		return false;
	}

	public function index() {
		// Store
		$this->config->set('config_store_id', 0);
		$this->config->set('config_url', HTTP_SERVER);
		$this->config->set('config_ssl', HTTPS_SERVER);
		
		// Settings
		$query = $this->db->query("SELECT * FROM `" . DB_PREFIX . "setting` WHERE store_id = '0' OR store_id = '" . (int)$this->config->get('config_store_id') . "' ORDER BY store_id ASC");
		$settings_data = $query->rows;
		
		foreach ($settings_data as $result) {
			if (!$result['serialized']) {
				$this->config->set($result['key'], $result['value']);
			} else {
				$this->config->set($result['key'], json_decode($result['value'], true));
			}
		}

		// Set time zone
		if ($this->config->get('config_timezone')) {
			date_default_timezone_set($this->config->get('config_timezone'));

			// Sync PHP and DB time zones.
			// $this->db->query("SET time_zone = '" . $this->db->escape(date('P')) . "'");
		}

		// Theme
		$this->config->set('template_cache', $this->config->get('developer_theme'));
		
		// Url
		$this->registry->set('url', new Url($this->config->get('config_url'), $this->config->get('config_ssl')));
		
		// Language
		$code = $this->config->get('config_language');
				
		// Overwrite the default language object
		$language = new Language($code);
		$language->load($code);
		
		$this->registry->set('language', $language);
		
		// Set the config language_id
		$this->config->set('config_language_id', 1);	

		// Customer
		$customer = new Cart\Customer($this->registry);
		$this->registry->set('customer', $customer);
		
		// Customer Group
		$this->config->set('config_customer_group_id', $this->config->get('config_customer_group_id'));
		
		// Currency
		$this->registry->set('currency', new Cart\Currency($this->registry));
		
		// Tax
		$this->registry->set('tax', new Cart\Tax($this->registry));
		$this->tax->setShippingAddress($this->config->get('config_country_id'), $this->config->get('config_zone_id'));
		$this->tax->setPaymentAddress($this->config->get('config_country_id'), $this->config->get('config_zone_id'));
		$this->tax->setStoreAddress($this->config->get('config_country_id'), $this->config->get('config_zone_id'));
		
		// Weight
		$this->registry->set('weight', new Cart\Weight($this->registry));
		
		// Length
		$this->registry->set('length', new Cart\Length($this->registry));
		
		// Cart
		$this->registry->set('cart', new Cart\Cart($this->registry));
		
		// Encryption
		$this->registry->set('encryption', new Encryption($this->config->get('config_encryption')));
	}
}
