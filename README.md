# Library rest sample #

Тестовое задание, содержит примеры кода.

## Инструкция по установке

1. Скопировать файл .env.dist в .env изменив необходимые настройки, по-умолчанию сервер будет доступен по адресу
   127.0.0.1:8000
2. Развернуть docker-окружение командой
    ```bash
    docker-compose up -d
    ```
3. Установить composer-зависимости командой (при необходимости изменить library_rest_php см. п.1):
     ```bash
     docker exec -it library_rest_php composer install
     ```
4. Создать структуру БД командой (при необходимости изменить library_rest_php см. п.1):
     ```bash
     docker exec -it library_rest_php php bin/console d:m:m -q
     ```

## Тестовые данные

1. Загрузить тестовые данные (при необходимости изменить library_rest_php см. п.1 инструкции по установке):
     ```bash
     docker exec -it library_rest_php /bin/bash -c "for i in {1..50}; do php bin/console d:f:l -q --append; done"
     ```

## Тестирование

1. Скопировать файл phpunit.xml.dist в phpunit.xml изменив необходимые настройки
2. Запуск тестов (при необходимости изменить library_rest_php см. п.1 инструкции по установке):
     ```bash
     docker exec -it library_rest_php php bin/phpunit
     ```   
