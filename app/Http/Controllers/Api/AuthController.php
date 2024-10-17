<?php

namespace App\Http\Controllers\Api;

use App\Helper\ResponseHelper;
use App\Http\Controllers\Controller;
use App\Http\Requests\LoginRequest;
use App\Http\Requests\RegisterRequest;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use Exception;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log as FacadesLog;

class AuthController extends Controller
{
    /**
     * Register new user
     * @param App\Requests\RegisterRequest $request
     * @return JSONRequest
     */

    /**
     * @OA\Post(
     *      path="/users/register",
     *      operationId="registerUser",
     *      tags={"Users"},
     *      summary="Register a new user",
     *      description="Registers a new user in the database",
     *      @OA\RequestBody(
     *          required=true,
     *          @OA\JsonContent(ref="#/components/schemas/RegisterUserRequest")
     *      ),
     *      @OA\Response(
     *          response=201,
     *          description="Successful operation",
     *       ),
     *      @OA\Response(
     *          response=400,
     *          description="Failure",
     *      ),
     *      @OA\Response(
     *          response=500,
     *          description="Internal Server Error",
     *      ),
     * )
     */
    public function register(RegisterRequest $request)
    {
        try{
            $user = User::create([
                'name' => $request->name,
                'email' => $request->email,
                'password' => Hash::make($request->password)
            ]);
            
            if($user){
                return ResponseHelper::success(message: "User has been added successfully", data: $user, statusCode: 201);
            }
            return ResponseHelper::error(message: "User addition failed!", statusCode: 400);
        }
        catch(Exception $e){
            FacadesLog::error("User was not added. Error: ".$e->getMessage().' - Line no. '.$e->getLine());
            return ResponseHelper::error(message: "User addition failed, please contact server admin", statusCode: 500);
        }

    }

    /**
     * Login user
     * @param App\Requests\LoginRequest $request
     */

    /**
     * @OA\Post(
     *      path="/users/login",
     *      operationId="loginUser",
     *      tags={"Users"},
     *      summary="Login as user",
     *      description="Logs in as a user",
     *      @OA\RequestBody(
     *          required=true,
     *          @OA\JsonContent(ref="#/components/schemas/LoginUserRequest")
     *      ),
     *      @OA\Response(
     *          response=200,
     *          description="Successful operation",
     *          @OA\JsonContent(ref="#/components/schemas/User")
     *       ),
     *      @OA\Response(
     *          response=400,
     *          description="Unmatching credentials",
     *      ),
     *      @OA\Response(
     *          response=500,
     *          description="Internal Server Error",
     *      ),
     * )
     */
    public function login(LoginRequest $request)
    {
        try{
            // If credentials are wrong, return error
            if(!Auth::attempt(['email' => $request->email, 'password'=>$request->password])){
                return ResponseHelper::error(message: "Provided Credentials didn't match our records, please try again.", statusCode: 400);
            }

            // else, Create API Token
            $user = Auth::user();
            $token = $user->createToken("User API Token")->plainTextToken;
            $authUser = [
                'user' => $user,
                'token' => $token
            ];

            return ResponseHelper::success(message:"Logged in successfully as ". $user->name, data: $authUser, statusCode: 200);
        }
        catch(Exception $e){
            FacadesLog::error("User login failed. Error: ".$e->getMessage().' - Line no. '.$e->getLine());
            return ResponseHelper::error(message: "Login failed, please contact server admin.", statusCode: 500);
        }
    }

    /**
     * Logout user
     */

    /**
     * @OA\get(
     *      path="/users/logout",
     *      operationId="logoutUser",
     *      tags={"Users"},
     *      summary="Logout",
     *      description="Logout from the system",
     *      security={{"bearer_token":{}}},
     *      @OA\Response(
     *          response=200,
     *          description="Successful operation",
     *       ),
     *      @OA\Response(
     *          response=400,
     *          description="Not logged in",
     *      ),
     *      @OA\Response(
     *          response=500,
     *          description="Internal Server Error",
     *      ),
     * )
     */
    public function logout(){
        try{
            $user = Auth::user();
            if($user){
                $user->currentAccessToken()->delete();
                return ResponseHelper::success(message:"Logged out successfully", data: $user, statusCode: 200);
            }
            return ResponseHelper::error(message: "You are not logged in, please login and try again.", statusCode: 400);
        }
        catch(Exception $e){
            FacadesLog::error("User logout failed. Error: ".$e->getMessage().' - Line no. '.$e->getLine());
            return ResponseHelper::error(message: "Logout failed, please contact server admin.", statusCode: 500);
        }
    }
}
