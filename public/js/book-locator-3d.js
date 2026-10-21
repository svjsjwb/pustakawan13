import { createScene, resizeScene } from './book-locator/scene.js';

import {
    createCamera,
    applyInitialCamera
} from './book-locator/camera.js';

import {
    createNavigation
} from './book-locator/navigation.js';

import {
    buildRackSystem
} from './book-locator/rack.js';

import {
    createPlayer
} from './book-locator/player.js';

import {
    createCollisionSystem
} from './book-locator/collision.js';

import {
    createBookInteraction
} from './book-locator/interaction.js';


document.addEventListener(
    'DOMContentLoaded',
    () => {

        /*
        |--------------------------------------------------------------------------
        | CONTAINER
        |--------------------------------------------------------------------------
        */

        const container =
            document.getElementById(
                'locator3d'
            );


        if (!container) {

            console.warn(
                'BOOK LOCATOR 3D: #locator3d tidak ditemukan.'
            );

            return;

        }


        /*
        |--------------------------------------------------------------------------
        | DATA DARI LARAVEL
        |--------------------------------------------------------------------------
        */

        const shelves =
            Array.isArray(
                window.bookLocatorShelves
            )
                ? window.bookLocatorShelves
                : [];


        const bookCopies =
            Array.isArray(
                window.bookLocatorCopies
            )
                ? window.bookLocatorCopies
                : [];


        const targetCopyId =
            Number(
                window.bookLocatorTargetCopyId || 0
            );


        /*
        |--------------------------------------------------------------------------
        | DEBUG
        |--------------------------------------------------------------------------
        */

        console.log(
            '===================================='
        );

        console.log(
            '3D LOCATOR DATA'
        );

        console.log(
            'Jumlah Shelf:',
            shelves.length
        );

        console.log(
            'Jumlah BookCopy:',
            bookCopies.length
        );

        console.log(
            'Target BookCopy:',
            targetCopyId
        );

        console.log(
            'Shelves:',
            shelves
        );

        console.log(
            'BookCopies:',
            bookCopies
        );

        console.log(
            '===================================='
        );


        /*
        |--------------------------------------------------------------------------
        | RESET CONTAINER
        |--------------------------------------------------------------------------
        */

        container.innerHTML = '';


        /*
        |--------------------------------------------------------------------------
        | CROSSHAIR
        |--------------------------------------------------------------------------
        */

        const crosshair =
            document.createElement(
                'div'
            );


        crosshair.className =
            'locator-crosshair';


        crosshair.innerHTML =
            '+';


        container.appendChild(
            crosshair
        );


        /*
        |--------------------------------------------------------------------------
        | MODAL
        |--------------------------------------------------------------------------
        */

        const locatorModal =
            document.getElementById(
                'locatorCopyModal'
            );


        if (locatorModal) {

            container.appendChild(
                locatorModal
            );

        }


        /*
        |--------------------------------------------------------------------------
        | SCENE
        |--------------------------------------------------------------------------
        */

        const {
            scene,
            renderer,
            locatorGroup,
            navigationGroup
        } =
            createScene(
                container
            );


        /*
        |--------------------------------------------------------------------------
        | TOMBOL FULLSCREEN / FPS
        |--------------------------------------------------------------------------
        */

        const fullscreenButton =
            document.createElement(
                'button'
            );


        fullscreenButton.type =
            'button';


        fullscreenButton.textContent =
            'Jelajahi 3D';


        fullscreenButton.className =
            'enter-3d-button';


        container.parentElement.style.position =
            'relative';


        fullscreenButton.style.position =
            'absolute';


        fullscreenButton.style.top =
            '18px';


        fullscreenButton.style.right =
            '18px';


        fullscreenButton.style.zIndex =
            '100';


        container.parentElement.appendChild(
            fullscreenButton
        );


        /*
        |--------------------------------------------------------------------------
        | CAMERA
        |--------------------------------------------------------------------------
        */

        const {
            camera,
            orbitControls,
            fpsControls
        } =
            createCamera(
                container,
                renderer
            );


        /*
        |--------------------------------------------------------------------------
        | RAK
        |--------------------------------------------------------------------------
        */

        const rackSystem =
            buildRackSystem({

                locatorGroup,

                shelves,

                bookCopies,

                targetCopyId

            });


        /*
        |--------------------------------------------------------------------------
        | NAVIGATION ARROW
        |--------------------------------------------------------------------------
        */

        const navigation =
            createNavigation({

                scene,

                locatorGroup,

                targetCopyId

            });


        /*
        |--------------------------------------------------------------------------
        | INITIAL CAMERA
        |--------------------------------------------------------------------------
        */

        applyInitialCamera(

            camera,

            orbitControls,

            rackSystem

        );


        /*
        |--------------------------------------------------------------------------
        | COLLISION
        |--------------------------------------------------------------------------
        */

        const collision =
            createCollisionSystem();


        collision.setColliders(
            locatorGroup.children
        );


        /*
        |--------------------------------------------------------------------------
        | PLAYER / FPS
        |--------------------------------------------------------------------------
        */

        const player =
            createPlayer({

                camera,

                renderer,

                container,

                fpsControls,

                orbitControls,

                collision

            });


        /*
        |--------------------------------------------------------------------------
        | INTERACTION BUKU
        |--------------------------------------------------------------------------
        |
        | Player sekarang dikirim ke interaction.
        |
        | Tujuannya:
        | - klik buku saat FPS
        | - pointer lock dilepas
        | - popup bisa diklik
        | - setelah popup ditutup FPS bisa dilanjutkan
        |
        */

        const bookInteraction =
            createBookInteraction({

                renderer,

                camera,

                locatorGroup,

                bookCopies:
                    rackSystem.normalizedCopies,

                player

            });


        /*
        |--------------------------------------------------------------------------
        | TOMBOL JELAJAHI 3D
        |--------------------------------------------------------------------------
        */

        fullscreenButton.addEventListener(
            'click',
            async () => {

                try {

                    if (
                        !document.fullscreenElement
                    ) {

                        await container.requestFullscreen();

                    }


                    player.enterWalkMode();

                } catch (error) {

                    console.error(
                        'BOOK LOCATOR: gagal masuk fullscreen.',
                        error
                    );

                }

            }
        );


        /*
        |--------------------------------------------------------------------------
        | DEBUG COLLISION
        |--------------------------------------------------------------------------
        */

        console.log(
            'Jumlah collider:',
            collision.colliders.length
        );


        /*
        |--------------------------------------------------------------------------
        | RESIZE
        |--------------------------------------------------------------------------
        */

        function resize() {

            resizeScene(

                container,

                camera,

                renderer

            );

        }


        window.addEventListener(
            'resize',
            resize
        );


        const resizeObserver =
            new ResizeObserver(
                () => resize()
            );


        resizeObserver.observe(
            container
        );


        /*
        |--------------------------------------------------------------------------
        | ANIMATION LOOP
        |--------------------------------------------------------------------------
        */

        let lastTime =
            performance.now();


        function animate(
            now = performance.now()
        ) {

            requestAnimationFrame(
                animate
            );


            const delta =
                Math.min(

                    (
                        now -
                        lastTime
                    ) / 1000,

                    0.05

                );


            lastTime =
                now;


            /*
            |--------------------------------------------------------------------------
            | ORBIT
            |--------------------------------------------------------------------------
            */

            if (
                orbitControls.enabled
            ) {

                orbitControls.update();

            }


            /*
            |--------------------------------------------------------------------------
            | PLAYER
            |--------------------------------------------------------------------------
            */

            player.update(
                delta
            );

            bookInteraction.update();


            /*
            |--------------------------------------------------------------------------
            | NAVIGATION
            |--------------------------------------------------------------------------
            |
            | Arrow selalu mengikuti posisi player.
            |
            */

            navigation.update(

                {
                    x:
                        camera.position.x,

                    z:
                        camera.position.z
                },

                delta

            );


            /*
            |--------------------------------------------------------------------------
            | RENDER
            |--------------------------------------------------------------------------
            */

            renderer.render(

                scene,

                camera

            );

        }


        animate();


        /*
        |--------------------------------------------------------------------------
        | FINAL DEBUG
        |--------------------------------------------------------------------------
        */

        console.log(
            '===================================='
        );


        console.log(
            '3D LOCATOR BERHASIL'
        );


        console.log(
            'Total rak fisik:',
            rackSystem.physicalRackList.length
        );


        console.log(

            'Rak:',

            rackSystem.physicalRackList.map(

                ([code, shelfList]) => ({

                    code,

                    shelves:
                        shelfList.map(
                            shelf =>
                                shelf.code
                        )

                })

            )

        );


        console.log(
            'Total book copy:',
            rackSystem.normalizedCopies.length
        );


        console.log(
            'Target:',
            targetCopyId
        );


        console.log(
            'FPS player prepared:',
            player.enabled === false
        );


        console.log(
            'Collision prepared:',
            collision.enabled === false
        );


        console.log(
            'Book interaction:',
            bookInteraction
        );


        console.log(
            '===================================='
        );

    }
);