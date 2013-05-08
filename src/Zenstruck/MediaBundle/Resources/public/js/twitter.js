var ZenstuckMedia = {
    initialize: function() {
        // add toolbar tooltips
        $('a[title]', '#zenstruck-media .btn-toolbar').tooltip({
            container: 'body',
            placement: 'iframe' == $('#zenstruck-media').data('layout') ? 'bottom' : 'top'
        });

        // add action tooltips
        $('a[title]', '#zenstruck-media .zenstruck-media-actions').tooltip();

        // thumbnail actions hover
        $('li', '#zenstruck-media-thumb').hover(function() {
            $('.zenstruck-media-actions', $(this)).toggleClass('hide');
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

        // delete action
        $('.delete', '.zenstruck-media-actions').on('click', function(e) {
            e.preventDefault();

            if (!confirm("Are you sure you want to delete?")) {
                return;
            }

            // create form
            $('<form></form>')
                .attr('method', 'POST')
                .attr('action', $(this).attr('href'))
                .append('<input type="hidden" name="_method" value="DELETE" />')
                .appendTo($('body'))
                .submit()
            ;
        });

        // media widget - clear selection
        $('.zenstruck-media-clear', '.zenstruck-media-widget').click(function(e) {
            e.preventDefault();
            $(this).siblings('.zenstruck-media-input').val('');
        });

        // media widget - browse
        $('.zenstruck-media-select', '.zenstruck-media-widget').fancybox({
            type:    'iframe',
            width:   '70%',
            height:  '70%',
            autoSize: false
        });
    }
};

$(function() {
    ZenstuckMedia.initialize();
});