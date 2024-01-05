<div class="tab-content align-center" v-if="currentSubTab === 'other'">
	<div class="form-section">
		<h3>Global listing type</h3>
		<p>
			Use this listing type in Explore page to display a global search form, that will
			look for results within all other listing types. You shouldn't have more than one global listing type.
			They also shouldn't be used in the Add Listing page or anywhere else besides the Explore page.
		</p>
		<label class="form-switch">
			<input type="checkbox" v-model="settings.global">
			<span class="switch-slider"></span>
		</label>
	</div>
</div>