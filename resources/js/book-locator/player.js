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
        velocityY: 0,
        grounded: true,

        // Posisi kaki player. Kamera berada di +height.
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

    function setKey(code, value) {
        if (code === 'KeyW' || code === 'ArrowUp') {
            player.keys.forward = value;
        }

        if (code === 'KeyS' || code === 'ArrowDown') {
            player.keys.backward = value;
        }

        if (code === 'KeyA' || code === 'ArrowLeft') {
            player.keys.left = value;
        }

        if (code === 'KeyD' || code === 'ArrowRight') {
            player.keys.right = value;
        }
    }

    function onKeyDown(event) {
        setKey(event.code, true);

        if (
            player.enabled &&
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

        if (
            event.code === 'Space' &&
            player.enabled &&
            player.grounded
        ) {

            player.velocityY =
                player.jumpPower;

            player.grounded = false;

            event.preventDefault();
        }
    }

    function onKeyUp(event) {
        setKey(event.code, false);
    }

    function enterWalkMode() {
        if (player.enabled) {
            return;
        }

        // Simpan posisi horizontal orbital saat masuk FPS.
        player.position.x = camera.position.x;
        player.position.z = camera.position.z;

        // Untuk tahap 1, player berdiri di lantai.
        player.position.y = 0;

        camera.position.set(
            player.position.x,
            player.position.y + player.height,
            player.position.z
        );

        orbitControls.enabled = false;
        fpsControls.enabled = true;
        player.enabled = true;

        fpsControls.lock();
    }

    function exitWalkMode() {
        if (!player.enabled) {
            return;
        }

        fpsControls.unlock();

        player.enabled = false;
        fpsControls.enabled = false;

        // Kembalikan OrbitControls pada posisi kamera terakhir.
        orbitControls.enabled = true;
        orbitControls.target.set(
            camera.position.x,
            camera.position.y - 2,
            camera.position.z
        );
        orbitControls.update();
    }

    function onPointerLockChange() {
        if (!document.pointerLockElement) {
            player.enabled = false;
            fpsControls.enabled = false;
            orbitControls.enabled = true;
            orbitControls.update();
        }
    }

    // Klik area 3D = masuk mode jalan.
    container.addEventListener('click', () => {
        if (!player.enabled) {
            enterWalkMode();
        }
    });

    fpsControls.addEventListener(
        'unlock',
        () => {
            player.enabled = false;
            fpsControls.enabled = false;
            orbitControls.enabled = true;
            orbitControls.update();
        }
    );

    document.addEventListener(
        'keydown',
        onKeyDown
    );

    document.addEventListener(
        'keyup',
        onKeyUp
    );

    document.addEventListener(
        'pointerlockchange',
        onPointerLockChange
    );

    function update(delta) {
        if (!player.enabled) {
            return;
        }

        // ================================
        // GRAVITASI & JUMP
        // ================================

        player.velocityY -=
            player.gravity * delta;

        player.position.y +=
            player.velocityY * delta;


        // ================================
        // BATAS LANTAI
        // ================================

        if (player.position.y <= 0) {

            player.position.y = 0;

            player.velocityY = 0;

            player.grounded = true;
        }


        // ================================
        // UPDATE POSISI KAMERA
        // ================================

        camera.position.y =
            player.position.y +
            player.height;

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
            Math.hypot(moveX, moveZ);

        if (length > 0) {

            moveX /= length;
            moveZ /= length;

            const speed =
                player.speed * delta;

            // ============================================
            // ARAH KAMERA
            // ============================================

            const direction =
                fpsControls.getDirection(
                    _direction
                );

            direction.y = 0;
            direction.normalize();

            const forwardX =
                direction.x;

            const forwardZ =
                direction.z;

            const forwardLength =
                Math.hypot(
                    forwardX,
                    forwardZ
                ) || 1;

            const fx =
                forwardX / forwardLength;

            const fz =
                forwardZ / forwardLength;

            const rx = -fz;
            const rz = fx;


            // ============================================
            // HITUNG POSISI BERIKUTNYA
            // ============================================

            const nextX =
                player.position.x +
                (fx * moveZ + rx * moveX) *
                speed;

            const nextZ =
                player.position.z +
                (fz * moveZ + rz * moveX) *
                speed;


            // ============================================
            // CEK COLLISION
            // ============================================

            const blocked =
                collision.check(
                    {
                        x: nextX,
                        y: player.position.y,
                        z: nextZ
                    },
                    player.radius,
                    player.height
                );


            // ============================================
            // BOLEH BERGERAK JIKA TIDAK MENABRAK
            // ============================================

            if (!blocked) {

                player.position.x =
                    nextX;

                player.position.z =
                    nextZ;

                camera.position.x =
                    nextX;

                camera.position.z =
                    nextZ;
            }
        }
    }

    return {
        ...player,
        enterWalkMode,
        exitWalkMode,
        update
    };
}

const _direction = new THREE.Vector3();
