<?php

namespace App\Virtual;

/**
 * @OA\Schema(
 *     title="Register User request",
 *     description="Register User request body data",
 *     type="object",
 *     required={"email", "password", ""}
 * )
 */
class RegisterUserRequest
{
    
    /**
     * @OA\Property(
     *      title="User name",
     *      description="User name",
     *      example="Moaz"
     * )
     *
     * @var string
     */
    public $name;
    /**
     * @OA\Property(
     *      title="User email",
     *      description="User email",
     *      example="moazsamy333@gmail.com"
     * )
     *
     * @var string
     */
    public $email;

    /**
     * @OA\Property(
     *      title="Password",
     *      description="password",
     * )
     *
     * @var string
     */
    public $password;
}