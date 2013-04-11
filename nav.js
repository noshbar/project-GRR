function nextPage()
{
    //TODO: show a 'please wait' message here
    //TODO: count how many times this has been called. If it's taking longer than e.g., 10 seconds, then stop trying and show an error message
    var pending = parseInt($('#content').attr('pendingMarks'));
    if (pending > 0)
        setTimeout(nextPage, 200);
    else
        loadItems(parseInt($('#content').attr('currentSiteId')));
}

function disableWayPoints()
{
    $('#content hr').each( function(index, element) {
        $(this).waypoint('destroy');
    });
    if ($('#nextPageDiv'))
        $('#nextPageDiv').waypoint('destroy');
}
