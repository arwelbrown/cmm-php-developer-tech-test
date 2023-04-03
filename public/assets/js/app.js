$(document).ready(function() {

    $('form').submit(function(){
        $(this).find(':submit').attr('disabled','disabled');
    });

    let more = $('.matches__match__more');

    $(more).on('click', (e) => {
        let id = e.target.id;

        $('#' + id + '_div').slideToggle();
    });
});
