<?php
namespace AWSS3;

if( !defined('CMS_VERSION') ) exit;
if( !$this->CheckPermission($this::MANAGE_PERM) ) return;

use \AWSS3\utils;

$template = \xt_param::get_string($params,'template',$this->GetPreference('default_uploadform'));
$nocaptcha = \xt_param::get_bool($params,'nocaptcha');
$message = null;
$utils = new utils();

$template = 'orig_uploadform_template.tpl';
$tpl = $smarty->CreateTemplate($this->GetTemplateResource($template),null,null,$smarty);

if( isset($params['input_submit']) ) {
    try {

        $prefix= $params['prefix']?$params['prefix']:'';

        $uploadfile = $_FILES[$id.'input_browse'];
        $file_name = basename($_FILES[$id.'input_browse']['name']); 
        $file_type = pathinfo($file_name, PATHINFO_EXTENSION); 

        if(empty($file_name)) {
            //throw new \Exception( $this->Lang('warn_file_missing') );
        }

        // Allow certain file formats 
/*        if( !$utils::is_file_acceptable( $file_type ) ) {
            throw new \Exception( $this->Lang('warn_file_not_allowed') );
        } */

        //check captcha
        $captcha = $this->GetModuleInstance('Captcha');
        if( is_object($captcha) && !$nocaptcha && isset($params['input_captcha']) ) {
            if( !$captcha->checkCaptcha(\xt_param::get_string($params,'input_captcha')) ) throw new \Exception($this->lang('error_captchamismatch'));
        }

        $file_temp_src = $_FILES[$id.'input_browse']["tmp_name"];
        if(is_uploaded_file($file_temp_src)){
            $bucket_id = $this->GetOptionValue('bucket_name');
            $ret = $utils->upload_file($bucket_id,$prefix.$file_name,$file_temp_src);
        }
    
        if( $ret[0] == false ) throw new \Exception($ret[1]);
        $good_upload = $ret[2];

        // redirect outa here, or display a message
        if( ($tmp = $params['redirect']) ) {
            $destpage = $this->resolve_alias_or_id($tmp,$returnid);
            $this->redirectcontent( $destpage );
        }
    }
    catch( \Exception $e ) {
        $errormessage = $e->getmessage();
    }

    if(isset($errormessage)){
        $type = "alert-danger";
        $message = $errormessage;
    } else {
        $type = "alert-success";
        $message = $this->Lang('successful_upload',$good_upload);
    }

    $this->_DisplayMessage($message,$type);

    $tpl->assign('message',$message);

}

$parms = array('prefix'=>$params['prefix'],'nocaptcha'=>$nocaptcha,'redirect'=>$params['redirect']);
// encrypt these params.
//function CreateFormStart($id, $action='default', $returnid='', $method='post', $enctype='', $inline=false, $idsuffix='', $params = array(), $extra='')
$tpl->assign('startform',$this->CreateFormStart($id, 'upload', $returnid,"post","multipart/form-data",false,'',$params));
$tpl->assign('endform',$this->CreateFormEnd());
if( !$nocaptcha ) {
    $captcha = $this->GetModuleInstance('Captcha');
    if( is_object($captcha) ) {
        $tpl->assign('captcha_title', $this->Lang('captcha_title'));
        $tpl->assign('captcha', $captcha->getCaptcha());
    }
}

$tpl->display();

?>
