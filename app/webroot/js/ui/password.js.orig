/**
 * Created by chris on 14.04.16.
 */

//################################### Button Handlers ###################################

//Display show Password Popup
$(document).on('click', '.showPassword', function () {
    onPopupShow();
    var parentNode = $(this).parent().parent();
    var id = $(this).parents('tr').attr('data-id');
    var pw_name = $(parentNode).children('.passwordName').text();
    var desc = $(parentNode).children('.passwordDescription').html();
    var tagname = $(parentNode).children('.passwordTag').text();
    var folderID = $(this).parents('.passwordEntrys').prev().find('[data-folderid]').attr('data-folderid');
    var url = null;
    var valid = '0';

    //Check if url is set
    var attr = $(parentNode).children('.passwordName').attr('data-siteurl');

    // For some browsers, `attr` is undefined; for others, `attr` is false. Check for both.
    if (typeof attr !== typeof undefined && attr !== false) {
        // Element has this attribute
        url = $(parentNode).children('.passwordName').attr('data-siteurl');
        valid = $(parentNode).children('.passwordName').attr('data-urlvalid');
    }

    $('#viewPasswordPopupID').val(id);
    $('#viewPasswordFolderID').val(folderID);
    if (url != '') {
        if (valid == '1') {
            $('.pageLink').html('');
            $('<a>').attr('href', url).attr('target', '_blank').text(pw_name).appendTo('#viewPasswordPopup .pageLink');
            $('#urlCopyLink').attr('href', url);
            $('#urlCopyLink').removeClass('disabled');
        } else {
            $('#urlCopyLink').addClass('disabled');
            $('#urlCopyLink').attr('href', '');
        }
        $('#urlCopy').val(url);
    } else {
        $('#viewPasswordPopup .pageLink').text(pw_name);
        $('#urlCopyLink').attr('href', '');
        $('#urlCopyLink').addClass('disabled');
    }
    $('#viewPasswordPopup .passwordDescription').html(desc);
    $('#viewPasswordPopup .passwordTag').val(tagname);

    $('#viewPasswordPopup').show();
    $('#blackOverlay').show();
});

//Add Password 
$(document).on('click', '.addPassword', function () {
    onPopupShow();
    var id = $(this).parent().attr('data-folderid');
    $.ajax({
        method: "POST",
        url: baseUrl + "Passwords/getAddPopup",

        dataType: "text"
    }).success(function (data) {
        $('<div>').html(data).appendTo('main');
        $('#addPasswordPopup').find('input:hidden[name="folderID"]').val(id);
        $('#addPasswordPopup').show();
        $('#blackOverlay').show();
    }).fail(function (data) {
        if (data.status == 401) {

        }
    });
});

//Get the edit password Popup from the server

function getEditPasswordPopup(id, folderID) {
    onPopupShow();
    var senddata = {
        "id": id,
        "task": "getPasswordPopup"
    };
    $.ajax({
        method: "POST",
        url: baseUrl + "Passwords/getEditPopup",
        data: JSON.stringify(senddata),
        dataType: "text"
    }).success(function (data) {
        $('<div>').html(data).appendTo('main');
        $('#editPasswordPopup').find('input:hidden[name="folderID"]').val(folderID);
        $('#editPasswordPopup').show();
        $('#blackOverlay').show();
    }).fail(function (data) {
        if (data.status == 401) {
            window.location.reload();
        }
    });
}

$(document).on('click', '.editPassword', function () {
    var id = $(this).parents('tr').attr('data-id');
    var folderID = $(this).parents('.passwordEntrys').find('[data-folderid]').attr('data-folderid');
    getEditPasswordPopup(id, folderID);
});

$('#editPasswordShow').on('click', function (evt) {
    //just to be sure
    evt.preventDefault();
    evt.stopPropagation();
    evt.stopImmediatePropagation();

    var id = $('#viewPasswordPopupID').val();
    var folderID = $('#viewPasswordFolderID').val();
    $('#viewPasswordPopup').hide();

    getEditPasswordPopup(id, folderID);
});

//Delete button on click action
$(document).on('click', '.deletePassword', function () {
    $('#editPasswordPopup').hide();
    $('#editPasswordPopup').parent().remove();
    var id = $(this).parents('form').find('input[name="passwordID"]').val();
    showConfirmDialog('Delete Password?', 'Are you sure to delete the Password',
        'Yes', 'No', deletePassword, closeConfirmPopup, id);
});

//Favorite Password
$(document).on('click', '.favoritePassword', function () {
    var id = $(this).parents('tr').attr('data-id');
    var senddata = {
        "id": id,
        "task": "favoritePassword"
    };
    var elm = $(this);
    $.ajax({
        method: "POST",
        url: baseUrl + "Passwords/favorite",
        data: JSON.stringify(senddata),
        dataType: "JSON"
    }).success(function (data) {
        reloadFavorites = true;
        var elementsToChange = elm.find('span');
        if (data.newFav == '0') {
            elementsToChange.removeClass('glyphicon-star').addClass('glyphicon-star-empty');
        } else {
            elementsToChange.removeClass('glyphicon-star-empty').addClass('glyphicon-star');
        }
    }).fail(function (data) {
        if (data.status == 401) {
            window.location.reload();
        }
    });
});

//#################################### Action Handler #######################################

//Send Add Password to server
$(document).on('submit', '#addPasswordForm', function (evt) {
    evt.preventDefault();
    var form = this;
    var folderID = $(form).find('input[name=folderID]').val();
    var sendData = $(form).serialize() + '&task=addPassword';
    console.log(folderID);
    $.ajax({
        method: "POST",
        url: baseUrl + "Passwords/add",
        data: sendData
    }).success(function (data) {
        window.location.reload();
        $('.closeBTN').click();
    }).fail(function (data) {
        if (data.status == 400) {
            var msg = JSON.parse(data.responseText);
            $(form).find('.formMessage').text(msg.message).show();
            $('.clearInput').val('');
        } else if (data.status == 401) {
            window.location.reload();
        }
    });


});

//Show password
$(document).on('submit', '#viewPasswordForm', function (evt) {
    evt.preventDefault();
    var sendData = $(this).serialize() + '&task=showPassword';
    $.ajax({
        method: "POST",
        url: baseUrl + "Passwords/show",
        data: sendData,
        dataType: 'json'
    }).done(function (data) {
        if (data.error == 1) {
            $('#viewPasswordMsg').show().text(data.msg);
        } else {
            $('#viewPasswordMsg').hide();
            $('#encUsername').val(data.username);
            $('#encPassword').val(data.password).select();
            try {
                //We try to copy
                document.execCommand("copy");
            } catch (err) {
                //but I know that it will fail
                //see: http://stackoverflow.com/questions/31925944/execcommandcopy-does-not-work-in-xhr-callback
                console.error('Auto copy failed');
                console.error(err);
            }
            delayedHide();
        }
    }).fail(function (data) {
        if (data.status == 401) {
            window.location.reload();
        }
    });
});

//Delete
function deletePassword(id) {
    var senddata = {'task': 'deletePassword', 'id': id};
    $.ajax({
        method: "POST",
        url: baseUrl + "Passwords/delete",
        async: true,
        dataType: 'text',
        contentType: "application/json",
        data: JSON.stringify(senddata)
    }).success(function (data) {
        window.location.reload();

        closeConfirmPopup(); //Fallback if reload isn't working
        //Remove all behind ?
        /*var oldLocation=(window.location).split('?')[0];
         window.location=oldLocation+"?msgType=success&msg=The Folder has been removed";*/
    }).fail(function (data) {
        if (data.status == 401) {
            window.location.reload();
        }
    });
}

//Copy Password username
new Clipboard('.copyToClipboard');