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

namespace AWSS3;

if (!function_exists("cmsms")) exit;
if( !$this->CheckPermission($this::MANAGE_PERM) ) return;

$bucket_id = $this->GetOptionValue('bucket_name');

$return_params = [ 'prefix'=>$params['prefix'],'__activetab'=>$bucket_id];
if (isset($params["cancel"])) $this->Redirect($id,"defaultadmin",$returnid,$return_params);
$mod_fm = \cms_utils::get_module('FileManager');

$selall = $params['selall'];
if( !is_array($selall) ) $selall = unserialize($selall);
if( !is_array($selall) ) $selall = unserialize($selall);

if (count($selall)==0) {
  $return_params["fmerror"]="nofilesselected";
  $this->Redirect($id,"defaultadmin",$returnid,$return_params);
}

// process form
$errors = array();
if( isset($params['submit']) ) {
  
  foreach( $selall as $file ) {
    // build complete path
    $lastchar = $file[-1];

    if ( strcmp($lastchar, "/") === 0 ) {
      aws_s3_utils::deleteObjectDir($bucket_id,$file);
      audit('',"AWSS3", "Removed directory: ".$file);
      \CMSMS\HookManager::do_hook('AWSS3::OnDirectoryDeleted', $parms );
      continue;
    }
    
    aws_s3_utils::deleteObject($bucket_id,$file);
    $parms = array('file'=>$file);
    audit('',"S3", "Removed file: ".$file);
    \CMSMS\HookManager::do_hook('AWSS3::OnFileDeleted', $parms );

  } // foreach

  if( count($errors) == 0 ) {
    $return_params["fmmessage"]= $mod_fm->Lang("deletesuccess"); //strips the file data
    $this->Redirect($id,"defaultadmin",$returnid,$return_params);
  }

} // if submit

// give everything to smarty.
if( count($errors) ) {
  echo $this->ShowErrors($errors);
  $smarty->assign('errors',$errors);
}
if( is_array($params['selall']) ) $params['selall'] = serialize($params['selall']);
$smarty->assign('selall',$selall);
$smarty->assign('mod_fm',$mod_fm);
$smarty->assign('startform', $this->CreateFormStart($id, 'fileaction', $returnid,"post","",false,"",$params));
$smarty->assign('endform', $this->CreateFormEnd());

foreach( $selall as $file ) {
  $lastchar = $file[-1];
  if ( strcmp($lastchar, "/") === 0 ) {
    $smarty->assign('hasdir',true);
  }
}

echo $this->ProcessTemplate('delete.tpl');

?>
