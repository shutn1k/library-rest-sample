<?php

namespace App\Model;

use OpenApi\Attributes as OA;
use Symfony\Component\Serializer\Annotation\Groups;

#[OA\Schema(
    description: 'Default API response',
    required: ['code', 'message', 'data']
)]
class ApiResponse
{
    #[OA\Property(nullable: false)]
    #[Groups(['api_v1'])]
    private int $code;
    #[OA\Property(nullable: false)]
    #[Groups(['api_v1'])]
    private string $message;
    #[OA\Property(nullable: false)]
    #[Groups(['api_v1'])]
    private mixed $data;

    /**
     * @param int $code
     * @param string $message
     * @param mixed $data
     */
    public function __construct(int $code = 200, string $message = 'Success', mixed $data = [])
    {
        $this->code = $code;
        $this->message = $message;
        $this->data = $data;
    }

    /**
     * @return int
     */
    public function getCode(): int
    {
        return $this->code;
    }

    /**
     * @return string
     */
    public function getMessage(): string
    {
        return $this->message;
    }

    /**
     * @return mixed
     */
    public function getData(): mixed
    {
        return $this->data;
    }
}
