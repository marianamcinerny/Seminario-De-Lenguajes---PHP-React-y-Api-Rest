<?php

namespace App\Models;

use App\config\Database;

class Game {
    private $conn;

    public function __construct() {
        $db = new Database();
        $this->conn = $db->connect();
    }

    public function getGameInfo($id) {
        $sql = "SELECT j.nombre, j.descripcion, j.imagen, j.clasificacion_edad, 
                    GROUP_CONCAT(DISTINCT p.nombre ORDER BY p.nombre ASC) AS plataformas
                FROM juego j
                LEFT JOIN soporte s ON j.id = s.juego_id
                LEFT JOIN plataforma p ON s.plataforma_id = p.id
                WHERE j.id = '$id'
                GROUP BY j.id";
        $result = $this->conn->query($sql);
    
        if ($result->num_rows > 0) {
            $game = $result->fetch_assoc();
    
            $sql = "SELECT u.nombre_usuario, c.estrellas 
                    FROM calificacion c
                    JOIN usuario u ON c.usuario_id = u.id
                    WHERE c.juego_id = '$id'
                    ORDER BY u.nombre_usuario ASC";
            $result = $this->conn->query($sql);
    
            if ($result->num_rows > 0) {
                $gameRatings = $result->fetch_all(MYSQLI_ASSOC);
    
                $game['plataformas'] = explode(',', $game['plataformas']);
    
                return ['success' => true,
                        'game' => $game,
                        'rating-list' => $gameRatings];
            } else {
                $game['plataformas'] = explode(',', $game['plataformas']);
                return ['success' => true,
                        'game' => $game,
                        'rating-list' => 'el juego no tiene calificaciones'];
            }
        } else {
            return ['success' => false, 'message' => 'Juego no encontrado'];
        }
    }    


    public function create($name, $description, $image, $age, $platform) {
        $sql = "INSERT INTO juego (nombre, descripcion, imagen, clasificacion_edad) 
                VALUES ('$name', '$description', '$image', '$age')";
        $result = $this->conn->query($sql);

        if ($this->conn->affected_rows == 0) {
            return ['success' => false,
                    'message' => 'No se pudo agregar el juego'];
        } else {
            $gameId = $this->getGameIdFromName($name);
            $sqlLastId = "SELECT MAX(id) as last_id FROM soporte";
            $resultLastId = $this->conn->query($sqlLastId);
            $row = $resultLastId->fetch_assoc();
            $newId = $row['last_id'] + 1;
            foreach ($platform as $singlePlatform) {
                $platformId = $this->getPlatformIdFromName($singlePlatform);
                $sqlSoporte = "INSERT INTO soporte (id, juego_id, plataforma_id)
                                VALUES ('$newId', '$gameId', '$platformId')";
                $resultS = $this->conn->query($sqlSoporte);

                if ($this->conn->affected_rows == 0) {
                    return [
                        'success' => false,
                        'message' => "Error al asignar la plataforma $platform"];
                }
                $newId++;
                }
            }
            return ['success' => true,
                    'message' => 'Juego añadido correctamente'];
    }


    public function updateInfo($id, $name, $description, $image, $age, $platform) {
        $sql = "UPDATE juego 
                SET nombre = '$name', descripcion = '$description', imagen = '$image', clasificacion_edad = '$age' 
                WHERE id = '$id'";
        $result = $this->conn->query($sql);

        $deleteSql = "DELETE FROM soporte WHERE juego_id = '$id'";
        $this->conn->query($deleteSql);

        $sqlLastId = "SELECT MAX(id) as last_id FROM soporte";
        $resultLastId = $this->conn->query($sqlLastId);
        $row = $resultLastId->fetch_assoc();
        $newId = $row['last_id'] + 1;
        foreach ($platform as $singlePlatform) {
            $platformId = $this->getPlatformIdFromName($singlePlatform);
            $sqlSoporte = "INSERT INTO soporte (id, juego_id, plataforma_id)
                        VALUES ('$newId', '$id', '$platformId')";
            $this->conn->query($sqlSoporte);
            $newId++;
        }
        return ['success' => true,
                'message' => 'Juego actualizado correctamente'];
    }


    public function delete($id) {
        $sql = "DELETE FROM soporte WHERE juego_id = '$id'";
        $resultS = $this->conn->query($sql);

        $sql = "DELETE FROM juego WHERE id = '$id'";
        $result = $this->conn->query($sql);

        if ($this->conn->affected_rows == 1) {
            return ['success' => true,
                    'message' => 'Juego eliminado correctamente'];
        } else {
            return ['success' => false,
                    'message' => 'No se ha podido eliminar el juego'];
        }
    }


    public function getList($page,$age,$text,$platform,) {
            $sql = "SELECT SQL_CALC_FOUND_ROWS j.id, j.nombre, j.clasificacion_edad, 
                    AVG(c.estrellas) AS promedio, 
                    GROUP_CONCAT(DISTINCT p.nombre ORDER BY p.nombre ASC) AS plataformas
            FROM juego j
            LEFT JOIN calificacion c ON j.id = c.juego_id
            JOIN soporte s ON j.id = s.juego_id
            JOIN plataforma p ON s.plataforma_id = p.id
            WHERE 1=1";
        
            if (!empty($text)) {
                $sql .= " AND j.nombre LIKE '%$text%'";
            }
        
            if (!empty($platform)) {
                $platformsIn = implode("','", $platform);
                $sql .= " AND p.nombre IN ('$platformsIn')";
            }
        
            if (!empty($age)) {
                if ($age == 'ATP') {
                    $sql .= " AND j.clasificacion_edad = 'ATP'";
                } elseif ($age == '+13') {
                    $sql .= " AND (j.clasificacion_edad = 'ATP' OR j.clasificacion_edad = '+13')";
                } elseif ($age == '+18') {
                    $sql .= " AND (j.clasificacion_edad = 'ATP' OR j.clasificacion_edad = '+13' OR j.clasificacion_edad = '+18')";
                }
            }

            // if (!$noFilters) {
                $offset = ($page - 1) * 5;
                $sql .= " GROUP BY j.id, j.nombre, j.clasificacion_edad
                        LIMIT 5 OFFSET $offset";
            // } else {
            //     $sql .= " GROUP BY j.id, j.nombre, j.clasificacion_edad";
            // }
        
            $result = $this->conn->query($sql);

            $totalPages = null;
            // if (!$noFilters) {
                $countResult = $this->conn->query("SELECT FOUND_ROWS() AS total");
                $totalGames = $countResult->fetch_assoc()['total'];
                $totalPages = ceil($totalGames / 5);
            // }
        
            if ($result->num_rows > 0) {
                $games = $result->fetch_all(MYSQLI_ASSOC);
                foreach ($games as &$game) {
                    $game['promedio'] = round($game['promedio'] ?? 0, 2);
                    $game['plataformas'] = explode(',', $game['plataformas']);
                }
                return ['success' => true,
                        'games' => $games,
                        'totalPages' => $totalPages];
            } else {
                return ['success' => false,
                        'message' => 'No hay juegos que coincidan con los filtros'];
            }
    }

    
    public function getGameIdFromName($name) {
        $sql = "SELECT id FROM juego WHERE nombre = '$name'";
        $result = $this->conn->query($sql);
        $result = $result->fetch_assoc();
        return $result['id'];
    }


    public function getPlatformIdFromName($platform) {
        $sqlPlataformId = "SELECT id FROM plataforma WHERE nombre = '$platform'";
        $resultPlataformId = $this->conn->query($sqlPlataformId);
        $row = $resultPlataformId->fetch_assoc();
        return $row['id'];
    }


    public function ratings($id) {
        $sql = "SELECT id FROM calificacion WHERE juego_id = '$id'";
        $result = $this->conn->query($sql);
        if ($result->num_rows > 0) {
            return true;
        } else {
            return false;
        }
    }


    public function gameNameExists ($name) {
        $sql = "SELECT nombre FROM juego WHERE nombre = '$name'";
        $result = $this->conn->query($sql);
        if ($result->num_rows === 1) {
            return true;
        } else {
            return false;
        }
    }

    public function gameExists($id) {
        $sql = "SELECT nombre FROM juego WHERE id = '$id'";
        $result = $this->conn->query($sql);
        
        if ($result->num_rows === 1) {
            $row = $result->fetch_assoc();
            return $row['nombre'];
        } else {
            return null;
        }
    }
}