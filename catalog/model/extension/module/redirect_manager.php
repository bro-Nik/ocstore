<?php
//==============================================================================
// Redirect Manager v300.1
// 
// Author: Clear Thinking, LLC
// E-mail: johnathan@getclearthinking.com
// Website: http://www.getclearthinking.com
// 
// All code within this file is copyright Clear Thinking, LLC.
// You may not copy or reuse code within this file without written permission.
//==============================================================================

class ModelExtensionModuleRedirectManager extends Model {
	private $type = 'module';
	private $name = 'redirect_manager';
	
	public function redirect() {
		$settings = $this->getSettings();
		if (empty($settings['status'])) return;
		 
		$server = $this->request->server;
		
		$preserve_query = $this->db->query("SELECT * FROM `" . DB_PREFIX . $this->name . "` WHERE from_url LIKE '%?%'");
		$request_uri = (strpos('%', $server['REQUEST_URI'])) ? urldecode($server['REQUEST_URI']) : $server['REQUEST_URI'];
		$browser_url = (!$preserve_query->num_rows) ? explode('?', $request_uri) : array($request_uri);
		$query_string = (!empty($browser_url[1])) ? $browser_url[1] : '';
		
		$from = 'http' . (!empty($server['HTTPS']) && $server['HTTPS'] != 'off' ? 's' : '') . '://' . $server['HTTP_HOST'] . $browser_url[0];
		$from = strtolower($from);
		if (substr($from, -1) == '/') $from = substr($from, 0, -1);
		
		$wildcard_query = $this->db->query("SELECT * FROM `" . DB_PREFIX . $this->name . "` WHERE from_url LIKE '%*%'");
		
		if ($wildcard_query->num_rows) {
			$redirect_query = $this->db->query("SELECT * FROM `" . DB_PREFIX . $this->name . "` WHERE ('" . $this->db->escape($from) . "' LIKE REPLACE(REPLACE(LCASE(from_url), '_', '\_'), '*', '%') OR '" . $this->db->escape($from) . "/' LIKE REPLACE(REPLACE(LCASE(from_url), '_', '\_'), '*', '%')) AND active = 1 AND (date_start = '0000-00-00' OR date_start < NOW()) AND (date_end = '0000-00-00' OR date_end > NOW())");
			if (!$redirect_query->num_rows) {
				$from = html_entity_decode($from, ENT_QUOTES, 'UTF-8');
				$redirect_query = $this->db->query("SELECT * FROM `" . DB_PREFIX . $this->name . "` WHERE ('" . $this->db->escape($from) . "' LIKE REPLACE(REPLACE(LCASE(from_url), '_', '\_'), '*', '%') OR '" . $this->db->escape($from) . "/' LIKE REPLACE(REPLACE(LCASE(from_url), '_', '\_'), '*', '%')) AND active = 1 AND (date_start = '0000-00-00' OR date_start < NOW()) AND (date_end = '0000-00-00' OR date_end > NOW())");
			}
		} else {
			$redirect_query = $this->db->query("SELECT * FROM `" . DB_PREFIX . $this->name . "` WHERE ('" . $this->db->escape($from) . "' = LCASE(from_url) OR '" . $this->db->escape($from) . "/' = LCASE(from_url)) AND active = 1 AND (date_start = '0000-00-00' OR date_start < NOW()) AND (date_end = '0000-00-00' OR date_end > NOW())");
			if (!$redirect_query->num_rows) {
				$from = html_entity_decode($from, ENT_QUOTES, 'UTF-8');
				$redirect_query = $this->db->query("SELECT * FROM `" . DB_PREFIX . $this->name . "` WHERE ('" . $this->db->escape($from) . "' = LCASE(from_url) OR '" . $this->db->escape($from) . "/' = LCASE(from_url)) AND active = 1 AND (date_start = '0000-00-00' OR date_start < NOW()) AND (date_end = '0000-00-00' OR date_end > NOW())");
			}
		}
		
		if ($redirect_query->num_rows) {
			$redirect_query->row['from_url'] = strtolower($redirect_query->row['from_url']);
			$redirect_query->row['to_url'] = strtolower($redirect_query->row['to_url']);
			
			$this->db->query("UPDATE `" . DB_PREFIX . $this->name . "` SET times_used = times_used + 1 WHERE " . $this->name . "_id = " . (int)$redirect_query->row[$this->name . '_id']);
			if (substr($redirect_query->row['from_url'], -1) == '/') $redirect_query->row['from_url'] = substr($redirect_query->row['from_url'], 0, -1);
			
			$from_wildcards = explode('|', str_replace(explode('*', $redirect_query->row['from_url']), '|', $from . '/'));
			$to_wildcards = explode('*', $redirect_query->row['to_url']);
			
			$to = '';
			for ($i = 0; $i < count($to_wildcards); $i++) {
				$to .= $from_wildcards[$i] . $to_wildcards[$i];
			}
			if ($query_string) {
				$to .= (strpos($redirect_query->row['to_url'], '?')) ? '&' . $query_string : '?' . $query_string;
			}
			if (substr($to, -1) == '/') $to = substr($to, 0, -1);
			
			header('Location: ' . str_replace('&amp;', '&', $to), true, $redirect_query->row['response_code']);
			exit();
			
		} elseif (isset($this->request->get['route']) && $this->request->get['route'] == 'error/not_found' && $settings['record']['404']['s']) {
			
			$ignore_ips = explode("\n", $settings['ignore_ips']);
			$ignore_ips = array_map('trim', $ignore_ips);
			
			$ip_match = false;
			
			foreach ($ignore_ips as $range) {
				$range = explode('-', $range);
				if (empty($range[0])) continue;
				if (empty($range[1])) $range[1] = $range[0];
				
				if (ip2long($server['REMOTE_ADDR']) >= ip2long($range[0]) && ip2long($server['REMOTE_ADDR']) <= ip2long($range[1])) {
					$ip_match = true;
					break;
				}
			}
			
			$ignore_user_agents = explode("\n", $settings['ignore_user_agents']);
			$ignore_user_agents = array_map('trim', $ignore_user_agents);
			
			if (!isset($server['HTTP_USER_AGENT'])) $server['HTTP_USER_AGENT'] = '';
			
			if (!$ip_match && !in_array($server['HTTP_USER_AGENT'], $ignore_user_agents)) {
				$from = 'http' . (!empty($server['HTTPS']) && $server['HTTPS'] != 'off' ? 's' : '') . '://' . $server['HTTP_HOST'] . $request_uri;
				$this->db->query("INSERT INTO `" . DB_PREFIX . $this->name . "_404` SET date_time = NOW(), url = '" . $this->db->escape($from) . "', ip = '" . $this->db->escape($server['REMOTE_ADDR']) . "', user_agent = '" . $this->db->escape($server['HTTP_USER_AGENT']) . "'");
			}
			
		}
	}
	
	//------------------------------------------------------------------------------
	// Private functions
	//------------------------------------------------------------------------------
	private function getSettings() {
		$code = (version_compare(VERSION, '3.0', '<') ? '' : $this->type . '_') . $this->name;
		
		$settings = array();
		$settings_query = $this->db->query("SELECT * FROM " . DB_PREFIX . "setting WHERE `code` = '" . $this->db->escape($code) . "' ORDER BY `key` ASC");
		
		foreach ($settings_query->rows as $setting) {
			$value = $setting['value'];
			if ($setting['serialized']) {
				$value = (version_compare(VERSION, '2.1', '<')) ? unserialize($setting['value']) : json_decode($setting['value'], true);
			}
			$split_key = preg_split('/_(\d+)_?/', str_replace($code . '_', '', $setting['key']), -1, PREG_SPLIT_DELIM_CAPTURE | PREG_SPLIT_NO_EMPTY);
			
				if (count($split_key) == 1)	$settings[$split_key[0]] = $value;
			elseif (count($split_key) == 2)	$settings[$split_key[0]][$split_key[1]] = $value;
			elseif (count($split_key) == 3)	$settings[$split_key[0]][$split_key[1]][$split_key[2]] = $value;
			elseif (count($split_key) == 4)	$settings[$split_key[0]][$split_key[1]][$split_key[2]][$split_key[3]] = $value;
			else 							$settings[$split_key[0]][$split_key[1]][$split_key[2]][$split_key[3]][$split_key[4]] = $value;
		}
		
		return $settings;
	}
}
?>