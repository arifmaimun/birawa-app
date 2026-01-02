import { Patient, Client } from '../types/models';

export const mockPatients: Patient[] = [
    {
        id: 1,
        client_id: 1,
        name: "Luna",
        species: "Cat",
        breed: "Persian",
        gender: "Female",
        dob: "2020-05-15",
        is_sterile: true,
        allergies: "Seafood",
        special_conditions: "Sensitive stomach",
        medical_records: [
            { id: 101, patient_id: 1, diagnosis: "Gastroenteritis", treatment: "Fluid therapy, Antibiotics", created_at: "2023-10-10" },
            { id: 102, patient_id: 1, diagnosis: "Annual Vaccination", treatment: "F4 Vaccine", created_at: "2022-10-10" }
        ]
    },
    {
        id: 2,
        client_id: 1,
        name: "Max",
        species: "Dog",
        breed: "Golden Retriever",
        gender: "Male",
        dob: "2019-02-20",
        is_sterile: false,
        medical_records: [
            { id: 103, patient_id: 2, diagnosis: "Skin Infection", treatment: "Medicated Shampoo", created_at: "2023-11-05" }
        ]
    },
    {
        id: 3,
        client_id: 2,
        name: "Coco",
        species: "Bird",
        breed: "Parrot",
        gender: "Female",
        dob: "2021-08-01",
        is_sterile: false
    }
];

export const mockClients: Client[] = [
    {
        id: 1,
        name: "John Doe",
        first_name: "John",
        last_name: "Doe",
        phone: "08123456789",
        address: "Jl. Sudirman No. 123, Jakarta",
        is_business: false,
        gender: "Male",
        occupation: "Engineer",
        patients: [mockPatients[0], mockPatients[1]]
    },
    {
        id: 2,
        name: "Pet Paradise Ltd",
        is_business: true,
        business_name: "Pet Paradise Ltd",
        contact_person: "Sarah Smith",
        phone: "021-5555555",
        address: "Jl. Kemang Raya No. 45, Jakarta",
        patients: [mockPatients[2]]
    }
];
