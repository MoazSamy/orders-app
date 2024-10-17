<?php

namespace App\Virtual;

/**
 * @OA\Schema(
 *     title="Update Order request",
 *     description="Update Order request body data",
 *     type="object",
 *     required={"customer_name", "delivery_address", "order_total"}
 * )
 */
class UpdateOrderRequest
{
    /**
     * @OA\Property(
     *      title="Customer Name",
     *      description="Name of the customer of the new order",
     *      example="Moaz Samy"
     * )
     *
     * @var string
     */
    public $customer_name;

    /**
     * @OA\Property(
     *      title="Delivery Address",
     *      description="Delivery address of the customer",
     *      example="31 Teraa St - Mansoura"
     * )
     *
     * @var string
     */
    public $delivery_address;


    /**
     * @OA\Property(
     *     title="Order Total",
     *     description="Order total price",
     *     example="20.00",
     *     format="decimal",
     *     type="decimal"
     * )
     *
     * @var decimal
     */
    public $order_total;

    /**
     * @OA\Property(
     *      title="Order Status",
     *      description="Current status of the order",
     * )
     * 
     * @var OrderStatus
     */
    public $order_status;

    /**
     * @OA\Property(
     *      title="Delivery Personnel ID",
     *      description="Delivery Presonnel's id of the new order",
     *      format="int64",
     *      example=1
     * )
     *
     * @var integer
     */
    public $delivery_personnel_id;
}
