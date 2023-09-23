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
          console.log(total);
          barValue(total);
          
          function barValue(total) {
            $("#progressarea").progressbar( {
              value: parseInt(total)
            });
            $(".ui-progressbar-value").html(str);
          }
        },
        done: function (e, data) {
          console.log(data.result);
          
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
          //$('#filesarea').load(refresh_url);
          $('#progressarea').fadeOut();
        }
    });
});
{/literal}</script>


<style type="text/css">
  .upload-wrapper { margin: 10px 0 }
  .hcentered { text-align: center }
  .vcentered {
    display: table-cell;
    vertical-align: middle;
  }
  #dropzone {
	 margin: 15px 0;
	 border-radius: 4px;
   background: #EEEEEE;
	}
  #dropzone.in {
    width: 600px;
    height: 200px;
    line-height: 200px;
    background: #147fdb;
    border: 2px dashed #fff;
    color: #fff;
    font-size: larger;
    cursor: move;
  }
  #dropzone.fade {
    -webkit-transition: all 0.3s ease-out;
    -moz-transition: all 0.3s ease-out;
    -ms-transition: all 0.3s ease-out;
    -o-transition: all 0.3s ease-out;
    transition: all 0.3s ease-out;
    opacity: 1;
  }
  #progressarea {
    margin: 15px;
    height: 2em;
    line-height: 2em;
    text-align: center;
    border: 1px solid #aaa;
    border-radius: 4px;
    display: none;
  }
</style>

{$formstart}
  <input type="hidden" name="disable_buffer" value="1" />
  {$hiddenpath}{$bucket_id}
  <fieldset>
    {if isset($is_ie)}
      <div class="pageerrorcontainer message">
        <p class="pageerror">{$ie_upload_message}</p>
      </div>
    {/if}
    <div class="upload-wrapper">
      <div style="width: 60%; float: left;">
        {*<input type="hidden" name="MAX_FILE_SIZE" value="{$maxfilesize}" />*}{* recommendation for browser *}
        <input id="fileupload" type="file" name="{$actionid}files[]" size="50" title="{$mod_fm->Lang('title_filefield')}" multiple/>
        <div id="pageoverflow">
          <p class="pagetext"></p>
          <p class="pageinput">
            <input id="cancel" type="submit" value="{$mod->Lang('cancel')}" style="display: none;"/>
            <input name="submit" type="submit" value="{$mod->Lang('submit')}"/>

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

