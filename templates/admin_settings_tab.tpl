<h3>{$mod->Lang('getstarted')}:</h3>

{if isset($message)}{$message}{/if}

{form_start}
<div class="pageoverflow">
 <p class="pagetext">{$mod->Lang('bucket_name')}:&nbsp;{cms_help key2='help_bucket_name' title=$mod->Lang('bucket_name')}</p>
 <p class="pageinput">
    <input type="text" name="{$actionid}settings_input[bucket_name]" value="{$mod->GetOptionValue("bucket_name")}" size="50" maxlength="50"/>
 </p>
</div>
<div class="pageoverflow">
<p class="pagetext">{$mod->Lang('region')}:</p>
  <div class="pageinput">
    <select name="{$actionid}settings_input[access_region]">
      {html_options options=$access_region_list selected=$mod->GetOptionValue("access_region")}
    </select>
  </div>
</div>
<div class="pageoverflow">
    <p class="pagetext">{$mod->Lang('allowed')}:&nbsp;{cms_help key2='fielddef_allow_help' title=$mod->Lang('allowed')}</p>
    <p class="pageinput">
	  <input type="text" name="{$actionid}settings_input[allowed]" value="{$mod->GetOptionValue('allowed', 'pdf,doc,docx,xls,xlsx,jpg,png,jpeg,gif')}" size="50" maxlength="200"/>
  </p>
</div>
<div class="pageoverflow">
		<p class="pagetext"><label for="fld5">{$mod->Lang('expiry_interval')}:</label> {cms_help key='help_opt_expiry_interval' title=$mod->Lang('expiry_interval')}</p>
		<p class="pageinput">
        <input type="text" name="{$actionid}settings_input[expiry_interval]" value="{$mod->GetOptionValue("expiry_interval")|default:60}" size="4" maxlength="4"/>
      </p>
	</div>
<fieldset>
  <legend>{$mod->Lang('prompt_custom_url')}</legend>
  <div class="pageoverflow">
  <p class="pageinput">
        <select name="{$actionid}settings_input[use_custom_url]">
            {cms_yesno selected=$mod->GetOptionValue("use_custom_url")}
        </select>
        <br/>
        {$mod->Lang('help_use_custom_url')}
    </p>
  </div>
  <div class="pageoverflow">
  <p class="pagetext">{$mod->Lang('prompt_custom_url')}:&nbsp;{cms_help key2='help_custom_url' title=$mod->Lang('custom_url')}</p>
  <p class="pageinput">
      <input type="text" name="{$actionid}settings_input[custom_url]" value="{$mod->GetOptionValue("custom_url")}" size="50" maxlength="200"/>
  </p>
  </div>
</fieldset>

<fieldset>
	<legend>{$mod->Lang('default_options')}</legend>
	<div class="pageoverflow">
        <p class="pagetext">{$mod->Lang('prompt_detailpage')}:</p>
        <p class="pageinput">{$input_detailpage}</p>
    </div> 
	<div class="pageoverflow">
        <p class="pagetext">{$mod->Lang('prompt_summarypage')}:</p>
        <p class="pageinput">{$input_summarypage}</p>
    </div> 	
</fieldset>

<div class="pageoverflow">
 <p class="pageinput">
<input type="submit" name="{$actionid}submit" value="{$mod->Lang('admin_save')}"/>
 </p>
</div>
{form_end}
{if $sdk_mod->is_developer_mode()}
  {*get_template_vars*}
{/if}
