<?php
class ControllerToolUpdater extends Controller {
    public function index() {
        $json = array();
        
        // Проверка авторизации
        if (!$this->user->isLogged()) {
            $json['error'] = 'Ошибка авторизации. Пожалуйста, войдите снова.';
            $this->response->addHeader('Content-Type: application/json');
            $this->response->setOutput(json_encode($json));
            return;
        }
        
        // Проверка токена
        if (!isset($this->request->get['user_token']) || !$this->validateToken($this->request->get['user_token'])) {
            $json['error'] = 'Неправильная токен-сессия. Авторизуйтесь снова.';
            $this->response->addHeader('Content-Type: application/json');
            $this->response->setOutput(json_encode($json));
            return;
        }
        
        // Если все проверки пройдены, выполняем обновление
        $affected = $this->updatePrices();
        $json['success'] = 'Цены успешно обновлены! Изменено товаров: ' . $affected;
        
        $this->response->addHeader('Content-Type: application/json');
        $this->response->setOutput(json_encode($json));
    }
    
    private function validateToken($token) {
        return isset($this->session->data['user_token']) && hash_equals($this->session->data['user_token'], $token);
    }
    
    private function updatePrices() {
        // Обновляем основную цену
        $this->db->query("UPDATE " . DB_PREFIX . "product SET price = 0 WHERE price = 100001");
        $affected = $this->db->countAffected();
        
        // Очищаем кэш
        $this->cache->delete('product');
        
        return $affected;
    }
}
