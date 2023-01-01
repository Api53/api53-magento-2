<?php
/**
 * @category   Api53
 * @package    Api53_Api53
 * @copyright  Copyright (c) 2018 Api53. (http://www.emarsys.net/)
 */

namespace Api53\Api53\Model;

use GuzzleHttp\Client;
use GuzzleHttp\ClientFactory;
use GuzzleHttp\Exception\GuzzleException;
use GuzzleHttp\Psr7\Response;
use GuzzleHttp\Psr7\ResponseFactory;
use Magento\Framework\Webapi\Rest\Request;
use Magento\Store\Model\StoreManagerInterface as StoreManager;

/**
 * Class Api
 * API class for Api53 API wrappers
 *
 * @package Api53\Api53\Model\Api
 */
class Api extends \Magento\Framework\DataObject
{
    /**
     * API request URL
     */
    const API_REQUEST_URI = 'https://internal-api.api53.com';

    /**
     * @var StoreManager
     */
    protected $apiKey;

    /**
     * @var StoreManager
     */
    protected $storeManager;

	/**
     * @var ResponseFactory
     */
    protected $responseFactory;

    /**
     * @var ClientFactory
     */
    protected $clientFactory;
    /**
     * Api constructor.
     * By default is looking for first argument as array and assigns it as object
     * attributes This behavior may change in child classes
     *
     * @param StoreManager $storeManager
     * @param array $data
     */
	public function __construct(
        ClientFactory $clientFactory,
        ResponseFactory $responseFactory,
		StoreManager $storeManager
    ) {
        $this->clientFactory = $clientFactory;
        $this->responseFactory = $responseFactory;
        $this->storeManager = $storeManager;
    }

    public function _construct()
    {
        $this->websiteId = '';
        $this->scope = '';
    }

    /**
     * @param $websiteId
     */
    public function setWebsiteId($websiteId)
    {
        $this->websiteId = $websiteId;
        $this->scope = 'websites';
    }

    /**
     * Return Api53 Api user name based on config data
     *
     * @return string
     */
    public function setApiKey($apiKey)
    {
		$this->apiKey = $apiKey;
    }
	
    /**
     * Return Api53 Api user name based on config data
     *
     * @return string
     */
    public function getApiKey()
    {
        return $this->apiKey;
    }

    /**
     * Return Api53 Api user name based on config data
     *
     * @return string
     */
   // public function setApiKeyFromConfig()
    //{
        /** @var \Magento\Store\Api\Data\WebsiteInterface $website */
      //  $website = $this->storeManager->getWebsite($this->websiteId);
      //  return $website->getConfig('api53/settings/api_key');
    //}

    /**
     * @param $requestType
     * @param null $endPoint
     * @param array $requestBody
     * @return array
     * @throws \Exception
     */
	private function doRequest(
        string $uriEndpoint,
        array $params = [],
        string $requestMethod = Request::HTTP_METHOD_GET
    ): Response {
        /** @var Client $client */
        $client = $this->clientFactory->create(['config' => [
            'base_uri' => self::API_REQUEST_URI
        ]]);

        try {
            $response = $client->request(
                $requestMethod,
                $uriEndpoint,
                $params
            );
        } catch (GuzzleException $exception) {
            /** @var Response $response */
            $response = $this->responseFactory->create([
                'status' => $exception->getCode(),
                'reason' => $exception->getMessage()
            ]);
        }

        return $response;
    }

    /**
     * @param $arrCustomerData
     * @return mixed|string
     * @throws \Exception
     */
    public function checkSubscriptionSKU()
    {
		return $this->doRequest('/v1/check-subscription-sku', ['body' => $this->apiKey], 'POST');;
    }

    /**
     * @param $arrCustomerData
     * @return mixed|string
     * @throws \Exception
     */
    public function updateOrCreateProduct($data)
    {
		return $this->doRequest('/v1/product', ['headers' => ['X-Api-Key' => $this->apiKey, 'charset' => 'utf-8'], 'json' => $data], 'POST');;
    }

    /**
     * @param $arrCustomerData
     * @return mixed|string
     * @throws \Exception
     */
    public function deleteProduct($data)
    {
		return $this->doRequest('/v1/product', ['headers' => ['X-Api-Key' => $this->apiKey], 'json' => $data], 'DELETE');;
    }
}