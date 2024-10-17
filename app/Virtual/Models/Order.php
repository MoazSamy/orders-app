<?php

namespace App\Virtual\Models;


/**
 * @OA\Schema(
 *     title="Order",
 *     description="Order model",
 *     @OA\Xml(
 *         name="Order"
 *     )
 * )
 */
class Order
{

    /**
     * @OA\Property(
     *     title="ID",
     *     description="ID",
     *     format="int64",
     *     example=1
     * )
     *
     * @var integer
     */
    private $id;

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
     * @var integer
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
    
    /**
     * @OA\Property(
     *     title="Created at",
     *     description="Created at",
     *     example="2020-01-27 17:50:45",
     *     format="datetime",
     *     type="string"
     * )
     *
     * @var \DateTime
     */
    private $created_at;

    /**
     * @OA\Property(
     *     title="Updated at",
     *     description="Updated at",
     *     example="2020-01-27 17:50:45",
     *     format="datetime",
     *     type="string"
     * )
     *
     * @var \DateTime
     */
    private $updated_at;

    /**
     * @OA\Property(
     *     title="Delivery Personnel",
     *     description="Delivery Personnel's user model"
     * )
     *
     * @var \App\Virtual\Models\User
     */
    private $delivery_personnel;
}