/**
 * Created by chris on 02.05.16.
 */
$(document).on('click', '#acceptInvites', function (evt) {
    evt.preventDefault();
    getAcceptInvitesPopup();
});

function getAcceptInvitesPopup() {
    onPopupShow();
    var senddata = {
        'id': -1,
        'task': 'popupInvite'
    };
    $.ajax({
        method: "POST",
        url: baseUrl + "Share/getAcceptInvitesPopup",
        data: JSON.stringify(senddata),
        dataType: "text"
    }).success(function (data) {
        $('<div>').html(data).appendTo('main');
        $('#acceptInvitePopup').show();
        $('#blackOverlay').show();
    }).fail(function (data) {
        if (data.status == 401) {
            window.location.reload();
        } else if (data.status == 404) {
            //No invites are left
            window.location.reload();
        }
    });
}

$(document).on('click', '.folderDropdownSelect', function (evt) {
    evt.preventDefault();
    var dropdown = $(this).parents('.selectFolderDropdown').parent();
    dropdown.find('.folderNameValue').val($(this).text());
    dropdown.find('.folderIdValue').val($(this).attr('data-tagID'));
});

$(document).on('submit', '#acceptInviteForm', function (evt) {
    evt.preventDefault();
    var form = this;
    var senddata = $(form).serialize();
    $.ajax({
        method: "POST",
        url: baseUrl + "Share/accept",
        data: senddata,
        dataType: "text"
    }).success(function (data) {
        getAcceptInvitesPopup();
    }).fail(function (data) {
        if (data.status == 400) {
            var msg = JSON.parse(data.responseText);
            $(form).find('.formMessage').text(msg.message).show().removeClass('hide');
        } else if (data.status == 401) {
            window.location.reload();
        }
    });
});

$(document).on('click', '.reject', function (evt) {
    evt.preventDefault();
    var form = $(this).parents('form');
    var senddata = form.serialize();
    $.ajax({
        method: "POST",
        url: baseUrl + "Share/reject",
        data: senddata,
        dataType: "text"
    }).success(function (data) {
        getAcceptInvitesPopup();
    }).fail(function (data) {
        if (data.status == 400) {
            var msg = JSON.parse(data.responseText);
            form.find('.formMessage').text(msg.message).show().removeClass('hide');
        } else if (data.status == 401) {
            p
            window.location.reload();
        }
    });
});