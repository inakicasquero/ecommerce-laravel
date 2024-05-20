<?php

namespace Webkul\Core;

use Illuminate\Support\Arr;
use Illuminate\Support\Collection;
use Webkul\Core\Menu\MenuItem;

class Menu
{
    /**
     * Menu items.
     */
    protected array $items = [];

    /**
     * Config menu.
     */
    protected array $configMenu = [];

    /**
     * Is admin menu.
     */
    protected bool $isForAdmin = false;

    /**
     * Contains current item route.
     */
    private string $current;

    /**
     * Contains current item key.
     */
    private string $currentKey = '';

    /**
     * Create a new instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->current = request()->url();
    }

    /**
     * Add a new menu item.
     */
    public function addItem(MenuItem $menuItem): void
    {
        $this->items[] = $menuItem;
    }

    /**
     * Get all menu items.
     */
    public function getItems(): Collection
    {
        if (! $this->items) {
            $this->prepareMenuItems();
        }

        return collect($this->removeUnauthorizedMenuItem())
            ->sortBy('sort');
    }

    /**
     * Get admin config.
     */
    public function forAdmin(): self
    {
        $this->isForAdmin = true;

        $this->configMenu = collect(config('menu.admin'))
            ->filter(fn ($item) => bouncer()->hasPermission($item['key']))->toArray();

        return $this;
    }

    /**
     * Get shop config.
     */
    public function forShop(): self
    {
        $isShowWishlist = ! (bool) core()->getConfigData('general.content.shop.wishlist_option');

        $this->configMenu = collect(config('menu.customer'))
            ->reject(fn ($item) => $item['key'] == 'account.wishlist' && $isShowWishlist)
            ->toArray();

        return $this;
    }

    /**
     * Prepare menu items.
     */
    private function prepareMenuItems(): void
    {
        $menuWithDotNotation = [];

        foreach ($this->configMenu as $item) {
            if (strpos($this->current, route($item['route'])) !== false) {
                $this->currentKey = $item['key'];
            }

            $menuWithDotNotation[$item['key']] = $item;
        }

        $menu = Arr::undot(Arr::dot($menuWithDotNotation));

        foreach ($menu as $menuItemKey => $menuItem) {
            $subMenuItems = $this->processSubMenuItems($menuItem);

            $this->addItem(new MenuItem(
                key: $menuItemKey,
                name: trans($menuItem['name']),
                route: $menuItem['route'],
                sort: $menuItem['sort'],
                icon: $menuItem['icon'],
                children: $subMenuItems,
            ));
        }
    }

    /**
     * Process sub menu items.
     */
    private function processSubMenuItems($menuItem): Collection
    {
        return collect($menuItem)
            ->sortBy('sort')
            ->filter(fn ($value) => is_array($value))
            ->map(function ($subMenuItem) {
                $subSubMenuItems = $this->processSubMenuItems($subMenuItem);

                return new MenuItem(
                    key: $subMenuItem['key'],
                    name: trans($subMenuItem['name']),
                    route: $subMenuItem['route'],
                    sort: $subMenuItem['sort'],
                    icon: $subMenuItem['icon'],
                    children: $subSubMenuItems,
                );
            });
    }

    /**
     * Get current active menu.
     */
    public function getCurrentActiveMenu(): ?MenuItem
    {
        $currentKey = implode('.', array_slice(explode('.', $this->currentKey), 0, 2));

        return $this->findMatchingItem($this->getItems(), $currentKey);
    }

    /**
     * Finding the matching item.
     */
    private function findMatchingItem($items, $currentKey): ?MenuItem
    {
        foreach ($items as $item) {
            if ($item->key == $currentKey) {
                return $item;
            }

            if ($item->haveChildren()) {
                $matchingChild = $this->findMatchingItem($item->getChildren(), $currentKey);

                if ($matchingChild) {
                    return $matchingChild;
                }
            }
        }

        return null;
    }

    /**
     * Remove unauthorized menu item.
     */
    private function removeUnauthorizedMenuItem(): array
    {
        if (! $this->isForAdmin) {
            return $this->items;
        }

        return collect($this->items)->map(function ($item) {
            $this->removeChildrenUnauthorizedMenuItem($item);

            return $item;
        })->toArray();
    }

    /**
     * Remove unauthorized menuItem's children. This will handle all levels.
     */
    private function removeChildrenUnauthorizedMenuItem(MenuItem &$menuItem): void
    {
        if ($menuItem->haveChildren()) {
            $firstChildrenItem = $menuItem->getChildren()->first();

            $menuItem->route = $firstChildrenItem->getRoute();

            $this->removeChildrenUnauthorizedMenuItem($firstChildrenItem);
        }
    }
}
