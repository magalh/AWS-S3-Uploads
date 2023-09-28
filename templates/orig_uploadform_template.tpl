<!-- Upload form template -->
<div id="uploadform">
{$startform}
  {if $good_upload}
     <div class="alert alert-info">{$mod->Lang('successful_upload',$good_upload)}</div>
  {/if}

  {* optional {$actionid}input_author field (default will be provided if empty) *}
  <div class="row">
    <p class="col-sm-3 text-right"><label for="u_author">{$mod->Lang('author')}:</label></p>
    <p class="col-sm-9"><input type="text" id="u_author" name="{$actionid}input_author" value="{$author}"/></p>
  </div>

  <div class="row">
    {* this field is required *}
    <p class="col-sm-3 text-right"><label for="u_file">{$mod->Lang('upload')}:</label></p>
    <p class="col-sm-9"><input type="file" id="u_file" name="{$actionid}input_browse"/></p>
  </div>
  <div class="row">
    {* this field is optional *}
    <p class="col-sm-3 text-right"><label for="u_summary">{$mod->Lang('summary')}:</label></p>
    <p class="col-sm-9"><input type="text" id="u_summary" name="{$actionid}input_summary" value="{$summary}" size="80"/></p>
  </div>
  <div class="row">
    {* this field is optional *}
    <p class="col-sm-3 text-right"><label for="u_desc">{$mod->Lang('description')}:</label></p>
    <p class="col-sm-9"><textarea id="u_desc" name="{$actionid}input_description" cols="80" roes="5">{$description}</textarea></p>
  </div>
  {if !$use_autogen}
    {* optionally allow overwriting the destination name. even if field is present in form, and filled in, if autogen is enabled, this value is ignored. this field can also be removed all together. *}
    <div class="row">
      <p class="col-sm-3 text-right"><label for="u_destname">{$mod->Lang('destname')}:</label></p>
      <p class="col-sm-9"><input type="text" id="u_destname" name="{$actionid}input_destname" value="{$destname}" size="80"/></p>
    </div>
  {/if}
  {* optionally allow uploading a different thumbnail (deprecated) *}
  <div class="row">
    <p class="col-sm-3 text-right"><label for="u_thumbnail">{$mod->Lang('thumbnail')}:</label></p>
    <p class="col-sm-9"><input type="file" id="u_thumbnail" name="{$actionid}input_thumbnail"/></p>
  </div>
  {if isset($fields)}
    {foreach from=$fields item='one' key='name'}
    <div class="row">{strip}
      <p class="col-sm-3 text-right">{$one.name}</p>
      <p class="col-sm-9">
        {if isset($one.input)}
          {$one.input}
        {elseif $one.type == 'textinput'}
          <input type="text" name="{$actionid}field_{$one.id}" value="{$one.value}" size="{$one.attrib.length}" maxlength="{$one.attrib.maxlength}"/>
        {elseif $one.type == 'checkbox'}
          <input type="checkbox" name="{$actionid}field_{$one.id}" value="1"{if $one.value == 1} checked="checked"{/if}/>
        {elseif $one.type == 'dropdown'}
          <select name="{$actionid}field_{$one.id}">
            {html_options options=$one.attrib.options}
          </select>
        {elseif $one.type == 'multiselect'}
          <select multiple="multiple" size="4" name="{$actionid}field_{$one.id}[]">
            {html_options options=$one.attrib.options}
          </select>
        {/if}
      </p>
    </div>{/strip}
    {/foreach}
  {/if}

  {* captcha ?? *}
  {if isset($captcha)}
  <div class="row">
    <div class="col-sm-3 text-right">{$mod->Lang('captcha_title')}</div>
    <div class="col-sm-9">
       {$captcha}<br/>
       <input type="text" name="{$actionid}input_captcha" size="10" maxlength="10"/>
    </div>
  </div>
  {/if}

  <div class="row">
    <p class="col-sm-3 text-right">&nbsp;</p>
    <p class="col-sm-9">
      <input type="submit" name="{$actionid}input_submit" value="{$mod->Lang('submit')}"/>
    </p>
  </div>

{$endform}
</div>{* #uploadform *}
