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

if( !defined('CMS_VERSION') ) exit;
if( !$this->CheckPermission(AWSS3::MANAGE_PERM) ) return;

use \AWSS3\helpers;
use \AWSS3\utils;

$mod_fm = \cms_utils::get_module('FileManager');
$sdk_mod = utils::get_sdk();
$sdk_utils = $sdk_mod->getUtils();
$s3_utils = new utils();

$ready = 0;

if($s3_utils->validate()){
	$ready = 1;
	$bucket_id = $this->GetOptionValue('bucket_name');
}

echo $this->StartTabHeaders();
	if($ready == 1){
		echo $this->SetTabHeader($bucket_id,$bucket_id);
		echo $this->SetTabHeader('permissions',"Permissions");
	}

	echo $this->SetTabHeader('settings',"Settings");
echo $this->EndTabHeaders();

echo $this->StartTabContent();

	if($ready == 1){

		echo $this->StartTab($bucket_id);
		include(dirname(__FILE__)."/action.uploadview.php");
		include(dirname(__FILE__)."/action.admin_fileview.php");
		echo $this->EndTab();

		echo $this->StartTab('permissions');
		include(__DIR__.'/function.admin_permissions_tab.php');
		echo $this->EndTab();
	}

	echo $this->StartTab('settings');
		include(__DIR__.'/function.admin_settings_tab.php');
	echo $this->EndTab();
	

echo $this->EndTabContent();

$this->SetCurrentTab("pixelsolution");

?>