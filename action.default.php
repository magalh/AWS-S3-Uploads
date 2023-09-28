<?php
#BEGIN_LICENSE
#-------------------------------------------------------------------------
# Module: AWSS3
# Authors: Magal Hezi, with CMS Made Simple Foundation able to assign new administrators.
# Copyright: (C) 2023 Magal Hezi, magal@pixelsolutions.biz
# AWSS3 is An addon module for CMS Made Simple to provide the ability to access and upload
# objects to Amazon (AWS) S3 Buckets.
#
#-------------------------------------------------------------------------
# CMS - CMS Made Simple is (c) 2005 by Ted Kulp (wishy@cmsmadesimple.org)
# This projects homepage is: http://www.cmsmadesimple.org
#
#-------------------------------------------------------------------------
#
# This program is free software; you can redistribute it and/or modify
# it under the terms of the GNU General Public License as published by
# the Free Software Foundation; either version 2 of the License, or
# (at your option) any later version.
#
# However, as a special exception to the GPL, this software is distributed
# as an addon module to CMS Made Simple.  You may not use this software
# in any Non GPL version of CMS Made simple, or in any version of CMS
# Made simple that does not indicate clearly and obviously in its admin
# section that the site was built with CMS Made simple.
#
# This program is distributed in the hope that it will be useful,
# but WITHOUT ANY WARRANTY; without even the implied warranty of
# MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
# GNU General Public License for more details.
# You should have received a copy of the GNU General Public License
# along with this program; if not, write to the Free Software
# Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA 02111-1307 USA
# Or read it online: http://www.gnu.org/licenses/licenses.html#GPL
#
#-------------------------------------------------------------------------
#END_LICENSE
if( !defined('CMS_VERSION') ) exit;

use \AWSS3\utils;
###################################
# Display the create comment form #
###################################

//
// Initialization
//
$error = $message = null;
$inline = false;

$mams = \cms_utils::get_module('MAMS');
if( $mams ) $mams_uid = $mams->LoggedInId();

print_r($params);

$filename = xt_param::get_string($params,'filename',$filename);
$inline = xt_param::get_bool($params,'inline',$inline);

//$query = new HolidayQuery(array('published'=>1));
//$holidays = $query->GetMatches();
$items = array();
$tpl = $smarty->CreateTemplate($this->GetTemplateResource('default.tpl'),null,null,$smarty);
$tpl->assign('items',$items);
$tpl->display();


#
# EOF
#