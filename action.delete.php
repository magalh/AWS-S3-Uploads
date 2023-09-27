<?php
namespace AWSS3;
if (!function_exists("cmsms")) exit;
if( !$this->CheckPermission($this::MANAGE_PERM) ) return;

$return_params = [ 'newdir'=>$params['path'],'__activetab'=>$params['bucket_id']];
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
      utils::deleteObjectDir($params['bucket_id'],$file);
      audit('',"S3", "Removed directory: ".$file);
      \CMSMS\HookManager::do_hook('AWSS3::OnDirectoryDeleted', $parms );
      continue;
    }
    
    utils::deleteObject($params['bucket_id'],$file);
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
