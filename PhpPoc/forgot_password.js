$(document).ready(function() {
    $('form').on('submit', function(e) {
        e.preventDefault(); 

        var formData = $(this).serialize();

        $.ajax({
            type: 'POST',
            url: 'forgot_password.php',
            data: formData,
            dataType: 'json',
            success: function(response) {
                if (response.status === 'success') {
                    $('#message-container').html('<div class="success-message">' + response.message + '</div>');
                } else {
                    $('#message-container').html('<div class="error-message">' + response.message + '</div>');
                }
            },
            error: function() {
                $('#message-container').html('<div class="error-message">An error occurred while processing your request.</div>');
            }
        });
    });
});

