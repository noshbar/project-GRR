function decreaseCount(siteName)
{
    for (var f = 0; f < 2; f++)
    {
        var counter = $('#counter' + siteName);
        var count   = counter.attr('unreadCount') - 1;
        counter.attr('unreadCount', count);
        counter.text(' (' + count + ')');
        siteName = 'All';
    }
}

function addStatusMessage(message)
{
    if (!message)
        return;

    var div = $('<div>').html(message);
    div.attr('class', 'statusMessage');
    $('#messages').prepend(div);
    $('#newMessageAlert').show();
}

function decodeJSON(data)
{
    if (!data)
        return null;

    data = jQuery.parseJSON(data);

    if (data.warning)
        addStatusMessage(data.warning);

    if (!data.error)
        return data;

    addStatusMessage(data.error);
    return null;
}

function contains(vector, value)
{
    var i = vector.length;
    while (i--)
        if (vector[i] === value)
            return true;
    return false;
}
