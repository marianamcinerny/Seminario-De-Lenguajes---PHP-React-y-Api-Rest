<?php

namespace App\Models;


use App\config\Database;

class User {
    private $conn;

    public function __construct() {
        $db = new Database();
        $this->conn = $db->connect();
    }


    public function createUser($username, $password) {
        $sql = "INSERT INTO usuario (nombre_usuario, clave) VALUES ('$username', '$password')";
        $result = $this->conn->query($sql);
        if ($result) {
            return ['success' => true,
                    'message' => 'Usuario creado exitosamente'];
        } else {
            return ['success' => false,
                    'message' => 'Ocurrio un error y no se pudo crear el usuario'];
        }
    }


    public function updateUser($id,$newUsername,$newPassword) {
        $sql = "UPDATE usuario SET nombre_usuario = '$newUsername', clave = '$newPassword' WHERE id = '$id'";
        $result = $this->conn->query($sql);
        if ($this->conn->affected_rows == 1) {
            return ['success' => true,
                    'message' => 'Usuario actualizado correctamente'];
        } else {
            return ['success' => false,
                    'message' => 'No se pudo actualizar el usuario'];
        }
    }


    public function deleteUser($id) {
        $sql = "DELETE FROM usuario WHERE id = '$id'";
        $result = $this->conn->query($sql);
        if ($this->conn->affected_rows == 1) {
            return ['success' => true,
                    'message' => 'Usuario eliminado correctamente'];
        } else {
            return ['success' => false,
                    'message' => 'Solo se puede eliminar el usuario propio'];
        }
    }


    public function getUser($id) {
        $sql = "SELECT id, nombre_usuario, clave, es_admin FROM usuario WHERE id = '$id'";
        $result = $this->conn->query($sql);
        if ($result->num_rows == 1) {
            return ['success' => true,
                    'info' => $result->fetch_assoc()];
        } else {
            return ['success' => false,
                    'message' => 'No se puso obtener la informacion del usuario'];
        }
    }


    public function checkAdmin($token) {
        $sql = "SELECT es_admin FROM usuario WHERE token = '$token'";
        $result = $this->conn->query($sql);
        $admin = $result->fetch_assoc();
        if ($admin['es_admin'] == 1) {
            return true;
        } else {
            return false;
        }
    }


    public function getUserIdFromToken($token) {
        $sql = "SELECT id FROM usuario WHERE token = '$token'";
        $result = $this->conn->query($sql);
        if ($result->num_rows > 0) {
            $result = $result->fetch_assoc();
            return $result['id'];
        } else {
            return null;
        }
    }


    public function usernameExists ($username) {
        $sql = "SELECT nombre_usuario FROM usuario WHERE nombre_usuario = '$username'";
        $result = $this->conn->query($sql);
        $result->fetch_assoc();
        if ($result->num_rows === 1) {
            return true;
        } else {
            return false;
        }
    }


    public function userExists ($id) {
        $sql = "SELECT nombre_usuario FROM usuario WHERE id = '$id'";
        $result = $this->conn->query($sql);
        if ($result->num_rows === 1) {
            return true;
        } else {
            return false;
        }
    }


    public function authenticate($username,$password) {
        $sql = "SELECT clave, id, es_admin FROM usuario WHERE nombre_usuario = '$username'";
        $result = $this->conn->query($sql);
        $user = $result->fetch_assoc();
        if ($user['clave'] === $password) { 
            return [
                'success' => true,
                'id' => $user['id'],
                'es_admin' => $user['es_admin']
            ];
        } else {
            return [
                'success' => false,
                'message' => 'Contraseña incorrecta'
            ];
        }    
    }


    public function saveToken($id,$token) {
        date_default_timezone_set('America/Argentina/Buenos_Aires');
        $exp = date('Y-m-d H:i:s', strtotime('+1 hour'));
        $sql = "UPDATE usuario SET token = '$token', vencimiento_token = '$exp' WHERE id = '$id'";
        $this->conn->query($sql);
    }


    public function checkLogin($token) {
        date_default_timezone_set('America/Argentina/Buenos_Aires');
        $now = date('Y-m-d H:i:s');
        $sql = "SELECT id, es_admin, vencimiento_token FROM usuario WHERE token = '$token'";

        $result = $this->conn->query($sql);
        if ($result->num_rows === 1) {

            $res = $result->fetch_assoc();
            $expToken = $res['vencimiento_token'];

            if ($expToken > $now) {
                return $res;
            } else {
                return null;
            }
        } else {
            return null;
        }
    }


    public function validateUsername($username) {
        if ((strlen($username) < 6) || (strlen($username) > 20) || (!(preg_match('/^[a-zA-Z0-9]+$/', $username)))) {
            return false;
        } else {
            return true;
        }
    }

    public function validatePassword($password) {
        if ((strlen($password) < 8) || (strlen($password) >16) || (!preg_match('/[A-Z]/',$password)) || (!preg_match('/[a-z]/', $password)) || (!preg_match('/\d/', $password)) || (!preg_match('/[\W_]/', $password))) {
            return false;
        } else {
            return true;
        }
    }
}