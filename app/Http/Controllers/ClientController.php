<?php

namespace App\Http\Controllers;

use App\Models\Client;
use App\Models\Patient;
use App\Models\User;
use App\Models\FormOption;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;

class ClientController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $search = $request->input('search');

        $clients = Client::when($search, function ($query) use ($search) {
                $query->where(function($q) use ($search) {
                    $q->where('name', 'like', "%{$search}%")
                      ->orWhere('phone', 'like', "%{$search}%");
                });
            })
            ->withCount('patients')
            ->latest()
            ->paginate(10);

        if ($request->wantsJson() || $request->is('api/*')) {
            return response()->json($clients);
        }

        return view('clients.index', compact('clients'));
    }


    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $idTypes = FormOption::category('id_type')->pluck('value');
        if ($idTypes->isEmpty()) {
            $idTypes = collect(['KTP', 'Passport', 'SIM', 'KITAS']);
        }
        
        $ethnicities = FormOption::category('ethnicity')->pluck('value');
        $religions = FormOption::category('religion')->pluck('value');
        $maritalStatuses = FormOption::category('marital_status')->pluck('value');
        $addressTypes = FormOption::category('location_type')->pluck('value');
        $parkingTypes = FormOption::category('parking_type')->pluck('value');

        return view('clients.create', compact('idTypes', 'ethnicities', 'religions', 'maritalStatuses', 'addressTypes', 'parkingTypes'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            // Client Basic
            'is_business' => 'boolean',
            'business_name' => 'required_if:is_business,1|nullable|string|max:255',
            'contact_person' => 'required_if:is_business,1|nullable|string|max:255',
            'first_name' => 'nullable|string|max:255',
            'last_name' => 'required_unless:is_business,1|nullable|string|max:255',
            'phone' => 'required|string|max:20',
            'email' => 'nullable|email|max:255',
            
            // Client Details
            'id_type' => 'nullable|string|max:50',
            'id_number' => 'nullable|string|max:50',
            'gender' => 'nullable|string|in:Laki-laki,Perempuan',
            'occupation' => 'nullable|string|max:100',
            'dob' => 'nullable|date',
            'ethnicity' => 'nullable|string|max:50',
            'religion' => 'nullable|string|max:50',
            'marital_status' => 'nullable|string|max:50',

            // Addresses
            'addresses' => 'required|array|min:1',
            'addresses.*.street' => 'required|string',
            'addresses.*.additional_info' => 'nullable|string',
            'addresses.*.city' => 'nullable|string',
            'addresses.*.province' => 'nullable|string',
            'addresses.*.postal_code' => 'nullable|string',
            'addresses.*.country' => 'nullable|string',
            'addresses.*.parking_type' => 'nullable|string',
            'addresses.*.address_type' => 'nullable|string',
            
            // Patient Section
            'patient_name' => 'required|string|max:255',
            'patient_species' => 'required|string|max:255',
            'patient_breed' => 'nullable|string|max:255',
            'patient_dob' => 'nullable|date',
            'patient_gender' => 'required|in:Jantan,Betina,Tidak Diketahui',
            'patient_is_sterile' => 'nullable|in:0,1',
        ]);

        $newPatient = DB::transaction(function () use ($request) {
            // Ensure Options Exist (Dynamic CRUD)
            $this->ensureOptionExists('id_type', $request->id_type);
            $this->ensureOptionExists('ethnicity', $request->ethnicity);
            $this->ensureOptionExists('religion', $request->religion);
            $this->ensureOptionExists('marital_status', $request->marital_status);

            // Determine Display Name
            if ($request->is_business) {
                $displayName = $request->business_name;
            } else {
                $displayName = trim(($request->first_name ?? '') . ' ' . $request->last_name);
            }

            // Primary Address (for legacy field)
            $primaryAddress = $request->addresses[0]['street'] ?? '';
            if (isset($request->addresses[0]['city'])) {
                $primaryAddress .= ', ' . $request->addresses[0]['city'];
            }

            // 1. Create User (for Login)
            $user = User::create([
                'name' => $displayName,
                'phone' => $request->phone,
                'email' => $request->email ?? $request->phone . '@birawa.vet',
                'address' => $primaryAddress,
                'role' => 'client',
                'password' => Hash::make('password'), // Default password
            ]);

            // 2. Create Client (Domain Record)
            $client = Client::create([
                'user_id' => $user->id,
                'name' => $displayName,
                'phone' => $request->phone,
                'address' => $primaryAddress, // Legacy field
                
                // Extended Fields
                'first_name' => $request->first_name,
                'last_name' => $request->last_name,
                'is_business' => $request->is_business,
                'business_name' => $request->business_name,
                'contact_person' => $request->contact_person,
                'id_type' => $request->id_type,
                'id_number' => $request->id_number,
                'gender' => $request->gender,
                'occupation' => $request->occupation,
                'dob' => $request->dob,
                'ethnicity' => $request->ethnicity,
                'religion' => $request->religion,
                'marital_status' => $request->marital_status,
            ]);

            // 3. Create Addresses
            foreach ($request->addresses as $addr) {
                $client->addresses()->create([
                    'street' => $addr['street'],
                    'additional_info' => $addr['additional_info'] ?? null,
                    'city' => $addr['city'] ?? null,
                    'province' => $addr['province'] ?? null,
                    'postal_code' => $addr['postal_code'] ?? null,
                    'country' => $addr['country'] ?? 'Indonesia',
                    'parking_type' => $addr['parking_type'] ?? null,
                    'address_type' => $addr['address_type'] ?? null,
                ]);
            }

            // 4. Create Patient
            $patient = Patient::create([
                'client_id' => $client->id,
                'name' => $request->patient_name,
                'species' => $request->patient_species,
                'breed' => $request->patient_breed,
                'dob' => $request->patient_dob,
                'gender' => $request->patient_gender,
                'is_sterile' => $request->patient_is_sterile,
            ]);
            
            return $patient;
        });

        if ($request->input('return_to') === 'visits.create') {
            return redirect()->route('visits.create', ['patient_id' => $newPatient->id])
                ->with('success', 'Client and Patient created successfully. Please continue creating visit.');
        }

        return redirect()->route('clients.index')
            ->with('success', 'Client and Patient created successfully.');
    }

    /**
     * Display the specified resource.
     */
    public function show(Request $request, Client $client)
    {
        $client->load('patients');
        
        if ($request->wantsJson() || $request->is('api/*')) {
            return response()->json($client);
        }

        return view('clients.show', compact('client'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Client $client)
    {
        return view('clients.edit', compact('client'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Client $client)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'phone' => 'required|string|max:20',
            'address' => 'nullable|string',
        ]);

        $client->update($request->only(['name', 'phone', 'address']));
        
        // Also update User if exists
        if ($client->user) {
            $client->user->update([
                'name' => $request->name,
                'phone' => $request->phone,
                'address' => $request->address,
            ]);
        }

        return redirect()->route('clients.index')
            ->with('success', 'Client updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Client $client)
    {
        $client->delete();
        // Should we delete User? Maybe not, to keep history? 
        // Soft delete handles client.
        
        return redirect()->route('clients.index')
            ->with('success', 'Client deleted successfully.');
    }

    /**
     * Ensure a form option exists in the database.
     */
    private function ensureOptionExists($category, $value)
    {
        if (empty($value) || $value === 'custom') {
            return;
        }

        // Check if value already exists (case insensitive check usually preferred, but strict here for now)
        // Using firstOrCreate to ensure atomic-ish check
        FormOption::firstOrCreate(
            ['category' => $category, 'value' => $value],
            ['is_active' => true]
        );
    }
}
