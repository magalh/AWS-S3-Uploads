<?php
if( !defined('CMS_VERSION') ) exit;
use AWSS3\utils;

try {
	if( !isset($params['key']) ) {
		throw new \AWSS3\Exception('Key is required');
	}

	$s3Client = utils::getS3Client();

	$bucket = $this->GetOptionValue("bucket_name");
	$file = utils::presignedUrl($bucket,$params['key'],$s3Client);
	//echo $file;
	header("Location: ".$file);
	die();
	
} catch (\AWSS3\Exception $e) {
	AWSS3\helpers::_DisplayAdminMessage($e->GetOptions(),$e->GetType());
}

?>
