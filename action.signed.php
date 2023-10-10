<?php
if( !defined('CMS_VERSION') ) exit;
use AWSS3\utils;

try {
	if( !isset($params['key']) ) {
		throw new \AWSS3\Exception('Key is required');
	}

	$bucket = $this->GetOptionValue("bucket_name");
	$key = $this->decodefilename($params['key']);

	$file = utils::presignedUrl($bucket,$key);
	//echo $file;
	header("Location: ".$file);
	die();
	
} catch (\AWSS3\Exception $e) {
	$this->_DisplayAdminMessage($e->GetOptions(),$e->GetType());
}

?>
