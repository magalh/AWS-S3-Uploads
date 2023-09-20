<?php
if( !defined('CMS_VERSION') ) exit;
if( !$this->CheckPermission(AWS_S3_Uploads::MANAGE_PERM) ) return;

use AWS_S3_Uploads\utils;

$utils = new utils();
$ready = 0;

if($utils->is_aws_ready()){
	$ready = 1;
	$buckets = $utils->list_my_buckets();
}

echo $this->StartTabHeaders();
	//echo $this->SetTabHeader('logs', "Logs");
	echo $this->SetTabHeader('settings',"Settings");

	if($ready){
		foreach ($buckets as $bucket) {
            echo $this->SetTabHeader(strtolower($bucket['Name']),strtoupper($bucket['Name']));
        }
	}
	

echo $this->EndTabHeaders();

echo $this->StartTabContent();

	echo $this->StartTab('settings');
	include(__DIR__.'/function.admin_settings_tab.php');
	echo $this->EndTab();

	if($ready){
		foreach ($buckets as $bucket) {
			echo $this->StartTab(strtolower($bucket['Name']));
			$bucket_id = $bucket['Name'];
			include(dirname(__FILE__)."/action.admin_fileview.php");
			echo $this->EndTab();
        }
	}

echo $this->EndTabContent();

$this->SetCurrentTab("pixelsolution");

?>