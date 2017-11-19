<?php

use Phinx\Seed\AbstractSeed;
use Faker\Factory;

class UserSeeder extends AbstractSeed
{
    public function run()
    {
        $faker = Factory::create();
        $data = [];

        for ($i = 0; $i < 10; $i++) {
            $data[] = [
                'username'      => $faker->email,
                'password'      => sha1($faker->password),
                'firstname'     => $faker->firstName,
                'lastname'      => $faker->lastName,
                'age'           => rand(12, 70),
            ];
        }

        $this->insert('users', $data);
    }
}
