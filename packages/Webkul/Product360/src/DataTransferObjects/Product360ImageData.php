<?php

namespace Webkul\Product360\DataTransferObjects;

use Webkul\Product360\Models\Product360Image;

/**
 * Data Transfer Object for Product360Image
 * 
 * Transfers image data to frontend in a structured format
 */
class Product360ImageData
{
    /**
     * Image ID
     *
     * @var int
     */
    public int $id;

    /**
     * Image URL
     *
     * @var string
     */
    public string $url;

    /**
     * Image position in sequence
     *
     * @var int
     */
    public int $position;

    /**
     * Create DTO from Product360Image model
     *
     * @param Product360Image $image
     * @return self
     */
    public static function fromModel(Product360Image $image): self
    {
        $dto = new self();
        $dto->id = $image->id;
        $dto->url = $image->url;
        $dto->position = $image->position;

        return $dto;
    }

    /**
     * Convert DTO to array
     *
     * @return array
     */
    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'url' => $this->url,
            'position' => $this->position,
        ];
    }
}
