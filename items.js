function itemToDiv(item, index)
{
    var itemIdValue = item.itemId; //sigh, something is nuking this value

    var div      = $("<div>");
    div.attr('class', 'contentItem');
    
    var title    = $("<a>").text(item.siteName + ': ' + item.title);
    title.attr('href', item.source);
    
    var when     = $("<b>").text(item.timestamp + "   ");

    var saveText = $("<sup>").text("[save destination page]");
    var saveLink = $("<a>").text("");
    saveLink.append(saveText);
    saveLink.attr('href', '#');
    saveLink.attr('class', 'saveLink');
    saveLink.attr('itemId', itemIdValue);
    saveLink.click( function() {
        var id = parseInt($(this).attr('itemId'));
        $.post("savePage.php",
        {
            itemId: id
        }, function(data) {
            data = decodeJSON(data);
            if (data)
                addStatusMessage(data.message);
        });
    });

    var contents = $("<div>").html(item.contents);

    div.append(when);
    div.append(saveLink);
    div.append($("<br>"));
    div.append(title);
    div.append($("<br>"));
    div.append(contents);
    div.append($("<br>"));

    //tags
    var tagsDiv    = $("<div>");
    var tagSelect = $("<select>");

    tagsDiv.attr('id', 'tagsDiv' + index);

    tagSelect.attr('id', 'tagSelect' + index);
    tagSelect.attr('itemId', itemIdValue);
    tagSelect.attr('tagsDivId', '#tagsDiv' + index);
    tagSelect.change(function() {
        //oh there has to be a better way of doing this...
        var tagId = $(this).val();
        if (tagId == -1)
            return;

        var elementName = '#' + $(this).attr('id');
        var tagText     = $(elementName+' option:selected').text();
        var tagsDivId   = $(this).attr('tagsDivId');

        $.post("changeItemTag.php",
        {
            action:  'add',
            item:    itemIdValue,
            tag:     tagId,
            text:    tagText,
            select:  elementName,
            div:     tagsDivId
        },
        function(data, status) {
            data = decodeJSON(data);
            if (!data || !data.select) //failure handled by decodeJSON
                return;

            $(data.select+" option[value='"+data.tag+"']").remove();
            $(data.div).append(makeTagSpan(data.select, data.item, data.tag, data.text));
        });
    });

    tagsDiv.append(tagSelect);
    tagsDiv.append("&nbsp;");
    div.append(tagsDiv);

    //remove tags from the select list if they've already been applied to the item and make tag spans out of them
    var noShow = new Array(); //array of tags not to display in the list
    if (item.tags)
    {
        for (var x = 0; x < item.tags.length; x++)
        {
            tagsDiv.append(makeTagSpan("#tagSelect" + index, itemIdValue, item.tags[x].tagId, item.tags[x].name));
            tagsDiv.append("&nbsp;");
            noShow.push(parseInt(item.tags[x].tagId));
        }
    }
    tagSelect.attr('noShow', noShow);

    return div;         
}

function markRead(site, item)
{
    $('#content').attr('pendingMarks', parseInt($('#content').attr('pendingMarks')) + 1);

    $.post("markItem.php",
    {
        itemId:item,
        siteId:site,
        action:'read'
    }, function(data) {
        data = decodeJSON(data);
        if (!data)
            return;
        $('#content').attr('pendingMarks', parseInt($('#content').attr('pendingMarks')) - 1);
        decreaseCount(data.siteId);
    });
}

function loadItems(siteId)
{
    currentSiteId = $('#content').attr('currentSiteId');

    clearContent();

    $('#content').attr('currentSiteId', siteId);
    lastItemId = $('#counter-1').attr('lastItemId');

    $('#content').attr('itemCount', 0);

    $.post("getItems.php",
    {
        site       : siteId,
        lastId     : lastItemId,
        maxItems   : 10,
    },
    function(data, status) {
        data = decodeJSON(data);
        if (!data || !data.items || !data.items.length)
        {
            var div = $("<div>").text('Nothing to see here.');
            $("#content").append(div);
            return;
        }

        for (var x = 0; x < data.items.length; x++)
        {
            var div      = itemToDiv(data.items[x], x);
            $("#content").append(div);

            var hr       = $("<hr>");
            var itemName = 'item' + data.items[x].itemId;
            hr.attr('id', itemName);
            hr.attr('itemId', data.items[x].itemId);
            hr.attr('siteId', data.items[x].siteId);
            $("#content").append(hr);

            $(hr).waypoint(function() {
                id   = $(this).attr('itemId');
                site = $(this).attr('siteId');
                markRead(site, id);
            }, { context: '.contents', triggerOnce: true });
        }

        $('#content').attr('itemCount', data.items.length);

        //next item set loader waypoint
        var nextPageDiv = $("<div>").text('The next items will load when this hits the top of the window...');
        nextPageDiv.attr('id', 'nextPageDiv');
        $("#content").append(nextPageDiv);

        //next page event occurs when this div scrolls to the end
        $("#container").scroll(function() {
            var container = $('#container');
            if (parseInt(container.attr('busy')) == 1)
                return;

            var content   = $('#content');
            var position  = Math.abs(content.offset().top) + container.height() + container.offset().top + 5; //+5 because it sometimes comes up a bit short
            var height    = content.outerHeight();
            if (height > 0 && position >= height)
            {
                container.attr('busy', 1);
                window.setTimeout(nextPage, 100);
            }
        });        

        //padding at the bottom to enable the HR of the last item to hit the top of the screen and trigger it being read
        var footerDiv = $("<div>");
        footerDiv.attr('id',    'footerDiv');
        footerDiv.attr('class', 'nextPagePadding');
        $("#content").append(footerDiv);
        loadTags();
        $('#container').attr('busy', 0);
        $('#content').focus();
    });
}
