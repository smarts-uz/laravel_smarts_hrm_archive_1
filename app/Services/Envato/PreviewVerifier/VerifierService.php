<?php

namespace App\Services\Envato\PreviewVerifier;

use App\Services\MTProtoService;

class VerifierService
{
    public $MTProto;

    public function __construct()
    {
        $this->MTProto = new MTProtoService();
    }

    public function verifier($start, $end = null)
    {

    }
}