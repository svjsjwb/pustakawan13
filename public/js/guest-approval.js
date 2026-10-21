document.addEventListener('DOMContentLoaded', function () {
    const popup = document.getElementById('guestApprovalPopup');

    if (!popup) return;

    // Bersihkan key lama jika ada
    try {
        sessionStorage.removeItem('guestApprovalPopupShown');
    } catch (e) {}

    const userId = popup.getAttribute('data-user-id') || 'guest';
    const storageKey = 'guestApprovalDismissed_' + userId;

    // Hanya hilangkan jika user SUDAH PERNAH memencet "Mengerti" / tombol tutup
    if (sessionStorage.getItem(storageKey) === '1') {
        popup.remove();
        return;
    }
});

function closeGuestApprovalPopup() {
    const popup = document.getElementById('guestApprovalPopup');

    if (!popup) return;

    const userId = popup.getAttribute('data-user-id') || 'guest';
    const storageKey = 'guestApprovalDismissed_' + userId;

    // Simpan penanda bahwa user SUDAH memencet Mengerti / Tutup
    try {
        sessionStorage.setItem(storageKey, '1');
    } catch (e) {}

    popup.classList.add('closing');

    setTimeout(function () {
        popup.remove();
    }, 180);
}