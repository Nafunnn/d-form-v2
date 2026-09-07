<?php

namespace App\Http\Controllers\Recruitment;

use App\Http\Controllers\Controller;
use App\Http\Requests\Recruitment\AuthenticateTrackingRequest;
use App\Services\Recruitment\RecruitmentTrackingAuthenticator;
use App\Services\Recruitment\RecruitmentTrackingSession;
use App\Services\Recruitment\TrackingPresenter;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class TrackingController extends Controller
{
    public function __construct(
        private readonly RecruitmentTrackingAuthenticator $authenticator,
        private readonly RecruitmentTrackingSession $trackingSession,
        private readonly TrackingPresenter $presenter,
    ) {
    }

    public function login(): Response|RedirectResponse
    {
        if ($this->trackingSession->isValid()) {
            return redirect()->route('open-recruitment.track.show');
        }

        return Inertia::render('OpenRecruitment/Track/Login', [
            'authenticateUrl' => route('open-recruitment.track.authenticate'),
        ]);
    }

    public function authenticate(AuthenticateTrackingRequest $request): RedirectResponse
    {
        $application = $this->authenticator->attempt(
            $request->string('registration_number')->toString(),
            $request->string('tracking_token')->toString(),
        );

        if ($application === null) {
            return back()
                ->withInput($request->only('registration_number'))
                ->withErrors([
                    'credentials' => RecruitmentTrackingAuthenticator::INVALID_CREDENTIALS_MESSAGE,
                ]);
        }

        $this->trackingSession->store($application->id);

        return redirect()->route('open-recruitment.track.show');
    }

    public function show(Request $request): Response
    {
        /** @var \App\Models\Recruitment\RecruitmentApplication $application */
        $application = $request->attributes->get('recruitment_application');

        return Inertia::render('OpenRecruitment/Track/Show', [
            'tracking' => $this->presenter->present($application),
            'logoutUrl' => route('open-recruitment.track.logout'),
        ]);
    }

    public function logout(): RedirectResponse
    {
        $this->trackingSession->flush();

        return redirect()
            ->route('open-recruitment.track.login')
            ->with('toast', [
                'type' => 'success',
                'message' => 'Sesi tracking berakhir.',
            ]);
    }
}
