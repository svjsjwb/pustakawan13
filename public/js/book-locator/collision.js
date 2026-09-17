import * as THREE from 'three';


export function createCollisionSystem() {

    const system = {

        enabled: true,

        colliders: [],


        // =========================================================
        // BUAT COLLIDER DARI OBJECT THREE.JS
        // =========================================================

        add(object) {

            if (!object) {
                return;
            }

            const box =
                new THREE.Box3().setFromObject(
                    object
                );

            if (!box.isEmpty()) {

                system.colliders.push(box);

            }

        },


        // =========================================================
        // SET COLLIDERS
        // =========================================================

        setColliders(objects) {

            system.colliders = [];

            if (!Array.isArray(objects)) {
                return;
            }

            objects.forEach(
                object => {

                    system.add(object);

                }
            );

        },


        // =========================================================
        // CEK COLLISION HORIZONTAL
        // =========================================================
        //
        // Collision samping tetap berlaku.
        //
        // Tetapi kalau player sudah berada di atas object,
        // object tersebut tidak dianggap sebagai tembok.
        //
        // =========================================================

        check(
            position,
            radius = 0.35,
            height = 4.1
        ) {

            const playerBottom =
                position.y;

            const playerTop =
                position.y +
                height;


            const playerBox =
                new THREE.Box3(

                    new THREE.Vector3(
                        position.x - radius,
                        playerBottom,
                        position.z - radius
                    ),

                    new THREE.Vector3(
                        position.x + radius,
                        playerTop,
                        position.z + radius
                    )

                );


            for (
                const collider
                of system.colliders
            ) {

                /*
                |--------------------------------------------------------------------------
                | CEK OVERLAP HORIZONTAL
                |--------------------------------------------------------------------------
                */

                const horizontalOverlap =
                    (
                        playerBox.max.x >
                        collider.min.x
                    ) &&
                    (
                        playerBox.min.x <
                        collider.max.x
                    ) &&
                    (
                        playerBox.max.z >
                        collider.min.z
                    ) &&
                    (
                        playerBox.min.z <
                        collider.max.z
                    );


                if (!horizontalOverlap) {
                    continue;
                }


                /*
                |--------------------------------------------------------------------------
                | PLAYER SUDAH DI ATAS OBJECT
                |--------------------------------------------------------------------------
                |
                | Kalau telapak kaki player sudah berada
                | sedikit di atas permukaan object,
                | jangan anggap sebagai tabrakan samping.
                |
                */

                const surfaceY =
                    collider.max.y;


                if (
                    playerBottom >=
                    surfaceY - 0.05
                ) {

                    continue;

                }


                /*
                |--------------------------------------------------------------------------
                | PLAYER MENABRAK OBJECT
                |--------------------------------------------------------------------------
                */

                if (
                    playerTop >
                    collider.min.y
                ) {

                    return true;

                }

            }


            return false;

        },


        // =========================================================
        // CARI PERMUKAAN ATAS TERDEKAT
        // =========================================================
        //
        // Digunakan untuk:
        //
        // - meja
        // - kursi
        // - rak
        // - object lain
        //
        // =========================================================

        getSurfaceY(
            position,
            radius = 0.35,
            currentY = 0
        ) {

            let highestSurface = 0;


            const playerMinX =
                position.x -
                radius;

            const playerMaxX =
                position.x +
                radius;


            const playerMinZ =
                position.z -
                radius;

            const playerMaxZ =
                position.z +
                radius;


            for (
                const collider
                of system.colliders
            ) {

                /*
                |--------------------------------------------------------------------------
                | CEK HORIZONTAL
                |--------------------------------------------------------------------------
                */

                const overlapsX =
                    playerMaxX >
                    collider.min.x &&
                    playerMinX <
                    collider.max.x;


                const overlapsZ =
                    playerMaxZ >
                    collider.min.z &&
                    playerMinZ <
                    collider.max.z;


                if (
                    !overlapsX ||
                    !overlapsZ
                ) {

                    continue;

                }


                const surfaceY =
                    collider.max.y;


                /*
                |--------------------------------------------------------------------------
                | HANYA PERMUKAAN DI BAWAH PLAYER
                |--------------------------------------------------------------------------
                */

                if (
                    surfaceY <=
                    currentY + 0.25
                ) {

                    highestSurface =
                        Math.max(
                            highestSurface,
                            surfaceY
                        );

                }

            }


            return highestSurface;

        }

    };


    return system;

}