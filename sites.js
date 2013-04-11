function addSiteToList(siteName, siteId, siteUnread)
{
    var div  = $("<div>");
    var link = $("<a>").text(siteName);
    link.attr('id', parseInt(siteId));
    link.attr('href', '#');
    link.click(function() {
        disableWayPoints();
        $('#content').attr('pendingMarks', 0);
        var id = parseInt($(this).attr('id'));
        loadItems(id);
    });

    var counter = $('<span>').text(' (' + siteUnread + ')');
    counter.attr('id', 'counter' + siteName);
    counter.attr('unreadCount', siteUnread);

    div.append(link);
    div.append(counter);
    div.append($("<br>"))

    $("#sites").append(div);

    //while we're here, may as well populate the search list too...
    var option = $("<option>").text(siteName);
    option.attr('value', siteId);
    $('#searchSiteList').append(option);
}

function loadSites()
{
    $.post("getSites.php",
    function(data, status) {
        addSiteToList('All', -1, 0);

        var br  = $('<br>');
        var div = $('<div>').text('Subscriptions');
        $('#sites').append(br);
        $('#sites').append(br);
        $('#sites').append(div);

        var totalUnread = 0;
        data = decodeJSON(data);
        if (data && data.sites && data.sites.length)
        {
            for (var x = 0; x < data.sites.length; x++)
            {
                addSiteToList(data.sites[x].siteName, data.sites[x].siteId, data.sites[x].unread);
                totalUnread += parseInt(data.sites[x].unread);
            }
        }
        else
        {
            $('#controlPanel').show();
        }
        $('#counterAll').attr('unreadCount', totalUnread);
        $('#counterAll').attr('lastItemId', data.lastItemId);
        $('#counterAll').text(' (' + totalUnread + ')');
    });
}

function addSite(title, url)
{
    $.post("addSite.php",
    {
        siteName:title,
        siteSource:url
    }, function(data) {
        data = decodeJSON(data);
        addSiteToList(data.site.name, data.site.id, data.site.unread);
        loadItems(data.site.id);
        var totalUnread = parseInt($('#counterAll').attr('unreadCount'));
        totalUnread += data.site.unread;
        $('#counterAll').attr('unreadCount', totalUnread);
        $('#counterAll').text(' (' + totalUnread + ')');
    });
}