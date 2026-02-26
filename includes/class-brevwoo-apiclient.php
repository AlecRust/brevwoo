<?php
/**
 * API client used to interact with Brevo.
 *
 * @package    BrevWoo
 * @subpackage BrevWoo/includes
 * @link       https://github.com/AlecRust/brevwoo
 */

use Brevo\Brevo;
use Brevo\Contacts\Requests\CreateContactRequest;
use Brevo\Contacts\Requests\GetFoldersRequest;
use Brevo\Contacts\Requests\GetListsRequest;

/**
 * API client used to interact with Brevo.
 */
class BrevWoo_ApiClient {

	/**
	 * Unified Brevo SDK client.
	 *
	 * @var Brevo
	 */
	private $client;

	/**
	 * Cached lists for the current request lifecycle.
	 *
	 * @var array<int, array{id:int,name:string,folderId:int}>|null
	 */
	private $lists_cache;

	/**
	 * Cached folders for the current request lifecycle.
	 *
	 * @var array<int, array{id:int,name:string}>|null
	 */
	private $folders_cache;

	/**
	 * Initialize the API client with the provided API key.
	 *
	 * @param string $api_key Brevo API key.
	 */
	public function __construct( $api_key ) {
		$this->client        = new Brevo( $api_key );
		$this->lists_cache   = null;
		$this->folders_cache = null;
	}

	/**
	 * Fetch the account information from Brevo.
	 * https://developers.brevo.com/reference/getaccount
	 *
	 * @return array<string, string>
	 */
	public function get_account() {
		$account = $this->client->account->getAccount();
		// phpcs:ignore WordPress.NamingConventions.ValidVariableName.UsedPropertyNotSnakeCase
		$company_name = strval( $account->companyName );
		return array(
			'email'        => strval( $account->email ),
			'company_name' => $company_name,
		);
	}

	/**
	 * Fetch all lists from Brevo.
	 * https://developers.brevo.com/reference/getlists-1
	 *
	 * @param int $limit  Number of lists to fetch per page (50 max).
	 * @param int $offset Offset for fetching lists.
	 * @return array<int, array{id:int,name:string,folderId:int}>
	 */
	public function get_lists( $limit = 50, $offset = 0 ) {
		if ( is_array( $this->lists_cache ) ) {
			return $this->lists_cache;
		}

		$limit          = max( 1, min( 50, intval( $limit ) ) );
		$offset         = max( 0, intval( $offset ) );
		$expected_count = null;
		$lists_by_id    = array();

		while ( true ) {
			$response = $this->client->contacts->getLists(
				new GetListsRequest(
					array(
						'limit'  => $limit,
						'offset' => $offset,
					)
				)
			);

			if ( null !== $response->count ) {
				$expected_count = intval( $response->count );
			}

			$page_lists = is_array( $response->lists ) ? $response->lists : array();
			$page_count = count( $page_lists );

			if ( 0 === $page_count ) {
				break;
			}

			foreach ( $page_lists as $list_item ) {
				$list_id = intval( $list_item->id );
				// phpcs:ignore WordPress.NamingConventions.ValidVariableName.UsedPropertyNotSnakeCase
				$folder_id               = intval( $list_item->folderId );
				$lists_by_id[ $list_id ] = array(
					'id'       => $list_id,
					'name'     => strval( $list_item->name ),
					'folderId' => $folder_id,
				);
			}

			$offset += $limit;

			if ( $page_count < $limit ) {
				break;
			}

			if (
				null !== $expected_count &&
				count( $lists_by_id ) >= $expected_count
			) {
				break;
			}
		}

		$this->lists_cache = array_values( $lists_by_id );
		return $this->lists_cache;
	}

	/**
	 * Fetch all folders from Brevo.
	 * https://developers.brevo.com/reference/getfolders-1
	 *
	 * @param int $limit  Number of folders to fetch per page (50 max).
	 * @param int $offset Offset for fetching folders.
	 * @return array<int, array{id:int,name:string}>
	 */
	public function get_folders( $limit = 50, $offset = 0 ) {
		if ( is_array( $this->folders_cache ) ) {
			return $this->folders_cache;
		}

		$limit          = max( 1, min( 50, intval( $limit ) ) );
		$offset         = max( 0, intval( $offset ) );
		$expected_count = null;
		$folders_by_id  = array();

		while ( true ) {
			$response = $this->client->contacts->getFolders(
				new GetFoldersRequest(
					array(
						'limit'  => $limit,
						'offset' => $offset,
					)
				)
			);

			if ( null !== $response->count ) {
				$expected_count = intval( $response->count );
			}

			$page_folders = is_array( $response->folders ) ? $response->folders : array();
			$page_count   = count( $page_folders );

			if ( 0 === $page_count ) {
				break;
			}

			foreach ( $page_folders as $folder_item ) {
				$folder_id                   = intval( $folder_item->id );
				$folders_by_id[ $folder_id ] = array(
					'id'   => $folder_id,
					'name' => strval( $folder_item->name ),
				);
			}

			$offset += $limit;

			if ( $page_count < $limit ) {
				break;
			}

			if (
				null !== $expected_count &&
				count( $folders_by_id ) >= $expected_count
			) {
				break;
			}
		}

		$this->folders_cache = array_values( $folders_by_id );
		return $this->folders_cache;
	}

	/**
	 * Create or update a contact in Brevo.
	 * https://developers.brevo.com/reference/createcontact
	 *
	 * @param string                      $email Brevo contact email.
	 * @param array<string, string|float> $attributes Brevo contact attributes.
	 * @param array<int>                  $list_ids The list IDs to add the contact to.
	 * @return array{id:int|null}
	 */
	public function create_contact( $email, $attributes, $list_ids ) {
		$list_ids = array_values(
			array_filter(
				array_map( 'intval', $list_ids )
			)
		);

		$response = $this->client->contacts->createContact(
			new CreateContactRequest(
				array(
					'email'         => $email,
					'updateEnabled' => true,
					'attributes'    => $attributes,
					'listIds'       => $list_ids,
				)
			)
		);

		return array(
			'id' => $response->id,
		);
	}
}
