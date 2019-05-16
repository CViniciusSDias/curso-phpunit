<?php

namespace Alura\Leilao\Tests\Domain;

use Alura\Leilao\Model\Lance;
use Alura\Leilao\Model\Leilao;
use Alura\Leilao\Model\Usuario;
use Alura\Leilao\Service\Avaliador;
use PHPUnit\Framework\TestCase;

class AvaliadorTest extends TestCase
{
    /** @var Avaliador */
    private $avaliador;

    protected function setUp(): void
    {
        $this->avaliador = new Avaliador();
    }

    /**
     * @dataProvider leilaoComLancesEmOrdemAleatoria
     * @dataProvider leilaoComLancesEmOrdemDecrescente
     * @dataProvider leilaoComLancesEmOrdemCrescente
     */
    public function testAvaliadorDeveAcharMaiorValor(Leilao $leilao)
    {
        $this->avaliador->avalia($leilao);

        static::assertEquals(2000, $this->avaliador->getMaiorValor());
    }

    /**
     * @dataProvider leilaoComLancesEmOrdemAleatoria
     * @dataProvider leilaoComLancesEmOrdemDecrescente
     * @dataProvider leilaoComLancesEmOrdemCrescente
     */
    public function testAvaliadorDeveAcharMenorValor(Leilao $leilao)
    {
        $this->avaliador->avalia($leilao);

        static::assertEquals(1000, $this->avaliador->getMenorValor());
    }

    /**
     * @dataProvider leilaoComLancesEmOrdemAleatoria
     * @dataProvider leilaoComLancesEmOrdemDecrescente
     * @dataProvider leilaoComLancesEmOrdemCrescente
     */
    public function testAvaliadorDeveOrdenarOs3Lances(Leilao $leilao)
    {
        $this->avaliador->avalia($leilao);

        $lances = $this->avaliador->getTresMaioresLances();

        static::assertCount(3, $lances);
        static::assertEquals(2000, $lances[0]->getValor());
        static::assertEquals(1500, $lances[1]->getValor());
        static::assertEquals(1000, $lances[2]->getValor());
    }

    public function testAvaliadorDeveRetornarOsMaioresLancesDisponiveis()
    {
        $leilao = new Leilao('Fiat 147 0KM');

        $leilao->recebeLance(new Lance(new Usuario('João'), 1000));
        $leilao->recebeLance(new Lance(new Usuario('Maria'), 1500));

        $this->avaliador->avalia($leilao);

        static::assertCount(2, $this->avaliador->getTresMaioresLances());
    }

    public function leilaoComLancesEmOrdemCrescente()
    {
        $leilao = new Leilao('Fiat 147 0KM');
        $joao = new Usuario('João');
        $maria = new Usuario('Maria');
        $ana = new Usuario('Ana');

        $leilao->recebeLance(new Lance($joao, 1000));
        $leilao->recebeLance(new Lance($maria, 1500));
        $leilao->recebeLance(new Lance($ana, 2000));

        return [
            [$leilao]
        ];
    }

    public function leilaoComLancesEmOrdemDecrescente()
    {
        $leilao = new Leilao('Fiat 147 0KM');
        $joao = new Usuario('João');
        $maria = new Usuario('Maria');
        $ana = new Usuario('Ana');

        $leilao->recebeLance(new Lance($ana, 2000));
        $leilao->recebeLance(new Lance($maria, 1500));
        $leilao->recebeLance(new Lance($joao, 1000));

        return [
            [$leilao]
        ];
    }

    public function leilaoComLancesEmOrdemAleatoria()
    {
        $leilao = new Leilao('Fiat 147 0KM');
        $joao = new Usuario('João');
        $maria = new Usuario('Maria');
        $ana = new Usuario('Ana');

        $leilao->recebeLance(new Lance($maria, 1500));
        $leilao->recebeLance(new Lance($ana, 2000));
        $leilao->recebeLance(new Lance($joao, 1000));

        return [
            [$leilao]
        ];
    }
}
