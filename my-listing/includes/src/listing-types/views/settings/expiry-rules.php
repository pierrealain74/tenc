<div class="tab-content align-center" v-if="currentSubTab === 'expiry-rules'">
	<expiry-rules inline-template>
		<div class="form-section expiry-rules">
			<h3>Listing expiry rules</h3>
			<p>Apart from the expiry date, you can have a listing expire when other conditions are met.</p>

			<div class="btn btn-secondary btn-block mb5 default-expiry">
				Expires when the expiry date is reached
			</div>

			<div v-for="rule in rules" class="btn btn-secondary btn-block mb5 rule" @click="removeRule(rule)">
				{{getRuleLabel(rule)}}
				<span>Delete rule</span>
			</div>

			<div class="select-wrapper text-right mt15">
				<select @change="addRule($event.target.value);$event.target.value='';">
					<option value="">
						{{ availableRules.length ? 'Add rule' : 'No additional rules available' }}
					</option>
					<option v-for="rule in availableRules" :value="rule.value">
						{{ rule.label }}
					</option>
				</select>
			</div>
		</div>
	</expiry-rules>
</div>