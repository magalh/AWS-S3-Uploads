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

if( !defined('CMS_VERSION') ) exit;
$this->CreatePermission(AWSS3::MANAGE_PERM,'Manage AWSS3');

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


  // create preferences
$this->SetPreference('allowed', 'jpg,jpeg,gif,png');
$this->SetPreference('itemsdisplayed', '25');
$this->AddEventHandler('Core', 'ContentPostRender', false);

?>