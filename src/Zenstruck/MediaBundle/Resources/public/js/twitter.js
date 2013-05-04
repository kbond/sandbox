$(function() {
    $('.zenstruck-media-rename').click(function(e) {
        e.preventDefault();
        var name = $(this).data('name');
        var url = $(this).data('url');

        // set up form
        $('#zenstruck-media-rename-new-name').val(name);
        $('form', '#zenstruck-media-rename').attr('action', url);

        // launch dialog
        $('#zenstruck-media-rename').modal();
    });
});