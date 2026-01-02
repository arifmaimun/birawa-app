import React, { useState, useEffect } from 'react';
import { motion, Variants } from 'framer-motion';
import Card from '../components/ui/Card';
import { getPatients, getClients } from '../services/api';
import { Patient, Client } from '../types/models';
import { Search, Loader2 } from 'lucide-react';

const Dashboard: React.FC = () => {
    const [searchTerm, setSearchTerm] = useState('');
    const [patients, setPatients] = useState<Patient[]>([]);
    const [clients, setClients] = useState<Client[]>([]);
    const [loading, setLoading] = useState(true);

    useEffect(() => {
        const fetchData = async () => {
            setLoading(true);
            try {
                // Fetch both concurrently
                // In a real app, you might want to fetch only what's visible or use SWR/React Query
                const [pData, cData] = await Promise.all([
                    getPatients(1, searchTerm),
                    getClients(1, searchTerm)
                ]);
                setPatients(pData.data);
                setClients(cData.data);
            } catch (error) {
                console.error("Failed to fetch data", error);
            } finally {
                setLoading(false);
            }
        };

        const timeoutId = setTimeout(() => {
            fetchData();
        }, 300);

        return () => clearTimeout(timeoutId);
    }, [searchTerm]);

    const containerVariants: Variants = {
        hidden: { opacity: 0 },
        visible: { 
            opacity: 1,
            transition: { 
                staggerChildren: 0.1 
            }
        }
    };

    const itemVariants: Variants = {
        hidden: { y: 20, opacity: 0 },
        visible: { 
            y: 0, 
            opacity: 1,
            transition: {
                type: 'spring',
                stiffness: 100
            }
        }
    };

    return (
        <div className="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
            <div className="mb-8">
                <h1 className="text-3xl font-bold text-gray-900 mb-4">Birawa Vet Directory</h1>
                <div className="relative max-w-md">
                    <div className="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                        <Search className="h-5 w-5 text-gray-400" />
                    </div>
                    <input
                        type="text"
                        className="block w-full pl-10 pr-3 py-2 border border-gray-300 rounded-md leading-5 bg-white placeholder-gray-500 focus:outline-none focus:placeholder-gray-400 focus:ring-1 focus:ring-blue-500 focus:border-blue-500 sm:text-sm transition duration-150 ease-in-out"
                        placeholder="Search patients or clients..."
                        value={searchTerm}
                        onChange={(e) => setSearchTerm(e.target.value)}
                    />
                    {loading && (
                        <div className="absolute inset-y-0 right-0 pr-3 flex items-center pointer-events-none">
                            <Loader2 className="h-4 w-4 text-blue-500 animate-spin" />
                        </div>
                    )}
                </div>
            </div>

            <div className="space-y-12">
                {/* Patients Section */}
                <section>
                    <div className="flex items-center justify-between mb-4">
                        <h2 className="text-xl font-semibold text-gray-800">Recent Patients</h2>
                        <span className="text-sm text-gray-500">{patients.length} records found</span>
                    </div>
                    
                    {patients.length > 0 ? (
                        <motion.div 
                            className="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6"
                            variants={containerVariants}
                            initial="hidden"
                            animate="visible"
                        >
                            {patients.map(patient => (
                                <motion.div key={`p-${patient.id}`} variants={itemVariants}>
                                    <Card 
                                        type="patient" 
                                        data={patient} 
                                    />
                                </motion.div>
                            ))}
                        </motion.div>
                    ) : (
                        <div className="text-center py-10 bg-gray-50 rounded-lg border border-dashed border-gray-300">
                            <p className="text-gray-500">{loading ? 'Searching...' : 'No patients found matching your search.'}</p>
                        </div>
                    )}
                </section>

                {/* Clients Section */}
                <section>
                    <div className="flex items-center justify-between mb-4">
                        <h2 className="text-xl font-semibold text-gray-800">Active Clients</h2>
                        <span className="text-sm text-gray-500">{clients.length} records found</span>
                    </div>
                    
                    {clients.length > 0 ? (
                        <motion.div 
                            className="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6"
                            variants={containerVariants}
                            initial="hidden"
                            animate="visible"
                        >
                            {clients.map(client => (
                                <motion.div key={`c-${client.id}`} variants={itemVariants}>
                                    <Card 
                                        type="client" 
                                        data={client} 
                                    />
                                </motion.div>
                            ))}
                        </motion.div>
                    ) : (
                        <div className="text-center py-10 bg-gray-50 rounded-lg border border-dashed border-gray-300">
                            <p className="text-gray-500">{loading ? 'Searching...' : 'No clients found matching your search.'}</p>
                        </div>
                    )}
                </section>
            </div>
        </div>
    );
};

export default Dashboard;
