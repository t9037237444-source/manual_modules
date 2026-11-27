# Модуль «Дерево категорий» для OpenCart 3

## Описание

Модуль выводит категории товаров в виде интерактивного дерева с возможностью:
- ✅ Свёртывания и развёртывания веток
- ✅ Включения/отключения отдельных категорий через админку
- ✅ Сохранения состояния (localStorage) — при перезагрузке страницы дерево остаётся в том же состоянии
- ✅ Подсветки активной категории
- ✅ Автоматического раскрытия пути к текущей категории
- ✅ Показа количества товаров в категории
- ✅ Показа изображений категорий
- ✅ Ограничения глубины вложенности
- ✅ Анимации свёртывания/развёртывания
- ✅ Кнопок «Развернуть всё» / «Свернуть всё»
- ✅ Адаптивности (мобильные устройства)
- ✅ Поддержки тёмной темы

---

## Структура файлов

```
opencart-module/
├── install.xml                                              # OCMod-установщик
├── admin/
│   ├── controller/extension/module/tree_catalog.php         # Контроллер админки
│   ├── language/ru-ru/extension/module/tree_catalog.php     # Языковой файл админки
│   └── view/template/extension/module/tree_catalog.twig     # Шаблон настроек
├── catalog/
│   ├── controller/extension/module/tree_catalog.php         # Контроллер фронтенда
│   ├── language/ru-ru/extension/module/tree_catalog.php     # Языковой файл фронтенда
│   └── view/
│       ├── theme/default/template/extension/module/
│       │   ├── tree_catalog.twig                            # Основной шаблон
│       │   └── tree_catalog_item.twig                       # Шаблон элемента (рекурсивный)
│       └── javascript/tree_catalog/
│           ├── tree_catalog.js                              # JavaScript
│           └── tree_catalog.css                             # Стили
```

---

## Установка

### Способ 1: Ручная установка (рекомендуется)

1. **Скопируйте файлы** на сервер, сохраняя структуру папок:

   ```
   # Из папки opencart-module/ скопируйте в корень сайта:
   
   admin/controller/extension/module/tree_catalog.php       → admin/controller/extension/module/
   admin/language/ru-ru/extension/module/tree_catalog.php   → admin/language/ru-ru/extension/module/
   admin/view/template/extension/module/tree_catalog.twig   → admin/view/template/extension/module/
   
   catalog/controller/extension/module/tree_catalog.php     → catalog/controller/extension/module/
   catalog/language/ru-ru/extension/module/tree_catalog.php → catalog/language/ru-ru/extension/module/
   catalog/view/theme/default/template/extension/module/    → catalog/view/theme/<ВАША_ТЕМА>/template/extension/module/
   catalog/view/javascript/tree_catalog/                    → catalog/view/javascript/tree_catalog/
   ```

   > ⚠️ Если вы используете кастомную тему (не `default`), скопируйте шаблоны twig в папку вашей темы.

2. **Установите OCMod** (опционально):
   - Зайдите в админку → **Модификаторы** → **Extensions** → **Installer**
   - Загрузите файл `install.xml` (упакуйте его в .ocmod.zip перед загрузкой)

3. **Обновите кэш модификаторов**:
   - Админка → **Модификаторы** → кнопка **Обновить** (синяя)

4. **Установите модуль**:
   - Админка → **Модули** → найдите **«Дерево категорий»** → нажмите **Install** (зелёный плюс)

5. **Настройте модуль**:
   - Нажмите **Edit** (синий карандаш)
   - Задайте название, состояние по умолчанию, глубину и т.д.
   - На вкладке **«Категории»** снимите галочки с ненужных категорий
   - Сохраните

6. **Добавьте модуль на макет**:
   - Админка → **Дизайн** → **Макеты** (Layouts)
   - Выберите макет «Category» или «Home»
   - Добавьте модуль «Дерево категорий» в нужную позицию (например, «Column Left»)
   - Сохраните

### Способ 2: Установка через OCMod

1. Упакуйте `install.xml` в ZIP-архив с именем `tree_catalog.ocmod.zip`
2. Админка → **Extensions** → **Installer** → загрузите архив
3. Админка → **Extensions** → **Modifications** → кнопка **Refresh**
4. Далее — шаги 4–6 из Способа 1

---

## Настройки модуля

| Параметр | Описание |
|----------|----------|
| **Название модуля** | Заголовок блока на сайте |
| **Максимальная глубина** | 0 = все уровни, 1–5 = ограничить вложенность |
| **Состояние по умолчанию** | Свёрнуто или развёрнуто при первом посещении |
| **Показывать кол-во товаров** | Число товаров в скобках рядом с названием |
| **Показывать изображения** | Миниатюры категорий (30×30px) |
| **Подсвечивать активную категорию** | Выделение текущей категории цветом |
| **Анимация** | Плавное свёртывание/развёртывание |
| **Порядок сортировки** | Приоритет при выводе нескольких модулей |
| **Статус** | Включён / Выключен |

### Вкладка «Категории»

- Отображается полный список всех категорий с путём (например: `Кабели > Витая пара`)
- Галочками можно включить/отключить отображение отдельных категорий
- Кнопки **«Выбрать все»** / **«Снять все»** для массового управления
- Отключённые в админке OpenCart категории помечены меткой «Отключена»

---

## Как это работает

1. **На сервере** модуль строит дерево категорий с учётом:
   - Исключённых категорий
   - Максимальной глубины
   - Статуса категорий

2. **На клиенте** JavaScript обеспечивает:
   - Свёртывание/развёртывание по клику на стрелку
   - Сохранение состояния в `localStorage` (запоминает какие ветки раскрыты)
   - Кнопки «Развернуть всё» / «Свернуть всё»
   - Автоматическое раскрытие пути к активной категории
   - Подсветку текущей категории

3. **При переходе на страницу категории** — дерево автоматически раскрывает путь к ней и подсвечивает активный элемент.

---

## Совместимость

- ✅ OpenCart 3.0.x
- ✅ PHP 7.x / 8.x
- ✅ jQuery (встроен в OpenCart)
- ✅ Font Awesome (встроен в OpenCart)
- ✅ Темы: default, journal, и другие стандартные

---

## Решение проблем

### Модуль не отображается
1. Проверьте, что модуль установлен и включён (Модули → Дерево категорий → Edit → Status: Enabled)
2. Проверьте, что модуль добавлен на нужный макет (Дизайн → Макеты)
3. Очистите кэш модификаторов (Модификаторы → Refresh)
4. Очистите кэш темы (если используется Journal или другая тема с кэшированием)

### Категории не сворачиваются
1. Проверьте, что jQuery подключён на странице
2. Проверьте консоль браузера (F12) на ошибки JavaScript
3. Убедитесь, что файлы `tree_catalog.js` и `tree_catalog.css` доступны по URL

### Не работает сохранение состояния
1. Проверьте, что `localStorage` доступен в браузере
2. В режиме инкогито сохранение работает, но данные удаляются при закрытии

---

## Файлы для копирования (краткий список)

```
# Админка:
admin/controller/extension/module/tree_catalog.php
admin/language/ru-ru/extension/module/tree_catalog.php
admin/view/template/extension/module/tree_catalog.twig

# Каталог:
catalog/controller/extension/module/tree_catalog.php
catalog/language/ru-ru/extension/module/tree_catalog.php
catalog/view/theme/default/template/extension/module/tree_catalog.twig
catalog/view/theme/default/template/extension/module/tree_catalog_item.twig
catalog/view/javascript/tree_catalog/tree_catalog.js
catalog/view/javascript/tree_catalog/tree_catalog.css
```

---

## Быстрая установка через SSH

Если у вас есть доступ по SSH к серверу, выполните из корня сайта:

```bash
# Перейдите в корень сайта
cd /path/to/your/opencart

# Скопируйте файлы (замените /path/to/opencart-module на реальный путь)
MODULE_DIR="/path/to/opencart-module"

# Админка
cp "$MODULE_DIR/admin/controller/extension/module/tree_catalog.php" admin/controller/extension/module/
cp "$MODULE_DIR/admin/language/ru-ru/extension/module/tree_catalog.php" admin/language/ru-ru/extension/module/
cp "$MODULE_DIR/admin/view/template/extension/module/tree_catalog.twig" admin/view/template/extension/module/

# Каталог (контроллер и языки)
cp "$MODULE_DIR/catalog/controller/extension/module/tree_catalog.php" catalog/controller/extension/module/
cp "$MODULE_DIR/catalog/language/ru-ru/extension/module/tree_catalog.php" catalog/language/ru-ru/extension/module/

# Шаблоны (для темы default — замените на вашу тему если нужно)
THEME="default"  # или ваша тема, например "journal3"
mkdir -p "catalog/view/theme/$THEME/template/extension/module"
cp "$MODULE_DIR/catalog/view/theme/default/template/extension/module/"*.twig "catalog/view/theme/$THEME/template/extension/module/"

# JavaScript и CSS
mkdir -p catalog/view/javascript/tree_catalog
cp "$MODULE_DIR/catalog/view/javascript/tree_catalog/"* catalog/view/javascript/tree_catalog/

# Установите правильные права
find admin/catalog -name "tree_catalog*" -exec chmod 644 {} \;
find catalog/view/javascript/tree_catalog -type f -exec chmod 644 {} \;

echo "Файлы скопированы! Теперь зайдите в админку и установите модуль."
```

---

## Проверка после установки

1. Убедитесь, что в настройках OpenCart включён подсчёт товаров:
   - **Система** → **Настройки** → **Ваш магазин** → **Вкладка «Сервер»** → **«Показывать количество товаров в категориях»** = **Да**

2. Очистите все кэши:
   - **Дизайн** → иконка шестерёнки → **Очистить кэш темы**
   - **Дизайн** → иконка шестерёнки → **Очистить кэш SASS**
   - **Модификаторы** → кнопка **Обновить**

---

## Версия

**1.0.0** — Первый релиз

## Changelog

### 1.0.0
- ✅ Дерево категорий с неограниченной вложенностью (до 5 уровней в шаблоне)
- ✅ Свёртывание/развёртывание веток
- ✅ Кнопки «Развернуть всё» / «Свернуть всё»
- ✅ Сохранение состояния в localStorage
- ✅ Подсветка активной категории
- ✅ Автоматическое раскрытие пути к текущей категории
- ✅ Подсчёт товаров в категориях
- ✅ Изображения категорий
- ✅ Ограничение глубины дерева
- ✅ Включение/отключение категорий через админку
- ✅ Анимация свёртывания/развёртывания
- ✅ Защита от бесконечной рекурсии
- ✅ Адаптивность для мобильных устройств
- ✅ Поддержка тёмной темы (CSS media query)

## Автор

Модуль разработан для сайта [isservice.pro](https://isservice.pro) — ИнжСпецСервис
