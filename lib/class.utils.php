<?php
#---------------------------------------------------------------------------------------------------
# Module: AWSS3
# Authors: Magal Hezi, with CMS Made Simple Foundation.
# Copyright: (C) 2023 Magal Hezi, h_magal@hotmail.com
# Licence: GNU General Public License version 3. See http://www.gnu.org/licenses/  
#---------------------------------------------------------------------------------------------------
# CMS Made Simple(TM) is (c) CMS Made Simple Foundation 2004-2020 (info@cmsmadesimple.org)
# Project's homepage is: http://www.cmsmadesimple.org
# Module's homepage is: http://dev.cmsmadesimple.org/projects/AWSS3
#---------------------------------------------------------------------------------------------------
# This program is free software; you can redistribute it and/or modify it under the terms of the GNU
# General Public License as published by the Free Software Foundation; either version 3 of the 
# License, or (at your option) any later version.
#
# However, as a special exception to the GPL, this software is distributed
# as an addon module to CMS Made Simple.  You may not use this software
# in any Non GPL version of CMS Made simple, or in any version of CMS
# Made simple that does not indicate clearly and obviously in its admin
# section that the site was built with CMS Made simple.
#
# This program is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; 
# without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  
# See the GNU General Public License for more details.
#---------------------------------------------------------------------------------------------------
#
# Amazon Web Services, AWS, and the Powered by AWS logo are trademarks of Amazon.com, Inc. or its affiliates
#---------------------------------------------------------------------------------------------------

namespace AWSS3;

use Aws\Sts\StsClient;
use \Aws\Credentials\Credentials;
use \Aws\S3\S3Client;  
use \Aws\Exception\AwsException;
use \Aws\Exception\UnresolvedEndpointException;
use \AWSSDK\Exception;
use \AWSS3\helpers;

final class utils
{

    private $s3Client = null;
    private static $__mod;
    private static $__sdk;

    public static $property = "hello world";

    public function __construct (){

        $this->awssdk = self::get_sdk();
        $this->mod = self::get_mod();
        $this->s3Client = null;
        $this->bucket_name = $this->mod->GetPreference('bucket_name');
        $this->access_region = $this->mod->GetPreference('access_region');
        $this->use_custom_url = $this->mod->GetPreference('use_custom_url');
        $this->custom_url = $this->mod->GetPreference('custom_url');
        $this->allowed = $this->mod->GetPreference('allowed');
        $this->errors = array();

    }

    private static function lang()
    {
        if( !self::$__mod ) self::$__mod = self::get_mod();
        $args = func_get_args();
        return call_user_func_array(array(self::$__mod,'Lang'),$args);
    }

    public function validate() {

        $smarty = cmsms()->GetSmarty();
        $settings = $this->mod->GetSettingsValues();
        if(empty($settings)) return false;

        try {
            $data = array('access_key','access_secret_key','bucket_name','access_region');

            foreach ($data as $key)
            {
                $item = $this->mod->GetPreference($key);
                if($item=="" or $item==null)
                {
                    $this->errors[] = $this->lang($key). " ".$this->lang("required") ;
                }
            }

            if(!empty($this->errors)){
                if (count($this->errors) == 4) {
                    return false;
                }
                //print_r($this->errors);
                throw new \AWSSDK\Exception($this->errors,"slide-danger");
            }

            $s3 = self::getS3Client();
            if (is_array($s3) && !$s3['conn']){
                $this->errors[] = $s3['message'];
                throw new \AWSSDK\Exception($this->errors,"slide-danger");
            }
            $this->s3Client = $s3;

            if(!$this->s3Client->doesBucketExist($this->mod->GetPreference('bucket_name'))){
                $this->errors[] = $this->mod->GetPreference('bucket_name')." does not exist";
                throw new \AWSSDK\Exception($this->errors,"slide-danger");
            }

            $smarty->assign("bucket_name",$this->mod->GetPreference('bucket_name'));
            $smarty->assign("bucket_url","https://".$this->mod->GetPreference('bucket_name').".s3.".$this->mod->GetPreference('access_region').".amazonaws.com/");
            
            //helpers::_DisplayAdminMessage(null,array(self::lang('msg_vrfy_integrityverified')));

        }
        catch (\AWSSDK\Exception $e) {
            $this->awssdk->_DisplayMessage($e->getText(),$e->getType());
            return false;
        }

        $message = $this->awssdk->_DisplayMessage($this->lang('msg_vrfy_integrityverified'),"success",1);
        $smarty->assign("message",$message);
        return true;
	}
    

    public static function getS3Client(){

        $result = array();
        $result['conn'] = true;

        try {

            $s3Client = new S3Client([
                'credentials' => self::get_credentials(),
                'region' => self::get_mod()->GetPreference('access_region'),
                'version' => 'latest'
                ]);

            $buckets = $s3Client->listBuckets();
            
        } catch (AwsException $e) {
            $result['conn'] = false;
            $result['message'] = $e->getAwsErrorMessage();
            return $result;
        }

        return $s3Client;
    }

    public function upload_file($file_name,$file_temp_src){

        $this->s3Client = $this::getS3Client();
        $ret = array();
        try { 
            $result = $this->s3Client->putObject([ 
                'Bucket' => $this->bucket_name, 
                'Key'    => $file_name,
                'SourceFile' => $file_temp_src,
                'Tagging' => "Module=CMSMS::AWSS3"
            ]); 
            //$result_arr = $result->toArray(); 

            $ret[0] = true;
            $ret[2] = $file_name;  

        } catch (AwsException $e) { 
            $ret[0] = false;
            $ret[1] = $e->getAwsErrorMessage();
            //echo $e->getCode() . " " .$e->getMessage();
        } 

        return $ret;
    }

    public function list_my_buckets(){
        $buckets = $this->s3Client->listBuckets();
        return $buckets['Buckets'];
    }

    public function bucketsExists($bucket_id){
       if($this->s3Client->doesBucketExist($bucket_id)){
        echo "bucket exists";
       } else {
        echo "bucket does not exist";
       }

    }

    public function removePath($link,$path){
        $newphrase = str_replace($path, "", $link);
        return $newphrase;
     }

    
     public function list_my_files(){
        $buckets = $this->s3Client->listBuckets();
        return $buckets['Buckets'];
    }

    public function get_file($prefix){

        $mod = \cms_utils::get_module('AWSS3');
        $this->s3Client = self::getS3Client();

        try {
            $file = $this->s3Client->getObject([
                'Bucket' => $mod->GetOptionValue("bucket_name"),
                'Key' => $prefix,
            ]);
            $body = $file->get('Body');
            $body->rewind();
            
        } catch (AwsException $e) {
            $errors[] = "Failed to download $file_name from $bucket_name with error: " . $e->getAwsErrorMessage();
            $errors[] = "Please fix error with file downloading before continuing.";
        
            $mod->DisplayErrorMessage($errors);

        }

    }

    public static function get_file_info($prefix){

        $mod = \cms_utils::get_module('AWSS3');
        $s3Client = self::getS3Client();

        $bucket_id = $mod->GetOptionValue("bucket_name");
        try {
            $file = $s3Client->getObject([
                'Bucket' => $bucket_id,
                'Key' => $prefix,
            ]);

            $head = $s3Client->headObject([
                'Bucket' => $bucket_id,
                'Key' => $prefix
            ]);

            $onerow = new \stdClass();
            $onerow->name = $prefix;
            $onerow->bucket = $bucket_id;
            $onerow->mime = $head['ContentType'];
            $onerow->size=self::formatBytes($file['ContentLength']);
            $onerow->postdate=strtotime( $file['LastModified'] );
            $onerow->url_original = self::getUrl($prefix);
            $onerow->url = $mod->GetPrettyUrl($prefix);
            $onerow->url_presigned = self::presignedUrl($prefix);
            $onerow->url = str_replace('\\','/',$onerow->url); // windoze both sucks, and blows.
            if(self::is_file_image($onerow->name)) $onerow->isimage = true;

            return $onerow;

            
        } catch (AwsException $e) {
            $errors[] = "Failed to download $file_name from $bucket_name with error: " . $e->getAwsErrorMessage();
            $errors[] = "Please fix error with file downloading before continuing.";
        
            $mod->DisplayErrorMessage($errors);

        }

    }

    public static function deleteObject($keyname){

        $instance = new self();
        $s3 = self::getS3Client();
        try
            {
                $result = $s3->deleteObject([
                    'Bucket' => $instance->bucket_name,
                    'Key'    => $keyname
                ]);
            }
            catch (AwsException $e) {
                echo "Error deleting object: " . $e->getAwsErrorMessage() . "\n";
            }

    }

    public static function deleteObjectDir($dir){
        $instance = new self();
        $s3 = self::getS3Client();
    
        $keys = $s3->listObjects([
            'Bucket' => $instance->bucket_name,
            'Prefix' => $dir
        ]);

        // 3. Delete the objects.
        foreach ($keys['Contents'] as $key)
        {
            $s3->deleteObjects([
                'Bucket'  => $instance->bucket_name,
                'Delete' => [
                    'Objects' => [
                        [
                            'Key' => $key['Key']
                        ]
                    ]
                ]
            ]);
        }
    
        return true;
    
    }
    
    public static function get_mod(){
        if( !self::$__mod ) self::$__mod = \cms_utils::get_module('AWSS3');
        return self::$__mod;
    }

    public static function get_sdk(){
        if( !self::$__sdk ) self::$__sdk = \cms_utils::get_module('AWSSDK');
        return self::$__sdk;
    }

    private static function get_credentials(){

        $mod = self::get_mod();
        $stsClient = new StsClient([
            'version' => 'latest',
            'region' => $mod->GetPreference('access_region'),
            'credentials' => [
                'key' => $mod->GetPreference('access_key'),
                'secret' => $mod->GetPreference('access_secret_key'),
            ],
        ]);

        $result = $stsClient->getSessionToken();
        $credentials = $stsClient->createCredentials($result);

        return $credentials;
    }

    public static function getUrl($key){
        $mod = self::get_mod();
        $bucket_id = $mod->GetOptionValue("bucket_name");

        $s3Client = self::getS3Client();
        $url = $s3Client->getObjectUrl($bucket_id, $key);
       
        return $url;

    }

    public static function presignedUrl($key){

        $mod = self::get_mod();
        $bucket_id = $mod->GetOptionValue("bucket_name");
        $use_custom_url = $mod->GetOptionValue("use_custom_url");
        $custom_url = $mod->GetOptionValue("custom_url");

        try {
            $s3Client = self::getS3Client();
            $cmd = $s3Client->getCommand('GetObject', [
                'Bucket' => $bucket_id,
                'Key' => $key
            ]);

            $request = $s3Client->createPresignedRequest($cmd, '+1 hour');
            $presignedUrl = (string)$request->getUri();

            if( $use_custom_url && $custom_url ) {
                $urlParts = parse_url($presignedUrl);
                if ($urlParts === false) {
                    echo "Error parsing the URL";
                } else {
                    $urlParts['scheme'] = parse_url($custom_url, PHP_URL_SCHEME);
                    $urlParts['host'] = parse_url($custom_url, PHP_URL_HOST);
                    $newUrl = $urlParts['scheme'] . '://' . $urlParts['host'];
                    if (isset($urlParts['path'])) {
                        $newUrl .= $urlParts['path'];
                    }
                    if (isset($urlParts['query'])) {
                        $newUrl .= '?'.$urlParts['query'];
                    }
                    $presignedUrl = $newUrl;
                }
            }

            return $presignedUrl;

        } catch (\AwsException $e) {
            // Handle the error
            $me->setError(array(
                'error' => "Failed to connect to AWS server",
                'errno' => $e->getAwsErrorCode(),
                'errstr' => $e->getAwsErrorMessage()
            ));

        }
       
    }

    public static function formatBytes($bytes) {
        if ($bytes > 0) {
            $i = floor(log($bytes) / log(1024));
            $sizes = array('B', 'KB', 'MB', 'GB', 'TB', 'PB', 'EB', 'ZB', 'YB');
            return sprintf('%.02F', round($bytes / pow(1024, $i),1)) * 1 . ' ' . @$sizes[$i];
        } else {
            return 0;
        }
    }

    public static function sumBytes($items) {
        $sum = 0;
        foreach ($items as $item) {
            if (isset($item->size)) {
                $sum += $item->size;
            }
        }
    
        return self::formatBytes($sum);
    }

    public static function is_file_acceptable( $file_type )
    {
        $mod = self::get_mod();
        $file_type = strtolower($file_type);
        $allowTypes = explode(',',$mod->GetPreference('allowed'));
        if(!in_array($file_type, $allowTypes)) return FALSE;
        return TRUE;
    }

    public static function is_file_image( $src_file_name )
    {
        $supported_image = array(
            'gif',
            'jpg',
            'jpeg',
            'png'
        );
        
        $ext = strtolower(pathinfo($src_file_name, PATHINFO_EXTENSION));
        if (!in_array($ext, $supported_image)) {
            return false;
        }

        return true;
    }

  public function check_iam_policy() {
        $smarty = cmsms()->GetSmarty();
        $error = null;
        try {
            $resp = $this->s3Client->getBucketPolicy([
                'Bucket' => $this->bucket_name
            ]);
            $message = "Succeed in receiving bucket policy:";

        } catch (AwsException $e) {
            if($e->getStatusCode() == 404){
                $error = 1;
                $message = $this->bucket_name." ".$e->getAwsErrorMessage();
                //$this->put_iam_policy();
            }
        }

        $smarty->assign('message',$message);
        $smarty->assign('error',$error);
        

    }

    

    private function put_iam_policy() {

        $policy = $this->get_iam_policy();
        $smarty = cmsms()->GetSmarty();

        try {
            $resp = $this->s3Client->putBucketPolicy([
                'Bucket' => $this->bucket_name,
                'Policy' => $policy,
            ]);
            echo "Succeed in put a policy on bucket: " . $this->bucket_name . "\n<br>";
        } catch (AwsException $e) {
            // Display error message
            $smarty->assign('message',$this->bucket_name." ".$e->getAwsErrorMessage());

        }

    }

    public static function get_policy(){
        $mod = self::get_mod();
        $s3Client = self::getS3Client();
        $bucket = $mod->GetOptionValue("bucket_name");

        try {
            $resp = $s3Client->getBucketAcl([
                'Bucket' => $bucket
            ]);
            //print_r($resp);
        } catch (AwsException $e) {
            // output error message if fails
            echo $e->getMessage();
            echo "\n";
        }

    }

    public static function getInstance()
    {
        static $instance = null;
        if (!$instance) {
            $instance = new self();
        }

        return $instance;
    }
    

    public static function get_iam_policy() : string {
        $mod = self::get_mod();
        $bucket_id = $mod->GetOptionValue("bucket_name");
		$bucket = strtok( $bucket_id, '/' );

		$path = null;

		if ( strpos( $bucket_id, '/' ) ) {
			$path = str_replace( strtok( $bucket_id, '/' ) . '/', '', $bucket_id );
		}

		return '{
            "Version": "2012-10-17",
            "Statement": [
                {
                    "Sid": "BasicAccess",
                    "Principal": "*",
                    "Effect": "Allow",
                    "Action": [
                        "s3:AbortMultipartUpload",
                        "s3:DeleteObject",
                        "s3:GetObject",
                        "s3:GetObjectAcl",
                        "s3:PutObject",
                        "s3:PutObjectAcl"
                    ],
                    "Resource": [
                        "arn:aws:s3:::'.$bucket_id.'/*"
                    ]
                },
                {
                    "Sid": "AllowRootAndHomeListingOfBucket",
                    "Principal": "*",
                    "Effect": "Allow",
                    "Action": [
                        "s3:GetBucketAcl",
                        "s3:GetBucketLocation",
                        "s3:GetBucketPolicy",
                        "s3:ListBucket"
                    ],
                    "Resource": ["arn:aws:s3:::' . $bucket_id . '"]
                }
            ]
        }';

        
	}

    public static function sort_by_key($array, $key) {
        usort($array, function($a, $b) use ($key) {
            return $a[$key] - $b[$key];
        });
        return $array;
    }

    public static function sortByProperty($array, $property) {
        usort($array, function($a, $b) use ($property) {
            return strcmp($a->$property, $b->$property);
        });
        return $array;
    }

    public static function moveItemsToBeginningByIndex(&$array, $indicesToMove) {
        $itemsToMove = array();
    
        // Extract the items to move based on the provided indices
        foreach ($indicesToMove as $index) {
            if (isset($array[$index])) {
                $itemsToMove[] = $array[$index];
                unset($array[$index]);
            }
        }
    
        // Merge the items to the beginning of the array
        $array = array_merge($itemsToMove, $array);
    }

    public static function getUpDir($myArray){

        $combinedString = '';

        for ($i = 0; $i < count($myArray) - 1; $i++) {
            $combinedString .= $myArray[$i]->name;
            $combinedString .= '/'; // Add a separator (e.g., comma and space)
        }

        return $combinedString;

    }
  

    protected function catchWarning($errno, $errstr, $errfile, $errline)
    {
        $this->setError(array(
            'error' => "Connecting to the AWS server raised a PHP warning: ",
            'errno' => $errno,
            'errstr' => $errstr,
            'errfile' => $errfile,
            'errline' => $errline
        ));
    }

    protected function setError($error,$debug=1)
    {
        $this->errors[] = $error;
        if ($debug >= 1) {
            echo '<pre>';
            foreach ($this->errors as $error) {
                print_r($error);
            }
            echo '</pre>';
        }
    }

    static final public function loader($params, $smarty)
    {
        if(empty($params["format"])) {
            $format = "%b %e, %Y";
        } else {
            $format = $params["format"];
        }
        return strftime($format,time());
    }


}
?>