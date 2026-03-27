<?php

use App\Filament\Resources\Clients\Pages\CreateClient;
use App\Filament\Resources\Clients\Pages\EditClient;
use App\Filament\Resources\Clients\Pages\ListClients;
use App\Models\Client;
use App\Models\User;
use Livewire\Livewire;

use function Pest\Laravel\actingAs;
use function Pest\Laravel\assertDatabaseHas;

beforeEach(function () {
    actingAs(User::factory()->create());
});

describe('list page', function () {
    it('loads successfully with records', function () {
        $clients = Client::factory()->count(3)->create();

        Livewire::test(ListClients::class)
            ->assertOk()
            ->assertCanSeeTableRecords($clients);
    });
});

describe('create page', function () {
    it('loads successfully', function () {
        Livewire::test(CreateClient::class)->assertOk();
    });

    it('can create a client', function () {
        $newClient = Client::factory()->make();

        Livewire::test(CreateClient::class)
            ->fillForm([
                'name' => $newClient->name,
                'email' => $newClient->email,
                'cpf' => $newClient->cpf,
            ])
            ->call('create')
            ->assertNotified()
            ->assertRedirect();

        assertDatabaseHas(Client::class, [
            'email' => $newClient->email,
            'cpf' => $newClient->cpf,
        ]);
    });

    it('validates required fields', function (array $data, array $errors) {
        $newClient = Client::factory()->make();

        Livewire::test(CreateClient::class)
            ->fillForm([
                'name' => $newClient->name,
                'email' => $newClient->email,
                'cpf' => $newClient->cpf,
                ...$data,
            ])
            ->call('create')
            ->assertHasFormErrors($errors)
            ->assertNotNotified();
    })->with([
        'name is required' => [['name' => null], ['name' => 'required']],
        'email is required' => [['email' => null], ['email' => 'required']],
        'email must be valid' => [['email' => 'not-an-email'], ['email' => 'email']],
        'cpf is required' => [['cpf' => null], ['cpf' => 'required']],
    ]);

    it('validates email uniqueness', function () {
        $existing = Client::factory()->create();

        Livewire::test(CreateClient::class)
            ->fillForm([
                'name' => 'New Name',
                'email' => $existing->email,
                'cpf' => '999.888.777-66',
            ])
            ->call('create')
            ->assertHasFormErrors(['email' => 'unique'])
            ->assertNotNotified();
    });

    it('validates cpf uniqueness', function () {
        $existing = Client::factory()->create();

        Livewire::test(CreateClient::class)
            ->fillForm([
                'name' => 'New Name',
                'email' => 'new@example.com',
                'cpf' => $existing->cpf,
            ])
            ->call('create')
            ->assertHasFormErrors(['cpf' => 'unique'])
            ->assertNotNotified();
    });
});

describe('edit page', function () {
    it('loads with correct state', function () {
        $client = Client::factory()->create();

        Livewire::test(EditClient::class, ['record' => $client->id])
            ->assertOk()
            ->assertSchemaStateSet([
                'name' => $client->name,
                'email' => $client->email,
                'cpf' => $client->cpf,
            ]);
    });

    it('can update a client', function () {
        $client = Client::factory()->create();
        $newData = Client::factory()->make();

        Livewire::test(EditClient::class, ['record' => $client->id])
            ->fillForm([
                'name' => $newData->name,
                'email' => $newData->email,
            ])
            ->call('save')
            ->assertNotified();

        assertDatabaseHas(Client::class, [
            'id' => $client->id,
            'name' => $newData->name,
            'email' => $newData->email,
        ]);
    });

    it('allows same email when editing own record', function () {
        $client = Client::factory()->create();

        Livewire::test(EditClient::class, ['record' => $client->id])
            ->fillForm(['email' => $client->email])
            ->call('save')
            ->assertHasNoFormErrors();
    });

    it('validates email uniqueness against other records', function () {
        $other = Client::factory()->create();
        $client = Client::factory()->create();

        Livewire::test(EditClient::class, ['record' => $client->id])
            ->fillForm(['email' => $other->email])
            ->call('save')
            ->assertHasFormErrors(['email' => 'unique']);
    });
});
