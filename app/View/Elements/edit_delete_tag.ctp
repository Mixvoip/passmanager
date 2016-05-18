<?php if ($tag['Tag']['id'] == Configure::read('RootTag') && $mode == 'Delete') {
    $disable = 'disabled';
} else {
    $disable = '';
} ?>
<div id="<?= $mode ?>Tag" class="popup tmpPopup">
    <h4><?= $mode ?> Tag</h4>
    <div id="closeAddFolderPopup" class="closeBTN"><span class="glyphicon glyphicon-remove"></span></div>
    <form id="<?= $mode ?>TagForm" class="row">
        <p id="editFolderMsg" class="alert alert-danger hide"></p>
        <?php if ($mode == 'Delete') { ?>
            <p class="col-md-12">
                Are you sure to remove the Tag? You can choose a replace tag below for user and passwords
                <?= $disable != '' ? '<br><em>!! You cannot remove the root tag !!</em>' : '' ?>
            </p>
        <?php } ?>
        <div class="col-md-12">
            <div class="input-group">
                <span id="desc_tag_name" class="input-group-addon">Tag name</span>
                <input name="tagname" class="form-control" aria-describedby="desc_tag_name"
                       value="<?= $tag['Tag']['name'] ?>" placeholder="Tag Name" required
                    <?= $mode == 'Delete' ? 'readonly' : '' ?>>
            </div>
        </div>
        <div class="col-md-5">
            <div class="input-group">
                <div class="input-group-btn selectTagDropdown">
                    <button type="button" class="btn btn-default dropdown-toggle" data-toggle="dropdown"
                            aria-haspopup="true" aria-expanded="false">Select Parent Tag<span class="caret"></span>
                    </button>
                    <ul class="dropdown-menu">
                        <li><a class="tagSelect" data-tagID="<?= $parent['Tag']['id'] ?>"
                               href="#"><?= $parent['Tag']['name'] ?></a></li>
                        <li role="separator" class="divider"></li>
                        <?php foreach ($allTags as $tag_entry) {
                            if ($tag['Tag']['id'] != $tag_entry['Tag']['id']) {
                                echo '<li><a class="tagSelect" data-tagID="' . $tag_entry['Tag']['id'] . '" href="#">' . $tag_entry['Tag']['name'] . '</a></li>';
                            }
                        } ?>
                    </ul>
                </div>
                <input type="text" class="form-control tagNameValue" value="<?= $parent['Tag']['name'] ?>"
                       placeholder="" readonly>
                <input type="hidden" name="parentTagID" value="<?= $parent['Tag']['id'] ?>" class="tagIdValue">
            </div>
        </div>
        <input type="hidden" name="tag_id" value='<?= $tag['Tag']['id'] ?>'>
        <input type="hidden" name="task" value="<?= $mode ?>Tag">
        <div class="form-group col-md-12">
            <button type="submit" name="editFolder" class="btn btn-success center-block <?= $disable ?>"><?= $mode ?>
                Tag
            </button>
        </div>
    </form>
</div>