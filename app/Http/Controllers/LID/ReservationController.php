<?php

namespace App\Http\Controllers\LID;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\LID\Reservation;
use App\Models\LID\ChemicalRequest;
use App\Models\LID\PDEARequest;
use Illuminate\Support\Facades\Log;

class ReservationController extends Controller
{
    public function index()
    {
        return view('LID.reservations.index');
    }
    
    public function create()
{
    $chemicals = \App\Models\LID\LIDChemical::all();
    $pdeaChemicals = \App\Models\LID\LIDPDEAChemical::all();

    return view('LID.forms.create', compact('chemicals', 'pdeaChemicals'));
}


    public function store(Request $request)
    {
        // Log input for debugging
        Log::info('Reservation request data:', $request->all());
        
        // Validate main reservation form
        $validated = $request->validate([
            'borrower_name' => 'required|string|max:255',
            'borrower_type' => 'required|string|in:Faculty Member,Student',
            'purpose' => 'required|array',
            'purpose.*' => 'string|in:Laboratory Activity,Student Thesis,Faculty Research',
            'room_no' => 'required|string|max:100',
            'date_requested' => 'required|date',
            'term' => 'required|string',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
            'number_of_groups' => 'required|integer|min:1',
            'time' => 'required|string',
            'program' => 'nullable|string|max:255',
            'year_section' => 'nullable|string|max:100',
            'subject_code' => 'nullable|string|max:100',
            'subject_description' => 'nullable|string|max:255',
            'activity_title' => 'nullable|string|max:255',
            'activity_no' => 'nullable|string|max:100',
        ]);

        // Convert purpose array to CSV string
        $validated['purpose'] = implode(', ', $request->purpose);

        // -----------------------------------------
        // 1️⃣ Generate Reference Number
        // Format: 2526-A-000001
        // Change logic here if needed.
        // -----------------------------------------
        $referenceNumber = sprintf('%s26-A-%06d', date('y'), Reservation::max('id') + 1);

        // Add to validated attributes
        $validated['reference_number'] = $referenceNumber;

        // -----------------------------------------
        // 2️⃣ Create Reservation Record
        // -----------------------------------------
        $reservation = Reservation::create($validated);
        
        Log::info('Reservation created:', $reservation->toArray());

        // -----------------------------------------
        // 3️⃣ Handle NON-REGULATED Chemicals
        // -----------------------------------------
        if ($request->has('nonregulated')) {
            foreach ($request->nonregulated as $chem) {
                if (!empty($chem['name'])) {
                    ChemicalRequest::create([
                        'reservation_id' => $reservation->id,
                        'name' => $chem['name'],
                        'solution' => isset($chem['soln']) ? 1 : 0,
                        'concentration' => isset($chem['conc']) ? 1 : 0,
                        'concentration_value' => $chem['concentration_value'] ?? null,
                        'volume' => $chem['volume'] ?? null,
                        'instruction' => $chem['instruction'] ?? null,
                    ]);
                }
            }
        }

        // -----------------------------------------
        // 4️⃣ Handle PDEA REGULATED Chemicals
        // -----------------------------------------
        if ($request->has('regulated')) {
            foreach ($request->regulated as $chem) {
                if (!empty($chem['name'])) {
                    PDEARequest::create([
                        'reservation_id' => $reservation->id,
                        'name' => $chem['name'],
                        'solution' => isset($chem['soln']) ? 1 : 0,
                        'concentration' => isset($chem['conc']) ? 1 : 0,
                        'concentration_value' => $chem['concentration_value'] ?? null,
                        'volume' => $chem['volume'] ?? null,
                        'instruction' => $chem['instruction'] ?? null,
                    ]);
                }
            }
        }

        // -----------------------------------------
        // 5️⃣ Return with Success Modal Trigger
        // -----------------------------------------
        return redirect()
            ->back()
            ->with('success_reference', $referenceNumber);
    }
}
