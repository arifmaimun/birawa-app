import React, { useEffect, useState } from 'react';
import { useParams, useNavigate } from 'react-router-dom';
import { motion } from 'framer-motion';
import { getClient, deleteClient } from '../services/api';
import { Client } from '../types/models';
import { ArrowLeft, Phone, MapPin, Briefcase, User, Edit, Trash2, PawPrint, Mail } from 'lucide-react';
import Card from '../components/ui/Card';

const ClientDetail: React.FC = () => {
    const { id } = useParams<{ id: string }>();
    const navigate = useNavigate();
    const [client, setClient] = useState<Client | null>(null);
    const [loading, setLoading] = useState(true);

    useEffect(() => {
        const fetchClient = async () => {
            if (!id) return;
            try {
                const data = await getClient(id);
                setClient(data);
            } catch (error) {
                console.error("Failed to fetch client", error);
            } finally {
                setLoading(false);
            }
        };

        fetchClient();
    }, [id]);

    const handleEdit = () => {
         // window.location.href = `/clients/${id}/edit`;
         alert('Edit functionality would go here');
    };

    const handleDelete = async () => {
        if (confirm('Are you sure you want to delete this client? This action cannot be undone.')) {
             try {
                if (client?.id) {
                    await deleteClient(client.id);
                    navigate('/');
                }
            } catch (error) {
                console.error("Failed to delete", error);
                alert("Failed to delete client");
            }
        }
    };

    if (loading) {
        return (
            <div className="flex justify-center items-center min-h-screen">
                <div className="animate-spin rounded-full h-12 w-12 border-t-2 border-b-2 border-indigo-500"></div>
            </div>
        );
    }

    if (!client) {
        return (
            <div className="max-w-4xl mx-auto px-4 py-8 text-center">
                <h2 className="text-2xl font-bold text-gray-800">Client Not Found</h2>
                <button 
                    onClick={() => navigate('/')}
                    className="mt-4 text-indigo-600 hover:text-indigo-800 font-medium"
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

            <div className="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden mb-8">
                <div className="bg-indigo-600 px-8 py-10 text-white flex justify-between items-start">
                    <div className="flex items-center space-x-6">
                        <div className="w-20 h-20 bg-white/20 backdrop-blur-sm rounded-full flex items-center justify-center ring-4 ring-white/30">
                            {client.is_business ? <Briefcase className="w-10 h-10" /> : <User className="w-10 h-10" />}
                        </div>
                        <div>
                            <h1 className="text-3xl font-bold">{client.is_business ? client.business_name : client.name}</h1>
                            {client.is_business && (
                                <p className="text-indigo-100 mt-1 flex items-center">
                                    <User className="w-4 h-4 mr-1" />
                                    Contact: {client.contact_person}
                                </p>
                            )}
                            {!client.is_business && client.occupation && (
                                <p className="text-indigo-100 mt-1">{client.occupation}</p>
                            )}
                        </div>
                    </div>

                    <div className="flex items-center space-x-3 ml-auto">
                        <button 
                            onClick={handleEdit}
                            className="p-2 bg-white/20 hover:bg-white/30 text-white rounded-full transition-colors"
                            aria-label="Edit Client"
                        >
                            <Edit className="w-5 h-5" />
                        </button>
                        <button 
                            onClick={handleDelete}
                            className="p-2 bg-white/20 hover:bg-red-500 text-white rounded-full transition-colors"
                            aria-label="Delete Client"
                        >
                            <Trash2 className="w-5 h-5" />
                        </button>
                    </div>
                </div>

                <div className="p-8 grid grid-cols-1 md:grid-cols-2 gap-8">
                    <div>
                        <h2 className="text-lg font-bold text-gray-800 mb-4 border-b border-gray-100 pb-2">Contact Information</h2>
                        <div className="space-y-4">
                            <div className="flex items-start">
                                <Phone className="w-5 h-5 text-gray-400 mr-3 mt-0.5" />
                                <div>
                                    <label className="block text-xs font-medium text-gray-500 uppercase">Phone</label>
                                    <p className="text-gray-800">{client.phone || 'N/A'}</p>
                                </div>
                            </div>
                            <div className="flex items-start">
                                <MapPin className="w-5 h-5 text-gray-400 mr-3 mt-0.5" />
                                <div>
                                    <label className="block text-xs font-medium text-gray-500 uppercase">Address</label>
                                    <p className="text-gray-800">{client.address || 'N/A'}</p>
                                </div>
                            </div>
                            <div className="flex items-start">
                                <Mail className="w-5 h-5 text-gray-400 mr-3 mt-0.5" />
                                <div>
                                    <label className="block text-xs font-medium text-gray-500 uppercase">Email</label>
                                    <p className="text-gray-800">client@{client.is_business ? 'business.com' : 'email.com'}</p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div>
                        <h2 className="text-lg font-bold text-gray-800 mb-4 border-b border-gray-100 pb-2">Stats</h2>
                        <div className="grid grid-cols-2 gap-4">
                            <div className="bg-indigo-50 p-4 rounded-lg text-center">
                                <span className="block text-2xl font-bold text-indigo-600">{client.patients?.length || 0}</span>
                                <span className="text-sm text-gray-600">Total Pets</span>
                            </div>
                            <div className="bg-green-50 p-4 rounded-lg text-center">
                                <span className="block text-2xl font-bold text-green-600">Active</span>
                                <span className="text-sm text-gray-600">Status</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div className="mb-6">
                <h2 className="text-xl font-bold text-gray-800 mb-4 flex items-center">
                    <PawPrint className="w-6 h-6 mr-2 text-indigo-500" />
                    Pets Owned
                </h2>
                
                {client.patients && client.patients.length > 0 ? (
                    <div className="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
                        {client.patients.map(patient => (
                            <Card 
                                key={patient.id}
                                type="patient" 
                                data={patient} 
                            />
                        ))}
                    </div>
                ) : (
                    <p className="text-gray-500 italic">No pets registered yet.</p>
                )}
            </div>
        </motion.div>
    );
};

export default ClientDetail;
