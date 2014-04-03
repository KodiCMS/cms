<?php 
	$options = array();
	
	if($plugin->webvisor == 1) $options[] = 'webvisor:true';
	if($plugin->clickmap == 1) $options[] = 'clickmap:true';
	if($plugin->track_links == 1) $options[] = 'trackLinks:true';
	if($plugin->accurate_track_bounce == 1) $options[] = 'accurateTrackBounce:true';
?>

<!-- Yandex.Metrika counter -->
<script type="text/javascript">
(function (d, w, c) {
	(w[c] = w[c] || []).push(function() {
		try {
			w.yaCounter<?php echo $plugin->counter_id ?> = new Ya.Metrika({
					id:<?php echo $plugin->counter_id ?><?php if(!empty($options)): ?>,<?php echo implode(',', $options); ?><?php endif; ?>
			});
		} catch(e) { }
	});

	var n = d.getElementsByTagName("script")[0],
		s = d.createElement("script"),
		f = function () { n.parentNode.insertBefore(s, n); };
	s.type = "text/javascript";
	s.async = true;
	s.src = (d.location.protocol == "https:" ? "https:" : "http:") + "//mc.yandex.ru/metrika/watch.js";

	if (w.opera == "[object Opera]") {
		d.addEventListener("DOMContentLoaded", f, false);
	} else { f(); }
})(document, window, "yandex_metrika_callbacks");
</script>
<noscript><div><img src="//mc.yandex.ru/watch/<?php echo $plugin->counter_id ?>" style="position:absolute; left:-9999px;" alt="" /></div></noscript>
<!-- /Yandex.Metrika counter -->