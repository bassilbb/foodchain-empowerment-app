<?php

namespace Tests\Feature\Account;

use App\Models\Notification;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CustomerNotificationTest extends TestCase
{
    use RefreshDatabase;

    private User $user;

    protected function setUp(): void
    {
        parent::setUp();

        $this->user = User::factory()->create([
            'is_active' => true,
            'membership_expires_at' => now()->addYear(),
        ]);
    }

    private function notification(User $user, array $overrides = []): Notification
    {
        return Notification::create(array_merge([
            'user_id' => $user->id,
            'type' => 'order',
            'title' => 'Order placed',
            'message' => 'Your order has been received.',
            'is_read' => false,
        ], $overrides));
    }

    public function test_guest_cannot_view_notifications(): void
    {
        $this->get(route('account.notifications.index'))->assertRedirect(route('login'));
    }

    public function test_customer_can_view_the_notification_centre(): void
    {
        $this->notification($this->user, ['title' => 'Payment successful']);

        $this->actingAs($this->user)
            ->get(route('account.notifications.index'))
            ->assertOk()
            ->assertSee('Payment successful');
    }

    public function test_empty_state_is_shown_with_no_notifications(): void
    {
        $this->actingAs($this->user)
            ->get(route('account.notifications.index'))
            ->assertOk()
            ->assertSee('You are all caught up.');
    }

    public function test_customer_only_sees_their_own_notifications(): void
    {
        $other = User::factory()->create(['is_active' => true]);

        $this->notification($this->user, ['title' => 'Mine here']);
        $this->notification($other, ['title' => 'Theirs here']);

        $this->actingAs($this->user)
            ->get(route('account.notifications.index'))
            ->assertOk()
            ->assertSee('Mine here')
            ->assertDontSee('Theirs here');
    }

    public function test_mark_as_read_sets_both_is_read_and_read_at(): void
    {
        $notification = $this->notification($this->user);

        $this->actingAs($this->user)
            ->post(route('account.notifications.read', $notification))
            ->assertRedirect();

        $fresh = $notification->fresh();
        $this->assertTrue((bool) $fresh->is_read);
        $this->assertNotNull($fresh->read_at, 'read_at must be stamped alongside is_read.');
    }

    public function test_mark_all_as_read_clears_every_unread_notification(): void
    {
        $this->notification($this->user);
        $this->notification($this->user);
        $this->notification($this->user, ['is_read' => true]);

        $this->actingAs($this->user)
            ->post(route('account.notifications.mark-all-read'))
            ->assertRedirect();

        $this->assertSame(
            0,
            Notification::where('user_id', $this->user->id)->where('is_read', false)->count()
        );
    }

    public function test_mark_all_as_read_does_not_touch_other_customers(): void
    {
        $other = User::factory()->create(['is_active' => true]);
        $theirs = $this->notification($other);

        $this->notification($this->user);

        $this->actingAs($this->user)->post(route('account.notifications.mark-all-read'));

        $this->assertFalse((bool) $theirs->fresh()->is_read);
    }

    public function test_customer_can_delete_their_own_notification(): void
    {
        $notification = $this->notification($this->user);

        $this->actingAs($this->user)
            ->delete(route('account.notifications.destroy', $notification))
            ->assertRedirect();

        $this->assertDatabaseMissing('notifications', ['id' => $notification->id]);
    }

    public function test_customer_cannot_delete_another_customers_notification(): void
    {
        $other = User::factory()->create(['is_active' => true]);
        $theirs = $this->notification($other);

        $this->actingAs($this->user)
            ->delete(route('account.notifications.destroy', $theirs))
            ->assertNotFound();

        $this->assertDatabaseHas('notifications', ['id' => $theirs->id]);
    }

    public function test_customer_cannot_mark_another_customers_notification_as_read(): void
    {
        $other = User::factory()->create(['is_active' => true]);
        $theirs = $this->notification($other);

        $this->actingAs($this->user)
            ->post(route('account.notifications.read', $theirs))
            ->assertNotFound();

        $this->assertFalse((bool) $theirs->fresh()->is_read);
    }

    public function test_unread_filter_shows_only_unread_notifications(): void
    {
        $this->notification($this->user, ['title' => 'Unread one', 'is_read' => false]);
        $this->notification($this->user, ['title' => 'Read one', 'is_read' => true]);

        $response = $this->actingAs($this->user)
            ->get(route('account.notifications.index', ['unread' => '1']));

        $response->assertOk()
            ->assertViewHas('notifications', function ($notifications) {
                // The header dropdown shows recent items regardless of filter, so
                // assert against the filtered result set instead of the raw HTML.
                return $notifications->total() === 1
                    && $notifications->first()->title === 'Unread one';
            });
    }

    public function test_type_filter_narrows_by_notification_category(): void
    {
        $this->notification($this->user, ['type' => 'order.shipped', 'title' => 'An order update']);
        $this->notification($this->user, ['type' => 'payment.successful', 'title' => 'A payment update']);

        $response = $this->actingAs($this->user)
            ->get(route('account.notifications.index', ['type' => 'payments']));

        $response->assertOk()
            ->assertViewHas('notifications', function ($notifications) {
                // Assert on the filtered result set, not the whole page: the
                // header notification dropdown intentionally shows the latest
                // notifications regardless of the active filter.
                return $notifications->total() === 1
                    && $notifications->first()->title === 'A payment update';
            });
    }

    public function test_unread_count_is_exposed_to_the_view(): void
    {
        $this->notification($this->user);
        $this->notification($this->user);

        $this->actingAs($this->user)
            ->get(route('account.notifications.index'))
            ->assertOk()
            ->assertViewHas('unreadNotifications', 2);
    }

    public function test_clear_all_removes_only_the_authenticated_customers_notifications(): void
    {
        $other = User::factory()->create(['is_active' => true]);

        $this->notification($this->user);
        $mine = $this->notification($this->user);
        $theirs = $this->notification($other);

        $this->actingAs($this->user)
            ->delete(route('account.notifications.clear-all'))
            ->assertRedirect();

        $this->assertDatabaseMissing('notifications', ['id' => $mine->id]);
        $this->assertDatabaseHas('notifications', ['id' => $theirs->id]);
    }
}
