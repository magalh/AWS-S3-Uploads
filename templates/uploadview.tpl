<script>{literal}
$(function () {
    $('#fileupload').fileupload({
        dataType: 'json',
        start: function(e,data) {
          $('#progressarea').show();
        },
        progressall: function(e, data) {
          // overall progress callback
          var perc = (data.loaded / data.total * 100).toFixed(2);
          var total = null;
          total = (data.loaded / data.total * 100).toFixed(0);
          var str = perc + ' %';
          //console.log(total);
          barValue(total);
          
          function barValue(total) {
            $("#progressarea").progressbar( {
              value: parseInt(total)
            });
            $(".ui-progressbar-value").html(str);
          }
        },
        done: function (e, data) {
            var filesloop=[];
            if(data.result.files != undefined){
              $.each(data.result.files, function (index, file) {
                if(file.error){
                  filesloop.push('<p>'+file.name + " - " + file.error+'</p>');
                }
              });
            }

            if (filesloop.length) {
              var $response = $('<aside/>').addClass('message');

                $response.addClass('pageerrorcontainer')
                    .append($('<span>').text('Close').addClass('close-warning'))
                    .append(filesloop.join(''));
           
              $('body').append($response).slideDown(1000, function() {
                  window.setTimeout(function() {
                      $response.slideUp();
                      $response.remove();
                  }, 10000);
              });
            }

        },
        stop: function(e, data) {
          $('#filesarea').load(refresh_url);
          $('#progressarea').fadeOut();
        }
    });
});
{/literal}</script>
{$formstart}
  <input type="hidden" name="disable_buffer" value="1" />
  {$hiddenpath}
  <fieldset>
    <div class="upload-wrapper">
      <div style="width: 60%; float: left;">
        {*<input type="hidden" name="MAX_FILE_SIZE" value="{$maxfilesize}" />*}{* recommendation for browser *}
        <input id="fileupload" type="file" name="{$actionid}files[]" size="50" title="{$FileManager->Lang('title_filefield')}" multiple/>
        <div id="pageoverflow">
          <p class="pagetext"></p>
          <p class="pageinput">
            <input id="cancel" type="submit" value="{$mod->Lang('cancel')}" style="display: none;"/>
            {*<input name="submit" type="submit" value="{$mod->Lang('submit')}"/>*}
          </p>
        </div>
      </div>
      <div id="leftcol" style="height: 4em; width: 40%; float: left; display: table;">
        {if !isset($is_ie)}
          <div id="dropzone" class="vcentered hcentered fade" title="{$mod->Lang('title_dropzone')}"><p id="dropzonetext">{$mod->Lang('prompt_dropfiles')}</p></div>
        {/if}
      </div>
      <div class="clearb"></div>
      <div id="progressarea"></div>
    </div>
  </fieldset>
{$formend}

