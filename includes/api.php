<?php
// phpcs:disable PSR1.Classes.ClassDeclaration.MissingNamespace
/**
 * API client used to interact with Brevo.
 *
 * @link       https://www.alecrust.com/
 * @package    BrevWoo
 * @subpackage BrevWoo/includes
 * @author     Alec Rust (https://www.alecrust.com/)
 */

class BrevWooApiClient
{
    /**
     * The instance of the ContactsApi.
     *
     * @var ContactsApi
     */
    private $contactsApiInstance;

    /**
     * The instance of the AccountApi.
     *
     * @var AccountApi
     */
    private $accountApiInstance;

    /**
     * Initialize the API client with the provided API key.
     * @SuppressWarnings(PHPMD.MissingImport)
     *
     * @param string $apiKey Brevo API key.
     */
    public function __construct($apiKey)
    {
        $config = Brevo\Client\Configuration::getDefaultConfiguration()->setApiKey(
            'api-key',
            $apiKey
        );
        $this->contactsApiInstance = new Brevo\Client\Api\ContactsApi(
            new GuzzleHttp\Client(),
            $config
        );
        $this->accountApiInstance = new Brevo\Client\Api\AccountApi(
            new GuzzleHttp\Client(),
            $config
        );
    }

    /**
     * Fetches the account information from Brevo.
     * https://developers.brevo.com/reference/getaccount
     */
    public function getAccount()
    {
        return $this->accountApiInstance->getAccount();
    }

    /**
     * Fetches all lists from Brevo.
     * https://developers.brevo.com/reference/getlists-1
     *
     * @param int $limit  Number of lists to fetch (50 max)
     * @param int $offset Offset for fetching lists.
     */
    public function getLists($limit = 50, $offset = 0)
    {
        return $this->contactsApiInstance->getLists($limit, $offset);
    }

    /**
     * Creates or updates a contact in Brevo.
     * https://developers.brevo.com/reference/createcontact
     *
     */
    public function createOrUpdateContact($createContact)
    {
        return $this->contactsApiInstance->createContact($createContact);
    }
}
