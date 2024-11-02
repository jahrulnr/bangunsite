<?php

namespace Tests\Feature;

use Tests\TestCase;

class apiTest extends TestCase
{
    /**
     * A basic feature test example.
     */
    public function test_example(): void
    {
        $hitPoin = [
            '/api/server/info',
            '/api/server/traffic',
            '/api/server/diskIO',
        ];

        foreach ($hitPoin as $endpoint) {
            $response = $this->post($endpoint);
            $response->assertStatus(200);
        }
    }
}
