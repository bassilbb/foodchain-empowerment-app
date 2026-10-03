<?php

namespace Tests\Feature\Qa;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Mail;
use Tests\TestCase;

/**
 * Regression tests for the public landing, sponsor, supplier and rider forms.
 *
 * Guards the full form lifecycle: valid submissions return the user with a
 * success message, invalid payloads bounce back with validation errors, and
 * no real email is ever dispatched during the test run.
 */
class FormSubmissionsTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        Mail::fake();
    }

    public static function validSubmissions(): array
    {
        return [
            'contact' => ['landing.contact', [
                'name' => 'Ada Obi',
                'email' => 'ada@example.com',
                'subject' => 'Membership help',
                'message' => 'How do I join the nearest union?',
            ]],
            'newsletter' => ['landing.newsletter', [
                'email' => 'ada@example.com',
            ]],
            'sponsor' => ['landing.sponsor', [
                'organization' => 'Zenith Supplies',
                'name' => 'Ada Obi',
                'email' => 'ada@example.com',
                'message' => 'We would like to sponsor the July distribution.',
            ]],
            'supplier' => ['landing.supplier', [
                'name' => 'Ada Obi',
                'organization' => 'Farm Fresh Ltd',
                'email' => 'ada@example.com',
                'message' => 'We supply rice and beans in Lagos.',
            ]],
            'rider' => ['landing.rider', [
                'name' => 'Chidi Eze',
                'phone' => '08012345678',
                'email' => 'chidi@example.com',
                'vehicle' => 'Boxer motorcycle',
                'address' => '12 Broad Street, Lagos',
            ]],
        ];
    }

    /** @dataProvider validSubmissions */
    public function test_valid_submission_redirects_with_success(string $route, array $payload): void
    {
        $from = ['landing.contact' => '/contact', 'landing.newsletter' => '/', 'landing.sponsor' => '/sponsor', 'landing.supplier' => '/supplier', 'landing.rider' => '/rider'][$route];

        $this->from($from)
            ->post(route($route), $payload)
            ->assertRedirect()
            ->assertSessionHas('success');
    }

    public function test_contact_form_rejects_invalid_email(): void
    {
        $this->from('/contact')
            ->post(route('landing.contact'), [
                'name' => 'Ada Obi',
                'email' => 'not-an-email',
                'subject' => 'Help',
                'message' => 'Hello',
            ])
            ->assertSessionHasErrors('email');
    }

    public function test_newsletter_form_requires_an_email(): void
    {
        $this->from('/')
            ->post(route('landing.newsletter'), ['email' => ''])
            ->assertSessionHasErrors('email');
    }

    public function test_rider_form_requires_all_fields(): void
    {
        $this->from('/rider')
            ->post(route('landing.rider'), [
                'name' => 'Chidi Eze',
                'phone' => '',
                'email' => 'chidi@example.com',
                'vehicle' => 'Bike',
                'address' => '',
            ])
            ->assertSessionHasErrors(['phone', 'address']);
    }

    public function test_sponsor_form_rejects_an_invalid_organization(): void
    {
        $this->from('/sponsor')
            ->post(route('landing.sponsor'), [
                'organization' => '',
                'name' => 'Ada Obi',
                'email' => 'ada@example.com',
            ])
            ->assertSessionHasErrors('organization');
    }
}