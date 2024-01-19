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

if (!function_exists("cmsms")) exit;
if( !$this->CheckPermission($this::MANAGE_PERM) ) return;
//if (!isset($params["path"])) $this->Redirect($id, 'defaultadmin');

$fileaction="";
if (isset($params["fileaction"])) $fileaction=$params["fileaction"];

if (isset($params["fileactionnewdir"]) || $fileaction=="newdir") {
  include_once(__DIR__."/action.newdir.php");
  return;
}

if (isset($params["fileactionrename"]) || $fileaction=="rename") {
  include_once(__DIR__."/action.rename.php");
  return;
}

if (isset($params["fileactiondelete"]) || $fileaction=="delete") {
  include_once(__DIR__."/action.delete.php");
  return;
}

if (isset($params["fileactioncopy"]) || $fileaction=="copy") {
  include_once(__DIR__."/action.copy.php");
  return;
}

if (isset($params["fileactionmove"]) || $fileaction=="move") {
  include_once(__DIR__."/action.move.php");
  return;
}

if (isset($params["fileactionunpack"]) || $fileaction=="unpack") {
  include_once(__DIR__."/action.unpack.php");
  return;
}

if (isset($params["fileactionthumb"]) || $fileaction=="thumb") {
  include_once(__DIR__."/action.thumb.php");
  return;
}

if (isset($params['fileactionresizecrop']) || $fileaction == 'resizecrop') {
  include_once(__DIR__.'/action.resizecrop.php');
  return;
}

if (isset($params['fileactionrotate']) || $fileaction == 'rotate') {
  include_once(__DIR__.'/action.rotate.php');
  return;
}

if (isset($params['fileactionrefresh']) || $fileaction == 'refresh') {
  include_once(__DIR__.'/action.refresh.php');
  return;
}

$this->Redirect($id,"defaultadmin",$returnid,array("prefix"=>$params["prefix"],"fmerror"=>"unknownfileaction"));

?>
