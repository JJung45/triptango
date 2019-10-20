<?php

namespace App\Http\Controllers;

use App\Answer;
use App\Service\RandomQuizService;
use App\Type;
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
    public function index()
    {
        if(!auth()->check()) {
            return redirect('/')->with('message','로그인 후 이용가능합니다.');
        }

        $start_number = 0;
        $user_email = auth()->user()->email;

        if (!empty($this->checkCustomerAnswer($user_email)) ) {
            return redirect('/result?email='.$user_email);
        }

        $args = [
            'number' => $start_number,
            'quiz' => $this->quizs[$start_number],
        ];

        return view('quiz', $args);
    }

    public function ajax_index(Request $request)
    {
        $number = $request['number'];
        $answer = $request['answer'];

        $total_quizs = count($this->quizs)-1;
        $quiz_id = $this->quizs[$number]['id'];

        $this->saveCustomerAnswer(auth()->user()->email, $quiz_id, $answer);

        $email = '';
        if ($number < $total_quizs) {
            $number = $number+1;
        } else if($number == $total_quizs) {
            $this->index();
        } else {
        }

        $args = [
            'number' => $number,
            'quiz' => $this->quizs[$number],
            'email' => $email,
        ];

        return view('quiz_sub', $args);

    }

    public function saveCustomerAnswer($customerInfo, $quiz, $answer)
    {
        $types = $this->checkType($quiz);

        $user_answer_idx = UserAnswer::insertGetId(
            [
                'user_id' => $customerInfo,
                'question_id' => $quiz,
                'answer_id' => $answer,
                'created_at' => date('Y-m-d H:i:s'),
            ]
        );

        if (!empty($types)) {

            foreach ($types as $row) {
                UserAnswer::where('id', $user_answer_idx)->update([$row => 1]);
            }

        }
    }

    public function checkCustomerAnswer(string $email)
    {
        try {
            if (empty($email)) {
                throw new Exception('이메일값 오류');
            }

            return UserAnswer::where('user_id', $email)->get()[0]->user_id ?? null;

        } catch (\Exception $e) {
            dump($e->getMessage());
        }
    }

    public function checkType(Int $id)
    {
        try {

            if (empty($id)) {
                throw new Exception('키값 오류');
            }

            $answer = Answer::where('quiz_id', $id)->get();

            $types = [];
            for ($i = 1; $i <= Type::MAX_TYPE; $i++) {

                if (!empty($answer[0]['type'.$i])){
                    $types[] = "type".$i;
                }

            }

            return $types;

        }catch (Exception $e) {
            dump($e->getMessage());
        }
    }

}
