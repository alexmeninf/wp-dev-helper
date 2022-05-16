<?php

/**
 * Delete Post Revision is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 2 of the License, or
 * any later version.
 *
 * Delete Post Revision is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with Disable Post Revision. If not, see <http://www.gnu.org/licenses/>.
 *
 */

// If this file is called directly, abort.
defined('ABSPATH') or exit;

if (!class_exists('Disable_Post_Revision')) :

	class Disable_Post_Revision
	{
		function __construct()
		{
			add_action('admin_enqueue_scripts', array($this, 'init_script_revision'));
			add_action('wp_ajax_delete_revision_posts', [$this, 'delete_revision_posts']);
			add_action('wp_ajax_nopriv_delete_revision_posts', [$this, 'delete_revision_posts']);
		}

		public function init_script_revision()
		{
			wp_enqueue_script(
				'wpdh-ajax_revision',
				PLUGINROOT . '/assets/js/wpdh-revision.js',
				array('jquery'),
				true
			);

			wp_localize_script(
				'wpdh-ajax_revision',
				'revision_var',
				array(
					'url'   => admin_url('admin-ajax.php'),
					'nonce' => wp_create_nonce("delete_revision_posts_nonce"),
				)
			);
		}

		public function delete_revision_posts()
		{
			global $wpdb;

			check_ajax_referer('delete_revision_posts_nonce', 'nonce');

			$sql = "DELETE FROM $wpdb->posts WHERE post_type = 'revision'";

			try {

				if (is_admin() && is_user_logged_in() && current_user_can('manage_options')) {
					$r = (int)$wpdb->query($wpdb->prepare($sql));

					if ($r > 0) {
						echo json_encode(
							array(
								'success' => 1,
								'message' => 'Todas as revisões foram excluídas! Total de ' . $r . ' linhas encontradas.',
							)
						);
					} else {
						echo json_encode(
							array(
								'success' => 1,
								'message' => 'Não existe revisões nas suas tabelas. Nenhuma linha foi afetada.',
							)
						);
					}
				}
			} catch (Exception $e) {

				printf(json_encode(
					array(
						'success' => 0,
						'error' => $wpdb->last_error,
					)
				));
			}

			wp_die();
		}
	}
endif;

new Disable_Post_Revision();
