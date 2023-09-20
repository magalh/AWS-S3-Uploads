<?php
namespace AWS_S3_Uploads;
if( !$this->CheckPermission($this::MANAGE_PERM) ) return;

$this->SetCurrentTab($bucket_id);
$mod_fm = \cms_utils::get_module('FileManager');
$path='';
//print_r($this->s3Client);

if( isset($params["newdir"])) {
  $path = $params["newdir"];
}

$filelist=utils::get_file_list($bucket_id,$path);

$config = $gCms->GetConfig();
$smarty->assign('path', $path);
$smarty->assign('hiddenpath', $this->CreateInputHidden($id, "path", $path));
$smarty->assign('formstart', $this->CreateFormStart($id, 'fileaction', $returnid));

$themeObject = \cms_utils::get_theme_object();
$titlelink = $mod_fm->Lang("filename");
$newsort = "";
if ($sortby == "nameasc") {
  $newsort = "namedesc";
  $titlelink .= "+";
} else {
  $newsort = "nameasc";
  if ($sortby == "namedesc") $titlelink.="-";
}
$params["newsort"] = $newsort;
$titlelink = $this->CreateLink($id, "defaultadmin", $returnid, $titlelink, $params);
$smarty->assign('filenametext', $titlelink);
$titlelink = $mod_fm->Lang("filesize");
$titlelink = $this->CreateLink($id, "defaultadmin", $returnid, $titlelink, $params);
$smarty->assign('filesizetext', $titlelink);
$smarty->assign('fileownertext', $mod_fm->Lang("fileowner"));
$smarty->assign('filepermstext', $mod_fm->Lang("fileperms"));
$smarty->assign('fileinfotext', $mod_fm->Lang("fileinfo"));

$smarty->assign('filedatetext', $mod_fm->Lang("filedate"));
$smarty->assign('actionstext', $mod_fm->Lang("actions"));

$countdirs = 0;
$countfiles = 0;
$countfilesize = 0;
$files = array();

for ($i = 0; $i < count($filelist); $i++) {
  $onerow = new \stdClass();
  if( isset($filelist[$i]['url']) ) {
    $onerow->url = $filelist[$i]['url'];
  }
  $onerow->name = $filelist[$i]['name'];
  $onerow->urlname = $this->encodefilename($filelist[$i]['name']);
  $onerow->type = array('file');
  $onerow->mime = $filelist[$i]['mime'];
  if( isset($params[$onerow->urlname]) ) {
    $onerow->checked = true;
  }

  if( strpos($onerow->mime,'text') !== FALSE ) {
    $onerow->type[] = 'text';
  }

  if ($filelist[$i]["dir"]) {
    $urlname="dir_" . $this->encodefilename($filelist[$i]["name"]);
    $value="";
    if (isset($params[$urlname])) $value="true";
    $onerow->checkbox = $this->CreateInputCheckBox($id, $urlname , "true", $value);
  } else {
    $urlname="file_" . $this->encodefilename($filelist[$i]["name"]);
    $value="";
    if (isset($params[$urlname])) $value="true";
    $onerow->checkbox = $this->CreateInputCheckBox($id, $urlname, "true", $value);
  }

  $onerow->thumbnail = '';
  $onerow->editor = '';
  if ($filelist[$i]["image"]) {
      $onerow->type[] = 'image';
      $params['imagesrc'] = $path.'/'.$filelist[$i]['name'];
      if($this->GetPreference("showthumbnails", 0) == 1) {
          $onerow->thumbnail = $this->GetThumbnailLink($filelist[$i], $path);
      }
  }

  if ($filelist[$i]["dir"]) {
      $onerow->iconlink = $this->CreateLink($id, "defaultadmin",  "", $this->GetFileIcon($filelist[$i]["ext"], $filelist[$i]["dir"]),
                                            array("prefix" => $filelist[$i]["name"]));
  } else {
      $onerow->iconlink = "<a href='" . $filelist[$i]["url"] . "' target='_blank'>" . $this->GetFileIcon($filelist[$i]["ext"]) . "</a>";
  }

  $link = $filelist[$i]["name"];
  if ($filelist[$i]["dir"]) {
      $parms = [ 'newdir'=>$filelist[$i]['name'],'__activetab'=>$bucket_id];
      $url = $this->create_url($id,'defaultadmin','',$parms);
      if( $filelist[$i]['name'] != '..' ) {
          $countdirs++;
          $onerow->type = array('dir');
          $onerow->txtlink = "<a class=\"dirlink\" href=\"{$url}\" title=\"{$mod_fm->Lang('title_changedir')}\">{$link}</a>";
      } else {
          // for the parent directory
          $onerow->noCheckbox = 1;
          $icon = $mod_fm->GetModuleURLPath().'/icons/themes/default/actions/dir_up.gif';
          $img_tag = '<img src="'.$icon.'" width="32" title="'.$mod_fm->Lang('title_changeupdir').'"/>';
          $onerow->iconlink = $this->CreateLink($id,'defaultadmin', '', $img_tag, $parms );
          $onerow->txtlink = "<a class=\"dirlink\" href=\"{$url}\" title=\"{$mod_fm->Lang('title_changeupdir')}\">{$link}</a>";
      }
  } else {
      $countfiles++;
      $countfilesize+=$filelist[$i]["size"];
      //$url = $this->create_url($id,'view','',array('file'=>$this->encodefilename($filelist[$i]['name'])));
      $url = $onerow->url;
      //$onerow->txtlink = "<a href='" . $filelist[$i]["url"] . "' target='_blank' title=\"".$mod_fm->Lang('title_view_newwindow')."\">" . $link . "</a>";
      $onerow->txtlink = "<a class=\"filelink\" href='" . $url . "' target='_blank' title=\"".$mod_fm->Lang('title_view_newwindow')."\">" . $link . "</a>";
  }
  if( $filelist[$i]['archive']  ) $onerow->type[] = 'archive';

  if ($filelist[$i]["dir"]) {
    $onerow->filesize = "&nbsp;";
  } else {
    $filesize = '';
    $onerow->filesize = $filelist[$i]["size"];
    $onerow->filesizeunit = '';
  }

  if (!$filelist[$i]["dir"]) {
    $onerow->filedate = $filelist[$i]["date"];
  } else {
    $onerow->filedate = '';
  }

  $files[] = $onerow;
}

// build display
$smarty->assign('files', $files);
$smarty->assign('itemcount', count($files));
$totalsize = $countfilesize;
$counts = $totalsize["size"] . " " . $totalsize["unit"] . " " . $mod_fm->Lang("in") . " " . $countfiles . " ";
if ($countfiles == 1) $counts.=$mod_fm->Lang("file"); else $counts.=$mod_fm->Lang("files");
$counts.=" " . $mod_fm->Lang("and") . " " . $countdirs . " ";
if ($countdirs == 1) $counts.=$mod_fm->Lang("subdir"); else $counts.=$mod_fm->Lang("subdirs");
$smarty->assign('countstext', $counts);
$smarty->assign('formend', $this->CreateFormEnd());

if( isset($params['noform']) ) $smarty->assign('noform',1);
$smarty->assign('refresh_url',$this->Create_url($id,'admin_fileview','',array('noform'=>1)));
$smarty->assign('viewfile_url',$this->create_url($id,'admin_fileview','',array('ajax'=>1)));
$smarty->assign('mod_fm',$mod_fm);
$smarty->assign('confirm_unpack',$mod_fm->Lang('confirm_unpack'));

$tmp_path_parts = explode('/',$path);
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
        $obj->url = $this->create_url( $id, 'defaultadmin', '',[ 'newdir' => $fullpath,'__activetab'=>$bucket_id] );
    } else {
        // the last entry... no link
    }
    $path_parts[] = $obj;
}
$smarty->assign('path',$path);
$smarty->assign('path_parts',$path_parts);

echo $this->ProcessTemplate('filemanager.tpl');

?>
