document.addEventListener('DOMContentLoaded', () => {

    document.addEventListener('selectstart', e => {
        e.preventDefault();
    });

    document.addEventListener('dragstart', e => {
        e.preventDefault();
    });

    document.addEventListener('mousedown', e => {
        if (e.detail > 1) {
            e.preventDefault();
        }
    });

});