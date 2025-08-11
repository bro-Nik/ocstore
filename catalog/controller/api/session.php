<?php
class ControllerApiSession extends Controller {
  public function index() {
    $json = array();
    
    // Разрешаем кросс-доменные запросы (CORS)
    header('Access-Control-Allow-Origin: *');
    header('Access-Control-Allow-Methods: GET');
    header('Content-Type: application/json');
        
    try {
      $cart = array();
      foreach ($this->cart->getProducts() as $product) {
        $cart[] = $product['product_id'];
      }
      $wishlist = $this->session->data['wishlist'] ?? array();
      $compare = $this->session->data['compare'] ?? array();

      $data = array(
        'cart'     => $cart,
        'wishlist' => array_values($wishlist),
        'compare'  => array_values($compare)
      );
      
      $json['success'] = true;
      $json['data'] = $data;
    } catch (Exception $e) {
      $json['success'] = false;
      // $json['error'] = $e->getMessage();
    }

    $this->response->addHeader('Content-Type: application/json');
    $this->response->setOutput(json_encode($json));
  }
    
}
