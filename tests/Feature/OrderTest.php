<?php

namespace Tests\Feature;

use App\Enums\OrderStatus;
use App\Enums\UserType;
use App\Models\Order;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class OrderTest extends TestCase
{

    use RefreshDatabase;
    /**
     * A basic feature test example.
     */
    public function testSuccessCreateOrder(): void
    {
        $order =[
            "customer_name" => "ahmed",
            "delivery_address" => "4 st st",
            "order_total" => 20.00,
        ];

        $response = $this->post('/api/orders/create', $order);

        $response->assertStatus(201);
    }

    public function testFailureCreateOrder(): void
    {
        $order =[];

        $response = $this->post('/api/orders/create', $order);

        $response->assertStatus(302);
    }

    public function testSuccessGetOrders():void
    {
        $user = User::factory()->create([
            "name" => "TestUser",
            "email" => "TestUser@test.com",
            "password" => bcrypt("testpass"),
            "type" => UserType::Admin,
        ]);

        Sanctum::actingAs($user);

        $response = $this->get("/api/orders/");

        $response->assertStatus(200);
    }

    public function testUnauthorizedGetOrders():void
    {
        $user = User::factory()->create([
            "name" => "TestUser",
            "email" => "TestUser@test.com",
            "password" => bcrypt("testpass"),
            "type" => UserType::DeliveryPersonnel,
        ]);

        Sanctum::actingAs($user);

        $response = $this->get("/api/orders/");

        $response->assertStatus(403);
    }

    public function testSuccessGetOrderAsAdmin():void
    {
        $user = User::factory()->create([
            "name" => "TestUser",
            "email" => "TestUser@test.com",
            "password" => bcrypt("testpass"),
            "type" => UserType::Admin,
        ]);

        $order = Order::factory()->create([
            "customer_name" => "ahmed",
            "delivery_address" => "4 st st",
            "order_total" => 20.00,
        ]);

        Sanctum::actingAs($user);

        $response = $this->get("/api/orders/".$order->id);

        $response->assertStatus(200);
    }

    public function testFailureGetNonexistingOrderAsAdmin():void
    {
        $user = User::factory()->create([
            "name" => "TestUser",
            "email" => "TestUser@test.com",
            "password" => bcrypt("testpass"),
            "type" => UserType::Admin,
        ]);

        Sanctum::actingAs($user);

        $response = $this->get("/api/orders/1");

        $response->assertStatus(404);
    }

    public function testSuccessGetOrderAsDeliveryPersonnel():void
    {
        $user = User::factory()->create([
            "name" => "TestUser",
            "email" => "TestUser@test.com",
            "password" => bcrypt("testpass"),
        ]);

        $order = Order::factory()->create([
            "customer_name" => "ahmed",
            "delivery_address" => "4 st st",
            "order_total" => 20.00,
            "delivery_personnel_id" => $user->id
        ]);
        Sanctum::actingAs($user);

        $response = $this->get("/api/orders/".$order->id);

        $response->assertStatus(200);
    }

    public function testFailureGetOrderAsDeliveryPersonnel():void
    {
        $user = User::factory()->create([
            "name" => "TestUser",
            "email" => "TestUser@test.com",
            "password" => bcrypt("testpass"),
            "type" => UserType::DeliveryPersonnel
        ]);

        $order = Order::factory()->create([
            "customer_name" => "ahmed",
            "delivery_address" => "4 st st",
            "order_total" => 20.00,
            "delivery_personnel_id" => null
        ]);

        Sanctum::actingAs($user);

        $response = $this->get("/api/orders/".$order->id);

        $response->assertStatus(403);
    }

    public function testSuccessUpdateOrderAsAdmin():void
    {
        $user = User::factory()->create([
            "name" => "TestUser",
            "email" => "TestUser@test.com",
            "password" => bcrypt("testpass"),
            "type" => UserType::Admin
        ]);

        $order = Order::factory()->create([
            "customer_name" => "ahmed",
            "delivery_address" => "4 st st",
            "order_total" => 20.00,
        ]);

        $orderUpdate = [
            "customer_name" => "mohamed",
            "delivery_address" => "5 st",
            "order_total" => 10.00
        ];

        Sanctum::actingAs($user);

        $response = $this->post("/api/orders/update/".$order->id, $orderUpdate);

        $response->assertStatus(202);
    }


    public function testFailureUpdateOrderAsNonAdmin():void
    {
        $user = User::factory()->create([
            "name" => "TestUser",
            "email" => "TestUser@test.com",
            "password" => bcrypt("testpass"),
            "type" => UserType::DeliveryPersonnel
        ]);

        $order = Order::factory()->create([
            "customer_name" => "ahmed",
            "delivery_address" => "4 st st",
            "order_total" => 20.00,
        ]);

        $orderUpdate = [
            "customer_name" => "mohamed",
            "delivery_address" => "5 st",
            "order_total" => 10.00
        ];

        Sanctum::actingAs($user);

        $response = $this->post("/api/orders/update/".$order->id, $orderUpdate);

        $response->assertStatus(403);
    }

    
    public function testFailureUpdateOrderWithNoDataAsAdmin():void
    {
        $user = User::factory()->create([
            "name" => "TestUser",
            "email" => "TestUser@test.com",
            "password" => bcrypt("testpass"),
            "type" => UserType::Admin
        ]);

        $order = Order::factory()->create([
            "customer_name" => "ahmed",
            "delivery_address" => "4 st st",
            "order_total" => 20.00,
        ]);

        $orderUpdate = [];

        Sanctum::actingAs($user);

        $response = $this->post("/api/orders/update/".$order->id, $orderUpdate);

        $response->assertStatus(400);
    }

    public function testFailureUpdateNonexistingOrderAsAdmin():void
    {
        $user = User::factory()->create([
            "name" => "TestUser",
            "email" => "TestUser@test.com",
            "password" => bcrypt("testpass"),
            "type" => UserType::Admin
        ]);

        $orderUpdate = [];

        Sanctum::actingAs($user);

        $response = $this->post("/api/orders/update/100", $orderUpdate);

        $response->assertStatus(404);
    }

    public function testSuccessAssignDeliveryPersonnelToOrderAsAdmin():void
    {
        $userAdmin = User::factory()->create([
            "name" => "TestUserAdmin",
            "email" => "TestUserAdmin@test.com",
            "password" => bcrypt("testpass"),
            "type" => UserType::Admin
        ]);

        $userDeliveryPersonnel = User::factory()->create([
            "name" => "TestUserDeliveryPersonnel",
            "email" => "TestUserDeliveryPersonnel@test.com",
            "password" => bcrypt("testpass"),
            "type" => UserType::DeliveryPersonnel
        ]);

        $order = Order::factory()->create([
            "customer_name" => "ahmed",
            "delivery_address" => "4 st st",
            "order_total" => 20.00,
        ]);

        Sanctum::actingAs($userAdmin);

        $response = $this->post("api/orders/assign/".$order->id."/person/".$userDeliveryPersonnel->id);

        $response->assertStatus(202);
    }
    
    public function testFailureAssignDeliveryPersonnelToNonExistingOrderAsAdmin():void
    {
        $userAdmin = User::factory()->create([
            "name" => "TestUserAdmin",
            "email" => "TestUserAdmin@test.com",
            "password" => bcrypt("testpass"),
            "type" => UserType::Admin
        ]);

        $userDeliveryPersonnel = User::factory()->create([
            "name" => "TestUserDeliveryPersonnel",
            "email" => "TestUserDeliveryPersonnel@test.com",
            "password" => bcrypt("testpass"),
            "type" => UserType::DeliveryPersonnel
        ]);

        Sanctum::actingAs($userAdmin);

        $response = $this->post("api/orders/assign/100/person/".$userDeliveryPersonnel->id);

        $response->assertStatus(404);
    }

    public function testFailureAssignNonExistingDeliveryPersonnelToOrderAsAdmin():void
    {
        $userAdmin = User::factory()->create([
            "name" => "TestUserAdmin",
            "email" => "TestUserAdmin@test.com",
            "password" => bcrypt("testpass"),
            "type" => UserType::Admin
        ]);

        $order = Order::factory()->create([
            "customer_name" => "ahmed",
            "delivery_address" => "4 st st",
            "order_total" => 20.00,
        ]);

        Sanctum::actingAs($userAdmin);

        $response = $this->post("api/orders/assign/".$order->id."/person/100");

        $response->assertStatus(404);
    }


    public function testFailureAssignSameDeliveryPersonnelToOrderAsNonAdmin():void
    {
        $userAdmin = User::factory()->create([
            "name" => "TestUserAdmin",
            "email" => "TestUserAdmin@test.com",
            "password" => bcrypt("testpass"),
            "type" => UserType::Admin
        ]);

        $userDeliveryPersonnel = User::factory()->create([
            "name" => "TestUserDeliveryPersonnel",
            "email" => "TestUserDeliveryPersonnel@test.com",
            "password" => bcrypt("testpass"),
            "type" => UserType::DeliveryPersonnel
        ]);

        $order = Order::factory()->create([
            "customer_name" => "ahmed",
            "delivery_address" => "4 st st",
            "order_total" => 20.00,
            "delivery_personnel_id" => $userDeliveryPersonnel->id
        ]);

        Sanctum::actingAs($userAdmin);

        $response = $this->post("api/orders/assign/".$order->id."/person/".$userDeliveryPersonnel->id);

        $response->assertStatus(400);
    }

    public function testFailureAssignDeliveryPersonnelToOrderAsNonAdmin():void
    {
        $userDeliveryPersonnel1 = User::factory()->create([
            "name" => "TestUserDeliveryPersonnel1",
            "email" => "TestUserDeliveryPersonnel1@test.com",
            "password" => bcrypt("testpass"),
            "type" => UserType::DeliveryPersonnel
        ]);

        $userDeliveryPersonnel2 = User::factory()->create([
            "name" => "TestUserDeliveryPersonnel2",
            "email" => "TestUserDeliveryPersonnel2@test.com",
            "password" => bcrypt("testpass"),
            "type" => UserType::DeliveryPersonnel
        ]);

        $order = Order::factory()->create([
            "customer_name" => "ahmed",
            "delivery_address" => "4 st st",
            "order_total" => 20.00,
        ]);

        Sanctum::actingAs($userDeliveryPersonnel1);

        $response = $this->post("api/orders/assign/".$order->id."/person/".$userDeliveryPersonnel2->id);

        $response->assertStatus(403);
    }

    public function testFailureAssignDeliveryPersonnelToMaximumOrders():void
    {
        $userAdmin = User::factory()->create([
            "name" => "TestUserAdmin",
            "email" => "TestUserAdmin@test.com",
            "password" => bcrypt("testpass"),
            "type" => UserType::Admin
        ]);

        $userDeliveryPersonnel = User::factory()->create([
            "name" => "TestUserDeliveryPersonnel1",
            "email" => "TestUserDeliveryPersonnel1@test.com",
            "password" => bcrypt("testpass"),
            "type" => UserType::DeliveryPersonnel
        ]);

        $order1 = Order::factory()->create([
            "customer_name" => "ahmed",
            "delivery_address" => "4 st st",
            "order_total" => 20.00,
            "delivery_personnel_id" => $userDeliveryPersonnel->id
        ]);
        $order2 = Order::factory()->create([
            "customer_name" => "mohamed",
            "delivery_address" => "5 st st",
            "order_total" => 30.00,
            "delivery_personnel_id" => $userDeliveryPersonnel->id
        ]);
        $order3 = Order::factory()->create([
            "customer_name" => "mostafa",
            "delivery_address" => "6 st st",
            "order_total" => 40.00,
            "delivery_personnel_id" => $userDeliveryPersonnel->id
        ]);
        $order4 = Order::factory()->create([
            "customer_name" => "qasem",
            "delivery_address" => "7 st st",
            "order_total" => 50.00,
            "delivery_personnel_id" => $userDeliveryPersonnel->id
        ]);

        Sanctum::actingAs($userAdmin);

        $response = $this->post("api/orders/assign/".$order4->id."/person/".$userDeliveryPersonnel->id);

        $response->assertStatus(400);
    }


    public function testSuccessChangeOrderStatus():void
    {
        $userDeliveryPersonnel = User::factory()->create([
            "name" => "TestUserDeliveryPersonnel",
            "email" => "TestUserDeliveryPersonnel@test.com",
            "password" => bcrypt("testpass"),
            "type" => UserType::DeliveryPersonnel
        ]);

        $order = Order::factory()->create([
            "customer_name" => "ahmed",
            "delivery_address" => "4 st st",
            "order_total" => 20.00,
            "order_status" => 0,
            "delivery_personnel_id" => $userDeliveryPersonnel->id,
        ]);

        Sanctum::actingAs($userDeliveryPersonnel);

        $response = $this->post("api/orders/update/".$order->id."/status/1");
        $response->assertStatus(202);
    }

    public function testFailureChangeUnExistingOrderStatus():void
    {
        $userDeliveryPersonnel = User::factory()->create([
            "name" => "TestUserDeliveryPersonnel",
            "email" => "TestUserDeliveryPersonnel@test.com",
            "password" => bcrypt("testpass"),
            "type" => UserType::DeliveryPersonnel
        ]);

        Sanctum::actingAs($userDeliveryPersonnel);

        $response = $this->post("api/orders/update/100/status/2");

        $response->assertStatus(404);
    }


    public function testFailureChangeOrderStatusToSameStatus():void
    {
        $userDeliveryPersonnel = User::factory()->create([
            "name" => "TestUserDeliveryPersonnel",
            "email" => "TestUserDeliveryPersonnel@test.com",
            "password" => bcrypt("testpass"),
            "type" => UserType::DeliveryPersonnel
        ]);

        $order = Order::factory()->create([
            "customer_name" => "ahmed",
            "delivery_address" => "4 st st",
            "order_total" => 20.00,
            "order_status" => 0,
            "delivery_personnel_id" => $userDeliveryPersonnel->id,
        ]);

        Sanctum::actingAs($userDeliveryPersonnel);

        $response = $this->post("api/orders/update/".$order->id."/status/0");

        $response->assertStatus(400);
    }

    public function testFailureChangeOrderStatusByDifferentDeliveryPersonnel():void
    {
        $userDeliveryPersonnel1 = User::factory()->create([
            "name" => "TestUserDeliveryPersonnel1",
            "email" => "TestUserDeliveryPersonnel1@test.com",
            "password" => bcrypt("testpass"),
            "type" => UserType::DeliveryPersonnel
        ]);

        $userDeliveryPersonnel2 = User::factory()->create([
            "name" => "TestUserDeliveryPersonnel2",
            "email" => "TestUserDeliveryPersonnel2@test.com",
            "password" => bcrypt("testpass"),
            "type" => UserType::DeliveryPersonnel
        ]);

        $order = Order::factory()->create([
            "customer_name" => "ahmed",
            "delivery_address" => "4 st st",
            "order_total" => 20.00,
            "order_status" => 0,
            "delivery_personnel_id" => $userDeliveryPersonnel1->id,
        ]);

        Sanctum::actingAs($userDeliveryPersonnel2);

        $response = $this->post("api/orders/update/".$order->id."/status/2");

        $response->assertStatus(403);
    }

    public function testSuccessDeleteOrder():void
    {
        $userAdmin = User::factory()->create([
            "name" => "TestUserAdmin",
            "email" => "TestUserAdmin@test.com",
            "password" => bcrypt("testpass"),
            "type" => UserType::Admin
        ]);

        $order = Order::factory()->create([
            "customer_name" => "ahmed",
            "delivery_address" => "4 st st",
            "order_total" => 20.00,
        ]);

        Sanctum::actingAs($userAdmin);

        $response=$this->post("api/orders/delete/".$order->id);

        $response->assertStatus(204);
    }

    public function testFailureDeleteOrder():void
    {
        $user = User::factory()->create([
            "name" => "TestUser",
            "email" => "TestUser@test.com",
            "password" => bcrypt("testpass"),
            "type" => UserType::DeliveryPersonnel
        ]);

        $order = Order::factory()->create([
            "customer_name" => "ahmed",
            "delivery_address" => "4 st st",
            "order_total" => 20.00,
        ]);

        Sanctum::actingAs($user);

        $response=$this->post("api/orders/delete/".$order->id);

        $response->assertStatus(403);
    }

    public function testFailureDeleteNonExistingOrder():void
    {
        $userAdmin = User::factory()->create([
            "name" => "TestUserAdmin",
            "email" => "TestUserAdmin@test.com",
            "password" => bcrypt("testpass"),
            "type" => UserType::Admin
        ]);

        Sanctum::actingAs($userAdmin);

        $response=$this->post("api/orders/delete/100");

        $response->assertStatus(404);
    }

}
