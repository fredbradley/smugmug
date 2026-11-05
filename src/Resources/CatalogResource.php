<?php

namespace FredBradley\SmugMug\Resources;

use FredBradley\SmugMug\Data\Commerce\BatchAddResult;
use FredBradley\SmugMug\Data\Commerce\CatalogCategory;
use FredBradley\SmugMug\Data\Commerce\CatalogProduct;
use FredBradley\SmugMug\Data\Commerce\CatalogSku;
use FredBradley\SmugMug\Data\Commerce\CatalogSkuPrice;
use FredBradley\SmugMug\Data\Commerce\CatalogSkuType;
use FredBradley\SmugMug\Data\Commerce\CatalogVendor;
use FredBradley\SmugMug\Data\Data;
use FredBradley\SmugMug\Listing;
use FredBradley\SmugMug\Query\Options;
use FredBradley\SmugMug\Requests\Catalog\BatchAddCatalogSkuRequest;
use FredBradley\SmugMug\Requests\Catalog\BuyCatalogSkuRequest;
use FredBradley\SmugMug\Requests\Catalog\GetCatalogCategoriesRequest;
use FredBradley\SmugMug\Requests\Catalog\GetCatalogCategoryRequest;
use FredBradley\SmugMug\Requests\Catalog\GetCatalogPrintVendorPricesRequest;
use FredBradley\SmugMug\Requests\Catalog\GetCatalogPrintVendorProductsRequest;
use FredBradley\SmugMug\Requests\Catalog\GetCatalogPrintVendorRequest;
use FredBradley\SmugMug\Requests\Catalog\GetCatalogProductAddonsRequest;
use FredBradley\SmugMug\Requests\Catalog\GetCatalogProductOptionsRequest;
use FredBradley\SmugMug\Requests\Catalog\GetCatalogProductRequest;
use FredBradley\SmugMug\Requests\Catalog\GetCatalogSkuPriceRequest;
use FredBradley\SmugMug\Requests\Catalog\GetCatalogSkuRequest;
use FredBradley\SmugMug\Requests\Catalog\GetCatalogSkuTypeRequest;
use FredBradley\SmugMug\Requests\Catalog\GetCatalogSubCategoriesRequest;
use FredBradley\SmugMug\Requests\Catalog\GetCatalogSubCategoryRequest;
use FredBradley\SmugMug\Requests\Catalog\GetCatalogVendorProductsRequest;
use FredBradley\SmugMug\Requests\Catalog\GetCatalogVendorRequest;

/**
 * SmugMug's catalogue of products: vendors, categories, products and SKUs, from SmugMug::catalog().
 */
class CatalogResource extends BaseResource
{
    /**
     * A vendor of the catalogue, such as SmugMug: GET /api/v2/catalog/vendor/{vendorId}.
     *
     * @param  Options|array<string, mixed>  $options
     */
    public function vendor(string $vendorId, Options|array $options = []): ?CatalogVendor
    {
        return $this->fetch(new GetCatalogVendorRequest($vendorId, $this->options($options)));
    }

    /**
     * A vendor's product categories: GET /api/v2/catalog/vendor/{vendorId}!categories.
     *
     * @param  Options|array<string, mixed>  $options
     * @return Listing<CatalogCategory>
     */
    public function categories(string $vendorId, Options|array $options = []): Listing
    {
        return $this->listing(fn (Options $o) => new GetCatalogCategoriesRequest($vendorId, $o), $options);
    }

    /**
     * A product category: GET /api/v2/catalog/vendor/{vendorId}/category/{categoryId}.
     *
     * @param  Options|array<string, mixed>  $options
     */
    public function category(string $vendorId, string $categoryId, Options|array $options = []): ?CatalogCategory
    {
        return $this->fetch(new GetCatalogCategoryRequest($vendorId, $categoryId, $this->options($options)));
    }

    /**
     * A category's subcategories: GET /api/v2/catalog/vendor/{vendorId}/category/{categoryId}!subcategories.
     *
     * @param  Options|array<string, mixed>  $options
     * @return Listing<CatalogCategory>
     */
    public function subCategories(string $vendorId, string $categoryId, Options|array $options = []): Listing
    {
        return $this->listing(fn (Options $o) => new GetCatalogSubCategoriesRequest($vendorId, $categoryId, $o), $options);
    }

    /**
     * A product subcategory: GET /api/v2/catalog/vendor/{vendorId}/category/{categoryId}/{subCategoryId}.
     *
     * @param  Options|array<string, mixed>  $options
     */
    public function subCategory(string $vendorId, string $categoryId, string $subCategoryId, Options|array $options = []): ?CatalogCategory
    {
        return $this->fetch(new GetCatalogSubCategoryRequest($vendorId, $categoryId, $subCategoryId, $this->options($options)));
    }

    /**
     * A vendor's products: GET /api/v2/catalog/vendor/{vendorId}!products.
     *
     *  - ShowDisabled: include disabled products
     *
     * @param  Options|array<string, mixed>  $options
     * @return Listing<CatalogProduct>
     */
    public function vendorProducts(string $vendorId, Options|array $options = []): Listing
    {
        return $this->listing(fn (Options $o) => new GetCatalogVendorProductsRequest($vendorId, $o), $options);
    }

    /**
     * A print lab: GET /api/v2/catalog/printvendor/{vendorId}.
     *
     *  - ApplyColorCorrection
     *  - PriceType: Base, Pro or Wholesale
     *
     * @param  Options|array<string, mixed>  $options
     */
    public function printVendor(string $vendorId, Options|array $options = []): ?CatalogVendor
    {
        return $this->fetch(new GetCatalogPrintVendorRequest($vendorId, $this->options($options)));
    }

    /**
     * A print lab's products: GET /api/v2/catalog/printvendor/{vendorId}!products.
     *
     *  - ShowDisabled: include disabled products
     *
     * @param  Options|array<string, mixed>  $options
     * @return Listing<CatalogProduct>
     */
    public function printVendorProducts(string $vendorId, Options|array $options = []): Listing
    {
        return $this->listing(fn (Options $o) => new GetCatalogPrintVendorProductsRequest($vendorId, $o), $options);
    }

    /**
     * A print lab's prices: GET /api/v2/catalog/printvendor/{vendorId}!prices.
     *
     *  - Currency
     *  - ApplyColorCorrection
     *  - PriceType: Base, Pro or Wholesale
     *
     * @param  Options|array<string, mixed>  $options
     * @return Listing<CatalogSkuPrice>
     */
    public function printVendorPrices(string $vendorId, Options|array $options = []): Listing
    {
        return $this->listing(fn (Options $o) => new GetCatalogPrintVendorPricesRequest($vendorId, $o), $options);
    }

    /**
     * A product, by id: GET /api/v2/catalog/product/{productId}.
     *
     * @param  Options|array<string, mixed>  $options
     */
    public function product(string $productId, Options|array $options = []): ?CatalogProduct
    {
        return $this->fetch(new GetCatalogProductRequest($productId, $this->options($options)));
    }

    /**
     * The add-ons sold with a product: GET /api/v2/catalog/product/{productId}!addons.
     *
     * @param  Options|array<string, mixed>  $options
     * @return Listing<CatalogSku>
     */
    public function productAddons(string $productId, Options|array $options = []): Listing
    {
        return $this->listing(fn (Options $o) => new GetCatalogProductAddonsRequest($productId, $o), $options);
    }

    /**
     * The options (SKUs) of a product: GET /api/v2/catalog/product/{productId}!options.
     *
     * @param  Options|array<string, mixed>  $options
     * @return Listing<CatalogSku>
     */
    public function productOptions(string $productId, Options|array $options = []): Listing
    {
        return $this->listing(fn (Options $o) => new GetCatalogProductOptionsRequest($productId, $o), $options);
    }

    /**
     * A SKU, by id: GET /api/v2/catalog/sku/{skuId}.
     *
     * @param  Options|array<string, mixed>  $options
     */
    public function sku(string $skuId, Options|array $options = []): ?CatalogSku
    {
        return $this->fetch(new GetCatalogSkuRequest($skuId, $this->options($options)));
    }

    /**
     * What a SKU costs: GET /api/v2/catalog/sku/{skuId}!price.
     *
     *  - Album, Image: the album or image it is sold from
     *  - Currency
     *  - ApplyColorCorrection
     *  - PriceType: Base, Pro or Wholesale
     *
     * @param  Options|array<string, mixed>  $options
     */
    public function skuPrice(string $skuId, Options|array $options = []): ?CatalogSkuPrice
    {
        return $this->fetch(new GetCatalogSkuPriceRequest($skuId, $this->options($options)));
    }

    /**
     * Add a SKU to the cart: POST /api/v2/catalog/sku/{skuId}!buy.
     *
     *  - Quantity (required)
     *  - Image, Album: the image (and album) to buy it for
     *  - CropLeft, CropTop, CropWidth, CropHeight, CropRotation, CropAcknowledged: the crop
     *  - Source
     *
     * @param  array<string, mixed>  $parameters
     * @param  Options|array<string, mixed>  $options
     * @return array<string, mixed>
     */
    public function buy(string $skuId, array $parameters = [], Options|array $options = []): array
    {
        return $this->fetch(new BuyCatalogSkuRequest($skuId, $parameters, $this->options($options)));
    }

    /**
     * Add a SKU to the cart for many images at once: POST /api/v2/catalog/sku/{skuId}!batchadd.
     *
     *  - Images (required): the images (a list of URIs)
     *  - Quantity (required)
     *  - FilterImagesOverTheCartItemLimit
     *
     * @param  array<string, mixed>|Data  $fields
     * @param  Options|array<string, mixed>  $options
     */
    public function batchAdd(string $skuId, array|Data $fields, Options|array $options = []): ?BatchAddResult
    {
        return $this->fetch(new BatchAddCatalogSkuRequest($skuId, $fields, $this->options($options)));
    }

    /**
     * A type of SKU, by type and code: GET /api/v2/catalog/skutype/{skuType}{typeCode*}.
     *
     * @param  Options|array<string, mixed>  $options
     */
    public function skuType(string $skuType, string $typeCode, Options|array $options = []): ?CatalogSkuType
    {
        return $this->fetch(new GetCatalogSkuTypeRequest($skuType, $typeCode, $this->options($options)));
    }
}
