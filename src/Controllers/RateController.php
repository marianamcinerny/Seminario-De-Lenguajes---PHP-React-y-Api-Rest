<?php

namespace App\Controllers;

use App\Models\Rate;
use App\Models\User;
use App\Models\Game;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

class RateController {

    public function create(Request $request, Response $response): Response {
        $userModel = new User();
        $authHeader = $request->getHeader('Authorization');
        if (empty($authHeader)) {
            $response->getBody()->write(json_encode(['message' => 'Ausencia del token']));
            return $response->withStatus(401);
        }
        $token = str_replace('Bearer ', '', $authHeader[0]);

        $result = $userModel->checkLogin($token);
        if (empty($result)) {
            $response->getBody()->write(json_encode(['message' => 'Token caducado o incorrecto, se debe iniciar sesión']));
            return $response->withStatus(401);
        } else {
            $userId = $result['id'];
        }

        $body = $request->getBody()->getContents();
        $data = json_decode($body, true);
        $gameId = $data['juego_id'] ?? null;
        $stars = $data['estrellas'] ?? null;

        $gameModel = new Game();
        if (!$gameModel->gameExists($gameId)) {
            $response->getBody()->write(json_encode(['message' => 'El juego que se quiere calificar no existe']));
            return $response->withStatus(404);
        }

        if (!is_numeric($stars) || $stars < 1 || $stars > 5) {
            $response->getBody()->write(json_encode(['message' => 'La calificación debe ser un número entre 1 y 5']));
            return $response->withStatus(400);
        }

        $ratingModel = new Rate();
        if ($ratingModel->ratingExistsUser($gameId, $userId)) {
            $response->getBody()->write(json_encode(['message' => 'Ya se hizo una calificacion de este juego, puede modificarla o eliminarla']));
            return $response->withStatus(400);
        }

        $result = $ratingModel->create($stars, $userId, $gameId);
        if ($result['success']) {
            $response->getBody()->write(json_encode(['message' => $result['message']]));
            return $response->withStatus(200);
        } else {
            $response->getBody()->write(json_encode(['message' => $result['message']]));
            return $response->withStatus(400);
        }
    }


    public function update(Request $request, Response $response, $args): Response {
        $userModel = new User();
        $authHeader = $request->getHeader('Authorization');
        if (empty($authHeader)) {
            $response->getBody()->write(json_encode(['message' => 'Ausencia del token']));
            return $response->withStatus(401);
        }
        $token = str_replace('Bearer ', '', $authHeader[0]);

        $result = $userModel->checkLogin($token);
        if (empty($result)) {
            $response->getBody()->write(json_encode(['message' => 'Token caducado o incorrecto, se debe iniciar sesión']));
            return $response->withStatus(401);
        } else {
            $userId = $result['id'];
        }

        $rateModel = new Rate();
        $gameId = $args['id'];

        $result = $rateModel->ratingExists($gameId, $userId);
        if ($result['success']) {
            $rateId = $result['rating_id'];
        } else {
            $response->getBody()->write(json_encode(['message' => 'No tiene una calificacion en este juego para modificar']));
            return $response->withStatus(404);
        }

        $body = $request->getBody()->getContents();
        $data = json_decode($body, true);

        $stars = $data['estrellas'] ?? null;
        if (!is_numeric($stars) || $stars < 1 || $stars > 5) {
            $response->getBody()->write(json_encode(['message' => 'La calificación debe ser un número entre 1 y 5']));
            return $response->withStatus(400);
        }

        $result = $rateModel->updateRating($rateId, $stars, $userId);
        if ($result['success']) {
            $response->getBody()->write(json_encode(['message' => $result['message']]));
            return $response->withStatus(200);
        } else {
            $response->getBody()->write(json_encode(['message' => $result['message']]));
            return $response->withStatus(400);
        }
    }


    public function delete(Request $request, Response $response, $args): Response {
        $userModel = new User();
        $authHeader = $request->getHeader('Authorization');
        if (empty($authHeader)) {
            $response->getBody()->write(json_encode(['message' => 'Ausencia del token']));
            return $response->withStatus(401);
        }
        $token = str_replace('Bearer ', '', $authHeader[0]);

        $result = $userModel->checkLogin($token);
        if (empty($result)) {
            $response->getBody()->write(json_encode(['message' => 'Token caducado o incorrecto, se debe iniciar sesión']));
            return $response->withStatus(401);
        } else {
            $userId = $result['id'];
        }

        $rateModel = new Rate();
        $gameId = $args['id'];

        $result = $rateModel->ratingExists($gameId, $userId);
        if ($result['success']) {
            $ratingId = $result['rating_id'];
        } else {
            $response->getBody()->write(json_encode(['message' => 'No tiene una calificacion para elimianar']));
            return $response->withStatus(404);
        }

        $result = $rateModel->deleteRating($ratingId, $userId);
        if ($result['success']) {
            $response->getBody()->write(json_encode(['message' => $result['message']]));
            return $response->withStatus(200);
        } else {
            $response->getBody()->write(json_encode(['message' => $result['message']]));
            return $response->withStatus(409);
        }
    }
}