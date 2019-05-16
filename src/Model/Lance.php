<?php

namespace Alura\Leilao\Model;

class Lance
{
    /** @var Usuario */
    private $usuario;
    /** @var float */
    private $valor;

    public function __construct(Usuario $usuario, float $valor)
    {
        $this->usuario = $usuario;
        $this->valor = $valor;
    }

    public function getUsuario(): Usuario
    {
        return $this->usuario;
    }

    public function getValor(): float
    {
        return $this->valor;
    }
}
