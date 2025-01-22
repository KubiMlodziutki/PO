<div class="cart-container">
  <div class="cart-left">
    <h2>Koszyk</h2>
    <?php if (empty($productsInCart)): ?>
      <p>Koszyk jest pusty.</p>
    <?php else: ?>
      <form method="POST" action="index.php?controller=product&action=cart">
        <?php foreach ($productsInCart as $item): ?>
          <div class="cart-item">
            <div class="cart-item-img">
              <img src="img/<?= htmlspecialchars($item['Obrazek'] ?? 'no_image.png') ?>" alt="produkt">
            </div>
            <div class="cart-item-info">
              <strong><?= htmlspecialchars($item['Nazwa']) ?></strong><br>
              Ilość: 
              <input type="number" name="quantities[<?= $item['ID'] ?>]"
                     value="<?= $item['quantity'] ?>" min="0">
              <br>
              Cena za szt.: <?= number_format($item['Cena'],2) ?> zł<br>
              Suma: <?= number_format($item['sum'],2) ?> zł
              <br>
            <button class="remove-from-cart-btn" data-id="<?= $item['ID'] ?>">Usuń</button>
            </div>
          </div>
        <?php endforeach; ?>

        <button type="submit" name="updateCart">Aktualizuj koszyk</button>
      </form>
    <?php endif; ?>
  </div>

  <div class="cart-right">
    <div class="cart-summary">
      <p>Wartość koszyka: <strong><?= number_format($totalValue,2) ?> zł</strong></p>
      <button class="order-btn">Złóż zamówienie</button>
      <button class="back-offer-btn" onclick="window.location.href='index.php?controller=product&action=list'">
        Powrót do oferty
      </button>
    </div>
  </div>
</div>

</main>
</body>
</html>

<script>
document.querySelectorAll('.remove-from-cart-btn').forEach(button => {
    button.addEventListener('click', function () {
        const productId = this.getAttribute('data-id');
        fetch('index.php?controller=product&action=removeFromCart&ajax=1', {
            method: 'POST',
            headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
            body: `productId=${productId}`
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                this.closest('.cart-item').remove();
                location.reload();
            } else {
                showPopup(data.error || "Nie udało się usunąć produktu!");
            }
        });
    });
});

</script>
