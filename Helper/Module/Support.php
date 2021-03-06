<?php

/*
 * @author     M2E Pro Developers Team
 * @copyright  2011-2015 ESS-UA [M2E Pro]
 * @license    Commercial use is forbidden
 */

namespace Ess\M2ePro\Helper\Module;

class Support extends \Ess\M2ePro\Helper\AbstractHelper
{
    const TYPE_BRONZE  = 'bronze';
    const TYPE_SILVER  = 'silver';
    const TYPE_GOLD    = 'gold';

    protected $urlBuilder;
    protected $modelFactory;
    protected $moduleConfig;
    protected $cacheConfig;

    //########################################

    public function __construct(
        \Magento\Backend\Model\UrlInterface $urlBuilder,
        \Ess\M2ePro\Model\Factory $modelFactory,
        \Ess\M2ePro\Model\Config\Manager\Cache $cacheConfig,
        \Ess\M2ePro\Model\Config\Manager\Module $moduleConfig,
        \Ess\M2ePro\Helper\Factory $helperFactory,
        \Magento\Framework\App\Helper\Context $context
    )
    {
        $this->urlBuilder = $urlBuilder;
        $this->modelFactory = $modelFactory;
        $this->moduleConfig = $moduleConfig;
        $this->cacheConfig = $cacheConfig;
        parent::__construct($helperFactory, $context);
    }

    //########################################

    public function getPageRoute()
    {
        return 'm2epro/'.$this->getPageControllerName().'/index';
    }

    public function getPageControllerName()
    {
        return 'support';
    }

    //########################################

    public function getWebsiteUrl()
    {
        return $this->moduleConfig->getGroupValue('/support/', 'website_url');
    }

    public function getClientsPortalUrl()
    {
        return $this->moduleConfig->getGroupValue('/support/', 'clients_portal_url');
    }

    public function getSupportUrl()
    {
        return $this->moduleConfig->getGroupValue('/support/', 'support_url');
    }

    //########################################

    public function getDocumentationUrl()
    {
        return $this->moduleConfig->getGroupValue('/support/', 'documentation_url');
    }

    public function getDocumentationComponentUrl($component)
    {
        switch ($component) {
            case \Ess\M2ePro\Helper\Component\Ebay::NICK:
                return $this->getDocumentationUrl() . '/display/eBayMagento2/';
            case \Ess\M2ePro\Helper\Component\Amazon::NICK:
                return $this->getDocumentationUrl() . 'display/AmazonMagento2/';
            default:
                throw new \Ess\M2ePro\Model\Exception\Logic('Invalid Channel.');
        }
    }

    public function getDocumentationArticleUrl($tinyLink)
    {
        return $this->getDocumentationUrl() . $tinyLink;
    }

    //----------------------------------------

    public function getKnowledgebaseUrl()
    {
        return $this->getSupportUrl() . 'knowledgebase/';
    }

    public function getKnowledgebaseComponentUrl($component)
    {
        switch ($component) {
            case \Ess\M2ePro\Helper\Component\Ebay::NICK:
                return $this->getKnowledgebaseUrl() . 'ebay/';
            case \Ess\M2ePro\Helper\Component\Amazon::NICK:
                return $this->getKnowledgebaseUrl() . 'amazon/';
            default:
                throw new \Ess\M2ePro\Model\Exception\Logic('Invalid Channel.');
        }
    }

    public function getKnowledgebaseArticleUrl($articleLink)
    {
        return $this->getKnowledgebaseUrl() . trim($articleLink, '/') . '/';
    }

    //----------------------------------------

    public function getIdeasUrl()
    {
        return $this->getSupportUrl() . 'ideas/';
    }

    public function getIdeasComponentUrl($component)
    {
        switch ($component) {
            case \Ess\M2ePro\Helper\Component\Ebay::NICK:
                return $this->getIdeasUrl() . 'ebay/';
            case \Ess\M2ePro\Helper\Component\Amazon::NICK:
                return $this->getIdeasUrl() . 'amazon/';
            default:
                throw new \Ess\M2ePro\Model\Exception\Logic('Invalid Channel.');
        }
    }

    public function getIdeasArticleUrl($articleLink)
    {
        return  $this->getIdeasUrl() . trim($articleLink, '/') . '/';
    }

    //----------------------------------------

    public function getForumUrl()
    {
        return $this->moduleConfig->getGroupValue('/support/', 'forum_url');
    }

    public function getForumComponentUrl($component)
    {
        switch ($component) {
            case \Ess\M2ePro\Helper\Component\Ebay::NICK:
                return $this->getForumUrl() . 'ebay/';
            case \Ess\M2ePro\Helper\Component\Amazon::NICK:
                return $this->getForumUrl() . 'amazon/';
            default:
                throw new \Ess\M2ePro\Model\Exception\Logic('Invalid Channel.');
        }
    }

    public function getForumArticleUrl($articleLink)
    {
        return $this->getForumUrl() . trim($articleLink, '/') . '/';
    }

    //########################################

    public function getMagentoMarketplaceUrl()
    {
        return $this->moduleConfig->getGroupValue('/support/', 'magento_marketplace_url');
    }

    //########################################

    public function getContactEmail()
    {
        $email = $this->moduleConfig->getGroupValue('/support/', 'contact_email');

        try {

            /** @var \Ess\M2ePro\Model\M2ePro\Connector\Dispatcher $dispatcherObject */
            $dispatcherObject = $this->modelFactory->getObject('M2ePro\Connector\Dispatcher');
            $connectorObj = $dispatcherObject->getVirtualConnector('settings','get','supportEmail');
            $dispatcherObject->process($connectorObj);
            $response = $connectorObj->getResponseData();

            if (!empty($response['email'])) {
                $email = $response['email'];
            }

        } catch (\Exception $exception) {
            $this->getHelper('Module\Exception')->process($exception);
        }

        return $email;
    }

    public function getType()
    {
        $type = $this->cacheConfig->getGroupValue('/support/premium/','type');
        $lastUpdateDate = $this->cacheConfig->getGroupValue('/support/premium/','last_update_time');

        if ($type && strtotime($lastUpdateDate) + 3600*24 > $this->getHelper('Data')->getCurrentGmtDate(true)) {
            return $type;
        }

        $type = self::TYPE_BRONZE;

        try {

            /** @var \Ess\M2ePro\Model\M2ePro\Connector\Dispatcher $dispatcherObject */
            $dispatcherObject = $this->modelFactory->getObject('M2ePro\Connector\Dispatcher');
            $connectorObj = $dispatcherObject->getVirtualConnector('settings','get','supportType');
            $dispatcherObject->process($connectorObj);
            $response = $connectorObj->getResponseData();

            !empty($response['type']) && $type = $response['type'];

        } catch (\Exception $exception) {
            $this->getHelper('Module\Exception')->process($exception);
        }

        $this->cacheConfig->setGroupValue('/support/premium/','type',$type);
        $this->cacheConfig->setGroupValue('/support/premium/','last_update_time',
            $this->getHelper('Data')->getCurrentGmtDate());

        return $type;
    }

    // ---------------------------------------

    public function isTypePremium()
    {
        return $this->getType() == self::TYPE_GOLD || $this->getType() == self::TYPE_SILVER;
    }

    //########################################
}