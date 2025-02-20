import React from 'react';
import '../assets/styles/ModalMessageComponent.css'
import Button from './ButtonComponent';

const ModalMessageComponent = ({ isOpen, onClose, title, message, type }) => {
    if (!isOpen) return null;

    const modalClass = `modal-content ${type}`;

    return (
        <div className="modal-overlay">
            <div className={modalClass}>
                <h2>{title}</h2>
                <p>{message}</p>
                <Button onClick={onClose}>OK</Button>
            </div>
        </div>
    );
};

export default ModalMessageComponent;

