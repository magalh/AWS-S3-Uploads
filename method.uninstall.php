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