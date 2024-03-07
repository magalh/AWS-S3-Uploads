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

use \AWSSDK\encrypt;

if( !$this->CheckPermission($this::MANAGE_PERM) ) return;

$bucket_id = $this->GetOptionValue('bucket_name');
$this->SetCurrentTab($bucket_id);
$FileManager = \cms_utils::get_module('FileManager');
$nminutes = (int)$this->GetPreference('expiry_interval', 180);
if ($nminutes == '') $nminutes = 0;

if( isset($params["ajax"])) {
  $bucket_id = $params["bucket_id"];
  $prefix = $params["prefix"];
  $smarty->assign('ajax', true);
}

if (isset($params["fmmessage"]) && $params["fmmessage"]!="") {
  // gotta get rid of this stuff.
  $count="";
  if (isset($params["fmmessagecount"]) && $params["fmmessagecount"]!="") $count=$params["fmmessagecount"];
  echo $this->ShowMessage($this->Lang($params["fmmessage"],$count));
}

if (isset($params["fmerror"]) && $params["fmerror"]!="") {
  // gotta get rid of this stuff
  $count="";
  if (isset($params["fmerrorcount"]) && $params["fmerrorcount"]!="") $count=$params["fmerrorcount"];
  echo $this->ShowErrors($FileManager->Lang($params["fmerror"],$count));
}

$pagelimit = $this->GetOptionValue('itemsdisplayed')?$this->GetOptionValue('itemsdisplayed'):10;
$pagecount = -1;
$startelement = 0;
$pagenumber = 1;

if( isset( $params['pagenumber'] ) && $params['pagenumber'] !== '' ) {
  $pagenumber = (int)$params['pagenumber'];
  $startelement = ($pagenumber-1) * $pagelimit;
}
$endelement = $startelement + $pagelimit - 1;

$qparms = array();
$qparms['bucket'] = $bucket_id;
$qparms['prefix'] = $params["prefix"];
$qparms['returnid'] = $returnid;
$smarty->assign('qparms',$qparms);

$json_file_Path = $this->getCacheFile($qparms);
if($nminutes == 0) {
  $data = bucket_query::cache_query($qparms,$json_file_Path);
} else {

  if (!file_exists($json_file_Path)) {
      $data = bucket_query::cache_query($qparms,$json_file_Path);
  } else {

      $json = file_get_contents($json_file_Path);
      $tmp = encrypt::decrypt($json);
      $data = json_decode($tmp, false);
  
      $enddate  = strtotime(sprintf("+%d minutes", $nminutes), $data->date);
      if (time() >= $enddate) {
          $data = bucket_query::cache_query($qparms,$json_file_Path);
      }

  }
}

$prefix = $params['prefix']?$params['prefix']:'';
$tmp_path_parts=explode('/', $prefix); 

$path_parts = [];
for( $i = 0; $i < count($tmp_path_parts); $i++ ) {
    $obj = new \StdClass;
    if( !$tmp_path_parts[$i] ) continue;
    $obj->name = $tmp_path_parts[$i];
    if( $obj->name == '::top::' ) {
        $obj->name = 'root';
    }
    if( $i < count($tmp_path_parts) - 1 ) {
        // not the last entry
        $fullpath = implode('/',array_slice($tmp_path_parts,0,$i+1));
        if( startswith($fullpath,'::top::') ) $fullpath = substr($fullpath,7);
        $obj->url = $this->create_url( $id, 'defaultadmin', '',[ 'prefix' => $fullpath.'/','__activetab'=>$bucket_id] );

    } else {
        // the last entry... no link
    }
    $path_parts[] = $obj;
}

$up_home = $this->CreateFrontendLink( $id, $returnid,'defaultadmin',$bucket_id);
$smarty->assign('up_home',$up_home);

$parts_counter = count($path_parts);
if( $parts_counter >= 1 ) {
  $updir = utils::getUpDir($path_parts);
  $icon = $FileManager->GetModuleURLPath().'/icons/themes/default/actions/dir_up.gif';
  $img_tag = '<img src="'.$icon.'" width="32" title="'.$this->Lang('title_changeupdir').'"/>';
  if($parts_counter > 1){
    $parms['prefix'] = $updir;
  }
  unset($params['pagenumber']);
  
  $up_dirurl = $this->create_url( $id, 'defaultadmin', '',$parms);
  $up_diriconlink = $this->CreateFrontendLink($id,$returnid,'defaultadmin',$img_tag,$parms,'',false,true,"class='dirlink'");
  $up_dirlink = "<a class=\"card-link\" href=\"{$up_dirurl}\" title=\"{$this->Lang('title_changeupdir')}\">..</a>";

  $smarty->assign('up_dirurl',$up_dirurl);
  $smarty->assign('up_diriconlink',$up_diriconlink);
  $smarty->assign('up_dirlink',$up_dirlink);

}

$smarty->assign('path',$params["prefix"]);
$smarty->assign('path_parts',$path_parts);

$countfiles = isset($data->total) ? $data->total : '0';
$pagecount = (int)($countfiles / $pagelimit);
if( ($countfiles % $pagelimit) != 0 ) $pagecount++;

// build display
$smarty->assign('itemcount', $countfiles);
$smarty->assign('hiddenpath', $this->CreateInputHidden($id, "prefix", $params["prefix"]));
$smarty->assign('formstart', $this->CreateFormStart($id, 'fileaction', $returnid));
// roundown
$totalsize = utils::sumBytes($data->items);
$counts = $totalsize . " " . $FileManager->Lang("in") . " " . $countfiles . " ";
if ($countfiles == 1) $counts.=$FileManager->Lang("file"); else $counts.=$FileManager->Lang("files");
$counts.=" " . $FileManager->Lang("and") . " " . $countdirs . " ";
if ($countdirs == 1) $counts.=$FileManager->Lang("subdir"); else $counts.=$FileManager->Lang("subdirs");
$smarty->assign('countstext', $counts);

$smarty->assign('formend', $this->CreateFormEnd());
$smarty->assign('bucket_id', $bucket_id);
$smarty->assign('cachefile',$json_file_Path);
$smarty->assign('lastupdate', $data->date);
$smarty->assign('pagenumber',$pagenumber);
$smarty->assign('startelement',$startelement);
if($endelement >= $countfiles)$endelement=$countfiles-1;
$smarty->assign('endelement', $endelement);
$smarty->assign('pagecount',$pagecount);
$smarty->assign('oftext',$this->Lang('prompt_of'));
$smarty->assign('pagetext',$this->Lang('prompt_page'));

if ($startelement < 0 || $startelement > $endelement) {
  //echo "Invalid indexes startelement.";
} else {
  // Slice the array to extract elements between the indexes
  
  $selectedItems = array_slice($data->items, $startelement, $endelement - $startelement + 1);

  if($up_dirurl){
    $onerow = new \stdClass();
    $onerow->url = $up_dirurl;
    $onerow->url_link = $up_dirlink;
    $onerow->icon_link = $onerow->presigned_icon_link = $up_diriconlink;
    $onerow->type = array('dir');
    $onerow->noCheckbox = 1;
    $entryarray[]= $onerow;
  }
  foreach( $selectedItems as $row ) {
      $onerow = $row;
      if($onerow->dir){
          $sendtodetail = array('key'=>$onerow->key);
          if( isset( $params['pagelimit'] ) ) {
              $sendtodetail = array_merge($sendtodetail,array('pagelimit'=>$pagelimit));
          }
          $onerow->url = $this->create_url('m1_','defaultadmin','',$sendtodetail);
          //<a href="{$file->presigned_url}" target="_blank">{admin_icon icon='permissions.gif' alt='view_page'|lang}</a>
          $onerow->presigned_link = "";
          $onerow->url_link = "<a href='" . $onerow->url . "' class=\"dirlink\" title=\"{$FileManager->Lang('title_changedir')}\">" . $onerow->name . "</a>";
          $onerow->icon_link = $onerow->presigned_icon_link = "<a href='" . $onerow->url . "' class=\"dirlink\" title=\"{$FileManager->Lang('title_changedir')}\">".$row->icon."</a>";
      }
      $entryarray[]= $onerow;
  }
}

$smarty->assign('startdate', $data->date);
$smarty->assign('enddate', $enddate);
$smarty->assign('cachetime', $nminutes);
$smarty->assign('path_parts',$path_parts);

if( isset($params['noform']) ) $smarty->assign('noform',1);
$smarty->assign('refresh_url',$this->Create_url($id,'admin_fileview','',array('ajax'=>1,'bucket_id'=>$bucket_id,'prefix'=>$prefix)));
$smarty->assign('FileManager',$FileManager);
$smarty->assign('items', $entryarray);

echo $this->ProcessTemplate('filemanager.tpl');



?>
