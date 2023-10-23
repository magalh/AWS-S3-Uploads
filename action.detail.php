<?php
#BEGIN_LICENSE
#-------------------------------------------------------------------------
# Module: Uploads (c) 2011-2014 by Robert Campbell
#         (calguy1000@cmsmadesimple.org)
#  An addon module for CMS Made Simple to allow management of uploaded files
#  has numerous capabilities including yousendit, time limited urls,
#  frontend uploads..
#
#-------------------------------------------------------------------------
# CMSMS - CMS Made Simple is (c) 2005 by Ted Kulp (wishy@cmsmadesimple.org)
# Visit the CMSMS Homepage at: http://www.cmsmadesimple.org
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
# Or read it online: http:	//www.gnu.org/licenses/licenses.html#GPL
#
#-------------------------------------------------------------------------
#END_LICENSE
if( !defined('CMS_VERSION') ) exit;

use \AWSSDK\aws_sdk_utils;
use \AWSS3\aws_s3_utils;

$helpers = $this->GetHelpers();

print_r($params);

try {

	if( !isset($params['key']) ) {
		
		throw new \AWSS3\Exception('Upload id not specified',500);
		//$errors[] = 'Upload id not specified';
		//$this->_DisplayErrorMessage($errors);
	}

	$key = urldecode($params['key']);
	$bucket_id = $this->GetOptionValue('bucket_name');
	$file_info = aws_s3_utils::get_file_info($key);

	//print_r($file_info);
	
} catch (\AWSS3\Exception $e) {
	$this->_DisplayMessage($e->getText(),$e->getType());
}

?>
