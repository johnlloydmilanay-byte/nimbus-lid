<?php

namespace App\Http\Controllers\Accounting;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\System\SysTerms;
use App\Models\System\SysAcademicGroups;
use App\Models\System\SrmFeesManagement;
use App\Models\System\SrmFeesPost;
use App\Models\System\SrmInstallmentScheme;
use App\Models\System\SrmInstallmentPaymentSchedule;
use App\Models\System\SrmInstallmentFeesBreakdown;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Carbon;

class InstallmentSchemeController extends Controller
{

    public function index(Request $request)
    {
        $moduleName = 'Installment Schemes Management';
        $title = 'Installment Scheme';
        $manageTitle = 'Payment Schedule';
        $manageTitle2 = 'Fees Breakdown';

        $terms = SysTerms::where('is_active', 1)->get();
        $acadorder = [3, 2, 6, 1, 5, 4, 8, 9, 10, 7];
        $academicgroup = SysAcademicGroups::where('is_active', 1)
            ->orderByRaw("FIELD(id, " . implode(',', $acadorder) . ")")
            ->get();

        $schemes = SrmInstallmentScheme::with('academicGroup')
            ->where('is_active', 1)
            ->when($request->year, fn($q) => $q->where('year', $request->year))
            ->when($request->term_id, fn($q) => $q->where('term_id', $request->term_id))
            ->when($request->academicgroup_id, fn($q) => $q->where('academicgroup_id', $request->academicgroup_id))
            ->get();

        $manageScheme = null;
        $schedules = collect();
        $rawSchedules = collect(); 

        $mainFeeDetails = collect();
        $miscFeeDetails = collect();
        $otherFeeDetails = collect();

        if ($request->filled('installment_scheme_id')) {

            $manageScheme = SrmInstallmentScheme::where('is_active', 1)->find($request->installment_scheme_id);

            if ($manageScheme) {

                // PAYMENT SCHEDULE
                $rawSchedules = SrmInstallmentPaymentSchedule::where('installment_scheme_id', $manageScheme->id)
                    ->where('is_active', 1)
                    ->orderBy('order')
                    ->get();

                for ($i = 1; $i <= $manageScheme->payment_count; $i++) {
                    $schedule = $rawSchedules->firstWhere('order', $i);

                    $schedules->push((object)[
                        'order' => $i,
                        'description' => $schedule->description ?? ($i == 1 ? 'Due upon enrollment' : ''),
                        'date_from' => $schedule->date_from ?? Carbon::today()->format('Y-m-d'),
                        'date_to' => $schedule->date_to ?? Carbon::today()->format('Y-m-d'),
                        'period_start' => $schedule->period_start ?? '',
                        'period_end' => $schedule->period_end ?? '',
                        'exam' => $schedule->exam ?? ''
                    ]);
                }

                // LOAD SAVED FEES BREAKDOWN
                $savedBreakdown = SrmInstallmentFeesBreakdown::where('installment_scheme_id', $manageScheme->id)
                    ->where('is_active', 1)
                    ->get()
                    ->map(function ($item) use ($manageScheme) {
                        $id = $item->fee_management_id ?? $item->fee_post_id; // take fee_management_id if exists, else fee_post_id
                        return [
                            'id' => $id,
                            'payment_count' => $item->payment_count,
                            'rate' => $item->rate
                        ];
                    })
                    ->groupBy('id')
                    ->map(function ($items) use ($manageScheme) {
                        $rates = array_fill(0, $manageScheme->payment_count, 0);
                        foreach ($items as $item) {
                            $index = $item['payment_count'] - 1;
                            $rates[$index] = $item['rate'];
                        }
                        return $rates;
                    });


                // MAIN FEES
                $fees = SrmFeesManagement::where('is_active', 1)->get();
                foreach ($fees as $f) {
                    $rates = $savedBreakdown[$f->id] ?? array_fill(0, $manageScheme->payment_count, 0);

                    $mainFeeDetails->push([
                        'id' => $f->id,
                        'name' => $f->name,
                        'rates' => $rates,
                        'type' => 'Main'
                    ]);
                }

                // MISC FEES
                $miscFees = SrmFeesPost::where('srm_fees_post.is_active', 1)
                    ->where('srm_fees_post.year', $manageScheme->year)
                    ->where('srm_fees_post.term', $manageScheme->term_id)
                    ->where('srm_fees_post.academicgroup_id', $manageScheme->academicgroup_id)
                    ->where('srm_fees_post.fee_types_id', 1)
                    ->join('sys_fees_types', 'srm_fees_post.fee_types_id', '=', 'sys_fees_types.id')
                    ->where('sys_fees_types.fee_management_id', 2)
                    ->distinct()
                    ->select('srm_fees_post.id', 'srm_fees_post.fee_name')
                    ->get();

                foreach ($miscFees as $misc) {
                    // For misc fees, use fee_post_id for savedBreakdown
                    $savedRates = $savedBreakdown[$misc->id] ?? array_fill(0, $manageScheme->payment_count, 0);

                    $miscFeeDetails->push([
                        'id' => $misc->id,
                        'name' => $misc->fee_name,
                        'rates' => $savedRates,
                        'type' => 'Misc'
                    ]);
                }

                // OTHER FEES
                $otherFees = SrmFeesPost::where('srm_fees_post.is_active', 1)
                    ->where('srm_fees_post.year', $manageScheme->year)
                    ->where('srm_fees_post.term', $manageScheme->term_id)
                    ->where('srm_fees_post.academicgroup_id', $manageScheme->academicgroup_id)
                    ->where('srm_fees_post.fee_types_id', 6)
                    ->join('sys_fees_types', 'srm_fees_post.fee_types_id', '=', 'sys_fees_types.id')
                    ->where('sys_fees_types.fee_management_id', 3)
                    ->distinct()
                    ->select('srm_fees_post.id', 'srm_fees_post.fee_name')
                    ->get();

                foreach ($otherFees as $other) {
                    $savedRates = $savedBreakdown[$other->id] ?? array_fill(0, $manageScheme->payment_count, 0);

                    $otherFeeDetails->push([
                        'id' => $other->id,
                        'name' => $other->fee_name,
                        'rates' => $savedRates,
                        'type' => 'Other'
                    ]);
                }
            }
        }

        return view('Accounting.InstallmentScheme.index', compact(
            'moduleName','title','manageTitle','manageTitle2','terms','academicgroup','schemes','manageScheme','schedules','mainFeeDetails','miscFeeDetails','otherFeeDetails','rawSchedules'
        ));
    }

    public function store(Request $request)
    {
        $request->validate([
            'academicgroup_id'  => 'required|exists:sys_academicgroups,id',
            'scheme_name'       => 'required|string|max:255',
            'payment_count'     => 'required|integer|min:1|max:10',
            'installment_fee'   => 'required|numeric|min:0',
        ]);

        SrmInstallmentScheme::create([
            'year'             => $request->year,
            'term_id'          => $request->term_id,
            'academicgroup_id' => $request->academicgroup_id,
            'scheme_name'      => $request->scheme_name,
            'payment_count'    => $request->payment_count,
            'installment_fee'  => $request->installment_fee,
            'is_active'        => 1,
            'created_by'       => Auth::user()->user_id ?? null,
        ]);

        return redirect()->back()->with([
            'message' => 'Installment Scheme added successfully!',
            'message_type' => 'success'
        ]);
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'academicgroup_id'  => 'required|exists:sys_academicgroups,id',
            'scheme_name'       => 'required|string|max:255',
            'payment_count'     => 'required|integer|min:1|max:10',
            'installment_fee'   => 'required|numeric|min:0',
        ]);

        $scheme = SrmInstallmentScheme::findOrFail($id);

        $scheme->update([
            'academicgroup_id' => $request->academicgroup_id,
            'scheme_name'      => $request->scheme_name,
            'payment_count'    => $request->payment_count,
            'installment_fee'  => $request->installment_fee,
            'updated_by'       => Auth::user()->user_id ?? null,
        ]);

        return redirect()->back()->with([
            'message' => 'Installment Scheme updated successfully!',
            'message_type' => 'success'
        ]);
    }

    public function store_manage(Request $request)
    {
        $request->validate([
            'description' => 'required|array',
            'date_from'   => 'required|array',
            'date_to'     => 'required|array',
            'period_start'=> 'nullable|array',
            'period_end'  => 'nullable|array',
            'exam'        => 'nullable|array',
            'installment_scheme_id' => 'required|exists:srm_installment_scheme,id'
        ]);

        $installmentSchemeId = $request->input('installment_scheme_id');

        foreach ($request->description as $orderKey => $description) {
            $order = (int)$orderKey;

            // Check if schedule exists
            $schedule = SrmInstallmentPaymentSchedule::firstOrNew([
                'installment_scheme_id' => $installmentSchemeId,
                'order' => $order
            ]);

            $schedule->description  = $order === 1 ? 'Due upon enrollment' : $description;
            $schedule->date_from    = $request->date_from[$order] ?? now();
            $schedule->date_to      = $request->date_to[$order] ?? now();
            $schedule->period_start = $request->period_start[$order] ?? null;
            $schedule->period_end   = $request->period_end[$order] ?? null;
            $schedule->exam         = $request->exam[$order] ?? null;

            // Set created_by if new, updated_by if exists
            if (!$schedule->exists) {
                $schedule->created_by = Auth::user()->user_id ?? null;
            } else {
                $schedule->updated_by = Auth::user()->user_id ?? null;
            }

            $schedule->save();
        }

        return redirect()->back()->with([
            'message' => 'Payment schedule successfully saved.',
            'message_type' => 'success'
        ]);
    }

    public function store_fees(Request $request, $id)
    {
        $scheme = SrmInstallmentScheme::findOrFail($id);

        foreach ($request->rates as $feeId => $rates) {
            foreach ($rates as $index => $rate) {
                $payment_no = $index + 1;

                $data = [
                    'installment_scheme_id' => $id,
                    'payment_count' => $payment_no,
                ];

                // Determine which foreign key to set
                if (is_numeric($feeId) && SrmFeesManagement::find($feeId)) {
                    $data['fee_management_id'] = $feeId;
                    $data['fee_post_id'] = null;
                } elseif ($feePost = SrmFeesPost::find($feeId)) {
                    $data['fee_post_id'] = $feeId;
                    $data['fee_management_id'] = null;
                } else {
                    $data['fee_management_id'] = null;
                    $data['fee_post_id'] = null;
                }

                $breakdown = SrmInstallmentFeesBreakdown::updateOrCreate(
                    $data, // match conditions
                    [
                        'rate' => $rate,
                        'created_by' => Auth::user()->user_id ?? null,
                        'updated_by' => Auth::user()->user_id ?? null
                    ]
                );
            }
        }

        return back()->with([
            'message' => 'Fees Breakdown Saved!',
            'message_type' => 'success'
        ]);
    }

    // delete function, delete also in payment sched & fees breakdown
    public function destroy($id)
    {
        $scheme = SrmInstallmentScheme::findOrFail($id);

        SrmInstallmentPaymentSchedule::where('installment_scheme_id', $scheme->id)
            ->update([
                'is_active' => 0,
                'deleted_by' => Auth::user()->user_id ?? null
            ]);

        SrmInstallmentFeesBreakdown::where('installment_scheme_id', $scheme->id)
            ->update([
                'is_active' => 0,
                'deleted_by' => Auth::user()->user_id ?? null
            ]);

        SrmInstallmentPaymentSchedule::where('installment_scheme_id', $scheme->id)->delete();
        SrmInstallmentFeesBreakdown::where('installment_scheme_id', $scheme->id)->delete();

        $scheme->update([
            'is_active' => 0,
            'deleted_by' => Auth::user()->user_id ?? null
        ]);

        $scheme->delete();

        return redirect()->back()->with([
            'message' => 'Installment Scheme, its payment schedules, and saved fees deleted successfully!',
            'message_type' => 'success'
        ]);
    }

}
