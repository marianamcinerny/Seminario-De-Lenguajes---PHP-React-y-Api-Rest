import React from 'react';
import '../assets/styles/ModalRatingComponent.css';
import Button from './ButtonComponent';

const ModalRating = ({ isOpen, action, initialRating, onClose, onSubmit }) => {
    if (!isOpen) return null;

    const handleSubmit = () => {
        const rating = action !== 'delete' ? document.getElementById('rating-input').value : null;
        onSubmit(rating);
    };

    return (
        <div className="modal">
            <div className="modal-content">
                <h2>{action === 'create' ? 'Crear calificación' : action === 'update' ? 'Modificar calificación' : 'Eliminar calificación'}</h2>
                {action !== 'delete' && (
                    <div>
                        <input
                            id="rating-input"
                            type="number"
                            placeholder="Calificación (1-5)"
                        />
                    </div>
                )}
                <div>
                    <Button onClick={handleSubmit}>{action === 'delete' ? 'Eliminar' : 'Enviar'}</Button>
                    <Button onClick={onClose}>Cerrar</Button>
                </div>
            </div>
        </div>
    );
};

export default ModalRating;