<?php
declare(strict_types=1);

spl_autoload_register(fn (string $c) => is_file($f = __DIR__ . "/$c.php") ? require $f : null);
Env::load(__DIR__ . '/../.env');

session_set_cookie_params(['httponly' => true, 'samesite' => 'Lax']);
session_start();

set_exception_handler(function (Throwable $e) {
    error_log($e->getMessage());
    Api::json(500, ['erreur' => 'Erreur serveur']);
});

(new Api())->handle($_SERVER['REQUEST_METHOD'], parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH));
