<?php

namespace App\Services;

use App\Models\Exam\Exam;
use App\Abstratctions\Service;
use App\Repositories\UserRepository;
use Users\Models\User;

/**
 *
 */
class UserService extends Service
{
    public function __construct(protected UserRepository $repo)
    {
    }

    /**
     * [returns integer as a status 0 for fail 2 for success 1 for pending]
     * @param \Users\Models\User $user
     * @param \App\Services\ExamService $examService
     * @return int
     */
    public function getExamStatus(User $user, ExamService $examService): int
    {

        $exams = $user->exams()->get();
        $lastExam = $exams->last();
        if (!$lastExam)
            return 0;

        if ($lastExam?->pivot->active) {
            return 1;
        }

        if ($lastExam->pivot->isSuccess) {
            return 2;
        }

        return 0;
    }
}

?>
