<?php
namespace AWS_S3_Uploads;

if( !defined('CMS_VERSION') ) exit;
if( !$this->CheckPermission($this::MANAGE_PERM) ) return;

header('Content-type: application/json');
use AWS_S3_Uploads\utils;
$utils = new utils();
$mod_fm = \cms_utils::get_module('FileManager');
$error = 0;

//$return_params = [ 'newdir'=>$params['path'],'__activetab'=>$params['bucket_id']];

$data = array();

/*$json = file_get_contents(dirname(__FILE__).'/files.json');
$json_data = json_decode($json,true);
echo json_encode($json_data);
exit();
*/
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
                //$s3 = $utils->upload_file($params["bucket_id"],$params["path"].$file_name,$file_temp_src);
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
