<?php

namespace Tests\Unit;

use Illuminate\Auth\Events\Registered;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\PropertyType;
use App\Region;
use App\Service;
use App\Language;

class RegistrationTest extends TestCase
{
    use WithFaker, RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->seed();
    }

    public function test_register_as_broker()
    {
        $paths = [];

        for ($i = 0; $i < $this->faker->numberBetween(1, 10); $i++) {
            $property_type = PropertyType::all()->random();
            $price_from = $this->faker->numberBetween(1, 100000);

            $paths[] = [
                'property_type' => $property_type->id,
                'properties' => $property_type->properties->random()->id,
                'regions' => Region::all()->random($this->faker->numberBetween(1, Region::all()->count()))->pluck('id')->toArray(),
                'services' => Service::all()->random($this->faker->numberBetween(1, Service::all()->count()))->pluck('id')->toArray(),
                'price_from' => $price_from,
                'price_to' => $this->faker->numberBetween($price_from, 200000)
            ];
        }

        $password = $this->faker->password(8);

        $data = [
            'name' => $this->faker->name,
            'agency' => $this->faker->company,
            'language' => Language::all()->random(2)->pluck('id')->toArray(),
            'description' => $this->faker->realText(255),
            'phone' => $this->faker->numerify('+370########'),
            'email' => $this->faker->email,
            'website' => $this->faker->url,
            'represent_video' => $this->faker->url,
            'password' => $password,
            'password_confirmation' => $password,
            'agreement' => true,
            'paths' => $paths
        ];

        $this->expectsEvents(Registered::class);

        $response = $this->postJson(route('register.broker'), $data);

        $response->assertStatus(200);
    }

    public function test_register_as_regular()
    {
        $password = $this->faker->password(8);

        $data = [
            'name' => $this->faker->name,
            'email' => $this->faker->email,
            'password' => $password,
            'password_confirmation' => $password,
            'agreement' => true,
        ];

        $this->expectsEvents(Registered::class);

        $response = $this->postJson(route('register.regular'), $data);
        $response->assertStatus(200);
    }
}
