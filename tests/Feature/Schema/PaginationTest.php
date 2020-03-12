<?php

namespace Tests\Feature\Schema;

use Apitizer\Apitizer;
use Illuminate\Pagination\LengthAwarePaginator;
use Tests\Feature\Models\User;
use Tests\Feature\TestCase;
use Tests\Support\Schemas\UserSchema;

class PaginationTest extends TestCase
{
    /** @test */
    public function it_returns_paginated_responses()
    {
        $user = factory(User::class)->create();
        $request = $this->request()->fields('id,name')->make();
        $paginator = UserSchema::make($request)->paginate();

        $this->assertInstanceOf(LengthAwarePaginator::class, $paginator);
        $this->assertEquals([$user->only('id', 'name')], $paginator->getCollection()->all());
        $this->assertEquals($user->getPerPage(), $paginator->perPage());
    }

    /** @test */
    public function pagination_links_contain_all_supported_query_parameters()
    {
        factory(User::class, 2)->create();
        $request = $this->request()
                        ->fields('id')
                        ->filter('active', 1)
                        ->sort('id')
                        ->limit(1)
                        ->make();

        $paginator = UserSchema::make($request)->paginate();

        $this->assertNotNull($paginator->nextPageUrl());
        $this->paginatorLinkContainsString($paginator, Apitizer::getFieldKey() . '=id');
        $this->paginatorLinkContainsString($paginator, Apitizer::getSortKey() . '=id');
        $this->paginatorLinkContainsString($paginator, Apitizer::getFilterKey() . '[active]=1');
        $this->paginatorLinkContainsString($paginator, 'limit=1');
    }

    private function paginatorLinkContainsString(LengthAwarePaginator $paginator, string $string)
    {
        $this->assertStringContainsStringIgnoringCase($string, urldecode($paginator->nextPageUrl()));
        $this->assertStringContainsStringIgnoringCase($string, urldecode($paginator->url(1)));
    }

    /** @test */
    public function the_pagination_limit_may_not_exceed_the_defined_limit()
    {
        $limit = 1;
        $request = $this->request()->limit($limit + 1)->make();
        $paginator = UserSchema::make($request)->setMaximumLimit($limit)->paginate();

        $this->assertEquals($limit, $paginator->perPage());
    }

    /** @test */
    public function the_pagination_limit_may_not_be_lower_than_1()
    {
        $request = $this->request()->limit(0)->make();
        $paginator = UserSchema::make($request)->paginate();

        $this->assertEquals(1, $paginator->perPage());
    }

    /** @test */
    public function paginate_accepts_a_custom_limit()
    {
        $request = $this->request()->make();
        $paginator = UserSchema::make($request)->paginate(1);
        $this->assertEquals(1, $paginator->perPage());
    }

    /** @test */
    public function the_custom_limit_may_not_surpass_the_maximum_limit()
    {
        // If you do want it to exceed the defined limit, call setMaximumLimit first.
        $request = $this->request()->make();
        $paginator = UserSchema::make($request)->paginate(1000);
        $this->assertEquals(1, $paginator->perPage());
    }

    /** @test */
    public function the_paginator_returns_the_requested_number_of_rows()
    {
        $users = factory(User::class, 2)->create();

        $request = $this->request()->limit(1)->fields('id')->make();
        $paginator = UserSchema::make($request)->paginate();

        $this->assertEquals(1, $paginator->perPage());
        $this->assertCount(1, $paginator->getCollection());
        $this->assertEquals([$users->first()->only('id')], $paginator->toArray()['data']);
    }
}
