<?php
class ControllerToolCache extends Controller {
  public function clearCache() {
    $this->cache->clear();
    $this->session->data['success'] = 'Кеш очищен!';
    $this->response->redirect($this->url->link('common/dashboard', 'user_token=' . $this->session->data['user_token'], true));
  }
}
