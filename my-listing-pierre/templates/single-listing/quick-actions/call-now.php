<?php
/**
 * `Call now` quick action.
 *
 * @since 2.0
 */

if ( ! ( $phone = $listing->get_field('phone') ) ) {
	return;
}

/* $link = sprintf( 'tel:%s', $phone ); */

$link = sprintf( '', $phone );
?>

<li id="<?php echo esc_attr( $action['id'] ) ?>" class="<?php echo esc_attr( $action['class'] ) ?>">
    <a href="#" rel="nofollow" onclick="displayPhone('<?php echo esc_js( $phone ) ?>')">
    	<?php echo c27()->get_icon_markup( $action['icon'] ) ?>
    	<span><?php echo $action['label'] ?></span>
    </a>
</li>
<!--Modification PAF 05-01/2024
Au clique sur le telephone de l'annocne cela lance une appli telephoniqe, 
Code modif : afficher le telephone, c'est tout.
-->
<script>
	function displayPhone(phone){
		//alert(phone);

		const spanPhone = document.querySelector('li a span');
		spanPhone.textContent = phone;

	}
</script>