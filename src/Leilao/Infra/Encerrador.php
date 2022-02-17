<?php

namespace Alura\PHPUnit\Leilao\Infra;

use Alura\PHPUnit\Leilao\App\EnviadorEmail;
use Alura\PHPUnit\Leilao\Domain\LeilaoRepository;

class Encerrador
{
    private $leilaoDao;

    public function __construct(LeilaoRepository $leilaoDao, EnviadorEmail $enviadorEmail)
    {
        $this->leilaoDao = $leilaoDao;
        $this->enviadorEmail = $enviadorEmail;
    }

    public function encerra()
    {
        $leiloes = $this->leilaoDao->recuperarNaoFinalizados();

        foreach ($leiloes as $leilao) {
            if ($leilao->temMaisDeUmaSemana()) {
                try {
                    $leilao->finaliza();
                    $this->leilaoDao->atualiza($leilao);
                    $this->enviadorEmail->notificaFimLeilao($leilao);
                } catch (\DomainException $th) {
                    error_log($th->getMessage());
                }
            }
        }
    }
}
