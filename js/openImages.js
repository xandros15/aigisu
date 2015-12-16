var images = {
    onReady: function () {
        images.findAllUnits();
        images.addClassToUnit(images.unitList);
        $('img.icon.hscene').mouseup(images.openImages);
        $('img.hscene').on('contextmenu', images.blockContextMenu);
    },
    addClassToUnit: function (list) {
        if (typeof list === 'undefined') {
            return false;
        }
        for (var i in list) {
            var unit = $('#unit-' + i + ' img.icon');
            unit.addClass('hscene');
            unit.attr('data-bind', list[i]);
        }
    },
    blockContextMenu: function () {
        return false;
    },
    openImages: function (e) {
        var data = $(e.target).data('bind');
        if (!data) {
            return false;
        }
        if (e.button === 2) {
            var url = window.location.hostname;
            url = 'http://' + url + data.toString();
            window.open(url, '_blank');
        }
    },
    addUnitToList: function (id, route) {
        images.unitList[id] = route;
    },
    findAllUnits: function () {
        var units = $('.is-any-images-uploaded');
        for (var i = 0; i < units.length; i++) {
            var id = units[i].value;
            var route = $(units[i]).next('.image-route').val();
            images.addUnitToList(id, route);
        }
    },
    unitList: []
};

$(document).ready(images.onReady);

