$(function()
{
    

    $(document).on('click', '.btn-add', function(e)
    {
        e.preventDefault();
        addEntry(this, '');
        

    }).on('click', '.btn-remove', function(e)
    {
        $(this).parents('.entry:first').remove();

        e.preventDefault();
        return false;
    });
});


function addEntry(t, val) {
    var controlForm = $('.controls .form:first'),
        currentEntry = $(t).parents('.entry:first'),
        newEntry = $(currentEntry.clone()).appendTo(controlForm);

    newEntry.find('input').val(val);
    controlForm.find('.entry:not(:last) .btn-add')
        .removeClass('btn-add').addClass('btn-remove')
        .removeClass('btn-success').addClass('btn-danger')
        .html('<span class="icon-minus"></span>');
}