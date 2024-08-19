$(document).ready(function() {
    $('#jokeForm').on('submit', function(e) {
        e.preventDefault(); // Предотвращаем стандартное поведение формы

        $.ajax({
            url: $(this).attr('action'),
            type: 'POST',
            data: $(this).serialize(),
            success: function(response) {
                $('#result').html('<p>' + response.message + '</p>').fadeIn(); // Показываем блок с результатом
            },
            error: function() {
                $('#result').html('<p>Произошла ошибка при отправке шутки.</p>').fadeIn(); // Показываем блок с ошибкой
            }
        });
    });
});