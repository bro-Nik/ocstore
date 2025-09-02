<?php
class ModelLocalisationLanguage extends Model {
	public function getLanguage($language_id) {
    return array(
        'language_id' => 1,
        'name'        => 'Russian',
        'code'        => 'ru',
        'locale'      => 'ru_RU.UTF-8,ru_RU,ru-ru,ru',
        'directory'   => 'russian',
        'sort_order'  => 1,
        'status'      => 1
    );
		$query = $this->db->query("SELECT * FROM " . DB_PREFIX . "language WHERE language_id = '" . (int)$language_id . "'");

		return $query->row;
	}

	public function getLanguages() {
		$language_data = $this->cache->get('catalog.language');

		if (!$language_data) {
			$language_data = array();

			$query = $this->db->query("SELECT * FROM " . DB_PREFIX . "language WHERE status = '1' ORDER BY sort_order, name");

			foreach ($query->rows as $result) {
				$language_data[$result['code']] = array(
					'language_id' => $result['language_id'],
					'name'        => $result['name'],
					'code'        => $result['code'],
					'locale'      => $result['locale'],
					'image'       => $result['image'],
					'directory'   => $result['directory'],
					'sort_order'  => $result['sort_order'],
					'status'      => $result['status']
				);
			}

			$this->cache->set('catalog.language', $language_data);
		}

		return $language_data;
	}
}
