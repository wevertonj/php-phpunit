<?php

namespace Alura\PHPUnit\Leilao\Domain;

class Usuario
{
    /** @var string */
    private $nome;

    public function __construct(string $nome)
    {
        $this->nome = $nome;
    }

    public function getNome(): string
    {
        return $this->nome;
    }
}
