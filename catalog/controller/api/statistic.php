<?php
class ControllerApiStatistic extends Controller {
  public function pageViewCounter() {
    $this->load->model('catalog/statistic');
    
    if (isset($this->request->post['type']) && isset($this->request->post['id'])) {
      $type = $this->request->post['type'];
      $id = (int)$this->request->post['id'];

      $this->model_catalog_statistic->pageViewCounter($type, $id);
    }
  }
}
