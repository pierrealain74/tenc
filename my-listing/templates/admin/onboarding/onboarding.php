<?php
/**
 * MyListing setup/onboarding screen.
 *
 * @since 2.5
 */
if ( ! defined('ABSPATH') ) {
	exit;
} ?>

<div class="wrap" id="mylisting-onboarding" v-cloak>
	<h1>Setup MyListing</h1>

	<div v-if="!demoImport.started">
		<div class="demos">
			<div v-for="demo, demo_key in config.demos" class="demo">
				<img :src="demo.demo_image" alt="">
				<strong>{{demo.name}}</strong> 
				<div class="demo-buttons">
					<a :href="demo.preview_url" class="btn btn-secondary btn-xs" target="_blank">Preview</a> 
					<a href="#" @click.prevent="startImport(demo_key)" class="btn btn-primary-alt btn-xs">Install</a>
				</div>
			</div>
		</div>
	</div>

	<div v-if="demoImport.started && !demoImport.done">
		<p><strong>Importing "{{demoImport.demo.name}}"</strong></p>
		<p>{{demoImport.message}}</p>
	</div>

	<div v-if="demoImport.done">
		<p>
			"{{demoImport.demo.name}}" has been imported successfully.
			<a href="<?php echo esc_url( home_url('/') ) ?>">Go to homepage.</a>
		</p>
	</div>

	<!-- <pre>{{$data}}</pre> -->
</div>