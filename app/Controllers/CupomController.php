<?php

namespace App\Controllers;

use App\Models\Cupom;
use PDO;

class CupomController
{

    private Cupom $cupomModel;

    public function __construct(PDO $pdo)
    {
        $this->cupomModel = new Cupom($pdo);
    }

    public function aplicar()
    {
        $codigo = $_POST['codigo'];
        $subtotal = $_POST['subtotal'];

        $resposta = $this->cupomModel->validar($codigo, $subtotal);

        echo json_encode($resposta);
    }
}
