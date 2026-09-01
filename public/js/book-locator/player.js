import * as THREE from 'three';

const DEFAULT_PLAYER = {
    height: 4.1,
    radius: 0.35,
    speed: 10.0,
    jumpPower: 16.0,
    gravity: 42.0
};


export function createPlayer({
    camera,
    renderer,
    container,
    fpsControls,
    orbitControls,
    collision
}) {

    const player = {

        ...DEFAULT_PLAYER,

        enabled: false,

        modalPaused: false,

        sitting: false,

        sittingChair: null,

        velocityY: 0,

        grounded: true,

        position: {
            x: camera.position.x,
            y: 0,
            z: camera.position.z
        },

        keys: {
            forward: false,
            backward: false,
            left: false,
            right: false
        }

    };


    /*
    |--------------------------------------------------------------------------
    | GLOBAL REFERENCE
    |--------------------------------------------------------------------------
    */

    window.bookLocatorPlayer =
        player;


    /*
    |--------------------------------------------------------------------------
    | RESET MOVEMENT
    |--------------------------------------------------------------------------
    */

    function resetKeys() {

        player.keys.forward = false;
        player.keys.backward = false;
        player.keys.left = false;
        player.keys.right = false;

    }


    /*
    |--------------------------------------------------------------------------
    | KEYBOARD
    |--------------------------------------------------------------------------
    */

    function setKey(code, value) {

        if (
            code === 'KeyW' ||
            code === 'ArrowUp'
        ) {

            player.keys.forward = value;

        }


        if (
            code === 'KeyS' ||
            code === 'ArrowDown'
        ) {

            player.keys.backward = value;

        }


        if (
            code === 'KeyA' ||
            code === 'ArrowLeft'
        ) {

            player.keys.left = value;

        }


        if (
            code === 'KeyD' ||
            code === 'ArrowRight'
        ) {

            player.keys.right = value;

        }

    }


    function onKeyDown(event) {

        if (
            !player.enabled ||
            player.modalPaused
        ) {

            return;

        }


        setKey(
            event.code,
            true
        );


        if (
            [
                'KeyW',
                'KeyA',
                'KeyS',
                'KeyD',
                'ArrowUp',
                'ArrowDown',
                'ArrowLeft',
                'ArrowRight'
            ].includes(event.code)
        ) {

            event.preventDefault();

        }


        /*
        |--------------------------------------------------------------------------
        | JUMP
        |--------------------------------------------------------------------------
        */

        if (
            event.code === 'Space' &&
            player.grounded
        ) {

            player.velocityY =
                player.jumpPower;

            player.grounded =
                false;

            event.preventDefault();

        }

    }


    function onKeyUp(event) {

        setKey(
            event.code,
            false
        );

    }


    /*
    |--------------------------------------------------------------------------
    | ENTER FPS
    |--------------------------------------------------------------------------
    */

    function enterWalkMode() {

        if (player.enabled) {
            return;
        }


        player.modalPaused =
            false;


        player.position.x =
            camera.position.x;

        player.position.z =
            camera.position.z;

        player.position.y =
            0;


        camera.position.set(

            player.position.x,

            player.position.y +
            player.height,

            player.position.z

        );


        orbitControls.enabled =
            false;


        player.enabled =
            true;


        container.classList.add(
            'fps-mode'
        );


        container.style.cursor =
            'none';


        if (fpsControls) {

            fpsControls.enabled =
                true;

            fpsControls.lock();

        }

    }


    /*
    |--------------------------------------------------------------------------
    | EXIT FPS
    |--------------------------------------------------------------------------
    */

    function exitWalkMode() {

        if (
            !player.enabled &&
            !player.modalPaused
        ) {

            return;

        }


        player.modalPaused =
            false;

        player.enabled =
            false;


        resetKeys();


        if (fpsControls) {

            fpsControls.unlock();

            fpsControls.enabled =
                false;

        }


        orbitControls.enabled =
            true;


        orbitControls.target.set(

            camera.position.x,

            camera.position.y - 2,

            camera.position.z

        );


        orbitControls.update();


        container.classList.remove(
            'fps-mode'
        );


        container.style.cursor =
            'default';

    }


    /*
    |--------------------------------------------------------------------------
    | PAUSE FPS UNTUK MODAL
    |--------------------------------------------------------------------------
    */

    function pauseForModal() {

        if (!player.enabled) {
            return;
        }

        player.modalPaused = true;

        resetKeys();

        /*
        |--------------------------------------------------------------------------
        | LEPAS POINTER LOCK
        |--------------------------------------------------------------------------
        */

        if (fpsControls) {

            fpsControls.enabled = false;

            fpsControls.unlock();

        }

        /*
        |--------------------------------------------------------------------------
        | MATIKAN STATE FPS VISUAL
        |--------------------------------------------------------------------------
        |
        | Penting supaya CSS cursor:none
        | dari .fps-mode tidak tetap aktif.
        |
        */

        container.classList.remove(
            'fps-mode'
        );

        container.classList.add(
            'modal-active'
        );

        /*
        |--------------------------------------------------------------------------
        | TAMPILKAN CURSOR
        |--------------------------------------------------------------------------
        */

        container.style.cursor =
            'default';

        renderer.domElement.style.cursor =
            'default';

    }


    /*
    |--------------------------------------------------------------------------
    | RESUME FPS SETELAH MODAL
    |--------------------------------------------------------------------------
    */

    function resumeAfterModal() {

        if (!player.modalPaused) {
            return;
        }

        /*
        |--------------------------------------------------------------------------
        | KALAU FULLSCREEN SUDAH KELUAR
        |--------------------------------------------------------------------------
        */

        if (!document.fullscreenElement) {

            player.modalPaused = false;
            player.enabled = false;

            resetKeys();

            container.classList.remove(
                'modal-active'
            );

            container.classList.remove(
                'fps-mode'
            );

            container.style.cursor =
                'default';

            renderer.domElement.style.cursor =
                'default';

            return;
        }

        /*
        |--------------------------------------------------------------------------
        | KEMBALI FPS
        |--------------------------------------------------------------------------
        */

        player.modalPaused = false;
        player.enabled = true;

        orbitControls.enabled = false;

        container.classList.remove(
            'modal-active'
        );

        container.classList.add(
            'fps-mode'
        );

        container.style.cursor =
            'none';

        renderer.domElement.style.cursor =
            'none';

        if (fpsControls) {

            fpsControls.enabled =
                true;

            fpsControls.lock();

        }

    }


    /*
    |--------------------------------------------------------------------------
    | POINTER LOCK CHANGE
    |--------------------------------------------------------------------------
    */

    function onPointerLockChange() {

        /*
        |--------------------------------------------------------------------------
        | MODAL SEDANG TERBUKA
        |--------------------------------------------------------------------------
        |
        | unlock() dari pauseForModal()
        | jangan dianggap sebagai exit FPS.
        |
        */

        if (
            player.modalPaused
        ) {

            container.style.cursor =
                'default';

            return;

        }


        /*
        |--------------------------------------------------------------------------
        | POINTER LOCK AKTIF
        |--------------------------------------------------------------------------
        */

        if (
            document.pointerLockElement ===
            renderer.domElement
        ) {

            return;

        }


        /*
        |--------------------------------------------------------------------------
        | POINTER LOCK LEPAS NORMAL
        |--------------------------------------------------------------------------
        |
        | Misalnya user tekan ESC.
        |--------------------------------------------------------------------------
        */

        if (
            player.enabled
        ) {

            player.enabled =
                false;


            resetKeys();


            if (fpsControls) {

                fpsControls.enabled =
                    false;

            }


            orbitControls.enabled =
                true;


            container.classList.remove(
                'fps-mode'
            );


            container.style.cursor =
                'default';


            orbitControls.target.set(

                camera.position.x,

                camera.position.y - 2,

                camera.position.z

            );


            orbitControls.update();

        }

    }


    /*
    |--------------------------------------------------------------------------
    | FPS UNLOCK EVENT
    |--------------------------------------------------------------------------
    */

    if (fpsControls) {

        fpsControls.addEventListener(
            'unlock',
            () => {

                /*
                |--------------------------------------------------------------------------
                | JANGAN MATIKAN FPS SAAT MODAL
                |--------------------------------------------------------------------------
                */

                if (
                    player.modalPaused
                ) {

                    return;

                }


                /*
                |--------------------------------------------------------------------------
                | NORMAL UNLOCK
                |--------------------------------------------------------------------------
                */

                if (
                    !player.enabled
                ) {

                    return;

                }


                player.enabled =
                    false;


                resetKeys();


                fpsControls.enabled =
                    false;


                orbitControls.enabled =
                    true;


                container.classList.remove(
                    'fps-mode'
                );


                container.style.cursor =
                    'default';


                orbitControls.target.set(

                    camera.position.x,

                    camera.position.y - 2,

                    camera.position.z

                );


                orbitControls.update();

            }
        );

    }


    /*
    |--------------------------------------------------------------------------
    | EVENTS
    |--------------------------------------------------------------------------
    */

    document.addEventListener(
        'pointerlockchange',
        onPointerLockChange
    );


    document.addEventListener(
        'keydown',
        onKeyDown
    );


    document.addEventListener(
        'keyup',
        onKeyUp
    );


    /*duduk*/
    function sitOnChair(chair) {

        if (!player.enabled) {
            return false;
        }

        if (!chair) {
            return false;
        }

        if (player.sitting) {
            return false;
        }

        if (
            chair.userData?.interactionType !== 'sit'
        ) {
            return false;
        }


        const chairPosition =
            new THREE.Vector3();

        chair.getWorldPosition(
            chairPosition
        );


        const seatHeight =
            Number(
                chair.userData?.seatHeight ?? 1.25
            );


        player.position.x =
            chairPosition.x;

        player.position.z =
            chairPosition.z;

        player.position.y =
            0;


        player.velocityY =
            0;

        player.grounded =
            true;


        /*
        |----------------------------------------------------------------------
        | KAMERA DITURUNKAN KE POSISI DUDUK
        |----------------------------------------------------------------------
        */

        camera.position.x =
            chairPosition.x;

        camera.position.y =
            seatHeight + 2.65;

        camera.position.z =
            chairPosition.z;


        /*
        |----------------------------------------------------------------------
        | HADAP MENGIKUTI ARAH KURSI
        |----------------------------------------------------------------------
        */

        const forward =
            new THREE.Vector3(
                0,
                0,
                1
            );

        const quaternion =
            new THREE.Quaternion();


        chair.getWorldQuaternion(
            quaternion
        );


        forward.applyQuaternion(
            quaternion
        );

        forward.y = 0;


        if (
            forward.lengthSq() > 0
        ) {

            forward.normalize();


            const lookTarget =
                camera.position.clone();

            lookTarget.add(
                forward
            );

            camera.lookAt(
                lookTarget
            );

        }


        /*
        |----------------------------------------------------------------------
        | STATE
        |----------------------------------------------------------------------
        */

        player.sitting =
            true;

        player.sittingChair =
            chair;


        /*
        |----------------------------------------------------------------------
        | RESET WASD
        |----------------------------------------------------------------------
        */

        player.keys.forward = false;
        player.keys.backward = false;
        player.keys.left = false;
        player.keys.right = false;


        return true;
    }


    function standFromChair() {

        if (!player.sitting) {
            return false;
        }


        const chair =
            player.sittingChair;


        if (chair) {

            const chairPosition =
                new THREE.Vector3();

            chair.getWorldPosition(
                chairPosition
            );


            const forward =
                new THREE.Vector3(
                    0,
                    0,
                    1
                );

            const quaternion =
                new THREE.Quaternion();


            chair.getWorldQuaternion(
                quaternion
            );


            forward.applyQuaternion(
                quaternion
            );

            forward.y = 0;


            if (
                forward.lengthSq() > 0
            ) {

                forward.normalize();

            }


            const standDistance =
                1.8;


            const nextX =
                chairPosition.x -
                forward.x *
                standDistance;

            const nextZ =
                chairPosition.z -
                forward.z *
                standDistance;


            const blocked =
                collision.check(

                    {
                        x: nextX,
                        y: 0,
                        z: nextZ
                    },

                    player.radius,
                    player.height

                );


            if (!blocked) {

                player.position.x =
                    nextX;

                player.position.z =
                    nextZ;

            }

        }


        player.position.y =
            0;

        player.velocityY =
            0;

        player.grounded =
            true;


        camera.position.x =
            player.position.x;

        camera.position.y =
            player.height;

        camera.position.z =
            player.position.z;


        player.sitting =
            false;

        player.sittingChair =
            null;


        return true;
    }


    /*
    |--------------------------------------------------------------------------
    | UPDATE
    |--------------------------------------------------------------------------
    */

    function update(delta) {

        if (
            !player.enabled ||
            player.modalPaused ||
            player.sitting
        ) {

            return;

        }


        /*
        |--------------------------------------------------------------------------
        | GRAVITY
        |--------------------------------------------------------------------------
        */

        player.velocityY -=
            player.gravity *
            delta;


        player.position.y +=
            player.velocityY *
            delta;


        // ================================
        // COLLISION PERMUKAAN / LANTAI
        // ================================

        const surfaceY =
            collision.getSurfaceY(
                player.position,
                player.radius,
                player.position.y
            );


        /*
        |--------------------------------------------------------------------------
        | lantai normal
        |--------------------------------------------------------------------------
        */

        if (
            player.position.y <= 0
        ) {

            player.position.y =
                0;

            player.velocityY =
                0;

            player.grounded =
                true;

        }

        /*
|--------------------------------------------------------------------------
| MENDARAT DI ATAS OBJECT
|--------------------------------------------------------------------------
*/

        else if (
            player.velocityY <= 0 &&
            surfaceY > 0 &&
            player.position.y <=
            surfaceY + 0.35
        ) {

            player.position.y =
                surfaceY;

            player.velocityY =
                0;

            player.grounded =
                true;

        }

        /*
        |--------------------------------------------------------------------------
        | CAMERA HEIGHT
        |--------------------------------------------------------------------------
        */

        camera.position.y =
            player.position.y +
            player.height;


        /*
        |--------------------------------------------------------------------------
        | MOVEMENT
        |--------------------------------------------------------------------------
        */

        let moveX = 0;

        let moveZ = 0;


        if (player.keys.forward) {
            moveZ += 1;
        }


        if (player.keys.backward) {
            moveZ -= 1;
        }


        if (player.keys.left) {
            moveX -= 1;
        }


        if (player.keys.right) {
            moveX += 1;
        }


        const length =
            Math.hypot(
                moveX,
                moveZ
            );


        if (length <= 0) {
            return;
        }


        moveX /=
            length;

        moveZ /=
            length;


        /*
        |--------------------------------------------------------------------------
        | CAMERA DIRECTION
        |--------------------------------------------------------------------------
        */

        const direction =
            new THREE.Vector3();


        camera.getWorldDirection(
            direction
        );


        direction.y =
            0;


        if (
            direction.lengthSq() === 0
        ) {

            return;

        }


        direction.normalize();


        const forwardX =
            direction.x;

        const forwardZ =
            direction.z;


        /*
        |--------------------------------------------------------------------------
        | RIGHT VECTOR
        |--------------------------------------------------------------------------
        */

        const rightX =
            -forwardZ;

        const rightZ =
            forwardX;


        /*
        |--------------------------------------------------------------------------
        | NEXT POSITION
        |--------------------------------------------------------------------------
        */

        const speed =
            player.speed *
            delta;


        const nextX =
            player.position.x +
            (
                forwardX * moveZ +
                rightX * moveX
            ) *
            speed;


        const nextZ =
            player.position.z +
            (
                forwardZ * moveZ +
                rightZ * moveX
            ) *
            speed;


        /*
        |--------------------------------------------------------------------------
        | COLLISION
        |--------------------------------------------------------------------------
        */

        /*
|--------------------------------------------------------------------------
| COLLISION SLIDING
|--------------------------------------------------------------------------
|
| Jangan cek X + Z sekaligus.
| Cek masing-masing sumbu secara terpisah.
|
| Kalau X terhalang tetapi Z tidak:
|   → tetap bergerak di Z
|
| Kalau Z terhalang tetapi X tidak:
|   → tetap bergerak di X
|
| Hasilnya player terasa "meluncur"
| sepanjang permukaan rak.
|
*/


        /*
        |--------------------------------------------------------------------------
        | CEK GERAK X
        |--------------------------------------------------------------------------
        */

        const blockedX =
            collision.check(
                {
                    x: nextX,
                    y: player.position.y,
                    z: player.position.z
                },
                player.radius,
                player.height
            );


        /*
        |--------------------------------------------------------------------------
        | CEK GERAK Z
        |--------------------------------------------------------------------------
        */

        const blockedZ =
            collision.check(
                {
                    x: player.position.x,
                    y: player.position.y,
                    z: nextZ
                },
                player.radius,
                player.height
            );


        /*
        |--------------------------------------------------------------------------
        | GERAK X
        |--------------------------------------------------------------------------
        */

        if (!blockedX) {

            player.position.x =
                nextX;

            camera.position.x =
                nextX;

        }


        /*
        |--------------------------------------------------------------------------
        | GERAK Z
        |--------------------------------------------------------------------------
        */

        if (!blockedZ) {

            player.position.z =
                nextZ;

            camera.position.z =
                nextZ;

        }

    }


    /*
    |--------------------------------------------------------------------------
    | CLEANUP
    |--------------------------------------------------------------------------
    */

    function destroy() {

        document.removeEventListener(
            'keydown',
            onKeyDown
        );


        document.removeEventListener(
            'keyup',
            onKeyUp
        );


        document.removeEventListener(
            'pointerlockchange',
            onPointerLockChange
        );

    }


    /*
    |--------------------------------------------------------------------------
    | RETURN API
    |--------------------------------------------------------------------------
    */

    return {

        get enabled() {

            return player.enabled;

        },

        get sitting() {

            return player.sitting;

        },

        get sittingChair() {

            return player.sittingChair;

        },

        enterWalkMode,

        exitWalkMode,

        sitOnChair,

        standFromChair,

        pauseForModal,

        resumeAfterModal,

        update,

        destroy

    };

}