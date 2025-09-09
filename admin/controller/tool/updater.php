<?php
// /admin/index.php?route=tool/updater&user_token=
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
        $json[] = $this->updateDbTabs();
        
        $this->response->addHeader('Content-Type: application/json');
        $this->response->setOutput(json_encode($json));
    }
    
    private function validateToken($token) {
        return isset($this->session->data['user_token']) && hash_equals($this->session->data['user_token'], $token);
    }

    private function updateDbTabs() {
        $sql = "ALTER TABLE `oc_blog_category` ADD `type` VARCHAR(255) NOT NULL AFTER `status`";
        $this->db->query($sql);

      return 'Внесение изменения структуру базы данных выполнено.';
    }

}
