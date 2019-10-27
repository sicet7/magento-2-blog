<?php
/**
 * Mageplaza
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Mageplaza.com license that is
 * available through the world-wide-web at this URL:
 * https://www.mageplaza.com/LICENSE.txt
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade this extension to newer
 * version in the future.
 *
 * @category    Mageplaza
 * @package     Mageplaza_Blog
 * @copyright   Copyright (c) Mageplaza (https://www.mageplaza.com/)
 * @license     https://www.mageplaza.com/LICENSE.txt
 */

namespace Mageplaza\Blog\Block\Category;

use Magento\Framework\DataObject;
use Magento\Framework\View\Element\Template;

;
use Magento\Framework\View\Element\Template\Context;
use Mageplaza\Blog\Helper\Data as HelperData;
use Mageplaza\Blog\Model\Category;
use Mageplaza\Blog\Model\CategoryFactory;
use Mageplaza\Blog\Model\ResourceModel\Category\Collection;
use Mageplaza\Blog\Model\ResourceModel\Category\CollectionFactory;

/**
 * Class Widget
 * @package Mageplaza\Blog\Block\Category
 */
class Menu extends Template
{
    /**
     * @var CollectionFactory
     */
    protected $categoryCollection;

    /**
     * @var CategoryFactory
     */
    protected $category;

    /**
     * @var HelperData
     */
    protected $helper;

    /**
     * Menu constructor.
     *
     * @param Context $context
     * @param CollectionFactory $collectionFactory
     * @param CategoryFactory $categoryFactory
     * @param HelperData $helperData
     * @param array $data
     */
    public function __construct(
        Context $context,
        CollectionFactory $collectionFactory,
        CategoryFactory $categoryFactory,
        HelperData $helperData,
        array $data = []
    ) {
        $this->categoryCollection = $collectionFactory;
        $this->category           = $categoryFactory;
        $this->helper = $helperData;
        parent::__construct($context, $data);
    }

    /**
     * @param $id
     *
     * @return DataObject[]
     */
    public function getChildCategory($id)
    {
        return $this->categoryCollection->create()->addAttributeToFilter('parent_id', $id)
            ->addAttributeToFilter('enabled', '1')->getItems();
    }

    /**
     * @return Collection
     */
    public function getCollections()
    {
        return $this->categoryCollection->create()
            ->addAttributeToFilter('level', '1')->addAttributeToFilter('enabled', '1');
    }

    /**
     * @param Category $parentCategory
     *
     * @return string
     */
    public function getMenuHtml($parentCategory)
    {
        $categoryUrl = $this->helper->getBlogUrl('category/'.$parentCategory->getUrlKey());
        $html = '<li class="level' . $parentCategory->getLevel() . ' category-item ui-menu-item" role="presentation">'
            . '<a href="' . $categoryUrl .'" class="ui-corner-all" tabindex="-1" role="menuitem">'
            . '<span>' . $parentCategory->getName() . '</span></a>';

        $childCategorys = $this->getChildCategory($parentCategory->getId());

        if (count($childCategorys) > 0) {
            $html .= '<ul class="level' . $parentCategory->getLevel() . ' submenu ui-menu ui-widget'
                . ' ui-widget-content ui-corner-all"'
                . ' role="menu" aria-expanded="false" style="display: none; top: 47px; left: -0.15625px;"'
                . ' aria-hidden="true">';

            /** @var Category $childCategory */
            foreach ($childCategorys as $childCategory) {
                $html .= $this->getMenuHtml($childCategory);
            }
            $html .= '</ul>';
        }
        $html .= '</li>';

        return $html;
    }

    /**
     * @return string
     */
    public function getBlogHomePageTitle()
    {
        return $this->helper->getBlogConfig('general/name') ?: __('Blog');
    }

    /**
     * @return string
     */
    public function getBlogHomeUrl()
    {
        return $this->helper->getBlogUrl('');
    }
}