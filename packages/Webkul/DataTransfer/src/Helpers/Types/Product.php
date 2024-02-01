<?php

namespace Webkul\DataTransfer\Helpers\Types;

use Illuminate\Support\Arr;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Validator;
use Webkul\Attribute\Repositories\AttributeFamilyRepository;
use Webkul\Attribute\Repositories\AttributeRepository;
use Webkul\Attribute\Repositories\AttributeOptionRepository;
use Webkul\Category\Repositories\CategoryRepository;
use Webkul\Core\Rules\Decimal;
use Webkul\Core\Rules\Slug;
use Webkul\DataTransfer\Contracts\ImportBatch as ImportBatchContract;
use Webkul\DataTransfer\Helpers\Import;
use Webkul\DataTransfer\Helpers\Types\Product\SKUStorage;
use Webkul\DataTransfer\Repositories\ImportBatchRepository;
use Webkul\Inventory\Repositories\InventorySourceRepository;
use Webkul\Product\Models\Product as ProductModel;
use Webkul\Product\Repositories\ProductAttributeValueRepository;
use Webkul\Product\Repositories\ProductInventoryRepository;
use Webkul\Product\Repositories\ProductRepository;
use Webkul\Product\Repositories\ProductBundleOptionRepository;
use Webkul\Product\Repositories\ProductBundleOptionProductRepository;


class Product extends AbstractType
{
    /**
     * Product type simple
     */
    const PRODUCT_TYPE_SIMPLE = 'simple';

    /**
     * Product type virtual
     */
    const PRODUCT_TYPE_VIRTUAL = 'virtual';

    /**
     * Product type downloadable
     */
    const PRODUCT_TYPE_DOWNLOADABLE = 'downloadable';

    /**
     * Product type configurable
     */
    const PRODUCT_TYPE_CONFIGURABLE = 'configurable';

    /**
     * Product type bundle
     */
    const PRODUCT_TYPE_BUNDLE = 'bundle';

    /**
     * Product type grouped
     */
    const PRODUCT_TYPE_GROUPED = 'grouped';

    /**
     * Error code for invalid product type
     */
    const ERROR_INVALID_TYPE = 'invalid_product_type';

    /**
     * Error code for non existing SKU
     */
    const ERROR_SKU_NOT_FOUND_FOR_DELETE = 'sku_not_found_to_delete';

    /**
     * Error code for duplicate url key
     */
    const ERROR_DUPLICATE_URL_KEY = 'duplicated_url_key';

    /**
     * Error code for invalid attribute family code
     */
    const ERROR_INVALID_ATTRIBUTE_FAMILY_CODE = 'attribute_family_code_not_found';

    /**
     * Error message templates
     */
    protected array $messages = [
        self::ERROR_INVALID_TYPE                  => 'data_transfer::app.validation.errors.products.invalid-type',
        self::ERROR_SKU_NOT_FOUND_FOR_DELETE      => 'data_transfer::app.validation.errors.products.sku-not-found',
        self::ERROR_DUPLICATE_URL_KEY             => 'data_transfer::app.validation.errors.products.duplicate-url-key',
        self::ERROR_INVALID_ATTRIBUTE_FAMILY_CODE => 'data_transfer::app.validation.errors.products.invalid-attribute-family',
    ];

    /**
     * Resource link needed
     */
    protected bool $linkNeeded = true;

    /**
     * Permanent entity columns
     */
    protected array $permanentAttributes = ['sku'];

    /**
     * Permanent entity columns
     */
    protected string $masterAttributeCode = 'sku';

    /**
     * Permanent entity columns
     */
    protected mixed $attributeFamilies = [];

    /**
     * Permanent entity columns
     */
    protected mixed $attributes = [];

    /**
     * Permanent entity columns
     */
    protected array $typeFamilyAttributes = [];

    /**
     * Permanent entity columns
     */
    protected array $typeFamilyValidationRules = [];

    /**
     * Permanent entity columns
     */
    protected array $categories = [];

    /**
     * Permanent entity columns
     */
    protected array $urlKeys = [];

    /**
     * Permanent entity columns
     */
    protected array $validColumnNames = [
        'locale',
        'type',
        'parent_sku',
        'attribute_family_code',
        'categories',
        'tax_category_name',
        'inventories',
        'related_skus',
        'cross_sell_skus',
        'up_sell_skus',
        'configurable_variants',
        'bundle_options',
        'associated_skus',
    ];

    /**
     * Create a new helper instance.
     *
     * @return void
     */
    public function __construct(
        protected ImportBatchRepository $importBatchRepository,
        protected AttributeFamilyRepository $attributeFamilyRepository,
        protected AttributeRepository $attributeRepository,
        protected AttributeOptionRepository $attributeOptionRepository,
        protected CategoryRepository $categoryRepository,
        protected InventorySourceRepository $inventorySourceRepository,
        protected ProductRepository $productRepository,
        protected ProductAttributeValueRepository $productAttributeValueRepository,
        protected ProductInventoryRepository $productInventoryRepository,
        protected ProductBundleOptionRepository $productBundleOptionRepository,
        protected ProductBundleOptionProductRepository $productBundleOptionProductRepository,
        protected SKUStorage $skuStorage
    ) {
        parent::__construct($importBatchRepository);

        $this->initAttributes();
    }

    /**
     * Load all attributes and families to use later
     */
    protected function initAttributes(): void
    {
        $this->attributeFamilies = $this->attributeFamilyRepository->all();

        $this->attributes = $this->attributeRepository->all();

        foreach ($this->attributes as $key => $attribute) {
            $this->validColumnNames[] = $attribute->code;
        }
    }

    /**
     * Initialize Product error templates
     */
    protected function initErrorMessages(): void
    {
        foreach ($this->messages as $errorCode => $message) {
            $this->errorHelper->addErrorMessage($errorCode, trans($message));
        }

        parent::initErrorMessages();
    }

    /**
     * Retrieve valid column names
     */
    public function getValidColumnNames(): array
    {
        return $this->validColumnNames;
    }

    /**
     * Save validated batches
     */
    protected function saveValidatedBatches(): self
    {
        $source = $this->getSource();

        $source->rewind();

        $this->skuStorage->init();

        while ($source->valid()) {
            try {
                $rowData = $source->current();
            } catch (\InvalidArgumentException $e) {
                $source->next();

                continue;
            }

            $this->validateRow($rowData, $source->getCurrentRowNumber());

            $source->next();
        }

        $this->checkForDuplicateUrlKeys();

        parent::saveValidatedBatches();

        return $this;
    }

    /**
     * Start the import process
     */
    public function importBatch(ImportBatchContract $batch): bool
    {
        Event::dispatch('data_transfer.imports.batch.import.before', $batch);

        /**
         * Load SKU storage with batch skus
         */
        $this->skuStorage->load(Arr::pluck($batch->data, 'sku'));

        $products = [];

        $categories = [];

        $attributes = [];

        $inventories = [];

        foreach ($batch->data as $rowData) {
            $this->prepareProducts($rowData, $products);

            $this->prepareCategories($rowData, $categories);

            $this->prepareAttributeValues($rowData, $attributes);

            $this->prepareInventories($rowData, $inventories);
        }

        $this->saveProducts($products);

        $this->saveCategories($categories);

        $this->saveAttributeValues($attributes);

        $this->saveInventories($inventories);

        /**
         * Update import batch summary
         */
        $this->importBatchRepository->update([
            'state' => Import::STATE_PROCESSING,

            'summary'      => [
                'created' => $this->getCreatedItemsCount(),
                'updated' => $this->getUpdatedItemsCount(),
                'deleted' => $this->getDeletedItemsCount(),
            ],
        ], $batch->id);

        Event::dispatch('data_transfer.imports.batch.import.after', $batch);

        return true;
    }

    /**
     * Start the import product links process
     */
    public function importLinksBatch(ImportBatchContract $batch): bool
    {
        Event::dispatch('data_transfer.imports.batch.linking.before', $batch);

        /**
         * Load SKU storage with batch skus
         */
        $this->skuStorage->load(Arr::pluck($batch->data, 'sku'));

        $configurableVariants = [];

        $groupAssociations = [];

        $bundleOptions = [];

        $links = [];

        foreach ($batch->data as $rowData) {
            /**
             * Prepare configurable variants
             */
            $this->prepareConfigurableVariants($rowData, $configurableVariants);

            /**
             * Prepare products association for grouped product
             */
            $this->prepareGroupAssociations($rowData, $groupAssociations);

            /**
             * Prepare bundle options
             */
            $this->prepareBundleOptions($rowData, $bundleOptions);

            /**
             * Prepare products association for related, cross sell and up sell
             */
            $this->prepareLinks($rowData, $links);
        }

        $this->saveConfigurableVariants($configurableVariants);

        $this->saveGroupAssociations($groupAssociations);

        $this->saveBundleOptions($bundleOptions);

        $this->saveLinks($links);

        dd('END HERE');

        /**
         * Update import batch summary
         */
        $this->importBatchRepository->update([
            'state' => Import::STATE_COMPLETED,
        ], $batch->id);

        Event::dispatch('data_transfer.imports.batch.linking.after', $batch);

        return true;
    }

    /**
     * Prepare products from current batch
     */
    public function prepareProducts(array $rowData, array &$products): void
    {
        $attributeFamilyId = $this->attributeFamilies
            ->where('code', $rowData['attribute_family_code'])
            ->first()->id;

        if ($this->isSKUExist($rowData['sku'])) {
            $products['update'][] = [
                'type'                => $rowData['type'],
                'sku'                 => $rowData['sku'],
                'attribute_family_id' => $attributeFamilyId,
            ];
        } else {
            $products['insert'][$rowData['sku']] = [
                'type'                => $rowData['type'],
                'sku'                 => $rowData['sku'],
                'attribute_family_id' => $attributeFamilyId,
                'created_at'          => $rowData['created_at'] ?? now(),
                'updated_at'          => $rowData['updated_at'] ?? now(),
            ];
        }
    }

    /**
     * Save products from current batch
     */
    public function saveProducts(array $products): void
    {
        if (! empty($products['update'])) {
            $this->updatedItemsCount += count($products['update']);

            $this->productRepository->upsert(
                $products['update'],
                $this->masterAttributeCode
            );
        }

        if (! empty($products['insert'])) {
            $this->createdItemsCount += count($products['insert']);

            $this->productRepository->insert($products['insert']);

            /**
             * Update the sku storage with newly created products
             */
            $newProducts = $this->productRepository->findWhereIn(
                'sku',
                array_keys($products['insert']),
                [
                    'id',
                    'type',
                    'sku',
                    'attribute_family_id',
                ]
            );

            foreach ($newProducts as $product) {
                $this->skuStorage->set($product->sku, [
                    'id'                  => $product->id,
                    'type'                => $product->type,
                    'attribute_family_id' => $product->attribute_family_id,
                ]);
            }
        }
    }

    /**
     * Prepare categories from current batch
     */
    public function prepareCategories(array $rowData, array &$categories): void
    {
        if (empty($rowData['categories'])) {
            return;
        }

        $names = explode('/', $rowData['categories'] ?? '');

        $categoryIds = [];

        foreach ($names as $name) {
            if (isset($this->categories[$name])) {
                $categoryIds = array_merge($categoryIds, $this->categories[$name]);

                continue;
            }

            $this->categories[$name] = $this->categoryRepository
                ->whereTranslation('name', $name)
                ->pluck('id')
                ->toArray();

            $categoryIds = array_merge($categoryIds, $this->categories[$name]);
        }

        $categories[$rowData['sku']] = $categoryIds;
    }

    /**
     * Save categories from current batch
     */
    public function saveCategories(array $categories): void
    {
        if ( empty($categories)) {
            return;
        }

        $productCategories = [];

        foreach ($categories as $sku => $categoryIds) {
            $product = $this->skuStorage->get($sku);

            foreach ($categoryIds as $categoryId) {
                $productCategories[] = [
                    'product_id'  => $product['id'],
                    'category_id' => $categoryId,
                ];
            }
        }

        DB::table('product_categories')->upsert(
            $productCategories,
            [
                'product_id',
                'category_id',
            ],
        );
    }

    /**
     * Save products from current batch
     */
    public function prepareAttributeValues(array $rowData, array &$attributes): array
    {
        $data = [];

        $familyAttributes = $this->getProductTypeFamilyAttributes($rowData['type'], $rowData['attribute_family_code']);

        foreach ($rowData as $attributeCode => $value) {
            if (is_null($value)) {
                continue;
            }

            $attribute = $familyAttributes->where('code', $attributeCode)->first();

            if (! $attribute) {
                continue;
            }

            $attributeTypeValues = array_fill_keys(array_values($attribute->attributeTypeFields), null);

            $attributes[$rowData['sku']][$attribute->id] = array_merge($attributeTypeValues, [
                'attribute_id'          => $attribute->id,
                $attribute->column_name => $value,
                'channel'               => $attribute->value_per_channel ? ($rowData['channel'] ?? 'default') : null,
                'locale'                => $attribute->value_per_locale ? $rowData['locale'] : null,
            ]);
        }

        return $attributes;
    }

    /**
     * Save products from current batch
     */
    public function saveAttributeValues(array $attributes): void
    {
        $attributeValues = [];

        foreach ($attributes as $sku => $skuAttributes) {
            foreach ($skuAttributes as $attribute) {
                $product = $this->skuStorage->get($sku);

                $attribute['product_id'] = (int) $product['id'];

                $attribute['unique_id'] = implode('|', array_filter([
                    $attribute['channel'],
                    $attribute['locale'],
                    $attribute['product_id'],
                    $attribute['attribute_id'],
                ]));

                $attributeValues[] = $attribute;
            }
        }

        $this->productAttributeValueRepository->upsert($attributeValues, 'unique_id');
    }

    /**
     * Prepare inventories
     */
    public function prepareInventories(array $rowData, array &$inventories): void
    {
        if (empty($rowData['inventories'])) {
            return;
        }

        $inventorySources = explode(',', $rowData['inventories'] ?? '');

        foreach ($inventorySources as $inventorySource) {
            [$inventorySource, $qty] = explode('=', $inventorySource ?? '');

            $inventories[$rowData['sku']][] = [
                'source' => $inventorySource,
                'qty'    => $qty,
            ];
        }
    }

    /**
     * Save inventories from current batch
     */
    public function saveInventories(array $inventories): void
    {
        if (empty($inventories)) {
            return;
        }

        $inventorySources = $this->inventorySourceRepository
            ->findWhereIn('code', Arr::flatten(Arr::pluck($inventories, '*.source')));

        $productInventories = [];

        foreach ($inventories as $sku => $skuInventories) {
            $product = $this->skuStorage->get($sku);

            foreach ($skuInventories as $inventory) {
                $inventorySource = $inventorySources->where('code', $inventory['source'])->first();

                if (! $inventorySource) {
                    continue;
                }

                $productInventories[] = [
                    'inventory_source_id' => $inventorySource->id,
                    'product_id'          => $product['id'],
                    'qty'                 => $inventory['qty'],
                    'vendor_id'           => 0,
                ];
            }
        }

        $this->productInventoryRepository->upsert(
            $productInventories,
            [
                'product_id',
                'inventory_source_id',
                'vendor_id',
            ],
        );
    }

    /**
     * Prepare configurable variants
     */
    public function prepareConfigurableVariants(array $rowData, array &$configurableVariants): void
    {
        if (
            $rowData['type'] != self::PRODUCT_TYPE_CONFIGURABLE
            && empty($rowData['configurable_variants'])
        ) {
            return;
        }

        $variants = explode('|', $rowData['configurable_variants']);

        foreach ($variants as $variant) {
            parse_str(str_replace(',', '&', $variant), $variantAttributes);

            $configurableVariants[$rowData['sku']][$variantAttributes['sku']] = Arr::except($variantAttributes, 'sku');
        }
    }

    /**
     * Save configurable variants from current batch
     */
    public function saveConfigurableVariants(array $configurableVariants): void
    {
        if (empty($configurableVariants)) {
            return;
        }

        $variantSkus = array_map('array_keys', $configurableVariants);

        /**
         * Load not loaded SKUs to the sku storage
         */
        $this->loadUnloadedSKUs(array_unique(Arr::flatten($variantSkus)));

        $superAttributeOptions = $this->getSuperAttributeOptions($configurableVariants);

        $parentAssociations = [];

        $superAttributes = [];

        $superAttributeValues = [];

        foreach ($configurableVariants as $sku => $variants) {
            $product = $this->skuStorage->get($sku);

            foreach ($variants as $variantSku => $variantSuperAttributes) {
                $variant = $this->skuStorage->get($variantSku);

                $parentAssociations[] = [
                    'sku'       => $variantSku,
                    'parent_id' => $product['id'],
                ];

                foreach ($variantSuperAttributes as $superAttributeCode => $optionLabel) {
                    $attribute = $this->attributes->where('code', $superAttributeCode)->first();

                    $attributeOption = $superAttributeOptions->where('attribute_id', $attribute->id)
                        ->where('admin_name', $optionLabel)
                        ->first();
                    
                    $attributeTypeValues = array_fill_keys(array_values($attribute->attributeTypeFields), null);

                    $attributeTypeValues = array_merge($attributeTypeValues, [
                        'product_id'            => $variant['id'],
                        'attribute_id'          => $attribute->id,
                        $attribute->column_name => $attributeOption->id,
                        'channel'               => $attribute->value_per_channel ? ($rowData['channel'] ?? 'default') : null,
                        'locale'                => $attribute->value_per_locale ? ($rowData['locale'] ?? 'en') : null,
                    ]);

                    $attributeTypeValues['unique_id'] = implode('|', array_filter([
                        $attributeTypeValues['channel'],
                        $attributeTypeValues['locale'],
                        $attributeTypeValues['product_id'],
                        $attributeTypeValues['attribute_id'],
                    ]));

                    $superAttributeValues[] = $attributeTypeValues;
                }
            }

            $superAttributeCodes = array_keys(current($variants));

            foreach ($superAttributeCodes as $attributeCode) {
                $attribute = $this->attributes->where('code', $attributeCode)->first();

                $superAttributes[] = [
                    'product_id'   => $product['id'],
                    'attribute_id' => $attribute->id,
                ];
            }
        }

        /**
         * Save the variants parent associations
         */
        $this->productRepository->upsert($parentAssociations, 'sku');

        /**
         * Save super attributes associations for configurable products
         */
        DB::table('product_super_attributes')->upsert(
            $superAttributes,
            [
                'product_id',
                'attribute_id',
            ],
        );

        /**
         * Save variants super attributes option values
         */
        $this->productAttributeValueRepository->upsert($superAttributeValues, 'unique_id');
    }

    /**
     * Prepare group associations
     */
    public function prepareGroupAssociations(array $rowData, array &$groupAssociations): void
    {
        if (
            $rowData['type'] != self::PRODUCT_TYPE_GROUPED
            && empty($rowData['associated_skus'])
        ) {
            return;
        }

        $associatedSkus = explode(',', $rowData['associated_skus']);

        foreach ($associatedSkus as $row) {
            [$sku, $qty] = explode('=', $row);

            $groupAssociations[$rowData['sku']][$sku] = $qty;
        }
    }

    /**
     * Save links from current batch
     */
    public function saveGroupAssociations(array $groupAssociations): void
    {
        if (empty($groupAssociations)) {
            return;
        }

        $associatedSkus = array_map('array_keys', $groupAssociations);

        /**
         * Load not loaded SKUs to the sku storage
         */
        $this->loadUnloadedSKUs(array_unique(Arr::flatten($associatedSkus)));

        $associatedProducts = [];

        foreach ($groupAssociations as $sku => $associatedSkus) {
            $product = $this->skuStorage->get($sku);

            $sortOrder = 0;

            foreach ($associatedSkus as $associatedSku => $qty) {
                $associatedProduct = $this->skuStorage->get($associatedSku);

                if (! $associatedProduct) {
                    continue;
                }

                $associatedProducts[] = [
                    'qty'                   => $qty,
                    'sort_order'            => $sortOrder++,
                    'product_id'            => $product['id'],
                    'associated_product_id' => $associatedProduct['id'],
                ];
            }
        }

        DB::table('product_grouped_products')->upsert(
            $associatedProducts,
            [
                'product_id',
                'associated_product_id',
            ],
        );
    }

    /**
     * Prepare bundle options from current batch
     */
    public function prepareBundleOptions(array $rowData, array &$bundleOptions): void
    {
        if (
            $rowData['type'] != self::PRODUCT_TYPE_BUNDLE
            && empty($rowData['bundle_options'])
        ) {
            return;
        }

        $options = explode('|', $rowData['bundle_options']);

        $optionSortOrder = 0;

        foreach ($options as $option) {
            parse_str(str_replace(',', '&', $option), $attributes);

            if (! isset($bundleOptions[$rowData['sku']][$attributes['name']])) {
                $productSortOrder = 0;

                $bundleOptions[$rowData['sku']][$attributes['name']]['attributes'] = [
                    'type'        => $attributes['type'],
                    'is_required' => $attributes['required'],
                    'sort_order'  => $optionSortOrder++,
                ];
            }

            $bundleOptions[$rowData['sku']][$attributes['name']]['skus'][$attributes['sku']] = [
                'qty'        => $attributes['qty'],
                'is_default' => $attributes['default'],
                'sort_order' => $productSortOrder++,
            ];
        }
    }

    /**
     * Save bundle options from current batch
     */
    public function saveBundleOptions(array &$bundleOptions): void
    {
        /**
         * TODO: Implement bundle products sku loading
         * 
         * Load not loaded SKUs to the sku storage
         */

        $upsertData = [];

        $existingOptions = $this->getExistingBundleOptions($bundleOptions);

        foreach ($bundleOptions as $sku => $options) {
            $product = $this->skuStorage->get($sku);

            foreach ($options as $optionName => $option) {
                $bundleOption = $existingOptions->where('product_id', $product['id'])
                    ->where('label', $optionName)
                    ->first();

                if (! $bundleOption) {
                    $bundleOption = $this->productBundleOptionRepository->create([
                        'product_id'  => $product['id'],
                        'type'        => $option['attributes']['type'],
                        'is_required' => $option['attributes']['is_required'],
                        'sort_order'  => $option['attributes']['sort_order'],
                    ]);
                } else {
                    $upsertData['options'][] = [
                        'id'          => $bundleOption->id,
                        'product_id'  => $product['id'],
                        'type'        => $option['attributes']['type'],
                        'is_required' => $option['attributes']['is_required'],
                        'sort_order'  => $option['attributes']['sort_order'],
                    ];
                }

                $upsertData['translations'][] = [
                    'product_bundle_option_id' => $bundleOption->id,
                    'label'                    => $optionName,
                    'locale'                   => 'en',
                ];

                foreach ($option['skus'] as $associatedSKU => $optionProduct) {
                    $associatedProduct = $this->skuStorage->get($associatedSKU);

                    $upsertData['products'][] = [
                        'product_bundle_option_id' => $bundleOption->id,
                        'product_id'               => $associatedProduct['id'],
                        'qty'                      => $optionProduct['qty'],
                        'is_default'               => $optionProduct['is_default'],
                        'sort_order'               => $optionProduct['sort_order'],
                    ];
                }
            }
        }

        if (! empty($upsertData['options'])) {
            $this->productBundleOptionRepository->upsert($upsertData['options'], 'id');
        }

        if (! empty($upsertData['products'])) {
            DB::table('product_bundle_option_translations')->upsert(
                $upsertData['translations'],
                [
                    'product_bundle_option_id',
                    'label',
                    'locale',
                ],
            );
        }

        if (! empty($upsertData['products'])) {
            $this->productBundleOptionProductRepository->upsert(
                $upsertData['products'],
                [
                    'product_id',
                    'product_bundle_option_id',
                ],
            );
        }
    }

    /**
     * Prepare links from current batch
     */
    public function prepareLinks(array $rowData, array &$links): void
    {
        $linkTableMapping = [
            'related'    => 'product_relations',
            'cross_sell' => 'product_cross_sells',
            'up_sell'    => 'product_up_sells',
        ];

        foreach ($linkTableMapping as $type => $table) {
            if (empty($rowData[$type . '_skus'])) {
                continue;
            }

            foreach (explode(',', $rowData[$type . '_skus'] ?? '') as $sku) {
                $links[$table][$rowData['sku']][] = $sku;
            }
        }
    }

    /**
     * Save links from current batch
     */
    public function saveLinks(array $links): void
    {
        /**
         * Load not loaded SKUs to the sku storage
         */
        $this->loadUnloadedSKUs(array_unique(Arr::flatten($links)));

        foreach ($links as $table => $linksData) {
            $productLinks = [];

            foreach ($linksData as $sku => $linkedSkus) {
                $product = $this->skuStorage->get($sku);

                foreach ($linkedSkus as $linkedSku) {
                    $linkedProduct = $this->skuStorage->get($linkedSku);

                    if (! $linkedProduct) {
                        continue;
                    }

                    $productLinks[] = [
                        'parent_id' => $product['id'],
                        'child_id'  => $linkedProduct['id'],
                    ];
                }
            }

            DB::table($table)->upsert(
                $productLinks,
                [
                    'parent_id',
                    'child_id',
                ],
            );
        }
    }

    /**
     * Returns existing bundled options of current batch
     */
    public function getExistingBundleOptions(array $bundleOptions): mixed
    {
        $queryBuilder = $this->productBundleOptionRepository
            ->select('product_bundle_options.id', 'label', 'product_id', 'type', 'is_required', 'sort_order')
            ->leftJoin('product_bundle_option_translations', 'product_bundle_option_translations.product_bundle_option_id', 'product_bundle_options.id');

        foreach ($bundleOptions as $sku => $options) {
            $product = $this->skuStorage->get($sku);

            foreach ($options as $optionName => $option) {
                $queryBuilder->orWhere(function($query) use ($product, $optionName) {
                    $query->where('product_bundle_options.product_id', $product['id'])
                        ->where('product_bundle_option_translations.label', $optionName);
                });
            }
        }

        return $queryBuilder->get();
    }

    /**
     * Returns super attributes options of current batch
     */
    public function getSuperAttributeOptions(array $variants): mixed
    {
        $optionLabels = array_unique(Arr::flatten($variants));

        return $this->attributeOptionRepository->findWhereIn('admin_name', $optionLabels);
    }

    /**
     * Save links
     */
    public function loadUnloadedSKUs(array $skus): void
    {
        $notLoadedSkus = [];

        foreach ($skus as $sku) {
            if ($this->skuStorage->has($sku)) {
                continue;
            }

            $notLoadedSkus[] = $sku;
        }

        /**
         * Load not loaded SKUs to the sku storage
         */
        if (! empty($notLoadedSkus)) {
            $this->skuStorage->load($notLoadedSkus);
        }
    }

    /**
     * Validates row
     */
    public function validateRow(array $rowData, int $rowNumber): bool
    {
        /**
         * If row is already validated than no need for further validation
         */
        if (isset($this->validatedRows[$rowNumber])) {
            return ! $this->errorHelper->isRowInvalid($rowNumber);
        }

        $this->validatedRows[$rowNumber] = true;

        /**
         * If import action is replace than no need for further validation
         */
        if ($this->import->action == Import::ACTION_REPLACE) {
            if (! $this->isSKUExist($rowData['sku'])) {
                $this->skipRow($rowNumber, self::ERROR_SKU_NOT_FOUND_FOR_DELETE);

                return false;
            }
        }

        /**
         * If import action is delete than no need for further validation
         */
        if ($this->import->action == Import::ACTION_DELETE) {
            if (! $this->isSKUExist($rowData['sku'])) {
                $this->skipRow($rowNumber, self::ERROR_SKU_NOT_FOUND_FOR_DELETE);

                return false;
            }

            return true;
        }

        /**
         * Check if product type exists
         */
        if (! config('product_types.' . $rowData['type'])) {
            $this->skipRow($rowNumber, self::ERROR_INVALID_TYPE, 'type');

            return false;
        }

        /**
         * Check if attribute family exists
         */
        if (! $this->attributeFamilies->where('code', $rowData['attribute_family_code'])->first()) {
            $this->skipRow($rowNumber, self::ERROR_INVALID_ATTRIBUTE_FAMILY_CODE, 'attribute_family_code');

            return false;
        }

        if (! isset($this->typeFamilyValidationRules[$rowData['type']][$rowData['attribute_family_code']])) {
            $this->typeFamilyValidationRules[$rowData['type']][$rowData['attribute_family_code']] = $this->getValidationRules($rowData);
        }

        /**
         * Validate product attributes
         */
        $validator = Validator::make($rowData, $this->typeFamilyValidationRules[$rowData['type']][$rowData['attribute_family_code']]);

        if ($validator->fails()) {
            $failedAttributes = $validator->failed();

            foreach ($validator->errors()->getMessages() as $attributeCode => $message) {
                $errorCode = array_key_first($failedAttributes[$attributeCode] ?? []);

                $this->skipRow($rowNumber, $errorCode, $attributeCode, current($message));
            }
        }

        /**
         * Check if url_key is unique
         */
        if (
            empty($this->urlKeys[$rowData['url_key']])
            || ($this->urlKeys[$rowData['url_key']]['sku'] == $rowData['sku'])
        ) {
            $this->urlKeys[$rowData['url_key']] = [
                'sku'        => $rowData['sku'],
                'row_number' => $rowNumber,
            ];
        } else {
            $message = sprintf(
                trans($this->messages[self::ERROR_DUPLICATE_URL_KEY]),
                'url_key',
                $this->urlKeys[$rowData['url_key']]['sku']
            );

            $this->skipRow($rowNumber, self::ERROR_DUPLICATE_URL_KEY, 'url_key', $message);
        }

        /**
         * TODO: Needs to be implement
         * 
         * Check if configurable super attribute exists in the attribute family
         */

        return ! $this->errorHelper->isRowInvalid($rowNumber);
    }

    /**
     * Prepare validation rules
     */
    public function getValidationRules(array $rowData): array
    {
        $rules = [
            'sku'                => ['required', new Slug],
            'url_key'            => ['required'],
            'special_price_from' => ['nullable', 'date'],
            'special_price_to'   => ['nullable', 'date', 'after_or_equal:special_price_from'],
            'special_price'      => ['nullable', new Decimal, 'lt:price'],
        ];

        $attributes = $this->getProductTypeFamilyAttributes($rowData['type'], $rowData['attribute_family_code']);

        foreach ($attributes as $attribute) {
            if (in_array($attribute->code, ['sku', 'url_key'])) {
                continue;
            }

            $validations = [];

            if (! isset($rules[$attribute->code])) {
                $validations[] = $attribute->is_required ? 'required' : 'nullable';
            } else {
                $validations = $rules[$attribute->code];
            }

            if (
                $attribute->type == 'text'
                && $attribute->validation
            ) {
                if ($attribute->validation === 'decimal') {
                    $validations[] = new Decimal;
                } elseif ($attribute->validation === 'regex') {
                    $validations[] = 'regex:' . $attribute->regex;
                } else {
                    $validations[] = $attribute->validation;
                }
            }

            if ($attribute->type == 'price') {
                $validations[] = new Decimal;
            }

            if ($attribute->is_unique) {
                array_push($validations, function ($field, $value, $fail) use ($attribute, $rowData) {
                    $product = $this->skuStorage->get($rowData['sku']);

                    $count = $this->productAttributeValueRepository
                        ->where($attribute->column_name, $rowData[$attribute->code])
                        ->where('attribute_id', '=', $attribute->id)
                        ->where('product_attribute_values.product_id', '!=', $product['id'])
                        ->count('product_attribute_values.id');

                    if ($count) {
                        $fail(__('admin::app.catalog.products.index.already-taken', ['name' => ':attribute']));
                    }
                });
            }

            $rules[$attribute->code] = $validations;
        }

        return $rules;
    }

    /**
     * Check that url_keys are not assigned to other products in DB
     */
    protected function checkForDuplicateUrlKeys(): void
    {
        $products = $this->productRepository
            ->resetScope()
            ->select('products.id', 'product_attribute_values.text_value as url_key', 'products.sku')
            ->leftJoin('product_attribute_values', 'products.id', 'product_attribute_values.product_id')
            ->leftJoin('attributes', 'product_attribute_values.attribute_id', 'attributes.id')
            ->where('attributes.code', 'url_key')
            ->where('product_attribute_values.text_value', array_keys($this->urlKeys))
            ->whereNotIn('products.sku', Arr::pluck($this->urlKeys, 'sku'))
            ->get();

        foreach ($products as $product) {
            $this->skipRow(
                $this->urlKeys[$product->url_key]['row_number'],
                self::ERROR_DUPLICATE_URL_KEY,
                'url_key',
                sprintf(
                    trans($this->messages[self::ERROR_DUPLICATE_URL_KEY]),
                    $product->url_key,
                    $product->sku
                )
            );
        }
    }

    /**
     * Retrieve product type family attributes
     */
    public function getProductTypeFamilyAttributes(string $type, string $attributeFamilyCode): mixed
    {
        if (isset($this->typeFamilyAttributes[$type][$attributeFamilyCode])) {
            return $this->typeFamilyAttributes[$type][$attributeFamilyCode];
        }

        $attributeFamily = $this->attributeFamilies->where('code', $attributeFamilyCode)->first();

        $product = ProductModel::make([
            'type'                => $type,
            'attribute_family_id' => $attributeFamily->id,
        ]);

        return $this->typeFamilyAttributes[$type][$attributeFamilyCode] = $product->getEditableAttributes();
    }

    /**
     * Prepare row data to save into the database
     */
    protected function prepareRowForDb(array $rowData): array
    {
        $rowData = array_map(function ($value) {
            return $value === '' ? null : $value;
        }, $rowData);

        return $rowData;
    }

    /**
     * Check if SKU exists
     */
    public function isSKUExist(string $sku): bool
    {
        return $this->skuStorage->has($sku);
    }

    /**
     * Add row as skipped
     *
     * @param  int|null  $rowNumber
     * @param  string|null  $columnName
     * @param  string|null  $errorMessage
     * @return $this
     */
    private function skipRow($rowNumber, string $errorCode, $columnName = null, $errorMessage = null): self
    {
        $this->addRowError($errorCode, $rowNumber, $columnName, $errorMessage);

        $this->errorHelper->addRowToSkip($rowNumber);

        return $this;
    }
}
