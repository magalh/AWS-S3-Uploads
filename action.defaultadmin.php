<?php
if( !defined('CMS_VERSION') ) exit;
if( !$this->CheckPermission(AWSS3::MANAGE_PERM) ) return;

$utils = new \AWSS3\utils;
$helpers = new \AWSS3\helpers;

$ready = 0;
$mod_fm = \cms_utils::get_module('FileManager');

if($utils->validate()){
	$ready = 1;
	$bucket_id = $this->GetOptionValue('bucket_name');
}

echo $this->StartTabHeaders();
	if($ready){
		echo $this->SetTabHeader($bucket_id,$bucket_id);
		echo $this->SetTabHeader('permissions',"Permissions");
	}

	echo $this->SetTabHeader('settings',"Settings");
echo $this->EndTabHeaders();

echo $this->StartTabContent();

	if($ready == 1){

		echo $this->StartTab($bucket_id);
		include(dirname(__FILE__)."/uploadview.php");
		include(dirname(__FILE__)."/action.admin_fileview.php");
		echo $this->EndTab();

		echo $this->StartTab('permissions');
		include(__DIR__.'/function.admin_permissions_tab.php');
		echo $this->EndTab();
	}

	echo $this->StartTab('settings');
		include(__DIR__.'/function.admin_settings_tab.php');
	echo $this->EndTab();
	

echo $this->EndTabContent();

$this->SetCurrentTab("pixelsolution");

?>