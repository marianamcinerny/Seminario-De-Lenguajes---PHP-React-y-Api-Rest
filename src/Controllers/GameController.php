<?php

namespace App\Controllers;

use App\Models\Game;
use App\Models\User;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;


class GameController {
    public function retrieve(Request $request, Response $response, $args) {
        $gameId = $args['id'];
        $gameModel = new Game();

        if(!$gameModel->gameExists($gameId)) {
            $response->getBody()->write(json_encode(['message' => 'El juego no existe']));
            return $response->withStatus(404);
        }

        $gameInfo = $gameModel->getGameInfo($gameId);
        if ($gameInfo['success']) {
            $response->getBody()->write(json_encode($gameInfo));
            return $response->withStatus(200);
        } else {
            $response->getBody()->write(json_encode(['success' => false, 'message' => 'Juego no encontrado']));
            return $response->withStatus(404);
        }
    }


    public function create(Request $request, Response $response): Response{
        $userModel = new User();
        $authHeader = $request->getHeader('Authorization');
        if (empty($authHeader)) {
            $response->getBody()->write(json_encode(['message' => 'Ausencia del token']));
            return $response->withStatus(401);
        }

        $token = str_replace('Bearer ', '', $authHeader[0]);
        $result = $userModel->checkLogin($token);
        if (empty($result)) {
            $response->getBody()->write(json_encode(['message' => 'Token caducado o incorrecro, se debe iniciar sesión']));
            return $response->withStatus(401);
        } else {
            $admin = $result['es_admin'];
        }

        if ($admin == 0) {
            $response->getBody()->write(json_encode(['message' => 'Se debe ser administrador']));
            return $response->withStatus(401);
        }

        $body = $request->getBody()->getContents();
        $data = json_decode($body, true);

        $platform = (array)($data['plataforma'] ?? []);
        $age = $data['clasificacion_edad'] ?? null;
        $name = $data['nombre'] ?? null;
        $description = $data['descripcion'] ?? null;
        $img = $data['imagen'] ?? null;

        if (empty($name) || empty($description) || empty($age) || empty($img) || empty($platform)) {
            $response->getBody()->write(json_encode(['message' => 'Se deben completar todos los campos']));
            return $response->withStatus(400);
        }

        $validPlatforms = ['PS', 'XBOX', 'PC' ,'Android', 'Otro'];
        foreach ($platform as $singlePlatform) {
            if (!in_array($singlePlatform, $validPlatforms)) {
                $response->getBody()->write(json_encode(['message' => 'La plataforma no es valida']));
                return $response->withStatus(400);
            }
        }

        if ($age == ' 18') {
            $age = '+18';
        } else if ($age == ' 13') {
            $age = '+13';
        }

        if ($age != 'ATP' && $age != '+13' && $age != '+18'){
            $response->getBody()->write(json_encode(['message' => 'Clasificacion de edad valida: ATP, +13, +18']));
            return $response->withStatus(400);
        }

        if ((strlen($name) > 45)) {
            $response->getBody()->write(json_encode(['message' => 'El nombre del juego no puede tener mas de 45 caracteres']));
            return $response->withStatus(400);
        }

        $gameModel = new Game();
        if ($gameModel->gameNameExists($name)) {
            $response->getBody()->write(json_encode(['message' => 'El juego ya existe']));
            return $response->withStatus(400);
        }

        $result = $gameModel->create($name, $description, $img, $age, $platform);
        if ($result['success']) {
            $response->getBody()->write(json_encode(['message' => $result['message']]));
            return $response->withStatus(200);
        } else {
            $response->getBody()->write(json_encode(['message' => $result['message']]));
            return $response->withStatus(400);
        }
    }


    public function update(Request $request, Response $response, $args): Response{
        $userModel = new User();
        $authHeader = $request->getHeader('Authorization');
        if (empty($authHeader)) {
            $response->getBody()->write('Ausencia del token');
            return $response->withStatus(401);
        }
        $token = str_replace('Bearer ', '', $authHeader[0]);

        $result = $userModel->checkLogin($token);
        if (empty($result)) {
            $response->getBody()->write('Token caducado o invalido, se debe iniciar sesion');
            return $response->withStatus(401);
        } else {
            $admin = $result['es_admin'];
        }

        if ($admin == 0) {
            $response->getBody()->write('Se debe ser administrador');
            return $response->withStatus(401);
        }

        $id = $args['id'];
        $gameModel = new Game();
        if (!$gameModel->gameExists($id)) {
            $response->getBody()->write('El juego que se quiere actualizar no existe');
            return $response->withStatus(404);
        }
        
        $data = $request->getParsedBody();

        $platform = (array)($data['plataforma'] ?? []);
        $age = $data['clasificacion_edad'] ?? null;
        $name = $data['nombre'] ?? null;
        $description = $data['descripcion'] ?? null;
        $img = $data['imagen'] ?? null;

        if (empty($name) || empty($description) || empty($age) || empty($img) || empty($platform)) {
            $response->getBody()->write('Se deben completar todos los campos');
            return $response->withStatus(400);
        }

        $validPlatforms = ['PS', 'XBOX','PC', 'Android', 'Otro'];
        foreach ($platform as $singlePlatform) {
            if (!in_array($singlePlatform, $validPlatforms)) {
                $response->getBody()->write("La plataforma $singlePlatform no es válida");
                return $response->withStatus(400);
            }
        }

        if ($age == ' 18') {
            $age = '+18';
        } else if ($age == ' 13') {
            $age = '+13';
        }

        if ($age != 'ATP' && $age != '+13' && $age != '+18'){
            $response->getBody()->write('Clasificacion de edad valida: ATP, +13, +18');
            return $response->withStatus(400);
        }

        if ((strlen($name) > 45)) {
            $response->getBody()->write('El nombre del juego no puede tener mas de 45 caracteres');
            return $response->withStatus(400);
        }

        $currentName = $gameModel->gameExists($id);
        if ($currentName != $name && $gameModel->gameNameExists($name)) {
            $response->getBody()->write('El juego ya existe');
            return $response->withStatus(400);
        }

        $result = $gameModel->updateInfo($id, $name, $description, $img, $age, $platform);
        if ($result['success']) {
            $response->getBody()->write($result['message']);
            return $response->withStatus(200);
        } else {
            $response->getBody()->write($result['message']);
            return $response->withStatus(400);
        }
    }


    public function delete(Request $request, Response $response, $args): Response{
        $userModel = new User();
        $authHeader = $request->getHeader('Authorization');
        if (empty($authHeader)) {
            $response->getBody()->write('Ausencia del token');
            return $response->withStatus(401);
        }
        $token = str_replace('Bearer ', '', $authHeader[0]);

        $result = $userModel->checkLogin($token);
        if (empty($result)) {
            $response->getBody()->write('Se debe iniciar sesion');
            return $response->withStatus(401);
        } else {
            $admin = $result['es_admin'];
        }

        if ($admin == 0) {
            $response->getBody()->write('Se debe ser administrador');
            return $response->withStatus(401);
        }

        $id = $args['id'];
        $gameModel = new Game();
        if (!$gameModel->gameExists($id)) {
            $response->getBody()->write('El juego que se quiere eliminar no existe');
            return $response->withStatus(404);
        }

        $result = $gameModel->ratings($id);
        if ($result) {
            $response->getBody()->write('Para ser eliminado, el juego no debe tener calificaciones');
            return $response->withStatus(409);
        }

        $result = $gameModel->delete($id);
        if ($result['success']) {
            $response->getBody()->write($result['message']);
            return $response->withStatus(200);
        } else {
            $response->getBody()->write($result['message']);
            return $response->withStatus(409);
        }
    }


    public function list (Request $request, Response $response): Response {
        $data = $request->getQueryParams();

        $page = (int)($data['pagina'] ?? null);
        $age = $data['clasificacion'] ?? null;
        $text = $data['texto'] ?? null;
        $platform = ((array)($data['plataforma'] ?? []));


        // $noFilters = !$age && !$text && empty($platform);

        $validPlatforms = ['PS', 'XBOX', 'PC' ,'Android', 'Otro'];
        foreach ($platform as $singlePlatform) {
            if (!in_array($singlePlatform, $validPlatforms)) {
                $response->getBody()->write(json_encode('La plataforma no es valida'));
                return $response->withStatus(400);
            }
        }

        if ($age == ' 18') {
            $age = '+18';
        } else if ($age == ' 13') {
            $age = '+13';
        }

        if ($age && $age != 'ATP' && $age != '+13' && $age != '+18'){
            $response->getBody()->write(json_encode('Clasificacion de edad valida: ATP, +13, +18'));
            return $response->withStatus(400);
        }

        $gameModel = new Game();
        $result = $gameModel->getList($page,$age,$text,$platform);

        if ($result['success']) {
            $response->getBody()->write(json_encode([
                'success' => true,
                'games' => $result['games'],
                'totalPages' => $result['totalPages']
            ]));
            return $response->withStatus(200);
        } else {
            $response->getBody()->write(json_encode([
                'success' => false,
                'message' => $result['message']
            ]));
            return $response->withStatus(404);
        }
    }
}