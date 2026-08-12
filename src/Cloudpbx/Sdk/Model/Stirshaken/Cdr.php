<?php

// This file is part of cloudpbx-php-sdk.  The COPYRIGHT file at the top level of
// this repository contains the full copyright notices and license terms.
//
// @author (2026) Agustin Serra <agustin@picallex.com>

declare(strict_types=1);

namespace Cloudpbx\Sdk\Model\Stirshaken;

final class Cdr extends \Cloudpbx\Sdk\Model
{
    /**
     * @var int
     */
    public $id;

    /**
     * @var string
     */
    public $call_id;

    /**
     * @var string
     */
    public $from_uri;

    /**
     * @var string
     */
    public $to_uri;

    /**
     * @var string
     */
    public $source_ip;

    /**
     * @var int|null
     */
    public $source_port;

    /**
     * @var string|null
     */
    public $orig_tn;

    /**
     * @var string|null
     */
    public $dest_tn;

    /**
     * @var string|null
     */
    public $attest_level;

    /**
     * @var string|null
     */
    public $origid;

    /**
     * @var int|null
     */
    public $iat;

    /**
     * @var string|null
     */
    public $x5u_url;

    /**
     * @var string|null
     */
    public $identity_token;

    /**
     * @var array<string, mixed>|null
     */
    public $jwt_header;

    /**
     * @var array<string, mixed>|null
     */
    public $jwt_payload;

    /**
     * @var string|null
     */
    public $jwt_signature;

    /**
     * @var string|null
     */
    public $cert_validation_status;

    /**
     * @var string outbound|inbound
     */
    public $call_direction;

    /**
     * @var int|null
     */
    public $sip_response_code;

    /**
     * @var string|null
     */
    public $sip_reason_phrase;

    /**
     * @var string|null
     */
    public $block_type;

    /**
     * @var string|null
     */
    public $provider_id;

    /**
     * @var string|null
     */
    public $provider_name;

    /**
     * @var string|null ISO-8601
     */
    public $setup_time;

    /**
     * @var string|null ISO-8601
     */
    public $answer_time;

    /**
     * @var string|null ISO-8601
     */
    public $end_time;

    /**
     * @var int|null
     */
    public $duration;

    /**
     * @var string|null
     */
    public $hangup_cause;

    /**
     * @var string|null
     */
    public $sip_request;

    /**
     * @var string|null
     */
    public $sip_response;

    /**
     * @var string|null ISO-8601
     */
    public $processing_timestamp;

    /**
     * @var string|null ISO-8601
     */
    public $created_at;

    public function __construct()
    {
    }
}
