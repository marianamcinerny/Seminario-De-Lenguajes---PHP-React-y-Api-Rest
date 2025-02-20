<?php

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\Factory\AppFactory;

require __DIR__ . '/vendor/autoload.php';

$app = AppFactory::create();
$app->addRoutingMiddleware();
$app->addErrorMiddleware(true, true, true);
$app->add( function ($request, $handler) {
    $response = $handler->handle($request);

    return $response
        ->withHeader('Access-Control-Allow-Origin', '*')
        ->withHeader('Access-Control-Allow-Headers', 'X-Requested-With, Content-Type, Accept, Origin, Authorization')
        ->withHeader('Access-Control-Allow-Methods', 'OPTIONS, GET, POST, PUT, PATCH, DELETE')
        ->withHeader('Content-Type', 'application/json')
    ;
});

//LOGIN
//verificar credenciales y retornar token
$app->post('/login','App\Controllers\LoginController:login');


//agrega un nuevo usuario con su nombre de usuario y clave
$app->post('/register','App\Controllers\LoginController:create');



//USUARIOS
//crea un nuevo usuario
$app->post('/usuario','App\Controllers\UserController:create');


//editar un usuario existente
$app->put('/usuario/{id}','App\Controllers\UserController:update');


//eliminar un usuario
$app->delete('/usuario/{id}','App\Controllers\UserController:delete');


//obtener informacion de un usuario especifico
$app->get('/usuario/{id}','App\Controllers\UserController:retrieve');



//JUEGOS
//listar los juegos de la pagina segun los parametros de busqueda incluyendo la puntuacion promedio del juego
$app->get('/juegos','App\Controllers\GameController:list');


//obtener informacion de un juego especifico y su listado de calificaciones
$app->get('/juegos/{id}','App\Controllers\GameController:retrieve');


//da de alta un juego nuevo, solo lo hace un usuario logueado y que sea administrador
$app->post('/juego','App\Controllers\GameController:create');


//actualiza los datos de un juego existente, solo lo puede hacer un usuario logueado y que sea administrador
$app->put('/juego/{id}','App\Controllers\GameController:update');


//borra el juego si no tiene calificaciones, solo lo hace un usuario logueado y que sea administrador
$app->delete('/juego/{id}','App\Controllers\GameController:delete');



//CALIFICACIONES
//crea una nueva calificacion, solo lo hace un usuario logueado
$app->post('/calificacion','App\Controllers\RateController:create');


//edita una calificacion, solo un usuario logueado
$app->put('/calificacion/{id}','App\Controllers\RateController:update');


//elimina una calificacion, solo un usuario logueado
$app->delete('/calificacion/{id}','App\Controllers\RateController:delete');


$app->run();