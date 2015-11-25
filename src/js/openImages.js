$('form img').mouseup(function (e) {
    if (e.button === 2) {
        var url = window.location.hostname;
        url = 'http://' + url + '?image=' + $(e.target).data('bind').toString();
        window.open(url, '_blank');
    }
});
$('form img').on('contextmenu', function(){ return false;});