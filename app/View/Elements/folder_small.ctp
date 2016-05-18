<div class="row folderHead">
    <table class="table">
        <thead class="folderHeader">
        <tr>
            <th class="col-md-2">Name</th>
            <th class="col-md-8">Description</th>
            <th class="col-md-2">Actions</th>
        </tr>
        </thead>
        <tbody>
        <tr>
            <td class="col-md-2"><?= str_replace('\n', '<br>', htmlspecialchars($folder['Folder']['name'])) ?></td>
            <td class="col-md-8"><?= str_replace('\n', '<br>', htmlspecialchars($folder['Folder']['description'])); ?></td>
            <td class="col-md-2" data-folderID="<?= $folder['Folder']['id'] ?>">
                <button class="btn btn-sm btn-default addPassword" data-toggle="tooltip"
                        title="Add new Password">
                    <span class="glyphicon glyphicon-plus-sign"></span>
                </button>
                <?php //if (count($folder['Password']) == 0) { ?>
                <button class="btn btn-sm btn-default deleteFolder" data-toggle="tooltip"
                        title="Delete Folder">
                    <span class="glyphicon glyphicon-minus-sign"></span>
                </button>
                <?php //} ?>
                <button class="btn btn-sm btn-default editFolder" data-toggle="tooltip"
                        title="Edit Folder">
                    <span class="glyphicon glyphicon-pencil"></span>
                </button>
                <?php if (isset($folder['Folder']['shared']) && $folder['Folder']['shared'] == 1) { ?>
                    <button class="btn btn-sm btn-default shareFolder" data-toggle="tooltip"
                            title="Share Options">
                        <span class="glyphicon glyphicon-share"></span>
                    </button>
                <?php } ?>
                <div class="closeFolder inline" data-toggle="tooltip" title="Toggle Folder view">
                    <span class="glyphicon glyphicon-chevron-down hide"></span>
                    <span class="glyphicon glyphicon-chevron-up"></span>
                </div>
            </td>
        </tr>
        </tbody>
    </table>
</div>
<?php echo $this->element('password_entry', array('entrys' => $folder['Password'], 'shared' => $folder['Folder']['shared'])); ?>