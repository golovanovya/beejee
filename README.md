# BeeJee test task
___
## Приложение-задачник.

Задачи состоят из:
- имени пользователя;
- е-mail;
- текста задачи;

Стартовая страница - список задач с возможностью сортировки по имени пользователя, email и статусу. Вывод задач нужно сделать страницами по 3 штуки (с пагинацией). Видеть список задач и создавать новые может любой посетитель без авторизации.

INSTALLATION
------------
### Manual install
Клонировать на локальный с помощью `git clone` или скачать архивом и разархивировать
Зайти в папку с проектом и выполнить команды
```
composer install
vendor/bin/doctrine orm:schema-tool:create
```

CONFIGURATION
-------------

### Configuration Environments

See comments in .env.example file

### Database

Edit the file `config/container.php` with real data
```
$container['dbParams'] = [
    'driver' => 'pdo_mysql',
    'url' => getenv('DB_URL'),
    'dsn' => getenv('DB_DSN'),
    'user' => getenv('DB_USER'),
    'password' => getenv('DB_PASS'),
];
```

* сконфигурировать сервер
* composer install
* vendor/bin/doctrine orm:schema-tool:create
* cp .env.example .env
* yarn
* gulp
