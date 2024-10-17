<?php

namespace App\Virtual\Models;


/**
 * @OA\Schema(
 *     title="User",
 *     description="User model",
 *     @OA\Xml(
 *         name="User"
 *     )
 * )
 */
class User
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
     *      title="Name",
     *      description="Username",
     *      example="Moaz"
     * )
     *
     * @var string
     */
    public $name;

    /**
     * @OA\Property(
     *      title="Email",
     *      description="User email",
     *      example="moazsamy333@gmail.com"
     * )
     *
     * @var string
     */
    public $email;

    
    /**
     * @OA\Property(
     *     title="User Type",
     *     description="User type. 0-Admin, 1-Delivery Personnel",
     *     example="1",
     * )
     *
     * @var integer
     */
    public $type;
    
    /**
     * @OA\Property(
     *      title="Password",
     *      description="Password",
     * )
     * 
     * @var string
     */
    public $password;
    
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
}