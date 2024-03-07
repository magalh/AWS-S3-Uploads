{if !isset($ajax)}
<style type="text/css">
a.filelink:visited {
   color: #000;
}
</style>
<script type="text/javascript">
var refresh_url = '{$refresh_url}'+'&showtemplate=false';
refresh_url = refresh_url.replace(/amp;/g,'');

// <![CDATA[
function enable_button(idlist) {
  $(idlist).removeAttr('disabled').removeClass('ui-state-disabled ui-button-disabled');
}
function disable_button(idlist) {
  $(idlist).attr('disabled','disabled').addClass('ui-state-disabled ui-button-disabled');
}

function enable_action_buttons() {

    var files = $("#filesarea input[type='checkbox'].fileselect").filter(':checked').length,
        dirs = $("#filesarea input[type='checkbox'].dir").filter(':checked').length,
        arch = $("#filesarea input[type='checkbox'].archive").filter(':checked').length,
        text = $("#filesarea input[type='checkbox'].text").filter(':checked').length,
        imgs = $("#filesarea input[type='checkbox'].image").filter(':checked').length;

    disable_button('button.filebtn');
    $('button.filebtn').attr('disabled','disabled');
    if (files == 0 && dirs == 0) {
        // nothing selected, enable anything with select_none
        enable_button('#btn_refresh');
    } else if (files == 1) {
        // 1 selected, enable anything with select_one
        enable_button('#btn_delete');

        if (dirs == 0) enable_button('#btn_copy');
        if (arch == 1) enable_button('#btn_unpack');
        if (imgs == 1) enable_button('#btn_view,#btn_thumb,#btn_resizecrop,#btn_rotate');
        if (text == 1) enable_button('#btn_view');
    } else if (files > 1 && dirs == 0) {
        // multiple files selected
        enable_button('#btn_delete,#btn_copy,#btn_move');
    } else if (files > 1 && dirs > 0) {
        // multiple selected, at least one dir.
        enable_button('#btn_delete,#btn_move');
    }
}

$(document).ready(function () {
    enable_action_buttons();

    $('#refresh').unbind('click');
    $('#refresh').bind('click', function () {
        // ajaxy reload for the files area.
        $('#filesarea').load(refresh_url);
        return false;
    });

    $(document).on('dropzone_chdir', $(this), function (e, data) {
        // if change dir via the dropzone, make sure filemanager refreshes.
        location.reload();
    });
    $(document).on('dropzone_stop', $(this), function (e, data) {
        // if change dir via the dropzone, make sure filemanager refreshes.
        location.reload();
    });

    $(document).on('change', '#filesarea input[type="checkbox"].fileselect', function (e) {
        // find the parent row
        e.stopPropagation();
        var t = $(this).attr('checked');
        if (t) {
            $(this).closest('tr').addClass('selected');
        } else {
            $(this).closest('tr').removeClass('selected');
        }
        enable_action_buttons();
    });

    $(document).on('change', '#tagall', function (event) {
        if ($(this).is(':checked')) {
            $('#filesarea input:checkbox.fileselect').attr('checked', true).trigger('change');
        } else {
            $('#filesarea input:checkbox.fileselect').attr('checked', false).trigger('change');
        }
    });

    $(document).on('click', '#btn_view', function () {
        // find the selected item.
        var tmp = $("#filesarea input[type='checkbox']").filter(':checked').val();
        var url = '{$viewfile_url}&showtemplate=false&{$actionid}viewfile=' + tmp;
        url = url.replace(/amp;/g, '');
        $('#popup_contents').load(url);
        $('#popup').dialog({
       	  minWidth: 380,
          maxHeight: 600
        });
        return false;
    });

    $(document).on('click', 'td.clickable', function () {
        var t = $(this).parent().find(':checkbox').attr('checked');
        if (t != 'checked') {
            $(this).parent().find(':checkbox').attr('checked', true).trigger('change');
        } else {
            $(this).parent().find(':checkbox').attr('checked', false).trigger('change');
        }
    });
});
// ]]>
</script>

<h3>{$FileManager->Lang('currentpath')}
   <span class="pathselector">{$up_home} / 
   {foreach $path_parts as $part}
     {if !empty($part->url)}
       <a href="{$part->url}">{$part->name}</a>
     {else}
       {$part->name}
     {/if}
     {if !$part@last}<span class="ds">/</span>{/if}
   {/foreach}
   </span>
</h3>

{function filebtn icon='ui-icon-circle-check'}
{$addclass='ui-button-icon-primary'}
{if isset($text) && $text != ''}
  {$addclass='ui-button-text-icon-primary'}
  {if !isset($title) || $title == ''}{$title=$text}{/if}
{/if}
<button type="submit" name="{$iname}" id="{$id}" title="{$title|default:''}" class="filebtn ui-button ui-widget ui-state-default ui-corner-all {$addclass}">
  <span class="ui-icon ui-button-icon-primary {$icon}"></span>
  {if isset($text) && $text != ''}<span class="ui-button-text">{$text}</span>{/if}
</button>
{/function}

<div>
  {$formstart}
<div>
	<fieldset id="navButtons">
      {*filebtn id='btn_newdir' iname="{$actionid}fileactionnewdir" icon='ui-icon-circle-plus' text=$FileManager->Lang('newdir') title=$FileManager->Lang('title_newdir')*}
      {*filebtn id='btn_view' iname="{$actionid}fileactionview" icon='ui-icon-circle-zoomin' text=$FileManager->Lang('view') title=$FileManager->Lang('title_view')*}
		{filebtn id='btn_delete' iname="{$actionid}fileactiondelete" icon='ui-icon-trash' text=$FileManager->Lang('delete') title=$FileManager->Lang('title_delete')}
    {filebtn id='btn_refresh' iname="{$actionid}fileactionrefresh" icon='ui-icon-refresh' text=$FileManager->Lang('refresh') title=$FileManager->Lang('title_refresh')}
    &nbsp;{$mod->Lang('last_refreshed')}&nbsp;{$lastupdate|localedate_format:'j %h Y H:i:s'}
	</fieldset>
</div>
{$hiddenpath}
{/if}

<div id="filesarea">
	<table width="100%" class="pagetable scrollable">
		<thead>
			<tr>
				<th class="pageicon">&nbsp;</th>
        <th class="pageicon">&nbsp;</th>
				<th>{$FileManager->Lang("filename")}</th>
				<th>{$FileManager->Lang('mimetype')}</th>
				<th class="pageicon" title="{$FileManager->Lang('title_col_filesize')}" style="text-align:right;">{$FileManager->Lang("filesize")}</th>
				<th class="pageicon" title="{$FileManager->Lang('title_col_filedate')}">{$FileManager->Lang("filedate")}</th>
                <th class="pageicon"></th>
				<th class="pageicon">
					<input type="checkbox" name="tagall" value="tagall" id="tagall" title="{$FileManager->Lang('title_tagall')}"/>
				</th>
			</tr>
		</thead>
		<tbody>
		{foreach from=$items item=file}
			{cycle values="row1,row2" assign=rowclass}
			<tr class="{$rowclass}">
				<td valign="middle">{$file->icon_link}</td>
    <td class="clickable" valign="middle">{if !$file->dir}<a href="{$file->presigned_url}" target="_blank">{admin_icon icon='permissions.gif' alt='view_page'|lang}</a>{/if}</td>
				<td class="clickable" valign="middle">{$file->url_link}</td>
				<td class="clickable" valign="middle">{$file->mime}</td>
				<td class="clickable" style="padding-right:8px;white-space:pre;text-align:right;" valign="middle">{*s3_utils::formatBytes($file->size)*}</td>
                <td class="clickable" style="padding-right:8px;white-space:pre;" valign="middle">{$file->date|cms_date_format}</td>
				<td>{$file->openlink} {*admin_icon icon='view.gif' alt="Open"*}</td>
				<td>
				{if !isset($file->noCheckbox)}
					<label for="x_{$file->urlname}" style="display: none;">{$FileManager->Lang('toggle')}</label>
					<input type="checkbox" title="{$FileManager->Lang('toggle')}" id="x_{$file->name}" name="{$actionid}selall[]" value="{$file->key}" class="fileselect {implode(' ',$file->type)}" {if isset($file->checked)}checked="checked"{/if}/>
				{/if}
				</td>
			</tr>
		{/foreach}
		</tbody>
		<tfoot>
			<tr>
				<td colspan="6">&nbsp;{$countstext}</td>
			</tr>
		</tfoot>
	</table>
</div>
{if !isset($ajax)}
	{*{$actiondropdown}{$targetdir}{$okinput}*}
	{$formend}
</div>
{/if}

<div class="row c_full">
  {if $itemcount > 0 && $pagecount > 1}
    <div class="pageoptions grid_12" style="text-align: right;">
      {form_start}
      {$mod->Lang('prompt_page')}&nbsp;
      <select name="{$actionid}pagenumber">
        {cms_pageoptions numpages=$pagecount curpage=$pagenumber}
      </select>&nbsp;
      <input type="submit" name="{$actionid}paginate" value="{$mod->Lang('prompt_go')}"/>
      <input type="hidden" name="{$actionid}prefix" value="{$path}"/>
      {form_end}
    </div>
  {/if}
</div>{* .row *}

{*get_template_vars*}