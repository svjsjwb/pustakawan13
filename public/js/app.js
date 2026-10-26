document.addEventListener('DOMContentLoaded', () => {

    document.addEventListener('selectstart', e => {
        const target = e.target;

        const isFormField =
            target.matches('input, textarea, select, [contenteditable="true"]') ||
            target.closest('input, textarea, select, [contenteditable="true"]');

        if (!isFormField) {
            e.preventDefault();
        }
    });

    document.addEventListener('dragstart', e => {
        const target = e.target;

        const isFormField =
            target.matches('input, textarea, select, [contenteditable="true"]') ||
            target.closest('input, textarea, select, [contenteditable="true"]');

        if (!isFormField) {
            e.preventDefault();
        }
    });

    document.addEventListener('mousedown', e => {
        if (e.detail > 1) {
            const target = e.target;

            const isFormField =
                target.matches('input, textarea, select, [contenteditable="true"]') ||
                target.closest('input, textarea, select, [contenteditable="true"]');

            if (!isFormField) {
                e.preventDefault();
            }
        }
    });

});