<?php

namespace Tests\Feature\Messaging;

use App\Models\Messaging\ContactMessage;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ContactMessageTest extends TestCase
{
    use RefreshDatabase;

    public function test_can_create_contact_message(): void
    {
        $message = ContactMessage::create([
            'name' => 'Test User',
            'phone' => '+966501234567',
            'email' => 'test@example.com',
            'subject' => 'Test Subject',
            'message' => 'Test message content',
            'is_read' => false,
        ]);

        $this->assertDatabaseHas('contact_messages', [
            'id' => $message->id,
            'name' => 'Test User',
            'is_read' => false,
        ]);
    }

    public function test_can_mark_contact_message_as_read(): void
    {
        $message = ContactMessage::create([
            'name' => 'Test User',
            'message' => 'Test message',
            'is_read' => false,
        ]);

        $message->update(['is_read' => true]);

        $this->assertDatabaseHas('contact_messages', [
            'id' => $message->id,
            'is_read' => true,
        ]);
    }

    public function test_can_soft_delete_contact_message(): void
    {
        $message = ContactMessage::create([
            'name' => 'Test User',
            'message' => 'Test message',
        ]);

        $message->delete();

        $this->assertSoftDeleted('contact_messages', [
            'id' => $message->id,
        ]);
    }
}
