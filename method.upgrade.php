<?php
if (!isset($gCms)) exit;
$db = $this->GetDb();

$uid = 1; // hardcode to first user

if( version_compare($oldversion,'1.1.1') <= 0 ) {
    # Setup upload template
    $message = null;
    try {
        $upload_template_type = new CmsLayoutTemplateType();
        $upload_template_type->set_originator($this->GetName());
        $upload_template_type->set_name('upload');
        $upload_template_type->set_dflt_flag(TRUE);
        $upload_template_type->set_lang_callback('AWSS3::page_type_lang_callback');
        $upload_template_type->set_content_callback('AWSS3::reset_page_type_defaults');
        $upload_template_type->reset_content_to_factory();
        $upload_template_type->set_help_callback('AWSS3::template_help_callback');
        $upload_template_type->save();
      }
      catch( CmsException $e ) {
        // log it
        $message = $e->GetMessage();
        debug_to_log(__FILE__.':'.__LINE__.' '.$message);
        audit('',$this->GetName(),'Upgrade Error: '.$message);
      }

      try {

        if($message == "Template Type with the same name already exists."){
            $upload_template_type = CmsLayoutTemplateType::load($this->GetName() . '::upload');
        }
    
        $fn = dirname(__FILE__).DIRECTORY_SEPARATOR.'templates'.DIRECTORY_SEPARATOR.'orig_uploadform_template.tpl';
        if( file_exists( $fn ) ) {
          $template = @file_get_contents($fn);
          $tpl = new CmsLayoutTemplate();
          $tpl->set_name('AWSS3 Upload Form Sample');
          $tpl->set_owner($uid);
          $tpl->set_content($template);
          $tpl->set_type($upload_template_type);
          $tpl->set_type_dflt(TRUE);
          $tpl->save();
        }
      }
      catch( CmsException $e ) {
        // log it
        debug_to_log(__FILE__.':'.__LINE__.' '.$e->GetMessage());
        audit('',$this->GetName(),'Upgrade Error: '.$e->GetMessage());
      }

}

?>
