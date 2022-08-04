<?php

// Copyright 2021 Picallex Holding Group. All rights reserved.
//
// @author (2021) Jovany Leandro G.C <jovany@picallex.com>

declare(strict_types=1);

namespace Cloudpbx\Sdk;

use Cloudpbx\Util\Argument;

/**
 * @phpstan-import-type RecordTransformer from Api
 */
class Sound extends Api
{
    /**
     * @param int $customer_id
     * @return array<Model\Sound>
     */
    public function all($customer_id)
    {
        Argument::isInteger($customer_id);

        $query = $this->protocol->prepareQuery('/api/v1/management/customers/{customer_id}/sounds', ['{customer_id}' => $customer_id]);

        $records = $this->protocol->list(
            $query
        );

        return $this->recordsToModel(
            $records,
            Model\Sound::class,
            [
            'transform' => $this->transform($customer_id)
        ]
        );
    }

    /**
     * @param int $customer_id
     * @param int $id
     *
     * @return \Cloudpbx\Sdk\Model\Sound
     */
    public function show($customer_id, $id)
    {
        Argument::isInteger($customer_id);
        Argument::isInteger($id);

        $query = $this->protocol->prepareQuery('/api/v1/management/customers/{customer_id}/sounds/{sound_id}', [
            '{customer_id}' => $customer_id,
            '{sound_id}' => $id
        ]);

        $record = $this->protocol->list(
            $query
        );

        return $this->recordToModel(
            $record,
            Model\Sound::class,
            [
                                        'transform' => $this->transform($customer_id)
                                    ]
        );
    }

    /**
     * @return \Cloudpbx\Sdk\Model\User
     */
    public function create(int $customer_id, string $name, string $template, string $usage, string $soundpath)
    {
        Argument::isInteger($customer_id);
        Argument::isString($name);
        Argument::isString($template);
        Argument::isString($usage);
        Argument::isPath($soundpath);

        $query = $this->protocol->prepareQuery(
            '/api/v1/management/customers/{customer_id}/sounds?name={name}&template={template}&usage={usage}',
            [
                '{customer_id}' => $customer_id,
                '{name}' => $name,
                '{template}' => $template,
                '{usage}' => $usage
            ]);

        $content = file_get_contents($soundpath);
        $mime_type = mime_content_type($soundpath);
        $record = $this->protocol->createWithRaw($query, $content, ['content-type' => $mime_type]);

        return $this->recordToModel(
            $record,
            Model\Sound::class,
            [
                'transform' => $this->transform($customer_id)
            ]
        );
    }

    /**
     * append $customer_id to $record.
     *
     * @param integer $customer_id
     *
     * @return RecordTransformer
     */
    private function transform($customer_id)
    {
        return [
            function (&$record, $customer_id) {
                $record['customer_id'] = $customer_id;
            },
            [$customer_id]
        ];
    }
}
