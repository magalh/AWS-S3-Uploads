<?php
if( !defined('CMS_VERSION') ) exit;
if( !$this->CheckPermission(AWSS3::MANAGE_PERM) ) return;

use \AWSSDK\aws_sdk_utils;
use \AWSS3\aws_s3_utils;
use \AWSS3\helpers;

$sdk_utils = new aws_sdk_utils();
$s3_utils = new aws_s3_utils();

$ready = 0;
$mod_fm = \cms_utils::get_module('FileManager');

if(!$sdk_utils->is_valid()){
    $error = 1;
    $link = $sdk_utils::get_mod()->create_url('m1_', 'defaultadmin', $returnid, array());
    $message = $sdk_utils::get_mod()->_DisplayMessage($this->Lang('error_setup',$link),"alert-danger",true);
} else if($s3_utils->validate()){
	$ready = 1;
	$bucket_id = $this->GetOptionValue('bucket_name');
}

echo $this->StartTabHeaders();
	if($ready == 1){
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