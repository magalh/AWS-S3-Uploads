<?php
if( !defined('CMS_VERSION') ) exit;
if( !$this->CheckPermission(AWS_S3_Uploads::MANAGE_PERM) ) return;

if( isset($params['submit']) ) {
    $this->SetPreference('s3_bucket_name',$params['s3_bucket_name']);
    $this->SetPreference('s3_region',$params['s3_region']);
    $this->SetPreference('s3_uploads_secret',$params['s3_uploads_secret']);
    $this->SetPreference('s3_uploads_key',$params['s3_uploads_key']);
    $this->SetMessage("Saved");
    $this->RedirectToAdminTab('settings');
}

$tpl = $smarty->CreateTemplate( $this->GetTemplateResource('admin_settings_tab.tpl'), null, null, $smarty );

$awsregionnames = file_get_contents(dirname(__FILE__).'/doc/aws-region-names.json');
$smarty->assign('s3_region_list',json_decode($awsregionnames,true));

$smarty->assign('s3_bucket_name',$this->GetPreference('s3_bucket_name'));
$smarty->assign('s3_region',$this->GetPreference('s3_region'));
$smarty->assign('s3_uploads_secret',$this->GetPreference('s3_uploads_secret'));
$smarty->assign('s3_uploads_key',$this->GetPreference('s3_uploads_key'));
$smarty->assign('root_path',CMS_ROOT_PATH);
$tpl->display();


