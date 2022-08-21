<?php

require __DIR__ . "/vendor/autoload.php";
session_start();
ob_start();
$route = new Route\Route\Route();

$route->get('/', "\\src\\controllers\\Web@home");
$route->post('/', "\\src\\controllers\\Web@home");
$route->get('/login', "\\src\\controllers\\Web@signIn");
$route->post('/login', "\\src\\controllers\\Web@signIn");
$route->get('/sair', "\\src\\controllers\\Web@logout");

// Rotas protegidas por autenticação
$route->get('/home', function () {});
$route->get('/artigos', "\\src\\controllers\\App@articles");
$route->post('/artigos', "\\src\\controllers\\App@articlesPost");
$route->get('/article', "src\\controllers\\App@home");
$route->get('/posts', "src\\controllers\\App@userPosts");
$route->post('/comentario-atigos', "src\controllers\\App@articleComment");
$route->get('/teste', "src\controllers\\Test@test");

$route->run();

if ($error = $route->getError()) {
    $code = $error->getHttpErrorCode();
    $message = $error->getHttpErrorMessage();
    echo "Error {$code}, {$message}!";
}
ob_end_flush();
