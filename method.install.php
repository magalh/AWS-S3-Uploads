<?php
if( !defined('CMS_VERSION') ) exit;
$this->CreatePermission(AWSS3::MANAGE_PERM,'Manage AWSS3');
//$this->SetPreference('logfilepath', CMS_ROOT_PATH);

$uid = null;
if( cmsms()->test_state(CmsApp::STATE_INSTALL) ) {
  $uid = 1; // hardcode to first user
} else {
  $uid = get_userid();
}
# Setup summary template
try {
    $summary_template_type = new CmsLayoutTemplateType();
    $summary_template_type->set_originator($this->GetName());
    $summary_template_type->set_name('summary');
    $summary_template_type->set_dflt_flag(TRUE);
    $summary_template_type->set_lang_callback('AWSS3::page_type_lang_callback');
    $summary_template_type->set_content_callback('AWSS3::reset_page_type_defaults');
    $summary_template_type->set_help_callback('AWSS3::template_help_callback');
    $summary_template_type->reset_content_to_factory();
    $summary_template_type->save();
  }
  catch( CmsException $e ) {
    // log it
    debug_to_log(__FILE__.':'.__LINE__.' '.$e->GetMessage());
    audit('',$this->GetName(),'Installation Error: '.$e->GetMessage());
  }

try {
    $fn = dirname(__FILE__).DIRECTORY_SEPARATOR.'templates'.DIRECTORY_SEPARATOR.'orig_summary_template.tpl';
    if( file_exists( $fn ) ) {
      $template = @file_get_contents($fn);
      $tpl = new CmsLayoutTemplate();
      $tpl->set_name('AWSS3 Summary Sample');
      $tpl->set_owner($uid);
      $tpl->set_content($template);
      $tpl->set_type($summary_template_type);
      $tpl->set_type_dflt(TRUE);
      $tpl->save();
    }
  }
  catch( CmsException $e ) {
    // log it
    debug_to_log(__FILE__.':'.__LINE__.' '.$e->GetMessage());
    audit('',$this->GetName(),'Installation Error: '.$e->GetMessage());
  }

  # Setup detail template
  try {
    $detail_template_type = new CmsLayoutTemplateType();
    $detail_template_type->set_originator($this->GetName());
    $detail_template_type->set_name('detail');
    $detail_template_type->set_dflt_flag(TRUE);
    $detail_template_type->set_lang_callback('AWSS3::page_type_lang_callback');
    $detail_template_type->set_content_callback('AWSS3::reset_page_type_defaults');
    $detail_template_type->reset_content_to_factory();
    $detail_template_type->set_help_callback('AWSS3::template_help_callback');
    $detail_template_type->save();
  }
  catch( CmsException $e ) {
    // log it
    debug_to_log(__FILE__.':'.__LINE__.' '.$e->GetMessage());
    audit('',$this->GetName(),'Installation Error: '.$e->GetMessage());
  }
  
  try {
    $fn = dirname(__FILE__).DIRECTORY_SEPARATOR.'templates'.DIRECTORY_SEPARATOR.'orig_detail_template.tpl';
    if( file_exists( $fn ) ) {
      $template = @file_get_contents($fn);
      $tpl = new CmsLayoutTemplate();
      $tpl->set_name('AWSS3 Detail Sample');
      $tpl->set_owner($uid);
      $tpl->set_content($template);
      $tpl->set_type($detail_template_type);
      $tpl->set_type_dflt(TRUE);
      $tpl->save();
    }
  }
  catch( CmsException $e ) {
    // log it
    debug_to_log(__FILE__.':'.__LINE__.' '.$e->GetMessage());
    audit('',$this->GetName(),'Installation Error: '.$e->GetMessage());
  }

?>