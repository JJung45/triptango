<input type="hidden" name="number" value="<?= $number ?>">
<h2><?= $quiz['quiz'] ?></h2>
<div class="select">
    <?php foreach ($quiz['answer'] as $row) { ?>
    <div class="option">
        <a href="javascript:;" class="submit" data-answer="<?= $row['id'] ?>"><?= $row['text'] ?></a>
    </div>
    <?php } ?>
</div>

<script>
    $('.submit').on('click',function (){

        var number = $('input[name=number]').val();
        var answer = $(this).data('answer');
        $.ajax({
            url: '/quiz/ajax_index',
            data: {
                'number': number,
                'answer' : answer,
            },
            success: function (result) {
                $('.active_quiz').html(result);
            }
        });
    });

</script>
