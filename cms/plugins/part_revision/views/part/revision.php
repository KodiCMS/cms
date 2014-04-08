<script>
function htmlspecialchars_decode (string, quote_style) {
  // http://kevin.vanzonneveld.net
  // +   original by: Mirek Slugen
  // +   improved by: Kevin van Zonneveld (http://kevin.vanzonneveld.net)
  // +   bugfixed by: Mateusz "loonquawl" Zalega
  // +      input by: ReverseSyntax
  // +      input by: Slawomir Kaniecki
  // +      input by: Scott Cariss
  // +      input by: Francois
  // +   bugfixed by: Onno Marsman
  // +    revised by: Kevin van Zonneveld (http://kevin.vanzonneveld.net)
  // +   bugfixed by: Brett Zamir (http://brett-zamir.me)
  // +      input by: Ratheous
  // +      input by: Mailfaker (http://www.weedem.fr/)
  // +      reimplemented by: Brett Zamir (http://brett-zamir.me)
  // +    bugfixed by: Brett Zamir (http://brett-zamir.me)
  // *     example 1: htmlspecialchars_decode("<p>this -&gt; &quot;</p>", 'ENT_NOQUOTES');
  // *     returns 1: '<p>this -> &quot;</p>'
  // *     example 2: htmlspecialchars_decode("&amp;quot;");
  // *     returns 2: '&quot;'
  var optTemp = 0,
    i = 0,
    noquotes = false;
  if (typeof quote_style === 'undefined') {
    quote_style = 2;
  }
  string = string.toString().replace(/&lt;/g, '<').replace(/&gt;/g, '>');
  var OPTS = {
    'ENT_NOQUOTES': 0,
    'ENT_HTML_QUOTE_SINGLE': 1,
    'ENT_HTML_QUOTE_DOUBLE': 2,
    'ENT_COMPAT': 2,
    'ENT_QUOTES': 3,
    'ENT_IGNORE': 4
  };
  if (quote_style === 0) {
    noquotes = true;
  }
  if (typeof quote_style !== 'number') { // Allow for a single string or an array of string flags
    quote_style = [].concat(quote_style);
    for (i = 0; i < quote_style.length; i++) {
      // Resolve string input to bitwise e.g. 'PATHINFO_EXTENSION' becomes 4
      if (OPTS[quote_style[i]] === 0) {
        noquotes = true;
      } else if (OPTS[quote_style[i]]) {
        optTemp = optTemp | OPTS[quote_style[i]];
      }
    }
    quote_style = optTemp;
  }
  if (quote_style & OPTS.ENT_HTML_QUOTE_SINGLE) {
    string = string.replace(/&#0*39;/g, "'"); // PHP doesn't currently escape if more than one 0, but it should
    // string = string.replace(/&apos;|&#x0*27;/g, "'"); // This would also be useful here, but not a part of PHP
  }
  if (!noquotes) {
    string = string.replace(/&quot;/g, '"');
  }
  // Put this in last place to avoid escape being double-decoded
  string = string.replace(/&amp;/g, '&');

  return string;
}

$(function() {
	var text1 = $('#text').html();
	
	$('.btn-clear').on('click', function() {
		$('#text').html(text1);
	});

	$('.btn-diff').on('click', function() {
		var id = $(this).data('id');
		
		var dmp = new diff_match_patch();
		
		var text2 = $('.text'+id).html();

		var d = dmp.diff_main(text1, text2);
		dmp.diff_cleanupSemantic(d);
		var ds = dmp.diff_prettyHtml(d);
		$('#text').html(htmlspecialchars_decode(ds));
	});
});
</script>

<?php if(!empty($part->id)): ?>
<h3><?php echo __('Current text'); ?>  <button class="btn btn-mini btn-clear"><?php echo __('Clear diff'); ?></button></h3>
<pre id="text"><?php echo htmlspecialchars($part->content); ?></pre>
<?php endif ;?>

<?php foreach($parts as $id => $p):?>
<h3><?php echo Date::format($p->created_on, 'd F Y H:i'); ?> 
	<span class="btn-group">
		<?php if(!empty($part->id)): ?>
		<button class="btn btn-mini btn-diff" data-id="<?php echo $p->id; ?>">
			<?php echo __('Show diff'); ?>
		</button>
		<?php endif ;?>
		<?php 
		$url =  Route::get('backend')->uri(array(
			'controller' => 'part',
			'action' => 'revert',
			'id' => $p->id
		)); ?>
		<a href="<?php echo $url; ?>" class="btn btn-mini btn-confirm btn-success">
			<?php echo __('Use this revision'); ?>
		</a>
	</span>
	

</h3>
<pre class="text<?php echo $p->id; ?>"><?php echo htmlspecialchars($p->content); ?></pre>
<?php endforeach; ?>
