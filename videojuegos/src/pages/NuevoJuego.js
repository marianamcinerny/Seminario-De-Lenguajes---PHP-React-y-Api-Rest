import React, { useState } from 'react';
import axios from 'axios';
import { useNavigate } from 'react-router-dom';
import '../assets/styles/NuevoJuego.css';
import Button from '../components/ButtonComponent';
import ModalMessage from '../components/ModalMessageComponent';
import config from '../config';

const CreateGame = () => {
    const navigate = useNavigate();
    const [modalInfo, setModalInfo] = useState({
        isOpen: false,
        title: '',
        message: '',
        type: 'success',
    });

    const [formData, setFormData] = useState({
        nombre: '',
        descripcion: '',
        imagen: '',
        clasificacionEdad: '',
        plataformas: [],
    });

    const handleFileChange = (e) => {
        const file = e.target.files[0];
        if (file && file.type === 'image/jpeg') {
            const reader = new FileReader();
            reader.onloadend = () => {
                setFormData({
                    ...formData,
                    imagen: reader.result.split(',')[1],
                });
            };
            reader.readAsDataURL(file);
        } else {
            setModalInfo({
                isOpen: true,
                title: 'Error',
                message: 'Solo se permiten imágenes en formato JPEG',
                type: 'error',
            });
        }
    };

    const handleCheckboxChange = (e) => {
        const { value, checked } = e.target;
        setFormData((prevState) => {
            const updatedPlataformas = checked
                ? [...prevState.plataformas, value]
                : prevState.plataformas.filter((plataforma) => plataforma !== value);

            return {
                ...prevState,
                plataformas: updatedPlataformas,
            };
        });
    };

    const handleSubmit = async (e) => {
        e.preventDefault();

        if (!formData.nombre || formData.length > 45) {
            setModalInfo({
                isOpen: true,
                title: 'Error',
                message: 'El nombre es obligatorio y no debe tener mas de 45 caracteres',
                type: 'error',
            });
            return;
        }

        if (!formData.descripcion) {
                setModalInfo({
                isOpen: true,
                title: 'Error',
                message: 'La descripcion del juego es obligatoria',
                type: 'error',
            });
            return;
        }

        if (!formData.imagen) {
            setModalInfo({
                isOpen: true,
                title: 'Error',
                message: 'La imagen es obligatoria y debe ser en formato JPEG',
                type: 'error',
            });
            return;
        }

        if (formData.clasificacionEdad === '') {
            setModalInfo({
                isOpen: true,
                title: 'Error',
                message: 'La clasificacion de edad es obligatoria',
                type: 'error',
            });
            return;
        }

        if (formData.plataformas.length === 0) {
            setModalInfo({
                isOpen: true,
                title: 'Error',
                message: 'Se debe elegir por lo menos una plataforma',
                type: 'error',
            });
            return;
        }

        try {
            const token = localStorage.getItem('token');
            const response = await axios.post(`${config.apiUrl}juego`, {
                    nombre: formData.nombre,
                    descripcion: formData.descripcion,
                    imagen: formData.imagen,
                    clasificacion_edad: formData.clasificacionEdad,
                    plataforma: formData.plataformas,
                },
                {
                    headers: {Authorization: `Bearer ${token}`},
                }
            );

            setFormData({
                nombre: '',
                descripcion: '',
                imagen: '',
                clasificacionEdad: '',
                plataformas: [],
            });

            setModalInfo({
                isOpen: true,
                title: 'Juego creado',
                message: response.data.message,
                type: 'success',
            });


        } catch (error) {
            setModalInfo({
                isOpen: true,
                title: 'Error',
                message: error.response?.data?.message || 'Hubo un problema al crear el juego.',
                type: 'error',
            });
        }
    };

    const handleCloseModal = () => {
        setModalInfo((prevState) => ({ ...prevState, isOpen: false }));
        if (modalInfo.type === 'success') {
            navigate('/');
        }
    };

    return (
        <div id="form-container">
            <form id='form' onSubmit={handleSubmit}>
                <label>
                    Nombre (máx. 45 caracteres):
                    <input
                        type="text"
                        name="nombre"
                        value={formData.nombre}
                        onChange={(e) => setFormData({ ...formData, nombre: e.target.value })}
                        maxLength="45"
                    />
                </label>

                <label>
                    Descripción:
                    <textarea
                        name="descripcion"
                        value={formData.descripcion}
                        onChange={(e) => setFormData({ ...formData, descripcion: e.target.value })}
                    ></textarea>
                </label>

                <label>
                    Imagen (JPEG):
                    <input
                        type="file"
                        name="imagen"
                        accept="image/jpeg"
                        onChange={handleFileChange}
                    />
                </label>

                <label>
                    Clasificación por edad:
                    <select
                        name="clasificacionEdad"
                        value={formData.clasificacionEdad}
                        onChange={(e) => setFormData({ ...formData, clasificacionEdad: e.target.value })}
                    >
                        <option value="">Seleccione una opción</option>
                        <option value="ATP">ATP</option>
                        <option value="+13">+13</option>
                        <option value="+18">+18</option>
                    </select>
                </label>

                <label>Plataforma:
                    {['PS', 'XBOX', 'PC', 'Android', 'Otro'].map((plataforma) => (
                        <label key={plataforma}>
                            <input
                                type="checkbox"
                                value={plataforma}
                                checked={formData.plataformas.includes(plataforma)}
                                onChange={handleCheckboxChange}
                            />
                            {plataforma}
                        </label>
                    ))}
                </label>
                <Button type="submit">Crear juego</Button>
            </form>
            <ModalMessage
                isOpen={modalInfo.isOpen}
                onClose={handleCloseModal}
                title={modalInfo.title}
                message={modalInfo.message}
                type={modalInfo.type}
            />
        </div>
    );
};

export default CreateGame;
