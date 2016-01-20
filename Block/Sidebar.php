<?php  namespace Sebwite\Sidebar\Block;

use Magento\Framework\View\Element\Template;

/**
 * Class:Sidebar
 * Sebwite\Sidebar\Block
 *
 * @author      Sebwite
 * @package     Sebwite\Sidebar
 * @copyright   Copyright (c) 2015, Sebwite. All rights reserved
 */
class Sidebar extends Template {

    /**
     * @var \Magento\Catalog\Helper\Category
     */
    protected $_categoryHelper;

    /**
     * @var \Magento\Framework\Registry
     */
    protected $_coreRegistry;

    /**
     * @var \Magento\Catalog\Model\Indexer\Category\Flat\State
     */
    protected $categoryFlatConfig;
    /**
     * @var \Magento\Catalog\Model\CategoryFactory
     */
    private $_categoryFactory;

    /**
     * @param Template\Context                                   $context
     * @param \Magento\Catalog\Helper\Category                   $categoryHelper
     * @param \Magento\Framework\Registry                        $registry
     * @param \Magento\Catalog\Model\Indexer\Category\Flat\State $categoryFlatState
     * @param \Magento\Catalog\Model\CategoryFactory             $categoryFactory
     * @param \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig
     * @param array                                              $data
     */
    public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
        \Magento\Catalog\Helper\Category $categoryHelper,
        \Magento\Framework\Registry $registry,
        \Magento\Catalog\Model\Indexer\Category\Flat\State $categoryFlatState,
        \Magento\Catalog\Model\CategoryFactory $categoryFactory,
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig,
        $data = []
    ) {
        $this->_categoryHelper = $categoryHelper;
        $this->_coreRegistry = $registry;
        $this->categoryFlatConfig = $categoryFlatState;
        $this->_categoryFactory = $categoryFactory;

        parent::__construct($context, $data);
    }

    /*
    * Get owner name
    * @return string
    */

    /**
     * Get all categories
     *
     * @param bool $sorted
     * @param bool $asCollection
     * @param bool $toLoad
     *
     * @return array|\Magento\Catalog\Model\ResourceModel\Category\Collection|\Magento\Framework\Data\Tree\Node\Collection
     */
    public function getCategories($sorted = false, $asCollection = false, $toLoad = true)
    {
        $cacheKey = sprintf('%d-%d-%d-%d', $this->getSelectedRootCategory(), $sorted, $asCollection, $toLoad);
        if (isset($this->_storeCategories[$cacheKey])) {
            return $this->_storeCategories[$cacheKey];
        }

        /**
         * Check if parent node of the store still exists
         */
        $category = $this->_categoryFactory->create();
        $storeCategories = $category->getCategories($this->getSelectedRootCategory(), $recursionLevel = 1, $sorted, $asCollection, $toLoad);

        $this->_storeCategories[$cacheKey] = $storeCategories;

        return $storeCategories;
    }

    public function getSelectedRootCategory()
    {
        $category = $this->_scopeConfig->getValue(
            'sebwite_sidebar/general/category'
        );

        if($category === null)
            return 1;

        return $category;
    }

    /**
     *
     */
    public function getChildCategoryView($category, $html = '')
    {
        // Check if category has children
        if($category->hasChildren()) {

            $childCategories = $this->getSubcategories($category);

            if(count($childCategories) > 0) {

                $html .= '<ul class="o-list o-list--unstyled ' . ($this->isActive($category) ? 'active' : '') .'">';

                // Loop through children categories
                foreach($childCategories as $childCategory) {

                    $html .= '<li>'; // ' . ($this->isActive($childCategory) ? 'class="is-active"' : '') . ' >';
                    $html .= '<a href="' . $this->getCategoryUrl($childCategory) . '" title="' . $childCategory->getName() . '">' . $childCategory->getName() . '</a>';

                    if($childCategory->hasChildren()) {
                        if($this->isActive($childCategory)) {
                            $html .= '<span class="expanded"></span>';
                        } else {
                            $html .= '<span class="expand"></span>';
                        }
                    }

                    $html .= '</li>';

                    if($childCategory->hasChildren()) {
                        $html .= $this->getChildCategoryView($childCategory);
                    }
                }
                $html .= '</ul>';
            }
        }

        return $html;
    }

    /**
     * Retrieve subcategories
     *
     * @param $category
     *
     * @return array
     */
    public function getSubcategories($category)
    {
        if ($this->categoryFlatConfig->isFlatEnabled() && $category->getUseFlatResource())
            return (array)$category->getChildrenNodes();

        return $category->getChildren();
    }

    /**
     * Get current category
     *
     * @param \Magento\Catalog\Model\Category $category
     * @return Category
     */
    public function isActive($category)
    {
        $activeCategory = $this->_coreRegistry->registry('current_category');

        if( ! $activeCategory)
            return false;

        // Check if this is the active category
        if ($this->categoryFlatConfig->isFlatEnabled() && $category->getUseFlatResource() AND
            $category->getId() == $activeCategory->getId())
            return true;

        // Check if a subcategory of this category is active
        $childrenIds = $category->getAllChildren(true);
        if(in_array($activeCategory->getId(), $childrenIds))
            return true;

        // Fallback - If Flat categories is not enabled the active category does not give an id
        return (($category->getName() == $activeCategory->getName()) ? true : false);
    }

    /**
     * Return Category Id for $category object
     *
     * @param $category
     * @return string
     */
    public function getCategoryUrl($category)
    {
        return $this->_categoryHelper->getCategoryUrl($category);
    }
}