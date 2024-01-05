<?php
$brand = c27()->get_setting( 'general_brand_color', '#f24286' );
?>
:root {
  --accent: <?php echo $brand ?>;
}

<?php
// four dots spinner
$loaderColor = c27()->hexToRgb( c27()->get_setting( 'general_loading_overlay_color' ,'#000000' ) );
$secondaryLoaderColor = $loaderColor;
$secondaryLoaderColor['a'] = '0.2';
?>
@keyframes spin3 {
  0%, 100% {
    box-shadow: 10px 10px rgba(<?php echo join(', ', $loaderColor) ?>), -10px 10px rgba(<?php echo join(', ', $secondaryLoaderColor) ?>),
                -10px -10px rgba(<?php echo join(', ', $loaderColor) ?>), 10px -10px rgba(<?php echo join(', ', $secondaryLoaderColor) ?>); }
    25% {
        box-shadow: -10px 10px rgba(<?php echo join(', ', $secondaryLoaderColor) ?>), -10px -10px rgba(<?php echo join(', ', $loaderColor) ?>),
                    10px -10px rgba(<?php echo join(', ', $secondaryLoaderColor) ?>), 10px 10px rgba(<?php echo join(', ', $loaderColor) ?>); }
    50% {
        box-shadow: -10px -10px rgba(<?php echo join(', ', $loaderColor) ?>), 10px -10px rgba(<?php echo join(', ', $secondaryLoaderColor) ?>),
                    10px 10px rgba(<?php echo join(', ', $loaderColor) ?>), -10px 10px rgba(<?php echo join(', ', $secondaryLoaderColor) ?>); }
    75% {
        box-shadow: 10px -10px rgba(<?php echo join(', ', $secondaryLoaderColor) ?>), 10px 10px rgba(<?php echo join(', ', $loaderColor) ?>),
                    -10px 10px rgba(<?php echo join(', ', $secondaryLoaderColor) ?>), -10px -10px rgba(<?php echo join(', ', $loaderColor) ?>); }
}

#wp<?php echo 'adminbar' ?> { top: 0 !important; }
#c27-site-wrapper { background-color: <?php echo c27()->get_setting( 'general_background_color', '#f4f4f4' ) ?> }