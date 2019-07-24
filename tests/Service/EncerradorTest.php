<?php

namespace Alura\Leilao\Tests\Domain;

use Alura\Leilao\Dao\Leilao as LeilaoDao;
use Alura\Leilao\Model\Leilao;
use Alura\Leilao\Service\Encerrador;
use PHPUnit\Framework\TestCase;

class EncerradorTest extends TestCase
{
    public function testDeveEncerrarLeiloesComMaisDeUmaSemana()
    {
        $leilaoFiat = new Leilao('Fiat 147 0Km', new \DateTimeImmutable('8 days ago'));
        $leilaoVariante = new Leilao('Variante 0Km', new \DateTimeImmutable('10 days ago'));

        $leilaoDao = $this->getMockBuilder(LeilaoDao::class)
            ->disableOriginalConstructor()
            ->getMock();
        $leilaoDao->method('recuperarFinalizados')
            ->willReturn([$leilaoFiat, $leilaoVariante]);

        $leilaoDao->expects(self::once())
            ->method('recuperarNaoFinalizados')
            ->willReturn([$leilaoFiat, $leilaoVariante]);

        $encerrador = new Encerrador($leilaoDao);
        $encerrador->encerra();

        $leiloesEncerrados = $leilaoDao->recuperarFinalizados();

        static::assertCount(2, $leiloesEncerrados);
        static::assertTrue($leiloesEncerrados[0]->estaFinalizado());
        static::assertTrue($leiloesEncerrados[1]->estaFinalizado());
    }
}
