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
        for (var i = 0; i < list.length; i++) {
            $('#unit-' + list[i] + ' img.icon').addClass('hscene');
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
            url = 'http://' + url + '?image=' + data.toString();
            window.open(url, '_blank');
        }
    },
    addUnitToList: function (id) {
        images.unitList.push(id);
    },
    findAllUnits: function () {
        var units = $('.is-any-images-uploaded');
        for (var i = 0; i < units.length; i++) {
            images.addUnitToList(units[i].value);
        }
    },
    unitList: []
};

$(document).ready(images.onReady);
