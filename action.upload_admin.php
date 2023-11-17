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

header('Content-type: application/json');
use AWSS3\utils;
$utils = new utils();
$FileManager = \cms_utils::get_module('FileManager');
$error = 0;

$data = array();

try {

    if(empty($_FILES)) {
        throw new \Exception( $this->Lang('warn_file_missing') );
    }

    $files = array_filter($_FILES[$id.'files']['name']);

    $total_count = count($_FILES[$id.'files']['name']);
    // Loop through every file
    for( $i=0 ; $i < $total_count ; $i++ ) {

        $file_name = basename($_FILES[$id.'files']['name'][$i]); 
        $file_type = pathinfo($file_name, PATHINFO_EXTENSION); 

        $return_params['name'] = $file_name;
        $return_params['size'] = $_FILES[$id.'files']['size'][$i];
        
        // Allow certain file formats 
        if( !$utils::is_file_acceptable( $file_type ) ) {
            //throw new \Exception( $this->Lang('warn_file_not_allowed') );
            $return_params['error'] = "Filetype not allowed";
        } else {
            //upload to S3
            $file_temp_src = $_FILES[$id.'files']["tmp_name"][$i];
            if(is_uploaded_file($file_temp_src)){
                $s3 = $utils->upload_file($params["prefix"].$file_name,$file_temp_src);
            }
        }

        $data['files'][] = $return_params;

    }

}
catch( \Exception $e ) {
	$error = 1;	
	$message = $e->getMessage();
    $data['error'] = $message;

}


echo json_encode($data);

#
# EOF
#
?>
