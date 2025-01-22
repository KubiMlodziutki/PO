<?php
namespace App\Controller;

use App\Model\ProductModel;
use App\Model\OpinionModel;

class ProductController
{
    public function removeFromCartAction(): void
    {
        $id = (int)($_POST['productId'] ?? 0);

        $cart = isset($_COOKIE['cart']) ? json_decode($_COOKIE['cart'], true) : [];
        if (isset($cart[$id])) {
            unset($cart[$id]);
            setcookie('cart', json_encode($cart), time() + 3600 * 24 * 7);
        }

        if (isset($_GET['ajax'])) {
            echo json_encode(['success' => true, 'message' => 'Produkt został usunięty z koszyka.']);
            exit;
        }

        header("Location: index.php?controller=product&action=cart");
        exit;
    }


    public function listAction(): void
    {
        $filters = [
            'search'       => $_GET['search']       ?? null,
            'category'     => $_GET['category']     ?? null,
            'price_min'    => $_GET['price_min']    ?? null,
            'price_max'    => $_GET['price_max']    ?? null,
            'date_min'     => $_GET['date_min']     ?? null,
            'date_max'     => $_GET['date_max']     ?? null,
            'availability' => $_GET['availability'] ?? null,
            'sort_price'   => $_GET['sort_price']   ?? null,
            'sort_date'    => $_GET['sort_date']    ?? null,
        ];

        $products = ProductModel::getAllProducts($filters);
        require __DIR__ . '/../View/layout/header.php';
        require __DIR__ . '/../View/product/list.php';
    }

    public function detailAction(): void
    {
        $id = (int)($_GET['id'] ?? 0);
        $product = ProductModel::getProductById($id);
        if (!$product) {
            die("Produkt o ID $id nie istnieje!");
        }
        $opinions = OpinionModel::getOpinionsForProduct($id);
        require __DIR__ . '/../View/layout/header.php';
        require __DIR__ . '/../View/product/detail.php';
    }

     public function addToCartAction(): void
{
    $id = (int)($_GET['id'] ?? 0);
    $qty = (int)($_GET['qty'] ?? 1);

    if ($qty <= 0) {
        
        exit;
    }

    $product = ProductModel::getProductById($id);
    if (!$product) {
        echo json_encode(['success' => false, 'error' => 'Produkt nie istnieje.']);
        exit;
    }

    $cart = isset($_COOKIE['cart']) ? json_decode($_COOKIE['cart'], true) : [];
    $currentQty = $cart[$id] ?? 0;
    $newTotalQty = $currentQty + $qty;

    if ($newTotalQty > $product['Stan_magazynowy']) {
        echo json_encode([
            'success' => false,
            'error' => 'Przekroczono stan magazynowy.',
            'productName' => $product['Nazwa'],
            'qty' => $newTotalQty,
            'available' => $product['Stan_magazynowy']
        ]);
        exit;
    }

    $cart[$id] = $newTotalQty;
    setcookie('cart', json_encode($cart), time() + 3600 * 24 * 7);

    echo json_encode([
        'success' => true,
        'message' => 'Dodano do koszyka.',
        'productName' => $product['Nazwa'],
        'qty' => $qty,
        'available' => $product['Stan_magazynowy']
    ]);
    exit;
}
    public function toggleFavoritesAction(): void
    {
        $id = (int)($_GET['id'] ?? 0);
        $ajax = isset($_GET['ajax']);

        $product = ProductModel::getProductById($id);
        if (!$product) {
            if ($ajax) {
                echo json_encode(['success' => false, 'message' => 'Produkt nie istnieje!']);
                exit;
            }
            header("Location: index.php?controller=product&action=list");
            exit;
        }

        $favorites = isset($_COOKIE['favorites'])
            ? json_decode($_COOKIE['favorites'], true)
            : [];

        $inFavorites = in_array($id, $favorites, true);
        if ($inFavorites) {
            $favorites = array_filter($favorites, fn($favId) => $favId != $id);
            $message = "{$product['Nazwa']} usunięto z ulubionych.";
        } else {
            $favorites[] = $id;
            $message = "{$product['Nazwa']} dodano do ulubionych.";
        }

        setcookie('favorites', json_encode(array_values($favorites)), time() + 3600 * 24 * 7);

        if ($ajax) {
            echo json_encode(['success' => true, 'inFavorites' => !$inFavorites, 'message' => $message]);
            exit;
        }

        header("Location: index.php?controller=product&action=list");
        exit;
    }

    public function cartAction(): void
    {
        $cart = isset($_COOKIE['cart']) ? json_decode($_COOKIE['cart'], true) : [];

        if (isset($_POST['updateCart'])) {
            foreach ($_POST['quantities'] as $productId => $newQty) {
                $product = ProductModel::getProductById($productId);
                if (!$product) {
                    unset($cart[$productId]);
                    continue;
                }
                $newQty = (int)$newQty;
                if ($newQty > $product['Stan_magazynowy']) {
                    header("Location: index.php?controller=product&action=cart"
                         . "&popup=wrongQty"
                         . "&productName=" . urlencode($product['Nazwa'])
                         . "&qty=$newQty"
                         . "&available=" . $product['Stan_magazynowy']);
                    exit;
                }
                if ($newQty > 0) {
                    $cart[$productId] = $newQty;
                } else {
                    unset($cart[$productId]);
                }
            }
            setcookie('cart', json_encode($cart), time() + 3600*24*7);
            header("Location: index.php?controller=product&action=cart");
            exit;
        }
        $productsInCart = [];
        $totalValue = 0;
        foreach ($cart as $productId => $qty) {
            $p = ProductModel::getProductById($productId);
            if ($p) {
                $p['quantity'] = $qty;
                $p['sum'] = $p['Cena'] * $qty;
                $totalValue += $p['sum'];
                $productsInCart[] = $p;
            }
        }

        require __DIR__ . '/../View/layout/header.php';
        require __DIR__ . '/../View/product/cart.php';
    }
}
