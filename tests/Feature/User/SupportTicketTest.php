<?php

namespace Tests\Feature\User;

use App\Models\MembershipTier;
use App\Models\SupportTicket;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SupportTicketTest extends TestCase
{
    use RefreshDatabase;

    private User $user;

    protected function setUp(): void
    {
        parent::setUp();

        $this->withoutMiddleware(\Illuminate\Foundation\Http\Middleware\ValidateCsrfToken::class);

        $tier = MembershipTier::factory()->create(['slug' => 'bronze', 'annual_fee' => 5000, 'instant_food_pack_price' => 20000]);
        $this->user = User::factory()->create(['current_tier_id' => $tier->id]);
    }

    public function test_create_screen_can_be_rendered(): void
    {
        $response = $this->actingAs($this->user)->get(route('support.create'));

        $response->assertStatus(200);
    }

    public function test_user_can_create_ticket(): void
    {
        $response = $this->actingAs($this->user)->post(route('support.store'), [
            'subject' => 'Test subject',
            'category' => 'billing',
            'message' => 'Test message content',
        ]);

        $response->assertSessionHasNoErrors();
        $response->assertRedirect();

        $this->assertDatabaseHas('support_tickets', [
            'user_id' => $this->user->id,
            'subject' => 'Test subject',
            'category' => 'billing',
            'message' => 'Test message content',
            'status' => 'open',
        ]);

        $ticket = SupportTicket::where('user_id', $this->user->id)->first();
        $response->assertRedirect(route('support.show', $ticket->id));
    }

    public function test_user_can_view_own_ticket(): void
    {
        $ticket = SupportTicket::create([
            'user_id' => $this->user->id,
            'subject' => 'Test',
            'category' => 'billing',
            'message' => 'Test',
            'status' => 'open',
        ]);

        $response = $this->actingAs($this->user)->get(route('support.show', $ticket->id));

        $response->assertStatus(200);
        $response->assertSee('Test');
    }

    public function test_user_cannot_view_others_ticket(): void
    {
        $otherUser = User::factory()->create();
        $ticket = SupportTicket::create([
            'user_id' => $otherUser->id,
            'subject' => 'Other ticket',
            'category' => 'billing',
            'message' => 'Secret',
            'status' => 'open',
        ]);

        $response = $this->actingAs($this->user)->get(route('support.show', $ticket->id));

        $response->assertStatus(403);
    }

    public function test_user_can_reply_to_own_ticket(): void
    {
        $ticket = SupportTicket::create([
            'user_id' => $this->user->id,
            'subject' => 'Test',
            'category' => 'billing',
            'message' => 'Test',
            'status' => 'open',
        ]);

        $response = $this->actingAs($this->user)->post(route('support.reply', $ticket->id), [
            'message' => 'My reply',
        ]);

        $response->assertSessionHas('success');
        $this->assertDatabaseHas('ticket_replies', [
            'ticket_id' => $ticket->id,
            'message' => 'My reply',
        ]);
    }

    public function test_user_cannot_reply_to_closed_ticket(): void
    {
        $ticket = SupportTicket::create([
            'user_id' => $this->user->id,
            'subject' => 'Test',
            'category' => 'billing',
            'message' => 'Test',
            'status' => 'closed',
        ]);

        $response = $this->actingAs($this->user)->post(route('support.reply', $ticket->id), [
            'message' => 'Should not work',
        ]);

        $response->assertSessionHas('error');
        $this->assertDatabaseMissing('ticket_replies', [
            'ticket_id' => $ticket->id,
            'message' => 'Should not work',
        ]);
    }

    public function test_user_cannot_reply_to_others_ticket(): void
    {
        $otherUser = User::factory()->create();
        $ticket = SupportTicket::create([
            'user_id' => $otherUser->id,
            'subject' => 'Test',
            'category' => 'billing',
            'message' => 'Test',
            'status' => 'open',
        ]);

        $response = $this->actingAs($this->user)->post(route('support.reply', $ticket->id), [
            'message' => 'My reply',
        ]);

        $response->assertStatus(403);
    }

    public function test_user_can_close_own_ticket(): void
    {
        $ticket = SupportTicket::create([
            'user_id' => $this->user->id,
            'subject' => 'Test',
            'category' => 'billing',
            'message' => 'Test',
            'status' => 'open',
        ]);

        $response = $this->actingAs($this->user)->post(route('support.close', $ticket->id));

        $response->assertRedirect(route('support.index'));
        $this->assertEquals('closed', $ticket->fresh()->status);
    }

    public function test_user_cannot_close_others_ticket(): void
    {
        $otherUser = User::factory()->create();
        $ticket = SupportTicket::create([
            'user_id' => $otherUser->id,
            'subject' => 'Test',
            'category' => 'billing',
            'message' => 'Test',
            'status' => 'open',
        ]);

        $response = $this->actingAs($this->user)->post(route('support.close', $ticket->id));

        $response->assertStatus(403);
    }

    public function test_last_activity_at_returns_ticket_created_at_when_no_replies(): void
    {
        $ticket = SupportTicket::create([
            'user_id' => $this->user->id,
            'subject' => 'Test',
            'category' => 'billing',
            'message' => 'Test',
            'status' => 'open',
        ]);

        $this->assertEquals(
            $ticket->created_at->toDateTimeString(),
            $ticket->last_activity_at->toDateTimeString()
        );
    }

    public function test_last_activity_at_returns_latest_reply_created_at(): void
    {
        $ticket = SupportTicket::create([
            'user_id' => $this->user->id,
            'subject' => 'Test',
            'category' => 'billing',
            'message' => 'Test',
            'status' => 'open',
        ]);

        $reply = $ticket->replies()->create([
            'sender_type' => get_class($this->user),
            'sender_id' => $this->user->id,
            'message' => 'A reply',
        ]);

        $this->assertEquals(
            $reply->created_at->toDateTimeString(),
            $ticket->last_activity_at->toDateTimeString()
        );
    }
}
