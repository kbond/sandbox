$(function() {
    // add tooltips
    $('a[title]', '#zenstruck-media').tooltip({
        container: 'body'
    });

    // rename click
    $('.zenstruck-media-rename').click(function(e) {
        e.preventDefault();
        var name = $(this).data('name');
        var url = $(this).data('url');

        // set up form
        $('#zenstruck-media-rename-new-name').val(name).focus(function() {
            this.select();
        });
        $('form', '#zenstruck-media-rename').attr('action', url);

        // launch dialog
        $('#zenstruck-media-rename').modal();
    });
});