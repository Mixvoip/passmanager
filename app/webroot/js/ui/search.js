/**
 * Filter table MK2
 */
$(".searchInput").keyup(function () {
    var context = $(this).attr('data-searchBody');
    if ($(this).val() == '') {
        clearSearch(context);
        $(this).parents('div.search').find('.regexSelect').hide();
    } else {
        var globalSearch = false;
        if ($(this).parents('.search').hasClass('searchNav')) {
            $('.folderHead').hide();
            $('.passwordEntrys').hide();
            $('.searchable').addClass('limitHeaders');
            globalSearch = true;
        }
        var data = $(this).val();
        var useRegex = data.indexOf('/') === 0 && (data.split('/').length - 1) >= 2;
        //var useRegex = $(this).parents('div.search').children().find('.regexToggle').is(':checked');
        var rows = '';
        var prefix = data.substring(0, 2);
        if (!globalSearch) prefix = ''; //Disable prefix check for non global search bars
        //Prefix declaration
        switch (prefix) {
            case folderPrefix:
                //Search Folders
                data = data.substring(2, data.length);
                rows = $('.folderHead').find('tr');
                data = data.split(" ");
                $.each(data, function (i, v) {
                    rows.filter(function () {
                        return $(this).text().toLowerCase().indexOf(v.toLowerCase()) > -1;
                    }).parents('.folderHead').show().next('div .passwordEntrys').show().find('tr').show();
                });
                break;
            case folderJmpPrefix:
                //scroll to the folder in the folder list
            /*
                clearSearch(context);
                data = data.substring(2, data.length);
                var target = [];
                $('#folderSelect li').each(function () {
                    if ($(this).text().toLowerCase().indexOf(data.toLowerCase()) > -1) {
                        target.push($(this).attr('title'));
                    }
                });
                if (target.length > 0) {
                    var elm = $('#folderSelect').find('li[title="' + target[1] + '"]');
                    scrollToElementSideNav(elm);
                }
                break;
             */
            default:
                //Normal search
                rows = $(context).find("tr").hide();
                if (useRegex) {
                    console.log('Using Regex');
                    $(this).parents('div.search').find('.regexSelect').show();
                    data = data.slice(1);
                    var flag = data.slice(-1);
                    var pattern = null;
                    if (flag != '/') {
                        data = data.substring(0, data.length - 2);
                        pattern = new RegExp(data, flag);
                    } else {
                        data = data.substring(0, data.length - 1);
                        pattern = new RegExp(data);
                    }
                    //Match using regEx
                    rows.filter(function () {
                        return pattern.test($(rows).text());
                    }).show().parents('.passwordEntrys').show().prev('.folderHead').show();
                } else {
                    $(this).parents('div.search').find('.regexSelect').hide();
                    //Normal per Word match
                    data = data.split(" ");
                    $.each(data, function (i, v) {
                        rows.filter(function () {
                            return $(this).text().toLowerCase().indexOf(v.toLowerCase()) > -1;
                        }).show().parents('.passwordEntrys').show().prev('.folderHead').show();
                    });
                }
        }
    }
}).keyup(function (evt) {
    //Search enter key press
    if (evt.keyCode == 13) {
        var context = $(this).attr('data-searchBody');
        var rows = $(context).find("tr:visible");
        if (rows.length > 0) {
            var firstRow = rows.first();
            //check type of row
            if (firstRow.children().first().hasClass('passwordName')) {
                //It's a password
                firstRow.children().find('.showPassword').click();
            } else if (firstRow.parents('table').is('#usersTable')) {
                //It's a User
                firstRow.children().find('.editUserAction').click();
            } else if (firstRow.parents('table').is('#tagTable')) {
                //It's a Tag
                firstRow.children().find('.showTagAction').click();
            }
        }
    }
});


function clearSearch(context) {
    $(context).find("tr").show();
    $('.folderHead').show();
    $('.passwordEntrys').show();
    $('.searchable').removeClass('limitHeaders');
}