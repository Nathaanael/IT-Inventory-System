<?php

namespace App\Services;

use Endroid\QrCode\Builder\Builder;
use Endroid\QrCode\Writer\SvgWriter;
use PragmaRX\Google2FA\Google2FA;

class MfaService
{
    public function __construct(private readonly Google2FA $google2fa)
    {
    }

    public function generateSecret(): string
    {
        return $this->google2fa->generateSecretKey(32);
    }

    public function provisioningUri(string $username, string $secret): string
    {
        return $this->google2fa->getQRCodeUrl(
            config('app.name', 'IT Inventory System'),
            $username,
            $secret,
        );
    }

    public function qrCodeDataUri(string $username, string $secret): string
    {
        $result = (new Builder(
            writer: new SvgWriter(),
            data: $this->provisioningUri($username, $secret),
            size: 240,
            margin: 10,
        ))->build();

        return 'data:'.$result->getMimeType().';base64,'.base64_encode($result->getString());
    }

    public function verify(string $secret, string $code, ?int $lastUsedAt = null): int|false
    {
        $code = preg_replace('/\D/', '', $code) ?? '';

        return $this->google2fa->verifyKeyNewer($secret, $code, $lastUsedAt ?? 0, 1);
    }

    public function currentCode(string $secret): string
    {
        return $this->google2fa->getCurrentOtp($secret);
    }
}
