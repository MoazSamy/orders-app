<?php

namespace App\Http\Controllers\Api;

use App\Enums\OrderStatus;
use App\Enums\UserType;
use App\Helper\ResponseHelper;
use App\Http\Controllers\Controller;
use App\Http\Requests\Orders\CreateOrderRequest;
use App\Http\Requests\Orders\UpdateOrderRequest;
use App\Models\Order;
use App\Models\User;
use Exception;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use OpenApi\Annotations as OA;

class OrdersController extends Controller
{
    /**
     * Display a listing of the resource.
     */

    /**
     * @OA\Get(
     *      path="/orders",
     *      operationId="getOrdersList",
     *      tags={"Orders"},
     *      summary="Get list of orders",
     *      description="Returns list of orders",
     *      security={{"bearer_token":{}}},
     *      @OA\Response(
     *          response=200,
     *          description="Successful operation",
     *       ),
     *      @OA\Response(
     *          response=401,
     *          description="Unauthenticated",
     *      ),
     *      @OA\Response(
     *          response=403,
     *          description="Forbidden"
     *      )
     *     )
     */
    public function index()
    {
        try{
            $user = Auth::user();
            if($user->type == UserType::Admin)
            {
                $orders = Order::all();
                if(count($orders) < 1)
                {
                    return ResponseHelper::success(message:"There are no orders right now.");
                }
                return ResponseHelper::success(message:"Orders retrieved successfully", data:$orders);
            }
            return ResponseHelper::error(message:"Access Denied", statusCode:403);
        }
        catch(Exception $e){
            Log::error("Orders were not retrieved. Error: " . $e->getMessage() . ' - Line no.' . $e->getLine());
            return ResponseHelper::error(message:"Failed to retrieve orders, please try again later.", statusCode:500);
        }
    }

    /**
     * Store a newly created resource in storage.
     */

    /**
     * @OA\Post(
     *     path="/orders/create",
     *     operationId="storeOrder",
     *     tags={"Orders"},
     *     summary="Store order",
     *     description="Store and return order",
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(ref="#/components/schemas/StoreOrderRequest")
     *     ),
     *     @OA\Response(
     *         response="201",
     *         description="Order created"
     *     ),
     *     @OA\Response(
     *         response="400",
     *         description="Order creation failed"
     *     ),
     *     @OA\Response(
     *         response="500",
     *         description="Server error"
     *     )
     * )
     */
    public function store(CreateOrderRequest $request)
    {
        try{
            $order = Order::create([
                'customer_name' => $request->customer_name,
                'delivery_address' => $request->delivery_address,
                'order_total' => $request->order_total
            ]);
            if($order)
            {
                return ResponseHelper::success(message:"Order created successfully, please assign a delivery personnel to it.", data:$order, statusCode:201);
            }
            return ResponseHelper::error(message:"Failed to create order, please try again later.", statusCode:400);
        }
        catch(Exception $e){
            Log::error("Order was not created. Error: " . $e->getMessage() . ' - Line no.' . $e->getLine());
            return ResponseHelper::error(message:"Failed to create order, please contact server admin.", statusCode:500);
        }
    }

    /**
     * Display the specified resource.
     */

    /**
     * @OA\Get(
     *      path="/orders/{id}",
     *      operationId="getOrderById",
     *      tags={"Orders"},
     *      summary="Get order information",
     *      description="Returns order data",
     *      @OA\Parameter(
     *          name="id",
     *          description="Order id",
     *          required=true,
     *          in="path",
     *          @OA\Schema(
     *              type="integer"
     *          )
     *      ),
     *      security={{"bearer_token":{}}},
     *      @OA\Response(
     *          response=200,
     *          description="Successful operation",
     *          @OA\JsonContent(ref="#/components/schemas/Order")
     *       ),
     *      @OA\Response(
     *          response=400,
     *          description="Bad Request"
     *      ),
     *      @OA\Response(
     *          response=401,
     *          description="Unauthenticated",
     *      ),
     *      @OA\Response(
     *          response=403,
     *          description="Forbidden"
     *      ),
     *      @OA\Response(
     *          response=404,
     *          description="Resource Not Found"
     *      )
     * )
     */

    public function show(string $id)
    {
        try{
            $order = Order::find($id);
            $user = Auth::user();
            if($order)
            {
                if($user->type == UserType::Admin || $order->delivery_personnel == $user->id)
                {
                    return ResponseHelper::success(message:"Order retrieved successfully", data:$order);
                }
                return ResponseHelper::error(message:"Access Denied", statusCode:403);
            }
            return ResponseHelper::success(message:"There are no orders matching this ID.", statusCode:404);
        }
        catch(Exception $e){
            Log::error("Order was not retrieved. Error: " . $e->getMessage() . ' - Line no.' . $e->getLine());
            return ResponseHelper::error(message:"Failed to retrieve order, please try again later.", statusCode:500);
        }
    }

    /**
     * Update the specified resource in storage.
     * @param UpdateOrderRequest $request
     * @param string $id
     */

    /**
     * @OA\Put(
     *      path="/orders/{id}",
     *      operationId="updateOrder",
     *      tags={"Orders"},
     *      summary="Update existing order",
     *      description="Returns updated order data",
     *      @OA\Parameter(
     *          name="id",
     *          description="Order id",
     *          required=true,
     *          in="path",
     *          @OA\Schema(
     *              type="integer"
     *          )
     *      ),
     *      security={{"bearer_token":{}}},
     *      @OA\RequestBody(
     *          required=true,
     *          @OA\JsonContent(ref="#/components/schemas/UpdateOrderRequest")
     *      ),
     *      @OA\Response(
     *          response=202,
     *          description="Successful operation",
     *          @OA\JsonContent(ref="#/components/schemas/Order")
     *       ),
     *      @OA\Response(
     *          response=400,
     *          description="Bad Request"
     *      ),
     *      @OA\Response(
     *          response=401,
     *          description="Unauthenticated",
     *      ),
     *      @OA\Response(
     *          response=403,
     *          description="Forbidden"
     *      ),
     *      @OA\Response(
     *          response=404,
     *          description="Resource Not Found"
     *      )
     * )
     */
    public function update(UpdateOrderRequest $request, string $id)
    {
        try {
            $order = Order::find($id);
            $user = Auth::user();
            $changed = false;
            if($user-> type == UserType::Admin){
                if($order)
                {
                    if ($request->customer_name != null) {
                        $order->customer_name = $request->customer_name;
                        $changed = true;
                    }
                    if ($request->delivery_address != null) {
                        $order->delivery_address = $request->delivery_address;
                        $changed = true;
                    }
                    if ($request->order_total != null) {
                        $order->order_total = $request->order_total;
                        $changed = true;
                    }
                    if ($request->order_status != null) {
                        $order->order_status = $request->order_status;
                        $changed = true;
                    }
                    if($changed == true){
                        $order->update();
                        return ResponseHelper::success(message:'Order updated successfully.', data:$order, statusCode:202);
                    }
                    return ResponseHelper::error(message:'No fields were updated, please check your data.',statusCode:400);
                }
                return ResponseHelper::error(message:'No orders found with this id.', statusCode:404);
            }
            return ResponseHelper::error(message:'Access denied. You have to be an admin to change order details.', statusCode:403);
        } catch (Exception $e) {
            Log::error(message:`Order update with id {$id} failed done by user {$user->name}. Error` . $e->getMessage() . ' - Line no. : ' . $e->getLine());
            return ResponseHelper::error(message:"Order update failed, please contact server admin.", statusCode:500);
        }
    }

    /**
     * Assigning delivery personnel to an order
     * @param string $orderId
     * @param string $deliveryPersonnelId
     */

    /**
     * @OA\Put(
     *      path="/orders/assign/{orderId}/person/{deliveryPersonnelId}",
     *      operationId="assignDeliveryPersonnel",
     *      tags={"Orders"},
     *      summary="assign delivery personnel to an existing order",
     *      description="Returns updated order data",
     *      @OA\Parameter(
     *          name="orderId",
     *          description="Order ID",
     *          required=true,
     *          in="path",
     *          @OA\Schema(
     *              type="integer"
     *          )
     *      ),
     *      @OA\Parameter(
     *          name="deliveryPersonnelId",
     *          description="Delivery Personnel ID",
     *          required=true,
     *          in="path",
     *          @OA\Schema(
     *              type="integer"
     *          )
     *      ),
     *      security={{"bearer_token":{}}},
     *      @OA\Response(
     *          response=202,
     *          description="Successful operation",
     *          @OA\JsonContent(ref="#/components/schemas/Order")
     *       ),
     *      @OA\Response(
     *          response=400,
     *          description="Bad Request"
     *      ),
     *      @OA\Response(
     *          response=401,
     *          description="Unauthenticated",
     *      ),
     *      @OA\Response(
     *          response=403,
     *          description="Forbidden"
     *      ),
     *      @OA\Response(
     *          response=404,
     *          description="Resource Not Found"
     *      )
     * )
     */
    public function AssignDeliveryPersonnel(string $orderId, string $deliveryPersonnelId)
    {
        try {
            $order = Order::find($orderId);
            $user = Auth::user();
            $deliveryPersonnel = User::find($deliveryPersonnelId);
            if($user->type == UserType::Admin){
                if($order){
                    if($deliveryPersonnel){
                        if($deliveryPersonnel->type == UserType::DeliveryPersonnel){
                            $deliveryPersonnelOrders = Order::where('delivery_personnel_id', $deliveryPersonnelId)->whereNot('order_status', OrderStatus::Completed)->get();
                            if(count($deliveryPersonnelOrders) >= 3){
                                return ResponseHelper::error(message:'Delivery Personnel cannot have more than 3 active orders');
                            }
                            if ($order->delivery_personnel_id != $deliveryPersonnelId) {
                                $order->delivery_personnel_id = $deliveryPersonnelId;
                                $order->update();
                                return ResponseHelper::success(message:'Delivery personnel was assigned successfully.', data:$order , statusCode:202);
                            }
                            return ResponseHelper::error(message:'Delivery personnel is already assigned to this order.', statusCode:400);
                        }
                    }
                    return ResponseHelper::error(message:'No delivery personnel with this ID was found.', statusCode:404);
                }
                return ResponseHelper::error(message:'No order with this ID was found.', statusCode:404);
            }
            return ResponseHelper::error(message:'Access denied. You have to be an admin to change delivery personnel.', statusCode:403);
        } catch (Exception $e) {
            Log::error("Assigning Delivery Personnel with id : {$deliveryPersonnelId} failed, requested by admin {$user->name}. Error: ".$e->getMessage()." - Line no. : ".$e->getLine());
            return ResponseHelper::error(message:'Assigning delivery personnel failed, please contact server admin', statusCode:500);
        }
    }

    /**
     * Changing order status function
     * @param string $orderId
     * @param string $status
     */
        /**
     * @OA\Put(
     *      path="orders/update/{orderId}/status/{orderStatus}",
     *      operationId="ChangeOrderStatus",
     *      tags={"Orders"},
     *      summary="Update status of existing order",
     *      description="Returns updated order data",
     *      @OA\Parameter(
     *          name="orderId",
     *          description="Order id",
     *          required=true,
     *          in="path",
     *          @OA\Schema(
     *              type="integer"
     *          )
     *      ),
     *      @OA\Parameter(
     *          name="orderStatus",
     *          description="Order Status",
     *          required=true,
     *          in="path",
     *          @OA\Schema(
     *              type="integer"
     *          )
     *      ),
     *      security={{"bearer_token":{}}},
     *      @OA\Response(
     *          response=202,
     *          description="Successful operation",
     *          @OA\JsonContent(ref="#/components/schemas/Order")
     *       ),
     *      @OA\Response(
     *          response=400,
     *          description="Bad Request"
     *      ),
     *      @OA\Response(
     *          response=401,
     *          description="Unauthenticated",
     *      ),
     *      @OA\Response(
     *          response=403,
     *          description="Forbidden"
     *      ),
     *      @OA\Response(
     *          response=404,
     *          description="Resource Not Found"
     *      )
     * )
     */
    public function ChangeOrderStatus(string $orderId, string $status)
    {
        try {
            $order = Order::find($orderId);
            $user = Auth::user();
            $statusName = OrderStatus::from($status);
            if($user){
                if($order){
                    if($user->id != $order->delivery_personnel_id)
                        return ResponseHelper::error(message:'Access denied. You have to be the delivery personnel assigned to this order to change it.', statusCode:403);
                    if($order->order_status == $status)
                        return ResponseHelper::error(message:'Cannot change the order status to itself.', statusCode:400);
                    $order->order_status = $status;
                    $order->update();
                    return ResponseHelper::success(message:'Order status updated successfully.', data:$order, statusCode:202);
                }
                return ResponseHelper::error(message:"No order with ID:{$orderId} was found.", statusCode:404);
            }
            return ResponseHelper::error(message:'Access denied. You have to be a delivery personnel to change the order status.', statusCode:403);
        } catch (Exception $e) {
            Log::error("Order status update failed with value {$statusName} on order with ID: {$orderId}. Error: ".$e->getMessage()." - Line no. ".$e->getLine());
            return ResponseHelper::error("Order status update failed, please contact the server admin", statusCode:500);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    
    /**
     * @OA\Delete(
     *      path="/orders/{id}",
     *      operationId="deleteOrder",
     *      tags={"Orders"},
     *      summary="Delete existing order",
     *      description="Deletes a record and returns no content",
     *      @OA\Parameter(
     *          name="id",
     *          description="Order id",
     *          required=true,
     *          in="path",
     *          @OA\Schema(
     *              type="integer"
     *          )
     *      ),
     *      security={{"bearer_token":{}}},
     *      @OA\Response(
     *          response=204,
     *          description="Successful operation",
     *          @OA\JsonContent()
     *       ),
     *      @OA\Response(
     *          response=401,
     *          description="Unauthenticated",
     *      ),
     *      @OA\Response(
     *          response=403,
     *          description="Forbidden"
     *      ),
     *      @OA\Response(
     *          response=404,
     *          description="Resource Not Found"
     *      )
     * )
    */
    public function destroy(string $id)
    {
        try {
            $order = Order::find($id);
            $user = Auth::user();
    
            if($user->type == UserType::Admin){
                if($order){
                    $order->delete();
                    return ResponseHelper::success(message:'Order deleted successfully', statusCode:204);
                }
                return ResponseHelper::error(message:"Order with ID : {$id} was not found.", statusCode:404);
            }
            return ResponseHelper::error(message:"Access Denied. You have to be an admin to delete orders.", statusCode:403);    
        } catch (Exception $e) {
            Log::error("Order deletion failed on order with ID: {$id}. Error: ".$e->getMessage()." - Line no. ".$e->getLine());
            return ResponseHelper::error("Order deletion failed, please contact the server admin", statusCode:500);
        }
    }
}
