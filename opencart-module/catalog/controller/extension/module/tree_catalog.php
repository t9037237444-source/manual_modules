<?php
class ControllerExtensionModuleTreeCatalog extends Controller {
    
    public function index($setting) {
        $this->load->language('extension/module/tree_catalog');
        $this->load->model('catalog/category');
        $this->load->model('catalog/product');
        $this->load->model('tool/image');

        $data['heading_title'] = isset($setting['name']) ? $setting['name'] : $this->language->get('heading_title');
        
        // Настройки
        $data['show_count'] = isset($setting['show_count']) ? (int)$setting['show_count'] : 0;
        $data['show_images'] = isset($setting['show_images']) ? (int)$setting['show_images'] : 0;
        $data['max_depth'] = isset($setting['max_depth']) ? (int)$setting['max_depth'] : 0;
        $data['default_state'] = isset($setting['default_state']) ? $setting['default_state'] : 'collapsed';
        $data['highlight_active'] = isset($setting['highlight_active']) ? (int)$setting['highlight_active'] : 1;
        $data['animation'] = isset($setting['animation']) ? (int)$setting['animation'] : 1;
        $data['excluded_categories'] = isset($setting['excluded_categories']) ? $setting['excluded_categories'] : array();
        $data['module_id'] = $setting['module_id'];

        // Текущая категория
        $data['current_category_id'] = 0;
        if (isset($this->request->get['path'])) {
            $parts = explode('_', $this->request->get['path']);
            $data['current_category_id'] = (int)array_pop($parts);
        }

        // Путь текущей категории (для автоматического раскрытия)
        $data['current_path'] = array();
        if (isset($this->request->get['path'])) {
            $data['current_path'] = explode('_', $this->request->get['path']);
        }

        // Текущий продукт — найти его категорию
        if (isset($this->request->get['product_id'])) {
            $categories = $this->model_catalog_product->getCategories($this->request->get['product_id']);
            if (!empty($categories)) {
                $data['current_category_id'] = $categories[0]['category_id'];
                // Построить путь
                $data['current_path'] = $this->getCategoryPathIds($data['current_category_id']);
            }
        }

        // Получить дерево категорий
        $data['categories'] = $this->buildTree(0, 0, $data['excluded_categories'], $data['max_depth']);

        // Подключаем CSS и JS
        $this->document->addStyle('catalog/view/javascript/tree_catalog/tree_catalog.css');
        $this->document->addScript('catalog/view/javascript/tree_catalog/tree_catalog.js');

        return $this->load->view('extension/module/tree_catalog', $data);
    }

    /**
     * Построить дерево категорий (рекурсивно)
     */
    protected function buildTree($parent_id = 0, $level = 0, $excluded = array(), $max_depth = 0) {
        $tree = array();

        // Проверка максимальной глубины
        if ($max_depth > 0 && $level >= $max_depth) {
            return $tree;
        }
        
        $filter_data = array(
            'filter_category_id' => $parent_id,
            'filter_status'      => 1,
            'sort'               => 'sort_order',
            'order'              => 'ASC'
        );

        $categories = $this->model_catalog_category->getCategories($filter_data);

        foreach ($categories as $category) {
            // Пропустить исключённые категории
            if (in_array($category['category_id'], $excluded)) {
                continue;
            }

            // Получить дочерние категории
            $children = $this->buildTree($category['category_id'], $level + 1, $excluded, $max_depth);
            
            // Подсчёт товаров
            $product_count = 0;
            if ($this->config->get('config_product_count')) {
                $filter_product = array(
                    'filter_category_id'  => $category['category_id'],
                    'filter_sub_category' => 1
                );
                $product_count = $this->model_catalog_product->getTotalProducts($filter_product);
            }

            // Изображение
            $image = '';
            if (!empty($category['image']) && file_exists(DIR_IMAGE . $category['image'])) {
                $image = $this->model_tool_image->resize($category['image'], 30, 30);
            }

            $tree[] = array(
                'category_id'   => $category['category_id'],
                'name'          => $category['name'],
                'href'          => $this->url->link('product/category', 'path=' . $this->getCategoryPath($category['category_id'])),
                'children'      => $children,
                'has_children'  => !empty($children),
                'product_count' => $product_count,
                'image'         => $image,
                'level'         => $level
            );
        }

        return $tree;
    }

    /**
     * Получить путь категории (для URL) — рекурсивно вверх
     */
    protected function getCategoryPath($category_id, $visited = array()) {
        // Защита от бесконечной рекурсии
        if (in_array($category_id, $visited)) {
            return (string)$category_id;
        }
        $visited[] = $category_id;
        
        $path = '';
        $category_info = $this->model_catalog_category->getCategory($category_id);
        
        if ($category_info) {
            $parent_id = $category_info['parent_id'];
            
            if ($parent_id > 0) {
                $path = $this->getCategoryPath($parent_id, $visited);
            }
            
            $path .= ($path ? '_' : '') . $category_id;
        }
        
        return $path;
    }

    /**
     * Получить массив ID пути категории (от корня к текущей)
     */
    protected function getCategoryPathIds($category_id, $visited = array()) {
        // Защита от бесконечной рекурсии
        if (in_array($category_id, $visited)) {
            return array($category_id);
        }
        $visited[] = $category_id;
        
        $path = array();
        $category_info = $this->model_catalog_category->getCategory($category_id);
        
        if ($category_info) {
            $parent_id = $category_info['parent_id'];
            
            if ($parent_id > 0) {
                $path = $this->getCategoryPathIds($parent_id, $visited);
            }
            
            $path[] = (int)$category_id;
        }
        
        return $path;
    }

    /**
     * Event handler — для будущих расширений
     */
    public function onHeaderAfter(&$route, &$data, &$output) {
        // Зарезервировано для будущих доработок
    }
}
