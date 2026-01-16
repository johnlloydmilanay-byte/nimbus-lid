<?php

namespace App\Models\PPFMO;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class ServiceRequest extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'request_number',
        'date_reported',
        'request_type',
        'specific_report',
        'location',
        'remarks',
        'status',
        'reported_by',
        'received_by',
        'endorsed_to',
        'attested_by',
        'date_completed'
    ];

    protected $casts = [
        'date_reported' => 'date',
        'date_completed' => 'date'
    ];

    // Request type options
    public const REQUEST_TYPES = [
        'Masonry' => 'Masonry',
        'Roofing' => 'Roofing',
        'Electrical' => 'Electrical',
        'Aluminum and Glass Concerns' => 'Aluminum and Glass Concerns',
        'Carpentry' => 'Carpentry',
        'Plumbing' => 'Plumbing'
    ];

    // Specific reports based on request types
    public const SPECIFIC_REPORTS = [
        'Masonry' => [
            'Cracked walls',
            'Damaged floors',
            'Broken tiles',
            'Concrete cracks',
            'Wall plaster issues',
            'Foundation problems'
        ],
        'Roofing' => [
            'Roof leaks',
            'Damaged roof sheets',
            'Gutter problems',
            'Roof sagging',
            'Missing shingles',
            'Roof drainage issues'
        ],
        'Electrical' => [
            'Power outage',
            'Faulty wiring',
            'Broken switches',
            'Circuit breaker issues',
            'Lighting problems',
            'Electrical safety concerns'
        ],
        'Aluminum and Glass Concerns' => [
            'Broken windows',
            'Damaged aluminum frames',
            'Door glass issues',
            'Window lock problems',
            'Glass cracks',
            'Aluminum corrosion'
        ],
        'Carpentry' => [
            'Damaged doors',
            'Broken cabinets',
            'Wood rot',
            'Furniture repair',
            'Wooden floor issues',
            'Carpentry finishing'
        ],
        'Plumbing' => [
            'Water leaks',
            'Clogged drains',
            'Toilet problems',
            'Pipe bursts',
            'Water pressure issues',
            'Sewage problems'
        ]
    ];

    // Status options
    public const STATUSES = [
        'Pending' => 'Pending',
        'In Progress' => 'In Progress',
        'Completed' => 'Completed',
        'Cancelled' => 'Cancelled'
    ];

    // Generate unique request number
    public static function generateRequestNumber(): string
    {
        $year = date('Y');
        $month = date('m');
        $latest = self::whereYear('created_at', $year)
            ->whereMonth('created_at', $month)
            ->latest()
            ->first();

        $number = $latest ? (int) substr($latest->request_number, -4) + 1 : 1;
        
        return 'SR-' . $year . $month . '-' . str_pad($number, 4, '0', STR_PAD_LEFT);
    }
}