import { render, screen, fireEvent } from '@testing-library/react';
import { describe, it, expect, vi } from 'vitest';
import Card from '../components/ui/Card';
import { Patient, Client } from '../types/models';
import { MemoryRouter } from 'react-router-dom';

const mockPatient: Patient = {
    id: 1,
    client_id: 1,
    name: 'Luna',
    species: 'Cat',
    breed: 'Persian',
    gender: 'Female',
    dob: '2020-05-15',
    is_sterile: true,
    medical_records: [
        { id: 1, patient_id: 1, diagnosis: 'Checkup', created_at: '2023-01-01' }
    ]
};

const mockClient: Client = {
    id: 1,
    name: 'John Doe',
    is_business: false,
    phone: '08123456789'
};

// Mock useNavigate
const mockNavigate = vi.fn();
vi.mock('react-router-dom', async () => {
    const actual = await vi.importActual('react-router-dom');
    return {
        ...actual,
        useNavigate: () => mockNavigate,
    };
});

describe('Card Component', () => {
    it('renders patient data correctly', () => {
        render(
            <MemoryRouter>
                <Card type="patient" data={mockPatient} />
            </MemoryRouter>
        );
        
        expect(screen.getByText('Luna')).toBeInTheDocument();
        expect(screen.getByText(/Cat - Persian/i)).toBeInTheDocument();
        expect(screen.getByText(/Checkup/i)).toBeInTheDocument();
    });

    it('renders client data correctly', () => {
        render(
            <MemoryRouter>
                <Card type="client" data={mockClient} />
            </MemoryRouter>
        );
        
        expect(screen.getByText('John Doe')).toBeInTheDocument();
        expect(screen.getByText('08123456789')).toBeInTheDocument();
    });

    it('navigates when clicked', () => {
        render(
            <MemoryRouter>
                <Card type="patient" data={mockPatient} />
            </MemoryRouter>
        );
        
        fireEvent.click(screen.getByRole('button'));
        expect(mockNavigate).toHaveBeenCalledWith('/patients/1');
    });

    it('handles keyboard navigation (Enter key)', () => {
        render(
            <MemoryRouter>
                <Card type="patient" data={mockPatient} />
            </MemoryRouter>
        );
        
        const card = screen.getByRole('button');
        card.focus();
        fireEvent.keyDown(card, { key: 'Enter' });
        
        expect(mockNavigate).toHaveBeenCalledWith('/patients/1');
    });
});
