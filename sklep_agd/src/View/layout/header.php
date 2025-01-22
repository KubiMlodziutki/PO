<!DOCTYPE html>
<html lang="pl">
<head>
  <meta charset="UTF-8">
  <title>Sklep AGD</title>
  <link rel="stylesheet" href="css/style.css">
</head>
<body>
<header>
  <div class="header-bar">
    <div class="logo-area">
      <a href="index.php?controller=product&action=list" class="logo">Media Ekspres - sklep AGD</a>
    </div>
    <div class="search-area">
      <form method="GET" action="index.php">
        <input type="hidden" name="controller" value="product">
        <input type="hidden" name="action" value="list">
        <input type="text" name="search" placeholder="Wyszukaj..."
               value="<?= htmlspecialchars($_GET['search'] ?? '') ?>">
        <button type="submit">Szukaj</button>
      </form>
    </div>
    <div class="cart-area">
      <a class="cart-btn" href="index.php?controller=product&action=cart">
        Przejdź do koszyka
      </a>
    </div>
  </div>
</header>

<div id="modal-overlay" class="modal-overlay" style="display: none;" onclick="closePopup()">
    <div class="modal-box" onclick="event.stopPropagation()">
        <div id="modal-message"></div>
        <button onclick="closePopup()">OK</button>
    </div>
</div>

<main>
<script src="js/popup.js"></script>
