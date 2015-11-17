var globalUrl;
var update = function (obj) {
    var id = $(obj).parents('form').attr('id');
    var name = $(obj).parents('form').find('.unit-name').val();
    var rarity = $(obj).parents('form').find('.unit-rarity').val();
    var query = {'unit': {'id': id, 'name': name, 'rarity': rarity}};
    $.post(globalUrl, query, function (c) {
        console.log(c);
        var results = JSON.parse(c);
        for (var valid in results.valid) {
            if (results.valid[valid].value) {
                $(obj).parents('form')
                        .find('.' + results.valid[valid].name)
                        .parents('.form-group')
                        .addClass('has-success')
                        .removeClass('has-error');
            } else {
                $(obj).parents('form')
                        .find('.' + results.valid[valid].name)
                        .parents('.form-group')
                        .addClass('has-error')
                        .removeClass('has-success');
            }
        }
    });
};