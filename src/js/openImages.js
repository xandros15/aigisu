$('form img.hscene').mouseup(function (e) {
    var data = $(e.target).data('bind');
    if(!data){
        return;
    }
    if (e.button === 2) {
        var url = window.location.hostname;
        url = 'http://' + url + '?image=' + data.toString();
        window.open(url, '_blank');
    }
});
$('form img.hscene').on('contextmenu', function(){ return false;});