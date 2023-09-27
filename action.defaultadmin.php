<?php
if( !defined('CMS_VERSION') ) exit;
if( !$this->CheckPermission(AWSS3::MANAGE_PERM) ) return;

use AWSS3\utils;
$utils = new utils();

$ready = 0;
$mod_fm = \cms_utils::get_module('FileManager');
if (isset($params["fmmessage"]) && $params["fmmessage"]!="") {

    $count="";
    if (isset($params["fmmessagecount"]) && $params["fmmessagecount"]!="") $count=$params["fmmessagecount"];
    //echo $this->ShowMessage($params["fmmessage"]);
	//echo $this->ShowErrors($params["fmmessage"]);
}

if($utils->is_aws_ready()){
	$ready = 1;
	$bucket_id = $this->GetPreference('bucket_name');
}

echo $this->StartTabHeaders();
	
	if($ready){
		echo $this->SetTabHeader($bucket_id,$bucket_id);
		echo $this->SetTabHeader('permissions',"Permissions");
/*		//dynamic buckets
		foreach ($buckets as $bucket) {
			echo $this->SetTabHeader(strtolower($bucket['Name']),strtoupper($bucket['Name']));
		}*/
		
	}

echo $this->SetTabHeader('settings',"Settings");
echo $this->EndTabHeaders();

echo $this->StartTabContent();

	if($ready){

		echo $this->StartTab($bucket_id);
		include(dirname(__FILE__)."/uploadview.php");
		include(dirname(__FILE__)."/action.admin_fileview.php");
		echo $this->EndTab();

/*		//dynamic buckets
		foreach ($buckets as $bucket) {
			echo $this->StartTab(strtolower($bucket['Name']));
			$bucket_id = $bucket['Name'];
			include(dirname(__FILE__)."/action.admin_fileview.php");
			echo $this->EndTab();
        }*/

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