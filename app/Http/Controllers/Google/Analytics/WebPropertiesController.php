<?php

namespace App\Http\Controllers\Google\Analytics;

use App\Http\Controllers\Controller;
use App\Models\Google\Analytics\WebProperty;
use App\JsonApi\Transformer\Google\Analytics\WebPropertyTransformer;
use App\JsonApi\Document\Google\Analytics\{
    WebPropertyDocument,
    WebPropertiesDocument
};
use Illuminate\Http\Request;
use Psr\Http\Message\ResponseInterface;
use WoohooLabs\Yin\JsonApi\JsonApi;

/**
 * Class WebPropertiesController
 * @package App\Http\Controllers
 */
class WebPropertiesController extends Controller
{
    /**
     * Get the list of properties
     *
     * @param string $accountId
     * @param Request $request
     * @param JsonApi $jsonApi
     * @return ResponseInterface
     */
    public function index($accountId, Request $request, JsonApi $jsonApi): ResponseInterface
    {
        /** @var \Illuminate\Support\Collection $properties */
        $properties = WebProperty::findByAccountId($accountId)
            ->latest('created_at')
            ->get()
            ->unique('webpropertyId');
        return $jsonApi->respond()->ok($this->createWebPropertiesDocument(), $properties);
    }

    /**
     * Get the list of properties
     *
     * @param Request $request
     * @param JsonApi $jsonApi
     * @param string $webPropertyId
     * @return ResponseInterface
     */
    public function history(Request $request, JsonApi $jsonApi, $webPropertyId): ResponseInterface
    {
        /** @var \Illuminate\Support\Collection $properties */
        $properties = WebProperty::findByWebPropertyId($webPropertyId)->paginate();
        return $jsonApi->respond()->ok($this->createWebPropertiesDocument(), $properties);
    }

    /**
     * Create properties document
     *
     * @return WebPropertiesDocument
     */
    protected function createWebPropertiesDocument()
    {
        return new WebPropertiesDocument($this->createWebPropertyTransformer());
    }

    /**
     * Create property resource transformer
     *
     * @return WebPropertyTransformer
     */
    protected function createWebPropertyTransformer()
    {
        return new WebPropertyTransformer();
    }

    /**
     * Get the list of webproperties
     *
     * @param string $accountId Account ID to retrieve web properties for. Can
     * either be a specific account ID or '~all', which refers to all the accounts
     * that user has access to.
     * @param Request $request
     * @return json
     */
    /*public function index($accountId, Request $request)
    {
      try {
          // returns instance of \Google_Service_Storage
          $analytics = Google::make('analytics');
          // Get the list of webproperties for the authorized user.
          $webproperties = $analytics->management_webproperties->listManagementWebproperties($accountId);
      } catch (Google_Service_Exception $e) {
          throw new GoogleServiceException($e->getMessage());
      }
      return $this->printResults($webproperties->getItems());
    }

    protected function printResults($webproperties)
    {
        $data = [];
        foreach ($webproperties as $webproperty) {
            $data[] = [
                'id' => $webproperty->getId(),
                'kind' => $webproperty->getKind(),
                'selfLink' => $webproperty->getSelfLink(),
                'accountId' => $webproperty->getAccountId(),
                'internalWebPropertyId' => $webproperty->getInternalWebPropertyId(),
                'name' => $webproperty->getName(),
                'websiteUrl' => $webproperty->getWebsiteUrl(),
                'level' => $webproperty->getLevel(),
                'profileCount' => $webproperty->getProfileCount(),
                'industryVertical' => $webproperty->getIndustryVertical(),
                'defaultProfileId' => $webproperty->getDefaultProfileId(),
                'created' => $webproperty->getCreated(),
                'updated' => $webproperty->getUpdated(),
                'starred' => $webproperty->getStarred(),
                'isUpdatedLastDay' => $this->isUpdatedLastDay($webproperty->getUpdated()),
                'isCreatedLastDay' => $this->isCreatedLastDay($webproperty->getCreated()),
            ];
        }

        $result = [
          'jsonapi' => [
            'version' => "1.0"
          ],
          'data' => $data,
        ];
        return json_encode($result);
    }*/

    /**
     * @return array
     */
    public function options()
    {
        return [];
    }
}