import React, { useState, useEffect } from 'react';
import axios from 'axios';
import { Link } from 'react-router-dom'; 
import { useNavigate } from 'react-router-dom';
import '../../assets/styles/JuegoPage.css';
import Button from '../../components/ButtonComponent';
import ModalMessage from '../../components/ModalMessageComponent';
import ModalRating from '../../components/ModalRatingComponent';
import config from '../../config';


const JuegoPage = () => {
    const navigate = useNavigate();
    const [juegos, setJuegos] = useState([]);
    const [currentPage, setCurrentPage] = useState(1);
    const [totalPages, setTotalPages] = useState(0);
    const [filtros, setFiltros] = useState({
        texto: '',
        clasificacion: '',
        plataforma: []
    });
    const [sesionIniciada, setSesion] = useState(!!localStorage.getItem('token'));
    const token = localStorage.getItem('token');

    const [modalInfo, setModalInfo] = useState({
        isOpen: false,
        title: '',
        message: '',
        type: '',
    });
    const [modalRating, setModalRating] = useState({
        isOpen: false,
        action: '',
        juegoId: null,
        initialRating: null,
    });
    

    const fetchJuegos = async () => {
        console.log(currentPage);
        try {
            const params = {
                pagina: currentPage,
                clasificacion: filtros.clasificacion,
                texto: filtros.texto,
                plataforma: filtros.plataforma
            };

            const response = await axios.get(`${config.apiUrl}juegos`, { params });

            setTotalPages(response.data.totalPages)
            setJuegos(response.data.games);

        } catch (error) {
            if (error.response) {
                setModalInfo({
                    isOpen: true,
                    title: 'Error',
                    message: error.response.data.message,
                    type: 'error',
                });
            }
            setJuegos([]);
        }

    };

    const checkLogin = () => {
        const token = localStorage.getItem('token');
        setSesion(!!token);
    }

    
    useEffect(() => {
        checkLogin();
        fetchJuegos();
        // eslint-disable-next-line react-hooks/exhaustive-deps
    }, [filtros,currentPage]);


    const handleFiltroChange = (e) => {
        const { name, value, checked } = e.target;

        if (name === "plataforma") {
            setFiltros((prevFiltros) => {
                const plataformasActuales = prevFiltros.plataforma;
                if (checked) {
                    return {
                        ...prevFiltros,
                        plataforma: [...plataformasActuales, value]
                    };
                } else {
                    return {
                        ...prevFiltros,
                        plataforma: plataformasActuales.filter((platform) => platform !== value)
                    };
                }
            });
        } else {
            setFiltros((prevFiltros) => ({
                ...prevFiltros,
                [name]: value,
            }));
        }
        setCurrentPage(1);
    };

    const handleCloseModal = () => {
        setModalInfo((prevState) => ({ ...prevState, isOpen: false }));
        if (modalInfo.type === 'error') {
            setFiltros({
                texto: '',
                clasificacion: '',
                plataforma: []
            });
            navigate('/');
        }
    };

    const openModalRating = (action, juegoId, initialRating = null) => {
        setModalRating({
            isOpen: true,
            action,
            juegoId,
            initialRating,
        });
    };
    
    const closeModalRating = () => {
        setModalRating({
            isOpen: false,
            action: '',
            juegoId: null,
            initialRating: null,
        });
    };

    const handleModalSubmit = async (rating = null) => {
        const { action, juegoId } = modalRating;
    
        try {
            if (action === 'create') {
                const response = await axios.post(
                    `${config.apiUrl}calificacion`,
                    { juego_id: juegoId,
                    estrellas: rating },
                    { headers: { Authorization: `Bearer ${token}` } }
                );
                setModalInfo({ 
                    isOpen: true, 
                    title: 'Éxito', 
                    message: response.data.message, 
                    type: 'success' });
            } else if (action === 'update') {
                const response = await axios.put(`${config.apiUrl}calificacion/${juegoId}`,
                    { estrellas: rating },
                    { headers: { Authorization: `Bearer ${token}` } }
                );
                setModalInfo({ 
                    isOpen: true, 
                    title: 'Éxito', 
                    message: response.data.message, 
                    type: 'success' });
            } else if (action === 'delete') {
                const response = await axios.delete(
                    `${config.apiUrl}calificacion/${juegoId}`,
                    { headers: { Authorization: `Bearer ${token}` } }
                );
                setModalInfo({ isOpen: true, 
                    title: 'Éxito', 
                    message: response.data.message, 
                    type: 'success' });
            }
    
            fetchJuegos();
        } catch (error) {
            setModalInfo({
                isOpen: true,
                title: 'Error',
                message: error.response?.data?.message || 'Error inesperado',
                type: 'error',
            });
        } finally {
            closeModalRating();
        }
    };
    

    return (
        <div>
            <div id='filtros'>
                <div>
                    <p>Filtros</p>
                    <div>
                        <input
                            type="text"
                            placeholder="Nombre"
                            name="texto"
                            value={filtros.texto}
                            onChange={handleFiltroChange}
                        />
                        <div>
                            {["PS", "XBOX", "PC", "Android", "Otro"].map((platform) => (
                                <label key={platform}>
                                    <input
                                        type="checkbox"
                                        name="plataforma"
                                        value={platform}
                                        checked={filtros.plataforma.includes(platform)}
                                        onChange={handleFiltroChange}
                                    /> {platform}
                                </label>
                            ))}
                        </div>
                        <select name="clasificacion" onChange={handleFiltroChange}>
                            <option value="">Edad</option>
                            <option value="ATP">ATP</option>
                            <option value="+13">+13</option>
                            <option value="+18">+18</option>
                        </select>
                    </div>
                </div>
            </div>
            <ul id='containerList'>
                {juegos.map((juego) => (
                    <li key={juego.id}>
                        <div id='titulo'>
                            <Link to={`/juego/${juego.id}`}>
                                <h2>{juego.nombre}</h2>
                            </Link>
                        </div>
                        <div id='info'>
                            <p>Clasificación: {juego.clasificacion_edad}</p>
                            <p>Puntuación promedio: {juego.promedio || 'sin calificaciones'}</p>
                            <p>Plataformas: {juego.plataformas.join(', ')}</p>
                        </div>
                        {sesionIniciada && (
                            <div id='botones-calificacion'>
                                <Button onClick={() => openModalRating('create', juego.id)}>Crear calificación</Button>
                                <Button onClick={() => openModalRating('update', juego.id, juego.calificacion)}>Modificar calificación</Button>
                                <Button onClick={() => openModalRating('delete', juego.id)}>Eliminar calificación</Button>
                            </div>
                        )}
                    </li>
                ))}
            </ul>
            <div id='botones'>
                {/* { 
                (filtros.texto || filtros.clasificacion || filtros.plataforma.length > 0) && ( */}
                    {/* <> */}
                        <Button
                            onClick={() => setCurrentPage(currentPage - 1)}
                            disabled={currentPage === 1}
                        >
                            Anterior
                        </Button>

                        <Button
                            onClick={() => setCurrentPage(currentPage + 1)}
                            disabled={currentPage === totalPages}
                        >
                            Siguiente
                        </Button>
                    {/* </> */}
                {/* ) */}
                {/* } */}
            </div>
            <ModalMessage
                isOpen={modalInfo.isOpen}
                onClose={handleCloseModal}
                title={modalInfo.title}
                message={modalInfo.message}
                type={modalInfo.type}
            />
            <ModalRating
                isOpen={modalRating.isOpen}
                action={modalRating.action}
                initialRating={modalRating.initialRating}
                onClose={closeModalRating}
                onSubmit={handleModalSubmit}
            />
        </div>
    );
};

export default JuegoPage;