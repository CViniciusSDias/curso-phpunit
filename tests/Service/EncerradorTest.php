<?php

namespace Alura\Leilao\Tests\Domain;

use Alura\Leilao\Dao\Leilao as LeilaoDao;
use Alura\Leilao\Model\Leilao;
use Alura\Leilao\Service\Encerrador;
use Alura\Leilao\Service\EnviadorDeEmail;
use PHPUnit\Framework\TestCase;

class EncerradorTest extends TestCase
{
    private $leilaoDao;
    private $enviadorDeEmailMock;

    protected function setUp(): void
    {
        $leilaoFiat = new Leilao('Fiat 147 0Km', new \DateTimeImmutable('8 days ago'));
        $leilaoVariante = new Leilao('Variante 0Km', new \DateTimeImmutable('10 days ago'));

        $this->leilaoDao = $this->getMockBuilder(LeilaoDao::class)
            ->disableOriginalConstructor()
            ->getMock();
        $this->leilaoDao->method('recuperarFinalizados')
            ->willReturn([$leilaoFiat, $leilaoVariante]);

        $this->leilaoDao->expects(self::once())
            ->method('recuperarNaoFinalizados')
            ->willReturn([$leilaoFiat, $leilaoVariante]);

        $this->leilaoDao->expects(self::exactly(2))
            ->method('atualiza')
            ->withConsecutive($leilaoFiat, $leilaoVariante);

        $this->enviadorDeEmailMock = $this->getMockBuilder(EnviadorDeEmail::class)->getMock();
    }

    public function testDeveEncerrarLeiloesComMaisDeUmaSemana()
    {
        $encerrador = new Encerrador($this->leilaoDao, $this->enviadorDeEmailMock);
        $encerrador->encerra();

        $leiloesEncerrados = $this->leilaoDao->recuperarFinalizados();

        static::assertCount(2, $leiloesEncerrados);
        static::assertTrue($leiloesEncerrados[0]->estaFinalizado());
        static::assertTrue($leiloesEncerrados[1]->estaFinalizado());
    }

    public function testDeveContinuarOProcessoamentoAoEncontrarErroAoEnviarEmail()
    {
        $e = new \DomainException('Erro ao enviar e-mail');

        $this->enviadorDeEmailMock
            ->expects(self::exactly(2))
            ->method('notificaTerminoLeilao')
            ->willThrowException($e);

        $encerrador = new Encerrador($this->leilaoDao, $this->enviadorDeEmailMock);
        $encerrador->encerra();
    }
}
