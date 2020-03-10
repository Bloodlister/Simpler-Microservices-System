<?php declare(strict_types=1);

namespace Tests;

use App\Exception\MissingArgumentException;
use App\Services\Payloads\Payload;
use PHPUnit\Framework\TestCase;

class TestPayload extends Payload
{
    protected $fields = [
        'field_a',
        'field_b',
    ];
}

class PayloadTest extends TestCase
{
    /**
     * @test
     */
    public function payload_throws_an_exception_if_a_field_is_missing()
    {
        $this->expectException(MissingArgumentException::class);
        $this->expectExceptionMessage('Missing argument');

        new TestPayload([
            'field_b' => true
        ]);
    }
}
