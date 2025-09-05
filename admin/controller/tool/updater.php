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
        $table_name = "oc_modification_backup";

        // Проверяем существование таблицы
        $query = $this->db->query("SHOW TABLES LIKE '" . $this->db->escape($table_name) . "'");

        if (!$query->num_rows) {
            $sql = "CREATE TABLE IF NOT EXISTS `" . $table_name . "` (
                `modification_id` int(11) NOT NULL AUTO_INCREMENT,
                `backup_id` int(11) NOT NULL,
                `name` varchar(64) NOT NULL,
                `code` varchar(64) NOT NULL,
                `author` varchar(64) NOT NULL,
                `version` varchar(32) NOT NULL,
                `link` varchar(255) NOT NULL,
                `xml` mediumtext NOT NULL,
                `status` tinyint(1) NOT NULL,
                `date_added` datetime NOT NULL,
                PRIMARY KEY (`modification_id`),
                KEY `backup_id` (`backup_id`),
                KEY `name` (`name`),
                KEY `code` (`code`),
                KEY `status` (`status`),
                KEY `date_added` (`date_added`)
            ) ENGINE=MyISAM DEFAULT CHARSET=utf8";
            
            $this->db->query($sql);
        }

      return 'Внесение изменения структуру базы данных выполнено.';
    }

}
