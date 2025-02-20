<?php

namespace App\Models;

use App\config\Database;

class Rate {
    private $conn;

    public function __construct() {
        $db = new Database();
        $this->conn = $db->connect();
    }




    public function create($stars,$userId,$gameId) {
        $sql = "INSERT INTO calificacion (estrellas, usuario_id, juego_id) VALUES ('$stars','$userId','$gameId')";
        $result = $this->conn->query($sql);
        if ($result) {
            if ($this->conn->affected_rows == 1) {
                return ['success' => true,
                        'message' => 'Calificación creada'];
            } else {
                return ['success' => false,
                        'message' => 'No se pudo crear la calificación'];
            }
        }
    }


    public function updateRating($id,$stars,$userId) {
        $sql = "UPDATE calificacion SET estrellas = '$stars' WHERE id = '$id' AND usuario_id = '$userId'";
        $result = $this->conn->query($sql);
        if ($result) {
            if ($this->conn->affected_rows == 1) {
                return ['success' => true,
                        'message' => 'Calificación modificada correctamente'];
            } else {
                return ['success' => false,
                        'message' => 'La calificacion no se modificó porque ingresó las mismas estrellas que antes'];
            }
        }
    }


    public function deleteRating($id,$userId) {
        $sql = "DELETE FROM calificacion WHERE id = '$id' AND usuario_id = '$userId'";
        $result = $this->conn->query($sql);
        if ($result) {
            if ($this->conn->affected_rows == 1) {
                return ['success' => true,
                        'message' => 'Calificación eliminada correctamente'];
            } else {
                return ['success' => false,
                        'message' => 'Solo se puede eliminar una calificación propia'];
            }
        }
    }


    public function ratingExistsUser($gameId,$userId) {
        $sql = "SELECT id FROM calificacion WHERE juego_id = '$gameId' AND usuario_id = '$userId'";
        $result = $this->conn->query($sql);
        if ($result->num_rows > 0) {
            return true;
        } else {
            return false;
        }
    }

    public function ratingExists($gameId, $userId){
        $sql = "SELECT id FROM calificacion WHERE juego_id = '$gameId' AND usuario_id = '$userId'";
        $result = $this->conn->query($sql);
        $info = $result->fetch_assoc();
        if ($result->num_rows > 0) {
            return ['success' => true,
                    'rating_id' => $info['id']];
        } else {
            return ['success' => false];
        }

    }

    public function madeRating($idUser) {
        $sql = "SELECT id FROM calificacion WHERE usuario_id = '$idUser'";
        $result = $this->conn->query($sql);
        if ($result->num_rows > 0) {
            return true;
        } else {
            return false;
        }
    }
}