import * as THREE from 'three';
import { OrbitControls } from 'three/addons/controls/OrbitControls.js';
import { PointerLockControls } from 'three/addons/controls/PointerLockControls.js';
import { CONFIG } from './config.js';

export function createCamera(container, renderer) {
    const camera = new THREE.PerspectiveCamera(
        42,
        container.clientWidth / Math.max(container.clientHeight, 1),
        0.1,
        1000
    );

    // FREECAM / ORBITAL — tetap dipertahankan.
    const orbitControls = new OrbitControls(
        camera,
        renderer.domElement
    );

    orbitControls.enableDamping = true;
    orbitControls.dampingFactor = 0.08;
    orbitControls.enablePan = true;
    orbitControls.enableZoom = true;
    orbitControls.minDistance = 5;
    orbitControls.maxDistance = 70;
    orbitControls.maxPolarAngle = Math.PI * 0.82;
    orbitControls.minPolarAngle = Math.PI * 0.12;
    orbitControls.enabled = true;

    // FPS / WALK MODE.
    const fpsControls = new PointerLockControls(
        camera,
        renderer.domElement
    );

    fpsControls.enabled = false;

    return {
        camera,
        orbitControls,
        fpsControls
    };
}

export function applyInitialCamera(
    camera,
    orbitControls,
    view
) {
    const {
        cameraTargetX,
        cameraTargetY,
        cameraTargetZ,
        targetSide
    } = view;

    const viewTargetY =
        cameraTargetY - 4.0;

    orbitControls.target.set(
        cameraTargetX,
        viewTargetY,
        cameraTargetZ
    );

    const cameraOffset =
        CONFIG.cameraDistance;

    if (targetSide !== 'back') {

        camera.position.set(
            cameraTargetX,
            cameraTargetY +
            CONFIG.rackHeight * 0.10,
            cameraTargetZ +
            cameraOffset
        );

    } else {

        camera.position.set(
            cameraTargetX,
            cameraTargetY +
            CONFIG.rackHeight * 0.10,
            cameraTargetZ -
            cameraOffset
        );
    }

    orbitControls.update();
}
