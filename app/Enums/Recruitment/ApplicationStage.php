<?php

namespace App\Enums\Recruitment;

enum ApplicationStage: string
{
    case Submitted = 'submitted';
    case Screening = 'screening';
    case Interview = 'interview';
    case FinalReview = 'final_review';
    case Completed = 'completed';
}
