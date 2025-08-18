<?php
trait FormHandlerTrait {
    public function handleFormRequest($postData, $requiredFields, $captchaType = '') {
        $json = [];
        
        if ($this->request->server['REQUEST_METHOD'] == 'POST') {
            $errors = $this->validateForm($postData, $requiredFields, $captchaType);
            
            if (!$errors) {
		        $product_id = (int)$postData['revproduct_id'] ?? 0;
		        $product_info = $this->model_catalog_product->getProduct($product_id);
			    $option = $this->request->post['option'] ?? array();
			    $quantity = (int)$this->request->post['quantity'] ?? 1;

				$data = array(
					'firstname'          => $postData['name'] ?? '',
					'telephone'          => $postData['phone'] ?? '',
					'email'          	 => $postData['email'] ?? '',
					'comment'            => $postData['comment'] ?? '',
					'site_url'		     => $postData['site_url'] ?? '',
			        'quantity'           => (int)$postData['quantity'] ?? 1,
				    'product_id'         => $product_id,
    				'product_name'       => $product_info['name'] ?? '',
				);	
				$json = $this->success($json, $data);
            } else {
				$json['toasts'] = $errors;
            }
        }
        
        $this->sendJsonResponse($json);
    }
    
    protected function sendJsonResponse(array $data) {
        ob_clean();
        $this->response->addHeader('Content-Type: application/json');
        $this->response->setOutput(json_encode($data));
    }

    protected function getCommonData($captchaType = '') {
		$data = array();
		$data['fields'] = self::FIELDS;
        $data['required_fields'] = self::REQUIRED_FIELDS;
		$data['button_shopping'] = $this->language->get('button_shopping');
		$data['button_checkout'] = $this->language->get('button_checkout_popupphone');
		$data['privacy_policy_confirmation'] = $this->getPrivacyPolicyConfirmation();
        if ($captchaType) {
		    $data['captcha'] = $this->getCaptcha($captchaType);
        }
        return $data;
    }
}
