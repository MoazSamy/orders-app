<?php

namespace App\Virtual;

/**
 * @OA\Schema(
 *     title="Login User request",
 *     description="Login User request body data",
 *     type="object",
 *     required={"email", "password"}
 * )
 */
class LoginUserRequest
{
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