<?php
if (!function_exists("cmsms")) exit;

$path='';
if( isset($params["newdir"])) {
  $path = $params["newdir"];
}
$smarty->assign('hiddenpath', $this->CreateInputHidden($id, "path", $path));
$smarty->assign('bucket_id', $this->CreateInputHidden($id, "bucket_id", $bucket_id));
$smarty->assign('formstart',$this->CreateFormStart($id, 'upload', $returnid,"post","multipart/form-data"));
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
$smarty->assign('action_url',$this->create_url('m1_','upload',$returnid));
$smarty->assign('ie_upload_message',$this->Lang('ie_upload_message'));
$smarty->assign('mod_fm',\cms_utils::get_module('FileManager'));

echo $this->ProcessTemplate('uploadview.tpl');
