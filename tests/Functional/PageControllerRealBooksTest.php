<?php

namespace App\Tests\Functional;

use App\Factory\UserFactory;
use App\Tests\Support\ImportsBooksTrait;

final class PageControllerRealBooksTest extends ApiTestCase
{
    use ImportsBooksTrait;

    protected function setUp(): void
    {
        parent::setUp();
        $this->importBooksIfNeeded();
    }

    public function test_can_get_books(): void
    {
        $admin = UserFactory::new()->asAdmin()->create();

        $this->browser()
            ->get('/api/books', [
                'server' => $this->authHeadersFor($admin->_real()),
            ])
            ->assertStatus(200);
    }
}