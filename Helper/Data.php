<?php

/**
 * @author JQ
 * @copyright Copyright (c) 2022 JQ
 * @package JQ_CategorySidebar
 */

namespace JQ\CategorySidebar\Helper;

use Magento\Framework\App\Config\ScopeConfigInterface;

class Data extends \Magento\Framework\App\Helper\AbstractHelper
{
    const XML_PATH_ENABLED                      = 'general/enabled';
    const XML_PATH_CATEGORY                     = 'general/category';
    const XML_PATH_CATEGORY_DEPTH_LEVEL         = 'general/categorydepth';

    /**
     * @var ScopeConfigInterface
     */
    protected $_scopeConfig;

    /**
     * @param \Magento\Framework\App\Helper\Context $context
     * @param ScopeConfigInterface $scopeConfig
     */
    public function __construct(\Magento\Framework\App\Helper\Context $context, ScopeConfigInterface $scopeConfig
    ) {
        $this->_scopeConfig = $scopeConfig;
        parent::__construct($context);
    }

    /**
     * @param $xmlPath
     * @param string $section
     *
     * @return string
     */
    public function getConfigPath($xmlPath, $section = 'categorysidebar')
    {
        return $section . '/' . $xmlPath;
    }

	 /**
     * Check if enabled
     *
     * @return string|null
     */
    public function isEnabled()
    {
        return $this->scopeConfig->getValue(
            $this->getConfigPath(self::XML_PATH_ENABLED),
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );
    }

	 /**
     * Get Category CategorySidebar
     *
     * @return string|null
     */
    public function getSidebarCategory()
    {
        return $this->scopeConfig->getValue(
            $this->getConfigPath(self::XML_PATH_CATEGORY),
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );
    }

	 /**
     * Get category depth level
     *
     * @return string|null
     */
    public function getCategoryDepthLevel()
    {
        return $this->scopeConfig->getValue(
            $this->getConfigPath(self::XML_PATH_CATEGORY_DEPTH_LEVEL),
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );
    }

}
