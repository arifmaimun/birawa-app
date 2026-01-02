export interface Patient {
    id: number;
    client_id: number;
    name: string;
    species: string;
    breed: string;
    gender: string;
    dob: string;
    is_sterile: boolean;
    allergies?: string;
    special_conditions?: string;
    vaccination_history?: string;
    avatar_url?: string; // Derived or placeholder
    medical_records?: MedicalRecord[]; // Basic info for card
    client?: Client;
}

export interface Client {
    id: number;
    user_id?: number;
    name: string;
    first_name?: string;
    last_name?: string;
    address?: string;
    phone?: string;
    is_business: boolean;
    business_name?: string;
    contact_person?: string;
    gender?: string;
    dob?: string;
    occupation?: string;
    avatar_url?: string; // Derived or placeholder
    patients?: Patient[];
}

export interface MedicalRecord {
    id: number;
    patient_id: number;
    visit_id?: number;
    diagnosis?: string; // Legacy support or flattened
    treatment?: string; // Legacy support or flattened
    plan_treatment?: string; // Real DB column
    subjective?: string;
    objective?: string;
    assessment?: string;
    plan_diagnostic?: string;
    diagnoses?: { name: string; code: string }[]; // Relation
    created_at: string;
    doctor?: User;
}

export interface User {
    id: number;
    name: string;
    email: string;
    avatar_url?: string;
}

export interface Visit {
    id: number;
    patient_id: number;
    user_id: number; // Doctor ID
    scheduled_at: string;
    status: string;
    notes?: string;
    patient?: Patient;
    doctor?: User; // Relation to User
    medical_records?: MedicalRecord[];
    distance_km?: number;
    actual_travel_minutes?: number;
    predicted_travel_minutes?: number;
}

export type CardType = 'patient' | 'client';

export interface CardProps {
    type: CardType;
    data: Patient | Client;
    onClick?: (id: number) => void;
    className?: string;
}
