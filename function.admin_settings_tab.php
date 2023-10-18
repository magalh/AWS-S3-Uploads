<?php
namespace AWSS3;

//$utils = new \AWSS3\utils;
//$sdk_utils = new \AWSSDK\utils;

if( !defined('CMS_VERSION') ) exit;
if( !$this->CheckPermission($this::MANAGE_PERM) ) return;

$SDK = \cms_utils::get_module('AWSSDK');
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

if(!$sdk_utils->is_valid()){
    $sdk_utils->do_debug = 1;
    $sdk_utils->getErrors();
    $message = $SDK->_DisplayMessage($sdk_utils->errors_array,"alert-danger",true);
}


$awsregionnames = $SDK->get_regions_options();

$tpl = $smarty->CreateTemplate( $this->GetTemplateResource('admin_settings_tab.tpl'), null, null, $smarty );
$smarty->assign('access_region_list',$awsregionnames);
$tpl->display();


