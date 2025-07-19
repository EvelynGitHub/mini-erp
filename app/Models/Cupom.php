<?php
namespace App\Models;

use PDO;

class Cupom
{
    protected PDO $pdo;

    public function __construct(PDO $pdo)
    {
        $this->pdo = $pdo;
    }

    public function validar(string $codigo, float $subtotal): array
    {
        $stmt = $this->pdo->prepare("SELECT * FROM cupons WHERE codigo = ? AND ativo = 1 AND validade >= CURDATE()");
        $stmt->execute([$codigo]);
        $cupom = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$cupom) {
            return ['valido' => false, 'msg' => 'Cupom inválido ou expirado'];
        }

        if ($subtotal < (float) $cupom['valor_minimo']) {
            return ['valido' => false, 'msg' => 'Valor mínimo não atingido'];
        }

        $desconto = $subtotal * ((float) $cupom['desconto_percentual'] / 100);
        return ['valido' => true, 'desconto' => $desconto];
    }
}
