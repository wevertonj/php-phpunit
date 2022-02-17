<?php

namespace Alura\PHPUnit\Tests\Leilao\App;

use Alura\PHPUnit\Leilao\App\Avaliador;
use Alura\PHPUnit\Leilao\Domain\Lance;
use Alura\PHPUnit\Leilao\Domain\Leilao;
use Alura\PHPUnit\Leilao\Domain\Usuario;
use PHPUnit\Framework\TestCase;

class AvaliadorTest extends TestCase
{
    /** @var Avaliador */
    private $avaliador;
    private $leilaoCrescente;
    private $leilaoDecrescente;
    private $leilaoAleatorio;

    protected function setUp(): void
    {
        $this->avaliador = new Avaliador();
    }

    public function leilaoProvider()
    {
        $ana = new Usuario('Ana');
        $maria = new Usuario('Maria');
        $joao = new Usuario('João');

        $LanceMenor = 1000;
        $lanceMedio = 1500;
        $lanceMaior = 2000;

        /** Ordem crescente */
        $lancesCrescente = array(
            new Lance($ana, $LanceMenor),
            new Lance($maria, $lanceMedio),
            new Lance($joao, $lanceMaior)
        );

        $this->leilaoCrescente = $this->getMockBuilder(Leilao::class)
            ->setConstructorArgs(['Fiat 147 0KM'])
            ->setMethodsExcept(array(
                'recebeLance',
                'finaliza',
                'estaFinalizado',
                'getLances',

            ))
            ->getMock();
        $this->leilaoCrescente->recebeLance($lancesCrescente[0]);
        $this->leilaoCrescente->recebeLance($lancesCrescente[1]);
        $this->leilaoCrescente->recebeLance($lancesCrescente[2]);

        /** Ordem decrescente */
        $lancesDecrescente = array(
            new Lance($ana, $lanceMaior),
            new Lance($maria, $lanceMedio),
            new Lance($joao, $LanceMenor)
        );

        $this->leilaoDecrescente = $this->getMockBuilder(Leilao::class)
            ->setConstructorArgs(['Fiat 147 0KM'])
            ->setMethodsExcept(array(
                'recebeLance',
                'finaliza',
                'estaFinalizado',
                'getLances',

            ))
            ->getMock();
        $this->leilaoDecrescente->recebeLance($lancesDecrescente[0]);
        $this->leilaoDecrescente->recebeLance($lancesDecrescente[1]);
        $this->leilaoDecrescente->recebeLance($lancesDecrescente[2]);

        /** Ordem aleatória */
        $lancesAleatorio = array(
            new Lance($maria, $lanceMedio),
            new Lance($ana, $lanceMaior),
            new Lance($joao, $LanceMenor)
        );

        $this->leilaoAleatorio = $this->getMockBuilder(Leilao::class)
            ->setConstructorArgs(['Fiat 147 0KM'])
            ->setMethodsExcept(array(
                'recebeLance',
                'finaliza',
                'estaFinalizado',
                'getLances',

            ))
            ->getMock();
        $this->leilaoAleatorio->recebeLance($lancesAleatorio[0]);
        $this->leilaoAleatorio->recebeLance($lancesAleatorio[1]);
        $this->leilaoAleatorio->recebeLance($lancesAleatorio[2]);

        return array(
            'ordem-crescente' => array($this->leilaoCrescente),
            'ordem-decrescente' => array($this->leilaoDecrescente),
            'ordem-aleatoria' => array($this->leilaoAleatorio)
        );
    }


    /**
     * @dataProvider leilaoProvider
     */
    public function testAvaliadorDeveAcharMaiorValor(Leilao $leilao)
    {
        $this->avaliador->avalia($leilao);
        $this->assertEquals(2000, $this->avaliador->getMaiorValor());
    }

    /**
     * @dataProvider leilaoProvider
     */
    public function testAvaliadorDeveAcharMenorValor(Leilao $leilao)
    {
        $this->avaliador->avalia($leilao);

        $this->assertEquals(1000, $this->avaliador->getMenorValor());
    }

    /**
     * @dataProvider leilaoProvider
     */
    public function testAvaliadorDeveOrdenarOs3Lances(Leilao $leilao)
    {
        $this->avaliador->avalia($leilao);
        $lances = $this->avaliador->getTresMaioresLances();

        $this->assertCount(3, $lances);
        $this->assertEquals(2000, $lances[0]->getValor());
        $this->assertEquals(1500, $lances[1]->getValor());
        $this->assertEquals(1000, $lances[2]->getValor());
    }

    /**
     * @dataProvider leilaoProvider
     */
    public function testLeilaoFinalizadoNaoPodeSerAvaliado(Leilao $leilao)
    {
        $this->expectException(\DomainException::class);
        $this->expectExceptionMessage('Não é possível avaliar um leilão finalizado');

        $leilao->finaliza();
        $this->avaliador->avalia($leilao);
    }

    public function testLelaoVazioDeveRetornarException()
    {
        $this->expectException(\DomainException::class);
        $this->expectExceptionMessage('Não é possível avaliar um leilão sem lances');

        $leilao = new Leilao('Fiat 147 0KM');
        $this->avaliador->avalia($leilao);
    }
}
