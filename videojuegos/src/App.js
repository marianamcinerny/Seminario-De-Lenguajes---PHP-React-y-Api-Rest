import { BrowserRouter as Router, Routes, Route } from 'react-router-dom';
import HeaderComponent from "./components/HeaderComponent";
import NavBarComponent from "./components/NavBarComponent";
import FooterComponent from "./components/FooterComponent";
import JuegoPage from './pages/juego/JuegoPage';
import RegistroPage from './pages/registro/RegistroPage';
import LoginPage from './pages/LoginPage';
import DetalleJuego from './pages/DetalleJuego';
import NuevoJuego from './pages/NuevoJuego';


function App() {
  return (
    <div>
      <HeaderComponent/>

      <Router>
        <NavBarComponent />
        <Routes>
          <Route path="/" element={<JuegoPage />} />
          <Route path="/juego/:id" element={<DetalleJuego />} />
          <Route path="/registro" element={<RegistroPage />} />
          <Route path="/login" element={<LoginPage />} />
          <Route path='/nuevo-juego' element={<NuevoJuego />} />
        </Routes>
      </Router>

      <FooterComponent/>
    </div>
  )
}

export default App;
