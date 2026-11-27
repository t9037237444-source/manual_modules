/**
 * Tree Catalog Module — JavaScript
 * Модуль дерева категорий для OpenCart 3
 * 
 * Функционал:
 * - Свёртывание/развёртывание веток
 * - Кнопки "Развернуть всё" / "Свернуть всё"
 * - Сохранение состояния в localStorage
 * - Анимация открытия/закрытия
 * - Подсветка активной категории
 */

var TreeCatalog = (function() {
    'use strict';

    var STORAGE_KEY = 'tree_catalog_state';
    var animationDuration = 250;

    /**
     * Инициализация модуля
     */
    function init(selector) {
        var $module = $(selector);
        if (!$module.length) return;

        var settings = {
            defaultState: $module.data('default-state') || 'collapsed',
            animation: $module.data('animation') !== undefined ? parseInt($module.data('animation')) : 1,
            highlightActive: $module.data('highlight-active') !== undefined ? parseInt($module.data('highlight-active')) : 1
        };

        if (settings.animation) {
            animationDuration = 250;
        } else {
            animationDuration = 0;
        }

        // Восстановить сохранённое состояние
        restoreState($module, settings);

        // Обработка кликов по toggle-кнопкам
        $module.on('click', '.tree-toggle:not(.tree-toggle-leaf)', function(e) {
            e.preventDefault();
            e.stopPropagation();
            toggleNode($(this), settings);
        });

        // Кнопка "Развернуть всё"
        $module.on('click', '.btn-expand-all', function(e) {
            e.preventDefault();
            expandAll($module, settings);
        });

        // Кнопка "Свернуть всё"
        $module.on('click', '.btn-collapse-all', function(e) {
            e.preventDefault();
            collapseAll($module, settings);
        });

        // Подсветка активной категории
        if (settings.highlightActive) {
            highlightActiveCategory($module);
        }

        // Сохранять состояние при изменениях
        $module.on('tree_state_changed', function() {
            saveState($module);
        });
    }

    /**
     * Переключение узла
     */
    function toggleNode($toggle, settings) {
        var $li = $toggle.closest('.tree-catalog-item');
        var $childList = $li.children('.tree-catalog-list');

        if (!$childList.length) return;

        var isExpanded = $toggle.hasClass('expanded');

        if (isExpanded) {
            collapseNode($toggle, $childList, settings);
        } else {
            expandNode($toggle, $childList, settings);
        }

        $li.trigger('tree_state_changed');
    }

    /**
     * Развернуть узел
     */
    function expandNode($toggle, $childList, settings) {
        $toggle.removeClass('collapsed').addClass('expanded');
        $childList.removeClass('tree-collapsed').addClass('tree-expanded');

        if (settings.animation) {
            $childList.stop(true, true).slideDown(animationDuration);
        } else {
            $childList.show();
        }
    }

    /**
     * Свернуть узел
     */
    function collapseNode($toggle, $childList, settings) {
        $toggle.removeClass('expanded').addClass('collapsed');
        $childList.removeClass('tree-expanded').addClass('tree-collapsed');

        if (settings.animation) {
            $childList.stop(true, true).slideUp(animationDuration);
        } else {
            $childList.hide();
        }
    }

    /**
     * Развернуть всё
     */
    function expandAll($module, settings) {
        $module.find('.tree-toggle:not(.tree-toggle-leaf)').each(function() {
            var $toggle = $(this);
            var $childList = $toggle.closest('.tree-catalog-item').children('.tree-catalog-list');
            if ($childList.length) {
                expandNode($toggle, $childList, settings);
            }
        });
        $module.trigger('tree_state_changed');
    }

    /**
     * Свернуть всё
     */
    function collapseAll($module, settings) {
        $module.find('.tree-toggle.expanded').each(function() {
            var $toggle = $(this);
            var $childList = $toggle.closest('.tree-catalog-item').children('.tree-catalog-list');
            if ($childList.length) {
                collapseNode($toggle, $childList, settings);
            }
        });
        $module.trigger('tree_state_changed');
    }

    /**
     * Сохранить состояние в localStorage
     */
    function saveState($module) {
        try {
            var state = {};
            var moduleId = $module.attr('id');

            $module.find('.tree-catalog-item.has-children').each(function() {
                var $li = $(this);
                var catId = $li.data('category-id');
                var $toggle = $li.children('.tree-catalog-node').find('.tree-toggle').first();
                state[catId] = $toggle.hasClass('expanded') ? 1 : 0;
            });

            localStorage.setItem(STORAGE_KEY + '_' + moduleId, JSON.stringify(state));
        } catch (e) {
            // localStorage недоступен — ничего не делаем
        }
    }

    /**
     * Восстановить состояние из localStorage
     */
    function restoreState($module, settings) {
        try {
            var moduleId = $module.attr('id');
            var savedState = localStorage.getItem(STORAGE_KEY + '_' + moduleId);

            if (savedState) {
                var state = JSON.parse(savedState);

                $module.find('.tree-catalog-item.has-children').each(function() {
                    var $li = $(this);
                    var catId = $li.data('category-id');
                    var $toggle = $li.children('.tree-catalog-node').find('.tree-toggle').first();
                    var $childList = $li.children('.tree-catalog-list');

                    if (state.hasOwnProperty(catId)) {
                        if (state[catId] === 1) {
                            $toggle.removeClass('collapsed').addClass('expanded');
                            $childList.removeClass('tree-collapsed').addClass('tree-expanded');
                            if (!settings.animation) {
                                $childList.show();
                            }
                        } else {
                            $toggle.removeClass('expanded').addClass('collapsed');
                            $childList.removeClass('tree-expanded').addClass('tree-collapsed');
                            if (!settings.animation) {
                                $childList.hide();
                            }
                        }
                    }
                });
            } else {
                // Если нет сохранённого состояния — применить состояние по умолчанию
                applyDefaultState($module, settings);
            }
        } catch (e) {
            applyDefaultState($module, settings);
        }
    }

    /**
     * Применить состояние по умолчанию
     */
    function applyDefaultState($module, settings) {
        if (settings.defaultState === 'expanded') {
            expandAll($module, settings);
        } else {
            // collapsed — всё уже свёрнуто по умолчанию в HTML
            // Но нужно скрыть свёрнутые ветки
            $module.find('.tree-collapsed').hide();
        }
    }

    /**
     * Подсветка активной категории и раскрытие пути к ней
     */
    function highlightActiveCategory($module) {
        var currentId = $module.data('current-category');
        if (!currentId) return;

        // Найти активный элемент и раскрыть всех его родителей
        var $activeItem = $module.find('.tree-catalog-item[data-category-id="' + currentId + '"]');
        if ($activeItem.length) {
            $activeItem.addClass('active');
            
            // Раскрыть всех родителей
            $activeItem.parents('.tree-catalog-item.has-children').each(function() {
                var $parentLi = $(this);
                var $toggle = $parentLi.children('.tree-catalog-node').find('.tree-toggle').first();
                var $childList = $parentLi.children('.tree-catalog-list');
                
                if (!$toggle.hasClass('expanded')) {
                    $toggle.removeClass('collapsed').addClass('expanded');
                    $childList.removeClass('tree-collapsed').addClass('tree-expanded');
                    $childList.show();
                }
            });

            // Прокрутить к активному элементу если нужно
            var moduleOffset = $module.offset();
            var activeOffset = $activeItem.offset();
            if (activeOffset && moduleOffset) {
                var relativeTop = activeOffset.top - moduleOffset.top;
                var moduleHeight = $module.height();
                if (relativeTop > moduleHeight || relativeTop < 0) {
                    $module[0].scrollIntoView({ behavior: 'smooth', block: 'center' });
                }
            }
        }
    }

    // Публичный API
    return {
        init: init,
        expandAll: function(selector) {
            var $module = $(selector);
            expandAll($module, { animation: 1 });
        },
        collapseAll: function(selector) {
            var $module = $(selector);
            collapseAll($module, { animation: 1 });
        }
    };

})();
