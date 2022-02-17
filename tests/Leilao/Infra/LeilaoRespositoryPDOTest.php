<?php

namespace Alura\PHPUnit\Tests\Dao;

use Alura\PHPUnit\Leilao\Domain\Leilao;
use Alura\PHPUnit\Leilao\Infra\LeilaoRepositoryPDO;
use PHPUnit\Framework\TestCase;

class LeilaoRepositoryPDOTest extends TestCase
{
    private static $pdo;

    public static function setUpBeforeClass(): void
    {
        self::$pdo = new \PDO('sqlite::memory:');
        $sql = 'CREATE TABLE leiloes (
            id INTEGER PRIMARY KEY AUTOINCREMENT,
            descricao TEXT,
            finalizado BOOL,
            dataInicio TEXT
        );';

        self::$pdo->exec($sql);
    }

    protected function setUp(): void
    {
        self::$pdo->beginTransaction();
    }

    /**
     * @dataProvider leiloes
     */
    public function testBuscaLeiloesNaoFinalizados(array $leiloes)
    {
        $leilaoDao = new LeilaoRepositoryPDO(self::$pdo);
        foreach ($leiloes as $leilao) {
            $leilaoDao->salva($leilao);
        }

        $buscado = $leilaoDao->recuperarNaoFinalizados();

        $this->assertCount(1, $buscado);
        $this->assertContainsOnlyInstancesOf(Leilao::class, $buscado);
        $this->assertSame('Playstation 4', $buscado[0]->recuperarDescricao());
    }

    /**
     * @dataProvider leiloes
     */
    public function testBuscaLeiloesFinalizados(array $leiloes)
    {
        $leilaoDao = new LeilaoRepositoryPDO(self::$pdo);
        foreach ($leiloes as $leilao) {
            $leilaoDao->salva($leilao);
        }

        $buscado = $leilaoDao->recuperarFinalizados();

        $this->assertCount(1, $buscado);
        $this->assertContainsOnlyInstancesOf(Leilao::class, $buscado);
        $this->assertSame('Xbox One', $buscado[0]->recuperarDescricao());
    }

    public function testAoAtualizarLeilaoStatusDeveSerAlterado()
    {
        $leilao = new Leilao('Xbox Series X');
        $leilaoDao = new LeilaoRepositoryPDO(self::$pdo);
        $leilao = $leilaoDao->salva($leilao);

        $leiloes = $leilaoDao->recuperarNaoFinalizados();
        $this->assertCount(1, $leiloes);
        $this->assertSame('Xbox Series X', $leiloes[0]->recuperarDescricao());
        $this->assertFalse($leiloes[0]->estaFinalizado());

        $leilao->finaliza();
        $leilaoDao->atualiza($leilao);


        $leiloes = $leilaoDao->recuperarFinalizados();
        $this->assertCount(1, $leiloes);
        $this->assertSame('Xbox Series X', $leiloes[0]->recuperarDescricao());
        $this->assertTrue($leiloes[0]->estaFinalizado());
    }

    protected function tearDown(): void
    {
        self::$pdo->rollback();
    }

    public function leiloes()
    {
        $naoFinalizado = new Leilao('Playstation 4');
        $finalizado = new Leilao('Xbox One');
        $finalizado->finaliza();

        return array(
            array(
                array(
                    $naoFinalizado,
                    $finalizado
                )
            )
        );
    }
}
