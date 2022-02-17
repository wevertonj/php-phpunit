<?php

namespace Alura\PHPUnit\Leilao\App;

use Alura\PHPUnit\Leilao\Domain\Leilao;

interface EnviadorEmail
{
    public function notificaFimLeilao(Leilao $leilao): void;
}
