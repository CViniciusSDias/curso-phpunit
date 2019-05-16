<?php

namespace Alura\Leilao\Model;

class Leilao
{
    /** @var Lance[] */
    private $lances;
    /** @var string */
    private $descricao;
    /** @var bool */
    private $finalizado;

    public function __construct(string $descricao)
    {
        $this->descricao = $descricao;
        $this->finalizado = false;
        $this->lances = [];
    }

    public function recebeLance(Lance $lance)
    {
        if ($this->finalizado) {
            throw new \DomainException('Este leilão já está finalizado');
        }

        $ultimoLance = $this->lances[count($this->lances) - 1];
        if (!empty($this->lances) && $ultimoLance->getUsuario() == $lance->getUsuario()) {
            throw new \DomainException('Usuário já deu o último lance');
        }

        $this->lances[] = $lance;
    }

    public function finaliza()
    {
        $this->finalizado = true;
    }

    /**
     * @return Lance[]
     */
    public function getLances(): array
    {
        return $this->lances;
    }
}
