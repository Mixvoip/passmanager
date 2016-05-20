/**
 * Created by chris on 14.04.16.
 */

//################################### Button Handlers ###################################

//Add Folder Popup
$(document).on('click', '.addFolder', function (evt) {
    evt.preventDefault();
    $('#addFolderPopup').show();
    $('#blackOverlay').show();
});

//Delete Folder click
$(document).on('click', '.deleteFolder', function () {
    var id = $(this).parent().attr('data-folderID');
    showConfirmDialog('Delete Folder?', 'Are you sure to delete the Folder',
        'Yes', 'No', deleteFolder, closeConfirmPopup, id);
});

//Show private folder warning
$(document).on('click', '#addFolderPopup input[name="privateFolder"]', function () {
    if ($(this).is(':checked')) {
        $('#privateFolderWarning').slideDown();
    } else {
        $('#privateFolderWarning').slideUp();
    }
});

//Create Folder
$(document).on('submit', '#addFolderForm', function (evt) {
    evt.preventDefault();
    var sendData = $(this).serialize();
    $.ajax({
        method: "POST",
        url: baseUrl + "Folder/create",
        data: sendData,
        dataType: "text"
    }).success(function (data) {
        console.log(data);
        data = JSON.parse(data);
        if (data.shared == '1') {
            $('#addFolderForm').hide();
            $('#showShareLink').show();
            $('#blackOverlay').show();
            $('#sharefolderID').val(data.folderID);
            $('#shareLink').val(data.key).select();
        } else {
            window.location.reload();
        }
    }).fail(function (data) {
        if (data.status == 400) {
            var msg = JSON.parse(data.responseText);
            console.log(msg);
            $('#addFolderMsg').text(msg.message).removeClass('hide');
        } else if (data.status == 401) {
            window.location.reload();
        }
    });
});

//Close link popup when ok button is clicked
$(document).on('click', '#showShareLink button.okButton', closeShareLinkPopup);

//Get the edit password Popup from the server
$(document).on('click', '.editFolder', function () {
    onPopupShow();
    var id = $(this).parent().attr('data-folderID');
    var senddata = {
        "id": id,
        "task": "getFolderPopup"
    };
    $.ajax({
        method: "POST",
        url: baseUrl + "Folder/getEditPopup",
        data: JSON.stringify(senddata),
        dataType: "text"
    }).success(function (data) {
        $('<div>').html(data).appendTo('main');
        $('#editFolderPopup').show();
        $('#blackOverlay').show();
    }).fail(function (data) {
        if (data.status == 401) {
            window.location.reload();
        }
    });
});

//Get the share folder Popup from the server
function getSharePopup(id, msg, error) {
    onPopupShow();
    var senddata = {
        "id": id,
        "task": "getFolderSharePopup"
    };
    $.ajax({
        method: "POST",
        url: baseUrl + "Folder/getSharePopup",
        data: JSON.stringify(senddata),
        dataType: "text"
    }).success(function (data) {
        $('<div>').html(data).appendTo('main');
        $('#shareFolderPopup').show();
        $('#blackOverlay').show();
        $('#shareFolderPopup').find('.formMessage').hide();
        if (msg != '') {
            $('#shareFolderPopup').find('.alert-success').text(msg).show()
        }
        if (error != '') {
            $('#shareFolderPopup').find('.alert-danger').text(error).show()
        }
    }).fail(function (data) {
        if (data.status == 401) {
            window.location.reload();
        }
    });
}

$(document).on('click', '.shareFolder', function () {
    var id = $(this).parent().attr('data-folderID');
    getSharePopup(id, '', '');
});

//send the share to the server
$(document).on('submit', '#shareFolderPopup form', function (evt) {
    evt.preventDefault();
    var sendData = $(this).serialize();
    var id = $(this).find('input[name="folderID"]').val();
    $.ajax({
        method: "POST",
        url: baseUrl + "Folder/share",
        data: sendData,
        dataType: "text"
    }).success(function (data) {
        console.log(data);
        var dataObj = JSON.parse(data);
        $('#addFolderForm').hide();
        $('#showShareLink').show();
        $('#blackOverlay').show();
        $('#shareLink').val(dataObj.key);
        $('#sharefolderID').val(dataObj.folderID);
        //getSharePopup(id, data, '');
    }).fail(function (data) {
        if (data.status == 400) {
            var msg = JSON.parse(data.responseText);
            getSharePopup(id, '', msg.message);
        } else if (data.status == 401) {
            window.location.reload();
        }
    });
});

//Hide folder content
$(document).on('click', '.closeFolder', function () {
    $(this).parents('.folderHead').next('div').children().first().slideToggle();
    $(this).children().toggleClass('hide');
});

//#################################### Action Handler #######################################

//Delete
function deleteFolder(id, evt) {
    var senddata = {'task': 'deleteFolder', 'id': id};
    $.ajax({
        method: "POST",
        url: baseUrl + "Folder/delete",
        async: true,
        dataType: 'text',
        contentType: "application/json",
        data: JSON.stringify(senddata),
        success: function (data) {
            window.location.reload();

            closeConfirmPopup(); //Fallback if reload isn't working
            //Remove all behind ?
            /*var oldLocation=(window.location).split('?')[0];
             window.location=oldLocation+"?msgType=success&msg=The Folder has been removed";*/
        },
        fail: function (data) {
            if (data.status == 401) {
                window.location.reload();
            }
        }
    });
}