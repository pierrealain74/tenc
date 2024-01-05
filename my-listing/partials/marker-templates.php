<script id="case27-basic-marker-template" type="text/template">
	<a href="#" class="marker-icon">
		<div class="marker-img" style="background-image: url({{marker-bg}});"></div>
	</a>
</script>
<script id="case27-traditional-marker-template" type="text/template">
	<div class="cts-marker-pin">
		<img src="<?php echo esc_url( c27()->image( 'pin.png' ) ) ?>">
	</div>
</script>
<script id="case27-user-location-marker-template" type="text/template">
	<div class="cts-geoloc-marker"></div>
</script>
<script id="case27-marker-template" type="text/template">
	<a href="#" class="marker-icon {{listing-id}}">
		{{icon}}
		<div class="marker-img" style="background-image: url({{marker-bg}});"></div>
	</a>
</script>