<?php

/**
 * @author JQ
 * @copyright Copyright (c) 2022 JQ
 * @package JQ_CategorySidebar
*/

namespace JQ\CategorySidebar\Model\Config\Source;

/**
 * Class:Categories
 * JQ\CategorySidebar\Model\Config\Source
 *
 */
class Categories implements \Magento\Framework\Option\ArrayInterface {

    /**
     * Store categories cache
     *
     * @var array
     */
    protected $_storeCategories = [];
    /**
     * @var \Magento\Catalog\Model\CategoryFactory
     */
    private $_categoryFactory;

    /**
     * Categories constructor.
     *
     * @param \Magento\Catalog\Model\CategoryFactory $categoryFactory
     */
    public function __construct(\Magento\Catalog\Model\CategoryFactory $categoryFactory)
    {
        $this->_categoryFactory = $categoryFactory;
    }

    /**
     * Return array of options as value-label pairs
     *
     * @return array Format: array(array('value' => '<value>', 'label' => '<label>'), ...)
     */
    public function toOptionArray()
    {
        $cacheKey = sprintf('%d-%d-%d-%d', 1, false, false, true);
        if (isset($this->_storeCategories[$cacheKey])) {
            return $this->_storeCategories[$cacheKey];
        }

        /**
         * Check if parent node of the store still exists
         */
        $category = $this->_categoryFactory->create();
        $storeCategories = $category->getCategories(1, $recursionLevel = 1, false, false, true);

        $this->_storeCategories[$cacheKey] = $storeCategories;

        $resultArray = [];
        foreach($storeCategories as $category) {
            $resultArray[$category->getId()] = __($category->getName());
        }

		$resultArray['current_category_children'] = __('Current Category Children');
		$resultArray['current_category_parent_children'] = __('Current Category Parent Children');
        $resultArray['current_category_branch_only'] = __('Only Branch of Current Category');

        return $resultArray;
    }
}
