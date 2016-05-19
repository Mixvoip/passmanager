<?php
/**
 * @link          http://cakephp.org CakePHP(tm) Project
 * @package       app.View.Pages
 * @since         CakePHP(tm) v 0.10.0.1076
 */

//Variable pass-thru
$this->assign('title', $title);
$this->assign('administration', $administration);
$this->assign('invites', $invites);
?>
    <div id="mainTab" class="tab-pane active row">
        <!--Side nav-->
        <div class="col-md-2 col-sm-12" id="folderSelect">
            <div class="tabs-left">
                <ul class="nav nav-pills nav-stacked folderNav">
                    <li title="favorite">
                        <a id="favTabControl" href="#fav" data-toggle="tab"><span
                                class="glyphicon glyphicon-star"></span></a>
                    </li>
                    <li title="all" class="active">
                        <a href="#all" data-toggle="tab">All</a>
                    </li>
                    <?php
                    //create tab entry's
                    foreach ($data_all as $folder) {
                        $icon = $folder['Folder']['name'];
                        if (isset($folder['Folder']['image_id'])) {
                            $icon = "<img src=\"{$folder['Image']['server_path']}\" alt=\"{$folder['Folder']['name']}\">";
                        }
                        echo "<li title=\"{$folder['Folder']['name']}\"><a class=\"dotOverflow scrollToTop\" href=\"#folder_{$folder['Folder']['id']}\" data-toggle=\"tab\">$icon</a></li>";
                    } ?>
                    <li title="new">
                        <a href="#new" title="Add a new Folder" data-toggle="tooltip" class="addFolder">
                            <?php echo $this->Html->image('add_folder_blue.png', array('alt' => 'Add new folder')); ?>
                        </a>
                    </li>
                </ul>
            </div>
        </div>
        <div class="col-md-10 col-sm-11 tab-content" id="passwordRow">
            <div class="tab-pane" id="fav">
                <h3><!--<span class="glyphicon glyphicon-star" style="color: yellow"></span> -->My Favorites</h3>
                <?php
                if (count($data_fav) > 0) {
                    echo $this->element('password_entry', array('entrys' => $data_fav, 'fav' => true));
                } else {
                    echo '<p class="alert alert-info">You have no Favorites. Use the star to select some.</p>';
                }
                ?>
            </div>
            <div class="tab-pane active" id="all">
                <?php
                //Create all entrys
                foreach ($data_all as $folder) {
                    $url = "img/defaultFolder.png";
                    if (isset($folder['Image']['server_path'])) {
                        $url = $folder['Image']['server_path'];
                    }
                    echo $this->element(Configure::read('Style.folder'), array('folder' => $folder, 'img_url' => $url));
                }
                ?>
            </div>
            <?php
            //Create view for tab entry's
            foreach ($data_all as $folder) {
                echo "<div class=\"tab-pane\" id=\"folder_{$folder['Folder']['id']}\">";
                $url = "img/defaultFolder.png";
                if (isset($folder['Image']['server_path'])) {
                    $url = $folder['Image']['server_path'];
                }
                echo $this->element(Configure::read('Style.folder'), array('folder' => $folder, 'img_url' => $url));
                echo "</div>";
            } ?>
        </div>
    </div>
<?= $this->element('popups', array('mainView' => true)) ?>
