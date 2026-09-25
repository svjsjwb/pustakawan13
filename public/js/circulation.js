document.addEventListener('DOMContentLoaded', function () {

    const searchInput =
        document.getElementById('borrowingSearch');

    const rows =
        document.querySelectorAll(
            '#borrowingTable tbody tr.borrowing-row'
        );

    if (searchInput) {

        searchInput.addEventListener('input', function () {

            const keyword =
                this.value.toLowerCase().trim();

            rows.forEach(function (row) {

                const text =
                    row.textContent.toLowerCase();

                row.style.display =
                    text.includes(keyword)
                        ? ''
                        : 'none';

            });

        });

    }

    const extendModal =
        document.getElementById('extendModal');

    const extendForm =
        document.getElementById('extendForm');

    document
        .querySelectorAll('.extend-btn')
        .forEach(function(btn){

            btn.addEventListener('click', function(){

                const borrowingId =
                    this.dataset.id;

                extendForm.action =
                    `/circulation/${borrowingId}/extend`;

                extendModal.classList.add('show');

            });

        });

    document
        .getElementById('closeExtendModal')
        ?.addEventListener('click', function(){

            extendModal.classList.remove('show');

        });

    extendModal?.addEventListener('click', function(e){

        if(e.target === extendModal){

            extendModal.classList.remove('show');

        }

    });

});