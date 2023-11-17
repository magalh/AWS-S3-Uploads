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
if( !$this->CheckPermission($this::MANAGE_PERM) ) return;

$path='';
if( isset($params["prefix"])) {
  $path = $params["prefix"];
  $smarty->assign('hiddenpath', $this->CreateInputHidden($id, "prefix", $path));
}

$smarty->assign('formstart',$this->CreateFormStart($id, 'upload_admin', $returnid,"post","multipart/form-data"));
$smarty->assign('actionid',$id);
$smarty->assign('maxfilesize',$config["max_upload_size"]);
$smarty->assign('submit',$this->CreateInputSubmit($id,"submit",$this->Lang("submit"),"",""));
$smarty->assign('formend',$this->CreateFormEnd());



$post_max_size = filemanager_utils::str_to_bytes(ini_get('post_max_size'));
$upload_max_filesize = filemanager_utils::str_to_bytes(ini_get('upload_max_filesize'));
$smarty->assign('max_chunksize',min($upload_max_filesize,$post_max_size-1024));
if (isset($_SERVER['HTTP_USER_AGENT']) && (strpos($_SERVER['HTTP_USER_AGENT'], 'MSIE') !== false)) {
    $smarty->assign('is_ie',1);
}
$smarty->assign('action_url_upload',$this->create_url('m1_','upload',$returnid));
$smarty->assign('FileManager',\cms_utils::get_module('FileManager'));

echo $this->ProcessTemplate('uploadview.tpl');
