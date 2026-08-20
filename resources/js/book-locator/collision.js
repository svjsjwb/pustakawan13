import * as THREE from 'three';

export function createCollisionSystem() {

    const system = {

        enabled: true,

        colliders: [],


        // ============================================
        // BUAT COLLIDER DARI OBJECT THREE.JS
        // ============================================

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


        // ============================================
        // SET COLLIDERS
        // ============================================

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


        // ============================================
        // CEK POSISI PLAYER
        // ============================================

        check(
            position,
            radius = 0.35,
            height = 4.1
        ) {

            const playerBox =
                new THREE.Box3(

                    new THREE.Vector3(
                        position.x - radius,
                        position.y,
                        position.z - radius
                    ),

                    new THREE.Vector3(
                        position.x + radius,
                        position.y + height,
                        position.z + radius
                    )

                );


            for (
                const collider
                of system.colliders
            ) {

                if (
                    playerBox.intersectsBox(
                        collider
                    )
                ) {

                    return true;

                }

            }


            return false;
        }

    };


    return system;
}