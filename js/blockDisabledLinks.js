$('.pagination .disabled a').click(function (e) {
    e.preventDefault();
});
$('.pagination .active a').click(function (e) {
    e.preventDefault();
});

$('.disabled *').click(function (e) {
    e.preventDefault();
});

$("button.btn[type='submit']").click( function(e){
   e.preventDefault(); 
   $(this).addClass('disabled');
   $(this).parents('form').submit();
});