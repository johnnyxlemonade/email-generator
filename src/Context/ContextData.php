<?php

namespace Lemonade\EmailGenerator\Context;

use InvalidArgumentException;

class ContextData
{
    /**
     * @var array Stores context data.
     */
    private array $data = [];

    /**
     * Constructor for ContextData.
     * Initializes the context with the provided data array.
     *
     * @param array $data Optional initial data for the context.
     */
    public function __construct(array $data = [])
    {
        $this->fromArray($data);
    }

    /**
     * Populates the context data from an array.
     *
     * @param array $data The data to populate the context with.
     * @return $this The current instance for method chaining.
     */
    protected function fromArray(array $data): self
    {
        if (count($data) > 0) {
            foreach ($data as $key => $value) {
                $this->set($key, $value);
            }
        }

        return $this;
    }

    /**
     * Sets a value in the context data.
     *
     * @param string $key The key for the value.
     * @param mixed $value The value to be set.
     */
    public function set(string $key, mixed $value): void
    {
        $this->data[$key] = $value;
    }

    /**
     * Retrieves a value from the context data by key.
     *
     * @param string $key The key of the value to retrieve.
     * @return mixed The value associated with the key, or null if the key does not exist.
     */
    public function get(string $key): mixed
    {
        return $this->data[$key] ?? null;
    }

    /**
     * Validates that all specified keys are present and not empty in the context data.
     *
     * @param array $keys The keys to validate.
     * @throws InvalidArgumentException If any key is missing or empty.
     */
    public function validate(array $keys): void
    {
        foreach ($keys as $key) {
            if (!array_key_exists($key, $this->data) || empty($this->data[$key])) {
                throw new InvalidArgumentException("Missing or empty required key: '$key'.");
            }
        }
    }

    /**
     * Converts the context data to an array.
     *
     * @return array The context data as an array.
     */
    public function toArray(): array
    {
        return $this->data;
    }
}
