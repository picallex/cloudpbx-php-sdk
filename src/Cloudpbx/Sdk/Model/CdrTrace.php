<?php

declare(strict_types=1);

namespace Cloudpbx\Sdk\Model;

final class CdrTrace extends \Cloudpbx\Sdk\Model
{
    /** @var string */
    public $recorduuid;

    /** @var int */
    public $customer_id;

    /** @var mixed */
    public $agent;

    /** @var bool */
    public $answered;

    /** @var object {mos: float, rating: string, score_pct: int} */
    public $audio_quality;

    /** @var string inbound|outbound */
    public $direction;

    /** @var int */
    public $duration_sec;

    /** @var string ISO-8601 */
    public $started_at;

    /** @var string ISO-8601 */
    public $ended_at;

    /** @var string */
    public $from;

    /** @var string */
    public $to;

    /** @var string */
    public $type;

    /** @var string */
    public $hangup_cause;

    /** @var string */
    public $recording;

    /** @var array<int, array{at: string, description: string}> */
    public $events;

    /**
     * full payload as returned by the api (incluye cualquier campo no tipado).
     *
     * @var array<string, mixed>
     */
    public $data;

    /**
     * @param array<string, mixed> $data
     */
    public function __construct($data = [])
    {
        $this->recorduuid = (string)($data['recorduuid'] ?? '');
        $this->customer_id = (int)($data['customer_id'] ?? 0);
        $this->agent = $data['agent'] ?? null;
        $this->answered = (bool)($data['answered'] ?? false);
        $this->audio_quality = (object)($data['audio_quality'] ?? []);
        $this->direction = (string)($data['direction'] ?? '');
        $this->duration_sec = (int)($data['duration_sec'] ?? 0);
        $this->started_at = (string)($data['started_at'] ?? '');
        $this->ended_at = (string)($data['ended_at'] ?? '');
        $this->from = (string)($data['from'] ?? '');
        $this->to = (string)($data['to'] ?? '');
        $this->type = (string)($data['type'] ?? '');
        $this->hangup_cause = (string)($data['hangup_cause'] ?? '');
        $this->recording = (string)($data['recording'] ?? '');
        $this->events = $data['events'] ?? [];
        $this->data = $data;
    }
}
