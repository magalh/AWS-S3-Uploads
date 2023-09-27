<?php
namespace AWSS3;

if( !defined('CMS_VERSION') ) exit;
if( !$this->CheckPermission($this::MANAGE_PERM) ) return;

$error = 0;

if( isset($params['submit']) ) {

    $settings_input = $params['settings_input'];
    if(count($settings_input) > 0)
    {
        foreach($settings_input as $key => $value) 
        {
            $this->SetOptionValue($key, $value);
        }
    }

    if($ready){
        $this->RedirectToAdminTab($params['bucket_name']);
    } else {
        $this->RedirectToAdminTab('settings');
    }
}

$tpl = $smarty->CreateTemplate( $this->GetTemplateResource('admin_settings_tab.tpl'), null, null, $smarty );
$awsregionnames = file_get_contents(dirname(__FILE__).'/doc/aws-region-names.json');
$smarty->assign('access_region_list',json_decode($awsregionnames,true));
$smarty->assign('root_path',CMS_ROOT_PATH);
if(isset($error_message)){
    $smarty->assign('aws_error_msg',$error_message);
}
$tpl->display();


