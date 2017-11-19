<?php
/**
 * @author Daniel Martinez <dani.dmd86@gmail.com>
 *
 * @package Dmd
 * @version 1
 */

/**
 * Class Dmd_MostExpensiveProducts_Block_Product_Widget_List
 */
class Dmd_MostExpensiveProducts_Block_Product_Widget_List extends Mage_Core_Block_Template implements Mage_Widget_Block_Interface
{
    /**
     * Number of product to be displayed in the top most expensive products.
     * @var integer
     */
    protected $numberProductsToDisplay;

    /**
     * Category where the most expensive products will be calculated.
     * @var Mage_Catalog_Model_Category
     */
    protected $category;

    /**
     * Load the category where the most expensive products will be calculated.
     *
     * @return Mage_Catalog_Model_Category
     */
    protected function getCategory()
    {
        if( !$this->category )
        {
            list($type, $category_id)   = explode('/', $this->getData('id_path'));
            $this->category = Mage::getModel('catalog/category')->load($category_id);
        }

        return $this->category;
    }

    /**
     * Get the category name of the most expensive products.
     *
     * @return string
     */
    public function getCategoryName()
    {
        return $this->getCategory()->getName();
    }

    /**
     * Get the number of products to display. Top X.
     *
     * @return integer
     */
    public function getNumberOfProductsToDisplay()
    {
        if( !$this->numberProductsToDisplay )
        {
            $this->numberProductsToDisplay = $this->getData('number_of_products');
        }
        return $this->numberProductsToDisplay;
    }

    /**
     * Get the top X most expensive products from a category.
     * The products must be in stock, visible or in a catalog and enabled.
     *
     * @return Mage_Catalog_Model_Resource_Product_Collection
     */
    public function getProducts()
    {
        $number_of_products = $this->getNumberOfProductsToDisplay();
        $products = $this->getCategory()
                        ->getProductCollection()
                        ->addAttributeToSelect('*')
                        ->addAttributeToFilter('status', Mage_Catalog_Model_Product_Status::STATUS_ENABLED)
                        ->addAttributeToFilter('visibility', Mage_Catalog_Model_Product_Visibility::VISIBILITY_BOTH)
                        ->setOrder('price', 'DESC')
                        ->setPageSize($number_of_products);

        Mage::getSingleton('cataloginventory/stock')->addInStockFilterToCollection($products);
        return $products;
    }
}