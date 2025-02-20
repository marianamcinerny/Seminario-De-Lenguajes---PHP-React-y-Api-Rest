<?php

namespace App\Controllers;

use App\Models\User;
use App\Models\Rate;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

class UserController {

    public function create(Request $request, Response $response): Response {
        $data = $request->getParsedBody();
        $username = $data['nombre_usuario'] ?? null;
        $password = $data['clave'] ?? null;

        if (empty($username) || empty($password)) {
            $response->getBody()->write('Se deben completar todos los campos');
            return $response->withStatus(400);
        }

        $userModel = new User();
        if (!$userModel->validateUsername($username)) {
            $response->getBody()->write('El nombre de usuario debe tener entre 6 y 20 caracteres y ser alfanumerico');
            return $response->withStatus(400);
        }

        if (!$userModel->validatePassword($password)) {
            $response->getBody()->write('La contraseña debe tener mas de 8 caracteres, contener al menos una minuscula, una mayuscula, un numero y un caracter especial');
            return $response->withStatus(400);
        }

        if ($userModel->usernameExists($username)) {
            $response->getBody()->write('El nombre de usuario está en uso');
            return $response->withStatus(400);
        }

        $result = $userModel->createUser($username, $password);
        if ($result['success']) {
            $response->getBody()->write($result['message']);
            return $response->withStatus(200);
        } else {
            $response->getBody()->write($result['message']);
            return $response->withStatus(400);
        }
    }


    public function update(Request $request, Response $response, $args): Response {
        $userModel = new User;
        $authHeader = $request->getHeader('Authorization');
        if (empty($authHeader)) {
            $response->getBody()->write('Ausencia del token');
            return $response->withStatus(401);
        }
        $token = str_replace('Bearer ', '', $authHeader[0]);

        $result = $userModel->checkLogin($token);
        if (empty($result)) {
            $response->getBody()->write('Token caducado');
            return $response->withStatus(401);
        }

        $id = $args['id'];

        $data = $request->getParsedBody();
        $newUsername = $data['nombre_usuario'] ?? null;
        $newPassword = $data['clave'] ?? null;

        if (empty($newUsername) || empty($newPassword)) {
            $response->getBody()->write('Se deben completar todos los campos');
            return $response->withStatus(400);
        }

        if (!$userModel->userExists($id)) {
            $response->getBody()->write('El usuario que se quiere modificar no existe');
            return $response->withStatus(404);
        }

        if (!$userModel->validateUsername($newUsername)) {
            $response->getBody()->write('El nombre de usuario debe tener entre 6 y 20 caracteres y ser alfanumerico');
            return $response->withStatus(400);
        }

        if (!$userModel->validatePassword($newPassword)) {
            $response->getBody()->write('La contraseña debe tener mas de 8 caracteres, contener al menos una minuscula, una mayuscula, un numero y un caraccter especial');
            return $response->withStatus(401);
        }

        if ($userModel->usernameExists($newUsername)){
            $response->getBody()->write("El nombre de usuario '$newUsername' ya esta en uso");
            return $response->withStatus(401);
        }

        $result = $userModel->updateUser($id,$newUsername,$newPassword);
        if ($result['success']) {
            $response->getBody()->write($result['message']);
            return $response->withStatus(200);
        } else {
            $response->getBody()->write($result['message']);
            return $response->withStatus(400);
        }
    }


    public function delete (Request $request, Response $response, $args): Response {
        $userModel = new User();
        $authHeader = $request->getHeader('Authorization');
        if (empty($authHeader)) {
            $response->getBody()->write('Ausencia del token');
            return $response->withStatus(401);
        }
        $token = str_replace('Bearer ', '', $authHeader[0]);

        $result = $userModel->checkLogin($token);
        if (empty($result)) {
            $response->getBody()->write('Token caducado');
            return $response->withStatus(401);
        }

        $id = $args['id'];
        if (!$userModel->userExists($id)) {
            $response->getBody()->write('El usuario que se quiere modificar no existe');
            return $response->withStatus(404);
        }

        $rateModel = new Rate();
        if ($rateModel->madeRating($id)) {
            $response->getBody()->write('No se puede eliminar un usuario que tiene calificaciones hechas');
            return $response->withStatus(409);
        }

        $result = $userModel->deleteUser($id);
        if ($result['success']) {
            $response->getBody()->write($result['message']);
            return $response->withStatus(200);
        } else {
            $response->getBody()->write($result['message']);
            return $response->withStatus(409);
        }
    }


    public function retrieve (Request $request, Response $response, $args): Response {
        $userModel = new User();
        $authHeader = $request->getHeader('Authorization');
        if (empty($authHeader)) {
            $response->getBody()->write('Ausencia de token');
            return $response->withStatus(401);
        }
        $token = str_replace('Bearer ', '', $authHeader[0]);

        $result = $userModel->checkLogin($token);
        if (empty($result)) {
            $response->getBody()->write('Token caducado');
            return $response->withStatus(401);
        }

        $id = $args['id'];
        if (!$userModel->userExists($id)) {
            $response->getBody()->write('El usuario no existe');
            return $response->withStatus(404);
        }
        
        $result = $userModel->getUser($id);
        if ($result['success']) {
            $userInfo = $result['info'];
            $response->getBody()->write("ID: " . $userInfo['id'] . ", usuario: " . $userInfo['nombre_usuario'] . ", contraseña: " . $userInfo['clave'] . ", admin: " . $userInfo['es_admin']);
            return $response->withStatus(200);
        } else {
            $response->getBody()->write($result['message']);
            return $response->withStatus(400);
        }
    }
}