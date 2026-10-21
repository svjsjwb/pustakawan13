import * as THREE from 'three';
import { CONFIG } from './config.js';


export function createScene(container) {

    const scene =
        new THREE.Scene();


    scene.background =
        new THREE.Color(
            CONFIG.backgroundColor
        );


    /* =========================================================
       RENDERER
    ========================================================== */

    const renderer =
        new THREE.WebGLRenderer({
            antialias: true
        });


    renderer.setPixelRatio(
        Math.min(
            window.devicePixelRatio,
            2
        )
    );


    renderer.setSize(
        container.clientWidth,
        container.clientHeight
    );


    renderer.shadowMap.enabled =
        true;


    renderer.shadowMap.type =
        THREE.PCFSoftShadowMap;


    container.appendChild(
        renderer.domElement
    );


    /* =========================================================
       LIGHT
    ========================================================== */

    const ambientLight =
        new THREE.HemisphereLight(
            0xffffff,
            0xd9d9d9,
            2.2
        );


    scene.add(
        ambientLight
    );


    const directionalLight =
        new THREE.DirectionalLight(
            0xffffff,
            3
        );


    directionalLight.position.set(
        8,
        15,
        12
    );


    directionalLight.castShadow =
        true;


    directionalLight.shadow.mapSize.width =
        2048;


    directionalLight.shadow.mapSize.height =
        2048;


    scene.add(
        directionalLight
    );


    const fillLight =
        new THREE.DirectionalLight(
            0xffffff,
            1.2
        );


    fillLight.position.set(
        -10,
        8,
        -10
    );


    scene.add(
        fillLight
    );


    /* =========================================================
       FLOOR
    ========================================================== */

    const floor =
        new THREE.Mesh(

            new THREE.PlaneGeometry(
                100,
                100
            ),

            new THREE.MeshStandardMaterial({
                color:
                    CONFIG.floorColor,

                roughness:
                    0.9
            })

        );


    floor.rotation.x =
        -Math.PI / 2;


    floor.receiveShadow =
        true;


    scene.add(
        floor
    );


    /* =========================================================
       GRID
    ========================================================== */

    const grid =
        new THREE.GridHelper(
            90,
            45,
            0xd0d0d0,
            0xe2e2e2
        );


    grid.position.y =
        0.01;


    scene.add(
        grid
    );


    /* =========================================================
       RAK / LOCATOR GROUP
    ========================================================== */

    const locatorGroup =
        new THREE.Group();


    locatorGroup.name =
        'locator-racks';


    locatorGroup.rotation.set(
        0,
        0,
        0
    );


    scene.add(
        locatorGroup
    );


    /* =========================================================
       NAVIGATION GROUP
    ========================================================== */

    /*
     * Path dipisahkan dari rack.
     *
     * Jadi:
     *
     * locatorGroup
     *     ↓
     *     Rak + buku
     *
     * navigationGroup
     *     ↓
     *     Path + marker
     *
     * Navigation TIDAK akan menjadi collider.
     */

    const navigationGroup =
        new THREE.Group();


    navigationGroup.name =
        'navigation';


    navigationGroup.rotation.set(
        0,
        0,
        0
    );


    scene.add(
        navigationGroup
    );


    return {

        scene,

        renderer,

        locatorGroup,

        navigationGroup

    };

}


export function resizeScene(
    container,
    camera,
    renderer
) {

    const width =
        container.clientWidth;


    const height =
        container.clientHeight;


    if (
        width <= 0 ||
        height <= 0
    ) {

        return;

    }


    camera.aspect =
        width / height;


    camera.updateProjectionMatrix();


    renderer.setSize(
        width,
        height
    );

}