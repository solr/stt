$('#weeklock').click(function(){
    if ($('#weeklock').prop("checked")) {
        $('.weeklock-warning').show();
    } else {
        $('.weeklock-warning').hide();
    }
});