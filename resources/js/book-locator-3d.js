import { createScene, resizeScene } from './book-locator/scene.js';
import { createCamera, applyInitialCamera } from './book-locator/camera.js';
import { buildRackSystem } from './book-locator/rack.js';
import { createPlayer } from './book-locator/player.js';
import { createCollisionSystem } from './book-locator/collision.js';

document.addEventListener('DOMContentLoaded', () => {
    const container = document.getElementById('locator3d');

    if (!container) {
        console.warn('BOOK LOCATOR 3D: #locator3d tidak ditemukan.');
        return;
    }

    const shelves = Array.isArray(window.bookLocatorShelves)
        ? window.bookLocatorShelves
        : [];

    const bookCopies = Array.isArray(window.bookLocatorCopies)
        ? window.bookLocatorCopies
        : [];

    const targetCopyId = Number(window.bookLocatorTargetCopyId || 0);

    console.log('====================================');
    console.log('3D LOCATOR DATA');
    console.log('Jumlah Shelf:', shelves.length);
    console.log('Jumlah BookCopy:', bookCopies.length);
    console.log('Target BookCopy:', targetCopyId);
    console.log('Shelves:', shelves);
    console.log('BookCopies:', bookCopies);
    console.log('====================================');

    container.innerHTML = '';

    const { scene, renderer, locatorGroup } = createScene(container);

    const fullscreenButton =
        document.createElement('button');

    fullscreenButton.type = 'button';
    fullscreenButton.textContent = 'Jelajahi 3D';
    fullscreenButton.className = 'enter-3d-button';

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



    const { camera, orbitControls, fpsControls } = createCamera(container, renderer);

    const rackSystem = buildRackSystem({
        locatorGroup,
        shelves,
        bookCopies,
        targetCopyId
    });

    applyInitialCamera(camera, orbitControls, rackSystem);

    const collision = createCollisionSystem();

    collision.setColliders(
        locatorGroup.children
    );

    // Disiapkan untuk tahap FPS/collision berikutnya, belum diaktifkan.
    const player = createPlayer({
        camera,
        renderer,
        container,
        fpsControls,
        orbitControls,
        collision
    });

    fullscreenButton.addEventListener(
        'click',
        async () => {

            if (!document.fullscreenElement) {

                await container.requestFullscreen();

            }

            player.enterWalkMode();

        }
    );

    console.log(
        'Jumlah collider:',
        collision.colliders.length
    );

    function resize() {
        resizeScene(container, camera, renderer);
    }

    window.addEventListener('resize', resize);

    const resizeObserver = new ResizeObserver(() => resize());
    resizeObserver.observe(container);

    let lastTime = performance.now();

    function animate(now = performance.now()) {
        requestAnimationFrame(animate);

        const delta = Math.min(
            (now - lastTime) / 1000,
            0.05
        );

        lastTime = now;

        if (orbitControls.enabled) {
            orbitControls.update();
        }

        player.update(delta);

        renderer.render(scene, camera);
    }

    animate();

    console.log('====================================');
    console.log('3D LOCATOR BERHASIL');
    console.log('Total rak fisik:', rackSystem.physicalRackList.length);
    console.log(
        'Rak:',
        rackSystem.physicalRackList.map(([code, shelfList]) => ({
            code,
            shelves: shelfList.map(shelf => shelf.code)
        }))
    );
    console.log('Total book copy:', rackSystem.normalizedCopies.length);
    console.log('Target:', targetCopyId);
    console.log('FPS player prepared:', player.enabled === false);
    console.log('Collision prepared:', collision.enabled === false);
    console.log('====================================');
});
