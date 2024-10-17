<?php

namespace App\Virtual\Resources;

/**
 * @OA\Schema(
 *     title="OrderResource",
 *     description="Order resource",
 *     @OA\Xml(
 *         name="OrderResource"
 *     )
 * )
 */
class OrderResource
{
    /**
     * @OA\Property(
     *     title="Data",
     *     description="Data wrapper"
     * )
     *
     * @var \App\Virtual\Models\Order[]
     */
    private $data;
}