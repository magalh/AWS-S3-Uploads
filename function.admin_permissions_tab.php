<?php
namespace AWSS3;

if( !defined('CMS_VERSION') ) exit;
if( !$this->CheckPermission($this::MANAGE_PERM) ) return;


$tpl = $smarty->CreateTemplate( $this->GetTemplateResource('admin_permissions_tab.tpl'), null, null, $smarty );
$bucket_policy = utils::get_iam_policy();
$smarty->assign('bucket_policy',$bucket_policy);

$tpl->display();


