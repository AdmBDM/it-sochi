# PDM-0001

# Physical Data Model

Версия: 1.0

Статус: Accepted

Дата: 2026-07-22

---

# Назначение

Настоящий документ определяет физическую структуру всех новых таблиц подсистемы Master Data.

Все миграции обязаны полностью соответствовать настоящему документу.

Любое изменение структуры допускается только после изменения настоящего документа.

---

# Общие правила

## Идентификаторы

Во всех таблицах используется

```
id          primaryKey()
```

Использование bigint запрещено.

---

## Иерархия

Во всех древовидных сущностях используется

```
parent_id   integer NULL
```

Если запись является корнем дерева

```
parent_id = NULL
```

---

## Программный идентификатор

```
code varchar(64) NOT NULL UNIQUE
```

Используется исключительно программной логикой.

Не отображается пользователю.

Не изменяется после создания записи.

---

## Отображаемое имя

```
name varchar(255) NOT NULL
```

Используется исключительно пользовательским интерфейсом.

Использование поля `name` в программной логике запрещено.

---

## Сортировка

```
sort_order integer NOT NULL DEFAULT 0
```

---

## Активность

```
is_active boolean NOT NULL DEFAULT TRUE
```

---

## Временные поля

```
created_at timestamp NOT NULL DEFAULT now()

updated_at timestamp NOT NULL DEFAULT now()
```

Поле `updated_at` изменяется исключительно триггером

```
update_updated_at_column()
```

---

## Стандарт внешних ключей

Во всех новых таблицах используется единый стандарт:

```
ON DELETE RESTRICT

ON UPDATE CASCADE
```

Использование иных вариантов допускается только при наличии отдельного архитектурного обоснования и должно фиксироваться в ADR.

Стандарт Yii Migration:

```php
$this->addForeignKey(
    'fk_child_parent',
    '{{%child}}',
    'parent_id',
    '{{%parent}}',
    'id',
    'RESTRICT',
    'CASCADE'
);
```

---

## Стандарт именования объектов БД

### Таблицы

Используются существительные во множественном числе.

Примеры

```
classifiers

vendors

catalog_items

space_objects

organization_objects
```

---

### Первичные ключи

Не именуются явно.

Используется стандартное имя PostgreSQL по-умолчанию

Пример

```
vendors_pkey
```

---

### Внешние ключи

```
fk_<table>_<column>
```

Примеры

```
fk_catalog_items_vendor

fk_space_objects_parent
```

---

### Уникальные ограничения

Также не именуются вручную.

Используется стандарт PostgreSQL:


```
<table>_<column>_key
```

---

### Индексы

Обычные

```
ix_<table>_<column>
```

---

### Триггеры

```
trg_<table>_updated_at
```

---

## Общий порядок полей

Во всех новых таблицах используется единый порядок.

```
id

parent_id

внешние ключи

code

name

description

служебные поля

sort_order

is_active

created_at

updated_at
```

---

# Таблицы

---

# classifiers

Назначение

Единый лес классификаторов.

## Поля

```
id

parent_id

code

name

description

sort_order

is_active

created_at

updated_at
```

## Индексы

```
UNIQUE(code)

INDEX(parent_id)

INDEX(name)

INDEX(sort_order)

INDEX(is_active)
```

---

# vendors

Назначение

Производители оборудования.

## Поля

```
id

code

name

website

description

sort_order

is_active

created_at

updated_at
```

## Индексы

```
UNIQUE(code)

UNIQUE(name)

INDEX(sort_order)

INDEX(is_active)
```

---

# catalog_items

Назначение

Единый каталог оборудования.

## Поля

```
id

parent_id

vendor_id

code

name

description

sort_order

is_active

created_at

updated_at
```

## Внешние ключи

```
parent_id

vendor_id
```

## Индексы

```
UNIQUE(code)

INDEX(parent_id)

INDEX(vendor_id)

INDEX(name)

INDEX(sort_order)

INDEX(is_active)
```

---

# space_objects

Назначение

Единая пространственная модель.

## Поля

```
id

parent_id

classifier_id

code

name

description

sort_order

is_active

created_at

updated_at
```

## Внешние ключи

```
parent_id

classifier_id
```

## Индексы

```
UNIQUE(code)

INDEX(parent_id)

INDEX(classifier_id)

INDEX(name)

INDEX(sort_order)

INDEX(is_active)
```

---

# organization_objects

Назначение

Единая организационная модель.

## Поля

```
id

parent_id

classifier_id

code

name

description

sort_order

is_active

created_at

updated_at
```

## Внешние ключи

```
parent_id

classifier_id
```

## Индексы

```
UNIQUE(code)

INDEX(parent_id)

INDEX(classifier_id)

INDEX(name)

INDEX(sort_order)

INDEX(is_active)
```

---

# Триггеры

После создания каждой новой таблицы обязательно создаётся триггер

```
BEFORE UPDATE

EXECUTE FUNCTION update_updated_at_column()
```

При удалении таблицы триггер удаляется в `safeDown()` до удаления таблицы.

---

# Правила разработки

Запрещается

- использовать `name` для поиска и программной идентификации;
- использовать `bigint` без отдельного ADR;
- изменять физическую структуру таблиц без изменения настоящего документа;
- нарушать единый порядок расположения полей.

Обязательно

- использовать `code` как программный идентификатор;
- использовать `parent_id` для всех иерархических сущностей;
- использовать единый триггер `update_updated_at_column()`;
- использовать единый стандарт внешних ключей;
- соблюдать единый стиль именования объектов БД;
- соблюдать единый стиль оформления миграций.

---

# Статус

Настоящий документ является официальной физической моделью подсистемы Master Data.

Все последующие миграции создаются исключительно на основании настоящего документа.