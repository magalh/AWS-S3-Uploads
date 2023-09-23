<h3>{$mod_fm->Lang('actiondelete')}:</h3>
{assign var='cancellabel' value=$mod_fm->Lang('cancel')}
{if isset($errors)}
{assign var='cancellabel' value=$mod_fm->Lang('return')}
{/if}


{*cms_help key2='settings_nosefurl' title=lang('warn_nosefurl')*}

{if $hasdir}
  <div class="warning" style="display: block;">{$mod->lang('warn_delete_directory')}</div>
{/if}

{$startform}
<div class="pageoverflow">
  <p class="pagetext">{$mod_fm->Lang('deleteselected')}:</p>
  <p class="pageinput">
    {'<br/>'|implode:$selall}
  </p>
</div>
<div class="pageoverflow">
  <p class="pagetext"></p>
  <p class="pageinput">
    {if !isset($errors)}
    <input type="submit" name="{$actionid}submit" value="{$mod_fm->Lang('delete')}"/>
    {/if}
    <input type="submit" name="{$actionid}cancel" value="{$cancellabel}"/>
  </p>
</div>
{$endform}

{if $mod->is_developer_mode()}
  {get_template_vars}
{/if}