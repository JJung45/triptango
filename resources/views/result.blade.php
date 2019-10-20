@include('header')
@include('login')
<div class="result">
    <?php for ($i=1; $i<=count((array)$user_info); $i++) { ?>
        <div class="question">
            질문: <?= $user_info->{$i}->question['text'] ?>
        </div>
        <div class="answer">
            답: <?= $user_info->{$i}->answer['text'] ?>
        </div>
    <?php } ?>
</div>
@include('footer')
