document.addEventListener('DOMContentLoaded', function () {
    const popup = document.getElementById('guestApprovalPopup');
    if (!popup) return;

    popup.addEventListener('click', function (e) {
        if (e.target === popup) {
            closeGuestApprovalPopup();
        }
    });

    document.addEventListener('keydown', function (e) {
        if (e.key === 'Escape' && document.getElementById('guestApprovalPopup')) {
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