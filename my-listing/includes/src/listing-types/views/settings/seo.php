<seo inline-template v-if="currentSubTab === 'seo'">
	<div class="tab-content align-center">
		<div class="settings-tab-seo-content">
			<div class="form-section">
				<h3>Schema Markup</h3>
				<p>
					Optimize your listing's visibility in search engine results through custom
					<a href="https://developers.google.com/search/docs/guides/intro-structured-data" target="_blank">structured data</a> markup.
					You can use the <a href="#" class="cts-show-tip" data-tip="bracket-syntax">bracket syntax</a> to retrieve listing information.
				</p>
			</div>

			<div class="form-jsoneditor">
				<div class="form-group schema-markup">
					<div id="lte-seo-markup" class="lte-seo-markup"></div>
				</div><br>
				<div class="text-right">
					<a @click.prevent="setDefaultMarkup" class="btn btn-secondary">Reset</a>
					<a @click.prevent="$root.setTab('settings', 'general')" class="btn btn-primary">Save</a>
				</div>
			</div>
			<!-- <pre>{{ $root.settings.seo.markup }}</pre> -->
		</div>
	</div>
</seo>