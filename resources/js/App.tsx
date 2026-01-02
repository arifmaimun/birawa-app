import React from 'react';
import { BrowserRouter as Router, Routes, Route, useLocation } from 'react-router-dom';
import { AnimatePresence } from 'framer-motion';
import Dashboard from './pages/Dashboard';
import PatientDetail from './pages/PatientDetail';
import ClientDetail from './pages/ClientDetail';
import VisitDetail from './pages/VisitDetail';
import ErrorBoundary from './components/utils/ErrorBoundary';

const AnimatedRoutes = () => {
    const location = useLocation();
    
    return (
        <AnimatePresence mode="wait">
            <Routes location={location} key={location.pathname}>
                <Route path="/" element={<Dashboard />} />
                <Route path="/dashboard" element={<Dashboard />} />
                <Route path="/patients" element={<Dashboard />} />
                <Route path="/clients" element={<Dashboard />} />
                <Route path="/patients/:id" element={<PatientDetail />} />
                <Route path="/clients/:id" element={<ClientDetail />} />
                <Route path="/visits/:id" element={<VisitDetail />} />
            </Routes>
        </AnimatePresence>
    );
};

const App: React.FC = () => {
    return (
        <ErrorBoundary>
            <Router>
                <div className="min-h-screen bg-gray-50 text-gray-900 font-sans">
                    <AnimatedRoutes />
                </div>
            </Router>
        </ErrorBoundary>
    );
};

export default App;
