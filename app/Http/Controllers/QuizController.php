<?php

namespace App\Http\Controllers;

use App\Answer;
use App\Service\RandomQuizService;
use App\UserAnswer;
use Exception;
use Illuminate\Contracts\View\Factory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\View\View;

class QuizController extends Controller
{

    private $quizs = [];

    protected $email;

    const MAX_TYPE = 4;

    /**
     * @param RandomQuizService $quiz
     */
    public function __construct(RandomQuizService $quiz)
    {
        if(!Cache::has('random_quiz')) {
            $quiz->MakeRandomQuiz();
        }

        $this->quizs = Cache::get('random_quiz');

    }

    /**
     * @param Request $request
     * @return Factory|View
     */
    public function index(Request $request)
    {
        if(!auth()->check()) {
            return redirect('/')->with('message','로그인 후 이용가능합니다.');
        }

        $user_email = auth()->user()->email;

        if ($this->checkCustomerAnswer($user_email) || $request['number'] == (count($this->quizs))) {
            return redirect('/result?email='.$user_email);
        }

        $args = [
            'number' => 0,
            'quiz' => $this->quizs[0],
        ];

        return view('quiz', $args);
    }

    public function ajax_index(Request $request)
    {
        $number = $request['number'];
        $answer = $request['answer'];

        $quiz_id = $this->quizs[$number]['id'];

        $this->saveCustomerAnswer(auth()->user()->email, $quiz_id, $answer);

        $next_number = $number+1;
        $args = [
            'number' => $next_number,
            'quiz' => $this->quizs[$next_number],
        ];

        return view('quiz_sub', $args);

    }

    public function saveCustomerAnswer($customerInfo, $quiz, $answer)
    {
        $types = $this->checkType($quiz);

            $user_answer_idx = UserAnswer::insert(
                [
                    'user_id' => $customerInfo,
                    'question_id' => $quiz,
                    'answer_id' => $answer,
                ]
            );

            foreach ($types as $row) {

                UserAnswer::where('id', $user_answer_idx)->update([$row => 1]);

            }
    }

    public function checkCustomerAnswer(string $email)
    {
        try {
            if (empty($email)) {
                throw new Exception('이메일값 오류');
            }

            $result = count(UserAnswer::where('user_id', $email)->get());

            return $result->user_id ?? 0;
        } catch (\Exception $e) {
            dump($e->getMessage());
        }
    }

    public function checkType(Int $id)
    {
        try {

            $types = [];

            if (empty($id)) {
                throw new Exception('키값 오류');
            }

            /**
             * answer 값에 어떤 type에 체크가 되있는 지 확인
             */
            $answer = Answer::where('id', $id)->first();

            for ($i = 1; $i <= QuizController::MAX_TYPE; $i++) {
                $key = "type".$i;
                if (!empty($answer->$key)){
                    $types[] = $key;
                }
            }

            return $types;

        }catch (Exception $e) {
            dump($e->getMessage());
        }
    }

}
