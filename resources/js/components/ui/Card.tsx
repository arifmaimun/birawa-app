import React, { useState } from 'react';
import { Patient, Client } from '../../types/models';
import { useNavigate } from 'react-router-dom';
import { User, Phone, Calendar, Stethoscope, Briefcase, MapPin, PawPrint } from 'lucide-react';
import { calculateAge } from '../../utils/formatters';

interface CardProps {
    type: 'patient' | 'client';
    data: Patient | Client;
    className?: string;
}

const Card: React.FC<CardProps> = ({ type, data, className = '' }) => {
    const navigate = useNavigate();
    const [imageError, setImageError] = useState(false);

    const isPatient = type === 'patient';
    const patient = isPatient ? (data as Patient) : null;
    const client = !isPatient ? (data as Client) : null;

    const handleClick = () => {
        if (type === 'patient') {
            navigate(`/patients/${data.id}`);
        } else {
            navigate(`/clients/${data.id}`);
        }
    };

    const handleKeyDown = (e: React.KeyboardEvent) => {
        if (e.key === 'Enter' || e.key === ' ') {
            e.preventDefault();
            handleClick();
        }
    };

    // Data derivation
    const title = isPatient ? patient?.name : (client?.is_business ? client?.business_name : client?.name);
    const subtitle = isPatient 
        ? `${patient?.species} - ${patient?.breed}` 
        : (client?.is_business ? `PIC: ${client?.contact_person}` : client?.occupation || 'Individual');
    
    const metaInfo = isPatient 
        ? `${calculateAge(patient?.dob || '')} • ${patient?.gender}`
        : client?.phone || 'No Phone';

    const avatarSrc = data.avatar_url;
    
    // Status color indicator (example logic)
    const statusColor = isPatient 
        ? (patient?.is_sterile ? 'bg-green-500' : 'bg-blue-500') 
        : (client?.is_business ? 'bg-purple-500' : 'bg-indigo-500');

    return (
        <div
            className={`
                group relative bg-white rounded-xl shadow-sm border border-gray-100 
                overflow-hidden cursor-pointer transition-all duration-300 ease-out
                hover:shadow-lg hover:scale-[1.02] hover:border-blue-200
                focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2
                ${className}
            `}
            role="button"
            tabIndex={0}
            onClick={handleClick}
            onKeyDown={handleKeyDown}
            aria-label={`View details for ${title}`}
        >
            <div className="p-5 flex items-start space-x-4">
                {/* Avatar Section */}
                <div className="relative shrink-0">
                    <div className="w-16 h-16 rounded-full overflow-hidden bg-gray-100 ring-2 ring-white shadow-sm flex items-center justify-center">
                        {avatarSrc && !imageError ? (
                            <img 
                                src={avatarSrc} 
                                alt={title} 
                                className="w-full h-full object-cover transition-transform duration-500 group-hover:scale-110"
                                onError={() => setImageError(true)}
                                loading="lazy"
                            />
                        ) : (
                            <span className="text-xl font-bold text-gray-400">
                                {isPatient ? <PawPrint className="w-8 h-8 text-gray-300" /> : <User className="w-8 h-8 text-gray-300" />}
                            </span>
                        )}
                    </div>
                    {/* Status Indicator */}
                    <div className={`absolute bottom-0 right-0 w-4 h-4 ${statusColor} border-2 border-white rounded-full shadow-sm`}></div>
                </div>

                {/* Content Section */}
                <div className="flex-1 min-w-0">
                    <div className="flex justify-between items-start">
                        <div>
                            <h3 className="text-lg font-semibold text-gray-900 truncate group-hover:text-blue-600 transition-colors">
                                {title}
                            </h3>
                            <p className="text-sm text-gray-500 truncate flex items-center mt-1">
                                {isPatient ? null : <Briefcase className="w-3 h-3 mr-1" />}
                                {subtitle}
                            </p>
                        </div>
                    </div>

                    <div className="mt-3 flex items-center text-sm text-gray-500">
                        {isPatient ? (
                            <>
                                <Calendar className="w-4 h-4 mr-1.5 text-gray-400" />
                                {metaInfo}
                            </>
                        ) : (
                            <>
                                <Phone className="w-4 h-4 mr-1.5 text-gray-400" />
                                {metaInfo}
                            </>
                        )}
                    </div>

                    {/* Tags / Badges */}
                    <div className="mt-4 flex flex-wrap gap-2">
                         {isPatient && patient?.medical_records && patient.medical_records.length > 0 && (
                             <span className="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-50 text-blue-700">
                                 <Stethoscope className="w-3 h-3 mr-1" />
                                 Latest: {patient.medical_records[0].diagnosis}
                             </span>
                         )}
                         {!isPatient && client?.address && (
                             <span className="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-700 truncate max-w-[150px]">
                                 <MapPin className="w-3 h-3 mr-1" />
                                 {client.address}
                             </span>
                         )}
                    </div>
                </div>
                
                {/* Arrow Icon */}
                <div className="self-center">
                     <div className={`
                        w-8 h-8 rounded-full bg-gray-50 flex items-center justify-center 
                        text-gray-400 transition-all duration-300
                        group-hover:bg-blue-50 group-hover:text-blue-600 group-hover:translate-x-1
                     `}>
                        <svg className="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path strokeLinecap="round" strokeLinejoin="round" strokeWidth="2" d="M9 5l7 7-7 7" />
                        </svg>
                     </div>
                </div>
            </div>
        </div>
    );
};

export default Card;
