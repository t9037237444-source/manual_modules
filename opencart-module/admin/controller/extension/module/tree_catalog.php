<?php
class ControllerExtensionModuleTreeCatalog extends Controller {
    private $error = array();

    public function index() {
        $this->load->language('extension/module/tree_catalog');

        $this->document->setTitle($this->language->get('heading_title'));

        $this->load->model('setting/module');
        $this->load->model('catalog/category');

        if (($this->request->server['REQUEST_METHOD'] == 'POST') && $this->validate()) {
            if (!isset($this->request->get['module_id'])) {
                $this->model_setting_module->addModule('tree_catalog', $this->request->post);
            } else {
                $this->model_setting_module->editModule($this->request->get['module_id'], $this->request->post);
            }

            $this->session->data['success'] = $this->language->get('text_success');

            $this->response->redirect($this->url->link('extension/extension', 'user_token=' . $this->session->data['user_token'] . '&type=module', true));
        }

        $data['heading_title'] = $this->language->get('heading_title');

        $data['text_edit'] = $this->language->get('text_edit');
        $data['text_enabled'] = $this->language->get('text_enabled');
        $data['text_disabled'] = $this->language->get('text_disabled');
        $data['text_expand_all'] = $this->language->get('text_expand_all');
        $data['text_collapse_all'] = $this->language->get('text_collapse_all');
        $data['text_root_only'] = $this->language->get('text_root_only');
        $data['text_select_all'] = $this->language->get('text_select_all');
        $data['text_unselect_all'] = $this->language->get('text_unselect_all');

        $data['entry_name'] = $this->language->get('entry_name');
        $data['entry_status'] = $this->language->get('entry_status');
        $data['entry_sort_order'] = $this->language->get('entry_sort_order');
        $data['entry_show_count'] = $this->language->get('entry_show_count');
        $data['entry_show_images'] = $this->language->get('entry_show_images');
        $data['entry_max_depth'] = $this->language->get('entry_max_depth');
        $data['entry_default_state'] = $this->language->get('entry_default_state');
        $data['entry_highlight_active'] = $this->language->get('entry_highlight_active');
        $data['entry_categories'] = $this->language->get('entry_categories');
        $data['entry_animation'] = $this->language->get('entry_animation');

        $data['button_save'] = $this->language->get('button_save');
        $data['button_cancel'] = $this->language->get('button_cancel');
        $data['button_category_add'] = $this->language->get('button_category_add');

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

        $data['breadcrumbs'] = array();

        $data['breadcrumbs'][] = array(
            'text' => $this->language->get('text_home'),
            'href' => $this->url->link('common/dashboard', 'user_token=' . $this->session->data['user_token'], true)
        );

        $data['breadcrumbs'][] = array(
            'text' => $this->language->get('text_extension'),
            'href' => $this->url->link('marketplace/extension', 'user_token=' . $this->session->data['user_token'] . '&type=module', true)
        );

        if (!isset($this->request->get['module_id'])) {
            $data['breadcrumbs'][] = array(
                'text' => $this->language->get('heading_title'),
                'href' => $this->url->link('extension/module/tree_catalog', 'user_token=' . $this->session->data['user_token'], true)
            );
        } else {
            $data['breadcrumbs'][] = array(
                'text' => $this->language->get('heading_title'),
                'href' => $this->url->link('extension/module/tree_catalog', 'user_token=' . $this->session->data['user_token'] . '&module_id=' . $this->request->get['module_id'], true)
            );
        }

        if (!isset($this->request->get['module_id'])) {
            $data['action'] = $this->url->link('extension/module/tree_catalog', 'user_token=' . $this->session->data['user_token'], true);
        } else {
            $data['action'] = $this->url->link('extension/module/tree_catalog', 'user_token=' . $this->session->data['user_token'] . '&module_id=' . $this->request->get['module_id'], true);
        }

        $data['cancel'] = $this->url->link('marketplace/extension', 'user_token=' . $this->session->data['user_token'] . '&type=module', true);

        if (isset($this->request->get['module_id']) && ($this->request->server['REQUEST_METHOD'] != 'POST')) {
            $module_info = $this->model_setting_module->getModule($this->request->get['module_id']);
        }

        if (isset($this->request->post['name'])) {
            $data['name'] = $this->request->post['name'];
        } elseif (!empty($module_info)) {
            $data['name'] = $module_info['name'];
        } else {
            $data['name'] = '';
        }

        if (isset($this->request->post['status'])) {
            $data['status'] = $this->request->post['status'];
        } elseif (!empty($module_info)) {
            $data['status'] = $module_info['status'];
        } else {
            $data['status'] = '';
        }

        if (isset($this->request->post['sort_order'])) {
            $data['sort_order'] = $this->request->post['sort_order'];
        } elseif (!empty($module_info)) {
            $data['sort_order'] = $module_info['sort_order'];
        } else {
            $data['sort_order'] = 0;
        }

        if (isset($this->request->post['show_count'])) {
            $data['show_count'] = $this->request->post['show_count'];
        } elseif (!empty($module_info)) {
            $data['show_count'] = $module_info['show_count'];
        } else {
            $data['show_count'] = 0;
        }

        if (isset($this->request->post['show_images'])) {
            $data['show_images'] = $this->request->post['show_images'];
        } elseif (!empty($module_info)) {
            $data['show_images'] = $module_info['show_images'];
        } else {
            $data['show_images'] = 0;
        }

        if (isset($this->request->post['max_depth'])) {
            $data['max_depth'] = $this->request->post['max_depth'];
        } elseif (!empty($module_info)) {
            $data['max_depth'] = $module_info['max_depth'];
        } else {
            $data['max_depth'] = 0;
        }

        if (isset($this->request->post['default_state'])) {
            $data['default_state'] = $this->request->post['default_state'];
        } elseif (!empty($module_info)) {
            $data['default_state'] = $module_info['default_state'];
        } else {
            $data['default_state'] = 'collapsed';
        }

        if (isset($this->request->post['highlight_active'])) {
            $data['highlight_active'] = $this->request->post['highlight_active'];
        } elseif (!empty($module_info)) {
            $data['highlight_active'] = $module_info['highlight_active'];
        } else {
            $data['highlight_active'] = 1;
        }

        if (isset($this->request->post['animation'])) {
            $data['animation'] = $this->request->post['animation'];
        } elseif (!empty($module_info)) {
            $data['animation'] = $module_info['animation'];
        } else {
            $data['animation'] = 1;
        }

        if (isset($this->request->post['excluded_categories'])) {
            $data['excluded_categories'] = $this->request->post['excluded_categories'];
        } elseif (!empty($module_info)) {
            $data['excluded_categories'] = $module_info['excluded_categories'];
        } else {
            $data['excluded_categories'] = array();
        }

        // Загружаем все категории
        $data['categories'] = $this->getAllCategories();

        $data['header'] = $this->load->controller('common/header');
        $data['column_left'] = $this->load->controller('common/column_left');
        $data['footer'] = $this->load->controller('common/footer');

        $this->response->setOutput($this->load->view('extension/module/tree_catalog', $data));
    }

    protected function getAllCategories() {
        $categories = array();
        
        // Получаем ВСЕ категории (включая отключённые) для отображения в настройках
        $sql = "SELECT c.category_id, c.parent_id, c.status, c.sort_order, c.image, 
                       cd.name 
                FROM " . DB_PREFIX . "category c 
                LEFT JOIN " . DB_PREFIX . "category_description cd 
                    ON (c.category_id = cd.category_id) 
                WHERE cd.language_id = '" . (int)$this->config->get('config_language_id') . "' 
                ORDER BY c.sort_order ASC, cd.name ASC";
        
        $query = $this->db->query($sql);
        $results = $query->rows;
        
        foreach ($results as $result) {
            $categories[] = array(
                'category_id' => $result['category_id'],
                'name'        => $this->getCategoryPath($result['category_id']),
                'status'      => $result['status'],
                'sort_order'  => $result['sort_order']
            );
        }
        
        return $categories;
    }

    protected function getCategoryPath($category_id) {
        $path = '';
        $parts = array();
        
        $current_id = $category_id;
        
        while ($current_id > 0) {
            $category_info = $this->model_catalog_category->getCategory($current_id);
            
            if ($category_info) {
                array_unshift($parts, $category_info['name']);
                $current_id = $category_info['parent_id'];
            } else {
                break;
            }
        }
        
        return implode(' &gt; ', $parts);
    }

    protected function validate() {
        if (!$this->user->hasPermission('modify', 'extension/module/tree_catalog')) {
            $this->error['warning'] = $this->language->get('error_permission');
        }

        if ((utf8_strlen($this->request->post['name']) < 3) || (utf8_strlen($this->request->post['name']) > 64)) {
            $this->error['name'] = $this->language->get('error_name');
        }

        return !$this->error;
    }

    public function install() {
        $this->load->model('extension/event');
        $this->model_extension_event->addEvent('tree_catalog', 'catalog/controller/common/header/after', 'extension/module/tree_catalog/onHeaderAfter');
    }

    public function uninstall() {
        $this->load->model('extension/event');
        $this->model_extension_event->deleteEventByCode('tree_catalog');
    }
}
