<?php
namespace AWS_S3_Uploads;

require dirname(__DIR__, 1).'/SDK/aws-autoloader.php';

use Aws\S3\S3Client;  
use Aws\Exception\AwsException;
use Aws\Credentials\Credentials;

final class utils
{

    private $s3Client = null;

    public function __construct (){}

    public static function list_validation_editors()
    {
        return 'ciao';
    }

    public function is_aws_ready() {
        $mod = $this->get_mod();
		$data = array('s3_bucket_name','s3_region','s3_uploads_secret','s3_uploads_key');
		$error_message="";
		foreach ($data as $key)
		{
			$item = $mod->GetPreference($key);
			if($item=="" or $item==null)
			{
				$error_message.=$mod->Lang($key).",";
			}
		}
		if($error_message!="")
		{
			$error_message=substr($error_message, 0,-1);
			$smarty = cmsms()->GetSmarty();
			$smarty->assign('errormsg',$error_message.' cannot be null or empty.');
			return false;
		}
		else
		{
            $this->s3Client = new S3Client([
                'credentials' => $this->get_credentials(),
                'region' => $mod->GetPreference('s3_region'),
                'version' => 'latest'
            ]);
			return true;
		}
	}

    public function list_my_buckets(){
        $buckets = $this->s3Client->listBuckets();
        return $buckets['Buckets'];
    }

    public function list_my_files(){
        $buckets = $this->s3Client->listBuckets();
        return $buckets['Buckets'];
    }
    
    private static function get_mod()
    {
        static $_mod;
        if( !$_mod ) $_mod = \cms_utils::get_module('AWS_S3_Uploads');
        return $_mod;
    }

    private static function get_credentials()
    {
        $mod = self::get_mod();
        $credentials = new Credentials($mod->GetPreference('s3_uploads_secret'), $mod->GetPreference('s3_uploads_key'));
        return $credentials;
    }

    private static function presignedUrl($bucket_id,$key,$s3Client)
    {
        $cmd = $s3Client->getCommand('GetObject', [
            'Bucket' => $bucket_id,
            'Key' => $key
        ]);

        $request = $s3Client->createPresignedRequest($cmd, '+20 minutes');
        $presignedUrl = (string)$request->getUri();
        return $presignedUrl;
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

    public static function get_file_list($bucket_id,$prefix=null)
    {

        $mod = \cms_utils::get_module('AWS_S3_Uploads');
        $filemod = \cms_utils::get_module('FileManager');
        $showhiddenfiles=$filemod->GetPreference('showhiddenfiles','1');
        $result=array();
        $config = \cms_config::get_instance();

        $s3Client = new S3Client([
            'credentials' => self::get_credentials(),
            'region' => $mod->GetPreference('s3_region'),
            'version' => 'latest'
        ]);

        $contents = $s3Client->listObjectsV2([
            'Bucket' => $bucket_id,
            'Prefix' => $prefix,
            'Delimiter' => '/'
        ]);

        //print_r($contents);

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
                    $info['url']=self::presignedUrl($bucket_id,$content['Key'],$s3Client);
                    $info['url'] = str_replace('\\','/',$info['url']); // windoze both sucks, and blows.
                    $explodedfile=explode('.', $content['Key']); $info['ext']=array_pop($explodedfile);
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


}
?>