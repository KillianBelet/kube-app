<?php

namespace Tests\Feature;

use Tests\TestCase;

class HealthTest extends TestCase
{
    /** @test */
    public function healthzRepondOkSansSession()
    {
        $response = $this->get('/healthz');

        $response->assertStatus(200);
        $this->assertSame('ok', $response->getContent());
        $this->assertEmpty($response->headers->getCookies());
    }

    /** @test */
    public function metricsRepondMemeSansBaseDeDonnees()
    {
        config(['database.connections.mysql.host' => '127.0.0.1', 'database.connections.mysql.port' => 1]);

        $response = $this->get('/metrics');

        $response->assertStatus(200);
        $this->assertStringContainsString('app_up 1', $response->getContent());
        $this->assertStringContainsString('app_db_up 0', $response->getContent());
        $this->assertEmpty($response->headers->getCookies());
    }
}
