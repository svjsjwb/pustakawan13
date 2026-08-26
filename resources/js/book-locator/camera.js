import * as THREE from 'three';

import {
    OrbitControls
} from 'three/addons/controls/OrbitControls.js';

import {
    PointerLockControls
} from 'three/addons/controls/PointerLockControls.js';

import {
    CONFIG
} from './config.js';


export function createCamera(
    container,
    renderer
) {

    const camera =
        new THREE.PerspectiveCamera(

            42,

            container.clientWidth /
            Math.max(
                container.clientHeight,
                1
            ),

            0.1,

            1000

        );


    /* =========================================================
       ORBIT / FREECAM
    ========================================================== */

    const orbitControls =
        new OrbitControls(

            camera,

            renderer.domElement

        );


    orbitControls.enableDamping =
        true;


    orbitControls.dampingFactor =
        0.08;


    orbitControls.enablePan =
        true;


    orbitControls.enableZoom =
        true;


    orbitControls.minDistance =
        5;


    orbitControls.maxDistance =
        70;


    orbitControls.maxPolarAngle =
        Math.PI * 0.82;


    orbitControls.minPolarAngle =
        Math.PI * 0.12;


    orbitControls.enabled =
        true;


    /* =========================================================
       FPS / WALK MODE
    ========================================================== */

    const fpsControls =
        new PointerLockControls(

            camera,

            renderer.domElement

        );


    /*
     * PointerLockControls tetap dibuat
     * supaya kompatibel dengan sistem player
     * yang sudah ada.
     *
     * Tetapi mode FPS kita sekarang
     * TIDAK menggunakan lock().
     */

    fpsControls.enabled =
        false;


    return {

        camera,

        orbitControls,

        fpsControls

    };

}


/* =============================================================
   INITIAL CAMERA
============================================================= */

export function applyInitialCamera(
    camera,
    orbitControls,
    view
) {

    /*
    |--------------------------------------------------------------------------
    | SPAWN FIXED
    |--------------------------------------------------------------------------
    |
    | Jangan gunakan posisi target buku.
    |
    | Target buku boleh berada di Rak A/B/C,
    | tetapi kamera awal selalu berada di
    | titik spawn yang sama.
    |
    */

    const spawnX = 0;

    const spawnY =
        CONFIG.rackHeight * 0.72;

    const spawnZ =
        CONFIG.cameraDistance;


    /*
    |--------------------------------------------------------------------------
    | TARGET VIEW FIXED
    |--------------------------------------------------------------------------
    |
    | Kamera melihat ke area Rak A.
    |
    | Tidak peduli buku target berada
    | di rak mana.
    |
    */

    const targetX = 0;

    const targetY =
        CONFIG.rackHeight / 2;

    const targetZ = 0;


    /*
    |--------------------------------------------------------------------------
    | SET CAMERA
    |--------------------------------------------------------------------------
    */

    camera.position.set(

        spawnX,

        spawnY,

        spawnZ

    );


    /*
    |--------------------------------------------------------------------------
    | SET ORBIT TARGET
    |--------------------------------------------------------------------------
    */

    orbitControls.target.set(

        targetX,

        targetY,

        targetZ

    );


    orbitControls.update();

}