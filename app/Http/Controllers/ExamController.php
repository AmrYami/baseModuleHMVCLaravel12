<?php

namespace App\Http\Controllers;

use Users\Http\Controllers\ParamedicsController;
use Illuminate\Http\Request;
use App\Services\ExamService;
use App\Services\UserService;
use App\Models\Exam\ExamResult;
use App\Models\Survey\AnswerQuestion;
use Illuminate\Support\Facades\Crypt;

class ExamController extends Controller
{

    /**
     * Auth.
     *
     * @return void
     */
    public function __construct(protected ExamService $service)
    {}

    public function index()
    {
        $results = $this->service->results();

        return view('dashboard.exams.index')
                ->with(['items' => $results]);
    }

    public function beforeStart()
    {
        $exam = auth()->user()->exams()->get()->last();

        if(!$exam || !$exam->pivot->active){
            session()->flash('error', 'Sorry You\'ve Taken That Exam Before ... !');
            return redirect()->route('dashboard');
        }

        return view('dashboard.exams.before-start');
    }

    public function start(Request $request, UserService $userService)
    {
        if ($request->isMethod('get')) {
            return $this->beforeStart($request);
        }

        $user = auth()->user();
        $status = $userService->getExamStatus($user, $this->service);
        $exam = auth()->user()->exams()->get()->last();

        if(!$exam || !$exam->pivot->active){
            session()->flash('error', 'Sorry You\'ve Taken That Exam Before ... !');
            return redirect()->route('dashboard');
        }

        if($status == 2){
            return redirect()->action([ParamedicsController::class, 'hourlyWorkAgreement']);
        }
        if($exam->started_at && $this->service->isTimeExceeded($user, $exam)){
            $this->service->forceFailUser($user, $exam);
            session()->flash('error', 'Sorry You\'ve Exceed The Exam Time ... !');
            $this->service->sendMail(
                    $user->email,
                    'Exam Failed ... ',
                    'exam_failure_exceed_time',
                    []
            );
            return redirect()->action([ExamController::class, 'failed']);
        }


        if(!$exam->pivot->started_at){
            $exam->pivot->update(['started_at' => time()]);
        }
        $sections = $exam->sections;
        $questions = array();
        $answers = array();
        foreach ($sections as $key => $section) {
            foreach ($section->questions as $k => $question) {
                if ($question->pivot->order >= 1) {
                    $questions[$key][$k] = $question;
                    $questions[$key][$k]['order'] = $k;
                    $questions[$key][$k]['guid'] = Crypt::encryptString($question->id);
                    $questions[$key][$k]['increment'] = 0;
                } else {
                    $k += rand(10, 2000);
                    $questions[$key][$k] = $question;
                    $questions[$key][$k]['order'] = $k;
                    $questions[$key][$k]['guid'] = Crypt::encryptString($question->id);
                    $questions[$key][$k]['increment'] = 0;
                }
                foreach ($question->answers as $ak => $answer) {
                    $answers[$key][$k][$ak] = $answer;
                    $answers[$key][$k][$ak]['order'] = $answer->pivot->order;
                    $answers[$key][$k][$ak]['guid'] = Crypt::encryptString($answer->id);
                }

            }
        }

            $countSectionActive = $exam->sections()->where('exam_section.active', 1)->count();

            if ($countSectionActive != 0) {
                $progress = 100 / $countSectionActive;
            } else {
                $progress = 0;
            }

            return view('dashboard.exams.show', [
                'exam' => $exam,
                'sections' => $sections,
                'questions' => $questions,
                'answers' => $answers,
                'countSection' => $countSectionActive,
                'progress' => $progress]);
    }


    public function store(Request $request)
    {
        $user = auth()->user();
        $exam = auth()->user()->exams()->get()->last();
        // to git pass and total grade / get maximam attempts / and if it learniner
        $passingGrade = $exam->passing_grade;
        $totalGrade = $exam->grade;
        $miniDuration = $exam->mini_duration;
        $examAttempts = $exam->attempts;
        $learn = $exam->learn;
        //answer grae
        $grade = 0;
        //count correct answers
        $countGrade = 0;
        // to get attempts and remaning time and grade
        $examUser = $exam->pivot;
        $examUserDetials = $examUser->first();
        $attempts = $examUserDetials->attempts + 1;
        $remaningTime = $examUserDetials->remaining_time;

        foreach ($request->all() as $key => $value) {
            $key = explode("-", $key);
            $question_id = $key[3] ?? '0';
            if ($question_id > 0 && $value != null && $key[0] != 'comment') {
                $answer_id = $value;
                $sectionId = $key[2];
                //get grade
                $grade = AnswerQuestion::where('answer_id', $answer_id)->where('question_id', $question_id)->where('section_id', $sectionId)->first()->grade ?? '0';

                $result = new ExamResult();
                $result->exam_id = $exam->id;
                $result->user_id = $user->id;
                $result->section_id = $sectionId ?? '0';
                $result->question_id = $question_id ?? '0';
                $result->answer_id = $answer_id ?? '0';
                $result->grade = $grade ?? '0';
                $result->comment = $comment ?? '';
                $result->attempts = $attempts ?? '';
                $result->save();

                $countGrade = $countGrade + $grade;
            }
            $grade = 0;
        }

        if (($countGrade >= $passingGrade) && ($examAttempts >= $attempts)) {
            // to update attempts and remaning time and grade
            $examUserUpdate = $examUser->update(['active' => 0, 'attempts' => $attempts, 'grade' => $countGrade]);

            session()->flash('success', 'Thanks you Pass Exam');
            return redirect()->route('dashboard');
        } else {
            if ($attempts >= $examAttempts) {
                // to update attempts and remaning time and grade
                $examUserUpdate = $examUser->update(['active' => 0]);
//                $this->service->sendMail(
//                    $user->email,
//                    'Competency Result',
//                    'application_rejected_after_competency',
//                    []
//                );
//                session()->flash('error', 'Sorry you exceed Attempts your score is ' . $countGrade . '/' . $totalGrade . ' Passing Score is ' . $passingGrade . ' this is Attempt ' . $attempts . '/' . $examAttempts);
            } else {
                $examUserUpdate = $examUser->update(['active' => 1, 'attempts' => $attempts, 'grade' => $countGrade]);
                session()->flash('error', 'Sorry Try Again your score is ' . $countGrade . '/' . $totalGrade . ' Passing Score is ' . $passingGrade . ' this is Attempt ' . $attempts . '/' . $examAttempts);
            }
        }

        if ($examUserUpdate) {
            session()->flash('success', 'Your Answers Submitted.');
        } else {
            session()->flash('error', 'Sorry Error in Exam submit.');
        }

        return redirect()->route('dashboard');
    }

    public function failed()
    {
        session()->flash('error', 'you already got this exam');
        return redirect()->route('dashboard');
    }

    public function success()
    {
        session()->flash('success', 'Your Answers Submited.');
        return redirect()->route('dashboard');
    }
}
