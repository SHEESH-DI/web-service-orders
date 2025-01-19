# web-service-orders

# API для управления заказами

Этот проект представляет собой RESTful API для управления заказами. Он позволяет создавать заказы, добавлять товары в существующие заказы и получать информацию о заказах.

## Запуск контейнеров

Для запуска проекта вам понадобятся Docker и Docker Compose. Убедитесь, что они установлены на вашем компьютере.

1. Склонируйте репозиторий:
2. Запустите контейнеры: docker-compose up --build -d
3. Необходимо перейти в СУБД и создать таблицу orders внутри БД order_db
create table orders
(
    id         varchar(255)                             not null primary key,
    items      json                                     not null,
    done       tinyint(1) default 0                     null,
    created_at datetime                                 not null,
    updated_at datetime   default '1900-01-01 00:00:00' null
);
Данные по подлюкчению к БД лежат в config.php

После запуска контейнеров и создания таблицы вы сможете получить доступ к API по адресу: http://localhost:8080.

## Доступные API запросы

### 1. Создание нового заказа

- **Метод:** POST
- **Путь:** /orders
- **Запрос:**
json
{
    "items": [1, 2, 3, 3]
}
**Ответ:**
json
{
    "order_id": "aac",
    "items": [1, 2, 3, 3],
    "done": false
}

---

### 2. Добавление товаров в заказ

- **Метод:** POST
- **Путь:** /orders/{order_id}/items
- **Запрос:**
json
[
4,
7,
8
]
### 3. Получение информации о заказе

- **Метод:** GET
- **Путь:** /orders/{order_id}
- **Ответ:**
json
{
    "order_id": "aac",
    "items": [1, 2, 3, 4],
    "done": false
}

### 4. Пометить заказ как выполненный

- **Метод:** POST
- **Путь:** /orders/{order_id}/done
- **Запрос:** Заголовок X-Auth-Key должен содержать ключ.
X-Auth-Key: qwerty123

### 5. Получение списка всех заказов

- **Метод:** GET
- **Путь:** /orders[?done=1|0]
- **Запрос:** Заголовок X-Auth-Key должен содержать ключ.
X-Auth-Key: qwerty123
- **Ответ:**
json
[
    {
        "order_id": "aac",
        "done": true
    },
    {
        "order_id": "ab",
        "done": false
    }
    ...
]
