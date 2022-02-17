<?php

namespace Alura\PHPUnit\Leilao\Infra;

use Alura\PHPUnit\Leilao\App\EnviadorEmail;
use Alura\PHPUnit\Leilao\Domain\Leilao;

class EnviadorEmailPHP implements EnviadorEmail
{

    public function notificaFimLeilao(Leilao $leilao): void
    {
        $foiEnviado = mail('email@test.com', 'Leilao terminado', 'O leilao para ' . $leilao->recuperarDescricao() . ' foi finalizado');

        if (!$foiEnviado) {
            throw new \DomainException('Erro ao enviar email');
        }
    }
}
