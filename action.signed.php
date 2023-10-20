<?php
if( !defined('CMS_VERSION') ) exit;
use AWSS3\aws_s3_utils;

$mod_fm = \cms_utils::get_module('FileManager');

try {
	if( !isset($params['key']) ) {
		throw new \AWSS3\Exception('Key is required');
	}

	$bucket = $this->GetOptionValue("bucket_name");
	$key = $mod_fm->decodefilename($params['key']);
	$file = aws_s3_utils::presignedUrl($bucket,$key);
	//echo $file;
	header("Location: ".$file);
	die();
	
} catch (\AWSS3\Exception $e) {
	//$this->_DisplayAdminMessage($e->GetOptions(),$e->GetType());
	aws_s3_utils::get_sdk()->_DisplayMessage($e->getText());
}
?>
