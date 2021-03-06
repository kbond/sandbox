var MyApp = {
    select2Callback: function(id, element) {
        console.log(id);
        console.log(element);
    }
};

$(function() {
    ZenstruckFormHelper.initialize();
    ZenstruckDashboardHelper.initialize();

    $('.wysiwyg').each(function() {
        CKEDITOR.replace($(this).attr('name'), {
            filebrowserBrowseUrl: '/media?layout=popup',
            filebrowserImageBrowseUrl: '/media?layout=popup&filesystem=Images&lock=true'
        });
    });
});