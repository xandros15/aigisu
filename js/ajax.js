
var ajaxModal = {
    onReady: function () {
        $('.btn.ajax').click(ajaxModal.onTrigger);
    },
    openModal: function (target) {
        $('#' + ajaxModal.loaded[target] + ' .modal').modal({});
    },
    loadAjax: function (target) {
        $.post(ajaxModal.url + target, {}, function (data) {
            var id = ajaxModal.makeid();
            var newDiv = document.createElement("div");
            newDiv.id = id;
            newDiv.innerHTML = data;
            document.body.appendChild(newDiv);
            ajaxModal.loaded[target] = id;
            ajaxModal.openModal(target);
        });
    },
    onTrigger: function (e) {
        e.preventDefault();
        var target = $(this).data('target');
        if(!target){
            return;
        }    
        if (ajaxModal.loaded[target]) {
            ajaxModal.openModal(target);
        } else {
            ajaxModal.loadAjax(target);
        }
    },
    makeid: function () {
        var text = "";
        var possible = "ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789";

        for (var i = 0; i < 5; i++) {
            text += possible.charAt(Math.floor(Math.random() * possible.length));
        }

        return text;
    },
    loaded: {},
    url: window.location
};
$(document).ready(ajaxModal.onReady);