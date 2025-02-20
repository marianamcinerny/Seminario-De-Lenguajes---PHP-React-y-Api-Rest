import React, { useEffect, useState } from 'react';
import { useParams } from 'react-router-dom';
import axios from 'axios';
import '../assets/styles/DetalleJuego.css'
import config from '../config';

const DetalleJuego = () => {
    const { id } = useParams();
    const [game, setGame] = useState(null);
    const [ratings, setRatings] = useState([]);
    const [error, setError] = useState(null);
    const user = localStorage.getItem('username');

    useEffect(() => {
        const fetchGameInfo = async () => {
        try {
            const response = await axios.get(`${config.apiUrl}juegos/${id}`);
            if (response.data.success) {
            setGame(response.data.game);
            setRatings(response.data['rating-list']);
            } else {
            setError(response.data.message);
            }
        } catch (error) {
            setError(error.response.data.message);
        }
        };

        fetchGameInfo();
    }, [id]);

    if (error) {
        return <div>{error}</div>;
    }

    if (!game) {
        return <div>cargando</div>;
    }

    return (
        <div className="game-page">
            <h1>{game.nombre}</h1>
            <img src={`data:image/jpeg;base64,${game.imagen}`} alt={game.nombre} />
            <p><strong>Descripción:</strong> {game.descripcion}</p>
            <p><strong>Clasificación por Edad:</strong> {game.clasificacion_edad}</p>
            
            <div>
                <h3>Plataformas:</h3>
                <ul>
                {game.plataformas.map((plataforma, index) => (
                    <li key={index}>{plataforma}</li>
                ))}
                </ul>
            </div>

            <div>
                <h3>Calificaciones:</h3>
                {Array.isArray(ratings) && ratings.length > 0 ? (
                <ul>
                    {ratings.map((rating, index) => (
                    <li key={index}>
                        {rating.nombre_usuario === user ? (
                            <strong>{rating.nombre_usuario}</strong>
                        ) : (
                            rating.nombre_usuario
                        )}
                        : {rating.estrellas} estrellas
                    </li>
                    ))}
                </ul>
                ) : (
                <p>{ratings}</p>
                )}
            </div>
        </div>
    );
};

export default DetalleJuego;
