<?php
namespace App\Model;

use PDO;

class OpinionModel
{
    public static function getOpinionsForProduct(int $productId): array
    {
        $pdo = Database::getConnection();
        $sql = "SELECT * FROM Opinie
                WHERE ProduktID = :pid
                ORDER BY Data_wystawienia DESC";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([':pid' => $productId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
