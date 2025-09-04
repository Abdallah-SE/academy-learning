<?php

namespace Tests\Unit;

use Tests\TestCase;
use App\Exceptions\CustomException;
use Illuminate\Foundation\Testing\RefreshDatabase;

class CustomExceptionTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function it_can_create_custom_exception_with_default_values()
    {
        $exception = new CustomException();

        $this->assertInstanceOf(CustomException::class, $exception);
        $this->assertEquals('An unexpected error occurred', $exception->getMessage());
        $this->assertEquals(500, $exception->getCode());
        $this->assertEquals([], $exception->getContext());
    }

    /** @test */
    public function it_can_create_custom_exception_with_custom_values()
    {
        $message = 'Custom error message';
        $code = 400;
        $context = ['field' => 'email', 'value' => 'invalid'];

        $exception = new CustomException($message, $code, $context);

        $this->assertEquals($message, $exception->getMessage());
        $this->assertEquals($code, $exception->getCode());
        $this->assertEquals($context, $exception->getContext());
    }

    /** @test */
    public function it_can_get_context()
    {
        $context = ['user_id' => 123, 'action' => 'login'];
        $exception = new CustomException('Test message', 500, $context);

        $this->assertEquals($context, $exception->getContext());
    }

    /** @test */
    public function it_can_render_exception_to_json_response()
    {
        $exception = new CustomException('Test error', 422, ['field' => 'email']);

        $request = request();
        $response = $exception->render($request);

        $this->assertEquals(422, $response->getStatusCode());
        $this->assertJson($response->getContent());

        $data = json_decode($response->getContent(), true);
        $this->assertFalse($data['success']);
        $this->assertEquals('Test error', $data['message']);
        $this->assertEquals(422, $data['code']);
        $this->assertArrayHasKey('timestamp', $data);
    }

    /** @test */
    public function it_includes_debug_info_in_development()
    {
        config(['app.debug' => true]);

        $exception = new CustomException('Test error', 500, ['context' => 'test']);

        $request = request();
        $response = $exception->render($request);

        $data = json_decode($response->getContent(), true);
        $this->assertArrayHasKey('debug', $data);
        $this->assertArrayHasKey('file', $data['debug']);
        $this->assertArrayHasKey('line', $data['debug']);
        $this->assertArrayHasKey('context', $data['debug']);
    }

    /** @test */
    public function it_excludes_debug_info_in_production()
    {
        config(['app.debug' => false]);

        $exception = new CustomException('Test error', 500, ['context' => 'test']);

        $request = request();
        $response = $exception->render($request);

        $data = json_decode($response->getContent(), true);
        $this->assertArrayNotHasKey('debug', $data);
    }

    /** @test */
    public function it_logs_exception_when_created()
    {
        // Mock the Log facade
        $this->mock(\Illuminate\Support\Facades\Log::class)
            ->shouldReceive('error')
            ->once()
            ->withArgs(function ($message, $data) {
                return str_contains($message, 'Custom Exception: Test error') &&
                       $data['message'] === 'Test error' &&
                       $data['code'] === 400;
            });

        new CustomException('Test error', 400, ['test' => 'context']);
    }

    /** @test */
    public function it_can_report_exception()
    {
        $exception = new CustomException('Test error', 500);

        // This should not throw an error
        $exception->report();

        $this->assertTrue(true); // Assert that we reached this point
    }
}
