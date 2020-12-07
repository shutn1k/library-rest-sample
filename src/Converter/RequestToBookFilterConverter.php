<?php

namespace App\Converter;

use App\Model\BookFilter;
use Symfony\Component\HttpFoundation\Request;

/**
 * Class RequestToBookFilterConverter
 * @package App\Converter
 */
class RequestToBookFilterConverter {

    /**
     * @param Request $request
     *
     * @return BookFilter
     */
    public function convert(Request $request): BookFilter {

        $bookFilter = new BookFilter();
        if ($request->query->has('name')) {
            $bookFilter->setName($request->query->get('name'));
        }

        return $bookFilter;
    }
}