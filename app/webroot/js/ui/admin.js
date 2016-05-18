/**
 * Created by chris on 18.04.16.
 */
//############################### Edit User ##################################
$(document).on('click', '.editUserAction', function (evt) {
    onPopupShow();
    var id = $(this).parents('tr').attr('data-userID');
    var senddata = {
        "id": id,
        "task": "getEditUserPopup"
    };
    $.ajax({
        method: "POST",
        url: baseUrl + "Users/getEditPopup",
        data: JSON.stringify(senddata),
        dataType: "text"
    }).success(function (data) {
        $('<div>').html(data).appendTo('main');
        $('#editUserPopup').show();
        $('#blackOverlay').show();
    }).fail(function (data) {
        if (data.status == 401) {
            window.location.reload();
        }
    });
});

$(document).on('submit', '#editUserForm', function (evt) {
    evt.preventDefault();
    var form = this;
    var senddata = $(form).serialize();
    $.ajax({
        method: "POST",
        url: baseUrl + "Users/edit",
        data: senddata,
        dataType: "text"
    }).success(function (data) {
        $('.closeBTN').click();
        $('#usersTable').find('tbody').remove();
        $('<tbody>').html(data).appendTo('#usersTable');
        refreshTagTable();
    }).fail(function (data) {
        if (data.status == 400) {
            var msg = JSON.parse(data.responseText);
            $(form).find('.formMessage').text(msg.message).show();
        } else if (data.status == 401) {
            window.location.reload();
        }
    });
});

$(document).on('submit', '#createUser', function (evt) {
    evt.preventDefault();
    var form = this;
    var senddata = $(form).serialize();
    $.ajax({
        method: "POST",
        url: baseUrl + "Users/create",
        data: senddata,
        dataType: "text"
    }).success(function (data) {
        $('#usersTable').find('tbody').remove();
        $('<tbody>').html(data).appendTo('#usersTable');
        refreshTagTable();
        //reset form:
        $('.clearInput').val('');
    }).fail(function (data) {
        if (data.status == 400) {
            var msg = JSON.parse(data.responseText);
            $(form).find('.formMessage').text(msg.message).show();
        } else if (data.status == 401) {
            window.location.reload();
        }
    });
});

$(document).on('click', '.lockUserAction', function (evt) {
    evt.preventDefault();
    var userID = $(this).parent().parent().attr('data-userID');
    var sentdata = {
        'id': userID,
        'task': 'toggleBlock'
    };
    $.ajax({
        method: "POST",
        url: baseUrl + "Users/toggleUserBlock",
        data: JSON.stringify(sentdata),
        dataType: "text"
    }).success(function (data) {
        $('#usersTable').find('tbody').remove();
        $('<tbody>').html(data).appendTo('#usersTable');
    }).fail(function (data) {
        if (data.status == 401) {
            window.location.reload();
        }
    });
});

$(document).on('click', '.deleteUserAction', function (evt) {
    evt.preventDefault();
    var userID = $(this).parent().parent().attr('data-userID');
    var sentdata = {
        'id': userID,
        'task': 'deleteUser'
    };
    showConfirmDialog("Confirm Delete", "Do you want to delete the User?",
        "Yes", "No", deleteUser, closeConfirmPopup, sentdata);
});

function deleteUser(sentdata) {
    $.ajax({
        method: "POST",
        url: baseUrl + "Users/delete",
        data: JSON.stringify(sentdata),
        dataType: "text"
    }).success(function (data) {
        closeConfirmPopup();
        $('#usersTable').find('tbody').remove();
        $('<tbody>').html(data).appendTo('#usersTable');
        refreshTagTable();
    }).fail(function (data) {
        if (data.status == 401) {
            window.location.reload();
        }
    });
}

//Add Tag
$(document).on('submit', '#createTag', function (evt) {
    evt.preventDefault();
    var form = this;
    var senddata = $(form).serialize();
    $.ajax({
        method: "POST",
        url: baseUrl + "Tags/create",
        data: senddata,
        dataType: "text"
    }).success(function (data) {
        closeConfirmPopup();
        $('#tagTable').find('tbody').remove();
        $('<tbody>').html(data).appendTo('#tagTable');
        refeshTagsDorpdown();
    }).fail(function (data) {
        if (data.status == 400) {
            var msg = JSON.parse(data.responseText);
            $(form).find('.formMessage').text(msg.message).show();
        } else if (data.status == 401) {
            window.location.reload();
        }
    });
});

//Show tag
$(document).on('click', '.showTagAction', function (evt) {
    onPopupShow();
    var id = $(this).parents('tr').attr('data-tagid');
    var senddata = {
        "id": id,
        "task": "getShowTagAction"
    };
    $.ajax({
        method: "POST",
        url: baseUrl + "Tags/getShowPopup",
        data: JSON.stringify(senddata),
        dataType: "text"
    }).success(function (data) {
        $('<div>').html(data).appendTo('main');
        $('#showTag').show();
        $('#blackOverlay').show();
        $('#showTag').find('table').tablesorter({
            headers: {
                2: {
                    sorter: false
                }
            },
            sortList: [[0, 0]]
        });
    }).fail(function (data) {
        if (data.status == 401) {
            window.location.reload();
        }
    });
});

//Remove user from tag
$(document).on('click', '.removeUserFromTagAction', function (evt) {
    onPopupShow();
    var userID = $(this).parent().parent().attr('data-userid');
    var tagID = $('#showTag').attr('data-tagid');
    var sentdata = {
        'id': userID,
        'tagID': tagID,
        'task': 'removeUserFromTag'
    };
    $.ajax({
        method: "POST",
        url: baseUrl + "Tags/removeUser",
        data: JSON.stringify(sentdata),
        dataType: "text"
    }).success(function (data) {
        $('#tagTable').find('tbody').remove();
        $('<tbody>').html(data).appendTo('#tagTable');
        $('.closeBTN').click();
    }).fail(function (data) {
        if (data.status == 401) {
            window.location.reload();
        }
    });
});

//Delete Tag
$(document).on('click', '.deleteTagAction', function (evt) {
    onPopupShow();
    //Get the delete Popup
    var id = $(this).parents('tr').attr('data-tagid');
    var sentdata = {
        'id': id,
        'task': 'getDeletePopup'
    };
    $.ajax({
        method: "POST",
        url: baseUrl + "Tags/deletePopup",
        data: JSON.stringify(sentdata),
        dataType: "text"
    }).success(function (data) {
        $('<div>').html(data).appendTo('main');
        $('#DeleteTag').show();
        $('#blackOverlay').show();
    }).fail(function (data) {
        if (data.status == 401) {
            window.location.reload();
        }
    });
});

$(document).on('submit', '#DeleteTagForm', function (evt) {
    evt.preventDefault();
    var form = this;
    var senddata = $(form).serialize();
    $.ajax({
        method: "POST",
        url: baseUrl + "Tags/delete",
        data: senddata,
        dataType: "text"
    }).success(function (data) {
        $('#tagTable').find('tbody').remove();
        $('<tbody>').html(data).appendTo('#tagTable');
        $('.closeBTN').click();
        refeshTagsDorpdown();
    }).fail(function (data) {
        if (data.status == 400) {
            var msg = JSON.parse(data.responseText);
            $(form).find('.formMessage').text(msg.message).show();
        } else if (data.status == 401) {
            window.location.reload();
        }
    });
});

//Edit Tag
$(document).on('click', '.editTagAction', function (evt) {
    onPopupShow();
    //Get the edit Popup
    var id = $(this).parents('tr').attr('data-tagid');
    var sentdata = {
        'id': id,
        'task': 'getEditPopup'
    };
    $.ajax({
        method: "POST",
        url: baseUrl + "Tags/editPopup",
        data: JSON.stringify(sentdata),
        dataType: "text"
    }).success(function (data) {
        $('<div>').html(data).appendTo('main');
        $('#EditTag').show();
        $('#blackOverlay').show();
    }).fail(function (data) {
        if (data.status == 401) {
            window.location.reload();
        }
    });
});

$(document).on('submit', '#EditTagForm', function (evt) {
    evt.preventDefault();
    var form = this;
    var senddata = $(form).serialize();
    $.ajax({
        method: "POST",
        url: baseUrl + "Tags/edit",
        data: senddata,
        dataType: "text"
    }).success(function (data) {
        $('#tagTable').find('tbody').remove();
        $('<tbody>').html(data).appendTo('#tagTable');
        $('.closeBTN').click();
        refeshTagsDorpdown();
    }).fail(function (data) {
        if (data.status == 400) {
            var msg = JSON.parse(data.responseText);
            $(form).find('.formMessage').text(msg.message).show();
        } else if (data.status == 401) {
            window.location.reload();
        }
    });
});

//Get Tag Table for refresh
function refreshTagTable() {
    var sentdata = {
        'id': -1,
        'task': 'refreshTags'
    };
    $.ajax({
        method: "POST",
        url: baseUrl + "Tags/refresh",
        data: JSON.stringify(sentdata),
        dataType: "text"
    }).success(function (data) {
        $('#tagTable').find('tbody').remove();
        $('<tbody>').html(data).appendTo('#tagTable');
        $('.closeBTN').click();
    }).fail(function (data) {
        if (data.status == 401) {
            window.location.reload();
        }
    });
}

//Get and set the freshly added Tags to the dropdown selector
function refeshTagsDorpdown() {
    var sentdata = {
        'id': -1,
        'task': 'refreshTags'
    };
    $.ajax({
        method: "POST",
        url: baseUrl + "Tags/refreshDropdown",
        data: JSON.stringify(sentdata),
        dataType: "text"
    }).success(function (data) {
        $('.selectTagDropdown ul').find('.tagGenerated').remove();
        $('.selectTagDropdown ul').append(data);
    }).fail(function (data) {
        if (data.status == 401) {
            window.location.reload();
        }
    });
}

//Hide forms
$(document).on('click', '.openForm', function (evt) {
    var target = $(this).attr('data-target');
    $(this).next(target).slideToggle('', function () {
        scrollToElement(target);
    });
    $(this).children().toggleClass('hide');
});

//On header Click
$(document).on('click', '.openFormHeader', function (evt) {
    $(this).next('.openForm').click();
});