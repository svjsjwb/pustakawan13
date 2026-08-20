import * as THREE from 'three';
import { CONFIG } from './config.js';

export function createScene(container) {
    const scene = new THREE.Scene();
    scene.background = new THREE.Color(CONFIG.backgroundColor);

    const renderer = new THREE.WebGLRenderer({ antialias: true });
    renderer.setPixelRatio(Math.min(window.devicePixelRatio, 2));
    renderer.setSize(container.clientWidth, container.clientHeight);
    renderer.shadowMap.enabled = true;
    renderer.shadowMap.type = THREE.PCFSoftShadowMap;
    container.appendChild(renderer.domElement);

    const ambientLight = new THREE.HemisphereLight(0xffffff, 0xd9d9d9, 2.2);
    scene.add(ambientLight);

    const directionalLight = new THREE.DirectionalLight(0xffffff, 3);
    directionalLight.position.set(8, 15, 12);
    directionalLight.castShadow = true;
    directionalLight.shadow.mapSize.width = 2048;
    directionalLight.shadow.mapSize.height = 2048;
    scene.add(directionalLight);

    const fillLight = new THREE.DirectionalLight(0xffffff, 1.2);
    fillLight.position.set(-10, 8, -10);
    scene.add(fillLight);

    const floor = new THREE.Mesh(
        new THREE.PlaneGeometry(100, 100),
        new THREE.MeshStandardMaterial({
            color: CONFIG.floorColor,
            roughness: 0.9
        })
    );
    floor.rotation.x = -Math.PI / 2;
    floor.receiveShadow = true;
    scene.add(floor);

    const grid = new THREE.GridHelper(90, 45, 0xd0d0d0, 0xe2e2e2);
    grid.position.y = 0.01;
    scene.add(grid);

    const locatorGroup = new THREE.Group();
    locatorGroup.rotation.set(0, 0, 0);
    scene.add(locatorGroup);

    return { scene, renderer, locatorGroup };
}

export function resizeScene(container, camera, renderer) {
    const width = container.clientWidth;
    const height = container.clientHeight;
    if (width <= 0 || height <= 0) return;
    camera.aspect = width / height;
    camera.updateProjectionMatrix();
    renderer.setSize(width, height);
}
