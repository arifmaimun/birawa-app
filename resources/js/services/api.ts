import axios from 'axios';
import { Patient, Client, Visit, MedicalRecord, User } from '../types/models';

// Configure Axios to include credentials and CSRF token if needed
// Laravel Sanctum/Web middleware usually handles CSRF via cookie
const api = axios.create({
    baseURL: '/api',
    headers: {
        'X-Requested-With': 'XMLHttpRequest',
        'Accept': 'application/json',
    },
    withCredentials: true,
});

// Add response interceptor for better error logging
api.interceptors.response.use(
    response => response,
    error => {
        if (error.response) {
            console.error('API Error:', error.response.status, error.response.data);
            // Check for authentication issues
            if (error.response.status === 401 || error.response.status === 419) {
                 console.warn('Authentication/CSRF issue detected. Session might be expired.');
                 // Optional: redirect to login or refresh token
            }
        } else if (error.request) {
            console.error('API No Response:', error.request);
        } else {
            console.error('API Request Config Error:', error.message);
        }
        return Promise.reject(error);
    }
);

export interface PaginatedResponse<T> {
    data: T[];
    current_page: number;
    last_page: number;
    total: number;
    per_page: number;
}

export const getPatients = async (page = 1, search = ''): Promise<PaginatedResponse<Patient>> => {
    const response = await api.get(`/patients`, {
        params: { page, search }
    });
    return response.data;
};

export const getPatient = async (id: string | number): Promise<Patient> => {
    const response = await api.get(`/patients/${id}`);
    return response.data;
};

export const getClients = async (page = 1, search = ''): Promise<PaginatedResponse<Client>> => {
    const response = await api.get(`/clients`, {
        params: { page, search }
    });
    return response.data;
};

export const getClient = async (id: string | number): Promise<Client> => {
    const response = await api.get(`/clients/${id}`);
    return response.data;
};

export const deletePatient = async (id: string | number): Promise<void> => {
    // Mock for now as destroy might not be API ready or risky
    // await api.delete(`/patients/${id}`);
    console.log(`Deleting patient ${id}`);
};

export const deleteClient = async (id: string | number): Promise<void> => {
    // await api.delete(`/clients/${id}`);
    console.log(`Deleting client ${id}`);
};

// Visits
export const getVisit = async (id: string | number): Promise<Visit> => {
    // In our hybrid app, sometimes we might need to hit the web route or api route.
    // Assuming we enabled API route or the controller handles JSON negotiation.
    // The controller is likely under /visits (web) but we can prefix /api if we set it up there.
    // Let's assume we use the web route that returns JSON for now, or the API route if defined.
    // Based on previous reads, Visits resource is in web.php.
    // Let's try /visits/{id} but ensuring Accept: application/json header is sent (which it is by default in our instance).
    // Note: Our baseURL is /api. But visits are in web.php.
    // We might need to override baseURL for this call or move visits to api.php.
    // For now, let's use absolute path to override baseURL.
    const response = await api.get(`/visits/${id}`, { baseURL: '/' }); 
    return response.data;
};

export const updateVisitStatus = async (id: number, status: string, data?: { distance_km?: number; actual_travel_minutes?: number }): Promise<Visit> => {
    const payload = { status, ...data };
    const response = await api.patch(`/visits/${id}/status`, payload, { baseURL: '/' });
    return response.data;
};

// Medical Records
export const createMedicalRecord = async (visitId: number, data: Partial<MedicalRecord>): Promise<MedicalRecord> => {
    const response = await api.post(`/visits/${visitId}/medical-record`, data, { baseURL: '/' });
    return response.data;
};

export const requestAccess = async (medicalRecordId: number): Promise<any> => {
    const response = await api.post(`/medical-records/${medicalRecordId}/request-access`, {}, { baseURL: '/' });
    return response.data;
};

// Social Features
export const getFriends = async () => {
    const response = await api.get('/friendships', { baseURL: '/' });
    return response.data;
};

export const sendFriendRequest = async (friendId: number) => {
    const response = await api.post('/friendships', { friend_id: friendId }, { baseURL: '/' });
    return response.data;
};

export const respondFriendRequest = async (friendshipId: number, status: 'accepted' | 'blocked') => {
    if (status === 'accepted') {
        const response = await api.patch(`/friendships/${friendshipId}/accept`, {}, { baseURL: '/' });
        return response.data;
    }
    // Block logic not implemented yet or different endpoint
    return null; 
};

export const getMessages = async (userId: number) => {
    const response = await api.get(`/messages/${userId}`, { baseURL: '/' });
    return response.data;
};

export const sendMessage = async (receiverId: number, message: string) => {
    const response = await api.post('/messages', { receiver_id: receiverId, message }, { baseURL: '/' });
    return response.data;
};

export const getConversations = async () => {
    const response = await api.get('/messages/conversations', { baseURL: '/' });
    return response.data;
};
