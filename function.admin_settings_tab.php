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

$content_ops         = cmsms()->GetContentOperations();
$awsregionnames      = $sdk_mod->get_regions_options();

$tpl = $smarty->CreateTemplate( $this->GetTemplateResource('admin_settings_tab.tpl'), null, null, $smarty );
$tpl->assign('access_region_list',$awsregionnames);
$tpl->assign('message',$message);
$tpl->assign('error',$error);

// Module defaults
$tpl->assign('input_detailpage', $content_ops->CreateHierarchyDropdown('', $this->GetPreference('detailpage'), $id.'settings_input[detailpage]'));
$tpl->assign('input_summarypage', $content_ops->CreateHierarchyDropdown('', $this->GetPreference('summarypage'), $id.'settings_input[summarypage]'));

$tpl->display();
//$tpl->display('string:<pre>{get_template_vars}</pre>');


