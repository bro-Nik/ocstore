<?php
class ControllerExtensionModuleSpecialOffer extends Controller {
    private $error = array();

    public function index() {
		$this->document->addStyle('view/stylesheet/special-offers-adm.css');
		$this->load->model('extension/module/special_offer');

        $this->load->language('extension/module/special_offer');

        $data['heading_title'] = $this->language->get('heading_title');

		$this->load->model('setting/setting');

		if (isset($this->request->get['back_offers'])) {
			$url = '&back_offers';
			$link_go_back = $this->url->link('extension/module/special_offer/offers', 'user_token=' . $this->session->data['user_token'], true);
		} else {
			$url = '';
			$link_go_back = $this->url->link('marketplace/extension', 'user_token=' . $this->session->data['user_token'] . '&type=module', true);
        }

		if (($this->request->server['REQUEST_METHOD'] == 'POST') && $this->validate()) {
			$special_offer_data = array();

			foreach ($this->request->post as $key => $value) {
				if ($key != 'special_offer_seo_url') $special_offer_data['module_special_offer_' . $key] = $value;
			}

			$this->model_extension_module_special_offer->setSpecialOfferPageUrl($this->request->post['special_offer_seo_url']);

			$this->model_setting_setting->editSetting('module_special_offer', $special_offer_data);

		//	$this->cache->delete('special_offers');

			$this->session->data['success'] = $this->language->get('text_settings_saved');

			if (isset($this->request->get['apply'])) {
				$this->response->redirect($this->url->link('extension/module/special_offer', 'user_token=' . $this->session->data['user_token'] . $url, true));
			} else {
				$this->response->redirect($link_go_back);
			}
		}

		$this->document->setTitle($this->language->get('heading_title'));

		if (isset($this->error['warning'])) {
			$data['error_warning'] = $this->error['warning'];
		} else {
			$data['error_warning'] = '';
		}

		if (isset($this->error['keyword'])) {
			$data['error_keyword'] = $this->error['keyword'];
		} else {
			$data['error_keyword'] = '';
		}

		if (isset($this->session->data['success'])) {
			$data['success'] = $this->session->data['success'];
			unset($this->session->data['success']);
		} else {
			$data['success'] = '';
		}

		$data['breadcrumbs'] = array();

		$data['breadcrumbs'][] = array(
			'text' => $this->language->get('text_home'),
			'href' => $this->url->link('common/dashboard', 'user_token=' . $this->session->data['user_token'], true)
		);

		$data['breadcrumbs'][] = array(
			'text' => $this->language->get('text_extension'),
			'href' => $this->url->link('extension/extension', 'user_token=' . $this->session->data['user_token'] . '&type=module', true)
		);

		$data['breadcrumbs'][] = array(
			'text' => $this->language->get('heading_title'),
			'href' => $this->url->link('extension/module/special_offer', 'user_token=' . $this->session->data['user_token'], true)
		);

		$data['save'] = $this->url->link('extension/module/special_offer', 'user_token=' . $this->session->data['user_token'] . $url, true);
		$data['apply'] = $this->url->link('extension/module/special_offer', 'user_token=' . $this->session->data['user_token'] . '&apply' . $url, true);
        $data['cancel'] = $link_go_back;


		$data['timer_special_module'] = $this->getModuleData('timer_special_module', 1);
		$data['timer_special_page'] = $this->getModuleData('timer_special_page', 1);
		$data['timer_product_page'] = $this->getModuleData('timer_product_page', 1);
		$data['redirect_special_link'] = $this->getModuleData('redirect_special_link', 1);
		$data['show_ended_offers'] = $this->getModuleData('show_ended_offers', 1);
		$data['giftphoto_as_label'] = $this->getModuleData('giftphoto_as_label', 1);
		$data['status'] = $this->getModuleData('status', 1);
		$data['module_limit'] = $this->getModuleData('module_limit', 4);
		$data['gift_price'] = $this->getModuleData('gift_price', 0);

// start for SEO
		$this->load->model('setting/store');

		$data['stores'] = array();

		$data['stores'][] = array(
			'store_id' => 0,
			'name'     => $this->language->get('text_default')
		);

		$stores = $this->model_setting_store->getStores();

		foreach ($stores as $store) {
			$data['stores'][] = array(
				'store_id' => $store['store_id'],
				'name'     => $store['name']
			);
		}

		$this->load->model('localisation/language');
		$data['languages'] = $this->model_localisation_language->getLanguages();

		if (isset($this->request->post['special_offer_seo_url'])) {
			$data['special_offer_seo_url'] = $this->request->post['special_offer_seo_url'];
		} else {
			$data['special_offer_seo_url'] = $this->model_extension_module_special_offer->getSpecialOfferPageUrl();
		}

// end for SEO

		$data['header'] = $this->load->controller('common/header');
		$data['column_left'] = $this->load->controller('common/column_left');
		$data['footer'] = $this->load->controller('common/footer');

		$this->response->setOutput($this->load->view('extension/module/special_offer', $data));

	}

	public function offers() {

		$data = $this->load->language('extension/module/special_offer');

		$this->document->setTitle($this->language->get('heading_title'));

		$this->load->model('extension/module/special_offer');

        $this->getList();

    }

    public function edit() {
        $this->load->language('extension/module/special_offer');

        $this->document->setTitle($this->language->get('heading_title'));

		$this->load->model('extension/module/special_offer');

        $this->getForm();
    }	 

    public function delete() {
        $this->load->language('extension/module/special_offer');

        $this->document->setTitle($this->language->get('heading_title'));

 		$this->load->model('extension/module/special_offer');

        if (isset($this->request->post['selected']) && $this->validateDelete()) {
            foreach ($this->request->post['selected'] as $special_offer_id) {
                $this->model_extension_module_special_offer->deleteSpecialOffer($special_offer_id);
            }

            $this->session->data['success'] = $this->language->get('text_success');

            $url = '';

            if (isset($this->request->get['sort'])) {
                $url .= '&sort=' . $this->request->get['sort'];
            }

            if (isset($this->request->get['order'])) {
                $url .= '&order=' . $this->request->get['order'];
            }

            if (isset($this->request->get['page'])) {
                $url .= '&page=' . $this->request->get['page'];
            }

            $this->response->redirect($this->url->link('extension/module/special_offer/offers', 'user_token=' . $this->session->data['user_token'] . $url, true));
        }

        $this->getList();
    }

    public function delete_special() {
        $this->load->language('extension/module/special_offer');

/*         $this->document->setTitle($this->language->get('heading_title')); */

        $this->load->model('extension/module/special_offer');

        if (isset($this->request->post['selected']) && $this->validateDelete()) {
            foreach ($this->request->post['selected'] as $product_special_id) {
                $this->model_extension_module_special_offer->deleteSpecial($product_special_id);
            }

            $this->session->data['success'] = $this->language->get('text_success_delete_special');

            $url = '';

			if (isset($this->request->get['filter_product_name'])) {
				$url .= '&filter_product_name=' . urlencode(html_entity_decode($this->request->get['filter_product_name'], ENT_QUOTES, 'UTF-8'));
			}

			if (isset($this->request->get['filter_offer_id'])) {
				$url .= '&filter_offer_id=' . $this->request->get['filter_offer_id'];
			}

			if (isset($this->request->get['filter_customer_group_id'])) {
				$url .= '&filter_customer_group_id=' . $this->request->get['filter_customer_group_id'];
			}

			if (isset($this->request->get['filter_start_date'])) {
				$url .= '&filter_start_date=' . $this->request->get['filter_start_date'];
			}

			if (isset($this->request->get['filter_end_date'])) {
				$url .= '&filter_end_date=' . $this->request->get['filter_end_date'];
			}

            if (isset($this->request->get['sort'])) {
                $url .= '&sort=' . $this->request->get['sort'];
            }

            if (isset($this->request->get['order'])) {
                $url .= '&order=' . $this->request->get['order'];
            }

            if (isset($this->request->get['page'])) {
                $url .= '&page=' . $this->request->get['page'];
            }

            $this->response->redirect($this->url->link('extension/module/special_offer/all_specials', 'user_token=' . $this->session->data['user_token'] . $url, true));
        }

        $this->getList();
    }

    protected function getList() {
		$this->document->addStyle('view/stylesheet/special-offers-adm.css');

		$data = $this->load->language('extension/module/special_offer');

		$this->document->setTitle($this->language->get('heading_title'));


        if (isset($this->request->get['sort'])) {
            $sort = $this->request->get['sort'];
        } else {
            $sort = 'name';
        }

        if (isset($this->request->get['order'])) {
            $order = $this->request->get['order'];
        } else {
            $order = 'ASC';
        }

        if (isset($this->request->get['page'])) {
            $page = $this->request->get['page'];
        } else {
            $page = 1;
        }

        $url = '';

        if (isset($this->request->get['sort'])) {
            $url .= '&sort=' . $this->request->get['sort'];
        }

        if (isset($this->request->get['order'])) {
            $url .= '&order=' . $this->request->get['order'];
        }

        if (isset($this->request->get['page'])) {
            $url .= '&page=' . $this->request->get['page'];
        }

        $data['breadcrumbs'] = array();

        $data['breadcrumbs'][] = array(
            'text' => $this->language->get('text_home'),
            'href' => $this->url->link('common/dashboard', 'user_token=' . $this->session->data['user_token'], true)
        );

        $data['breadcrumbs'][] = array(
            'text' => $this->language->get('heading_title'),
            'href' => $this->url->link('extension/module/special_offer/offers', 'user_token=' . $this->session->data['user_token'] . $url, true)
        );

        $data['link_settings'] = $this->url->link('extension/module/special_offer', 'user_token=' . $this->session->data['user_token'] . '&back_offers' . $url, true);
		$data['link_all_specials'] = $this->url->link('extension/module/special_offer/all_specials', 'user_token=' . $this->session->data['user_token'] . '&back_offers' . $url, true);
        $data['add'] = $this->url->link('extension/module/special_offer/edit', 'user_token=' . $this->session->data['user_token'] . $url, true);
        $data['delete'] = $this->url->link('extension/module/special_offer/delete', 'user_token=' . $this->session->data['user_token'] . $url, true);
		$data['user_token'] = $this->session->data['user_token'];

        $data['special_offers'] = array();

        $filter_data = array(
            'sort'  => $sort,
            'order' => $order,
            'start' => ($page - 1) * $this->config->get('config_limit_admin'),
            'limit' => $this->config->get('config_limit_admin')
        );

		$this->load->model('extension/module/special_offer');

        $special_offer_total = $this->model_extension_module_special_offer->getTotalSpecialOffers();

        $results = $this->model_extension_module_special_offer->getSpecialOffers($filter_data);

        foreach ($results as $result) {
            $data['special_offers'][] = array(
                'special_offer_id' => $result['special_offer_id'],
                'name'            => $result['name'],
                'offer_type'      => $result['offer_type'],
                'date_start'      => $result['date_start'],
                'date_end'        => $result['date_end'],
                'priority'        => $result['priority'],
                'offer_status'    => $result['offer_status'],
                'edit'            => $this->url->link('extension/module/special_offer/edit', 'user_token=' . $this->session->data['user_token'] . '&special_offer_id=' . $result['special_offer_id'] . $url, true)
            );
        }

		$data['offer_types'] = [
			0 => $this->language->get('text_type_price'),
		    3 => $this->language->get('text_type_same_product_discount'),
		    6 => $this->language->get('text_type_any_product_discount'),
		    4 => $this->language->get('text_type_cart_discount'),
		    7 => $this->language->get('text_type_group_discount'),
			1 => $this->language->get('text_type_gift'),
			5 => $this->language->get('text_type_gift_from_amount'),
		    2 => $this->language->get('text_type_shipping') ];

        if (isset($this->error['warning'])) {
            $data['error_warning'] = $this->error['warning'];
        } else {
            $data['error_warning'] = '';
        }

        if (isset($this->session->data['success'])) {
            $data['success'] = $this->session->data['success'];

            unset($this->session->data['success']);
        } else {
            $data['success'] = '';
        }

        if (isset($this->request->post['selected'])) {
            $data['selected'] = (array)$this->request->post['selected'];
        } else {
            $data['selected'] = array();
        }

        $url = '';

        if ($order == 'ASC') {
            $url .= '&order=DESC';
        } else {
            $url .= '&order=ASC';
        }

        if (isset($this->request->get['page'])) {
            $url .= '&page=' . $this->request->get['page'];
        }

        $data['sort_name'] = $this->url->link('extension/module/special_offer/offers', 'user_token=' . $this->session->data['user_token'] . '&sort=name' . $url, true);
		$data['sort_offer_type'] = $this->url->link('extension/module/special_offer/offers', 'user_token=' . $this->session->data['user_token'] . '&sort=offer_type' . $url, true);
        $data['sort_date_start'] = $this->url->link('extension/module/special_offer/offers', 'user_token=' . $this->session->data['user_token'] . '&sort=date_start' . $url, true);
        $data['sort_date_end'] = $this->url->link('extension/module/special_offer/offers', 'user_token=' . $this->session->data['user_token'] . '&sort=date_end' . $url, true);
        $data['sort_priority'] = $this->url->link('extension/module/special_offer/offers', 'user_token=' . $this->session->data['user_token'] . '&sort=priority' . $url, true);

        $url = '';

        if (isset($this->request->get['sort'])) {
            $url .= '&sort=' . $this->request->get['sort'];
        }

        if (isset($this->request->get['order'])) {
            $url .= '&order=' . $this->request->get['order'];
        }

        $pagination = new Pagination();
        $pagination->total = $special_offer_total;
        $pagination->page = $page;
        $pagination->limit = $this->config->get('config_limit_admin');
        $pagination->url = $this->url->link('extension/module/special_offer/offers', 'user_token=' . $this->session->data['user_token'] . $url . '&page={page}', true);

        $data['pagination'] = $pagination->render();

        $data['results'] = sprintf($this->language->get('text_pagination'), ($special_offer_total) ? (($page - 1) * $this->config->get('config_limit_admin')) + 1 : 0, ((($page - 1) * $this->config->get('config_limit_admin')) > ($special_offer_total - $this->config->get('config_limit_admin'))) ? $special_offer_total : ((($page - 1) * $this->config->get('config_limit_admin')) + $this->config->get('config_limit_admin')), $special_offer_total, ceil($special_offer_total / $this->config->get('config_limit_admin')));

        $data['sort'] = $sort;
        $data['order'] = $order;

        $data['header'] = $this->load->controller('common/header');
        $data['column_left'] = $this->load->controller('common/column_left');
        $data['footer'] = $this->load->controller('common/footer');

        $this->response->setOutput($this->load->view('extension/module/special_offer_list', $data));
    }

    protected function getForm() {
		$this->document->addStyle('view/stylesheet/special-offers-adm.css');

        $data['text_form'] = !isset($this->request->get['special_offer_id']) ? $this->language->get('text_add') : $this->language->get('text_edit');

		$data['offer_types'] = [
			0 => $this->language->get('text_type_price'),
		    3 => $this->language->get('text_type_same_product_discount'),
		    6 => $this->language->get('text_type_any_product_discount'),
		    4 => $this->language->get('text_type_cart_discount'),
		    7 => $this->language->get('text_type_group_discount'),
			1 => $this->language->get('text_type_gift'),
			5 => $this->language->get('text_type_gift_from_amount'),
		    2 => $this->language->get('text_type_shipping') ];

        if (isset($this->error['warning'])) {
            $data['error_warning'] = $this->error['warning'];
        } else {
            $data['error_warning'] = '';
        }

        if (isset($this->error['name'])) {
            $data['error_name'] = $this->error['name'];
        } else {
            $data['error_name'] = '';
        }

		if (isset($this->error['meta_title'])) {
			$data['error_meta_title'] = $this->error['meta_title'];
		} else {
			$data['error_meta_title'] = array();
		}

        if (isset($this->error['description'])) {
            $data['error_description'] = $this->error['description'];
        } else {
            $data['error_description'] = '';
        }

		if (isset($this->error['keyword'])) {
			$data['error_keyword'] = $this->error['keyword'];
		} else {
			$data['error_keyword'] = '';
		}

		if (isset($this->error['customer_group'])) {
			$data['error_customer_group'] = $this->error['customer_group'];
		} else {
			$data['error_customer_group'] = '';
		}

		if (isset($this->error['product_quantity'])) {
			$data['error_product_quantity'] = $this->error['product_quantity'];
		} else {
			$data['error_product_quantity'] = '';
		}

		if (isset($this->error['gift_quantity'])) {
			$data['error_gift_quantity'] = $this->error['gift_quantity'];
		} else {
			$data['error_gift_quantity'] = '';
		}

		if (isset($this->request->post['selected'])) {
			$data['selected'] = (array)$this->request->post['selected'];
		} else {
			$data['selected'] = array();
		}

         $url = '';

        if (isset($this->request->get['sort'])) {
            $url .= '&sort=' . $this->request->get['sort'];
        }

        if (isset($this->request->get['order'])) {
            $url .= '&order=' . $this->request->get['order'];
        }

        if (isset($this->request->get['page'])) {
            $url .= '&page=' . $this->request->get['page'];
        }

        $data['breadcrumbs'] = array();

        $data['breadcrumbs'][] = array(
            'text' => $this->language->get('text_home'),
            'href' => $this->url->link('common/dashboard', 'user_token=' . $this->session->data['user_token'], true)
        );

        $data['breadcrumbs'][] = array(
            'text' => $this->language->get('heading_title'),
            'href' => $this->url->link('extension/module/special_offer/offers', 'user_token=' . $this->session->data['user_token'] . $url, true)
        );

        if (!isset($this->request->get['special_offer_id'])) {
            $data['special_offer_id'] = '0';
            $data['total_found_products'] = array();
        } else {
            $data['special_offer_id'] = $this->request->get['special_offer_id'];
			$data['total_found_products'] = $this->model_extension_module_special_offer->getTotalFoundSOMProducts($this->request->get['special_offer_id']);
        }

        $data['cancel'] = $this->url->link('extension/module/special_offer/offers', 'user_token=' . $this->session->data['user_token'] . $url, true);

		$this->load->model('extension/module/special_offer');

        if (isset($this->request->get['special_offer_id']) && ($this->request->server['REQUEST_METHOD'] != 'POST')) {
            $special_offer_info = $this->model_extension_module_special_offer->getSpecialOffer($this->request->get['special_offer_id']);
        }

        $data['user_token'] = $this->session->data['user_token'];

		$this->load->model('localisation/language');

		$data['languages'] = $this->model_localisation_language->getLanguages();

		$this->load->model('catalog/product');
		$this->load->model('catalog/manufacturer');
		$this->load->model('catalog/category');
		$this->load->model('customer/customer_group');

		if (isset($this->request->post['special_offer_description'])) {
			$data['special_offer_description'] = $this->request->post['special_offer_description'];
		} elseif (isset($this->request->get['special_offer_id'])) {
			$data['special_offer_description'] = $this->model_extension_module_special_offer->getSpecialOfferDescriptions($this->request->get['special_offer_id']);
		} else {
			$data['special_offer_description'] = array();
		}

		if (isset($this->request->post['offer_type'])) {
			$data['offer_type'] = $this->request->post['offer_type'];
		} elseif (!empty($special_offer_info)) {
			$data['offer_type'] = $special_offer_info['offer_type'];
		} else {
			$data['offer_type'] = '0';
		}

		if (isset($this->request->post['gift_product_id'])) {
			$data['gift_product_id'] = $this->request->post['gift_product_id'];
		} elseif (!empty($special_offer_info)) {
			$data['gift_product_id'] = $special_offer_info['gift_product_id'];
		} else {
			$data['gift_product_id'] = '0';
		}

		if (!empty($data['gift_product_id'])) {
			$gift_info = $this->model_catalog_product->getProduct($data['gift_product_id']);
			$data['gift_product_name'] = $gift_info['name'];
		} else {
			$data['gift_product_name'] = '';
		}

        if (isset($this->request->post['product_sum'])) {
            $data['product_sum'] = $this->request->post['product_sum'];
        } elseif (!empty($special_offer_info)) {
            $data['product_sum'] = $special_offer_info['product_sum'];
        } else {
            $data['product_sum'] = 0;
        }

        if (isset($this->request->post['product_quantity'])) {
            $data['product_quantity'] = $this->request->post['product_quantity'];
        } elseif (!empty($special_offer_info)) {
            $data['product_quantity'] = $special_offer_info['product_quantity'];
        } else {
            $data['product_quantity'] = 1;
        }

        if (isset($this->request->post['gift_quantity'])) {
            $data['gift_quantity'] = $this->request->post['gift_quantity'];
        } elseif (!empty($special_offer_info)) {
            $data['gift_quantity'] = $special_offer_info['gift_quantity'];
        } else {
            $data['gift_quantity'] = 1;
        }

        if (isset($this->request->post['percent'])) {
            $data['percent'] = $this->request->post['percent'];
        } elseif (!empty($special_offer_info)) {
            $data['percent'] = $special_offer_info['percent'];
        } else {
            $data['percent'] = 1;
        }

		$data['customer_groups'] = $this->model_customer_customer_group->getCustomerGroups();

		if (isset($this->request->post['special_offer_customer_group'])) {
			$data['special_offer_customer_group'] = $this->request->post['special_offer_customer_group'];
		} elseif (!empty($special_offer_info)) {
			$list_customer_group_id = substr($special_offer_info['list_customer_group_id'],1,-1);
			$data['special_offer_customer_group'] = explode(",", $list_customer_group_id);
		} else {
			$data['special_offer_customer_group'] = array(0);
		}

		if (isset($this->request->post['date_start'])) {
			$data['date_start'] = $this->request->post['date_start'];
		} elseif (!empty($special_offer_info)) {
			$data['date_start'] = ($special_offer_info['date_start'] != '0000-00-00') ? $special_offer_info['date_start'] : '';
		} else {
			$data['date_start'] = date('Y-m-d');
		}

		if (isset($this->request->post['date_end'])) {
			$data['date_end'] = $this->request->post['date_end'];
		} elseif (!empty($special_offer_info)) {
			$data['date_end'] = ($special_offer_info['date_end'] != '0000-00-00') ? $special_offer_info['date_end'] : '';
		} else {
			$data['date_end'] = date('Y-m-d');
		}

        if (isset($this->request->post['cycle_of_timer'])) {
            $data['cycle_of_timer'] = $this->request->post['cycle_of_timer'];
        } elseif (!empty($special_offer_info)) {
            $data['cycle_of_timer'] = $special_offer_info['cycle_of_timer'];
        } else {
            $data['cycle_of_timer'] = 0;
        }

		$this->load->model('setting/store');

		$data['stores'] = array();

		$data['stores'][] = array(
			'store_id' => 0,
			'name'     => $this->language->get('text_default')
		);

		$stores = $this->model_setting_store->getStores();

		foreach ($stores as $store) {
			$data['stores'][] = array(
				'store_id' => $store['store_id'],
				'name'     => $store['name']
			);
		}

        if (isset($this->request->post['image'])) {
            $data['image'] = $this->request->post['image'];
        } elseif (!empty($special_offer_info)) {
            $data['image'] = $special_offer_info['image'];
        } else {
            $data['image'] = '';
        }

        $this->load->model('tool/image');

        if (isset($this->request->post['image']) && is_file(DIR_IMAGE . $this->request->post['image'])) {
            $data['thumb'] = $this->model_tool_image->resize($this->request->post['image'], 100, 100);
        } elseif (!empty($special_offer_info) && is_file(DIR_IMAGE . $special_offer_info['image'])) {
            $data['thumb'] = $this->model_tool_image->resize($special_offer_info['image'], 100, 100);
        } else {
            $data['thumb'] = $this->model_tool_image->resize('no_image.png', 100, 100);
        }

        if (isset($this->request->post['image-label'])) {
            $data['image_label'] = $this->request->post['image_label'];
        } elseif (!empty($special_offer_info)) {
            $data['image_label'] = $special_offer_info['image_label'];
        } else {
            $data['image_label'] = '';
        }

        if (isset($this->request->post['image_label']) && is_file(DIR_IMAGE . $this->request->post['image_label'])) {
            $data['thumb_label'] = $this->model_tool_image->resize($this->request->post['image_label'], 100, 100);
        } elseif (!empty($special_offer_info) && is_file(DIR_IMAGE . $special_offer_info['image_label'])) {
            $data['thumb_label'] = $this->model_tool_image->resize($special_offer_info['image_label'], 100, 100);
        } else {
            $data['thumb_label'] = $this->model_tool_image->resize('no_image.png', 100, 100);
        }

        $data['placeholder'] = $this->model_tool_image->resize('no_image.png', 100, 100);


        if (isset($this->request->post['priority'])) {
            $data['priority'] = $this->request->post['priority'];
        } elseif (!empty($special_offer_info)) {
            $data['priority'] = $special_offer_info['priority'];
        } else {
            $data['priority'] = 3;
        }

		if (isset($this->request->post['timer_status'])) {
			$data['timer_status'] = $this->request->post['timer_status'];
		} elseif (!empty($special_offer_info)) {
			$data['timer_status'] = $special_offer_info['timer_status'];
		} else {
			$data['timer_status'] = 1;
		}

		if (isset($this->request->post['free_shipping'])) {
			$data['free_shipping'] = $this->request->post['free_shipping'];
		} elseif (!empty($special_offer_info)) {
			$data['free_shipping'] = $special_offer_info['free_shipping'];
		} else {
			$data['free_shipping'] = 0;
		}

		$this->load->model('localisation/language');

		$data['languages'] = $this->model_localisation_language->getLanguages();

		if (isset($this->request->post['special_offer_seo_url'])) {
			$data['special_offer_seo_url'] = $this->request->post['special_offer_seo_url'];
		} elseif (isset($this->request->get['special_offer_id'])) {
			$data['special_offer_seo_url'] = $this->model_extension_module_special_offer->getSpecialOfferSeoUrls($this->request->get['special_offer_id']);
		} else {
			$data['special_offer_seo_url'] = array();
		}

		if (isset($this->request->post['filter_name'])) {
			$data['filter_name'] = $this->request->post['filter_name'];
		} else {
			$data['filter_name'] = '';
		}

		if (isset($this->request->post['filter_sku'])) {
			$data['filter_sku'] = $this->request->post['filter_sku'];
		} else {
			$data['filter_sku'] = '';
		}

		if (isset($this->request->post['filter_model'])) {
			$data['filter_model'] = $this->request->post['filter_model'];
		} else {
			$data['filter_model'] = '';
		}
		if (isset($this->request->post['filter_min_price'])) {
			$data['filter_min_price'] = $this->request->post['filter_min_price'];
		} else {
			$data['filter_min_price'] = '';
		}

		if (isset($this->request->post['filter_max_price'])) {
			$data['filter_max_price'] = $this->request->post['filter_max_price'];
		} else {
			$data['filter_max_price'] = '';
		}

		if (isset($this->request->post['offer_status'])) {
			$data['offer_status'] = $this->request->post['offer_status'];
		} elseif (!empty($special_offer_info)) {
			$data['offer_status'] = $special_offer_info['offer_status'];
		} else {
			$data['offer_status'] = 1;
		}
        $data['header'] = $this->load->controller('common/header');
        $data['column_left'] = $this->load->controller('common/column_left');
        $data['footer'] = $this->load->controller('common/footer');

		$sort_categ = array();
		$sort_categ['sort'] = 'name';
		$data['all_categories'] = $this->model_catalog_category->getCategories($sort_categ);
		$data['all_manufacturers'] = $this->model_catalog_manufacturer->getManufacturers(0);

        $this->response->setOutput($this->load->view('extension/module/special_offer_form', $data));
    }

    public function getProducts() {
		$this->load->model('extension/module/special_offer');

		if (isset($this->request->post['offer_id'])) {
			$products = $this->model_extension_module_special_offer->getProductsBySpecialOfferId($this->request->post['offer_id']);
		} else {
			$products = array();
		}

		$json = array();
		$json['quantity'] = count($products);

		$json['products'] = $this->getInfoProducts($products,0);

 		$this->response->addHeader('Content-Type: application/json');
		$this->response->setOutput(json_encode($json));

    }

    public function foundSOMProducts() {
		$this->load->model('extension/module/special_offer');

		if (isset($this->request->post['offer_id'])) {
			$products = $this->model_extension_module_special_offer->getFoundSOMProducts($this->request->post['offer_id']);
		} else {
			$products = array();
		}

		$json = array();
		$json['quantity'] = count($products);

		$json['products'] = $this->getInfoProducts($products);

 		$this->response->addHeader('Content-Type: application/json');
		$this->response->setOutput(json_encode($json));
    }
	
	public function saveOffer() {
		$this->load->model('catalog/product');
		$this->load->model('extension/module/special_offer');
		$this->load->model('catalog/manufacturer');
		$this->load->model('catalog/category');

		$json = array();

		if (isset($_POST['offer_id'])) {
			$offer_id = $_POST['offer_id'];
		} else {
			$offer_id = 0;
		}

		if ($_POST['offer_type']==5) $_POST['product_quantity']=1;
		if ($_POST['offer_type']==1) $_POST['product_sum']=0;

 	    $this->load->language('extension/module/special_offer');
		
		$json['error'] = $this->validateForm($offer_id, $_POST);
		
$this->log->write(print_r($json['error'],true));

        if (!$json['error']) {

			if ($offer_id) {
				$this->model_extension_module_special_offer->editSpecialOffer($offer_id,$_POST);
			} else {
				$this->model_extension_module_special_offer->addSpecialOffer($_POST);
			}

            $this->session->data['success'] = $this->language->get('text_success');
        }

		$this->response->addHeader('Content-Type: application/json');
		$this->response->setOutput(json_encode($json));
	}
	public function addProductsToList() {
		$this->load->model('extension/module/special_offer');

		$json = array();

		if ($this->request->server['REQUEST_METHOD'] == 'POST') {
			$category_id = $this->request->post['category_id'];
			$manufacturer_id = $this->request->post['manufacturer_id'];
			$filter_name = $this->request->post['filter_name'];
			$filter_sku = $this->request->post['filter_sku'];
			$filter_model = $this->request->post['filter_model'];
			$filter_min_price = $this->request->post['filter_min_price'];
			$filter_max_price = $this->request->post['filter_max_price'];
			$special_offer_id = $this->request->post['special_offer_id'];
			$filter_special_status = $this->request->post['filter_special_status'];

			$data = array(
				'filter_category_id' => $category_id,
				'filter_manufacturer_id' => $manufacturer_id,
				'filter_name' => $filter_name,
				'filter_sku' => $filter_sku,
				'filter_model' => $filter_model,
				'filter_min_price' => $filter_min_price,
				'filter_max_price' => $filter_max_price,
				'filter_special_status' => $filter_special_status,
				'filter_exclude_special_offer_id' => $special_offer_id
			);

			if (isset($this->request->post['filter_status'])) {
				$data['filter_status'] = '1';
			} else {
				$data['filter_status'] = NULL;
			}

			if (isset($this->request->post['filter_in_stock'])) {
				$data['filter_in_stock'] = 1;
			} else {
				$data['filter_in_stock'] = 0;
			}

			$results = $this->model_extension_module_special_offer->getProductsByFilter($data); // Get Products by Category & Manufacturer & Name
			$json['quantity'] = count($results);

			$json['products'] = $this->getInfoProducts($results);
		}

		$this->response->addHeader('Content-Type: application/json');
		$this->response->setOutput(json_encode($json));
	}

    protected function getInfoProducts($products, $fl_status=1) {
		$this->load->model('catalog/product');
		$this->load->model('catalog/manufacturer');
		$this->load->model('catalog/category');
		$this->load->model('tool/image');

		$info = array();

		if ($products) {
			foreach ($products as $product) {
 				$product_info=$this->model_catalog_product->getProduct($product['product_id']);

				if (is_file(DIR_IMAGE . $product_info['image'])) {
					$image = $this->model_tool_image->resize($product_info['image'], 40, 40);
				} else {
					$image = $this->model_tool_image->resize('no_image.png', 40, 40);
				}

				$manufacturer_info = $this->model_catalog_manufacturer->getManufacturer($product_info['manufacturer_id']);
				if ($manufacturer_info) {
					$manufacturer_name = $manufacturer_info['name'];
				} else {
					$manufacturer_name = '';
				}

				$categories = $this->model_catalog_product->getProductCategories($product['product_id']);
				$categories_name = array();
				foreach ($categories as $category_id) {
					$category_info = $this->model_catalog_category->getCategory($category_id);

					if ($category_info) {
						$categories_name[] = ($category_info['path']) ? $category_info['path'] . ' &gt; ' . $category_info['name'] : $category_info['name'];
					}
				}

				if ($fl_status) {
					$status=1;
				} elseif (isset($product['status']) && ($product['status']==1)) {
					$status=1;
				} else {
					$status=0;
				}

				$info[] = array(
					'product_id'  => $product['product_id'],
					'thumb'       => $image,
					'name'        => $product_info['name'],
					'sku'         => $product_info['sku'],
					'model'       => $product_info['model'],
					'price'       => $product_info['price'],
					'special_price'    => $product['special_price'] ? $product['special_price'] : $product_info['price'],
					'quantity'    => $product_info['quantity'],
					'status'      => $status,
					'manufacturer_name' => $manufacturer_name,
					'categories'  => $categories_name
				);
			}
		}

		return $info;
	}

	public function addProductToListByID() {
		$this->load->model('catalog/product');
		$this->load->model('extension/module/special_offer');
		$this->load->model('catalog/manufacturer');
		$this->load->model('catalog/category');

		$json = array();

		if($_POST['product_id']) {
			$product_id = $_POST['product_id'];

			$product_info = $this->model_catalog_product->getProduct($product_id);

			$this->load->model('tool/image');

			$json['error'] = '';
			$json['success'] = $product_info['product_id'];
			$json['product'] = array();

			if (is_file(DIR_IMAGE . $product_info['image'])) {
				$image = $this->model_tool_image->resize($product_info['image'], 40, 40);
			} else {
				$image = $this->model_tool_image->resize('no_image.png', 40, 40);
			}

			$manufacturer_info = $this->model_catalog_manufacturer->getManufacturer($product_info['manufacturer_id']);
			if ($manufacturer_info) {
				$manufacturer_name = $manufacturer_info['name'];
			} else {
				$manufacturer_name = '';
			}

			$categories = $this->model_catalog_product->getProductCategories($product_info['product_id']);
			$categories_name = array();
			foreach ($categories as $category_id) {
				$category_info = $this->model_catalog_category->getCategory($category_id);
				if ($category_info) {
					$categories_name[] = ($category_info['path']) ? $category_info['path'] . ' &gt; ' . $category_info['name'] : $category_info['name'];
				}
			}

			$special_price = false;
			$product_specials = $this->model_catalog_product->getProductSpecials($product_info['product_id']);
			foreach ($product_specials  as $product_special) {
				if (($product_special['date_start'] == '0000-00-00' || strtotime($product_special['date_start']) < time()) && ($product_special['date_end'] == '0000-00-00' || strtotime($product_special['date_end']) > time())) {
					$special_price = $product_special['price'];
					break;
				}
			}
			$json['product'] = array(
				'product_id' => $product_info['product_id'],
				'thumb'       => $image,
				'name'        => $product_info['name'],
				'sku'        => $product_info['sku'],
				'model'      => $product_info['model'],
				'price'      => $product_info['price'],
 				'special_price'  => $special_price ? special_price : $product_info['price'],
				'quantity'   => $product_info['quantity'],
				'status'     => 1,
				'manufacturer_name' => $manufacturer_name,
				'categories'   => $categories_name
			);
		}

		$this->response->addHeader('Content-Type: application/json');
		$this->response->setOutput(json_encode($json));
	}

	public function all_specials() {
		$this->document->addStyle('view/stylesheet/special-offers-adm.css');

		$data = $this->load->language('extension/module/special_offer');

        $this->document->setTitle($this->language->get('heading_title'));

        $this->load->model('extension/module/special_offer');

		$this->load->model('customer/customer_group');

		if (isset($this->request->get['filter_product_name'])) {
			$filter_product_name = $this->request->get['filter_product_name'];
		} else {
			$filter_product_name = null;
		}

		if (isset($this->request->get['filter_offer_id'])) {
			$filter_offer_id = $this->request->get['filter_offer_id'];
		} else {
			$filter_offer_id = null;
		}

		if (isset($this->request->get['filter_customer_group_id'])) {
			$filter_customer_group_id = $this->request->get['filter_customer_group_id'];
		} else {
			$filter_customer_group_id = null;
		}

		if (isset($this->request->get['filter_start_date'])) {
			$filter_start_date = $this->request->get['filter_start_date'];
		} else {
			$filter_start_date = null;
		}

		if (isset($this->request->get['filter_end_date'])) {
			$filter_end_date = $this->request->get['filter_end_date'];
		} else {
			$filter_end_date = null;
		}

		if (isset($this->request->get['sort'])) {
			$sort = $this->request->get['sort'];
		} else {
			$sort = 'pd.name';
		}

		if (isset($this->request->get['order'])) {
			$order = $this->request->get['order'];
		} else {
			$order = 'ASC';
		}

		if (isset($this->request->get['page'])) {
			$page = $this->request->get['page'];
		} else {
			$page = 1;
		}

		$url = '';

		if (isset($this->request->get['filter_product_name'])) {
			$url .= '&filter_product_name=' . urlencode(html_entity_decode($this->request->get['filter_product_name'], ENT_QUOTES, 'UTF-8'));
		}

		if (isset($this->request->get['filter_offer_id'])) {
			$url .= '&filter_offer_id=' . $this->request->get['filter_offer_id'];
		}

		if (isset($this->request->get['filter_customer_group_id'])) {
			$url .= '&filter_customer_group_id=' . $this->request->get['filter_customer_group_id'];
		}

		if (isset($this->request->get['filter_start_date'])) {
			$url .= '&filter_start_date=' . $this->request->get['filter_start_date'];
		}

		if (isset($this->request->get['filter_end_date'])) {
			$url .= '&filter_end_date=' . $this->request->get['filter_end_date'];
		}

		if (isset($this->request->get['sort'])) {
			$url .= '&sort=' . $this->request->get['sort'];
		}

		if (isset($this->request->get['order'])) {
			$url .= '&order=' . $this->request->get['order'];
		}

		if (isset($this->request->get['page'])) {
			$url .= '&page=' . $this->request->get['page'];
		}

		$data['breadcrumbs'] = array();

		$data['breadcrumbs'][] = array(
			'text' => $this->language->get('text_home'),
			'href' => $this->url->link('common/dashboard', 'user_token=' . $this->session->data['user_token'], true)
		);

		$data['breadcrumbs'][] = array(
			'text' => $this->language->get('heading_title'),
			'href' => $this->url->link('extension/module/special_offer/offers', 'user_token=' . $this->session->data['user_token'] . $url, true)
		);

		$data['breadcrumbs'][] = array(
			'text' => $this->language->get('text_all_specials_list'),
			'href' => $this->url->link('extension/module/special_offer/all_specials', 'user_token=' . $this->session->data['user_token'] . $url, true)
		);

		$data['add'] = $this->url->link('catalog/product/add', 'user_token=' . $this->session->data['user_token'] . $url, true);
		$data['copy'] = $this->url->link('catalog/product/copy', 'user_token=' . $this->session->data['user_token'] . $url, true);
		$data['delete'] = $this->url->link('extension/module/special_offer/delete_special', 'user_token=' . $this->session->data['user_token'] . $url, true);

		$data['products'] = array();

		$filter_data = array(
			'filter_product_name' => $filter_product_name,
			'filter_offer_id' => $filter_offer_id,
			'filter_customer_group_id' => $filter_customer_group_id,
			'filter_start_date' => $filter_start_date,
			'filter_end_date' => $filter_end_date,

			'sort'            => $sort,
			'order'           => $order,
			'start'           => ($page - 1) * $this->config->get('config_limit_admin'),
			'limit'           => $this->config->get('config_limit_admin')
		);

		$this->load->model('tool/image');
		$this->load->model('catalog/product');
		$this->load->model('extension/module/special_offer');

		$product_total = $this->model_extension_module_special_offer->getTotalSpecialProducts($filter_data);

		$results = $this->model_extension_module_special_offer->getSpecialProductsByFilter($filter_data);

		foreach ($results as $result) {
			if (is_file(DIR_IMAGE . $result['image'])) {
				$image = $this->model_tool_image->resize($result['image'], 40, 40);
			} else {
				$image = $this->model_tool_image->resize('no_image.png', 40, 40);
			}

			$customer_group = $this->model_customer_customer_group->getCustomerGroup($result['customer_group_id']);

			if ($result['special_offer_id']) {
				$special_offer_info = $this->model_extension_module_special_offer->getSpecialOffer($result['special_offer_id']);
				$special_offer_name = $special_offer_info['name'];
			} else {
				$special_offer_name = '';
			}

			$data['products'][] = array(
				'product_special_id' => $result['product_special_id'],
				'product_id' => $result['product_id'],
				'image'      => $image,
				'name'       => $result['name'],
				'model'      => $result['model'],
				'price'      => $result['price'],
				'special'    => $result['special_price'],
				'customer_group_name' => $customer_group['name'],
				'customer_group_id' => $result['customer_group_id'],
				'priority'          => $result['priority'],
				'date_start'        => ($result['date_start'] != '0000-00-00') ? date($this->language->get('date_format_short'), strtotime($result['date_start'])) : '',
				'date_end'          => ($result['date_end'] != '0000-00-00') ? date($this->language->get('date_format_short'), strtotime($result['date_end'])) :  '',
				'special_offer_name'  => $special_offer_name,
				'special_offer_id'    => $result['special_offer_id'],

				'edit'       => $this->url->link('catalog/product/edit', 'user_token=' . $this->session->data['user_token'] . '&product_id=' . $result['product_id'] . $url, true)
			);
		}

		$data['customer_groups'] = $this->model_customer_customer_group->getCustomerGroups();

		$special_offers = $this->model_extension_module_special_offer->getSpecialOffers();

        $data['special_offers'][0] = array(
                'special_offer_id' => '0',
                'name'             => $this->language->get('text_no_special_offer')
            );

        foreach ($special_offers as $special_offer) {
            $data['special_offers'][] = array(
                'special_offer_id' => $special_offer['special_offer_id'],
                'name'             => $special_offer['name']
			);
		}

		$data['link_all_spec_offers'] = $this->url->link('extension/module/special_offer/offers', 'user_token=' . $this->session->data['user_token'], true);

		$data['heading_title'] = $this->language->get('heading_title');

		$data['text_list'] = $this->language->get('text_list');
		$data['text_enabled'] = $this->language->get('text_enabled');
		$data['text_disabled'] = $this->language->get('text_disabled');
		$data['text_no_results'] = $this->language->get('text_no_results');
		$data['text_confirm'] = $this->language->get('text_confirm');

		$data['column_image'] = $this->language->get('column_image');
		$data['column_name'] = $this->language->get('column_name');
		$data['column_model'] = $this->language->get('column_model');
		$data['column_price'] = $this->language->get('column_price');
		$data['column_quantity'] = $this->language->get('column_quantity');
		$data['column_status'] = $this->language->get('column_status');
		$data['column_action'] = $this->language->get('column_action');

		$data['entry_name'] = $this->language->get('entry_name');
		$data['entry_model'] = $this->language->get('entry_model');
		$data['entry_price'] = $this->language->get('entry_price');
		$data['entry_quantity'] = $this->language->get('entry_quantity');
		$data['entry_status'] = $this->language->get('entry_status');
		$data['entry_image'] = $this->language->get('entry_image');

		$data['button_copy'] = $this->language->get('button_copy');
		$data['button_add'] = $this->language->get('button_add');
		$data['button_edit'] = $this->language->get('button_edit');
		$data['button_delete'] = $this->language->get('button_delete');
		$data['button_filter'] = $this->language->get('button_filter');

		$data['user_token'] = $this->session->data['user_token'];

		if (isset($this->error['warning'])) {
			$data['error_warning'] = $this->error['warning'];
		} else {
			$data['error_warning'] = '';
		}

		if (isset($this->session->data['success'])) {
			$data['success'] = $this->session->data['success'];

			unset($this->session->data['success']);
		} else {
			$data['success'] = '';
		}

		if (isset($this->request->post['selected'])) {
			$data['selected'] = (array)$this->request->post['selected'];
		} else {
			$data['selected'] = array();
		}

		$url = '';

		if (isset($this->request->get['filter_product_name'])) {
			$url .= '&filter_product_name=' . urlencode(html_entity_decode($this->request->get['filter_product_name'], ENT_QUOTES, 'UTF-8'));
		}

		if (isset($this->request->get['filter_offer_id'])) {
			$url .= '&filter_offer_id=' . $this->request->get['filter_offer_id'];
		}

		if (isset($this->request->get['filter_customer_group_id'])) {
			$url .= '&filter_customer_group_id=' . $this->request->get['filter_customer_group_id'];
		}

		if (isset($this->request->get['filter_start_date'])) {
			$url .= '&filter_start_date=' . $this->request->get['filter_start_date'];
		}

		if (isset($this->request->get['filter_end_date'])) {
			$url .= '&filter_end_date=' . $this->request->get['filter_end_date'];
		}

		if ($order == 'ASC') {
			$url .= '&order=DESC';
		} else {
			$url .= '&order=ASC';
		}

		if (isset($this->request->get['page'])) {
			$url .= '&page=' . $this->request->get['page'];
		}

		$data['sort_name'] = $this->url->link('extension/module/special_offer/all_specials', 'user_token=' . $this->session->data['user_token'] . '&sort=pd.name' . $url, true);
		$data['sort_price'] = $this->url->link('extension/module/special_offer/all_specials', 'user_token=' . $this->session->data['user_token'] . '&sort=p.price' . $url, true);
		$data['sort_model'] = $this->url->link('extension/module/special_offer/all_specials', 'user_token=' . $this->session->data['user_token'] . '&sort=p.model' . $url, true);
		$data['sort_date_start'] = $this->url->link('extension/module/special_offer/all_specials', 'user_token=' . $this->session->data['user_token'] . '&sort=date_start' . $url, true);
		$data['sort_date_end'] = $this->url->link('extension/module/special_offer/all_specials', 'user_token=' . $this->session->data['user_token'] . '&sort=date_end' . $url, true);
		$data['sort_priority'] = $this->url->link('extension/module/special_offer/all_specials', 'user_token=' . $this->session->data['user_token'] . '&sort=priority' . $url, true);

		$url = '';

		if (isset($this->request->get['filter_product_name'])) {
			$url .= '&filter_product_name=' . urlencode(html_entity_decode($this->request->get['filter_product_name'], ENT_QUOTES, 'UTF-8'));
		}

		if (isset($this->request->get['filter_offer_id'])) {
			$url .= '&filter_offer_id=' . urlencode(html_entity_decode($this->request->get['filter_offer_id'], ENT_QUOTES, 'UTF-8'));
		}

		if (isset($this->request->get['filter_customer_group_id'])) {
			$url .= '&filter_customer_group_id=' . urlencode(html_entity_decode($this->request->get['filter_customer_group_id'], ENT_QUOTES, 'UTF-8'));
		}

		if (isset($this->request->get['filter_start_date'])) {
			$url .= '&filter_start_date=' . $this->request->get['filter_start_date'];
		}

		if (isset($this->request->get['filter_end_date'])) {
			$url .= '&filter_end_date=' . $this->request->get['filter_end_date'];
		}

		if (isset($this->request->get['sort'])) {
			$url .= '&sort=' . $this->request->get['sort'];
		}

		if (isset($this->request->get['order'])) {
			$url .= '&order=' . $this->request->get['order'];
		}

		$pagination = new Pagination();
		$pagination->total = $product_total;
		$pagination->page = $page;
		$pagination->limit = $this->config->get('config_limit_admin');
		$pagination->url = $this->url->link('catalog/product', 'user_token=' . $this->session->data['user_token'] . $url . '&page={page}', true);

		$data['pagination'] = $pagination->render();

		$data['results'] = sprintf($this->language->get('text_pagination'), ($product_total) ? (($page - 1) * $this->config->get('config_limit_admin')) + 1 : 0, ((($page - 1) * $this->config->get('config_limit_admin')) > ($product_total - $this->config->get('config_limit_admin'))) ? $product_total : ((($page - 1) * $this->config->get('config_limit_admin')) + $this->config->get('config_limit_admin')), $product_total, ceil($product_total / $this->config->get('config_limit_admin')));

		$data['filter_product_name'] = $filter_product_name;
		$data['filter_offer_id'] = $filter_offer_id;
		$data['filter_customer_group_id'] = $filter_customer_group_id;
		$data['filter_start_date'] = $filter_start_date;
		$data['filter_end_date'] = $filter_end_date;

		$data['sort'] = $sort;
		$data['order'] = $order;

		$data['header'] = $this->load->controller('common/header');
		$data['column_left'] = $this->load->controller('common/column_left');
		$data['footer'] = $this->load->controller('common/footer');

		$this->response->setOutput($this->load->view('extension/module/special_offer_all_products', $data));
	}

	protected function validate() {
		if (!$this->user->hasPermission('modify', 'extension/module/special_offer')) {
			$this->error['warning'] = $this->language->get('error_permission');
		}

		if ($this->request->post['special_offer_seo_url']) {
			$this->load->model('design/seo_url');

			foreach ($this->request->post['special_offer_seo_url'] as $store_id => $language) {
				foreach ($language as $language_id => $keyword) {
					if (!empty($keyword)) {
						if (count(array_keys($language, $keyword)) > 1) {
							$this->error['keyword'][$store_id][$language_id] = $this->language->get('error_unique');
						}

						$seo_urls = $this->model_design_seo_url->getSeoUrlsByKeyword($keyword);

						foreach ($seo_urls as $seo_url) {
							if ($seo_url['query'] != 'extension/module/special_offer/offerlist') {
								$this->error['keyword'][$store_id][$language_id] = $this->language->get('error_keyword');
							}
						}
					}
				}
			}
		}

		if ($this->error && !isset($this->error['warning'])) {
			$this->error['warning'] = $this->language->get('error_warning');
		}

		return !$this->error;
	}

    protected function validateForm($offer_id,$offer_info) {
		$form_error = array();

        if (!$this->user->hasPermission('modify', 'extension/module/special_offer')) {
            $form_error['warning'] = $this->language->get('error_permission');
        }

		foreach ($offer_info['special_offer_description'] as $language_id => $value) {
			if ((utf8_strlen($value['name']) < 2) || (utf8_strlen($value['name']) > 255)) {
				$form_error['name'.$language_id] = $this->language->get('error_name');
			}

			if ((utf8_strlen($value['meta_title']) < 3) || (utf8_strlen($value['meta_title']) > 255)) {
				$form_error['meta_title'.$language_id] = $this->language->get('error_meta_title');
			}
		}

		if (!isset($offer_info['special_offer_customer_group'])) {
			$form_error['customer_group'] = $this->language->get('error_customer_group');
		}

		if (!isset($offer_info['product_quantity']) || !preg_match('/^[1-9]{1}[\d]*$/', trim($offer_info['product_quantity']))) {
			$form_error['product_quantity'] = $this->language->get('error_count_number');
		}

		if (!isset($offer_info['gift_quantity']) || !preg_match('/^[1-9]{1}[\d]*$/', trim($offer_info['gift_quantity']))) {
			$form_error['gift_quantity'] = $this->language->get('error_count_number');
		}

		if ($this->request->post['special_offer_seo_url']) {
			$this->load->model('design/seo_url');

			foreach ($offer_info['special_offer_seo_url'] as $store_id => $language) {
				foreach ($language as $language_id => $keyword) {
					if (!empty($keyword)) {
						if (count(array_keys($language, $keyword)) > 1) {
							$form_error['keyword_'.$store_id.'_'.$language_id] = $this->language->get('error_unique');
						}

						$seo_urls = $this->model_design_seo_url->getSeoUrlsByKeyword($keyword);

						foreach ($seo_urls as $seo_url) {
							if (($seo_url['store_id'] == $store_id) && (!$offer_id) || (($seo_url['query'] != 'special_offer_id=' . $offer_id))) {
								$form_error['keyword_'.$store_id.'_'.$language_id] = $this->language->get('error_keyword');
							}
						}
					}
				}
			}
		}

		if ($form_error && !isset($form_error['warning'])) {
			$form_error['warning'] = $this->language->get('error_warning');
		}

        return ($form_error ? $form_error : false);
    }

    protected function validateDelete() {
        if (!$this->user->hasPermission('modify', 'extension/module/special_offer')) {
            $this->error['warning'] = $this->language->get('error_permission');
        }

        return !$this->error;
    }

    public function autocomplete() {
        $json = array();

        if (isset($this->request->get['filter_name'])) {
            $this->load->model('extension/module/special_offer');

            $filter_data = array(
                'filter_name' => $this->request->get['filter_name'],
                'start'       => 0,
                'limit'       => 5
            );

            $results = $this->model_extension_module_special_offer->getspecial_offers($filter_data);

            foreach ($results as $result) {
                $json[] = array(
                    'special_offer_id' => $result['special_offer_id'],
                    'name'            => strip_tags(html_entity_decode($result['name'], ENT_QUOTES, 'UTF-8'))
                );
            }
        }

        $sort_order = array();

        foreach ($json as $key => $value) {
            $sort_order[$key] = $value['name'];
        }

        array_multisort($sort_order, SORT_ASC, $json);

        $this->response->addHeader('Content-Type: application/json');
        $this->response->setOutput(json_encode($json));
    }

	public function autocompleteProduct() {
		$json = array();

		$category_id = $this->request->post['category_id'];
		$manufacturer_id = $this->request->post['manufacturer_id'];
		$filter_name = $this->request->post['filter_name'];


		if($category_id or $manufacturer_id or $filter_name) {
			$this->load->model('catalog/product');
			$this->load->model('catalog/manufacturer');
			$this->load->model('catalog/category');
			$this->load->model('extension/module/special_offer');

			if (isset($this->request->get['limit'])) {
				$limit = $this->request->get['limit'];
			} else {
				$limit = 5;
			}

			$filter_data = array(
				'filter_category_id' => $category_id,
				'filter_manufacturer_id' => $manufacturer_id,
				'filter_name' => $filter_name,
				'start'        => 0,
				'limit'        => $limit
			);

			$results = $this->model_extension_module_special_offer->getProductsByFilter($filter_data); // Get Products by Category & Manufacturer

			foreach ($results as $result) {


				$json[] = array(
					'product_id' => $result['product_id'],
					'name'       => strip_tags(html_entity_decode($result['name'], ENT_QUOTES, 'UTF-8')),
					'price'      => $result['price']
				);
			}
		}

		$this->response->addHeader('Content-Type: application/json');
		$this->response->setOutput(json_encode($json));
	}

	protected function getModuleData($key, $default = 0) {
		if (isset($this->request->post[$key])) {
			return $this->request->post[$key];
		} else if ($this->config->has('module_special_offer_' . $key)) {
			return $this->config->get('module_special_offer_' . $key);
		} else {
			return $default;
		}
	}

	public function install() {
		$this->db->query("CREATE TABLE IF NOT EXISTS `" . DB_PREFIX . "special_offer` (
		`special_offer_id` int(11) NOT NULL AUTO_INCREMENT,
		`offer_type` int(11) NOT NULL,
		`image` varchar(255) CHARACTER SET utf8 COLLATE utf8_bin,
		`image_label` varchar(255) CHARACTER SET utf8 COLLATE utf8_bin,
		`list_customer_group_id` varchar(255) CHARACTER SET utf8 COLLATE utf8_bin NOT NULL,
		`priority` int(5) NOT NULL DEFAULT 1,
		`date_start` date  NOT NULL DEFAULT '0000-00-00',
		`date_end` date  NOT NULL DEFAULT '0000-00-00',
		`gift_product_id` int(11) NOT NULL,
		`product_quantity` int(11) NOT NULL DEFAULT 1,
		`product_sum` decimal(15,2) NOT NULL DEFAULT 0,
		`gift_quantity` int(11) NOT NULL DEFAULT 1,
		`percent` decimal(10,2) NOT NULL DEFAULT 0,
		`timer_status` TINYINT(1) NOT NULL DEFAULT 1,
		`free_shipping` TINYINT(1) NOT NULL DEFAULT 0,
		`cycle_of_timer` int(11) NOT NULL,
		`offer_status` TINYINT(1) NOT NULL DEFAULT 1,
		PRIMARY KEY (`special_offer_id`)
		) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1");

		$this->db->query("CREATE TABLE IF NOT EXISTS `" . DB_PREFIX . "special_offer_description` (
		`special_offer_id` int(11) NOT NULL,
		`language_id` int(11) NOT NULL,
		`name` varchar(255) CHARACTER SET utf8 COLLATE utf8_bin NOT NULL,
		`description` text CHARACTER SET utf8 COLLATE utf8_bin NOT NULL,
		`meta_title` varchar(255) CHARACTER SET utf8 COLLATE utf8_bin NOT NULL,
		`meta_h1` varchar(255) CHARACTER SET utf8 COLLATE utf8_bin NOT NULL,
		`meta_description` varchar(255) CHARACTER SET utf8 COLLATE utf8_bin NOT NULL,
		`meta_keyword` varchar(255) CHARACTER SET utf8 COLLATE utf8_bin NOT NULL,
		PRIMARY KEY (`special_offer_id`, `language_id`)
		) ENGINE=InnoDB DEFAULT CHARSET=utf8");

 		$this->db->query("CREATE TABLE IF NOT EXISTS `" . DB_PREFIX . "special_offer_products` (
		`special_offer_id` int(11) NOT NULL,
		`product_id` int(11) NOT NULL,
		`price` decimal(15,4) NOT NULL DEFAULT 0,
		`status` TINYINT(1) NOT NULL DEFAULT 1,
		`price_status` TINYINT(1) NOT NULL DEFAULT 1,
		PRIMARY KEY (`special_offer_id`,`product_id`)
		) ENGINE=InnoDB DEFAULT CHARSET=utf8");
		
		$this->load->model('user/user_group');
		$this->model_user_user_group->addPermission($this->user->getGroupId(), 'access', 'extension/module/special_offer');
		$this->model_user_user_group->addPermission($this->user->getGroupId(), 'modify', 'extension/module/special_offer');

		$chk = $this->db->query("SHOW COLUMNS FROM `" . DB_PREFIX . "product_special` WHERE `field` = 'special_offer_id'");
		if (!$chk->num_rows) {
			$this->db->query("ALTER TABLE `" . DB_PREFIX . "product_special` ADD COLUMN  special_offer_id INT(11) NOT NULL");
			$this->db->query("ALTER TABLE `" . DB_PREFIX . "product_special` ADD INDEX  special_offer_id (special_offer_id)");
		}

		// === DB tables upgrade ====
		$chk = $this->db->query("SHOW COLUMNS FROM `" . DB_PREFIX . "special_offer` WHERE `field` = 'list_customer_group_id'");
		if (!$chk->num_rows) {
			$this->db->query("ALTER TABLE `" . DB_PREFIX . "special_offer` ADD COLUMN  list_customer_group_id varchar(255) CHARACTER SET utf8 COLLATE utf8_bin NOT NULL");

			$query = $this->db->query("SELECT * FROM " . DB_PREFIX . "special_offer");
			foreach ($query->rows as $result) {
				$list_customer_group_id = "," . $result['customer_group_id'] . ",";
				$this->db->query("UPDATE " . DB_PREFIX . "special_offer SET list_customer_group_id = '" . $list_customer_group_id . "' WHERE special_offer_id = '" . (int)$result['special_offer_id'] . "'");
			}
			$this->db->query("ALTER TABLE `" . DB_PREFIX . "special_offer` DROP `customer_group_id`");
		}

		// добавляем колонку цикл таймера
		$chk = $this->db->query("SHOW COLUMNS FROM `" . DB_PREFIX . "special_offer` WHERE `field` = 'cycle_of_timer'");
		if (!$chk->num_rows) {
			$this->db->query("ALTER TABLE `" . DB_PREFIX . "special_offer` ADD COLUMN  cycle_of_timer INT(11) NOT NULL");
		}

		// добавляем колонку количество товаров
		$chk = $this->db->query("SHOW COLUMNS FROM `" . DB_PREFIX . "special_offer` WHERE `field` = 'product_quantity'");
		if (!$chk->num_rows) {
			$this->db->query("ALTER TABLE `" . DB_PREFIX . "special_offer` ADD COLUMN  product_quantity INT(11) NOT NULL DEFAULT 1");
		}

		// добавляем колонку количество подарков
		$chk = $this->db->query("SHOW COLUMNS FROM `" . DB_PREFIX . "special_offer` WHERE `field` = 'gift_quantity'");
		if (!$chk->num_rows) {
			$this->db->query("ALTER TABLE `" . DB_PREFIX . "special_offer` ADD COLUMN  gift_quantity INT(11) NOT NULL DEFAULT 1");
		}

		// добавляем колонку цена продажи
		$chk = $this->db->query("SHOW COLUMNS FROM `" . DB_PREFIX . "special_offer` WHERE `field` = 'selling_price'");
		if (!$chk->num_rows) {
			$this->db->query("ALTER TABLE `" . DB_PREFIX . "special_offer` ADD COLUMN  selling_price INT(11) NOT NULL");
		}

		// добавляем колонку процент и статус таймера
		$chk = $this->db->query("SHOW COLUMNS FROM `" . DB_PREFIX . "special_offer` WHERE `field` = 'percent'");
		if (!$chk->num_rows) {
			$this->db->query("ALTER TABLE `" . DB_PREFIX . "special_offer` ADD COLUMN  percent decimal(10,2) NOT NULL DEFAULT 0");
			$this->db->query("ALTER TABLE `" . DB_PREFIX . "special_offer` ADD COLUMN  timer_status TINYINT(1) NOT NULL DEFAULT 1");
		}

		// добавляем колонку стоимости товаров
		$chk = $this->db->query("SHOW COLUMNS FROM `" . DB_PREFIX . "special_offer` WHERE `field` = 'product_sum'");
		if (!$chk->num_rows) {
			$this->db->query("ALTER TABLE `" . DB_PREFIX . "special_offer` ADD COLUMN  product_sum  decimal(15,2) NOT NULL DEFAULT 0");
		}

		// добавляем колонку беспл доставки
		$chk = $this->db->query("SHOW COLUMNS FROM `" . DB_PREFIX . "special_offer` WHERE `field` = 'free_shipping'");
		if (!$chk->num_rows) {
			$this->db->query("ALTER TABLE `" . DB_PREFIX . "special_offer` ADD COLUMN  free_shipping TINYINT(1) NOT NULL DEFAULT 0");
		}

		// добавляем колонку статуса спецпредложения
		$chk = $this->db->query("SHOW COLUMNS FROM `" . DB_PREFIX . "special_offer` WHERE `field` = 'offer_status'");
		if (!$chk->num_rows) {
			$this->db->query("ALTER TABLE `" . DB_PREFIX . "special_offer` ADD COLUMN  offer_status TINYINT(1) NOT NULL DEFAULT 1");
		}
		
		$this->db->query("DELETE FROM `" . DB_PREFIX . "seo_url` WHERE query = 'extension/module/special_offer'");
	}

	public function uninstall(){
//		$this->db->query("DROP TABLE `" . DB_PREFIX . "special_offer`");
	}
}	