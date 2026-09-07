<?php

use App\Http\Controllers\Dashboard\Recruitment\RecruitmentApplicationController;
use App\Http\Controllers\Dashboard\Recruitment\RecruitmentDashboardController;
use App\Http\Controllers\Dashboard\Recruitment\RecruitmentDivisionController;
use App\Http\Controllers\Dashboard\Recruitment\RecruitmentPeriodController;
use App\Http\Controllers\Dashboard\Recruitment\RecruitmentScreeningController;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth', 'recruitment.access'])
    ->prefix('admin/recruitment')
    ->name('dashboard.recruitment.')
    ->group(function (): void {
        Route::get('/', RecruitmentDashboardController::class)->name('index');

        Route::resource('periods', RecruitmentPeriodController::class);
        Route::post('periods/{period}/open', [RecruitmentPeriodController::class, 'open'])->name('periods.open');
        Route::post('periods/{period}/close', [RecruitmentPeriodController::class, 'close'])->name('periods.close');

        Route::get('divisions', [RecruitmentDivisionController::class, 'index'])->name('divisions.index');
        Route::put('divisions/{division}', [RecruitmentDivisionController::class, 'update'])->name('divisions.update');
        Route::post('interviewers/assign', [RecruitmentDivisionController::class, 'assignInterviewer'])->name('interviewers.assign');
        Route::delete('interviewers/{assignment}', [RecruitmentDivisionController::class, 'unassignInterviewer'])->name('interviewers.unassign');

        Route::get('applications', [RecruitmentApplicationController::class, 'index'])->name('applications.index');
        Route::get('applications/{application}', [RecruitmentApplicationController::class, 'show'])->name('applications.show');
        Route::get('applications/{application}/documents/{type}', [RecruitmentApplicationController::class, 'downloadDocument'])
            ->name('applications.documents.download')
            ->where('type', 'cv|portfolio');
        Route::post('applications/{application}/screening/pass', [RecruitmentScreeningController::class, 'pass'])
            ->name('applications.screening.pass');
        Route::post('applications/{application}/screening/revision', [RecruitmentScreeningController::class, 'revision'])
            ->name('applications.screening.revision');
        Route::post('applications/{application}/screening/reject', [RecruitmentScreeningController::class, 'reject'])
            ->name('applications.screening.reject');
    });
