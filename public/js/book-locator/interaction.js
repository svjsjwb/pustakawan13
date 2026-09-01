import * as THREE from 'three';


export function createBookInteraction({
    renderer,
    camera,
    locatorGroup,
    bookCopies,
    player
}) {

    const raycaster =
        new THREE.Raycaster();


    /*
    |--------------------------------------------------------------------------
    | MOUSE / CROSSHAIR
    |--------------------------------------------------------------------------
    |
    | TETAP DIPAKAI UNTUK KLIK BUKU
    |
    */

    const mouse =
        new THREE.Vector2(
            0,
            0
        );


    /*
    |--------------------------------------------------------------------------
    | JARAK INTERAKSI KURSI
    |--------------------------------------------------------------------------
    */

    const interactionDistance =
        2.0;


    /*
    |--------------------------------------------------------------------------
    | PROMPT INTERAKSI
    |--------------------------------------------------------------------------
    */

    const interactionPrompt =
        document.createElement(
            'div'
        );


    interactionPrompt.textContent =
        'Tekan E untuk duduk';


    interactionPrompt.style.position =
        'fixed';

    interactionPrompt.style.left =
        '50%';

    interactionPrompt.style.top =
        '50%';

    interactionPrompt.style.transform =
        'translateX(-50%, -100%)';

    interactionPrompt.style.padding =
        '10px 16px';

    interactionPrompt.style.background =
        'rgba(0, 0, 0, 0.65)';

    interactionPrompt.style.color =
        '#fff';

    interactionPrompt.style.borderRadius =
        '8px';

    interactionPrompt.style.fontSize =
        '14px';

    interactionPrompt.style.fontWeight =
        '600';

    interactionPrompt.style.pointerEvents =
        'none';

    interactionPrompt.style.userSelect =
        'none';

    interactionPrompt.style.zIndex =
        '9999';

    interactionPrompt.style.display =
        'none';


    renderer.domElement.parentElement.appendChild(
        interactionPrompt
    );


    /*
    |--------------------------------------------------------------------------
    | CARI OBJECT BUKU
    |--------------------------------------------------------------------------
    */

    function findBookObject(object) {

        let current =
            object;


        while (current) {

            if (
                current.userData &&
                current.userData.type ===
                'book'
            ) {

                return current;

            }


            current =
                current.parent;

        }


        return null;

    }


    /*
    |--------------------------------------------------------------------------
    | CARI KURSI TERDEKAT
    |--------------------------------------------------------------------------
    */

    function findNearestChair() {

        if (
            !player ||
            !player.enabled
        ) {

            return null;

        }


        const playerPosition =
            new THREE.Vector3(

                camera.position.x,

                0,

                camera.position.z

            );


        let nearestChair =
            null;


        let nearestDistance =
            interactionDistance;


        locatorGroup.traverse(
            function (object) {

                if (
                    object.userData?.interactionType !==
                    'sit'
                ) {

                    return;

                }


                const chairPosition =
                    new THREE.Vector3();


                object.getWorldPosition(
                    chairPosition
                );


                chairPosition.y =
                    0;


                const distance =
                    playerPosition.distanceTo(
                        chairPosition
                    );


                if (
                    distance <=
                    nearestDistance
                ) {

                    nearestDistance =
                        distance;


                    nearestChair =
                        object;

                }

            }
        );

        console.log(
            'Nearest chair:',
            nearestChair
        );
        return nearestChair;

    }


    function updatePromptPosition(chair) {

        if (!chair) {
            return;
        }

        const worldPosition =
            new THREE.Vector3();

        chair.getWorldPosition(
            worldPosition
        );


        /*
        |----------------------------------------------------------------------
        | NAIKKAN PROMPT DI ATAS KURSI
        |---------------------------------------------------------------------- 
        */

        worldPosition.y += 3.8;


        /*
        |----------------------------------------------------------------------
        | KONVERSI 3D → SCREEN
        |---------------------------------------------------------------------- 
        */

        worldPosition.project(
            camera
        );


        const width =
            window.innerWidth;

        const height =
            window.innerHeight;


        const x =
            (worldPosition.x * 0.5 + 0.5) *
            width;


        const y =
            (-worldPosition.y * 0.5 + 0.5) *
            height;


        interactionPrompt.style.left =
            `${x}px`;


        interactionPrompt.style.top =
            `${y}px`;


        interactionPrompt.style.bottom =
            'auto';


        interactionPrompt.style.transform =
            'translate(-50%, -100%)';
    }


    /*
    |--------------------------------------------------------------------------
    | BUKA MODAL
    |--------------------------------------------------------------------------
    */

    function openBookModal(copy) {

        const modal =
            document.getElementById(
                'locatorCopyModal'
            );


        if (!modal) {

            console.warn(
                'BOOK LOCATOR: #locatorCopyModal tidak ditemukan.'
            );

            return;

        }


        /*
        |--------------------------------------------------------------------------
        | PAUSE FPS
        |--------------------------------------------------------------------------
        */

        if (
            player &&
            player.enabled
        ) {

            player.pauseForModal();

        }


        /*
        |--------------------------------------------------------------------------
        | DATA COPY
        |--------------------------------------------------------------------------
        */

        const title =
            copy.title ??
            copy.book?.title ??
            'Buku';


        const barcode =
            copy.barcode ??
            '-';


        const status =
            copy.status ??
            '-';


        const shelf =
            copy.shelf?.code ??
            copy.shelf_code ??
            '-';


        const row =
            copy.row ??
            '-';


        const column =
            copy.column ??
            '-';


        const side =
            String(
                copy.side ??
                'front'
            ).toLowerCase();


        const sideLabel =
            side === 'back'
                ? 'Belakang'
                : 'Depan';


        /*
        |--------------------------------------------------------------------------
        | ELEMENT MODAL
        |--------------------------------------------------------------------------
        */

        const bookTitle =
            document.getElementById(
                'locatorModalBookTitle'
            );


        const copyNumber =
            document.getElementById(
                'locatorModalCopyNumber'
            );


        const barcodeElement =
            document.getElementById(
                'locatorModalBarcode'
            );


        const statusElement =
            document.getElementById(
                'locatorModalStatus'
            );


        const shelfElement =
            document.getElementById(
                'locatorModalShelf'
            );


        const positionElement =
            document.getElementById(
                'locatorModalPosition'
            );


        /*
        |--------------------------------------------------------------------------
        | ISI MODAL
        |--------------------------------------------------------------------------
        */

        if (bookTitle) {

            bookTitle.textContent =
                title;

        }


        if (copyNumber) {

            copyNumber.textContent =
                copy.id
                    ? `#${copy.id}`
                    : '-';

        }


        if (barcodeElement) {

            barcodeElement.textContent =
                barcode;

        }


        if (statusElement) {

            statusElement.textContent =
                status;

        }


        if (shelfElement) {

            shelfElement.textContent =
                shelf;

        }


        if (positionElement) {

            positionElement.textContent =
                `Muka ${sideLabel} • Baris ${row} • Kolom ${column}`;

        }


        /*
        |--------------------------------------------------------------------------
        | TAMPILKAN MODAL
        |--------------------------------------------------------------------------
        */

        modal.hidden =
            false;


        modal.classList.add(
            'is-open'
        );

    }


    /*
    |--------------------------------------------------------------------------
    | MODAL ELEMENT
    |--------------------------------------------------------------------------
    */

    const modal =
        document.getElementById(
            'locatorCopyModal'
        );


    const closeButton =
        document.getElementById(
            'locatorModalClose'
        );


    const overlay =
        modal?.querySelector(
            '.locator-modal-overlay'
        );


    /*
    |--------------------------------------------------------------------------
    | TUTUP MODAL
    |--------------------------------------------------------------------------
    */

    function closeModal() {

        if (!modal) {

            return;

        }


        modal.hidden =
            true;


        modal.classList.remove(
            'is-open'
        );


        /*
        |--------------------------------------------------------------------------
        | KEMBALI KE FPS
        |--------------------------------------------------------------------------
        */

        if (
            player &&
            document.fullscreenElement
        ) {

            player.resumeAfterModal();

        }

    }


    /*
    |--------------------------------------------------------------------------
    | CLOSE BUTTON
    |--------------------------------------------------------------------------
    */

    closeButton?.addEventListener(
        'click',
        closeModal
    );


    /*
    |--------------------------------------------------------------------------
    | OVERLAY
    |--------------------------------------------------------------------------
    */

    overlay?.addEventListener(
        'click',
        closeModal
    );


    /*
    |--------------------------------------------------------------------------
    | KLIK BUKU
    |--------------------------------------------------------------------------
    |
    | BAGIAN INI TETAP.
    |
    */

    function handlePointerDown(event) {

        if (
            event.button !== 0
        ) {

            return;

        }


        if (
            modal &&
            !modal.hidden
        ) {

            return;

        }


        mouse.x =
            0;

        mouse.y =
            0;


        raycaster.setFromCamera(
            mouse,
            camera
        );


        const intersections =
            raycaster.intersectObjects(
                locatorGroup.children,
                true
            );


        let selectedBook =
            null;


        for (
            const intersection
            of intersections
        ) {

            const book =
                findBookObject(
                    intersection.object
                );


            if (book) {

                selectedBook =
                    book;

                break;

            }

        }


        if (
            !selectedBook
        ) {

            return;

        }


        const copyId =
            Number(
                selectedBook.userData.copyId
            );


        if (!copyId) {

            console.warn(
                'BOOK LOCATOR: copyId tidak ditemukan pada object 3D.'
            );

            return;

        }


        const copy =
            bookCopies.find(
                item =>
                    Number(item.id) ===
                    copyId
            );


        if (!copy) {

            console.warn(
                'BOOK LOCATOR: BookCopy tidak ditemukan:',
                copyId
            );

            return;

        }


        console.log(
            'BOOK DIKLIK:',
            copy
        );


        openBookModal(
            copy
        );

    }


    /*
    |--------------------------------------------------------------------------
    | INTERAKSI E
    |--------------------------------------------------------------------------
    */

    function handleInteractionKey(event) {

        if (
            event.code !==
            'KeyE'
        ) {

            return;

        }


        if (
            !player ||
            !player.enabled
        ) {

            return;

        }


        if (
            player.modalPaused
        ) {

            return;

        }


        /*
        |--------------------------------------------------------------------------
        | SUDAH DUDUK → BERDIRI
        |--------------------------------------------------------------------------
        */

        if (
            player.sitting
        ) {

            player.standFromChair();

            interactionPrompt.style.display =
                'none';

            return;

        }


        /*
        |--------------------------------------------------------------------------
        | CARI KURSI TERDEKAT
        |--------------------------------------------------------------------------
        */

        const chair =
            findNearestChair();


        if (!chair) {

            return;

        }


        /*
        |--------------------------------------------------------------------------
        | DUDUK
        |--------------------------------------------------------------------------
        */

        const success =
            player.sitOnChair(
                chair
            );


        if (
            success
        ) {

            interactionPrompt.style.display =
                'none';

        }

    }


    /*
    |--------------------------------------------------------------------------
    | UPDATE PROMPT
    |--------------------------------------------------------------------------
    */

    function updateInteractionPrompt() {

        if (
            !player ||
            !player.enabled ||
            player.modalPaused
        ) {

            interactionPrompt.style.display =
                'none';

            return;

        }


        /*
        |--------------------------------------------------------------------------
        | SAAT DUDUK
        |--------------------------------------------------------------------------
        */

        if (
            player.sitting
        ) {

            interactionPrompt.textContent =
                'Tekan E untuk berdiri';

            interactionPrompt.style.display =
                'block';


            updatePromptPosition(
                player.sittingChair
            );


            return;

        }


        /*
        |--------------------------------------------------------------------------
        | CARI KURSI TERDEKAT
        |--------------------------------------------------------------------------
        */

        const chair =
            findNearestChair();


        /*
        |--------------------------------------------------------------------------
        | ADA KURSI
        |--------------------------------------------------------------------------
        */

        if (chair) {

            interactionPrompt.textContent =
                'Tekan E untuk duduk';

            interactionPrompt.style.display =
                'block';


            /*
            | Prompt mengikuti posisi kursi
            */

            updatePromptPosition(
                chair
            );


        } else {

            interactionPrompt.style.display =
                'none';

        }

    }


    /*
    |--------------------------------------------------------------------------
    | EVENT E
    |--------------------------------------------------------------------------
    */

    document.addEventListener(
        'keydown',
        handleInteractionKey
    );


    /*
    |--------------------------------------------------------------------------
    | EVENT CLICK
    |--------------------------------------------------------------------------
    */

    renderer.domElement.addEventListener(
        'pointerdown',
        handlePointerDown
    );


    /*
    |--------------------------------------------------------------------------
    | ESCAPE
    |--------------------------------------------------------------------------
    */

    function handleEscape(
        event
    ) {

        if (
            event.key === 'Escape' &&
            modal &&
            !modal.hidden
        ) {

            closeModal();

        }

    }


    document.addEventListener(
        'keydown',
        handleEscape
    );


    /*
    |--------------------------------------------------------------------------
    | RETURN
    |--------------------------------------------------------------------------
    */

    return {

        openBookModal,

        closeModal,

        update:
            updateInteractionPrompt,


        destroy() {

            document.removeEventListener(
                'keydown',
                handleInteractionKey
            );


            renderer.domElement
                .removeEventListener(
                    'pointerdown',
                    handlePointerDown
                );


            closeButton?.removeEventListener(
                'click',
                closeModal
            );


            overlay?.removeEventListener(
                'click',
                closeModal
            );


            document.removeEventListener(
                'keydown',
                handleEscape
            );


            interactionPrompt.remove();

        }

    };

}