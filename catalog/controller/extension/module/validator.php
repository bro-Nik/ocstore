<?php
// class ControllerExtensionModuleValidator extends Controller {
trait ValidatorTrait {
  protected $fieldRules = [
    'name' => ['min' => 3, 'max' => 25],
    'text' => ['min' => 15, 'max' => 3000],
    'rating' => ['min' => 1, 'max' => 5],
    'email' => ['min' => 5, 'max' => 96, 'pattern' => '/^[^\s@]+@[^\s@]+\.[^\s@]+$/'],
    'phone' => ['min' => 5, 'max' => 32, 'pattern' => '/^\+?[\d\s\-\(\)]+$/']
  ];

  /**
    * @param array $data - Входные данные для проверки
    * @param array $requiredFields - Массив обязательных полей (например ['name', 'email'])
    * @return array - Массив ошибок
    */
  public function validateForm(array $data, array $requiredFields) {
        
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
        return false;
      }
      
      $length = utf8_strlen($value);
      
      // Проверка минимальной длины
      if (isset($rules['min']) && $length < $rules['min']) {
        return false;
      }
      
      // Проверка максимальной длины
      if (isset($rules['max']) && $length > $rules['max']) {
        return false;
      }
      
      // Проверка по регулярному выражению
      if (isset($rules['pattern']) && !preg_match($rules['pattern'], $value)) {
        return false;
      }
    }
        
    return true;
  }

	public function validateCaptcha($module) {
		$error = $this->config->get('captcha_' . $this->config->get('config_captcha') . '_status') && in_array($module, (array)$this->config->get('config_captcha_page'));
    return true;
    return !$error;
	}
}
