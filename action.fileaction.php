<?php
if (!function_exists("cmsms")) exit;
if( !$this->CheckPermission($this::MANAGE_PERM) ) return;
if (!isset($params["path"])) $this->Redirect($id, 'defaultadmin');

$path=$params["path"];

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

$this->Redirect($id,"defaultadmin",$returnid,array("path"=>$params["path"],"fmerror"=>"unknownfileaction"));

?>
