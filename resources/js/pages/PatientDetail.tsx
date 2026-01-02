import React, { useEffect, useState } from 'react';
import { useParams, useNavigate } from 'react-router-dom';
import { motion } from 'framer-motion';
import { getPatient, deletePatient } from '../services/api';
import { Patient } from '../types/models';
import { ArrowLeft, Calendar, Activity, AlertCircle, CheckCircle, Edit, Trash2 } from 'lucide-react';
import { calculateAge } from '../utils/formatters';

const PatientDetail: React.FC = () => {
    const { id } = useParams<{ id: string }>();
    const navigate = useNavigate();
    const [patient, setPatient] = useState<Patient | null>(null);
    const [loading, setLoading] = useState(true);

    useEffect(() => {
        const fetchPatient = async () => {
            if (!id) return;
            try {
                const data = await getPatient(id);
                setPatient(data);
            } catch (error) {
                console.error("Failed to fetch patient", error);
            } finally {
                setLoading(false);
            }
        };

        fetchPatient();
    }, [id]);

    const handleEdit = () => {
        // In real app, navigate to edit page or open modal
        // For now, we can redirect to Laravel edit page if we want hybrid
        // window.location.href = `/patients/${id}/edit`;
        alert('Edit functionality would go here');
    };

    const handleDelete = async () => {
        if (confirm('Are you sure you want to delete this patient? This action cannot be undone.')) {
            try {
                if (patient?.id) {
                    await deletePatient(patient.id);
                    navigate('/');
                }
            } catch (error) {
                console.error("Failed to delete", error);
                alert("Failed to delete patient");
            }
        }
    };

    if (loading) {
        return (
            <div className="flex justify-center items-center min-h-screen">
                <div className="animate-spin rounded-full h-12 w-12 border-t-2 border-b-2 border-blue-500"></div>
            </div>
        );
    }

    if (!patient) {
        return (
            <div className="max-w-4xl mx-auto px-4 py-8 text-center">
                <h2 className="text-2xl font-bold text-gray-800">Patient Not Found</h2>
                <button 
                    onClick={() => navigate('/')}
                    className="mt-4 text-blue-600 hover:text-blue-800 font-medium"
                >
                    Back to Dashboard
                </button>
            </div>
        );
    }

    return (
        <motion.div 
            initial={{ opacity: 0, x: 20 }}
            animate={{ opacity: 1, x: 0 }}
            exit={{ opacity: 0, x: -20 }}
            transition={{ duration: 0.3 }}
            className="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8 py-8"
        >
            <button 
                onClick={() => navigate(-1)}
                className="flex items-center text-gray-500 hover:text-gray-700 mb-6 transition-colors"
            >
                <ArrowLeft className="w-5 h-5 mr-1" />
                Back
            </button>

            <div className="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
                <div className="bg-gradient-to-r from-blue-500 to-indigo-600 px-8 py-10 text-white flex justify-between items-start">
                    <div className="flex items-center space-x-6">
                        <div className="w-24 h-24 bg-white/20 backdrop-blur-sm rounded-full flex items-center justify-center ring-4 ring-white/30">
                            <span className="text-4xl font-bold">{patient.name[0]}</span>
                        </div>
                        <div>
                            <h1 className="text-3xl font-bold">{patient.name}</h1>
                            <p className="text-blue-100 mt-1 text-lg">{patient.species} • {patient.breed}</p>
                            <div className="flex items-center mt-3 space-x-4">
                                <span className="bg-white/20 px-3 py-1 rounded-full text-sm font-medium backdrop-blur-sm">
                                    {patient.gender}
                                </span>
                                <span className="bg-white/20 px-3 py-1 rounded-full text-sm font-medium backdrop-blur-sm">
                                    {calculateAge(patient.dob)}
                                </span>
                            </div>
                        </div>
                    </div>
                    
                    <div className="flex items-center space-x-3 ml-auto">
                        <button 
                            onClick={handleEdit}
                            className="p-2 bg-white/20 hover:bg-white/30 text-white rounded-full transition-colors"
                            aria-label="Edit Patient"
                        >
                            <Edit className="w-5 h-5" />
                        </button>
                        <button 
                            onClick={handleDelete}
                            className="p-2 bg-white/20 hover:bg-red-500 text-white rounded-full transition-colors"
                            aria-label="Delete Patient"
                        >
                            <Trash2 className="w-5 h-5" />
                        </button>
                    </div>
                </div>

                <div className="grid grid-cols-1 md:grid-cols-3 gap-0">
                    <div className="col-span-2 p-8 border-r border-gray-100">
                        <h2 className="text-xl font-bold text-gray-800 mb-6 flex items-center">
                            <Activity className="w-5 h-5 mr-2 text-blue-500" />
                            Medical History
                        </h2>

                        <div className="space-y-6">
                            {patient.medical_records && patient.medical_records.length > 0 ? (
                                patient.medical_records.map(record => (
                                        <div key={record.id} className="relative pl-8 pb-6 border-l-2 border-gray-200 last:pb-0">
                                            <div className="absolute -left-[9px] top-0 w-4 h-4 rounded-full bg-blue-500 ring-4 ring-white"></div>
                                            <div className="mb-1 text-sm text-gray-500 font-medium">
                                                {new Date(record.created_at).toLocaleDateString()}
                                            </div>
                                            <h3 className="text-lg font-semibold text-gray-800">
                                                {record.diagnoses && record.diagnoses.length > 0 
                                                    ? record.diagnoses.map(d => d.name).join(', ') 
                                                    : (record.diagnosis || 'No Diagnosis')}
                                            </h3>
                                            <p className="text-gray-600 mt-1">
                                                {record.plan_treatment || record.treatment || 'No Treatment Info'}
                                            </p>
                                        </div>
                                    ))
                            ) : (
                                <p className="text-gray-500 italic">No medical records found.</p>
                            )}
                        </div>
                    </div>

                    <div className="p-8 bg-gray-50">
                        <h2 className="text-lg font-bold text-gray-800 mb-4">Quick Info</h2>
                        
                        <div className="space-y-4">
                            <div>
                                <label className="text-xs font-semibold text-gray-400 uppercase tracking-wider">Date of Birth</label>
                                <p className="text-gray-700 font-medium flex items-center mt-1">
                                    <Calendar className="w-4 h-4 mr-2 text-gray-400" />
                                    {new Date(patient.dob).toLocaleDateString()}
                                </p>
                            </div>

                            <div>
                                <label className="text-xs font-semibold text-gray-400 uppercase tracking-wider">Status</label>
                                <div className="mt-1">
                                    {patient.is_sterile ? (
                                        <span className="inline-flex items-center text-green-700 bg-green-100 px-2 py-1 rounded text-sm font-medium">
                                            <CheckCircle className="w-3 h-3 mr-1" />
                                            Sterilized
                                        </span>
                                    ) : (
                                        <span className="inline-flex items-center text-yellow-700 bg-yellow-100 px-2 py-1 rounded text-sm font-medium">
                                            <AlertCircle className="w-3 h-3 mr-1" />
                                            Not Sterilized
                                        </span>
                                    )}
                                </div>
                            </div>

                            {patient.allergies && (
                                <div>
                                    <label className="text-xs font-semibold text-gray-400 uppercase tracking-wider">Allergies</label>
                                    <p className="text-red-600 font-medium mt-1 bg-red-50 p-2 rounded-md border border-red-100">
                                        {patient.allergies}
                                    </p>
                                </div>
                            )}

                            {patient.special_conditions && (
                                <div>
                                    <label className="text-xs font-semibold text-gray-400 uppercase tracking-wider">Special Conditions</label>
                                    <p className="text-amber-600 font-medium mt-1 bg-amber-50 p-2 rounded-md border border-amber-100">
                                        {patient.special_conditions}
                                    </p>
                                </div>
                            )}
                        </div>
                    </div>
                </div>
            </div>
        </motion.div>
    );
};

export default PatientDetail;
