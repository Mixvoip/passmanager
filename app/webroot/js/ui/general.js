/**
 * Created by chris on 06.04.16.
 */
//UI Config
var autoClearTimeout = 20000;
var hideMessageTimeOut = 5000;
var reloadFavorites = false;
//END

var timeoutID;
var hideMessageTimerID;
//Flash Message Close
$(document).on('click', '.messageClose', function () {
    $(this).parent().slideUp();
});

//#################### General Popup Management ###################################

//ClosePopup
$(document).on('click', '.closeBTN', function (evt) {
    evt.preventDefault();
    if ($(this).parent().is('#showShareLink')) {
        closeShareLinkPopup();
    } else {
        $(this).parent().hide();
        $('#blackOverlay').hide();

        if ($(this).parents('.popup').hasClass('tmpPopup')) {
            $(this).parents('.popup').parent().remove();
        }

        //Remove password and user name
        clearFields();
        if ($(this).parents('.popup').hasClass('reloadOnClose')) {
            window.location.reload();
        }
    }
    onPopupHide();
});

//Close share link Popup
function closeShareLinkPopup() {
    $('#shareLink').text('');
    $('#showShareLink').hide();
    $('#blackOverlay').hide();
    onPopupHide();
    window.location.reload();
}

function onPopupShow() {
    lockScroll();
}

function onPopupHide() {
    unlockScroll();
}

// ############################## Confirm dialog #####################################

function showConfirmDialog(title, msg, okBtnText, cancelBtnText, callback_ok, callback_cancel, callback_param) {
    onPopupShow();
    $('#blackOverlay').show();
    $('#confirmPopup').show().find('h4').text(title);
    $('#confirmPopup').find('p').text(msg);
    $('#confirmPopup_ok').text(okBtnText);
    $('#confirmPopup_cancel').text(cancelBtnText);
    $(document).on('click', '#confirmPopup_ok', function (evt) {
        callback_ok(callback_param, evt);
    });
    $(document).on('click', '#confirmPopup_cancel', function (evt) {
        callback_cancel(callback_param, evt);
    });
}

function closeConfirmPopup() {
    $('#confirmPopup').hide();
    $('#blackOverlay').hide();
    onPopupHide();
}

// ############################## Change Password Functions #####################################

//Check if Password match
$('.passwordMatch').on('submit', function (evt) {
    var pw = $('.srcPassword').val();
    if (pw != $('.matchPassword').val()) {
        evt.preventDefault();
        $('.srcPassword').addClass('passwordError');
        $('.matchPassword').addClass('passwordError');
        $('.passwordAlert').text("Passwords don't match").slideDown();
    } else {
        if (pw.length < minPwLength) {
            evt.preventDefault();
            $('.srcPassword').addClass('passwordError');
            $('.matchPassword').addClass('passwordError');
            $('.passwordAlert').text("Password must be at least " + minPwLength + " characters").slideDown();
        }
    }
});

//Tag form helper
$(document).on('click', '.tagSelect', function (evt) {
    evt.preventDefault();
    var dropdown = $(this).parents('.selectTagDropdown').parent();
    dropdown.find('.tagNameValue').val($(this).text());
    dropdown.find('.tagIdValue').val($(this).attr('data-tagID'));
});

//Images
$('.disabled a').click(function (evt) {
    evt.preventDefault();
    alert("This isn't implemented at the moment. Shame on me :(");
});

//show tags
$(document).on('click', '.showTags', function (evt) {
    $(this).next('ul').slideToggle();
    $(this).children().toggleClass('hide');
});

// ############################## Helper Functions #####################################

//Auto hide passwords
function delayedHide() {
    timeoutID = window.setTimeout(clearFields, autoClearTimeout);
}

function clearFields() {
    //Remove password and user name
    $('#encUsername').val('');
    $('#encPassword').val('');
    $('input[type=password]').val('');
    //Clear clipboard
    clearHide();
}

function clearHide() {
    window.clearTimeout(timeoutID);
}

function autoMessageHide() {
    hideMessageTimerID = window.setTimeout(autoHideMessageAction, hideMessageTimeOut);
}

function autoHideMessageAction() {
    $('.messageClose').click();
    clearMessageTimeOut();
}

function clearMessageTimeOut() {
    window.clearTimeout(hideMessageTimerID);
}

$(".clearSearch").click(function (evt) {
    $('.searchInput').val('');
    var context = $(this).parents('.search').children().find('.searchInput').attr('data-searchBody');
    clearSearch(context);
});

//scroll to element
function scrollToElement(elementSelector) {
    $('html, body').animate({
        scrollTop: $(elementSelector).offset().top
    }, 500);
}

function scrollToElementSideNav(elm) {
    $('#folderSelect').animate({
        scrollTop: elm.offset().top
    }, 10);
}

$(document).on('click', '.scrollToTop', function () {
    scrollToElement('body');
});

//lock scroll
function lockScroll() {
    $('html, main').css({
        'overflow': 'hidden',
        'height': '100%'
    });
}

function unlockScroll() {
    $('html, main').css({
        'overflow': 'auto',
        'height': 'auto'
    });
}

function setCookie(cname, cvalue, exdays) {
    var d = new Date();
    d.setTime(d.getTime() + (exdays * 24 * 60 * 60 * 1000));
    var expires = "expires=" + d.toUTCString();
    document.cookie = cname + "=" + cvalue + "; " + expires;
}

function getCookie(cname) {
    var name = cname + "=";
    var ca = document.cookie.split(';');
    for (var i = 0; i < ca.length; i++) {
        var c = ca[i];
        while (c.charAt(0) == ' ') {
            c = c.substring(1);
        }
        if (c.indexOf(name) == 0) {
            return c.substring(name.length, c.length);
        }
    }
    return "";
}

//############################# Reload for Favorites ################################

$(document).on('click', '#favTabControl', function (evt) {
    if (reloadFavorites) {
        evt.preventDefault();
        setCookie('reloadFavorites', '1', 2);
        window.location.reload();
    }
});

//############################# Change password #####################################

$(document).on('click', '#changePasswordFormButton', function (evt) {
    evt.preventDefault();
    console.log('Here');
    showConfirmDialog('Change Password', "Do you want to change your password? (This can take some time and after that you will be logged out)",
        'Yes', 'No', changePassword, closeConfirmPopup, evt);
});

function changePassword(evt) {
    $('#changePasswordForm').submit();
}

// ############################# JQ Ready ###########################################
$(document).ready(function () {

    //Auto hide messages
    autoMessageHide();

    $('#usersTable').tablesorter({
        headers: {
            4: {
                sorter: false
            }
        }
    });
    if ($("#usersTable").length > 0) $(this).tablesorter({sortList: [[0, 0]]});
    $('#tagTable tbody tr').tablesorter({
        headers: {
            4: {
                sorter: false
            }
        }
    });
    if ($("#tagTable").length > 0) $(this).tablesorter({sortList: [[0, 0]]});

    $('.passwordEntrys table').tablesorter({
        headers: {
            3: {
                sorter: false
            }
        }
    });
    if ($(".passwordEntrys table tbody tr").length > 0)
        $(this).tablesorter({sortList: [[0, 0]]});

    //jmp to favorites if necessary
    var cookieVal = getCookie('reloadFavorites');
    if (cookieVal == '1') {
        $('.folderNav a[href="#fav"]').tab('show');
        setCookie('reloadFavorites', '0', 2);
    }

    //auto focus search on load
    $('.searchInput').focus();
});


//Key Bindings
$(window).keypress(function (evt) {
    //ctrl+y focus search.
    //evt.charCode == 121 for Firefox, evt.charCode == 26 for chrome/opera and evt.charCode == 25 for edge
    if ((evt.charCode == 26 || evt.charCode == 121 || evt.charCode == 25) && evt.ctrlKey) {
        //try to stop the default action
        //evt.stopPropagation();
        evt.preventDefault();
        $('.searchInput').focus();
        return false;
    }
});