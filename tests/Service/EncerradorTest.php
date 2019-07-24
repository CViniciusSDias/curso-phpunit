<?php

namespace Alura\Leilao\Tests\Domain;

use Alura\Leilao\Dao\Leilao as LeilaoDao;
use Alura\Leilao\Model\Leilao;
use Alura\Leilao\Service\Encerrador;
use PHPUnit\Framework\TestCase;

class LeilaoDaoMock extends LeilaoDao
{
    private $leiloes = [];

    public function salva(Leilao $leilao): void
    {
        $this->leiloes[] = $leilao;
    }

    public function recuperarFinalizados(): array
    {
        return array_filter($this->leiloes, function (Leilao $leilao) {
            return $leilao->estaFinalizado();
        });
    }

    public function recuperarNaoFinalizados(): array
    {
        return array_filter($this->leiloes, function (Leilao $leilao) {
            return !$leilao->estaFinalizado();
        });
    }

    public function atualiza(Leilao $leilao)
    {
        return;
    }
}

class EncerradorTest extends TestCase
{
    public function testDeveEncerrarLeiloesComMaisDeUmaSemana()
    {
        $leilaoFiat = new Leilao('Fiat 147 0Km', new \DateTimeImmutable('8 days ago'));
        $leilaoVariante = new Leilao('Variante 0Km', new \DateTimeImmutable('10 days ago'));

        $leilaoDao = new LeilaoDaoMock();
        $leilaoDao->salva($leilaoFiat);
        $leilaoDao->salva($leilaoVariante);

        $encerrador = new Encerrador($leilaoDao);
        $encerrador->encerra();

        $leiloesEncerrados = $leilaoDao->recuperarFinalizados();

        static::assertCount(2, $leiloesEncerrados);
        static::assertTrue($leiloesEncerrados[0]->estaFinalizado());
        static::assertTrue($leiloesEncerrados[1]->estaFinalizado());
    }
}
