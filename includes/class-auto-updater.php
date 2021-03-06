<?php
/**
 * Global functionality of the plugin.
 *
 * Defines the functionality loaded both on admin and frontend.
 *
 * @since      0.9.0
 * @package    RankMath
 * @subpackage RankMath\Core
 * @author     Rank Math <support@rankmath.com>
 */

namespace RankMath;

use RankMath\Traits\Hooker;
use RankMath\Helper;

defined( 'ABSPATH' ) || exit;

/**
 * Common class.
 */
class Auto_Updater {

	use Hooker;

	/**
	 * Constructor method.
	 */
	public function __construct() {
		$this->filter( 'auto_update_plugin', 'auto_update_plugin', 20, 2 );
		$this->filter( 'plugin_auto_update_setting_html', 'plugin_auto_update_setting_html', 10, 3 );
	}

	/**
	 * Auto update the plugin.
	 *
	 * @param bool  $update Whether to update the plugin or not.
	 * @param array $item  The update plugin object.
	 *
	 * @return bool
	 */
	public function auto_update_plugin( $update, $item ) {
		if ( $this->is_rm_update( $item ) ) {
			// Never update to beta automatically.
			if ( $this->is_beta_update( $item->new_version ) ) {
				return false;
			}

			return Helper::get_auto_update_setting();
		}

		return $update;
	}

	/**
	 * Check if updatable object is RM.
	 *
	 * @param object $item Updatable object.
	 * @return boolean
	 */
	public function is_rm_update( $item ) {
		return isset( $item->slug ) &&
			'seo-by-rank-math' === $item->slug &&
			isset( $item->new_version );
	}

	/**
	 * Check if given version is beta.
	 *
	 * @param string $version Version number.
	 * @return boolean
	 */
	public function is_beta_update( $version ) {
		return false !== stripos( $version, 'beta' );
	}

	/**
	 * Hide "update scheduled in X hours" message if update is a beta version.
	 *
	 * @param string $html        HTML string.
	 * @param string $plugin_file Plugin file relative to the plugin directory.
	 * @param array  $plugin_data Plugin update data.
	 * @return string
	 */
	public function plugin_auto_update_setting_html( $html, $plugin_file, $plugin_data ) {
		if ( 'seo-by-rank-math/rank-math.php' !== $plugin_file ) {
			return $html;
		}

		if ( ! empty( $plugin_data['is_beta'] ) ) {
			$html = str_replace( 'class="auto-update-time"', 'class="auto-update-time hidden"', $html );
		}

		return $html;
	}
}
