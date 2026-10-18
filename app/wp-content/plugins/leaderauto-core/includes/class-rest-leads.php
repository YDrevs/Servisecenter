<?php
/**
 * Leads API for the CRM, behind an X-Api-Key header.
 *
 *   GET   /wp-json/leaderauto/v1/leads
 *   GET   /wp-json/leaderauto/v1/leads/{id}
 *   PATCH /wp-json/leaderauto/v1/leads/{id}   { status?, note? }
 *
 * @package LeaderAuto\Core
 */

namespace LeaderAuto\Core;

use WP_Error;
use WP_REST_Request;
use WP_REST_Response;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class Rest_Leads {

	private const NAMESPACE = 'leaderauto/v1';
	private const MAX_NOTE  = 2000;

	public function hooks(): void {
		add_action( 'rest_api_init', array( $this, 'register' ) );
	}

	public function register(): void {
		register_rest_route(
			self::NAMESPACE,
			'/leads',
			array(
				'methods'             => 'GET',
				'permission_callback' => array( $this, 'permission' ),
				'callback'            => array( $this, 'list' ),
				'args'                => array(
					'updated_since' => array( 'type' => 'string' ),
					'status'        => array( 'type' => 'string' ),
					'page'          => array( 'type' => 'integer', 'default' => 1, 'minimum' => 1 ),
					'per_page'      => array( 'type' => 'integer', 'default' => 50, 'minimum' => 1, 'maximum' => 100 ),
				),
			)
		);

		register_rest_route(
			self::NAMESPACE,
			'/leads/(?P<id>\d+)',
			array(
				array(
					'methods'             => 'GET',
					'permission_callback' => array( $this, 'permission' ),
					'callback'            => array( $this, 'get' ),
				),
				array(
					'methods'             => 'PATCH',
					'permission_callback' => array( $this, 'permission' ),
					'callback'            => array( $this, 'patch' ),
					'args'                => array(
						'status' => array( 'type' => 'string' ),
						'note'   => array( 'type' => 'string' ),
					),
				),
			)
		);
	}

	/**
	 * @return true|WP_Error
	 */
	public function permission( WP_REST_Request $request ) {
		$key = settings()['crm_api_key'];
		if ( '' === $key ) {
			return new WP_Error( 'leaderauto_api_disabled', __( 'API заявок вимкнено.', 'leaderauto-core' ), array( 'status' => 503 ) );
		}
		if ( ! hash_equals( $key, (string) $request->get_header( 'x_api_key' ) ) ) {
			return new WP_Error( 'leaderauto_api_unauthorized', __( 'Невірний або відсутній X-Api-Key.', 'leaderauto-core' ), array( 'status' => 401 ) );
		}
		return true;
	}

	/**
	 * @return WP_REST_Response|WP_Error
	 */
	public function list( WP_REST_Request $request ) {
		$allowed  = array_merge( array_keys( Leads::statuses() ), array( 'trash' ) );
		$statuses = $allowed;
		$raw      = trim( (string) $request->get_param( 'status' ) );
		if ( '' !== $raw ) {
			$statuses = array_values( array_filter( array_map( 'trim', explode( ',', $raw ) ) ) );
			if ( ! $statuses || array_diff( $statuses, $allowed ) ) {
				return $this->bad_request( 'status', __( 'Допустимі статуси: ', 'leaderauto-core' ) . implode( ', ', $allowed ) );
			}
		}

		$args = array(
			'post_type'      => Leads::POST_TYPE,
			'post_status'    => $statuses,
			'orderby'        => array( 'modified' => 'ASC', 'ID' => 'ASC' ),
			'posts_per_page' => (int) $request->get_param( 'per_page' ),
			'paged'          => (int) $request->get_param( 'page' ),
		);

		$since = trim( (string) $request->get_param( 'updated_since' ) );
		if ( '' !== $since ) {
			try {
				$utc = ( new \DateTimeImmutable( $since ) )->setTimezone( new \DateTimeZone( 'UTC' ) );
			} catch ( \Exception $e ) {
				return $this->bad_request( 'updated_since', __( 'Дата має бути в форматі ISO 8601, напр. 2026-10-17T10:00:00+03:00.', 'leaderauto-core' ) );
			}
			$args['date_query'] = array(
				array(
					'column'    => 'post_modified_gmt',
					'after'     => $utc->format( 'Y-m-d H:i:s' ),
					'inclusive' => true,
				),
			);
		}

		$query = new \WP_Query( $args );
		$items = array_map( array( Leads::class, 'to_array' ), wp_list_pluck( $query->posts, 'ID' ) );

		return new WP_REST_Response(
			array(
				'items'       => $items,
				'page'        => $args['paged'],
				'per_page'    => $args['posts_per_page'],
				'total'       => (int) $query->found_posts,
				'total_pages' => (int) $query->max_num_pages,
			),
			200
		);
	}

	/**
	 * @return WP_REST_Response|WP_Error
	 */
	public function get( WP_REST_Request $request ) {
		$lead = Leads::to_array( (int) $request['id'] );
		return $lead ? new WP_REST_Response( $lead, 200 ) : $this->not_found();
	}

	/**
	 * @return WP_REST_Response|WP_Error
	 */
	public function patch( WP_REST_Request $request ) {
		$id   = (int) $request['id'];
		$lead = Leads::to_array( $id );
		if ( ! $lead ) {
			return $this->not_found();
		}
		if ( 'trash' === $lead['status'] ) {
			return new WP_Error( 'leaderauto_lead_trashed', __( 'Заявка в кошику.', 'leaderauto-core' ), array( 'status' => 409 ) );
		}

		$params = $request->get_json_params() ?: $request->get_body_params();
		$status = array_key_exists( 'status', (array) $params ) ? (string) $request->get_param( 'status' ) : null;
		$note   = array_key_exists( 'note', (array) $params ) ? (string) $request->get_param( 'note' ) : null;
		if ( null === $status && null === $note ) {
			return $this->bad_request( 'body', __( 'Передайте status і/або note.', 'leaderauto-core' ) );
		}

		$errors = array();
		if ( null !== $status && ! isset( Leads::statuses()[ $status ] ) ) {
			$errors['status'] = __( 'Допустимі статуси: ', 'leaderauto-core' ) . implode( ', ', array_keys( Leads::statuses() ) );
		}
		if ( null !== $note ) {
			$note = sanitize_textarea_field( $note );
			if ( mb_strlen( $note ) > self::MAX_NOTE ) {
				$errors['note'] = __( 'Нотатка задовга (максимум 2000 символів).', 'leaderauto-core' );
			}
		}
		if ( $errors ) {
			return new WP_Error( 'leaderauto_invalid', __( 'Перевірте поля.', 'leaderauto-core' ), array( 'status' => 422, 'fields' => $errors ) );
		}

		// Note first, so a status_changed event to Make already carries it.
		if ( null !== $note ) {
			update_post_meta( $id, '_la_note', $note );
		}
		$res = wp_update_post( array( 'ID' => $id, 'post_status' => $status ?? $lead['status'] ), true );
		if ( is_wp_error( $res ) ) {
			return new WP_Error( 'leaderauto_update_failed', $res->get_error_message(), array( 'status' => 500 ) );
		}

		return new WP_REST_Response( Leads::to_array( $id ), 200 );
	}

	private function not_found(): WP_Error {
		return new WP_Error( 'leaderauto_lead_not_found', __( 'Заявку не знайдено.', 'leaderauto-core' ), array( 'status' => 404 ) );
	}

	private function bad_request( string $field, string $message ): WP_Error {
		return new WP_Error( 'leaderauto_bad_request', $message, array( 'status' => 400, 'field' => $field ) );
	}
}
