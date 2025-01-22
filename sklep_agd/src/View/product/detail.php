<div class="detail-container">
  <button class="back-btn" onclick="history.back()">← Powrót do oferty</button>

  <div class="detail-content">
    <div class="detail-image">
      <img src="img/<?= htmlspecialchars($product['Obrazek']) ?>" alt="produkt">
    </div>
    <div class="detail-info">
      <h2><?= htmlspecialchars($product['Nazwa']) ?></h2>
      <p><strong>Cena za sztukę:</strong> <?= number_format($product['Cena'],2) ?> zł</p>
      <p><strong>Model:</strong> <?= htmlspecialchars($product['Model'] ?? '') ?></p>
      <p><strong>Data produkcji:</strong> <?= $product['Data_produkcji'] ?></p>
      <p><strong>Kategoria:</strong> <?= htmlspecialchars($product['Kategoria']) ?></p>
      <p><?= nl2br(htmlspecialchars($product['Opis'])) ?></p>

      <form class="detail-cart-form" data-id="<?= $product['ID'] ?>">
        <input type="hidden" name="controller" value="product">
        <input type="hidden" name="action" value="addToCart">
        <input type="hidden" name="id" value="<?= $product['ID'] ?>">
        Ilość:
        <input type="number" class="qty-input" value="1" min="1">
        <button type="button" class="add-cart-btn">Dodaj do koszyka</button>
      </form>

      <?php
        $favorites = isset($_COOKIE['favorites']) ? json_decode($_COOKIE['favorites'], true) : [];
        $inFavorites = in_array($product['ID'], $favorites, true);
      ?>
      <a href="javascript:void(0)" 
        class="fav-btn <?= $inFavorites ? 'fav-active' : '' ?>" 
        data-id="<?= $product['ID'] ?>">
        <?= $inFavorites ? '♥ Usuń' : '♥ Dodaj' ?>
      </a>

      <div id="favorites-popup" style="display: none;" class="popup">
        <p id="favorites-message"></p>
        <button onclick="closePopup()">OK</button>
      </div>


    </div>
  </div>
  <div class="detail-opinions">
    <h3>Opinie użytkowników</h3>
    <?php if (empty($opinions)): ?>
      <p>Brak opinii dla tego produktu.</p>
    <?php else: ?>
      <?php foreach ($opinions as $op): ?>
        <div class="opinion-item">
          <div class="opinion-date">
            <strong>Data:</strong> <?= htmlspecialchars($op['Data_wystawienia']) ?>
            <strong>Ocena:</strong> <?= (int)$op['Ocena'] ?>/5
          </div>
          <div class="opinion-text"><?= nl2br(htmlspecialchars($op['Tresc'])) ?></div>
        </div>
      <?php endforeach; ?>
    <?php endif; ?>
  </div>
</div>

</main>
</body>
</html>

<script>
document.querySelector('.add-cart-btn').addEventListener('click', function () {
    const form = this.closest('.detail-cart-form');
    const productId = form.getAttribute('data-id');
    const qty = form.querySelector('.qty-input').value;

    fetch(`index.php?controller=product&action=addToCart&id=${productId}&qty=${qty}&ajax=1`)
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                showPopup('addedToCart', {
                    productName: data.productName,
                    qty: data.qty,
                    available: data.available
                });
            } else {
                showPopup('wrongQty', {
                    productName: data.productName,
                    qty: data.qty,
                    available: data.available
                });
            }
        })
        .catch(() => {
            showPopup("Wystąpił błąd przy dodawaniu produktu!");
        });
});

document.querySelector('.fav-btn').addEventListener('click', function () {
    const productId = this.getAttribute('data-id');
    fetch(`index.php?controller=product&action=toggleFavorites&id=${productId}&ajax=1`)
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                const button = document.querySelector('.fav-btn');
                button.classList.toggle('fav-active');
                button.innerHTML = data.inFavorites ? '♥ Usuń' : '♥ Dodaj';
                showPopup(data.message);
            }
        });
});
function showPopup(type, details = {}) {
    const overlay = document.getElementById('modal-overlay');
    const msgEl = document.getElementById('modal-message');
    overlay.style.display = 'flex';

    let productName = details.productName || '---';
    let qty = details.qty || '1';
    let available = details.available || '?';

    let message = "";

    switch (type) {
        case 'addedToCart':
            message = `
                <strong>Pomyślnie dodano do koszyka!</strong><br>
                Przedmiot: <em>${productName}</em>, ilość: <strong>${qty}</strong>
            `;
            break;
        case 'wrongQty':
            message = `
                <strong>Błędna ilość!</strong><br>
                Próbowano dodać/zmienić ilość na: <strong>${qty}</strong><br>
                Dostępne: <strong>${available}</strong><br><br>
                Produkt: <em>${productName}</em>
            `;
            break;
        default:
            message = `<strong>Komunikat:</strong> ${type}`;
            break;
    }

    msgEl.innerHTML = message;
}

function closePopup() {
    const overlay = document.getElementById('modal-overlay');
    overlay.style.display = 'none';
    window.history.replaceState({}, document.title, "index.php");
}


</script>

