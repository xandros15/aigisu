$('.pagination .disabled a').click(function (e) {
    e.preventDefault();
});
$('.pagination .active a').click(function (e) {
    e.preventDefault();
});

$('.disabled *').click(function (e) {
    e.preventDefault();
});