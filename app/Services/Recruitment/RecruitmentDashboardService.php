<?php

namespace App\Services\Recruitment;

use App\Enums\Recruitment\ApplicationResult;
use App\Enums\Recruitment\ApplicationStage;
use App\Models\Recruitment\RecruitmentApplication;
use App\Models\Recruitment\RecruitmentPeriod;

final class RecruitmentDashboardService
{
    /**
     * @return array<string, mixed>
     */
    public function summary(?string $periodId = null): array
    {
        $periodQuery = RecruitmentPeriod::query()->orderByDesc('created_at');
        $activePeriod = $periodId !== null
            ? RecruitmentPeriod::query()->find($periodId)
            : $periodQuery->where('status', 'open')->first()
                ?? $periodQuery->first();

        if ($activePeriod === null) {
            return [
                'active_period' => null,
                'stats' => $this->emptyStats(),
            ];
        }

        $applications = RecruitmentApplication::query()
            ->where('recruitment_period_id', $activePeriod->id);

        return [
            'active_period' => app(RecruitmentPeriodService::class)->toInertiaArray($activePeriod),
            'stats' => [
                'total_applicants' => (clone $applications)->count(),
                'pending_screening' => (clone $applications)->where('stage', ApplicationStage::Submitted)->count(),
                'in_screening' => (clone $applications)->where('stage', ApplicationStage::Screening)->count(),
                'passed_screening' => (clone $applications)->whereIn('stage', [
                    ApplicationStage::Interview,
                    ApplicationStage::FinalReview,
                ])->count(),
                'rejected_applicants' => (clone $applications)->where('result', ApplicationResult::Rejected)->count(),
                'in_interview' => (clone $applications)->where('stage', ApplicationStage::Interview)->count(),
                'final_review' => (clone $applications)->where('stage', ApplicationStage::FinalReview)->count(),
                'completed' => (clone $applications)->where('stage', ApplicationStage::Completed)->count(),
            ],
        ];
    }

    /**
     * @return array<string, int>
     */
    private function emptyStats(): array
    {
        return [
            'total_applicants' => 0,
            'pending_screening' => 0,
            'in_screening' => 0,
            'passed_screening' => 0,
            'rejected_applicants' => 0,
            'in_interview' => 0,
            'final_review' => 0,
            'completed' => 0,
        ];
    }
}
