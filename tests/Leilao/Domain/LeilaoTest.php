<?php

namespace Alura\PHPUnit\Tests\Tests\Leilao\Domain;

use Alura\PHPUnit\Leilao\Domain\Lance;
use Alura\PHPUnit\Leilao\Domain\Leilao;
use Alura\PHPUnit\Leilao\Domain\Usuario;
use DomainException;
use PHPUnit\Framework\TestCase;

class LeilaoModelTest extends TestCase
{
    private $leilao;
    private $lanceAna;
    private $lanceMaria;

    protected function setUp(): void
    {
        $this->lanceAna = $this->createMock(Lance::class);
        $this->lanceAna->method('getUsuario')->willReturn(new Usuario('Ana'));
        $this->lanceAna->expects($this->any())->method('getValor')->withConsecutive([1000.0], [1500.0]);

        $this->lanceMaria = $this->createMock(Lance::class);
        $this->lanceMaria->method('getUsuario')->willReturn(new Usuario('Maria'));
        $this->lanceMaria->method('getValor')->willReturn(1500.0);

        $this->leilao = new Leilao('Fiat 147 0KM');
    }

    public function testProporLanceEmLeilaoFinalizadoDeveLancarExcecao()
    {
        $this->expectException(DomainException::class);
        $this->expectExceptionMessage('Este leilão já está finalizado');

        $this->leilao->finaliza();
        $this->leilao->recebeLance($this->lanceMaria);
    }

    public function testProporLancesEmLeilaoDeveFuncionar()
    {
        $this->leilao->recebeLance($this->lanceAna);
        $this->leilao->recebeLance($this->lanceMaria);
        $this->leilao->recebeLance($this->lanceAna);

        static::assertCount(3, $this->leilao->getLances());
    }

    public function testMesmoUsuarioNaoPodeProporDoisLancesSeguidos()
    {
        $this->expectException(DomainException::class);
        $this->expectExceptionMessage('Usuário já deu o último lance');

        $this->leilao->recebeLance($this->lanceAna);
        $this->leilao->recebeLance($this->lanceAna);
    }
}
