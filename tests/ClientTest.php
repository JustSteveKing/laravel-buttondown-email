<?php

declare(strict_types=1);

namespace JustSteveKing\Laravel\ButtonDownEmail\Tests;

use RuntimeException;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Http;
use JustSteveKing\ParameterBag\ParameterBag;
use JustSteveKing\Laravel\ButtonDownEmail\Client;
use JustSteveKing\Laravel\ButtonDownEmail\DTO\BaseSubscriber;
use JustSteveKing\Laravel\ButtonDownEmail\Resources\Subscribers;
use JustSteveKing\Laravel\ButtonDownEmail\Contracts\ClientContract;
use Symfony\Component\HttpFoundation\Response;

class ClientTest extends TestCase
{
    protected Client $client;
    protected string $testEmail;

    public function setUp(): void
    {
        parent::setUp();

        $this->client = resolve(Client::class);
        $this->testEmail = 'juststevemcd+test@gmail.com';

        $endpoint = config('buttondown.api.url');
        $this->client->fake([
            "{$endpoint}/ping" => Http::response(
                body: null,
                status: Response::HTTP_OK,
            ),
            "{$endpoint}/subscribers" => Http::response(
                body: [
                    'count' => 1,
                    'results' => [
                        [
                            'creation_date' => '2021-05-14T11:37:29.828600Z',
                            'email' => 'test@email.com',
                            'id' => '12341234-1234-1234-1234-12341234',
                            'notes' => 'testing testing',
                            'referrer_url' => 'https://buttondown.email.subscribers',
                            'metadata' => [],
                            'secondary_id' => 1,
                            'subscriber_type' => 'regular',
                            'source' => 'organic',
                            'tags' => [],
                            'utm_campaign' => '',
                            'utm_medium' => '',
                            'utm_source' => '',
                        ]
                    ]
                ]
            )
        ]);
    }

    /**
     * @test
     */
    public function it_can_create_a_new_client()
    {
        $this->assertInstanceOf(
            expected: Client::class,
            actual: $this->client,
        );

        $this->assertInstanceOf(
            expected: ClientContract::class,
            actual: $this->client,
        );
    }

    /**
     * @test
     */
    public function it_can_hit_the_ping_endpoint()
    {
        $this->assertTrue(
            condition: $this->client->ping(),
        );
    }

    /**
     * @test
     */
    public function it_can_get_a_list_of_subscribers()
    {
        $subscribers = $this->client->subscribers()->get();

        $this->assertInstanceOf(
            expected: Collection::class,
            actual: $subscribers,
        );

        $this->assertCount(
            expectedCount: 1,
            haystack: $subscribers,
        );

        $subscribers = $this->client->subscribers()->all();

        $this->assertInstanceOf(
            expected: Collection::class,
            actual: $subscribers,
        );

        $this->assertCount(
            expectedCount: 1,
            haystack: $subscribers,
        );
    }

    /**
     * @test
     */
    public function it_can_filter_the_subscribers()
    {
        $subscribers = $this->client->subscribers();

        $this->assertInstanceOf(
            expected: Subscribers::class,
            actual: $subscribers,
        );

        $regular = $subscribers->where('type', 'regular');

        $this->assertInstanceOf(
            expected: ParameterBag::class,
            actual: $regular->url->query(),
        );

        $this->assertTrue(
            condition: $regular->url->query()->has(
                key: 'type'
            ),
        );
    }

    /**
     * @test
     */
    public function it_handles_a_validation_exception()
    {
        $this->expectException(
            exception: RuntimeException::class,
        );

        $this->client->subscribers()->create([]);
    }

    /**
     * @test
     */
    public function it_validates_creating_a_subscriber()
    {
        $this->expectException(
            exception: RuntimeException::class
        );

        $subscriber = $this->client->subscribers()->create(
            attributes: [
                'foo' => 'bar',
            ],
        );
    }

    /**
     * @test
     */
    public function it_can_create_a_new_subscriber()
    {
        $subscriber = $this->client->subscribers()->create(
            attributes: [
                'email' => $this->testEmail,
            ],
        );

        $this->assertInstanceOf(
            expected: BaseSubscriber::class,
            actual: $subscriber,
        );

        $this->assertEquals(
            expected: $this->testEmail,
            actual: $subscriber->email,
        );
    }

    /**
     * @test
     */
    public function it_can_find_a_subscriber()
    {
        $subscribers = $this->client->subscribers()->get();

        $subscriber = $this->client->subscribers()->find(
            id: $subscribers->first()->id,
        );

        $this->assertInstanceOf(
            expected: BaseSubscriber::class,
            actual: $subscriber,
        );
    }

    /**
     * @test
     */
    public function it_can_update_a_subscriber()
    {
        $subscribers = $this->client->subscribers()->get();

        $subscriber = $this->client->subscribers()->find(
            id: $subscribers->first()->id,
        );

        $response = $this->client->subscribers()->update(
            attributes: [
                'email' => $subscriber->email,
                'notes' => 'A test note from the Laravel Buttondown Email Package'
            ],
            id: $subscriber->id,
        );

        $this->assertInstanceOf(
            expected: BaseSubscriber::class,
            actual: $response,
        );

        $this->assertEquals(
            expected: 'A test note from the Laravel Buttondown Email Package',
            actual: $response->notes,
        );
    }

    /**
     * @test
     */
    public function it_can_delete_a_subscriber()
    {
        $subscribers = $this->client->subscribers()->get();

        $subscriber = $this->client->subscribers()->find(
            id: $subscribers->first()->id,
        );

        $response = $this->client->subscribers()->delete(
            id: $subscriber->id,
        );

        $this->assertTrue(
            condition: $response,
        );
    }

    /**
     * @test
     */
    public function it_can_get_a_list_of_unsubscribers()
    {
        $unsubscribers = $this->client->unsubscribers()->get();

        $this->assertInstanceOf(
            expected: Collection::class,
            actual: $unsubscribers,
        );

        $this->assertCount(
            expectedCount: 1,
            haystack: $unsubscribers,
        );

        $unsubscribers = $this->client->unsubscribers()->all();

        $this->assertInstanceOf(
            expected: Collection::class,
            actual: $unsubscribers,
        );

        $this->assertCount(
            expectedCount: 1,
            haystack: $unsubscribers,
        );
    }

    /**
     * @test
     */
    public function it_can_find_an_unsubscriber()
    {
        $unsubscribers = $this->client->unsubscribers()->get();

        $unsubscriber = $this->client->unsubscribers()->find(
            id: $unsubscribers->first()->id,
        );

        $this->assertInstanceOf(
            expected: BaseSubscriber::class,
            actual: $unsubscriber,
        );
    }
}
