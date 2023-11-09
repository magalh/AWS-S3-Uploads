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

if( !defined('CMS_VERSION') ) exit;
if( !$this->CheckPermission($this::MANAGE_PERM) ) return;

use \AWSS3\aws_s3_utils;
$__sdk = \cms_utils::get_module('AWSSDK');

$template = \xt_param::get_string($params,'template',$this->GetPreference('default_uploadform'));
$nocaptcha = \xt_param::get_bool($params,'nocaptcha');
$message = null;
$utils = new aws_s3_utils();

//$template = 'orig_uploadform_template.tpl';
$tpl = $smarty->CreateTemplate($this->GetTemplateResource($template),null,null,$smarty);


if( isset($params['input_submit']) ) {
    try {

        $prefix= $params['prefix']?$params['prefix']:'';

        $uploadfile = $_FILES[$id.'input_browse'];
        $file_name = basename($_FILES[$id.'input_browse']['name']); 
        $file_type = pathinfo($file_name, PATHINFO_EXTENSION); 

        if(empty($file_name)) {
            throw new \Exception( $this->Lang('warn_file_missing') );
        }

        // Allow certain file formats 
        if( !$utils::is_file_acceptable( $file_type ) ) {
            throw new \Exception( $this->Lang('warn_file_not_allowed') );
        } 

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
            $destpage = $__sdk->resolve_alias_or_id($tmp,$returnid);
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

    $message = $__sdk->_DisplayMessage($message,$type,1);

    $tpl->assign('message',$message);

}

$parms = array('prefix'=>$params['prefix'],'nocaptcha'=>$nocaptcha,'redirect'=>$params['redirect']);
// encrypt these params.
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
