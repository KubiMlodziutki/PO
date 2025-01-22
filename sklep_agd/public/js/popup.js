function getUrlParam(key) {
    const urlParams = new URLSearchParams(window.location.search);
    return urlParams.get(key);
  }
  
  window.addEventListener('DOMContentLoaded', function() {
    const popupType = getUrlParam('popup');
    if (popupType) {
      showPopup(popupType);
    }
  });
  
  function showPopup(type) {
    const overlay = document.getElementById('modal-overlay');
    const msgEl = document.getElementById('modal-message');
    overlay.style.display = 'flex';

    let productName = getUrlParam('productName') || '---';
    let qty = getUrlParam('qty') || '1';
    let available = getUrlParam('available') || '?';

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
        case 'addedToFavorites':
            message = `<strong>Dodano do ulubionych!</strong><br>Przedmiot: <em>${productName}</em>`;
            break;
        case 'removedFromFavorites':
            message = `<strong>Usunięto z ulubionych:</strong><br><em>${productName}</em>`;
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

  