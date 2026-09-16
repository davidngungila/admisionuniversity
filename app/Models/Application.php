<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;

#[Fillable([
    'applicant_id',
    'academic_year_id',
    'admission_window_id',
    'application_number',
    'status',
    'current_step',
    'progress_percentage',
    'submitted_at',
    'acceptance_confirmed',
    'acceptance_confirmed_at',
])]
class Application extends Model
{
    use \Illuminate\Database\Eloquent\Factories\HasFactory;

    public const STATUS_DRAFT                    = 'DRAFT';
    public const STATUS_PAYMENT_PENDING          = 'PAYMENT_PENDING';
    public const STATUS_PAYMENT_CONFIRMED        = 'PAYMENT_CONFIRMED';
    public const STATUS_APPLICATION_IN_PROGRESS  = 'APPLICATION_IN_PROGRESS';
    public const STATUS_SUBMITTED                = 'SUBMITTED';
    public const STATUS_UNDER_REVIEW             = 'UNDER_REVIEW';
    public const STATUS_ELIGIBILITY_CHECKED      = 'ELIGIBILITY_CHECKED';
    public const STATUS_ELIGIBLE                 = 'ELIGIBLE';
    public const STATUS_SELECTED                 = 'SELECTED';
    public const STATUS_ADMITTED                 = 'ADMITTED';

    public const STATUS_PAYMENT_FAILED           = 'PAYMENT_FAILED';
    public const STATUS_DOCUMENT_CORRECTION      = 'DOCUMENT_CORRECTION_REQUIRED';
    public const STATUS_INELIGIBLE               = 'INELIGIBLE';
    public const STATUS_REJECTED                 = 'REJECTED';
    public const STATUS_WAITLISTED               = 'WAITLISTED';
    public const STATUS_WITHDRAWN                = 'WITHDRAWN';

    protected function casts(): array
    {
        return [
            'progress_percentage' => 'integer',
            'submitted_at' => 'datetime',
            'acceptance_confirmed' => 'boolean',
            'acceptance_confirmed_at' => 'datetime',
        ];
    }

    public function applicant(): BelongsTo
    {
        return $this->belongsTo(Applicant::class);
    }

    public function academicYear(): BelongsTo
    {
        return $this->belongsTo(AcademicYear::class);
    }

    public function admissionWindow(): BelongsTo
    {
        return $this->belongsTo(AdmissionWindow::class);
    }

    public function stepCompletions(): HasMany
    {
        return $this->hasMany(ApplicationStepCompletion::class);
    }

    public function selectedProgrammes(): HasMany
    {
        return $this->hasMany(ApplicationProgramme::class);
    }

    public function documents(): HasMany
    {
        return $this->hasMany(ApplicationDocument::class);
    }

    public function statusHistory(): HasMany
    {
        return $this->hasMany(ApplicationStatusHistory::class);
    }

    public function payments(): HasMany
    {
        return $this->hasMany(Payment::class);
    }

    public function academicResults(): HasMany
    {
        return $this->hasMany(AcademicResult::class);
    }

    public function selectionResults(): HasMany
    {
        return $this->hasMany(SelectionResult::class);
    }

    public function admissionLetters(): HasMany
    {
        return $this->hasMany(AdmissionLetter::class);
    }

    public function programmes(): HasManyThrough
    {
        return $this->hasManyThrough(Programme::class, ApplicationProgramme::class);
    }

    public function totalCompletedSteps(): int
    {
        return $this->stepCompletions()->whereNotNull('completed_at')->count();
    }

    public function totalWorkflowSteps(): int
    {
        return $this->admissionWindow->admissionLevel->workflowSteps()->active()->count();
    }

    public function recalculateProgress(): void
    {
        $completed = $this->totalCompletedSteps();
        $total     = max(1, $this->totalWorkflowSteps());
        $percentage = (int) round(($completed / $total) * 100);

        $this->update([
            'progress_percentage' => $percentage,
        ]);
    }

    public function advanceStatus(string $newStatus, ?int $changedBy = null, ?string $remarks = null): void
    {
        $oldStatus = $this->status;

        $this->update(['status' => $newStatus]);

        ApplicationStatusHistory::create([
            'application_id' => $this->id,
            'old_status'     => $oldStatus,
            'new_status'     => $newStatus,
            'changed_by'     => $changedBy,
            'remarks'        => $remarks,
        ]);
    }

    /**
     * Build a professional application number:
     * UNI-2627-R02-BSC-000125
     *  UNI   → Institution / OAS prefix
     *  2627  → Academic year 2026/2027
     *  R02   → Admission round 2
     *  BSC   → Level code (Bachelor)
     *  000125→ Application sequence (6 digits)
     */
    public static function generateApplicationNumber(?self $application = null): string
    {
        $prefix   = 'UNI';
        $yearCode = date('y').str_pad((string) (((int) date('Y') + 1) % 100), 2, '0', STR_PAD_LEFT);
        $round    = 1;
        $level    = 'GEN';

        if ($application) {
            $application->loadMissing(['academicYear', 'admissionWindow.applicationRound', 'admissionWindow.admissionLevel']);

            $yearName = (string) ($application->academicYear?->name ?? '');
            if (preg_match('/(\d{4})\D+(\d{2,4})/', $yearName, $m)) {
                $end      = strlen($m[2]) === 4 ? substr($m[2], -2) : $m[2];
                $yearCode = substr($m[1], -2).$end;
            }

            $round = (int) ($application->admissionWindow?->applicationRound?->round_number ?? 1);
            $level = strtoupper((string) ($application->admissionWindow?->admissionLevel?->code ?? 'GEN'));
        }

        $query = static::query()->whereNotNull('application_number');
        if ($application?->academic_year_id) {
            $query->where('academic_year_id', $application->academic_year_id);
        }

        $seq = $query->count() + 1;

        do {
            $number = sprintf('%s-%s-R%02d-%s-%06d', $prefix, $yearCode, $round, $level, $seq);
            $seq++;
        } while (static::where('application_number', $number)->exists());

        return $number;
    }
}