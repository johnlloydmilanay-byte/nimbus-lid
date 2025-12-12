<?php

namespace App\Http\Controllers\Cashiering;

use App\Http\Controllers\Controller;
use App\Models\Admission\PseAdmission;
use App\Models\Admission\JhsAdmission;
use App\Models\Admission\ShsAdmission;
use App\Models\Admission\CollegeAdmission;
use App\Models\System\SrmPaymentCodeManagement;
use App\Models\System\SysPaymentCodeTypes;
use App\Models\System\SysPaymentTypes;
use App\Models\System\SysTerms;
use App\Models\System\SrmPayment;
use Illuminate\Http\Request;

class CollectionsController extends Controller
{
    public function index()
    {
        $paymentcodes = SysPaymentCodeTypes::where('is_active', 1)->get();
        $terms = SysTerms::where('is_active', 1)->get();
        
        $firstCode = $paymentcodes->first();
        $paymentfor = $firstCode
            ? SrmPaymentCodeManagement::where('paymentcode_id', $firstCode->id)
                ->where('is_active', 1)
                ->orderBy('name')
                ->get()
            : collect();

        $paymenttype = SysPaymentTypes::where('is_active', 1)->get();

        return view('Cashiering.Collections.index', compact('paymentcodes', 'terms', 'paymentfor', 'paymenttype'));
    }

    public function getPaymentFor($paymentcode_id)
    {
        $paymentfor = SrmPaymentCodeManagement::where('paymentcode_id', $paymentcode_id)
            ->where('is_active', 1)
            ->orderBy('name')
            ->get(['id', 'name']);

        return response()->json($paymentfor);
    }

    public function search(Request $request)
    {
        $searchTerm = trim($request->get('search_term'));

        // List of all admission models
        $admissionModels = [
            PseAdmission::class,
            JhsAdmission::class,
            ShsAdmission::class,
            CollegeAdmission::class,
        ];

        $application = null;
        $applications = [];

        // First, try exact match by application number
        foreach ($admissionModels as $model) {
            $application = $model::where('application_number', $searchTerm)->first();
            if ($application) {
                break; // stop at the first match
            }
        }

        if ($application) {
            if ($application->status == 1) {
                return response()->json([
                    'success' => false,
                    'already_paid' => true,
                    'message' => 'Applicant already paid.',
                ]);
            }

            return response()->json([
                'success' => true,
                'payor_name' => $application->lastname.', '.$application->firstname.' '.$application->middlename,
                'application_number' => $application->application_number,
            ]);
        }

        foreach ($admissionModels as $model) {
            $results = $model::where('status', 0)
                ->where(function ($query) use ($searchTerm) {
                    $query->where('firstname', 'LIKE', "%{$searchTerm}%")
                        ->orWhere('middlename', 'LIKE', "%{$searchTerm}%")
                        ->orWhere('lastname', 'LIKE', "%{$searchTerm}%")
                        ->orWhereRaw("CONCAT(firstname, ' ', lastname) LIKE ?", ["%{$searchTerm}%"])
                        ->orWhereRaw("CONCAT(lastname, ', ', firstname) LIKE ?", ["%{$searchTerm}%"]);
                })
                ->get();

            foreach ($results as $result) {
                $applications[] = [
                    'application_number' => $result->application_number,
                    'full_name' => $result->lastname.', '.$result->firstname.' '.$result->middlename,
                    'level' => $this->getLevelFromModel($model),
                ];
            }
        }

        if (! empty($applications)) {
            return response()->json([
                'success' => true,
                'multiple_results' => true,
                'applications' => $applications,
                'count' => count($applications),
            ]);
        }

        return response()->json([
            'success' => false,
            'already_paid' => false,
            'message' => 'No unpaid applicants found for: '.$searchTerm,
        ]);
    }

    private function getLevelFromModel($model)
    {
        $levels = [
            PseAdmission::class => 'PSE',
            JhsAdmission::class => 'JHS',
            ShsAdmission::class => 'SHS',
            CollegeAdmission::class => 'College',
        ];

        return $levels[$model] ?? 'Unknown';
    }

    public function store(Request $request)
    {
        // Validate form inputs
        $validated = $request->validate([
            'year'               => 'required|string|max:20',
            'term_id'            => 'required|string|max:50',
            'application_number' => 'required|string|max:50',
            'payor_name'         => 'required|string|max:255',
            'payment_code_type_id'=> 'required|string|max:50',
            'payment_for_id'     => 'required|string|max:50',
            'amount_due'         => 'nullable|numeric',
            'amount_to_pay'      => 'required|numeric|min:1',
            'amount_tendered'    => 'required|numeric|min:1',
            'payment_type_id'    => 'required|string|max:50',
            'remarks'            => 'nullable|string',
        ]);

        // Create record in collections table
        $collection = SrmPayment::create([
            'year'               => $validated['year'],
            'term_id'            => $validated['term_id'],
            'or_number'          => null,
            'application_number' => $validated['application_number'],
            'payor_name'         => $validated['payor_name'],
            'payment_code_type_id'=> $validated['payment_code_type_id'],
            'payment_for_id'     => $validated['payment_for_id'],
            'amount_due'         => $validated['amount_due'] ?? 0,
            'amount_to_pay'      => $validated['amount_to_pay'],
            'amount_tendered'    => $validated['amount_tendered'],
            'change'             => ($validated['amount_tendered'] - $validated['amount_to_pay']),
            'payment_type_id'    => $validated['payment_type_id'],
            'remarks'            => $validated['remarks'] ?? null,
            'cashier_id'         => auth()->user()->user_id,
            'created_by'         => auth()->user()->user_id,
        ]);

        // Update OR number
        $collection->update([
            'or_number' => 'C' . auth()->id() . '-AQS-' . $collection->id,
        ]);

        // List of all admission models
        $admissionModels = [
            PseAdmission::class,
            JhsAdmission::class,
            ShsAdmission::class,
            CollegeAdmission::class,
        ];

        // Update status for the correct admission record
        foreach ($admissionModels as $model) {
            $updated = $model::where('application_number', $validated['application_number'])
                            ->update(['status' => 1]);
            if ($updated) {
                break; // stop after updating the first match
            }
        }

        return redirect()->route('cashiering.collections.receipt', $collection->or_number)->with([
            'show_success_modal' => true,
        ]);
    }

    public function receipt($or_number)
    {
        $collection = SrmPayment::where('or_number', $or_number)->first();

        if (!$collection) {
            return redirect()->route('cashiering.collections.index')->with('error', 'Payment not found.');
        }

        return view('Cashiering.Collections.receipt', compact('collection'));
    }
}
