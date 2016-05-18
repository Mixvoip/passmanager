<div class="row folderHead">
    <div class="col-md-2 hidden-sm">
        <img class="folderIcon" src="<?= $img_url ?>" alt="placeholder">
    </div>
    <div class="col-md-8 col-sm-11">
        <h4>
            <?php
            if ($folder['Folder']['user_id'] == $_SESSION['user_id']) echo '<span data-toggle="tooltip" title="You are the Owner">*</span> ';
            echo str_replace('\n', '<br>', htmlspecialchars($folder['Folder']['name']));
            ?>
        </h4>
        <p><?= str_replace('\n', '<br>', htmlspecialchars($folder['Folder']['description'])) ?></p>
        <p data-folderID="<?= $folder['Folder']['id'] ?>">
            <button class="btn btn-sm btn-default addPassword" data-toggle="tooltip" title="Add new Password">
                <span class="glyphicon glyphicon-plus-sign"></span>
            </button>
            <?php //if (count($folder['Password']) == 0) { ?>
                <button class="btn btn-sm btn-default deleteFolder" data-toggle="tooltip" title="Delete Folder">
                    <span class="glyphicon glyphicon-minus-sign"></span>
                </button>
            <?php //} ?>
            <button class="btn btn-sm btn-default editFolder" data-toggle="tooltip" title="Edit Folder">
                <span class="glyphicon glyphicon-pencil"></span>
            </button>
            <?php if (isset($folder['Folder']['shared']) && $folder['Folder']['shared'] == 1) { ?>
                <button class="btn btn-sm btn-default shareFolder" data-toggle="tooltip" title="Share Options">
                    <span class="glyphicon glyphicon-share"></span>
                </button>
            <?php } ?>
        </p>
    </div>
    <div class="col-md-1 col-sm-1 right closeFolder" data-toggle="tooltip" title="Toggle Folder view">
        <span class="glyphicon glyphicon-chevron-down hide"></span>
        <span class="glyphicon glyphicon-chevron-up"></span>
    </div>
</div>
<?php echo $this->element('password_entry', array('entrys' => $folder['Password'], 'shared' => $folder['Folder']['shared'])); ?>
