
<div class="d-md-flex flex-md-row-reverse align-items-center justify-content-between">
  <div class="mb-3 mb-md-0 d-flex text-nowrap">
  <small class="d-inline-flex px-2 py-1 fw-semibold text-success-emphasis bg-success-subtle border border-success-subtle rounded-2 me-2">Last Refreshed {$startdate|localedate_format:'j %h Y H:i:s'}</small>
  </div>
  <h1 class="bd-title mb-0" id="content">{$bucket}</h1>
</div>
         
  <nav aria-label="breadcrumb">
  <ol class="breadcrumb">
  <li class="breadcrumb-item active" aria-current="page"><a href="{cms_action_url}">Home</a></li>
  {foreach $path_parts as $part}
    <li class="breadcrumb-item">
    {if !empty($part->url)}
      <a href="{$part->url}">{$part->name}</a>
    {else}
      {$part->name}
    {/if}
    </li>
  {/foreach}
  </ol>
</nav>

<div class="card">
<ul class="list-group list-group-flush">
{if $prefix && $prefix != ''}
  <li class="list-group-item">{$diriconlink}</li>
{/if}
{foreach from=$items item=entry}
  {if $entry->dir}
    <li class="list-group-item"><a href="{cms_action_url newdir=$entry->key}" class="card-link">{$entry->icon} {$entry->name}</a></li>
  {else}
    <li class="list-group-item">{$entry->presigned_icon_link} {$entry->presigned_link}</li>
  {/if}
{/foreach}
</ul>
</div>

{if $pagecount > 1}
  <p>
{if $pagenumber > 1}
{$firstpage}&nbsp;{$prevpage}&nbsp;
{/if}
{$pagetext}&nbsp;{$pagenumber}&nbsp;{$oftext}&nbsp;{$pagecount}
{if $pagenumber < $pagecount}
&nbsp;{$nextpage}&nbsp;{$lastpage}
{/if}
</p>
{/if}
<!-- End AWSS3 Display Template -->
{get_template_vars}