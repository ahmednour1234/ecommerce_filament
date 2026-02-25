<?php

namespace Tests\Feature\Messaging;

use App\Models\Messaging\MessageContact;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class MessageContactTest extends TestCase
{
    use RefreshDatabase;

    public function test_can_create_message_contact(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user);

        $contact = MessageContact::create([
            'name_ar' => 'Test Contact',
            'phone' => '+966501234567',
            'source' => 'manual',
        ]);

        $this->assertDatabaseHas('message_contacts', [
            'id' => $contact->id,
            'name_ar' => 'Test Contact',
            'phone' => '+966501234567',
        ]);
    }

    public function test_can_soft_delete_message_contact(): void
    {
        $contact = MessageContact::create([
            'name_ar' => 'Test Contact',
            'phone' => '+966501234567',
            'source' => 'manual',
        ]);

        $contact->delete();

        $this->assertSoftDeleted('message_contacts', [
            'id' => $contact->id,
        ]);
    }
}
