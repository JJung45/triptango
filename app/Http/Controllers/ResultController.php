<?php

namespace App\Http\Controllers;

use App\Answer;
use App\Quiz;
use App\Type;
use App\UserAnswer;
use Exception;
use Illuminate\Http\Request;

class ResultController
{
    public function index(Request $request)
    {
        try{

            $email = $request['email'];

            if (empty($email)) {
                throw new Exception('잘 못된 접근입니다.');
            }

            $user_answer = UserAnswer::where('user_id', $email)->get()->toArray();

            $user_info = new \stdClass();
            foreach ($user_answer as $row) {

                if(empty($user_info->{$row['question_id']})) {
                    $user_info->{$row['question_id']} = new \stdClass();
                    $user_info->{$row['question_id']} = new \stdClass();
                }
                $user_info->{$row['question_id']}->question = Quiz::where('id', $row['question_id'])->get()[0]->toArray();
                $user_info->{$row['question_id']}->answer = Answer::where('id', $row['answer_id'])->get()[0]->toArray();

            }

            /** TODO 최대값을 같을 경우 어떻게 결과낼지, 결과값 function 추가필요 */
            $maxType = $this->maxType($user_answer) ?? '';

            if (empty($user_info)) {
                throw new Exception('알수없는 오류');
            }

            return view('result', ['user_info' => $user_info]);

        } catch (Exception $e) {
        }
    }

    public function maxType(Array $answers)
    {
        $count = [];

        foreach (Type::TYPES as $type) {

            $count[$type] = 0;
            foreach ($answers as $answer) {
                if (!empty($answer[$type])) {
                    $count[$type]++;
                }
            }
        }

        $maxType = array_keys($count, max($count));

        return $maxType;
    }

}
