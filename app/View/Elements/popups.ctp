<div id="blackOverlay"></div>
<div id="confirmPopup" class="popup">
    <h4>Confirm Action</h4>
    <div id="closeConfirmPopup" class="closeBTN"><span class="glyphicon glyphicon-remove"></span></div>
    <div class="row">
        <p class="col-md-12">Message</p>
    </div>
    <div class="row">
        <button class="col-sm-offset-2 col-sm-2 btn btn-success" id="confirmPopup_ok">OK</button>
        <button class="col-sm-offset-4 col-sm-2 btn btn-danger" id="confirmPopup_cancel">Cancel</button>
    </div>
</div>
<?php
if (isset($mainView) && $mainView) {
    echo $this->element('viewPasswordPopup', array());
    echo $this->element('addFolderPopup', array());
    echo $this->element('show_shareLink', array());
}
?>