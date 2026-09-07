<?php

namespace App\Services\Recruitment;

use App\Enums\Recruitment\ApplicationStage;
use App\Models\Recruitment\RecruitmentApplication;

final class TrackingPresenter
{
    /**
     * @return array<string, mixed>
     */
    public function present(RecruitmentApplication $application): array
    {
        $application->loadMissing([
            'period',
            'primaryDivision',
            'secondaryDivision',
            'interview',
            'queueEntry',
            'finalDecision.finalDivision',
        ]);

        return [
            'application' => $this->presentApplication($application),
            'period' => [
                'name' => $application->period?->name,
            ],
            'timeline' => $this->presentTimeline($application),
            'interview' => $this->presentInterview($application),
            'queue' => $this->presentQueue($application),
            'final' => $this->presentFinal($application),
        ];
    }

    /**
     * @return array<string, mixed>
     */
    private function presentApplication(RecruitmentApplication $application): array
    {
        return [
            'registration_number' => $application->registration_number,
            'full_name' => $application->full_name,
            'nim' => $application->nim,
            'semester' => $application->semester,
            'phone' => $application->phone,
            'personal_email' => $application->personal_email,
            'student_email' => $application->student_email,
            'instagram_username' => $application->instagram_username,
            'primary_division' => $application->primaryDivision?->name,
            'secondary_division' => $application->secondaryDivision?->name,
            'stage' => $application->stage->value,
            'stage_label' => $application->stage->label(),
            'result' => $application->result->value,
            'result_label' => $application->result->label(),
            'revision_required' => $application->revision_required,
            'submitted_at' => $application->submitted_at?->toIso8601String(),
        ];
    }

    /**
     * @return list<array<string, mixed>>
     */
    private function presentTimeline(RecruitmentApplication $application): array
    {
        $currentStage = $application->stage;
        $currentIndex = array_search($currentStage, ApplicationStage::timelineOrder(), true);

        if ($currentIndex === false) {
            $currentIndex = 0;
        }

        $items = [];

        foreach (ApplicationStage::timelineOrder() as $index => $stage) {
            $status = match (true) {
                $index < $currentIndex => 'completed',
                $index === $currentIndex => 'current',
                default => 'upcoming',
            };

            if ($stage === ApplicationStage::Completed && $application->result->value !== 'pending') {
                $status = 'completed';
            }

            $items[] = [
                'key' => $stage->value,
                'label' => $stage->label(),
                'status' => $status,
                'note' => $stage === ApplicationStage::Screening && $application->revision_required
                    ? 'Perlu revisi data — tim akan menghubungi kamu.'
                    : null,
            ];
        }

        return $items;
    }

    /**
     * @return array<string, mixed>|null
     */
    private function presentInterview(RecruitmentApplication $application): ?array
    {
        $interview = $application->interview;

        if ($interview === null) {
            return null;
        }

        return [
            'scheduled_at' => $interview->scheduled_at?->toIso8601String(),
            'location' => $interview->location,
            'room' => $interview->room,
            'status' => $interview->status,
        ];
    }

    /**
     * @return array<string, mixed>|null
     */
    private function presentQueue(RecruitmentApplication $application): ?array
    {
        $queue = $application->queueEntry;

        if ($queue === null) {
            return null;
        }

        return [
            'queue_number' => $queue->queue_number,
            'status' => $queue->status,
        ];
    }

    /**
     * @return array<string, mixed>|null
     */
    private function presentFinal(RecruitmentApplication $application): ?array
    {
        if ($application->stage !== ApplicationStage::Completed) {
            return null;
        }

        $decision = $application->finalDecision;

        if ($decision === null && $application->result->value === 'rejected') {
            return [
                'membership_type' => null,
                'final_division' => null,
                'public_message' => null,
                'result' => $application->result->value,
                'result_label' => $application->result->label(),
            ];
        }

        if ($decision === null) {
            return null;
        }

        return [
            'membership_type' => $decision->membership_type,
            'final_division' => $decision->finalDivision?->name,
            'public_message' => $decision->public_message,
            'result' => $application->result->value,
            'result_label' => $application->result->label(),
        ];
    }
}
