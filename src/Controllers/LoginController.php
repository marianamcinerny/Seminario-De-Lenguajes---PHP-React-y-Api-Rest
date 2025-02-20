<?php

namespace App\Controllers;

use App\Models\User;
use Slim\Psr7\Request;
use Slim\Psr7\Response;

class LoginController {

    public function login(Request $request, Response $response): Response {
        $body = $request->getBody()->getContents();
        $data = json_decode($body, true);

        $username = $data['nombre_usuario'] ?? null;
        $password = $data['clave'] ?? null;

        if (empty($username) || empty($password)) {
            $response->getBody()->write(json_encode([
                                    'success' => false,
                                    'message' => "Se deben completar todos los campos"]));
            return $response->withStatus(400);
        }

        $userModel = new User();

        $result = $userModel->usernameExists($username);
        if ($result) {
            $result = $userModel->authenticate($username,$password);
            if ($result['success']) {
                $token = bin2hex(random_bytes(16));
                $userModel->saveToken($result['id'],$token);

                $response->getBody()->write(json_encode([
                    'success' => true,
                    'message' => 'Se ha iniciado sesion correctamente',
                    'token' => $token,
                    'admin' => $result['es_admin']
                ]));
            return $response->withStatus(200);
            } else {
                $response->getBody()->write(json_encode( [
                    'success' => false,
                    'message' => $result['message']]));
            return $response->withStatus(401);
            }
        } else {
            $response->getBody()->write(json_encode([
                    'success' => false,
                    'message' => 'El usuario no existe']));
            return $response->withStatus(401);
        }
    }

    public function create(Request $request, Response $response): Response {
        $body = $request->getBody()->getContents();
        $data = json_decode($body, true);

        $username = $data['nombre_usuario'] ?? null;
        $password = $data['clave'] ?? null;

        if (empty($username) || empty($password)) {
            $response->getBody()->write(json_encode(['message' => 'Se deben completar todos los campos']));
            return $response->withStatus(400);
        }

        $userModel = new User();
        if (!$userModel->validateUsername($username)) {
            $response->getBody()->write(json_encode(['message' => 'El nombre de usuario debe tener entre 6 y 20 caracteres y ser alfanumerico']));
            return $response->withStatus(400);
        }

        if (!$userModel->validatePassword($password)) {
            $response->getBody()->write(json_encode(['message' => 'La contraseña debe tener mas de 8 caracteres, contener al menos una minuscula, una mayuscula, un numero y un caracter especial']));
            return $response->withStatus(400);
        }

        if ($userModel->usernameExists($username)) {
            $response->getBody()->write(json_encode(['message' => 'El nombre de usuario está en uso']));
            return $response->withStatus(400);
        }

        $result = $userModel->createUser($username, $password);

        if ($result['success']) {
            $response->getBody()->write(json_encode(['message' => $result['message']]));
            return $response->withStatus(200);
        } else {
            $response->getBody()->write(json_encode(['message' => $result['message']]));
            return $response->withStatus(400);
        }
    }
}