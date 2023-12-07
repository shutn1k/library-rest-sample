<?php

namespace App;

use OpenApi\Attributes as OA;

#[OA\Info(
    version: "1.0",
    description: "Library REST API sample",
    title: "Library REST API",
    contact: new OA\Contact(name: 'Sergey Zhuchkov', email: 'z@sergey.moscow')
)]
#[OA\Server(
    url: 'https://library-sample.sergey.moscow',
    description: 'Test API server'
)]
#[OA\Tag('book', description: 'Book endpoint')]
#[OA\Tag('author', description: 'Author endpoint')]
class OpenApiSpec
{
}
