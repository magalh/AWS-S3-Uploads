<?php
namespace AWSS3;

if( !defined('CMS_VERSION') ) exit;
if( !$this->CheckPermission($this::MANAGE_PERM) ) return;

$sdk_mod = $sdk_utils::get_mod();
$error = 0;
$ready = 0;

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




$awsregionnames = $sdk_mod->get_regions_options();

$tpl = $smarty->CreateTemplate( $this->GetTemplateResource('admin_settings_tab.tpl'), null, null, $smarty );
$tpl->assign('access_region_list',$awsregionnames);
$tpl->assign('message',$message);
$tpl->assign('error',$error);
$tpl->assign('sdk_mod',$sdk_mod);
$tpl->display();


