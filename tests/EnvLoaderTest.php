<?php

namespace Tests;

use PHPUnit\Framework\TestCase;
use DarlanSchmeller\EnvLoader\EnvLoader;

class EnvLoaderTest extends TestCase
{
    private string $envFile;

    protected function setUp(): void
    {
        parent::setUp();
        $this->envFile = sys_get_temp_dir() . '/test.env';
        $_ENV = [];
    }

    protected function tearDown(): void
    {
        if (file_exists($this->envFile)) {
            unlink($this->envFile);
        }
        parent::tearDown();
    }

    private function writeEnv(string $content): void
    {
        file_put_contents($this->envFile, $content);
    }

    public function test_it_throws_exception_if_env_file_does_not_exist(): void
    {
        $this->expectException(\RuntimeException::class);
        (new EnvLoader('/path/does/not/exist.env'))->load();
    }

    public function test_it_loads_basic_key_value_pairs(): void
    {
        $this->writeEnv("APP_NAME=MyApp\nAPP_ENV=production");
        $vars = EnvLoader::loadFrom($this->envFile);
        $this->assertSame('MyApp', $vars['APP_NAME']);
        $this->assertSame('production', $vars['APP_ENV']);
    }

    public function test_it_casts_booleans(): void
    {
        $this->writeEnv("DEBUG=true\nENABLED=false");
        $vars = EnvLoader::loadFrom($this->envFile);
        $this->assertTrue($vars['DEBUG']);
        $this->assertFalse($vars['ENABLED']);
    }

    public function test_it_casts_numbers_and_null(): void
    {
        $this->writeEnv("INT=42\nFLOAT=3.14\nEMPTY=\nNULL=null");
        $vars = EnvLoader::loadFrom($this->envFile);
        $this->assertSame(42, $vars['INT']);
        $this->assertSame(3.14, $vars['FLOAT']);
        $this->assertNull($vars['EMPTY']);
        $this->assertNull($vars['NULL']);
    }

    public function test_it_handles_quoted_strings(): void
    {
        $this->writeEnv("Q1=\"hello\"\nQ2='world'");
        $vars = EnvLoader::loadFrom($this->envFile);
        $this->assertSame('hello', $vars['Q1']);
        $this->assertSame('world', $vars['Q2']);
    }

    public function test_it_ignores_inline_comments(): void
    {
        $this->writeEnv("HOST=localhost # comment here");
        $vars = EnvLoader::loadFrom($this->envFile);
        $this->assertSame('localhost', $vars['HOST']);
    }

    public function test_it_sets_values_in_global_env(): void
    {
        $this->writeEnv("FOO=bar");
        EnvLoader::loadFrom($this->envFile);
        $this->assertSame('bar', $_ENV['FOO']);
    }
}
