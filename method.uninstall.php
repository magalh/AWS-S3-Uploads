<?php
if( !defined('CMS_VERSION') ) exit;
$this->RemovePermission(AWSS3::MANAGE_PERM);

$this->DeleteTemplate('displaysummary');
$this->DeleteTemplate('displaydetail');
$this->RemovePreference();

// remove templates
// and template types.
try {
    $types = CmsLayoutTemplateType::load_all_by_originator($this->GetName());
    if( is_array($types) && count($types) ) {
      foreach( $types as $type ) {
        $templates = $type->get_template_list();
        if( is_array($templates) && count($templates) ) {
      foreach( $templates as $template ) {
        $template->delete();
      }
        }
        $type->delete();
      }
    }
  }
  catch( Exception $e ) {
    // log it
    audit('',$this->GetName(),'Uninstall Error: '.$e->GetMessage());
  }

  $this->RemoveEventHandler('Core', 'ContentPostRender');
  
?>