<?php

namespace App\Http\Controllers\LID;

use App\Http\Controllers\Controller;
use App\Models\LID\ChemicalRequest;
use App\Models\LID\EquipmentRequest;
use App\Models\LID\GlasswareRequest;
use App\Models\LID\LIDChemical;
use App\Models\LID\LIDEquipment;
use App\Models\LID\LIDGlassware;
use App\Models\LID\LIDPDEAChemical;
use App\Models\LID\PDEARequest;
use App\Models\LID\Reservation;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Validator; // Added this import
use Illuminate\Validation\ValidationException; // Added this import

class ReservationController extends Controller
{
    public function index(Request $request)
    {
        // Base query with relationships
        $query = Reservation::with(['chemicalRequests', 'pdeaRequests', 'glasswareRequests', 'equipmentRequests', 'studentReservations']);

        // Apply filters
        if ($request->borrower_name) {
            $query->where('borrower_name', 'like', '%'.$request->borrower_name.'%');
        }

        if ($request->borrower_type) {
            $query->where('borrower_type', $request->borrower_type);
        }

        if ($request->reference_number) {
            $query->where('reference_number', 'like', '%'.$request->reference_number.'%');
        }

        if ($request->date_from) {
            $query->whereDate('date_requested', '>=', $request->date_from);
        }

        if ($request->date_to) {
            $query->whereDate('date_requested', '<=', $request->date_to);
        }

        // Get filtered reservations
        $reservations = $query->orderBy('created_at', 'desc')->paginate(10);

        // Calculate statistics
        $totalReservations = Reservation::count();

        if (Schema::hasColumn('reservations', 'status')) {
            $approvedReservations = Reservation::where('status', 'Approved')->count();
            $pendingReservations = Reservation::where('status', 'Pending')->count();
        } else {
            $approvedReservations = 0;
            $pendingReservations = $totalReservations;
        }

        $pdeaRequests = PDEARequest::count();

        return view('LID.reservations.index', compact(
            'reservations',
            'totalReservations',
            'approvedReservations',
            'pendingReservations',
            'pdeaRequests'
        ));
    }

    public function create()
    {
        $chemicals = LIDChemical::where('quantity', '>', 0)->get();
        $pdeaChemicals = LIDPDEAChemical::where('quantity', '>', 0)->get();
        $glassware = LIDGlassware::where('quantity', '>', 0)->get();
        $equipment = LIDEquipment::where('quantity', '>', 0)->get();

        return view('LID.forms.create', compact('chemicals', 'pdeaChemicals', 'glassware', 'equipment'));
    }

    public function studentCreate()
    {
        return view('LID.forms.student-create');
    }

    public function getFacultyReservation($reference)
    {
        try {
            $facultyReservation = Reservation::where('reference_number', $reference)
                ->where('borrower_type', 'Faculty Member')
                ->with(['chemicalRequests', 'pdeaRequests', 'glasswareRequests', 'equipmentRequests'])
                ->first();

            if (! $facultyReservation) {
                return response()->json([
                    'success' => false,
                    'message' => 'Faculty reservation not found',
                ]);
            }

            // Check if reservation is approved
            if ($facultyReservation->status !== 'Approved') {
                $statusMessage = $facultyReservation->status === 'Pending'
                    ? 'Faculty reservation is still pending approval'
                    : ($facultyReservation->status === 'Rejected'
                        ? 'Faculty reservation has been rejected'
                        : 'Faculty reservation has been '.strtolower($facultyReservation->status));

                return response()->json([
                    'success' => false,
                    'message' => $statusMessage,
                    'status' => $facultyReservation->status,
                    'status_message' => $statusMessage,
                ]);
            }

            // Count existing student reservations for this faculty
            $studentCount = Reservation::where('faculty_reference_id', $facultyReservation->id)->count();

            // Get chemical requests
            $chemicals = $facultyReservation->chemicalRequests;
            $pdeaChemicals = $facultyReservation->pdeaRequests;
            $glassware = $facultyReservation->glasswareRequests;
            $equipment = $facultyReservation->equipmentRequests;

            return response()->json([
                'success' => true,
                'reservation' => $facultyReservation,
                'student_count' => $studentCount,
                'chemicals' => $chemicals,
                'pdea_chemicals' => $pdeaChemicals,
                'glassware' => $glassware,
                'equipment' => $equipment,
            ]);
        } catch (\Exception $e) {
            Log::error('Error fetching faculty reservation: '.$e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Error fetching reservation details',
            ]);
        }
    }

    public function store(Request $request)
    {
        // Validate faculty reservation
        $validator = Validator::make($request->all(), [
            'borrower_name' => 'required|string|max:255',
            'date_requested' => 'required|date',
            'term' => 'required|string',
            'purpose' => 'required|array',
            'purpose.*' => 'string|in:Laboratory Activity,Student Thesis,Faculty Research',
            'room_no' => 'required|string|max:100',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
            'number_of_groups' => 'required|integer|min:1|max:20',
            'time' => 'required|string',
            'program' => 'nullable|string|max:255',
            'year_section' => 'nullable|string|max:100',
            'subject_code' => 'nullable|string|max:100',
            'subject_description' => 'nullable|string|max:255',
            'activity_title' => 'nullable|string|max:255',
            'activity_no' => 'nullable|string|max:100',
            'nonregulated' => 'nullable|array',
            'nonregulated.*.name' => 'nullable|string',
            'nonregulated.*.quantity_per_group' => 'nullable|numeric|min:0.1',
            'nonregulated.*.is_solution' => 'nullable|in:0,1',
            'nonregulated.*.has_concentration' => 'nullable|in:0,1',
            'nonregulated.*.concentration_value' => 'nullable|numeric|min:0',
            'nonregulated.*.concentration_unit' => 'nullable|string|max:50',
            'nonregulated.*.volume' => 'nullable|numeric|min:0',
            'nonregulated.*.volume_unit' => 'nullable|string|max:50',
            'regulated' => 'nullable|array',
            'regulated.*.name' => 'nullable|string',
            'regulated.*.quantity_per_group' => 'nullable|numeric|min:0.1',
            'regulated.*.is_solution' => 'nullable|in:0,1',
            'regulated.*.has_concentration' => 'nullable|in:0,1',
            'regulated.*.concentration_value' => 'nullable|numeric|min:0',
            'regulated.*.concentration_unit' => 'nullable|string|max:50',
            'regulated.*.volume' => 'nullable|numeric|min:0',
            'regulated.*.volume_unit' => 'nullable|string|max:50',
            'glassware' => 'nullable|array',
            'glassware.*.name' => 'nullable|string',
            'glassware.*.quantity_per_group' => 'nullable|numeric|min:0.1',
            'glassware.*.type' => 'nullable|string',
            'equipment' => 'nullable|array',
            'equipment.*.name' => 'nullable|string',
            'equipment.*.quantity_per_group' => 'nullable|numeric|min:0.1',
            'equipment.*.type' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            Log::error('Validation failed:', $validator->errors()->toArray());

            return redirect()
                ->back()
                ->withErrors($validator)
                ->withInput();
        }

        // Validate all requests
        try {
            $this->validateChemicalRequests($request);
            $this->validateItemRequests($request, 'glassware');
            $this->validateItemRequests($request, 'equipment');
        } catch (ValidationException $e) {
            Log::error('Request validation failed:', $e->errors());

            return redirect()
                ->back()
                ->withErrors($e->errors())
                ->withInput();
        }

        // Generate reference number for faculty: 2526-A-000001
        $lastId = Reservation::max('id') ?? 0;
        $referenceNumber = sprintf('%s26-A-%06d', date('y'), $lastId + 1);
        Log::info('Generated reference number: '.$referenceNumber);

        // Create faculty reservation
        $reservationData = $request->only([
            'borrower_name', 'date_requested', 'term', 'room_no',
            'start_date', 'end_date', 'number_of_groups', 'time',
            'program', 'year_section', 'subject_code', 'subject_description',
            'activity_title', 'activity_no',
        ]);

        $reservationData['borrower_type'] = 'Faculty Member';
        $reservationData['reference_number'] = $referenceNumber;
        $reservationData['purpose'] = implode(', ', $request->purpose);
        $reservationData['status'] = 'Pending';

        Log::info('Attempting to create reservation with data:', $reservationData);

        DB::beginTransaction();
        try {
            $reservation = Reservation::create($reservationData);
            Log::info('Reservation created successfully. ID: '.$reservation->id);

            // Process all request types
            $this->processChemicalRequests($request, $reservation, 'nonregulated');
            Log::info('Non-regulated chemicals processed');

            $this->processChemicalRequests($request, $reservation, 'regulated');
            Log::info('Regulated chemicals processed');

            $this->processItemRequests($request, $reservation, 'glassware');
            Log::info('Glassware processed');

            $this->processItemRequests($request, $reservation, 'equipment');
            Log::info('Equipment processed');

            DB::commit();
            Log::info('Transaction committed successfully');

            return redirect()
                ->route('lid.reservations.index')
                ->with('success', "Faculty reservation created successfully! Reference Number: {$referenceNumber}")
                ->with('reference_number', $referenceNumber);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Reservation creation failed: ', [
                'message' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine(),
                'trace' => $e->getTraceAsString(),
            ]);

            return redirect()
                ->back()
                ->withErrors(['error' => 'Failed to create reservation. Error: '.$e->getMessage()])
                ->withInput();
        }
    }

    public function storeStudentReservation(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'faculty_reference' => 'required|string|max:50',
            'borrower_name' => 'required|string|max:255',
            'student_id' => 'required|string|max:50',
            'group_number' => 'required|integer|min:1',
            'email' => 'nullable|email|max:255',
            'contact_number' => 'nullable|string|max:20',
        ]);

        if ($validator->fails()) {
            Log::error('Student reservation validation failed:', $validator->errors()->toArray());

            // Return JSON response for AJAX requests
            if ($request->ajax() || $request->wantsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Validation failed',
                    'errors' => $validator->errors(),
                ], 422);
            }

            return redirect()
                ->back()
                ->withErrors($validator)
                ->withInput();
        }

        // Find faculty reservation
        $facultyReservation = Reservation::where('reference_number', $request->faculty_reference)
            ->where('borrower_type', 'Faculty Member')
            ->first();

        if (! $facultyReservation) {
            Log::error('Faculty reservation not found for reference: '.$request->faculty_reference);

            if ($request->ajax() || $request->wantsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Invalid faculty reference number',
                    'errors' => ['faculty_reference' => 'Invalid faculty reference number'],
                ], 404);
            }

            return redirect()
                ->back()
                ->withErrors(['faculty_reference' => 'Invalid faculty reference number'])
                ->withInput();
        }

        // Check if faculty reservation is approved
        if ($facultyReservation->status !== 'Approved') {
            Log::error('Faculty reservation is not approved. Status: '.$facultyReservation->status);

            $statusMessage = $facultyReservation->status === 'Pending'
                ? 'Faculty reservation is still pending approval'
                : ($facultyReservation->status === 'Rejected'
                    ? 'Faculty reservation has been rejected'
                    : 'Faculty reservation has been '.strtolower($facultyReservation->status));

            if ($request->ajax() || $request->wantsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => $statusMessage,
                    'errors' => ['faculty_reference' => $statusMessage],
                ], 422);
            }

            return redirect()
                ->back()
                ->withErrors(['faculty_reference' => $statusMessage])
                ->withInput();
        }

        // Check if group number is available
        $existingGroup = Reservation::where('faculty_reference_id', $facultyReservation->id)
            ->where('group_number', $request->group_number)
            ->exists();

        if ($existingGroup) {
            Log::error('Group number already taken: '.$request->group_number.' for faculty reservation: '.$facultyReservation->id);

            if ($request->ajax() || $request->wantsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'This group number is already taken',
                    'errors' => ['group_number' => 'This group number is already taken'],
                ], 422);
            }

            return redirect()
                ->back()
                ->withErrors(['group_number' => 'This group number is already taken'])
                ->withInput();
        }

        // Check if group number exceeds allowed groups
        if ($request->group_number > $facultyReservation->number_of_groups) {
            Log::error('Group number exceeds allowed groups: '.$request->group_number.' > '.$facultyReservation->number_of_groups);

            if ($request->ajax() || $request->wantsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Group number exceeds available groups',
                    'errors' => ['group_number' => 'Group number exceeds available groups'],
                ], 422);
            }

            return redirect()
                ->back()
                ->withErrors(['group_number' => 'Group number exceeds available groups'])
                ->withInput();
        }

        DB::beginTransaction();
        try {
            // Generate student reference number: 2526-A-000001-1
            $studentReference = $facultyReservation->reference_number.'-'.$request->group_number;

            // Create student reservation
            $studentReservation = $facultyReservation->replicate();
            $studentReservation->borrower_name = $request->borrower_name;
            $studentReservation->borrower_type = 'Student';
            $studentReservation->reference_number = $studentReference;
            $studentReservation->faculty_reference_id = $facultyReservation->id;
            $studentReservation->group_number = $request->group_number;
            $studentReservation->student_id = $request->student_id;
            $studentReservation->email = $request->email;
            $studentReservation->contact_number = $request->contact_number;
            $studentReservation->date_requested = now()->format('Y-m-d');
            $studentReservation->save();

            Log::info('Student reservation created successfully. ID: '.$studentReservation->id);

            // Copy all request types from faculty reservation with all properties
            $this->copyChemicalRequests($facultyReservation->id, $studentReservation->id);
            Log::info('Chemical requests copied successfully for student reservation ID: '.$studentReservation->id);

            $this->copyItemRequests($facultyReservation->id, $studentReservation->id, 'glassware');
            Log::info('Glassware requests copied successfully for student reservation ID: '.$studentReservation->id);

            $this->copyItemRequests($facultyReservation->id, $studentReservation->id, 'equipment');
            Log::info('Equipment requests copied successfully for student reservation ID: '.$studentReservation->id);

            DB::commit();

            // Always return JSON for AJAX requests
            if ($request->ajax() || $request->wantsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Student reservation created successfully!',
                    'reference_number' => $studentReference,
                    'redirect_url' => route('lid.reservations.index'),
                ]);
            }

            return redirect()
                ->route('lid.reservations.index')
                ->with('success', "Student reservation created successfully! Reference Number: {$studentReference}");
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Student reservation creation failed: ', [
                'message' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine(),
                'trace' => $e->getTraceAsString(),
            ]);

            if ($request->ajax() || $request->wantsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Failed to create student reservation: '.$e->getMessage(),
                    'errors' => ['error' => $e->getMessage()],
                ], 500);
            }

            return redirect()
                ->back()
                ->withErrors(['error' => 'Failed to create student reservation. Please try again.'])
                ->withInput();
        }
    }

    private function validateChemicalRequests(Request $request)
    {
        $hasChemicals = false;

        // Check non-regulated chemicals
        if ($request->has('nonregulated')) {
            foreach ($request->nonregulated as $index => $chem) {
                if (! empty($chem['name'])) {
                    $hasChemicals = true;

                    if (empty($chem['quantity_per_group']) || $chem['quantity_per_group'] <= 0) {
                        throw ValidationException::withMessages([
                            'nonregulated.'.$index.'.quantity_per_group' => 'Quantity per group is required for all chemicals',
                        ]);
                    }

                    // Check stock availability
                    $chemical = LIDChemical::where('name', $chem['name'])->first();
                    if ($chemical) {
                        $totalRequired = $chem['quantity_per_group'] * $request->number_of_groups;
                        if ($totalRequired > $chemical->available_quantity) {
                            throw ValidationException::withMessages([
                                'nonregulated.'.$index.'.quantity_per_group' => "Insufficient stock for {$chem['name']}. Available: {$chemical->available_quantity} {$chemical->unit}, Required: {$totalRequired}",
                            ]);
                        }
                    }
                }
            }
        }

        // Check regulated chemicals
        if ($request->has('regulated')) {
            foreach ($request->regulated as $index => $chem) {
                if (! empty($chem['name'])) {
                    $hasChemicals = true;

                    if (empty($chem['quantity_per_group']) || $chem['quantity_per_group'] <= 0) {
                        throw ValidationException::withMessages([
                            'regulated.'.$index.'.quantity_per_group' => 'Quantity per group is required for all chemicals',
                        ]);
                    }

                    // Check stock availability
                    $chemical = LIDPDEAChemical::where('name', $chem['name'])->first();
                    if ($chemical) {
                        $totalRequired = $chem['quantity_per_group'] * $request->number_of_groups;
                        if ($totalRequired > $chemical->available_quantity) {
                            throw ValidationException::withMessages([
                                'regulated.'.$index.'.quantity_per_group' => "Insufficient stock for {$chem['name']}. Available: {$chemical->available_quantity} {$chemical->unit}, Required: {$totalRequired}",
                            ]);
                        }
                    }
                }
            }
        }

        if (! $hasChemicals) {
            throw ValidationException::withMessages([
                'chemicals' => 'Please select at least one chemical',
            ]);
        }
    }

    private function validateItemRequests(Request $request, string $requestType)
    {
        $hasItems = false;

        if ($request->has($requestType)) {
            foreach ($request->$requestType as $index => $item) {
                if (! empty($item['name'])) {
                    $hasItems = true;

                    if (empty($item['quantity_per_group']) || $item['quantity_per_group'] <= 0) {
                        throw ValidationException::withMessages([
                            $requestType.'.'.$index.'.quantity_per_group' => 'Quantity per group is required for all items',
                        ]);
                    }

                    // Check stock availability
                    $stockModel = $requestType === 'glassware' ? LIDGlassware::class : LIDEquipment::class;
                    $stockItem = $stockModel::where('name', $item['name'])->first();
                    if ($stockItem) {
                        $totalRequired = $item['quantity_per_group'] * $request->number_of_groups;
                        if ($totalRequired > $stockItem->available_quantity) {
                            throw ValidationException::withMessages([
                                $requestType.'.'.$index.'.quantity_per_group' => "Insufficient stock for {$item['name']}. Available: {$stockItem->available_quantity} {$stockItem->unit}, Required: {$totalRequired}",
                            ]);
                        }
                    }
                }
            }
        }
    }

    private function processChemicalRequests(Request $request, Reservation $reservation, string $requestType)
    {
        if (! $request->has($requestType)) {
            return;
        }

        $modelClass = $requestType === 'nonregulated' ? ChemicalRequest::class : PDEARequest::class;
        $stockModel = $requestType === 'nonregulated' ? LIDChemical::class : LIDPDEAChemical::class;

        foreach ($request->$requestType as $index => $chem) {
            if (empty($chem['name'])) {
                continue;
            }

            // Calculate total quantity required
            $quantityPerGroup = floatval($chem['quantity_per_group']);
            $totalRequired = $quantityPerGroup * $reservation->number_of_groups;

            // Get chemical details from database
            if ($requestType === 'nonregulated') {
                $chemical = LIDChemical::where('name', $chem['name'])->first();
            } else {
                $chemical = LIDPDEAChemical::where('name', $chem['name'])->first();
            }

            if (! $chemical) {
                Log::warning("Chemical not found: {$chem['name']}");

                continue;
            }

            // Prepare chemical properties with proper type casting
            $chemicalRequest = [
                'reservation_id' => $reservation->id,
                'name' => $chem['name'],
                'is_solution' => isset($chem['is_solution']) ? (int) $chem['is_solution'] : ($chemical->is_solution ?? 0),
                'has_concentration' => isset($chem['has_concentration']) ? (int) $chem['has_concentration'] : ($chemical->has_concentration ?? 0),
                'concentration_value' => isset($chem['concentration_value']) ? (float) $chem['concentration_value'] : ($chemical->concentration_value ?? 0),
                'concentration_unit' => $chem['concentration_unit'] ?? ($chemical->concentration_unit ?? ''),
                'volume' => isset($chem['volume']) ? (float) $chem['volume'] : ($chemical->volume ?? 0),
                'volume_unit' => $chem['volume_unit'] ?? ($chemical->volume_unit ?? ''),
                'quantity_per_group' => $quantityPerGroup,
                'total_quantity' => $totalRequired,
                'unit' => $chemical->unit,
                'instruction' => $chem['instruction'] ?? null,
            ];

            // Log the data being saved
            Log::info("Creating {$requestType} chemical request for reservation {$reservation->id}:", $chemicalRequest);

            $modelClass::create($chemicalRequest);

            // Reserve stock (don't deduct until approved)
            $this->reserveStock($chem['name'], $totalRequired, $requestType);
        }
    }

    private function processItemRequests(Request $request, Reservation $reservation, string $requestType)
    {
        if (! $request->has($requestType)) {
            return;
        }

        $modelClass = $requestType === 'glassware' ? GlasswareRequest::class : EquipmentRequest::class;
        $stockModel = $requestType === 'glassware' ? LIDGlassware::class : LIDEquipment::class;

        foreach ($request->$requestType as $index => $item) {
            if (empty($item['name'])) {
                continue;
            }

            // Calculate total quantity required
            $quantityPerGroup = floatval($item['quantity_per_group']);
            $totalRequired = $quantityPerGroup * $reservation->number_of_groups;

            // Get item details from database
            if ($requestType === 'glassware') {
                $stockItem = LIDGlassware::where('name', $item['name'])->first();
            } else {
                $stockItem = LIDEquipment::where('name', $item['name'])->first();
            }

            if (! $stockItem) {
                Log::warning("Item not found: {$item['name']}");

                continue;
            }

            // Prepare item properties
            $itemRequest = [
                'reservation_id' => $reservation->id,
                'name' => $item['name'],
                'type' => $item['type'] ?? $stockItem->type,
                'quantity_per_group' => $quantityPerGroup,
                'total_quantity' => $totalRequired,
                'unit' => $stockItem->unit,
                'instruction' => $item['instruction'] ?? null,
            ];

            // Log the data being saved
            Log::info("Creating {$requestType} request for reservation {$reservation->id}:", $itemRequest);

            $modelClass::create($itemRequest);

            // Reserve stock (don't deduct until approved)
            $this->reserveItemStock($item['name'], $totalRequired, $requestType);
        }
    }

    private function reserveStock($chemicalName, $quantity, $type)
    {
        if ($type === 'nonregulated') {
            $chemical = LIDChemical::where('name', $chemicalName)->first();
        } else {
            $chemical = LIDPDEAChemical::where('name', $chemicalName)->first();
        }

        if ($chemical) {
            $chemical->reserved_quantity = ($chemical->reserved_quantity ?? 0) + $quantity;
            $chemical->save();
        }
    }

    private function reserveItemStock($itemName, $quantity, $type)
    {
        if ($type === 'glassware') {
            $item = LIDGlassware::where('name', $itemName)->first();
        } else {
            $item = LIDEquipment::where('name', $itemName)->first();
        }

        if ($item) {
            $item->reserved_quantity = ($item->reserved_quantity ?? 0) + $quantity;
            $item->save();
        }
    }

    private function copyChemicalRequests($facultyReservationId, $studentReservationId)
    {
        try {
            // Copy non-regulated chemical requests
            $nonRegChemicals = ChemicalRequest::where('reservation_id', $facultyReservationId)->get();
            Log::info('Found '.$nonRegChemicals->count().' non-regulated chemicals for faculty reservation ID: '.$facultyReservationId);

            foreach ($nonRegChemicals as $chem) {
                $newChem = new ChemicalRequest();
                $newChem->reservation_id = $studentReservationId;
                $newChem->name = $chem->name;
                $newChem->is_solution = $chem->is_solution;
                $newChem->has_concentration = $chem->has_concentration;
                $newChem->concentration_value = $chem->concentration_value;
                $newChem->concentration_unit = $chem->concentration_unit;
                $newChem->volume = $chem->volume;
                $newChem->volume_unit = $chem->volume_unit;
                $newChem->quantity_per_group = $chem->quantity_per_group;
                $newChem->total_quantity = $chem->quantity_per_group; // Student gets quantity for one group
                $newChem->unit = $chem->unit;
                $newChem->instruction = $chem->instruction;
                $newChem->save();

                Log::info('Copied non-regulated chemical: '.$chem->name.' for student reservation ID: '.$studentReservationId);
            }

            // Copy PDEA chemical requests
            $pdeaChemicals = PDEARequest::where('reservation_id', $facultyReservationId)->get();
            Log::info('Found '.$pdeaChemicals->count().' PDEA chemicals for faculty reservation ID: '.$facultyReservationId);

            foreach ($pdeaChemicals as $chem) {
                $newChem = new PDEARequest();
                $newChem->reservation_id = $studentReservationId;
                $newChem->name = $chem->name;
                $newChem->is_solution = $chem->is_solution;
                $newChem->has_concentration = $chem->has_concentration;
                $newChem->concentration_value = $chem->concentration_value;
                $newChem->concentration_unit = $chem->concentration_unit;
                $newChem->volume = $chem->volume;
                $newChem->volume_unit = $chem->volume_unit;
                $newChem->quantity_per_group = $chem->quantity_per_group;
                $newChem->total_quantity = $chem->quantity_per_group; // Student gets quantity for one group
                $newChem->unit = $chem->unit;
                $newChem->instruction = $chem->instruction;
                $newChem->save();

                Log::info('Copied PDEA chemical: '.$chem->name.' for student reservation ID: '.$studentReservationId);
            }
        } catch (\Exception $e) {
            Log::error('Error copying chemical requests: ', [
                'message' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine(),
                'trace' => $e->getTraceAsString(),
            ]);
            throw $e;
        }
    }

    private function copyItemRequests($facultyReservationId, $studentReservationId, string $requestType)
    {
        try {
            $modelClass = $requestType === 'glassware' ? GlasswareRequest::class : EquipmentRequest::class;
            $requests = $modelClass::where('reservation_id', $facultyReservationId)->get();

            Log::info('Found '.$requests->count().' '.$requestType.' for faculty reservation ID: '.$facultyReservationId);

            foreach ($requests as $request) {
                $newRequest = new $modelClass();
                $newRequest->reservation_id = $studentReservationId;
                $newRequest->name = $request->name;
                $newRequest->type = $request->type;
                $newRequest->quantity_per_group = $request->quantity_per_group;
                $newRequest->total_quantity = $request->quantity_per_group; // Student gets quantity for one group
                $newRequest->unit = $request->unit;
                $newRequest->instruction = $request->instruction;
                $newRequest->save();

                Log::info('Copied '.$requestType.': '.$request->name.' for student reservation ID: '.$studentReservationId);
            }
        } catch (\Exception $e) {
            Log::error('Error copying '.$requestType.' requests: ', [
                'message' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine(),
                'trace' => $e->getTraceAsString(),
            ]);
            throw $e;
        }
    }

    public function show($id)
    {
        $reservation = Reservation::with(['chemicalRequests', 'pdeaRequests', 'glasswareRequests', 'equipmentRequests', 'studentReservations'])->findOrFail($id);

        return view('LID.reservations.show', compact('reservation'))->render();
    }

    public function updateStatus(Request $request, $id)
    {
        if (! Schema::hasColumn('reservations', 'status')) {
            return response()->json(['error' => 'Status feature not available'], 400);
        }

        $reservation = Reservation::findOrFail($id);
        $oldStatus = $reservation->status;
        $reservation->status = $request->status;
        $reservation->save();

        // If approved, deduct stock from inventory
        if ($oldStatus !== 'Approved' && $request->status === 'Approved') {
            $this->deductStockForReservation($reservation);
        }

        // If rejected or cancelled, release reserved stock
        if (($oldStatus === 'Approved' || $oldStatus === 'Pending') &&
            in_array($request->status, ['Rejected', 'Cancelled'])) {
            $this->releaseReservedStock($reservation);
        }

        return response()->json(['success' => true]);
    }

    private function deductStockForReservation(Reservation $reservation)
    {
        // Deduct non-regulated chemicals
        $chemicalRequests = ChemicalRequest::where('reservation_id', $reservation->id)->get();
        foreach ($chemicalRequests as $request) {
            $chemical = LIDChemical::where('name', $request->name)->first();
            if ($chemical) {
                $chemical->quantity -= $request->total_quantity;
                $chemical->reserved_quantity = max(0, $chemical->reserved_quantity - $request->total_quantity);
                $chemical->save();
            }
        }

        // Deduct PDEA chemicals
        $pdeaRequests = PDEARequest::where('reservation_id', $reservation->id)->get();
        foreach ($pdeaRequests as $request) {
            $chemical = LIDPDEAChemical::where('name', $request->name)->first();
            if ($chemical) {
                $chemical->quantity -= $request->total_quantity;
                $chemical->reserved_quantity = max(0, $chemical->reserved_quantity - $request->total_quantity);
                $chemical->save();
            }
        }

        // Deduct glassware
        $glasswareRequests = GlasswareRequest::where('reservation_id', $reservation->id)->get();
        foreach ($glasswareRequests as $request) {
            $item = LIDGlassware::where('name', $request->name)->first();
            if ($item) {
                $item->quantity -= $request->total_quantity;
                $item->reserved_quantity = max(0, $item->reserved_quantity - $request->total_quantity);
                $item->save();
            }
        }

        // Deduct equipment
        $equipmentRequests = EquipmentRequest::where('reservation_id', $reservation->id)->get();
        foreach ($equipmentRequests as $request) {
            $item = LIDEquipment::where('name', $request->name)->first();
            if ($item) {
                $item->quantity -= $request->total_quantity;
                $item->reserved_quantity = max(0, $item->reserved_quantity - $request->total_quantity);
                $item->save();
            }
        }
    }

    private function releaseReservedStock(Reservation $reservation)
    {
        // Release non-regulated chemicals
        $chemicalRequests = ChemicalRequest::where('reservation_id', $reservation->id)->get();
        foreach ($chemicalRequests as $request) {
            $chemical = LIDChemical::where('name', $request->name)->first();
            if ($chemical) {
                $chemical->reserved_quantity = max(0, $chemical->reserved_quantity - $request->total_quantity);
                $chemical->save();
            }
        }

        // Release PDEA chemicals
        $pdeaRequests = PDEARequest::where('reservation_id', $reservation->id)->get();
        foreach ($pdeaRequests as $request) {
            $chemical = LIDPDEAChemical::where('name', $request->name)->first();
            if ($chemical) {
                $chemical->reserved_quantity = max(0, $chemical->reserved_quantity - $request->total_quantity);
                $chemical->save();
            }
        }

        // Release glassware
        $glasswareRequests = GlasswareRequest::where('reservation_id', $reservation->id)->get();
        foreach ($glasswareRequests as $request) {
            $item = LIDGlassware::where('name', $request->name)->first();
            if ($item) {
                $item->reserved_quantity = max(0, $item->reserved_quantity - $request->total_quantity);
                $item->save();
            }
        }

        // Release equipment
        $equipmentRequests = EquipmentRequest::where('reservation_id', $reservation->id)->get();
        foreach ($equipmentRequests as $request) {
            $item = LIDEquipment::where('name', $request->name)->first();
            if ($item) {
                $item->reserved_quantity = max(0, $item->reserved_quantity - $request->total_quantity);
                $item->save();
            }
        }
    }

    public function destroy($id)
    {
        $reservation = Reservation::findOrFail($id);

        // Release reserved stock before deleting
        $this->releaseReservedStock($reservation);

        $reservation->delete();

        return response()->json(['success' => true]);
    }

    public function edit($id)
    {
        $reservation = Reservation::with(['chemicalRequests', 'pdeaRequests', 'glasswareRequests', 'equipmentRequests'])->findOrFail($id);

        // For student reservations, also load the faculty reservation
        if ($reservation->borrower_type === 'Student' && $reservation->faculty_reference_id) {
            $reservation->load('facultyReservation');
        }

        // Fetch available inventory items
        $chemicals = LIDChemical::where('quantity', '>', 0)->get();
        $pdeaChemicals = LIDPDEAChemical::where('quantity', '>', 0)->get();
        $glassware = LIDGlassware::where('quantity', '>', 0)->get();
        $equipment = LIDEquipment::where('quantity', '>', 0)->get();

        // Create lookup arrays for available quantities
        $chemicalStocks = [];
        foreach ($chemicals as $chemical) {
            $chemicalStocks[$chemical->name] = [
                'available_quantity' => $chemical->available_quantity,
                'unit' => $chemical->unit,
            ];
        }

        $pdeaChemicalStocks = [];
        foreach ($pdeaChemicals as $chemical) {
            $pdeaChemicalStocks[$chemical->name] = [
                'available_quantity' => $chemical->available_quantity,
                'unit' => $chemical->unit,
            ];
        }

        $glasswareStocks = [];
        foreach ($glassware as $item) {
            $glasswareStocks[$item->name] = [
                'available_quantity' => $item->available_quantity,
                'unit' => $item->unit,
            ];
        }

        $equipmentStocks = [];
        foreach ($equipment as $item) {
            $equipmentStocks[$item->name] = [
                'available_quantity' => $item->available_quantity,
                'unit' => $item->unit,
            ];
        }

        return view('LID.reservations.edit', compact(
            'reservation',
            'chemicals',
            'pdeaChemicals',
            'glassware',
            'equipment',
            'chemicalStocks',
            'pdeaChemicalStocks',
            'glasswareStocks',
            'equipmentStocks'
        ));
    }

    public function update(Request $request, $id)
    {
        $reservation = Reservation::findOrFail($id);

        // Store original state to check for changes
        $originalItemsReturned = $reservation->items_returned;

        // Validate the request
        $validator = Validator::make($request->all(), [
            'borrower_name' => 'required|string|max:255',
            'date_requested' => 'required|date',
            'term' => 'required|string',
            'purpose' => 'required|array',
            'purpose.*' => 'string|in:Laboratory Activity,Student Thesis,Faculty Research',
            'room_no' => 'required|string|max:100',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
            'number_of_groups' => 'required|integer|min:1|max:20',
            'time' => 'required|string',
            'program' => 'nullable|string|max:255',
            'year_section' => 'nullable|string|max:100',
            'subject_code' => 'nullable|string|max:100',
            'subject_description' => 'nullable|string|max:255',
            'activity_title' => 'nullable|string|max:255',
            'activity_no' => 'nullable|string|max:100',
            'status' => 'required|string|in:Pending,Approved,Rejected,Cancelled',
            'items_released' => 'boolean',
            'items_returned' => 'boolean',
        ]);

        if ($validator->fails()) {
            return redirect()
                ->back()
                ->withErrors($validator)
                ->withInput();
        }

        // Update reservation
        $reservationData = $request->only([
            'borrower_name', 'date_requested', 'term', 'room_no',
            'start_date', 'end_date', 'number_of_groups', 'time',
            'program', 'year_section', 'subject_code', 'subject_description',
            'activity_title', 'activity_no', 'status',
        ]);

        $reservationData['purpose'] = implode(', ', $request->purpose);

        // Handle items released status
        $itemsReleased = $request->boolean('items_released');
        if ($itemsReleased && ! $reservation->items_released) {
            $reservationData['items_released_at'] = now();
        } elseif (! $itemsReleased) {
            $reservationData['items_released_at'] = null;
        }
        $reservationData['items_released'] = $itemsReleased;

        // Handle items returned status
        $itemsReturned = $request->boolean('items_returned');
        if ($itemsReturned && ! $reservation->items_returned) {
            $reservationData['items_returned_at'] = now();
        } elseif (! $itemsReturned) {
            $reservationData['items_returned_at'] = null;
        }
        $reservationData['items_returned'] = $itemsReturned;

        // For student reservations, update additional fields
        if ($reservation->borrower_type === 'Student') {
            $reservationData['student_id'] = $request->student_id;
            $reservationData['group_number'] = $request->group_number;
            $reservationData['email'] = $request->email;
            $reservationData['contact_number'] = $request->contact_number;
        }

        $reservation->update($reservationData);

        // *** NEW LOGIC: Check if items have just been marked as returned ***
        // This ensures inventory is only updated once, when the checkbox is first ticked.
        if (! $originalItemsReturned && $itemsReturned) {
            $this->returnItemsToInventory($reservation);
        }

        // Update chemical requests
        $this->updateChemicalRequests($request, $reservation, 'nonregulated');
        $this->updateChemicalRequests($request, $reservation, 'regulated');

        // Update item requests
        $this->updateItemRequests($request, $reservation, 'glassware');
        $this->updateItemRequests($request, $reservation, 'equipment');

        return redirect()
            ->route('lid.reservations.index')
            ->with('success', 'Reservation updated successfully!');
    }

    /**
     * Return items to inventory when marked as returned.
     * This increases the main quantity and decreases the reserved quantity.
     *
     * @param  Reservation  $reservation
     */
    private function returnItemsToInventory(Reservation $reservation)
    {
        // Return glassware to inventory
        $glasswareRequests = GlasswareRequest::where('reservation_id', $reservation->id)->get();
        foreach ($glasswareRequests as $request) {
            $item = LIDGlassware::where('name', $request->name)->first();
            if ($item) {
                // Increase available quantity
                $item->quantity += $request->total_quantity;
                // Decrease reserved quantity
                $item->reserved_quantity = max(0, $item->reserved_quantity - $request->total_quantity);
                $item->save();
                Log::info("Returned {$request->total_quantity} of {$request->name} (Glassware) to inventory.");
            }
        }

        // Return equipment to inventory
        $equipmentRequests = EquipmentRequest::where('reservation_id', $reservation->id)->get();
        foreach ($equipmentRequests as $request) {
            $item = LIDEquipment::where('name', $request->name)->first();
            if ($item) {
                // Increase available quantity
                $item->quantity += $request->total_quantity;
                // Decrease reserved quantity
                $item->reserved_quantity = max(0, $item->reserved_quantity - $request->total_quantity);
                $item->save();
                Log::info("Returned {$request->total_quantity} of {$request->name} (Equipment) to inventory.");
            }
        }
    }

    private function updateChemicalRequests(Request $request, Reservation $reservation, string $requestType)
    {
        $modelClass = $requestType === 'nonregulated' ? ChemicalRequest::class : PDEARequest::class;
        $stockModel = $requestType === 'nonregulated' ? LIDChemical::class : LIDPDEAChemical::class;

        // Get existing request IDs
        $existingIds = $modelClass::where('reservation_id', $reservation->id)->pluck('id')->toArray();

        // Process submitted requests
        $submittedIds = [];

        if ($request->has($requestType)) {
            foreach ($request->$requestType as $index => $chem) {
                if (empty($chem['name'])) {
                    continue;
                }

                // Calculate total quantity required
                $quantityPerGroup = floatval($chem['quantity_per_group']);
                $totalRequired = $quantityPerGroup * $reservation->number_of_groups;

                // Get chemical details from database to get the unit
                $chemical = $stockModel::where('name', $chem['name'])->first();
                $unit = $chemical ? $chemical->unit : '';

                // Prepare chemical data
                $chemicalData = [
                    'name' => $chem['name'],
                    'is_solution' => isset($chem['is_solution']) ? (int) $chem['is_solution'] : 0,
                    'has_concentration' => isset($chem['has_concentration']) ? (int) $chem['has_concentration'] : 0,
                    'concentration_value' => isset($chem['concentration_value']) ? (float) $chem['concentration_value'] : 0,
                    'concentration_unit' => $chem['concentration_unit'] ?? '',
                    'volume' => 0,
                    'volume_unit' => '',
                    'quantity_per_group' => $quantityPerGroup,
                    'total_quantity' => $totalRequired,
                    'unit' => $unit, // Use the unit from the database
                    'instruction' => $chem['instruction'] ?? null,
                ];

                if (isset($chem['id'])) {
                    // Update existing request
                    $chemicalRequest = $modelClass::find($chem['id']);
                    if ($chemicalRequest) {
                        $chemicalRequest->update($chemicalData);
                        $submittedIds[] = $chemicalRequest->id;
                    }
                } else {
                    // Create new request
                    $chemicalData['reservation_id'] = $reservation->id;
                    $newRequest = $modelClass::create($chemicalData);
                    $submittedIds[] = $newRequest->id;
                }
            }
        }

        // Delete requests that were not submitted
        $toDelete = array_diff($existingIds, $submittedIds);
        if (! empty($toDelete)) {
            $modelClass::whereIn('id', $toDelete)->delete();
        }
    }

    private function updateItemRequests(Request $request, Reservation $reservation, string $requestType)
    {
        $modelClass = $requestType === 'glassware' ? GlasswareRequest::class : EquipmentRequest::class;
        $stockModel = $requestType === 'glassware' ? LIDGlassware::class : LIDEquipment::class;

        // Get existing request IDs
        $existingIds = $modelClass::where('reservation_id', $reservation->id)->pluck('id')->toArray();

        // Process submitted requests
        $submittedIds = [];

        if ($request->has($requestType)) {
            foreach ($request->$requestType as $index => $item) {
                if (empty($item['name'])) {
                    continue;
                }

                // Calculate total quantity required
                $quantityPerGroup = floatval($item['quantity_per_group']);
                $totalRequired = $quantityPerGroup * $reservation->number_of_groups;

                // Get item details from database to get the unit
                $stockItem = $stockModel::where('name', $item['name'])->first();
                $unit = $stockItem ? $stockItem->unit : '';

                // Prepare item data
                $itemData = [
                    'name' => $item['name'],
                    'type' => $item['type'] ?? '',
                    'quantity_per_group' => $quantityPerGroup,
                    'total_quantity' => $totalRequired,
                    'unit' => $unit, // Use the unit from the database
                    'instruction' => $item['instruction'] ?? null,
                ];

                if (isset($item['id'])) {
                    // Update existing request
                    $itemRequest = $modelClass::find($item['id']);
                    if ($itemRequest) {
                        $itemRequest->update($itemData);
                        $submittedIds[] = $itemRequest->id;
                    }
                } else {
                    // Create new request
                    $itemData['reservation_id'] = $reservation->id;
                    $newRequest = $modelClass::create($itemData);
                    $submittedIds[] = $newRequest->id;
                }
            }
        }

        // Delete requests that were not submitted
        $toDelete = array_diff($existingIds, $submittedIds);
        if (! empty($toDelete)) {
            $modelClass::whereIn('id', $toDelete)->delete();
        }
    }
}
