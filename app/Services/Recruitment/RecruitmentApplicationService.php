<?php

namespace App\Services\Recruitment;

use App\Models\Recruitment\RecruitmentActivityLog;
use App\Models\Recruitment\RecruitmentApplication;
use App\Models\Recruitment\RecruitmentDivision;
use App\Models\Recruitment\RecruitmentPeriod;
use App\Models\Recruitment\RecruitmentScreening;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

final class RecruitmentApplicationService
{
    /**
     * @param  array<string, mixed>  $filters
     */
    public function paginate(array $filters = [], int $page = 1, int $perPage = 20): LengthAwarePaginator
    {
        $query = RecruitmentApplication::query()
            ->with(['primaryDivision:id,name,code', 'secondaryDivision:id,name,code', 'period:id,name'])
            ->orderByDesc('submitted_at');

        if (! empty($filters['period_id'])) {
            $query->where('recruitment_period_id', $filters['period_id']);
        }

        if (! empty($filters['division_id'])) {
            $divisionId = $filters['division_id'];
            $query->where(function ($q) use ($divisionId): void {
                $q->where('primary_division_id', $divisionId)
                    ->orWhere('secondary_division_id', $divisionId);
            });
        }

        if (! empty($filters['stage'])) {
            $query->where('stage', $filters['stage']);
        }

        if (! empty($filters['semester'])) {
            $query->where('semester', (int) $filters['semester']);
        }

        if (! empty($filters['search'])) {
            $search = (string) $filters['search'];
            $query->where(function ($q) use ($search): void {
                $q->where('full_name', 'like', "%{$search}%")
                    ->orWhere('nim', 'like', "%{$search}%")
                    ->orWhere('registration_number', 'like', "%{$search}%");
            });
        }

        return $query->paginate($perPage, ['*'], 'page', $page);
    }

    /**
     * @return array<string, mixed>
     */
    public function toListArray(RecruitmentApplication $application): array
    {
        return [
            'id' => $application->id,
            'registration_number' => $application->registration_number,
            'full_name' => $application->full_name,
            'nim' => $application->nim,
            'semester' => $application->semester,
            'stage' => $application->stage->value,
            'stage_label' => $application->stage->label(),
            'result' => $application->result->value,
            'result_label' => $application->result->label(),
            'revision_required' => $application->revision_required,
            'submitted_at' => $application->submitted_at?->toIso8601String(),
            'primary_division' => $application->primaryDivision ? [
                'id' => $application->primaryDivision->id,
                'name' => $application->primaryDivision->name,
            ] : null,
            'period' => $application->period ? [
                'id' => $application->period->id,
                'name' => $application->period->name,
            ] : null,
        ];
    }

    /**
     * @return array<string, mixed>
     */
    public function toShowArray(RecruitmentApplication $application): array
    {
        $application->loadMissing([
            'period',
            'primaryDivision',
            'secondaryDivision',
            'document',
            'screenings.actor',
            'activityLogs.actor',
        ]);

        return [
            'id' => $application->id,
            'registration_number' => $application->registration_number,
            'full_name' => $application->full_name,
            'nim' => $application->nim,
            'semester' => $application->semester,
            'phone' => $application->phone,
            'personal_email' => $application->personal_email,
            'student_email' => $application->student_email,
            'instagram_username' => $application->instagram_username,
            'stage' => $application->stage->value,
            'stage_label' => $application->stage->label(),
            'result' => $application->result->value,
            'result_label' => $application->result->label(),
            'is_verified' => $application->is_verified,
            'revision_required' => $application->revision_required,
            'submitted_at' => $application->submitted_at?->toIso8601String(),
            'period' => $application->period ? [
                'id' => $application->period->id,
                'name' => $application->period->name,
            ] : null,
            'primary_division' => $this->divisionArray($application->primaryDivision),
            'secondary_division' => $this->divisionArray($application->secondaryDivision),
            'document' => $application->document ? [
                'cv_original_name' => $application->document->cv_original_name,
                'cv_mime' => $application->document->cv_mime,
                'cv_size_bytes' => $application->document->cv_size_bytes,
                'portfolio_type' => $application->document->portfolio_type,
                'portfolio_url' => $application->document->portfolio_url,
                'portfolio_original_name' => $application->document->portfolio_original_name,
                'portfolio_mime' => $application->document->portfolio_mime,
                'portfolio_size_bytes' => $application->document->portfolio_size_bytes,
                'has_cv_file' => filled($application->document->cv_path),
                'has_portfolio_file' => filled($application->document->portfolio_path),
            ] : null,
            'screenings' => $application->screenings
                ->sortByDesc('acted_at')
                ->values()
                ->map(fn (RecruitmentScreening $screening): array => [
                    'id' => $screening->id,
                    'decision' => $screening->decision->value,
                    'decision_label' => $screening->decision->label(),
                    'reason' => $screening->reason?->value,
                    'reason_label' => $screening->reason?->label(),
                    'notes' => $screening->notes,
                    'acted_at' => $screening->acted_at?->toIso8601String(),
                    'actor' => $screening->actor ? [
                        'id' => $screening->actor->id,
                        'name' => $screening->actor->name,
                    ] : null,
                ])
                ->all(),
            'activity_logs' => $application->activityLogs
                ->sortByDesc('created_at')
                ->values()
                ->map(fn (RecruitmentActivityLog $log): array => [
                    'id' => $log->id,
                    'action' => $log->action,
                    'old_values' => $log->old_values,
                    'new_values' => $log->new_values,
                    'created_at' => $log->created_at?->toIso8601String(),
                    'actor' => $log->actor ? [
                        'id' => $log->actor->id,
                        'name' => $log->actor->name,
                    ] : null,
                ])
                ->all(),
            'can_screen' => $this->canScreen($application),
        ];
    }

    /**
     * @return list<array{id: string, name: string}>
     */
    public function periodOptions(): array
    {
        return RecruitmentPeriod::query()
            ->orderByDesc('created_at')
            ->get(['id', 'name'])
            ->map(fn (RecruitmentPeriod $period): array => [
                'id' => $period->id,
                'name' => $period->name,
            ])
            ->all();
    }

    /**
     * @return list<array{id: string, name: string, code: string}>
     */
    public function divisionOptions(): array
    {
        return RecruitmentDivision::query()
            ->where('is_active', true)
            ->orderBy('sort_order')
            ->get(['id', 'name', 'code'])
            ->map(fn (RecruitmentDivision $division): array => [
                'id' => $division->id,
                'name' => $division->name,
                'code' => $division->code,
            ])
            ->all();
    }

    private function canScreen(RecruitmentApplication $application): bool
    {
        if ($application->cancelled_at !== null) {
            return false;
        }

        if ($application->result !== \App\Enums\Recruitment\ApplicationResult::Pending) {
            return false;
        }

        return in_array($application->stage, [
            \App\Enums\Recruitment\ApplicationStage::Submitted,
            \App\Enums\Recruitment\ApplicationStage::Screening,
        ], true);
    }

    /**
     * @return array{id: string, name: string, code: string}|null
     */
    private function divisionArray(?RecruitmentDivision $division): ?array
    {
        if ($division === null) {
            return null;
        }

        return [
            'id' => $division->id,
            'name' => $division->name,
            'code' => $division->code,
        ];
    }
}
