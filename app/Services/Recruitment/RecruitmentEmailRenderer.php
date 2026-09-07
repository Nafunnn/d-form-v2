<?php

namespace App\Services\Recruitment;

use App\Models\Recruitment\RecruitmentEmailTemplate;

final class RecruitmentEmailRenderer
{
    /**
     * @param  array<string, string>  $variables
     */
    public function render(string $template, array $variables): string
    {
        $rendered = $template;

        foreach ($variables as $key => $value) {
            $rendered = str_replace('{{'.$key.'}}', $value, $rendered);
        }

        return $rendered;
    }

    /**
     * @param  array<string, string>  $variables
     * @return array{subject: string, body_html: string, body_text: string}
     */
    public function renderTemplate(string $eventType, array $variables): array
    {
        $template = RecruitmentEmailTemplate::query()
            ->where('event_type', $eventType)
            ->where('is_active', true)
            ->first();

        if ($template === null) {
            return $this->fallbackApplicationSubmitted($variables);
        }

        return [
            'subject' => $this->render($template->subject, $variables),
            'body_html' => $this->render($template->body_html, $variables),
            'body_text' => $this->render($template->body_text ?? strip_tags($template->body_html), $variables),
        ];
    }

    /**
     * @param  array<string, string>  $variables
     * @return array{subject: string, body_html: string, body_text: string}
     */
    private function fallbackApplicationSubmitted(array $variables): array
    {
        $subject = '[DOSCOM OpRec] Konfirmasi Pendaftaran — '.$variables['registration_number'];
        $bodyHtml = '<p>Halo '.$variables['applicant_name'].',</p>'
            .'<p>Pendaftaran OpenRecruitment DOSCOM kamu telah berhasil diterima.</p>'
            .'<p><strong>Nomor Pendaftaran:</strong> '.$variables['registration_number'].'</p>'
            .'<p><strong>Token Tracking:</strong> '.$variables['tracking_token'].'</p>'
            .'<p>Gunakan nomor pendaftaran dan token untuk memantau progress di '
            .$variables['tracking_url'].'</p>';

        return [
            'subject' => $subject,
            'body_html' => $bodyHtml,
            'body_text' => strip_tags($bodyHtml),
        ];
    }
}
