<?php
// class ControllerExtensionModuleValidator extends Controller {
trait ValidatorTrait {
  protected $fieldRules = [
    'name' => ['min' => 3, 'max' => 25],
    'text' => ['min' => 15, 'max' => 3000],
    'rating' => ['min' => 1, 'max' => 5],
    'email' => ['min' => 5, 'max' => 96, 'pattern' => '/^[^\s@]+@[^\s@]+\.[^\s@]+$/'],
    'phone' => ['min' => 18, 'max' => 18, 'pattern' => '/^\+?[\d\s\-\(\)]+$/']
  ];

  protected $messages = [
    'agree_privacy_policy' => 'Для обработки нужно дать согласие'
  ];

  /**
    * @param array $data - Входные данные для проверки
    * @param array $requiredFields - Массив обязательных полей (например ['name', 'email'])
    * @return array - Массив ошибок
    */
  public function validateForm(array $data, array $requiredFields, $formName) {
    $errors = array();
    $notCorrect = false;
        
    // Проверяем только поля, которые есть в rules
    foreach ($this->fieldRules as $field => $rules) {
      $value = $data[$field] ?? '';
      $isRequired = in_array($field, $requiredFields);
      
      // Если поле не обязательное и пустое - пропускаем
      if (!$isRequired && empty($value)) {
        continue;
      }
      
      // Проверка на обязательность
      if ($isRequired && empty($value)) {
        $notCorrect = true;
      }
      
      // Проверка длины
      $length = utf8_strlen($value);
      if ((isset($rules['min']) && $length < $rules['min']) || (isset($rules['max']) && $length > $rules['max'])) {
        $notCorrect = true;
      }
      
      // Проверка по регулярному выражению
      if (isset($rules['pattern']) && !preg_match($rules['pattern'], $value)) {
        $notCorrect = true;
      }
    }
    if ($notCorrect) {
      $errors[] = [
        'category' => 'warning',
        'text' => 'Не коректные данные'
      ];
    }

    // Captcha
		$error = $this->config->get('captcha_' . $this->config->get('config_captcha') . '_status') && in_array($formName, (array)$this->config->get('config_captcha_page'));
    if ($error) {
      $errors[] = [
        'category' => 'warning',
        'text' => $error
      ];
    }

    if (isset($this->request->post['agree_privacy_policy'])) {
			// ToDo
			$this->load->model('catalog/information');
			$information_info = $this->model_catalog_information->getInformation($this->config->get('revtheme_all_settings')['pol_konf']);
			if ($information_info && !isset($this->request->post['agree_privacy_policy'])) {
        $errors[] = [
          'category' => 'warning',
          'text' => $this->language->get('error_agree_pol_konf')
        ];
			}
    }
        
    return $errors;
  }

	public function getCaptcha($name) {
		if ($this->config->get('captcha_' . $this->config->get('config_captcha') . '_status') && in_array($name, (array)$this->config->get('config_captcha_page'))) {
			return $this->load->controller('extension/captcha/' . $this->config->get('config_captcha'));
    }
		return '';
	}

}
