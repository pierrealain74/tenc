<?php
/**
 * Default footer template for email notifications.
 *
 * @since 2.1
 */
if ( ! defined('ABSPATH') ) {
	exit;
} ?>
		                                </td>
		                            </tr>
		                        </table>
		                    </td>
		                </tr>
                    </table>
                    <div class="footer">
                        <table role="presentation" border="0" cellpadding="0" cellspacing="0">
                            <tr>
                                <td class="content-block powered-by">
                                	<a href="<?php echo esc_url( home_url('/') ) ?>" target="_blank">
                                		<strong><?php bloginfo('name') ?></strong><br>
                                		<?php bloginfo('description') ?>
                                	</a>
                                </td>
                            </tr>
                        </table>
                    </div>
                </div>
            </td>
            <td>&nbsp;</td>
        </tr>
    </table>
</body>
</html>