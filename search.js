function makeLocalCopyLink(href)
{
    var linkSup = $("<sup>").text('(local copy)');
    var localItem = $("<a>");
    localItem.append(linkSup);
    localItem.attr('href', href);
    return localItem;
}

function onShowItem(id)
{
    $('#content').empty();
    $.post("getItems.php",
    {
        itemId: id
    }, function(data) {
        data = decodeJSON(data);
        if (!data || !data.items || !data.items.length)
        {
            var div = $("<div>").text('Something went wrong.');
            $("#content").append(div);
            return;
        }

        for (var x = 0; x < data.items.length; x++)
        {
            var div = itemToDiv(data.items[x], x); //from items.js
            $("#content").append(div);
        }
    });
}

function search(term, siteId, tagId)
{
    $("#searchResults").empty();

    $.post("search.php",
    {
        searchTerm : term,
        site       : siteId,
        tag        : tagId
    }, function(data) {
        data = decodeJSON(data);
        if (!data || !data.items || !data.items.length)
        {
            var div = $("<div>").text('No results returned.');
            $("#searchResults").append(div);
            return;
        }
        else
        {
            var div = $("<div>").text(data.items.length + ' results returned.');
            $("#searchResults").append(div);
        }

        for (var x = 0; x < data.items.length; x++)
        {
            var div      = $("<div>");
            var linkText = data.items[x].site + ': ' + data.items[x].title;
            var showItem = $("<a>").text(linkText);

            div.append(showItem);
            $("#searchResults").append(div);

            if (data.items[x].localCopy)
                div.append(makeLocalCopyLink(data.items[x].localCopy));

            showItem.attr('href', '#');
            showItem.attr('itemId', data.items[x].itemId);
            showItem.click( function() {
                onShowItem(parseInt($(this).attr('itemId')));
            });
        }
    });
}

function startSearch()
{
    searchTerm = $("#searchTerm").val();
    siteId     = $("#searchSiteList").val();
    tagId      = $("#searchTagList").val();
    search(searchTerm, siteId, tagId);
}
