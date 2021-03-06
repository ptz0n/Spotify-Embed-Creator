<?php
  // get the plugin base url
  $pluginRoot = plugins_url('', __DIR__);
  $sec = new SpotifyEmbedCreator();
  wp_enqueue_script('SpotifyEmbedCreator');
?>  
  <div id="spotify-container">
  <select id="choose-editor"></select>
    <table class="wp-list-table widefat users" cellspacing="0">
      <thead>
        <tr>
          <th scope="col" id="role" class="manage-column column-role" style="display:none;">Search Artist</th>
          <th scope="col" id="role" class="manage-column column-role" style="">Search Album</th>
          <th scope="col" id="role" class="manage-column column-role" style="">Search Låt</th>
          
          <th scope="col" id="role" class="manage-column column-role" style="">Width</th>
          <th scope="col" id="role" class="manage-column column-role" style="">Height</th>          
        </tr>
      </thead>
      <tbody>
        <tr>
          <th scope="col" id="role" class="manage-column column-role" style="display:none;">
          	<input type="text" id="artist-search" />
          	<input type="button" id="artist-do-search" value="Search" />
          </th>
          <th scope="col" id="role" class="manage-column column-role" style="">
          	<input type="text" id="album-search" />          
          	<input type="button" id="album-do-search" value="Search" />          	
          </th>
          <th scope="col" id="role" class="manage-column column-role" style="">
          	<input type="text" id="song-search" />          
          	<input type="button" id="song-do-search" value="Search" />          	
          </th>
          
          <th scope="col" id="role" class="manage-column column-role" style="">
          	<input type="text" id="iframe-width" value="300" />             
		  </td>
          <th scope="col" id="role" class="manage-column column-role" style="">
<!--           	<input type="text" id="iframe-height" value="380" />              -->
				<label>Compact</label>
				<input type="checkbox"id="compact" />
		  </td>          
      </tbody>
      </table>
  </div>

  <div id="spotify-result-container" style="float:left;width:50%;padding-right:20px;">
  </div>
  <div style="float:left; width: 47%;">
	  <div id="codeboxes" style="display:none;">
		  <h3>iFrame code</h3>
		  <textarea id="iframe-code" cols="100" rows="4"></textarea>
		  <h3>Shortcode</h3>
		  <textarea id="shortcode-code" cols="100" rows="4"></textarea>  
  	  </div>  
	  <div id="spotify-preview-container" style="float:left;"></div>  
  </div>

  <div class="clear"></div>
  <script type="text/javascript">
jQuery(document).ready(function() {
    setTimeout(load_select, 1000);
    
  jQuery('#artist-do-search').bind('click', function(event) {
  	var query = jQuery('#artist-search').val();
  	console.log("Ska söka artist: " + query);
  	search_spotify("artist", query);
  });
  jQuery('#album-do-search').bind('click', function(event) {
  	var query = jQuery('#album-search').val();
  	console.log("Ska söka album: " + query);
  	search_spotify("album", query);
  });
  jQuery('#song-do-search').bind('click', function(event) {
  	var query = jQuery('#song-search').val();
  	console.log("Ska söka låt: " + query);
  	search_spotify("track", query);
  });    
});

function load_select()
{
    var counter = 0;
    jQuery(tinyMCE.editors).each(function(){
        jQuery('#choose-editor').html( jQuery('#choose-editor').html() + "<option value='"+ counter +"'>"+this.editorId+"</option>" );
        counter++;
    });    
}

function search_spotify(type, query)
{
	jQuery("#spotify-result-container").html("Searching...");
	jQuery("#spotify-preview-container").hide();
	jQuery("#codeboxes").hide();
            jQuery.ajax({
                type: "POST",
                url: "<?php echo $pluginRoot ?>/Api/Spotify-Request-handler.php",
                async: true,
                timeout: 50000,
                data: { searchtype: type, searchquery: query },
                success: function(data) {
                	console.log("lyckades");
                	var html = '<ul class="result-list">';
                	if(type == "artist")
                	{
                		for(var i = 0; i < data.artists.length; ++i)
                		{
                			//html += '<li>'+ data.artists[i].name +' - <input type="button" onClick="get_iframe_code(\''+data.artists[i].href+'\');" value="Skapa iframe"/></li>';
                		}
                	}
                	if(type == "album")
                	{
                		for(var i = 0; i < data.albums.length; ++i)
                		{
                			//html += '<li>'+ data.albums[i].artists[0].name + ' - ' + data.albums[i].name +' - <input type="button" onClick="get_iframe_code(\''+data.albums[i].href+'\');" value="Skapa iframe"/></li>';
                			html += '<li><a href="#" onClick="get_iframe_code(\''+data.albums[i].href+'\');">'+ data.albums[i].artists[0].name + ' - ' + data.albums[i].name +'</a></li>';
                		}                	
                	}
                	if(type == "track")
                	{
                		for(var i = 0; i < data.tracks.length; ++i)
                		{
                			//html += '<li>'+ data.tracks[i].artists[0].name + ' - ' + data.tracks[i].name +' - <input type="button" onclick="get_iframe_code(\''+data.tracks[i].href+'\');" value="Skapa iframe"/></li>';
                			html += '<li><a href="#" onclick="get_iframe_code(\''+data.tracks[i].href+'\');">' + data.tracks[i].artists[0].name + ' - ' + data.tracks[i].name +'</a></li>';
                		}                	
                	}
                	html += '</ul>';
                    jQuery("#spotify-result-container").html(html);
                },
                error: function(data) {
  		            console.log("misslyckades");
                    alert("Gick dåligt");
                }
            });	 	
}

function get_iframe_code(href)
{
	var iframewidth = jQuery("#iframe-width").val();
//	var iframeheight = jQuery("#iframe-height").val();	
	var compact = jQuery("#compact").is(":checked") == true ? "80" : parseInt(iframewidth)+80;
	var iframehtml = '<iframe src="https://embed.spotify.com/?uri='+href+'" width="'+ iframewidth +'" height="'+ compact +'" frameborder="0" allowtransparency="true"></iframe>';
	var sizetype = compact == "80" ? "compact" : "width";
	var shortcodehtml = '[spotify play="'+href+'" size="'+iframewidth+'" sizetype="'+sizetype+'"]';
	//jQuery("#iframe-code").val(iframehtml);
	//jQuery("#shortcode-code").val(shortcodehtml);
	//jQuery("#codeboxes").show();
	//jQuery("#spotify-preview-container").html(iframehtml);
	//jQuery("#spotify-preview-container").show();
	//location.href = "#spotify-container";
    tinyMCE.editors[0].setContent( tinyMCE.editors[0].getContent() + shortcodehtml );
}
  </script>