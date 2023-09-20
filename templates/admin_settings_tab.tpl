<h3>{$mod->Lang('getstarted')}:</h3>

{if isset($errormsg) && $errormsg != ""}
	<div class="pageerrorcontainer">{$errormsg}</div>
{/if}
{if isset($aws_error_msg) && $aws_error_msg != ""}
	<div class="error">{$aws_error_msg}</div>
{/if}

{form_start}
<div class="pageoverflow">
 <p class="pagetext">{$mod->Lang('s3_bucket_name')}:</p>
 <p class="pageinput">
    <input type="text" name="{$actionid}s3_bucket_name" value="{$s3_bucket_name}" size="50" maxlength="50"/>
 </p>
</div>
<div class="pageoverflow">
<p class="pagetext">{$mod->Lang('region')}: {$s3_region}</p>
  <div class="pageinput">
    <select name="{$actionid}s3_region">
      {html_options options=$s3_region_list selected=$s3_region}
    </select>
  </div>
</div>
<div class="pageoverflow">
 <p class="pagetext">{$mod->Lang('s3_uploads_secret')}:</p>
 <p class="pageinput">
    <input type="text" name="{$actionid}s3_uploads_secret" value="{$s3_uploads_secret}" size="100" maxlength="255"/>
 </p>
</div>
<div class="pageoverflow">
 <p class="pagetext">{$mod->Lang('s3_uploads_key')}:</p>
 <p class="pageinput">
    <input type="text" name="{$actionid}s3_uploads_key" value="{$s3_uploads_key}" size="100" maxlength="255"/>
 </p>
</div>
<div class="pageoverflow">
 <p class="pageinput">
        <input type="submit" name="{$actionid}submit" value="{$mod->Lang('admin_save')}"/>
 </p>
</div>
{form_end}
{if $mod->is_developer_mode()}
  {get_template_vars}
{/if}
