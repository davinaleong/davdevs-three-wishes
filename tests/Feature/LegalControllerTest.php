<?php

use App\Models\User;

it('displays legal terms page', function () {
    $response = $this->get(route('legal.terms'));

    $response->assertStatus(200)
        ->assertViewIs('legal.terms')
        ->assertSee('Terms and Conditions')
        ->assertSee('Dav/Devs Three Wishes')
        ->assertSee('Christian');
});

it('displays privacy policy page', function () {
    $response = $this->get(route('legal.privacy'));

    $response->assertStatus(200)
        ->assertViewIs('legal.privacy')
        ->assertSee('Privacy Policy')
        ->assertSee('GDPR')
        ->assertSee('PDPA')
        ->assertSee('Dav/Devs Three Wishes');
});

it('legal pages are publicly accessible', function () {
    // Test without authentication
    $this->get(route('legal.terms'))->assertStatus(200);
    $this->get(route('legal.privacy'))->assertStatus(200);
    
    // Test with authentication
    $user = User::factory()->create();
    $this->actingAs($user)->get(route('legal.terms'))->assertStatus(200);
    $this->actingAs($user)->get(route('legal.privacy'))->assertStatus(200);
});

it('contains required legal content in terms', function () {
    $response = $this->get(route('legal.terms'));

    $response->assertSee('Description of Service')
        ->assertSee('User Accounts and Registration')
        ->assertSee('Privacy and Data Protection')
        ->assertSee('Intellectual Property')
        ->assertSee('Limitation of Liability')
        ->assertSee('support@gracesoft.dev');
});

it('contains required privacy content', function () {
    $response = $this->get(route('legal.privacy'));

    $response->assertSee('Data Controller')
        ->assertSee('Legal Basis for Processing')
        ->assertSee('Your Rights Under GDPR')
        ->assertSee('Data Retention')
        ->assertSee('Security Measures')
        ->assertSee('privacy@gracesoft.dev');
});