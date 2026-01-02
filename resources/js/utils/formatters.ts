import { differenceInYears, differenceInMonths } from 'date-fns';

export const calculateAge = (dob: string): string => {
    if (!dob) return 'N/A';
    const birthDate = new Date(dob);
    const now = new Date();
    
    const years = differenceInYears(now, birthDate);
    if (years > 0) return `${years} Tahun`;
    
    const months = differenceInMonths(now, birthDate);
    return `${months} Bulan`;
};

export const formatCurrency = (amount: number): string => {
    return new Intl.NumberFormat('id-ID', {
        style: 'currency',
        currency: 'IDR',
        minimumFractionDigits: 0
    }).format(amount);
};

export const getInitials = (name: string): string => {
    if (!name) return '?';
    return name
        .split(' ')
        .map(word => word[0])
        .join('')
        .toUpperCase()
        .slice(0, 2);
};
