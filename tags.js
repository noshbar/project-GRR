function onAddTag()
{
    var tagName = $("#addTagName").val();
    addTag(tagName);
}

function makeTagSpan(selectBoxName, itemId, tagId, text)
{
    var tagSpan = $('<span>').text(text + ' ');
    tagSpan.attr('class',     'tag');
    tagSpan.attr('id',        'tagSpan' + itemId + "_" + tagId);
    tagSpan.attr('tagId',     tagId);

    var hideButton  = $('<img>');
    hideButton.attr('src', 'delete.png');

    var hideTag     = $('<a>');
    hideTag.append(hideButton);
    hideTag.attr('href',      '#');
    hideTag.attr('itemId',    itemId);
    hideTag.attr('tagText',   text);
    hideTag.attr('tagId',     tagId);
    hideTag.attr('selectBox', selectBoxName);
    hideTag.click(function() {
        var itemId    = $(this).attr('itemId');
        var tagText   = $(this).attr('tagText');
        var tagId     = $(this).attr('tagId');
        var selectBox = $(this).attr('selectBox');
        var selectDiv = $(selectBox).attr('tagsDivId');

        $.post("changeItemTag.php",
        {
            action:  'remove',
            item:    itemId,
            tag:     tagId,
            text:    tagText,
            select:  selectBox,
            div:     selectDiv
        },
        function(data, status) {
            data = decodeJSON(data);
            if (!data || !data.select) //failure handled by decodeJSON
                return;

            var option  = $("<option>").text(data.text);
            option.attr('value', data.tag);
            $('#tagSpan'+data.item+'_'+data.tag).remove();
            $(data.select).append(option);
        });                 
    });

    tagSpan.append(hideTag);
    return tagSpan;
}

function addTag(tagName)
{
    $.post("addTag.php",
    {
        name: tagName
    }, function(data) {
        data = decodeJSON(data);
        if (!data)
            return;

        $('#addTagName').val('');
        $('#addTagName').attr('placeholder', data.result);
        addStatusMessage(data.result, true);
        loadTags();
    });
}

function removeTag(tagId)
{
    $.post("removeTag.php",
    {
        id: tagId
    }, function(data) {
        data = decodeJSON(data);
        if (!data)
            return;

        loadTags();
        $("span").each(function(index) {
            var tag = $(this).attr('tagId');
            if (tag && parseInt(tag) == parseInt(tagId))
                $(this).remove();
        });
    });
}

function loadTags()
{
    $.post("getTags.php",
    {
    }, function(data) {
        data = decodeJSON(data);
        if (!data.tags)
            return;

        var selects = new Array();
        selects.push('#searchTagList');
        selects.push('#availableTags');
        contentCount = parseInt($('#content').attr('itemCount'));
        for (var item = 0; item < contentCount; item++)
            selects.push('#tagSelect' + item);

        for (var item = 0; item < selects.length; item++)
        {
            $(selects[item]).empty();

            if (item == 0 || item > 1)
            {
                var text = (item == 0) ? 'any tag' : 'Select tag to add';
                var option = $("<option>").text(text);
                option.attr('value', -1);
                $(selects[item]).append(option);
            }
            
            var noShow = $(selects[item]).attr('noShow');
            for (var x = 0; x < data.tags.length; x++)
            {
                if (!noShow || !contains(noShow, data.tags[x].id))
                {
                    var option = $("<option>").text(data.tags[x].name);
                    option.attr('value', data.tags[x].id);
                    $(selects[item]).append(option);
                }
            }
        }
    });
}            