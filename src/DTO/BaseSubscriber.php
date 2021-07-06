<?php

declare(strict_types=1);

namespace JustSteveKing\Laravel\ButtonDownEmail\DTO;

use Carbon\Carbon;
use RuntimeException;
use Illuminate\Support\Facades\Validator;
use Symfony\Component\HttpFoundation\Response;
use JustSteveKing\Laravel\ButtonDownEmail\Contracts\SubscriberContract;

class BaseSubscriber implements SubscriberContract
{
    public function __construct(
        public null|Carbon $creationDate,
        public string $email,
        public string $id,
        public string $notes,
        public string $referrerUrl,
        public array $metadata,
        public int $secondaryID,
        public null|string $type,
        public string $source,
        public array $tags,
        public string $utmCampaign,
        public string $utmMedium,
        public string $utmSource,
    ) {}

    public static function hydrate(array $attributes): self
    {
        return new self(
            creationDate: isset($attributes['creation_date'])
                ? Carbon::parse($attributes['creation_date'])
                : null,
            email: $attributes['email'],
            id: $attributes['id'],
            notes: $attributes['notes'],
            referrerUrl: $attributes['referrer_url'],
            metadata: $attributes['metadata'],
            secondaryID: $attributes['secondary_id'],
            type: $attributes['subscriber_type'] ?? null,
            source: $attributes['source'],
            tags: $attributes['tags'],
            utmCampaign: $attributes['utm_campaign'],
            utmMedium: $attributes['utm_medium'],
            utmSource: $attributes['utm_source'],
        );
    }

    public static function validate(array $attributes): void
    {
        $validator = Validator::make($attributes, [
            'email' => ['required', 'email:rfc,dns'],
            'metadata' => ['nullable', 'array'],
            'notes' => ['nullable', 'string'],
            'referrer_url' => ['nullable', 'string', 'max:500'],
            'tags' => ['nullable', 'array'],
        ]);

        if ($validator->fails()) {
            throw new RuntimeException(
                message: "Failed validation on request.",
                code: Response::HTTP_UNPROCESSABLE_ENTITY,
            );
        }
    }
}
