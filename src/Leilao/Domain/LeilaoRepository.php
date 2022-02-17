<?php

namespace Alura\PHPUnit\Leilao\Domain;

use Alura\PHPUnit\Leilao\Domain\Leilao;

interface LeilaoRepository
{
    public function salva(Leilao $leilao): Leilao;
    public function recuperarNaoFinalizados(): array;
    public function recuperarFinalizados(): array;
    public function atualiza(Leilao $leilao): void;
}
