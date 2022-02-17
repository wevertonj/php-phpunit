<?php

namespace Alura\PHPUnit\Leilao\App;

use Alura\PHPUnit\Leilao\Domain\Lance;
use Alura\PHPUnit\Leilao\Domain\Leilao;

class Avaliador
{
    /** @var float */
    private $menorValor = INF;
    /** @var float */
    private $maiorValor = 0;
    /** @var Lance[]|array */
    private $maiores;

    public function avalia(Leilao $leilao)
    {
        if ($leilao->estaFinalizado()) {
            throw new \DomainException('Não é possível avaliar um leilão finalizado');
        }

        if (empty($leilao->getLances())) {
            throw new \DomainException('Não é possível avaliar um leilão sem lances');
        }

        foreach ($leilao->getLances() as $lance) {
            if ($lance->getValor() > $this->maiorValor) {
                $this->maiorValor = $lance->getValor();
            }

            if ($lance->getValor() < $this->menorValor) {
                $this->menorValor = $lance->getValor();
            }

            $this->maiores = $this->avaliaTresMaioresLances($leilao);
        }
    }

    public function getMenorValor(): float
    {
        return $this->menorValor;
    }

    public function getMaiorValor(): float
    {
        return $this->maiorValor;
    }

    /**
     * @return Lance[]
     */
    public function getTresMaioresLances(): array
    {
        return $this->maiores;
    }

    /**
     * @param Leilao $leilao
     * @return Lance[]|array
     */
    private function avaliaTresMaioresLances(Leilao $leilao)
    {
        $lances = $leilao->getLances();
        usort($lances, function (Lance $lance1, Lance $lance2) {
            return $lance2->getValor() - $lance1->getValor();
        });

        return array_slice($lances, 0, 3);
    }
}
