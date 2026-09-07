<?php

namespace App\Jobs\Recruitment;

use App\Enums\EmailLogStatus;
use App\Enums\EmailNotificationType;
use App\Jobs\Concerns\AppliesOutgoingEmailDelay;
use App\Mail\Recruitment\RecruitmentApplicationConfirmationMail;
use App\Models\EmailLog;
use App\Models\Recruitment\RecruitmentApplication;
use App\Services\Recruitment\RecruitmentEmailRenderer;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class SendRecruitmentNotificationJob implements ShouldQueue
{
    use AppliesOutgoingEmailDelay;
    use Queueable;

    public function __construct(
        public string $applicationId,
        public string $templateKey,
    ) {
    }

    public function handle(RecruitmentEmailRenderer $renderer): void
    {
        $application = RecruitmentApplication::query()
            ->with(['period', 'primaryDivision'])
            ->find($this->applicationId);

        if ($application === null) {
            Log::warning('[SendRecruitmentNotificationJob] Application not found.', [
                'application_id' => $this->applicationId,
                'template_key' => $this->templateKey,
            ]);

            return;
        }

        $notificationType = $this->resolveNotificationType();
        $recipientEmail = $application->personal_email;
        $trackingUrl = url(route('open-recruitment.track.login', absolute: false));

        $variables = [
            'applicant_name' => $application->full_name,
            'registration_number' => $application->registration_number,
            'period_name' => $application->period?->name ?? 'OpenRecruitment DOSCOM',
            'organization_name' => 'DOSCOM',
            'nim' => $application->nim,
            'semester' => (string) $application->semester,
            'primary_division' => $application->primaryDivision?->name ?? '',
            'tracking_url' => $trackingUrl,
        ];

        if ($recipientEmail === '') {
            EmailLog::query()->create([
                'recruitment_application_id' => $application->id,
                'event_id' => null,
                'user_id' => null,
                'recipient_email' => '',
                'status' => EmailLogStatus::Failed,
                'notification_type' => $notificationType,
                'error_message' => 'No recipient email address configured.',
                'sent_at' => null,
            ]);

            return;
        }

        $rendered = $renderer->renderTemplate($this->templateKey, $variables);

        try {
            Mail::to($recipientEmail)->send(new RecruitmentApplicationConfirmationMail(
                subjectLine: $rendered['subject'],
                bodyHtml: $rendered['body_html'],
                bodyText: $rendered['body_text'],
            ));

            EmailLog::query()->create([
                'recruitment_application_id' => $application->id,
                'event_id' => null,
                'user_id' => null,
                'recipient_email' => $recipientEmail,
                'status' => EmailLogStatus::Sent,
                'notification_type' => $notificationType,
                'error_message' => null,
                'sent_at' => now(),
            ]);
        } catch (\Throwable $exception) {
            EmailLog::query()->create([
                'recruitment_application_id' => $application->id,
                'event_id' => null,
                'user_id' => null,
                'recipient_email' => $recipientEmail,
                'status' => EmailLogStatus::Failed,
                'notification_type' => $notificationType,
                'error_message' => $exception->getMessage(),
                'sent_at' => null,
            ]);

            throw $exception;
        }
    }

    private function resolveNotificationType(): EmailNotificationType
    {
        return match ($this->templateKey) {
            'revision_required' => EmailNotificationType::RecruitmentRevisionRequired,
            'passed_screening' => EmailNotificationType::RecruitmentPassedScreening,
            'rejected_screening' => EmailNotificationType::RecruitmentRejectedScreening,
            default => EmailNotificationType::RecruitmentApplicationSubmitted,
        };
    }
}
