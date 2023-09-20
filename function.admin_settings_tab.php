<?php
namespace AWS_S3_Uploads;

if( !defined('CMS_VERSION') ) exit;
if( !$this->CheckPermission($this::MANAGE_PERM) ) return;



$error = 0;



    //$credentials = new Credentials($this->GetPreference('s3_uploads_secret'), $this->GetPreference('s3_uploads_key'));

/*    $s3Client = new S3Client([
        'credentials' => $credentials,
        'region' => $this->GetPreference('s3_region'),
        'version' => 'latest'
    ]);*/

    /*$buckets = $s3Client->listBuckets();
foreach ($buckets['Buckets'] as $bucket) {
    echo $bucket['Name'] . "\n";
}
    
    try {
        $contents = $s3Client->listObjectsV2([
            'Bucket' => $this->GetPreference('s3_bucket_name'),
        ]);
    
        foreach ($contents['Contents'] as $content) {
            echo $content['Key'] . "<br>";
        }

    } catch (\Exception $e) {
        $error = 1;
        $error_message = $e->getMessage();
    }
*/





if( isset($params['submit']) ) {
    $this->SetPreference('s3_bucket_name',$params['s3_bucket_name']);
    $this->SetPreference('s3_region',$params['s3_region']);
    $this->SetPreference('s3_uploads_secret',$params['s3_uploads_secret']);
    $this->SetPreference('s3_uploads_key',$params['s3_uploads_key']);
    $this->SetMessage("Saved");
    $this->RedirectToAdminTab('settings');
}

$tpl = $smarty->CreateTemplate( $this->GetTemplateResource('admin_settings_tab.tpl'), null, null, $smarty );
$smarty->assign('all_validations',utils::list_validation_editors() );


$awsregionnames = file_get_contents(dirname(__FILE__).'/doc/aws-region-names.json');
$smarty->assign('s3_region_list',json_decode($awsregionnames,true));

$smarty->assign('s3_bucket_name',$this->GetPreference('s3_bucket_name'));
$smarty->assign('s3_region',$this->GetPreference('s3_region'));
$smarty->assign('s3_uploads_secret',$this->GetPreference('s3_uploads_secret'));
$smarty->assign('s3_uploads_key',$this->GetPreference('s3_uploads_key'));
$smarty->assign('root_path',CMS_ROOT_PATH);
if(isset($error_message)){
    $smarty->assign('aws_error_msg',$error_message);
}
$tpl->display();


