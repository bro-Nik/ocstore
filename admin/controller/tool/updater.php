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
        // $json[] = $this->updatePrices();
        $json[] = $this->updateDbTabs();
        // $json[] = $this->deleteCategory('Тепловое оборудование');
        
        $this->response->addHeader('Content-Type: application/json');
        $this->response->setOutput(json_encode($json));
    }
    
    private function validateToken($token) {
        return isset($this->session->data['user_token']) && hash_equals($this->session->data['user_token'], $token);
    }
    
    // private function updatePrices() {
    //     // Обновляем основную цену
    //     $this->db->query("UPDATE " . DB_PREFIX . "product SET price = 0 WHERE price = 100001");
    //     $affected = $this->db->countAffected();
    //     
    //     // Очищаем кэш
    //     $this->cache->delete('product');
    //
    //     return 'Цены успешно обновлены! Изменено товаров: ' . $affected;
    // }

    private function updateDbTabs() {
        $table_name = "related_categories";

        // Проверяем существование таблицы
        $query = $this->db->query("SHOW TABLES LIKE '" . $this->db->escape($table_name) . "'");

        if (!$query->num_rows) {
            $sql = "CREATE TABLE IF NOT EXISTS `" . $table_name . "` (
                `id` int(11) NOT NULL AUTO_INCREMENT,
                `query` varchar(255) NOT NULL,
                `category_id` int(11) NOT NULL,
                `pages` text NOT NULL,
                `sort_order` int(3) NOT NULL DEFAULT '0',
                PRIMARY KEY (`id`),
                KEY `query` (`query`),
                KEY `category_id` (`category_id`),
                KEY `sort_order` (`sort_order`)
            ) ENGINE=MyISAM DEFAULT CHARSET=utf8";
            
            $this->db->query($sql);
        }

      // $query = $this->db->query("SHOW COLUMNS FROM `oc_category` LIKE 'viewed'");
      // if (!$query->num_rows) {
      //   $this->db->query("ALTER TABLE `oc_category` ADD COLUMN `viewed` INT(11) NOT NULL DEFAULT 0 AFTER `status`");
      // }
      //
      // $query = $this->db->query("SHOW COLUMNS FROM `oc_ocfilter_page` LIKE 'viewed'");
      // if (!$query->num_rows) {
      //   $this->db->query("ALTER TABLE `oc_ocfilter_page` ADD COLUMN `viewed` INT(11) NOT NULL DEFAULT 0 AFTER `status`");
      // }
      //
      // $query = $this->db->query("SHOW COLUMNS FROM `oc_manufacturer` LIKE 'viewed'");
      // if (!$query->num_rows) {
      //   $this->db->query("ALTER TABLE `oc_manufacturer` ADD COLUMN `viewed` INT(11) NOT NULL DEFAULT 0 AFTER `noindex`");
      // }

      return 'Внесение изменения структуру базы данных выполнено.';
    }

  // private function deleteCategory($category_name) {
  //   $message = '';
  //
  //   // Находим ID основной категории
  //   $query = $this->db->query("SELECT category_id FROM " . DB_PREFIX . "category_description WHERE name = '" . $category_name . "' LIMIT 1");
  //   
  //   if ($query->num_rows) {
  //       $main_category_id = $query->row['category_id'];
  //       
  //       // Получаем все ID подкатегорий (включая основную)
  //       $query = $this->db->query("SELECT GROUP_CONCAT(cp.category_id) AS ids FROM " . DB_PREFIX . "category_path cp 
  //                                 WHERE cp.path_id = '" . (int)$main_category_id . "' OR cp.category_id = '" . (int)$main_category_id . "'");
  //       $category_ids = $query->row['ids'];
  //
  //       // Получаем изображения категорий перед удалением
  //       $category_images = $this->getCategoryImages($category_ids);
  //       
  //       // Получаем все ID связанных товаров
  //       $query = $this->db->query("SELECT GROUP_CONCAT(DISTINCT p2c.product_id) AS ids FROM " . DB_PREFIX . "product_to_category p2c 
  //                                 WHERE FIND_IN_SET(p2c.category_id, '" . $this->db->escape($category_ids) . "')");
  //       $product_ids = $query->row['ids'];
  //       
  //       if ($product_ids) {
  //
  //           $products_images = $this->getProductsImages($product_ids);
  //
  //           // Удаляем товары и все связанные данные
  //           $tables = array(
  //               'product', 'product_description', 'product_attribute', 'product_discount',
  //               'product_filter', 'product_image', 'product_option', 'product_option_value',
  //               'product_related', 'product_reward', 'product_special', 'product_to_category',
  //               'product_to_download', 'product_to_layout', 'product_to_store', 'product_recurring',
  //               'review', 'nkf_similar_product', 'product_recurring', 'product_related_article',
  //               'product_related_mn', 'product_related_wb', 'product_tab', 'product_tab_desc',
  //               'prodvar'
  //           );
  //           
  //           foreach ($tables as $table) {
  //               $this->db->query("DELETE FROM " . DB_PREFIX . $table . " WHERE product_id IN (" . $this->db->escape($product_ids) . ")");
  //           }
  //           
  //           // Удаляем SEO URL товаров
  //           $this->db->query("DELETE FROM " . DB_PREFIX . "seo_url WHERE query IN (
  //               SELECT CONCAT('product_id=', product_id) FROM " . DB_PREFIX . "product 
  //               WHERE product_id IN (" . $this->db->escape($product_ids) . ")
  //           )");
  //
  //           $deleted_images = $this->deleteProductsImages($products_images);
  //           if ($deleted_images) {
  //             $message = $message . 'Удалено ' . $deleted_images . ' изображений товаров.';
  //           }
  //       }
  //       
  //       // Удаляем категории и все связанные данные
  //       $tables = array(
  //           'category', 'category_description', 'category_filter',
  //           'category_to_layout', 'category_to_store', 'category_path'
  //       );
  //       
  //       foreach ($tables as $table) {
  //           $this->db->query("DELETE FROM " . DB_PREFIX . $table . " WHERE category_id IN (" . $this->db->escape($category_ids) . ")");
  //       }
  //       
  //       // Удаляем SEO URL категорий
  //       $this->db->query("DELETE FROM " . DB_PREFIX . "seo_url WHERE query IN (
  //           SELECT CONCAT('category_id=', category_id) FROM " . DB_PREFIX . "category 
  //           WHERE category_id IN (" . $this->db->escape($category_ids) . ")
  //       )");
  //
  //       $categories_images_deleted = $this->deleteCategoriesImages($category_images);
  //       if ($categories_images_deleted) {
  //         $message = $message . 'Удалено ' . $categories_images_deleted . ' изображений категорий.';
  //       }
  //       
  //       return 'Категория ' . $category_name . ' и все связанные данные успешно удалены!' . $message;
  //   } else {
  //       return 'Категория ' . $category_name . ' не найдена!' . $message;
  //   }
  // }

  // private function deleteProductsImages($products_images) {
  //   if ($products_images) {
  //       
  //     $deleted = 0;
  //     foreach ($products_images as $row) {
  //       if ($row['image'] && file_exists(DIR_IMAGE . $row['image'])) {
  //         unlink(DIR_IMAGE . $row['image']);
  //         $deleted++;
  //         
  //         // Удаляем кэшированные версии
  //         $path_parts = pathinfo($row['image']);
  //         $cache_files = glob(DIR_IMAGE . 'cache/*/' . $path_parts['filename'] . '-*.' . $path_parts['extension']);
  //         foreach ($cache_files as $cache_file) {
  //             unlink($cache_file);
  //         }
  //       }
  //     }
  //     return $deleted;
  //   }
  // }

  // private function deleteCategoriesImages($category_images) {
  //     if (!$category_images) return 0;
  //     
  //     $deleted = 0;
  //     foreach ($category_images as $row) {
  //         if ($row['image'] && file_exists(DIR_IMAGE . $row['image'])) {
  //             unlink(DIR_IMAGE . $row['image']);
  //             $deleted++;
  //             
  //             // Удаляем кэшированные версии
  //             $path_parts = pathinfo($row['image']);
  //             $cache_files = glob(DIR_IMAGE . 'cache/*/' . $path_parts['filename'] . '-*.' . $path_parts['extension']);
  //             foreach ($cache_files as $cache_file) {
  //                 unlink($cache_file);
  //             }
  //         }
  //     }
  //     return $deleted;
  // }

  // private function getCategoryImages($category_ids) {
  //     $query = $this->db->query("SELECT image FROM " . DB_PREFIX . "category 
  //                               WHERE category_id IN (" . $this->db->escape($category_ids) . ") 
  //                               AND image IS NOT NULL AND image != ''");
  //     return $query->rows;
  // }
  // private function getProductsImages($product_ids) {
  //     $query = $this->db->query("SELECT image FROM " . DB_PREFIX . "product 
  //                               WHERE product_id IN (" . $this->db->escape($product_ids) . ")
  //                               UNION 
  //                               SELECT image FROM " . DB_PREFIX . "product_image 
  //                               WHERE product_id IN (" . $this->db->escape($product_ids) . ")");
  //     return $query->rows;
  // }
}
