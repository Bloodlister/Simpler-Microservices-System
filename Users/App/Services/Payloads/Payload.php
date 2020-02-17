<?php declare(strict_types=1);

namespace App\Services\Payloads;

use App\Exception\MissingArgumentException;

abstract class Payload
{
    protected $fields = [];

    /**
     * Payload constructor.
     * @param array $payload
     * @throws MissingArgumentException
     */
    public function __construct(array $payload)
    {
        $this->validateFields($payload);

        foreach ($this->fields as $field) {
            $this->$field = $payload[$field];
        }
    }

    protected function validateFields(array $payload): void
    {
        foreach ($this->fields as $field) {
            if (!array_key_exists($field, $payload)) {
                throw new MissingArgumentException(sprintf('Missing argument `%1$s`', $field));
            }
        }
    }
}
