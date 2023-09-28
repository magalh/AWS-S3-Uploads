<!-- Start Upload Detail template -->
<table>
  <tr>
    {if isset($entry->thumbnail_url)}
    <td>{$mod->Lang('thumbnail')}</td>
    <td><img src="{$entry->thumbnail_url}" border="0" /></td>
    {else}
    <td>{$mod->Lang('icon')}</td>
    <td><img src="{$entry->iconurl}" border="0" /></td>
    {/if}
  </tr>
  <tr>
    <td>{$mod->Lang('category')}</td>
    <td>{$entry->category}</td>
  </tr>
  <tr>
    <td>{$mod->Lang('id')}</td>
    <td>{$entry->id}</td>
  </tr>

  {if isset($entry->download_url)}
  <tr>
    <td>{$mod->Lang('name')}</td>
    <td><a href="{$entry->download_url}" title="{$entry->name}">{$entry->name}</a>&nbsp;&nbsp;
        <a href="{$entry->sendfile_url}" title="">Send this file</a><br/>
    </td>
  </tr>
  {/if}

  {if isset($entry->delete_url)}
  <tr>
    <td>{$mod->Lang('delete')}</td>
    <td><a href="{$entry->delete_url}" title="{$mod->Lang('delete')}" onclick="return confirm('{$mod->Lang('areyousure')}');">{$entry->name}</a></td>
  </tr>
  {/if}
  <tr>
    <td>{$mod->Lang('date')}</td>
    <td>{$entry->date|cms_date_format}</td>
  </tr>
  <tr>
    <td>{$mod->Lang('author')}</td>
    <td>{$entry->author|default:''}</td>
  </tr>
  <tr>
    <td>{$mod->Lang('size')}</td>
    <td>{$entry->size}</td>
  </tr>
  <tr>
    <td>{$mod->Lang('summary')}</td>
    <td>{$entry->summary|default:''}</td>
  </tr>
  <tr>
    <td>{$mod->Lang('description')}</td>
    <td>{$entry->description|default:''}</td>
  </tr>

  {if isset($entry->fields)}
    {foreach name=fields from=$entry->fields key='fldname' item='field'}
    <tr>
      <td>{$field.name}</td>
      <td>{$field.value}</td>
    </tr>
    {/foreach}
  {/if}
</table>
<!-- End Upload Detail template -->
