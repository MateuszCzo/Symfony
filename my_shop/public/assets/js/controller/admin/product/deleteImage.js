$(function() {
    $('.delete-image-js').on('click', function(event) {
        event.preventDefault();

        let button = $(this)
        let imagedeleteUrl = button.data('image-delete-url');
        let parent = button.parent();

        $.ajax({
            url: imagedeleteUrl,
            type: 'GET',
            success: function(response) {
                parent.html('');
                alert(response);
            },
            error: function(response) {
                alert('Can not delete image');
            }
        });
    });
})