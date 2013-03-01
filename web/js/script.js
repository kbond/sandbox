var MyApp = {
    select2Callback: function(id, element) {
        console.log(id);
        console.log(element);
    }
};

$(function() {
    ZenstruckFormHelper.initialize();
    ZenstruckDashboardHelper.initialize();
});