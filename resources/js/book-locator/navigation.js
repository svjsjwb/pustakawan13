import * as THREE from 'three';

import { CONFIG } from './config.js';


/*
|--------------------------------------------------------------------------
| NAVIGATION CONFIG
|--------------------------------------------------------------------------
*/

const ARROW_DISTANCE_FROM_PLAYER = 5.0;

const ARROW_HEIGHT = 2.1;

const ARROW_FLOAT_AMPLITUDE = 0.18;

const ARROW_FLOAT_SPEED = 3.0;

const ARROW_PULSE_SPEED = 4.0;

const ARROW_MIN_OPACITY = 0.45;

const ARROW_MAX_OPACITY = 0.85;

const TARGET_STOP_DISTANCE = 1.8;


/*
|--------------------------------------------------------------------------
| NAVIGATION SYSTEM
|--------------------------------------------------------------------------
*/

export function createNavigation({
    scene,
    locatorGroup,
    targetCopyId
}) {

    /*
    |--------------------------------------------------------------------------
    | GROUP
    |--------------------------------------------------------------------------
    */

    const navigationGroup =
        new THREE.Group();

    navigationGroup.name =
        'NavigationArrow';

    scene.add(
        navigationGroup
    );


    /*
    |--------------------------------------------------------------------------
    | STATE
    |--------------------------------------------------------------------------
    */

    let targetPosition = null;

    let navigationArrow = null;

    let pulseTime = 0;


    /*
    |--------------------------------------------------------------------------
    | FIND TARGET BOOK
    |--------------------------------------------------------------------------
    */

    function findTargetBook() {

        let result = null;


        locatorGroup.traverse(
            object => {

                if (result) {
                    return;
                }


                if (
                    Number(
                        object.userData?.copyId
                    ) ===
                    Number(targetCopyId)
                ) {

                    result =
                        object;

                }

            }
        );


        return result;

    }


    /*
    |--------------------------------------------------------------------------
    | UPDATE TARGET POSITION
    |--------------------------------------------------------------------------
    */

    function updateTarget() {

        const target =
            findTargetBook();


        if (!target) {

            targetPosition =
                null;

            if (navigationArrow) {

                navigationArrow.visible =
                    false;

            }

            return null;

        }


        const worldPosition =
            new THREE.Vector3();


        target.getWorldPosition(
            worldPosition
        );


        targetPosition =
            worldPosition;


        /*
        |--------------------------------------------------------------------------
        | Arrow target berada pada bidang horizontal.
        |--------------------------------------------------------------------------
        */

        targetPosition.y =
            ARROW_HEIGHT;


        return targetPosition;

    }


    /*
    |--------------------------------------------------------------------------
    | CREATE ARROW
    |--------------------------------------------------------------------------
    */

    function createNavigationArrow() {

        /*
        |--------------------------------------------------------------------------
        | CONE
        |--------------------------------------------------------------------------
        |
        | ConeGeometry default mengarah ke +Y.
        |
        | Nanti kita putar menggunakan quaternion
        | supaya +Y mengarah ke target.
        |
        */

        const geometry =
            new THREE.ConeGeometry(
                0.48,
                1.15,
                3
            );


        /*
        |--------------------------------------------------------------------------
        | MATERIAL
        |--------------------------------------------------------------------------
        */

        const material =
            new THREE.MeshBasicMaterial({

                color: 0x35a7ff,

                transparent: true,

                opacity: ARROW_MAX_OPACITY,

                depthWrite: false,

                side: THREE.DoubleSide

            });


        /*
        |--------------------------------------------------------------------------
        | MESH
        |--------------------------------------------------------------------------
        */

        navigationArrow =
            new THREE.Mesh(
                geometry,
                material
            );


        navigationArrow.name =
            'NavigationArrow';


        /*
        |--------------------------------------------------------------------------
        | POSISI AWAL
        |--------------------------------------------------------------------------
        */

        navigationArrow.position.set(

            0,

            ARROW_HEIGHT,

            0

        );


        /*
        |--------------------------------------------------------------------------
        | ADD KE SCENE
        |--------------------------------------------------------------------------
        */

        navigationGroup.add(
            navigationArrow
        );

    }


    /*
    |--------------------------------------------------------------------------
    | UPDATE ARROW
    |--------------------------------------------------------------------------
    */

    function updateNavigationArrow(
        playerPosition,
        delta
    ) {

        /*
        |--------------------------------------------------------------------------
        | TARGET TIDAK ADA
        |--------------------------------------------------------------------------
        */

        if (
            !targetPosition
        ) {

            if (navigationArrow) {

                navigationArrow.visible =
                    false;

            }

            return;

        }


        /*
        |--------------------------------------------------------------------------
        | BUAT ARROW JIKA BELUM ADA
        |--------------------------------------------------------------------------
        */

        if (!navigationArrow) {

            createNavigationArrow();

        }


        /*
        |--------------------------------------------------------------------------
        | ARAH PLAYER → TARGET
        |--------------------------------------------------------------------------
        */

        const direction =
            new THREE.Vector3(

                targetPosition.x -
                playerPosition.x,

                0,

                targetPosition.z -
                playerPosition.z

            );


        const distance =
            direction.length();


        /*
        |--------------------------------------------------------------------------
        | TARGET SUDAH SANGAT DEKAT
        |--------------------------------------------------------------------------
        */

        if (
            distance <=
            TARGET_STOP_DISTANCE
        ) {

            navigationArrow.visible =
                false;

            return;

        }


        /*
        |--------------------------------------------------------------------------
        | NORMALIZE
        |--------------------------------------------------------------------------
        */

        direction.normalize();


        /*
        |--------------------------------------------------------------------------
        | ARROW SELALU TERLIHAT
        |--------------------------------------------------------------------------
        */

        navigationArrow.visible =
            true;


        /*
        |--------------------------------------------------------------------------
        | POSISI ARROW DI DEPAN PLAYER
        |--------------------------------------------------------------------------
        */

        const arrowX =
            playerPosition.x +
            direction.x *
            ARROW_DISTANCE_FROM_PLAYER;


        const arrowZ =
            playerPosition.z +
            direction.z *
            ARROW_DISTANCE_FROM_PLAYER;


        /*
        |--------------------------------------------------------------------------
        | FLOATING
        |--------------------------------------------------------------------------
        */

        pulseTime +=
            delta *
            ARROW_FLOAT_SPEED;


        const floatOffset =
            Math.sin(
                pulseTime
            ) *
            ARROW_FLOAT_AMPLITUDE;


        navigationArrow.position.set(

            arrowX,

            ARROW_HEIGHT +
            floatOffset,

            arrowZ

        );


        /*
        |--------------------------------------------------------------------------
        | ARAHKAN ARROW
        |--------------------------------------------------------------------------
        |
        | Cone default mengarah ke +Y.
        |
        | Kita ubah +Y → direction target.
        |
        */

        const up =
            new THREE.Vector3(
                0,
                1,
                0
            );


        navigationArrow.quaternion.setFromUnitVectors(
            up,
            direction
        );


        /*
        |--------------------------------------------------------------------------
        | BLINK / PULSE
        |--------------------------------------------------------------------------
        */

        const pulse =
            (
                Math.sin(
                    pulseTime *
                    ARROW_PULSE_SPEED
                ) +
                1
            ) / 2;


        navigationArrow.material.opacity =
            ARROW_MIN_OPACITY +
            (
                (
                    ARROW_MAX_OPACITY -
                    ARROW_MIN_OPACITY
                ) *
                pulse
            );

    }


    /*
    |--------------------------------------------------------------------------
    | CLEAR NAVIGATION
    |--------------------------------------------------------------------------
    */

    function clearPath() {

        if (!navigationArrow) {
            return;
        }


        navigationArrow.visible =
            false;

    }


    /*
    |--------------------------------------------------------------------------
    | MAIN UPDATE
    |--------------------------------------------------------------------------
    */

    function update(
        playerPosition,
        delta = 0.016
    ) {

        /*
        |--------------------------------------------------------------------------
        | UPDATE TARGET
        |--------------------------------------------------------------------------
        */

        updateTarget();


        /*
        |--------------------------------------------------------------------------
        | UPDATE ARROW
        |--------------------------------------------------------------------------
        |
        | Dipanggil setiap frame.
        |
        | Jadi ketika player bergerak,
        | arrow langsung ikut berpindah.
        |
        */

        updateNavigationArrow(
            playerPosition,
            delta
        );

    }


    /*
    |--------------------------------------------------------------------------
    | INITIAL TARGET
    |--------------------------------------------------------------------------
    */

    updateTarget();


    /*
    |--------------------------------------------------------------------------
    | RETURN API
    |--------------------------------------------------------------------------
    */

    return {

        group:
            navigationGroup,


        update,


        updateTarget,


        clearPath,


        getTargetPosition() {

            return targetPosition;

        }

    };

}