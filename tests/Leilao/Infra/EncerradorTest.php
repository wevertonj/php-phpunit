<?php

namespace Alura\PHPUnit\Tests\Leilao\Infra;

use Alura\PHPUnit\Leilao\App\EnviadorEmail;
use Alura\PHPUnit\Leilao\Domain\Leilao;
use Alura\PHPUnit\Leilao\Domain\LeilaoRepository;
use Alura\PHPUnit\Leilao\Infra\Encerrador;
use PHPUnit\Framework\TestCase;

class EncerradorTest extends TestCase
{
    private $encerrador;
    private $enviadorEmail;
    private $leilaoFiat147;
    private $leilaoVariante;

    protected function setUp(): void
    {
        $this->leilaoFiat147 = new Leilao('Fiat 147 0Km', new \DateTimeImmutable('8 days ago'));
        $this->leilaoVariante = new Leilao('Variante 1972 0Km', new \DateTimeImmutable('8 days ago'));

        $leilaoDao = $this->createMock(LeilaoRepository::class);

        /** MockBuilder para testes mais avançados */
        // $leilaoDao = $this->getMockBuilder(LeilaoDao::class)->setConstructorArgs([new \PDO('sqlite::memory:')])->getMock();

        $leilaoDao->method('recuperarNaoFinalizados')->willReturn([$this->leilaoFiat147, $this->leilaoVariante]); // erro do intelephense, o método está correto
        $leilaoDao->method('recuperarFinalizados')->willReturn([$this->leilaoFiat147, $this->leilaoVariante]); // erro do intelephense, o método está correto
        $leilaoDao->expects($this->exactly(2))->method('atualiza')->withConsecutive([$this->leilaoFiat147], [$this->leilaoVariante]); // erro do intelephense, o método está correto

        $this->enviadorEmail = $this->createMock(EnviadorEmail::class);

        $this->encerrador = new Encerrador($leilaoDao, $this->enviadorEmail);
    }

    public function testLeiloesComMaisDeUmaSemanaDevemSerEncerrados()
    {
        $this->encerrador->encerra();

        $leilao = [$this->leilaoFiat147, $this->leilaoVariante];
        $this->assertCount(2, $leilao);
        $this->assertTrue($this->leilaoFiat147->estaFinalizado());
        $this->assertTrue($this->leilaoVariante->estaFinalizado());
    }

    public function testProcessoDeEncerramentoDeveContinuarMesmoComErro()
    {
        $exception = new \DomainException('Erro ao enviar email');
        $this->enviadorEmail->expects($this->exactly(2))->method('notificaFimLeilao')->willThrowException($exception);

        $this->encerrador->encerra();
    }

    public function testSoDeveEnviarNotificarLeilaoAposFinalizado()
    {
        $this->enviadorEmail->expects($this->exactly(2))->method('notificaFimLeilao')->willReturnCallback(
            fn (Leilao $leilao) => $this->assertTrue($leilao->estaFinalizado())
        );

        $this->encerrador->encerra();
    }
}
