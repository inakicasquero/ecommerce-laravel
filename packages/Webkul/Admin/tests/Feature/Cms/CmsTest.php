<?php

use Webkul\CMS\Models\Page;

use function Pest\Laravel\deleteJson;
use function Pest\Laravel\get;
use function Pest\Laravel\getJson;
use function Pest\Laravel\postJson;
use function Pest\Laravel\putJson;

afterEach(function () {
    /**
     * Cleaning up rows which are created.
     */
    Page::query()
        ->whereNotBetween('id', [1, 11])
        ->delete();
});

it('should returns the cms page', function () {
    // Act and Assert
    $this->loginAsAdmin();

    get(route('admin.cms.index'))
        ->assertOk()
        ->assertSeeText(trans('admin::app.cms.index.title'))
        ->assertSeeText(trans('admin::app.cms.index.create-btn'));
});

it('should returns the listing cms', function () {
    // Act and Assert
    $this->loginAsAdmin();

    getJson(route('admin.cms.index'), [
        'X-Requested-With' => 'XMLHttpRequest',
    ])
        ->assertOk()
        ->assertJsonPath('records.0.id', 11)
        ->assertJsonPath('records.0.page_title', 'Privacy Policy')
        ->assertJsonPath('records.0.url_key', 'privacy-policy')
        ->assertJsonPath('meta.total', 11);
});

it('should create the new cms page', function () {
    // Act and Assert
    $this->loginAsAdmin();

    get(route('admin.cms.create'))
        ->assertOk()
        ->assertSeeText(trans('admin::app.cms.create.title'))
        ->assertSeeText(trans('admin::app.account.edit.back-btn'));
});

it('should store newly created cms pages', function () {
    // Act and Assert
    $this->loginAsAdmin();

    postJson(route('admin.cms.store'), [
        'url_key'      => $slug = fake()->slug(),
        'page_title'   => $pageTitle = fake()->title(),
        'html_content' => $htmlContent = substr(fake()->paragraph(), 0, 50),
        'channels'     => [
            'value' => 1,
        ],
    ])
        ->assertRedirect(route('admin.cms.index'))
        ->isRedirection();

    $this->assertDatabaseHas('cms_page_translations', [
        'url_key'      => $slug,
        'page_title'   => $pageTitle,
        'html_content' => $htmlContent,
    ]);
});

it('should show the edit cms page', function () {
    // Act and Assert
    $this->loginAsAdmin();

    $cms = Page::factory()->hasTranslations()->create();

    get(route('admin.cms.edit', $cms->id))
        ->assertOk()
        ->assertSeeText(trans('admin::app.cms.edit.title'))
        ->assertSeeText(trans('admin::app.cms.edit.back-btn'));
});

it('should update the cms page', function () {
    // Act and Assert
    $this->loginAsAdmin();

    $cms = Page::factory()->hasTranslations()->create();

    $localeCode = core()->getCurrentLocale()->code;

    putJson(route('admin.cms.update', $cms->id), [
        $localeCode => [
            'url_key'      => $cms->url_key,
            'page_title'   => $pageTitle = fake()->word(),
            'html_content' => $htmlContent = substr(fake()->paragraph(), 0, 50),
        ],

        'locale' => $localeCode,

        'channels' => [
            1,
        ],
    ])
        ->assertRedirect(route('admin.cms.index'))
        ->isRedirection();

    $this->assertDatabaseHas('cms_page_translations', [
        'url_key'      => $cms->url_key,
        'page_title'   => $pageTitle,
        'html_content' => $htmlContent,
    ]);
});

it('should delete the cms page', function () {
    // Act and Assert
    $this->loginAsAdmin();

    $cms = Page::factory()->hasTranslations()->create();

    deleteJson(route('admin.cms.delete', $cms->id))
        ->assertOk()
        ->assertSeeText(trans('admin::app.cms.delete-success'));

    $this->assertDatabaseMissing('cms_pages', [
        'id' => $cms->id,
    ]);
});

it('should mass delete cms pages', function () {
    // Act and Assert
    $this->loginAsAdmin();

    $cmsPages = Page::factory()->count(2)->hasTranslations()->create();

    postJson(route('admin.cms.mass_delete'), [
        'indices' => $cmsPages->pluck('id')->toArray(),
    ])
        ->assertOk()
        ->assertSeeText(trans('admin::app.cms.index.datagrid.mass-delete-success'));

    foreach ($cmsPages as $page) {
        $this->assertDatabaseMissing('cms_pages', [
            'id' => $page->id,
        ]);
    }
});
