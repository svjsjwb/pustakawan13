document.addEventListener('DOMContentLoaded', function () {
    const popup = document.getElementById('guestApprovalPopup');

    if (!popup) return;

    // Kalau popup sudah pernah ditampilkan dalam session ini,
    // langsung hilangkan.
    if (sessionStorage.getItem('guestApprovalPopupShown') === '1') {
        popup.remove();
        return;
    }

    sessionStorage.setItem('guestApprovalPopupShown', '1');

    // Tutup popup ketika klik area gelap di luar modal
    popup.addEventListener('click', function (event) {
        if (event.target === popup) {
            closeGuestApprovalPopup();
        }
    });
});

function closeGuestApprovalPopup() {
    const popup = document.getElementById('guestApprovalPopup');

    if (!popup) return;

    popup.classList.add('closing');

    setTimeout(function () {
        popup.remove();
    }, 180);
}