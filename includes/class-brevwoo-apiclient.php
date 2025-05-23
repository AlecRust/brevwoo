<?php
/**
 * API client used to interact with Brevo.
 *
 * @package    BrevWoo
 * @subpackage BrevWoo/includes
 * @link       https://github.com/AlecRust/brevwoo
 */

use Brevo\Client\Configuration;
use Brevo\Client\Api\ContactsApi;
use Brevo\Client\Api\AccountApi;
use GuzzleHttp\Client;

/**
 * API client used to interact with Brevo.
 */
class BrevWoo_ApiClient {

	/**
	 * The instance of the ContactsApi.
	 *
	 * @var ContactsApi
	 */
	private $contacts_instance;

	/**
	 * The instance of the AccountApi.
	 *
	 * @var AccountApi
	 */
	private $account_instance;

	/**
	 * Initialize the API client with the provided API key.
	 *
	 * @param string $api_key Brevo API key.
	 */
	public function __construct( $api_key ) {
		$config                  = Configuration::getDefaultConfiguration()->setApiKey(
			'api-key',
			$api_key
		);
		$this->contacts_instance = new ContactsApi(
			new Client(),
			$config
		);
		$this->account_instance  = new AccountApi(
			new Client(),
			$config
		);
	}

	/**
	 * Fetch the account information from Brevo.
	 * https://developers.brevo.com/reference/getaccount
	 *
	 * @return Brevo\Client\Model\GetAccount
	 */
	public function get_account() {
		return $this->account_instance->getAccount();
	}

	/**
	 * Fetch all lists from Brevo.
	 * https://developers.brevo.com/reference/getlists-1
	 *
	 * @param int $limit  Number of lists to fetch (50 max).
	 * @param int $offset Offset for fetching lists.
	 * @return Brevo\Client\Model\GetLists
	 */
	public function get_lists( $limit = 50, $offset = 0 ) {
		return $this->contacts_instance->getLists( $limit, $offset );
	}

	/**
	 * Fetch all folders from Brevo.
	 * https://developers.brevo.com/reference/getfolders-1
	 *
	 * @param int $limit  Number of folders to fetch (50 max).
	 * @param int $offset Offset for fetching folders.
	 * @return Brevo\Client\Model\GetFolders
	 */
	public function get_folders( $limit = 50, $offset = 0 ) {
		return $this->contacts_instance->getFolders( $limit, $offset );
	}

	/**
	 * Create or update a contact in Brevo.
	 * https://developers.brevo.com/reference/createcontact
	 *
	 * @param Brevo\Client\Model\CreateContact $brevo_contact Contact to create or update.
	 * @return Brevo\Client\Model\CreateUpdateContactModel
	 */
	public function create_contact( $brevo_contact ) {
		return $this->contacts_instance->createContact( $brevo_contact );
	}
}
