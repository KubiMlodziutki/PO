<div class="main-container">
  <aside class="sidebar">
    <h3>Filtry i Sortowanie</h3>
    <form id="filterForm" method="GET" action="index.php">
      <input type="hidden" name="controller" value="product">
      <input type="hidden" name="action" value="list">
      <label><strong>Cena</strong></label><br>
      <label><input type="radio" name="sort_price" value="asc"
                    <?= (($_GET['sort_price'] ?? '') === 'asc')?'checked':'' ?>>
             Od najniższej</label><br>
      <label><input type="radio" name="sort_price" value="desc"
                    <?= (($_GET['sort_price'] ?? '') === 'desc')?'checked':'' ?>>
             Od najwyższej</label><br>
      <label><input type="radio" name="sort_price" value=""
                    <?= empty($_GET['sort_price'])?'checked':'' ?>>
             Brak</label><br><br>
      <label><strong>Data dodania</strong></label><br>
      <label><input type="radio" name="sort_date" value="oldest"
                    <?= (($_GET['sort_date'] ?? '') === 'oldest')?'checked':'' ?>>
             Najdawniej</label><br>
      <label><input type="radio" name="sort_date" value="newest"
                    <?= (($_GET['sort_date'] ?? '') === 'newest')?'checked':'' ?>>
             Niedawno</label><br>
      <label><input type="radio" name="sort_date" value=""
                    <?= empty($_GET['sort_date'])?'checked':'' ?>>
             Brak</label><br><br>
      <label><strong>Dostępność</strong></label><br>
      <label><input type="radio" name="availability" value="all"
                    <?= (($_GET['availability']??'')==='all' || !isset($_GET['availability']))?'checked':'' ?>>
             Wszystko</label><br>
      <label><input type="radio" name="availability" value="available"
                    <?= (($_GET['availability']??'')==='available')?'checked':'' ?>>
             Na stanie</label><br>
      <label><input type="radio" name="availability" value="unavailable"
                    <?= (($_GET['availability']??'')==='unavailable')?'checked':'' ?>>
             Niedostępne</label><br><br>
      <label>Cena od: <input type="number" step="0.01" name="price_min"
             value="<?= htmlspecialchars($_GET['price_min'] ?? '') ?>"></label><br>
      <label> do: <input type="number" step="0.01" name="price_max"
             value="<?= htmlspecialchars($_GET['price_max'] ?? '') ?>"></label><br><br>
      <label>Data dodania od: <input type="date" name="date_min"
             value="<?= htmlspecialchars($_GET['date_min'] ?? '') ?>"></label><br>
      <label> do: <input type="date" name="date_max"
             value="<?= htmlspecialchars($_GET['date_max'] ?? '') ?>"></label><br><br>
      <label><strong>Kategoria:</strong></label><br>
      <select name="category">
        <option value="">Wszystkie</option>
        <option value="Pralki"   <?= (($_GET['category']??'')==='Pralki')?'selected':'' ?>>Pralki</option>
        <option value="Zmywarki" <?= (($_GET['category']??'')==='Zmywarki')?'selected':'' ?>>Zmywarki</option>
        <option value="Lodówki"  <?= (($_GET['category']??'')==='Lodówki')?'selected':'' ?>>Lodówki</option>
      </select><br><br>

      <button type="submit">Zastosuj</button>
    </form>
  </aside>
  <section class="products-list">
    <?php if (empty($products)): ?>
      <p>Brak produktów spełniających kryteria.</p>
    <?php else: ?>
      <?php foreach ($products as $p): ?>
        <div class="product-row">
          <div class="prod-image">
            <img src="img/<?= htmlspecialchars($p['Obrazek'] ?? 'no_image.png') ?>"
                 alt="produkt">
          </div>
          <div class="prod-info">
          <div class="prod-basic">
                <strong class="prod-name"><?= htmlspecialchars($p['Nazwa']) ?></strong><br>
                <span class="prod-date">Data dodania: <?= $p['Data_dodania'] ?></span><br>
                <span class="prod-price"><?= number_format($p['Cena'],2) ?> zł</span><br>
                <span class="prod-availability">
                    Dostępność: <?= ($p['Stan_magazynowy']>0 ? 'Na stanie ('.$p['Stan_magazynowy'].')' : 'Brak') ?>
                </span><br>
                <p class="prod-desc"><?= htmlspecialchars($p['Opis']) ?></p>
           </div>

          </div>
          <div class="prod-actions">
            <form action="index.php" method="GET" class="qty-form">
              <input type="hidden" name="controller" value="product">
              <input type="hidden" name="action" value="addToCart">
              <input type="hidden" name="id" value="<?= $p['ID'] ?>">

              <div class="qty-buttons">
                <button type="button" onclick="minusQty(this)">-</button>
                <input type="number" name="qty" value="1" min="1">
                <button type="button" onclick="plusQty(this)">+</button>
              </div>

              <button type="button" class="add-cart-btn">Dodaj do koszyka</button>
            </form>
            <?php
                $favorites = isset($_COOKIE['favorites']) ? json_decode($_COOKIE['favorites'], true) : [];
                $inFavorites = in_array($p['ID'], $favorites, true);
            ?>
            <a href="javascript:void(0)" 
                class="fav-btn <?= $inFavorites ? 'fav-active' : '' ?>" 
                data-id="<?= $p['ID'] ?>">
                <?= $inFavorites ? '♥ Usuń' : '♥ Dodaj' ?>
            </a>
            <a href="index.php?controller=product&action=detail&id=<?= $p['ID'] ?>"
               class="details-btn">Szczegóły</a>
          </div>
        </div>
      <?php endforeach; ?>
    <?php endif; ?>
  </section>
</div>

</main>
</body>
</html>

<script>
document.querySelectorAll('.add-cart-btn').forEach(button => {
    button.addEventListener('click', function (event) {
        event.preventDefault();

        const form = this.closest('.qty-form');
        const productId = form.querySelector('input[name="id"]').value;
        const qty = form.querySelector('input[name="qty"]').value;

        fetch(`index.php?controller=product&action=addToCart&id=${productId}&qty=${qty}&ajax=1`)
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    showPopup('addedToCart2', {
                        productName: data.productName,
                        qty: data.qty,
                        available: data.available
                    });
                } else {
                    showPopup('wrongQty2', {
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
        case 'addedToCart2':
            message = `
                <strong>Pomyślnie dodano do koszyka!</strong><br>
                Przedmiot: <em>${productName}</em>, ilość: <strong>${qty}</strong>
            `;
            break;
        case 'wrongQty2':
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
}


document.querySelectorAll('.fav-btn').forEach(button => {
    button.addEventListener('click', function () {
        const productId = this.getAttribute('data-id');
        fetch(`index.php?controller=product&action=toggleFavorites&id=${productId}&ajax=1`)
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    this.classList.toggle('fav-active');
                    this.innerHTML = data.inFavorites ? '♥ Usuń' : '♥ Dodaj';
                    showPopup(data.message);
                }
            });
    });
});

function minusQty(btn) {
  const input = btn.parentNode.querySelector('input[name="qty"]');
  let val = parseInt(input.value, 10);
  if (val > 1) {
    val--;
    input.value = val;
  }
}
function plusQty(btn) {
  const input = btn.parentNode.querySelector('input[name="qty"]');
  let val = parseInt(input.value, 10);
  val++;
  input.value = val;
}
</script>
