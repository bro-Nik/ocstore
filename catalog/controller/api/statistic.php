<?php
class ControllerApiStatistic extends Controller {
  public function pageViewCounter() {
    $this->load->model('catalog/statistic');
    
    if (isset($this->request->get['type']) && isset($this->request->get['id'])) {
      $type = $this->request->get['type'];
      $id = (int)$this->request->get['id'];
      $this->model_catalog_counter->pageViewCounter($type, $id);
    }
  }
}
