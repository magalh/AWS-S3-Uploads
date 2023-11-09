<div class="row mb-2">
<div class="col-md-6">
  <div class="row g-0 border rounded overflow-hidden flex-md-row mb-4 shadow-sm h-md-250 position-relative">
    <div class="col p-4 d-flex flex-column position-static">
    {if isset($entry->bucket)}
      <strong class="d-inline-block mb-2 text-primary">{$entry->bucket}</strong>
    {/if}
      <h3 class="mb-0">{$entry->name|cms_escape:htmlall}</h3>
      {if $entry->postdate}<div class="mb-1 text-muted">{$entry->postdate|cms_date_format}</div>{/if}
      <p class="card-text mb-auto">{$mod->Lang('label_mime')} {$entry->mime}, {$mod->Lang('label_size')} {$entry->size}</p>
      <ol class="list-unstyled mb-0">
        {if $entry->url_original}<li><a href="{$entry->url_original}">{$mod->Lang('label_url_original')}</a></li>{/if}
        {if $entry->url}<li><a href="{$entry->url}">{$mod->Lang('label_url')}</a></li>{/if}
        {if $entry->url_presigned}<li><a href="{$entry->url_presigned}">{$mod->Lang('label_url_presigned')}</a></li>{/if}
        </ol>
    </div>
    {if $entry->isimage}
    <div class="col-auto d-none d-lg-block p-0">
        <div class="bd-placeholder-img" width="200">
          <img src="{$entry->url_presigned}" width="200"/>
        </div>
    </div>
    {/if}
  </div>
</div>
</div>