<?php
if( !defined('CMS_VERSION') ) exit;
if( !$this->CheckPermission(AWS_S3_Uploads::MANAGE_PERM) ) return;

use AWS_S3_Uploads\utils;

$utils = new utils();
$ready = 0;
$mod_fm = \cms_utils::get_module('FileManager');
if (isset($params["fmmessage"]) && $params["fmmessage"]!="") {
    // gotta get rid of this stuff.
    $count="";
    if (isset($params["fmmessagecount"]) && $params["fmmessagecount"]!="") $count=$params["fmmessagecount"];
    echo $this->ShowMessage($params["fmmessage"]);
}

if($utils->is_aws_ready()){
	$ready = 1;
	$bucket_id = $this->GetPreference('s3_bucket_name');
}

echo $this->StartTabHeaders();
	
	if($ready){
		echo $this->SetTabHeader($bucket_id,$bucket_id);
		
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
		//include(dirname(__FILE__)."/uploadview.php");
		//include(dirname(__FILE__)."/action.admin_fileview.php");
		echo $this->EndTab();

/*		//dynamic buckets
		foreach ($buckets as $bucket) {
			echo $this->StartTab(strtolower($bucket['Name']));
			$bucket_id = $bucket['Name'];
			include(dirname(__FILE__)."/action.admin_fileview.php");
			echo $this->EndTab();
        }*/
	}

	echo $this->StartTab('settings');
		include(__DIR__.'/function.admin_settings_tab.php');
	echo $this->EndTab();

echo $this->EndTabContent();

$this->SetCurrentTab("pixelsolution");

?>