<?php

// This file is part of cloudpbx-php-sdk.  The COPYRIGHT file at the top level of
// this repository contains the full copyright notices and license terms.
//
// @author (2026) Matias Gomez <matias@picallex.com>

declare(strict_types=1);

namespace Cloudpbx\Sdk\Model\Webphone;

/**
 * Estado de agente de una extension.
 *
 * Separado de ExtensionAvailability a proposito: ese contesta si se puede
 * transferir una llamada, este contesta si el agente esta disponible u ocupado y
 * con que pausa.
 */
final class AgentStatus extends \Cloudpbx\Sdk\Model
{
    protected $_primary_key = null;

    /**
     * @var string
     */
    public $domain;

    /**
     * @var string
     */
    public $extension;

    /**
     * @var string
     */
    public $fqdn;

    /**
     * Registro sip vivo. null cuando no se pudo leer el runtime.
     *
     * @var bool|null
     */
    public $registered;

    /**
     * @var bool
     */
    public $do_not_disturb;

    /**
     * Nombre de la pausa. null con do_not_disturb en false.
     *
     * @var string|null
     */
    public $dnd_status;

    /**
     * Estado del agente en mod_callcenter (Available, Logged Out, On Break).
     * null cuando la extension no es agente de cola o no se pudo leer.
     *
     * @var string|null
     */
    public $callcenter_status;

    /**
     * Solo lo trae setAgentStatus: si el estado de tiempo real quedo espejado.
     * false significa que el cambio SI se aplico, pero el panel y el camino de
     * transferencias pueden seguir viendo la etiqueta anterior. null en las
     * consultas, donde no aplica.
     *
     * @var bool|null
     */
    public $realtime_synced;

    public function __construct()
    {
    }

    /**
     * @return bool
     */
    public function isAvailable()
    {
        return $this->do_not_disturb === false;
    }
}
