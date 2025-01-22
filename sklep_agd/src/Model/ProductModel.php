<?php
namespace App\Model;

use PDO;

class ProductModel
{
    public static function getAllProducts(array $filters = []): array
    {
        $pdo = Database::getConnection();
        $sql = "SELECT * FROM Produkt WHERE 1=1";
        $params = [];

        if (!empty($filters['search'])) {
            $sql .= " AND (Nazwa LIKE :search OR Opis LIKE :search)";
            $params[':search'] = '%'.$filters['search'].'%';
        }

        if (!empty($filters['category'])) {
            $sql .= " AND Kategoria = :cat";
            $params[':cat'] = $filters['category'];
        }

        if (isset($filters['price_min']) && is_numeric($filters['price_min'])) {
            $sql .= " AND Cena >= :pmin";
            $params[':pmin'] = (float)$filters['price_min'];
        }
        if (isset($filters['price_max']) && is_numeric($filters['price_max'])) {
            $sql .= " AND Cena <= :pmax";
            $params[':pmax'] = (float)$filters['price_max'];
        }

        if (!empty($filters['date_min'])) {
            $sql .= " AND Data_dodania >= :dmin";
            $params[':dmin'] = $filters['date_min'];
        }
        if (!empty($filters['date_max'])) {
            $sql .= " AND Data_dodania <= :dmax";
            $params[':dmax'] = $filters['date_max'];
        }

        if (isset($filters['availability'])) {
            if ($filters['availability'] === 'available') {
                $sql .= " AND Stan_magazynowy > 0";
            } elseif ($filters['availability'] === 'unavailable') {
                $sql .= " AND Stan_magazynowy = 0";
            }
        }

        if (!empty($filters['sort_price'])) {
            if ($filters['sort_price'] === 'asc') {
                $sql .= " ORDER BY Cena ASC";
            } elseif ($filters['sort_price'] === 'desc') {
                $sql .= " ORDER BY Cena DESC";
            }
        }
        elseif (!empty($filters['sort_date'])) {
            if ($filters['sort_date'] === 'oldest') {
                $sql .= " ORDER BY Data_dodania ASC";
            } elseif ($filters['sort_date'] === 'newest') {
                $sql .= " ORDER BY Data_dodania DESC";
            }
        }

        $stmt = $pdo->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public static function getProductById(int $id): ?array
    {
        $pdo = Database::getConnection();
        $stmt = $pdo->prepare("SELECT * FROM Produkt WHERE ID = :id");
        $stmt->execute([':id' => $id]);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        return $row ?: null;
    }
}
