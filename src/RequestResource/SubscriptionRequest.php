<?php

declare(strict_types=1);

namespace EIU\LLIntegration\RequestResource;

use EIU\LLIntegration\Resource\AbstractApiResource;
use EIU\LLIntegration\Resource\Subscription;

/**
 * Class SubscriptionRequest
 *
 * @package EIU\LLIntegration\RequestResource
 */
class SubscriptionRequest extends AbstractApiRequest
{
    /**
     * {@inheritdoc}
     */
    public function getRequestDataJSON(): string
    {
        $data = [
            'title'        => $_POST['llsub_title'],
            'package_code' => $_POST['llsub_package_code'],
            'trial'        => false,
            'perpetual'    => false,
            'start'        => $_POST['llsub_start'],
            'end'          => $_POST['llsub_end'],
        ];

        return json_encode($data);
    }

    /**
     * {@inheritdoc}
     */
    public function getApiEndpoint(): string
    {
        return '@account_subs';
    }

    /**
     * {@inheritdoc}
     */
    public function createResource(mixed $response): AbstractApiResource
    {
        return new Subscription($response);
    }

    /**
     * {@inheritdoc}
     */
    public function getFailLogMessage(): string
    {
        return 'Subscription request failed {payload}';
    }

    /**
     * {@inheritdoc}
     */
    public function getSuccessLogMessage(): string
    {
        return 'Subscription request for title {title} succeeded id={id} package_code={package_code}';
    }

    /**
     * {@inheritdoc}
     */
    public function getSuccessLogContext(AbstractApiResource $resource): array
    {
        return [
            'id'           => $resource->id,
            'title'        => $resource->title,
            'package_code' => $resource->package_code,
            'active'     => $resource->active,
        ];
    }
}
