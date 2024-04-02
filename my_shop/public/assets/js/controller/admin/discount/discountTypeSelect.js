$(function() {
    $('.discount-type').parent().hide();
    $('.' + $('.discount-type-select').val()).parent().show();

    $('.discount-type-select').on('change', function() {
        $('.discount-type').parent().hide();
        $('.' + $(this).val()).parent().show();
    });
});
