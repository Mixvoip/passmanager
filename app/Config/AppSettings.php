<?php
/**
 * App Settings
 */

/**
 * Master Root Tag
 * The tag with the id blow can't be deleted from the App and should't be removed in the DB either
 */
Configure::write('RootTag', 1);

/**
 * Fixed first folder
 */
Configure::write('FirstFolderID', 2);

/**
 * Recovery Configuration (Danger when turning on with existing database)
 */
Configure::write('Recovery.Enable', false);
Configure::write('Recovery.Key', null);

/**
 * Minimum Password length
 */
Configure::write('MinPasswordLength', 8);

/**
 * Invite valid duration in sec
 * If null invites never expires
 */
Configure::write('InviteTimeOut', null);

/**
 * Max access_level for Administration Tab
 * Counting the AccessLevel Down (UserAccessLevel <= AccessLevel.Administration == true && UserAccessLevel > 0)
 */
Configure::write('AccessLevel.Administration', 1);

/**
 * Enable/Disable Strict Tagging
 * When Strict Tagging is enabled you can only assign and see Tags to Password in which you are subscribed
 */
Configure::write('StrictTagging.enable', true);

//################################################# UI Config ####################################################
/**
 * Select Folder Style (Accepted values: folder_small folder_big)
 */
Configure::write('Style.folder', 'folder_small');

/**
 * Set the folder search prefix
 */
Configure::write('UI.FolderSearchPrefix', '::');

/**
 * QuickAccess Configuration
 */
//Enable QuickAccess: Accepted Values: true , false
Configure::write('QuickAccess.Enable', true);

//QuickAccess Title: Accepted Values: any string
Configure::write('QuickAccess.Title', "QuickAccess ");

/* Links
	 *Format : "link" => "name"
	 *Use "---" to create a separator
	 */
$links = array(
    "http://mail.mbox.lu" => "Mail Box",
    "http://mixvoip.slack.com" => "Slack",
    "---",
    "https://internal.mixvoip.com:12663" => "Internal Wiki",
    "---",
    "http://www.mixvoip.com" => "Homepage"
);

//Add debug-entrys to the QuickAccess
if (Configure::read('debug') > 0) {
    $links[] = "---";
    $links['/Password_Manager/debug'] = "Debug Page";
}
Configure::write('QuickAccess.Menu', $links);
