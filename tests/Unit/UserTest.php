<?php

namespace Tests\Unit;

use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class UserTest extends TestCase
{
    /** @test */
    public function user_attributes_are_fillable()
    {
        $user = new User([
            'name' => 'John Doe',
            'email' => 'john@example.com',
            'password' => 'password',
        ]);

        $this->assertEquals('John Doe', $user->name);
        $this->assertEquals('john@example.com', $user->email);
    }

    /** @test */
    public function user_attributes_are_hidden()
    {
        $user = new User();
        $hiddenAttributes = $user->getHidden();

        $this->assertContains('password', $hiddenAttributes);
        $this->assertContains('remember_token', $hiddenAttributes);
    }

    /** @test */
    public function user_attributes_are_casted()
    {
        $user = new User();
        $casts = $user->getCasts();

        $this->assertArrayHasKey('email_verified_at', $casts);
        $this->assertArrayHasKey('password', $casts);
        $this->assertEquals('hashed', $casts['password']);
    }

    /** @test */
    public function password_is_hashed_when_set()
    {
        $passwordPlainText = 'password';
        $user = new User([
            'name' => 'John Doe',
            'email' => 'john@example.com',
            'password' => $passwordPlainText,
        ]);

        $this->assertNotEquals($passwordPlainText, $user->password);
        $this->assertTrue(Hash::check($passwordPlainText, $user->password));
    }
}
