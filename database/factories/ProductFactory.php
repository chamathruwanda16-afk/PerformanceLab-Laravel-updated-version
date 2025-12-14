<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\Category;

class ProductFactory extends Factory
{
    public function definition(): array
    {
        $categories = Category::pluck('id')->toArray();

        return [
            'category_id' => $this->faker->randomElement($categories),
            'name' => $this->faker->randomElement([
                'HKS Turbo Kit',
                'Greddy Intercooler',
                'Tein Street Advance Z Coilovers',
                'Invidia N1 Exhaust',
                'Cusco Strut Tower Bar',
                'Bride Racing Seat',
                'Nardi Steering Wheel',
                'Tomei Fuel Regulator',
                'Blitz Air Intake System',
                'Spoon Sports Radiator Cap'
            ]),
            'slug' => $this->faker->unique()->slug(),
            'description' => $this->faker->sentence(15),
            'price' => $this->faker->randomFloat(2, 50000, 350000),
            'stock' => $this->faker->numberBetween(2, 15),
            'image_path' => $this->faker->randomElement([
                'hks-turbo.jpg',
                'greddy-intercooler.jpg',
                'tein-coilovers.jpg',
                'invidia-n1.jpg',
                'cusco-strut.jpg',
                'bride-seat.jpg',
                'nardi-wheel.jpg',
                'tomei-fuel.jpg',
                'blitz-intake.jpg',
                'spoon-cap.jpg'
            ]),
        ];
    }
}
