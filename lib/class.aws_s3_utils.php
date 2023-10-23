<?php
namespace AWSS3;

use \Aws\S3\S3Client;  
use \Aws\Exception\AwsException;
use \Aws\Exception\UnresolvedEndpointException;
use \AWSS3\helpers;

final class aws_s3_utils
{

    private $s3Client = null;
    private static $__mod;
    private static $__sdk;

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
        //print_r($settings);
        if(empty($settings)) return false;

        try {
            $data = array('bucket_name','access_region');

            foreach ($data as $key)
            {
                $item = $this->mod->GetPreference($key);
                if($item=="" or $item==null)
                {
                    $this->errors[] = $this->lang($key). " ".$this->lang("required") ;
                }
            }
            
            if(!empty($this->errors)){
                throw new \AWSSDK\Exception($this->errors,"slide-danger");
            }

            $s3 = self::getS3Client();
            if (is_array($s3) && !$s3['conn']){
                $this->errors[] = $s3['message'];
                throw new \AWSS3\Exception($this->errors,"slide-danger");
            }
            $this->s3Client = $s3;

            if(!$this->s3Client->doesBucketExist($this->mod->GetPreference('bucket_name'))){
                $this->errors[] = $this->mod->GetPreference('bucket_name')." does not exist";
                throw new \AWSS3\Exception($this->errors,"slide-danger");
            }

            $smarty->assign("bucket_name",$this->mod->GetPreference('bucket_name'));
            $smarty->assign("bucket_url","https://".$this->mod->GetPreference('bucket_name').".s3.".$this->mod->GetPreference('access_region').".amazonaws.com/");
            
            //helpers::_DisplayAdminMessage(null,array(self::lang('msg_vrfy_integrityverified')));

        }
        catch (\AWSS3\Exception $e) {
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

    public function upload_file($bucket_id,$file_name,$file_temp_src){

        $this->s3Client = $this::getS3Client();
        $ret = array();
        try { 
            $result = $this->s3Client->putObject([ 
                'Bucket' => $bucket_id, 
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
            echo "Downloaded the file and it begins with: {$body->read(26)}.\n";
            
        } catch (AwsException $e) {
            $errors[] = "Failed to download $file_name from $bucket_name with error: " . $e->getAwsErrorMessage();
            $errors[] = "Please fix error with file downloading before continuing.";
        
            $mod->DisplayErrorMessage($errors);

        }

    }

    public static function get_file_info($prefix){

        echo $prefix;
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

            $objectData = $result['Body']->getContents();
            $body = $file->get('Body');
            $body->rewind();
            echo "Downloaded the file and it begins with: {$body->read(26)}.\n";

            print_r($objectData);
            
        } catch (AwsException $e) {
            $errors[] = "Failed to download $file_name from $bucket_name with error: " . $e->getAwsErrorMessage();
            $errors[] = "Please fix error with file downloading before continuing.";
        
            $mod->DisplayErrorMessage($errors);

        }

    }

    

    public static function deleteObject($bucket,$keyname){
        $s3 = self::getS3Client();
        try
            {
                echo 'Attempting to delete ' . $keyname . '...' . PHP_EOL;

                $result = $s3->deleteObject([
                    'Bucket' => $bucket,
                    'Key'    => $keyname
                ]);

                if ($result['DeleteMarker'])
                {
                    echo $keyname . ' was deleted or does not exist.' . PHP_EOL;
                } else {
                    exit('Error: ' . $keyname . ' was not deleted.' . PHP_EOL);
                }
            }
            catch (AwsException $e) {
                exit('Error: ' . $e->getAwsErrorMessage() . PHP_EOL);
            }

    }

    public static function deleteObjectDir($bucket_id,$dir){
        $s3 = self::getS3Client();
    
        $keys = $s3->listObjects([
            'Bucket' => $bucket_id,
            'Prefix' => $dir
        ]);

        // 3. Delete the objects.
        foreach ($keys['Contents'] as $key)
        {
            $s3->deleteObjects([
                'Bucket'  => $bucket_id,
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
        $credentials = self::get_sdk()->getCredentials();
        return $credentials;
    }

    public static function presignedUrl($bucket_id,$key){

        $me = self::getInstance();
        set_error_handler(array($me, 'catchWarning'));

        try {
            $s3Client = self::getS3Client();
            $cmd = $s3Client->getCommand('GetObject', [
                'Bucket' => $bucket_id,
                'Key' => $key
            ]);

            $request = $s3Client->createPresignedRequest($cmd, '+20 minutes');
            $presignedUrl = (string)$request->getUri();

            return $presignedUrl;

        } catch (\AwsException $e) {
            // Handle the error
            $me->setError(array(
                'error' => "Failed to connect to AWS server",
                'errno' => $e->getAwsErrorCode(),
                'errstr' => $e->getAwsErrorMessage()
            ));

        }

        restore_error_handler();

       
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

    public static function get_file_list($bucket_id,$prefix=null){

        $mod = self::get_mod();
        $result=array();
        $config = \cms_config::get_instance();

        $s3Client = self::getS3Client();

        try {
            $contents = $s3Client->listObjectsV2([
                'Bucket' => $bucket_id,
                'Prefix' => $prefix,
                'Delimiter' => '/'
            ]);

        } catch (AwsException $e) {
            // Handle the error
            if($e->getStatusCode() == 404){
                $error_message = $bucket_id." ".$e->getAwsErrorMessage();
            } else {
                $error_message = $e->getMessage();
            }

            $smarty = cmsms()->GetSmarty();
            $smarty->assign('error',1);
            $smarty->assign('message',$error_message);
        }

        //print_r($contents['Contents']);

        if(isset($prefix) && $prefix!=''){

            $info=array();
            $info['name']= '..';
            $info['dir'] = true;
            $info['mime'] = 'directory';
            $info['ext']='';
            $result[]=$info;

        }

        //Directories
        foreach ($contents['CommonPrefixes'] as $dir) {

            $info=array();
            $info['name']=$dir['Prefix'];
            $info['dir'] = true;
            $info['mime'] = 'directory';
            $info['ext']='';

            $result[]=$info;

        }
        //Files 
        foreach ($contents['Contents'] as $content) {

            $cmd = $s3Client->headObject([
                'Bucket' => $bucket_id,
                'Key' => $content['Key']
            ]);

            $info=array();
            $info['name']=$content['Key'];
            $info['dir'] = FALSE;
            $info['image'] = FALSE;
            $info['archive'] = FALSE;
            $info['mime'] = $cmd['ContentType'];

                if (is_dir($fullname)) {
                    $info['dir']=true;
                    $info['ext']='';
                } else {
                    $info['size']=self::formatBytes($content['Size']);
                    $info['date']=strtotime( $content['LastModified'] );
                    $info['url']= "https://".$mod->GetPreference('bucket_name').".s3.".$mod->GetPreference('access_region').".amazonaws.com/".$content['Key'];
                    //$info['presignedUrl']=self::presignedUrl($bucket_id,$content['Key'],$s3Client);
                    //$info['url']=$s3Client->getObjectUrl($bucket_id,$content['Key']);
                    $info['url'] = str_replace('\\','/',$info['url']); // windoze both sucks, and blows.
                    $explodedfile=explode('.', $content['Key']); 
                    $info['ext']=array_pop($explodedfile);
                }
            
            // test for archive
            $info['archive'] = '';
            // test for image
            $info['image'] = '';
            $info['fileowner']='N/A';
            $info['writable']='';

            $result[]=$info;


            }

        return $result;
    }

    public static function get_files($options)
    {

        $mod = self::get_mod();
        $result=array();
        $s3Client = self::getS3Client();

        try {

            $contents = $s3Client->listObjectsV2($options);

            if(isset($options['Prefix']) && $options['Prefix']!==''){

                $onerow = new \stdClass();
                $onerow->name = '..';
                $onerow->dir = true;
                $onerow->mime = 'directory';
                $onerow->ext = '';
                $result[]=$onerow;
    
            }
    
            //Directories
            foreach ($contents['CommonPrefixes'] as $dir) {
    
                $onerow = new \stdClass();
                $onerow->name = $dir['Prefix'];
                $onerow->dir = true;
                $onerow->mime = 'directory';
                $onerow->ext = '';
    
                $result[]=$onerow;
    
            }

            foreach ($contents['Contents'] as $content) {

                $onerow = new \stdClass();
    
                $cmd = $s3Client->headObject([
                    'Bucket' => $options['Bucket'],
                    'Key' => $content['Key']
                ]);
    
                $onerow->name = $content['Key'];
                $onerow->dir = FALSE;
                $onerow->image = FALSE;
                $onerow->archive = FALSE;
                $onerow->mime = $cmd['ContentType'];
    
                $onerow->size=self::formatBytes($content['Size']);
                $onerow->date=strtotime( $content['LastModified'] );
                $onerow->url= "https://".$mod->GetPreference('bucket_name').".s3.".$mod->GetPreference('access_region').".amazonaws.com/".$content['Key'];
                $onerow->iconlink = $mod->CreatePrettyLink($content['Key']);
                //$onerow->presignedUrl']=self::presignedUrl($bucket_id,$content['Key'],$s3Client);
                //$onerow->url']=$s3Client->getObjectUrl($bucket_id,$content['Key']);
                $onerow->url = str_replace('\\','/',$onerow->url); // windoze both sucks, and blows.
                $explodedfile=explode('.', $content['Key']); $onerow->ext=array_pop($explodedfile);
                
    
                $result[]=$onerow;
    
            }

            return $result;

        } catch (\Exception $e) {
            $mod->_DisplayMessage($e->getMessage());
            return;
            //throw new \Exception($error_message);
        }

        
        
    }

    public static function is_file_acceptable( $file_type )
  {
      $mod = self::get_mod();
      $file_type = strtolower($file_type);
      $allowTypes = explode(',',$mod->GetPreference('allowed'));
      if(!in_array($file_type, $allowTypes)) return FALSE;
      return TRUE;
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

}
?>