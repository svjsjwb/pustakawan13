document.addEventListener('DOMContentLoaded', function () {

    const account =
        document.querySelector('.user-navbar-account');

    const profileButton =
        document.getElementById('userNavbarProfile');

    if (!account || !profileButton) {
        return;
    }


    profileButton.addEventListener(
        'click',
        function (event) {

            event.stopPropagation();

            account.classList.toggle('open');

        }
    );


    document.addEventListener(
        'click',
        function (event) {

            if (!account.contains(event.target)) {

                account.classList.remove('open');

            }

        }
    );


    document.addEventListener(
        'keydown',
        function (event) {

            if (event.key === 'Escape') {

                account.classList.remove('open');

            }

        }
    );

});