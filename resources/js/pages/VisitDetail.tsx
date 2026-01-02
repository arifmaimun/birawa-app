import React, { useState, useEffect } from 'react';
import { useParams, useNavigate } from 'react-router-dom';
import { Visit, User } from '../types/models';
import { getVisit, createMedicalRecord, requestAccess, getFriends, sendFriendRequest, getMessages, sendMessage, updateVisitStatus } from '../services/api';
import { User as UserIcon, Calendar, Send, Shield, MessageSquare, ArrowLeft, Save, Plus, UserPlus } from 'lucide-react';
import { format } from 'date-fns';

const VisitDetail: React.FC = () => {
    const { id } = useParams<{ id: string }>();
    const navigate = useNavigate();
    const [visit, setVisit] = useState<Visit | null>(null);
    const [loading, setLoading] = useState(true);
    const [error, setError] = useState<string | null>(null);
    const [activeTab, setActiveTab] = useState<'soap' | 'history'>('soap');
    
    // SOAP Form State
    const [soapForm, setSoapForm] = useState({
        subjective: '',
        objective: '',
        assessment: '',
        plan_diagnostic: '',
        plan_treatment: '',
    });
    const [saving, setSaving] = useState(false);

    // Social & History State
    const [historyAccess, setHistoryAccess] = useState<{[key: number]: boolean}>({}); // visit_id -> has_access
    const [friends, setFriends] = useState<User[]>([]);
    const [messages, setMessages] = useState<any[]>([]);
    const [messageInput, setMessageInput] = useState('');
    const [showChat, setShowChat] = useState<User | null>(null);

    useEffect(() => {
        const fetchData = async () => {
            if (!id) return;
            try {
                const visitData = await getVisit(id);
                setVisit(visitData);
                
                try {
                    const friendsData = await getFriends();
                    setFriends(friendsData.friends || []);
                } catch (e) {
                    console.warn("Failed to load friends", e);
                }
            } catch (err: any) {
                console.error("Error fetching visit:", err);
                setError(err.message || 'Failed to load visit details');
            } finally {
                setLoading(false);
            }
        };

        fetchData();
    }, [id]);

    useEffect(() => {
        if (showChat) {
            const fetchMessages = async () => {
                try {
                    const msgs = await getMessages(showChat.id);
                    setMessages(msgs);
                } catch (err) {
                    console.error("Failed to load messages", err);
                }
            };
            fetchMessages();
            // Poll for new messages every 5 seconds
            const interval = setInterval(fetchMessages, 5000);
            return () => clearInterval(interval);
        }
    }, [showChat]);

    const handleSoapChange = (e: React.ChangeEvent<HTMLTextAreaElement>) => {
        setSoapForm(prev => ({ ...prev, [e.target.name]: e.target.value }));
    };

    const handleSaveSoap = async () => {
        if (!visit) return;
        setSaving(true);
        try {
            await createMedicalRecord(visit.id, soapForm);
            alert('Medical record saved successfully!');
            // Refresh data
            const updatedVisit = await getVisit(visit.id);
            setVisit(updatedVisit);
        } catch (err: any) {
            alert('Failed to save record: ' + err.message);
        } finally {
            setSaving(false);
        }
    };

    const handleRequestAccess = async (recordId: number) => {
        try {
            await requestAccess(recordId);
            setHistoryAccess(prev => ({ ...prev, [recordId]: true }));
            alert('Access request sent to the doctor.');
        } catch (err: any) {
            alert('Failed to request access: ' + err.message);
        }
    };

    const handleAddFriend = async (friendId: number) => {
        try {
            await sendFriendRequest(friendId);
            alert('Friend request sent!');
            // Refresh friends list
            const friendsData = await getFriends();
            setFriends(friendsData.friends || []);
        } catch (err: any) {
            alert('Failed to send friend request: ' + err.message);
        }
    };

    const handleStatusUpdate = async (newStatus: string) => {
        if (!visit) return;
        try {
            const data = {
                distance_km: visit.distance_km,
                actual_travel_minutes: visit.actual_travel_minutes
            };
            const updated = await updateVisitStatus(visit.id, newStatus, data);
            setVisit(updated);
        } catch (err: any) {
            alert('Failed to update status');
        }
    };

    const handleSendMessage = async () => {
        if (!messageInput.trim() || !showChat) return;
        try {
            await sendMessage(showChat.id, messageInput);
            setMessageInput('');
            // Refresh messages
            const msgs = await getMessages(showChat.id);
            setMessages(msgs);
        } catch (err: any) {
            alert('Failed to send message: ' + err.message);
        }
    };

    if (loading) return <div className="p-8 flex justify-center"><div className="animate-spin rounded-full h-12 w-12 border-b-2 border-blue-600"></div></div>;
    if (error || !visit) return <div className="p-8 text-center text-red-600">Error: {error || 'Visit not found'}</div>;

    const patient = visit.patient;

    return (
        <div className="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
            {/* Header */}
            <div className="mb-8">
                <button onClick={() => navigate(-1)} className="flex items-center text-gray-500 hover:text-gray-700 mb-4">
                    <ArrowLeft className="w-4 h-4 mr-1" /> Back
                </button>
                <div className="flex justify-between items-start">
                    <div>
                        <h1 className="text-3xl font-bold text-gray-900">Visit Details</h1>
                        <div className="flex items-center mt-2 text-gray-600">
                            <Calendar className="w-4 h-4 mr-2" />
                            <span>{format(new Date(visit.scheduled_at), 'PPP p')}</span>
                            <span className={`ml-4 px-3 py-1 rounded-full text-xs font-medium ${
                                visit.status === 'completed' ? 'bg-green-100 text-green-800' : 
                                visit.status === 'otw' ? 'bg-yellow-100 text-yellow-800' :
                                visit.status === 'scheduled' ? 'bg-blue-100 text-blue-800' : 'bg-gray-100 text-gray-800'
                            }`}>
                                {visit.status === 'otw' ? 'On The Way' : visit.status}
                            </span>
                        </div>
                    </div>
                    
                    {/* Status Actions */}
                    <div className="flex flex-col items-end space-y-2">
                        <div className="flex space-x-2">
                            {visit.status === 'scheduled' && (
                                <button 
                                    onClick={() => handleStatusUpdate('otw')}
                                    className="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700"
                                >
                                    Start Visit
                                </button>
                            )}
                            {visit.status === 'otw' && (
                                <button 
                                    onClick={() => handleStatusUpdate('arrived')}
                                    className="bg-yellow-500 text-white px-4 py-2 rounded-lg hover:bg-yellow-600"
                                >
                                    Arrived
                                </button>
                            )}
                             {(visit.status === 'arrived' || visit.status === 'otw') && (
                                <button 
                                    onClick={() => handleStatusUpdate('completed')}
                                    className="bg-green-600 text-white px-4 py-2 rounded-lg hover:bg-green-700"
                                >
                                    Complete Visit
                                </button>
                            )}
                        </div>
                        
                        {/* Trip Data Inputs */}
                            {visit.predicted_travel_minutes && !visit.actual_travel_minutes && (
                                <div className="text-xs text-blue-600 mb-2">
                                    Predicted time: ~{visit.predicted_travel_minutes} mins
                                </div>
                            )}
                            {(visit.status === 'otw' || visit.status === 'arrived' || visit.status === 'completed') && (
                            <div className="flex space-x-2 bg-gray-50 p-2 rounded-lg border border-gray-200">
                                <div>
                                    <label className="block text-xs font-medium text-gray-500">Distance (km)</label>
                                    <input 
                                        type="number" 
                                        className="w-20 text-sm border-gray-300 rounded focus:ring-blue-500 focus:border-blue-500"
                                        placeholder="0.0"
                                        defaultValue={visit.distance_km || ''}
                                        onBlur={(e) => {
                                            if (visit.status !== 'completed' && e.target.value) {
                                                // Trigger update to save distance if not completed
                                                 handleStatusUpdate(visit.status);
                                            }
                                        }}
                                        onChange={(e) => {
                                            setVisit({...visit, distance_km: parseFloat(e.target.value)});
                                        }}
                                    />
                                </div>
                                <div>
                                    <label className="block text-xs font-medium text-gray-500">Time (min)</label>
                                    <input 
                                        type="number" 
                                        className="w-20 text-sm border-gray-300 rounded focus:ring-blue-500 focus:border-blue-500"
                                        placeholder="0"
                                        defaultValue={visit.actual_travel_minutes || ''}
                                        onBlur={(e) => {
                                            if (visit.status !== 'completed' && e.target.value) {
                                                handleStatusUpdate(visit.status);
                                            }
                                        }}
                                        onChange={(e) => {
                                            setVisit({...visit, actual_travel_minutes: parseInt(e.target.value)});
                                        }}
                                    />
                                </div>
                            </div>
                        )}
                    </div>
                </div>
            </div>

            <div className="grid grid-cols-1 lg:grid-cols-3 gap-8">
                {/* Left Column: Patient Info & Context */}
                <div className="space-y-6">
                    {/* Patient Card */}
                    <div className="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
                        <div className="flex items-center space-x-4 mb-4">
                            <div className="w-16 h-16 rounded-full bg-gray-100 flex items-center justify-center">
                                {patient?.avatar_url ? (
                                    <img src={patient.avatar_url} alt={patient.name} className="w-full h-full rounded-full object-cover" />
                                ) : (
                                    <span className="text-xl font-bold text-gray-400">{patient?.name?.charAt(0)}</span>
                                )}
                            </div>
                            <div>
                                <h2 className="text-xl font-bold text-gray-900">{patient?.name}</h2>
                                <p className="text-gray-500">{patient?.species} - {patient?.breed}</p>
                            </div>
                        </div>
                        <div className="space-y-3 text-sm">
                            <div className="flex justify-between">
                                <span className="text-gray-500">Gender</span>
                                <span className="font-medium">{patient?.gender}</span>
                            </div>
                            <div className="flex justify-between">
                                <span className="text-gray-500">Age</span>
                                <span className="font-medium">{patient?.dob}</span>
                            </div>
                             <div className="flex justify-between">
                                <span className="text-gray-500">Owner</span>
                                <span className="font-medium">{patient?.client?.name}</span>
                            </div>
                        </div>
                    </div>

                     {/* Previous Visits (Simplified History List) */}
                    <div className="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
                        <h3 className="text-lg font-semibold mb-4">History</h3>
                        <div className="space-y-4">
                            {/* This would map over patient's medical history */}
                            {patient?.medical_records?.map(record => (
                                <div key={record.id} className="border-b border-gray-100 pb-3 last:border-0 last:pb-0">
                                    <div className="flex justify-between items-start mb-1">
                                        <span className="font-medium text-gray-900">{format(new Date(record.created_at), 'MMM d, yyyy')}</span>
                                        {/* Logic to check access: if record.doctor_id !== current_user_id */}
                                        {/* For demo, assume we have access if data is present, else show Request button */}
                                        {record.diagnosis ? (
                                            <span className="text-xs bg-green-100 text-green-800 px-2 py-0.5 rounded">Accessible</span>
                                        ) : (
                                            <button 
                                                onClick={() => handleRequestAccess(record.id)}
                                                disabled={historyAccess[record.id]}
                                                className={`text-xs px-2 py-0.5 rounded flex items-center ${
                                                    historyAccess[record.id] 
                                                    ? 'bg-gray-100 text-gray-500 cursor-not-allowed' 
                                                    : 'bg-yellow-100 text-yellow-800 hover:bg-yellow-200'
                                                }`}
                                            >
                                                <Shield className="w-3 h-3 mr-1" /> {historyAccess[record.id] ? 'Requested' : 'Request Access'}
                                            </button>
                                        )}
                                    </div>
                                    <p className="text-sm text-gray-600 truncate">{record.diagnosis || 'Restricted Access'}</p>
                                    <div className="mt-2 flex items-center justify-between">
                                        <div className="flex items-center space-x-2">
                                            <span className="text-xs text-gray-500">Dr. {record.doctor?.name || 'Unknown'}</span>
                                            {record.doctor && record.doctor.id !== visit.user_id && (
                                                <div className="flex space-x-1">
                                                    {friends.some(f => f.id === record.doctor!.id) ? (
                                                        <button 
                                                            onClick={() => setShowChat(record.doctor!)}
                                                            className="text-blue-600 hover:text-blue-800 p-1"
                                                            title="Chat"
                                                        >
                                                            <MessageSquare className="w-4 h-4" />
                                                        </button>
                                                    ) : (
                                                        <button 
                                                            onClick={() => handleAddFriend(record.doctor!.id)}
                                                            className="text-green-600 hover:text-green-800 p-1"
                                                            title="Add Friend"
                                                        >
                                                            <UserPlus className="w-4 h-4" />
                                                        </button>
                                                    )}
                                                </div>
                                            )}
                                        </div>
                                    </div>
                                </div>
                            ))}
                            {(!patient?.medical_records || patient.medical_records.length === 0) && (
                                <p className="text-gray-500 text-sm italic">No history available.</p>
                            )}
                        </div>
                    </div>
                </div>

                {/* Right Column: SOAP & Actions */}
                <div className="lg:col-span-2 space-y-6">
                    <div className="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
                        <div className="border-b border-gray-100">
                            <nav className="flex -mb-px">
                                <button
                                    onClick={() => setActiveTab('soap')}
                                    className={`py-4 px-6 font-medium text-sm border-b-2 ${
                                        activeTab === 'soap'
                                            ? 'border-blue-500 text-blue-600'
                                            : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300'
                                    }`}
                                >
                                    Medical Record (SOAP)
                                </button>
                                <button
                                    onClick={() => setActiveTab('history')}
                                    className={`py-4 px-6 font-medium text-sm border-b-2 ${
                                        activeTab === 'history'
                                            ? 'border-blue-500 text-blue-600'
                                            : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300'
                                    }`}
                                >
                                    Full History
                                </button>
                            </nav>
                        </div>

                        <div className="p-6">
                            {activeTab === 'soap' && (
                                <div className="space-y-6">
                                    <div>
                                        <label className="block text-sm font-medium text-gray-700 mb-1">Subjective (S)</label>
                                        <textarea
                                            name="subjective"
                                            value={soapForm.subjective}
                                            onChange={handleSoapChange}
                                            rows={3}
                                            className="w-full rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"
                                            placeholder="Client complaints, history of present illness..."
                                        />
                                    </div>
                                    <div>
                                        <label className="block text-sm font-medium text-gray-700 mb-1">Objective (O)</label>
                                        <textarea
                                            name="objective"
                                            value={soapForm.objective}
                                            onChange={handleSoapChange}
                                            rows={3}
                                            className="w-full rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"
                                            placeholder="Physical exam findings, vital signs..."
                                        />
                                    </div>
                                    <div>
                                        <label className="block text-sm font-medium text-gray-700 mb-1">Assessment (A)</label>
                                        <textarea
                                            name="assessment"
                                            value={soapForm.assessment}
                                            onChange={handleSoapChange}
                                            rows={3}
                                            className="w-full rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"
                                            placeholder="Diagnosis, differential diagnosis..."
                                        />
                                    </div>
                                    <div className="grid grid-cols-1 md:grid-cols-2 gap-4">
                                        <div>
                                            <label className="block text-sm font-medium text-gray-700 mb-1">Plan - Diagnostic</label>
                                            <textarea
                                                name="plan_diagnostic"
                                                value={soapForm.plan_diagnostic}
                                                onChange={handleSoapChange}
                                                rows={3}
                                                className="w-full rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"
                                                placeholder="Labs, imaging needed..."
                                            />
                                        </div>
                                        <div>
                                            <label className="block text-sm font-medium text-gray-700 mb-1">Plan - Treatment</label>
                                            <textarea
                                                name="plan_treatment"
                                                value={soapForm.plan_treatment}
                                                onChange={handleSoapChange}
                                                rows={3}
                                                className="w-full rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"
                                                placeholder="Medications, procedures, advice..."
                                            />
                                        </div>
                                    </div>

                                    <div className="flex justify-end pt-4">
                                        <button
                                            onClick={handleSaveSoap}
                                            disabled={saving}
                                            className="flex items-center px-6 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 disabled:opacity-50"
                                        >
                                            {saving ? 'Saving...' : <><Save className="w-4 h-4 mr-2" /> Save Record</>}
                                        </button>
                                    </div>
                                </div>
                            )}

                            {activeTab === 'history' && (
                                <div className="text-center py-12 text-gray-500">
                                    <p>Full history view coming soon...</p>
                                </div>
                            )}
                        </div>
                    </div>
                </div>
            </div>
            {/* Chat Modal */}
            {showChat && (
                <div className="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50 p-4">
                    <div className="bg-white rounded-xl shadow-xl w-full max-w-md flex flex-col h-[500px]">
                        <div className="p-4 border-b border-gray-100 flex justify-between items-center">
                            <h3 className="font-bold text-lg flex items-center">
                                <UserIcon className="w-5 h-5 mr-2 text-gray-500" />
                                Chat with {showChat.name}
                            </h3>
                            <button onClick={() => setShowChat(null)} className="text-gray-400 hover:text-gray-600">
                                <Plus className="w-6 h-6 transform rotate-45" />
                            </button>
                        </div>
                        
                        <div className="flex-1 overflow-y-auto p-4 space-y-4 bg-gray-50">
                            {messages.length === 0 ? (
                                <p className="text-center text-gray-400 text-sm py-8">No messages yet. Say hi!</p>
                            ) : (
                                messages.map((msg, idx) => (
                                    <div key={idx} className={`flex ${msg.sender_id === visit.user_id ? 'justify-end' : 'justify-start'}`}>
                                        <div className={`max-w-[80%] rounded-lg px-4 py-2 text-sm ${
                                            msg.sender_id === visit.user_id 
                                                ? 'bg-blue-600 text-white' 
                                                : 'bg-white border border-gray-200 text-gray-800'
                                        }`}>
                                            <p>{msg.body}</p>
                                            <p className={`text-xs mt-1 ${
                                                msg.sender_id === visit.user_id ? 'text-blue-100' : 'text-gray-400'
                                            }`}>
                                                {format(new Date(msg.created_at), 'p')}
                                            </p>
                                        </div>
                                    </div>
                                ))
                            )}
                        </div>

                        <div className="p-4 border-t border-gray-100 bg-white">
                            <div className="flex space-x-2">
                                <input
                                    type="text"
                                    value={messageInput}
                                    onChange={(e) => setMessageInput(e.target.value)}
                                    onKeyPress={(e) => e.key === 'Enter' && handleSendMessage()}
                                    placeholder="Type a message..."
                                    className="flex-1 rounded-lg border-gray-300 focus:border-blue-500 focus:ring-blue-500"
                                />
                                <button 
                                    onClick={handleSendMessage}
                                    disabled={!messageInput.trim()}
                                    className="bg-blue-600 text-white p-2 rounded-lg hover:bg-blue-700 disabled:opacity-50"
                                >
                                    <Send className="w-5 h-5" />
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            )}
        </div>
    );
};

export default VisitDetail;
