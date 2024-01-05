<?php
/**
 * Iconpicker template.
 *
 * @since 1.0
 */
if ( ! defined('ABSPATH') ) {
	exit;
} ?>
<script id="cts-icon-picker-template" type="text/template">
	<div class="c27-icon-picker">
		<div class="selected-icon" @click="expanded = !expanded" :title="selected">
			<i :class="selected"></i>
		</div>
		<div class="icons-box-wrapper" v-if="expanded" @click.self="expanded=false">
			<div class="icons-box">
				<div class="box-header">
					<div class="filter-item">
						<label>Filter by name</label>
						<input type="text" placeholder="Search..." class="search-icon" v-model="filter">
					</div>

					<div class="filter-item">
						<label>Filter by icon pack</label>
						<div class="select-wrapper">
							<select class="select-pack" v-model="active_pack">
								<option v-for="(icons, icon_pack) in icon_packs" :value="icon_pack">{{ icon_pack.replace("-", " ") }}</option>
							</select>
						</div>
					</div>

					<div class="filter-item current-icon" v-show="selected">
						<i :class="selected"></i>
						<p><strong>{{selected}}</strong> (<a @click.prevent="selected = ''; expanded = false;" href="#">Remove</a>)</p>
					</div>

					<div @click="expanded = false;" class="button close-dialog">Close Dialog</div>
				</div>
				<div class="icons-list">
					<i v-for="icon in activeIcons" @click="selected = icon; expanded = false;" :class="icon" :title="icon"></i>
				</div>
			</div>
		</div>
	</div>
</script>