/*
|--------------------------------------------------------------------------
| BOOK LOCATOR 3D
|--------------------------------------------------------------------------
|
| STRUKTUR:
|
| RAK A
|   A-01 = kiri 30 kolom
|   A-02 = kanan 30 kolom
|
| RAK B
|   B-01 = kiri 30 kolom
|   B-02 = kanan 30 kolom
|
| RAK C
|   C-01 = kiri 30 kolom
|   C-02 = kanan 30 kolom
|
| SETIAP RAK:
|
|   3 BARIS
|   60 KOLOM
|   2 MUKA
|
| FRONT
| BACK
|
|--------------------------------------------------------------------------
*/

import * as THREE from 'three';

import {
    OrbitControls
} from 'three/addons/controls/OrbitControls.js';


document.addEventListener('DOMContentLoaded', () => {

    /*
    |--------------------------------------------------------------------------
    | CONTAINER
    |--------------------------------------------------------------------------
    */

    const container =
        document.getElementById('locator3d');


    if (!container) {

        console.warn(
            'BOOK LOCATOR 3D: #locator3d tidak ditemukan.'
        );

        return;

    }


    /*
    |--------------------------------------------------------------------------
    | DATA LARAVEL
    |--------------------------------------------------------------------------
    */

    const shelves =
        Array.isArray(window.bookLocatorShelves)
            ? window.bookLocatorShelves
            : [];


    const bookCopies =
        Array.isArray(window.bookLocatorCopies)
            ? window.bookLocatorCopies
            : [];


    const targetCopyId =
        Number(
            window.bookLocatorTargetCopyId || 0
        );


    console.log(
        '===================================='
    );

    console.log(
        '3D LOCATOR DATA'
    );

    console.log(
        'Jumlah Shelf:',
        shelves.length
    );

    console.log(
        'Jumlah BookCopy:',
        bookCopies.length
    );

    console.log(
        'Target BookCopy:',
        targetCopyId
    );

    console.log(
        'Shelves:',
        shelves
    );

    console.log(
        'BookCopies:',
        bookCopies
    );

    console.log(
        '===================================='
    );


    /*
    |--------------------------------------------------------------------------
    | CONFIG
    |--------------------------------------------------------------------------
    */

    const CONFIG = {

        /*
        |--------------------------------------------------------------------------
        | RAK
        |--------------------------------------------------------------------------
        */

        rackWidth: 18,

        rackHeight: 5.4,

        rackDepth: 2.0,

        rackThickness: 0.16,

        rackSideThickness: 0.22,

        dividerThickness: 0.20,

        rowCount: 3,

        columnCount: 60,

        columnsPerSide: 30,


        /*
        |--------------------------------------------------------------------------
        | BUKU
        |--------------------------------------------------------------------------
        */

        bookWidth: 0.24,

        bookHeight: 1.15,

        bookDepth: 0.48,

        bookGap: 0.035,


        /*
        |--------------------------------------------------------------------------
        | JARAK RAK
        |--------------------------------------------------------------------------
        */

        rackSpacingZ: 9,

        cameraDistance: 22,


        /*
        |--------------------------------------------------------------------------
        | POSISI MUKA RAK
        |--------------------------------------------------------------------------
        */

        frontZ: 0.73,

        backZ: -0.73,


        /*
        |--------------------------------------------------------------------------
        | WARNA
        |--------------------------------------------------------------------------
        */

        rackColor: 0x76583c,

        shelfColor: 0x8b6a4a,

        bookColor: 0xe7e2d7,

        bookSideColor: 0xd6d0c5,

        targetColor: 0xffcc00,

        targetEdgeColor: 0xff3300,

        floorColor: 0xf4f4f4,

        backgroundColor: 0xf1f1f1

    };


    /*
    |--------------------------------------------------------------------------
    | CLEAR
    |--------------------------------------------------------------------------
    */

    container.innerHTML = '';


    /*
    |--------------------------------------------------------------------------
    | SCENE
    |--------------------------------------------------------------------------
    */

    const scene =
        new THREE.Scene();


    scene.background =
        new THREE.Color(
            CONFIG.backgroundColor
        );


    /*
    |--------------------------------------------------------------------------
    | CAMERA
    |--------------------------------------------------------------------------
    */

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


    /*
    |--------------------------------------------------------------------------
    | RENDERER
    |--------------------------------------------------------------------------
    */

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


    /*
    |--------------------------------------------------------------------------
    | ORBIT CONTROLS
    |--------------------------------------------------------------------------
    |
    | YANG BERGERAK = CAMERA
    |
    | RAK TIDAK DIPUTAR.
    |--------------------------------------------------------------------------
    */

    const controls =
        new OrbitControls(
            camera,
            renderer.domElement
        );


    controls.enableDamping =
        true;


    controls.dampingFactor =
        0.08;


    controls.enablePan =
        true;


    controls.enableZoom =
        true;


    controls.minDistance =
        5;


    controls.maxDistance =
        70;


    controls.maxPolarAngle =
        Math.PI * 0.82;


    controls.minPolarAngle =
        Math.PI * 0.12;


    /*
    |--------------------------------------------------------------------------
    | LIGHT
    |--------------------------------------------------------------------------
    */

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


    /*
    |--------------------------------------------------------------------------
    | FLOOR
    |--------------------------------------------------------------------------
    */

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


    /*
    |--------------------------------------------------------------------------
    | GRID
    |--------------------------------------------------------------------------
    */

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


    /*
    |--------------------------------------------------------------------------
    | GROUP UTAMA
    |--------------------------------------------------------------------------
    */

    const locatorGroup =
        new THREE.Group();


    /*
    |--------------------------------------------------------------------------
    | RAK TIDAK DIPUTAR
    |--------------------------------------------------------------------------
    */

    locatorGroup.rotation.set(
        0,
        0,
        0
    );


    scene.add(
        locatorGroup
    );


    /*
    |--------------------------------------------------------------------------
    | MATERIAL
    |--------------------------------------------------------------------------
    */

    const rackMaterial =
        new THREE.MeshStandardMaterial({

            color:
                CONFIG.rackColor,

            roughness:
                0.72

        });


    const shelfMaterial =
        new THREE.MeshStandardMaterial({

            color:
                CONFIG.shelfColor,

            roughness:
                0.75

        });


    const bookMaterial =
        new THREE.MeshStandardMaterial({

            color:
                CONFIG.bookColor,

            roughness:
                0.82

        });


    const bookSideMaterial =
        new THREE.MeshStandardMaterial({

            color:
                CONFIG.bookSideColor,

            roughness:
                0.85

        });


    const targetMaterial =
        new THREE.MeshStandardMaterial({

            color:
                CONFIG.targetColor,

            emissive:
                CONFIG.targetColor,

            emissiveIntensity:
                0.35,

            roughness:
                0.5

        });


    /*
    |--------------------------------------------------------------------------
    | BOOK MATERIAL
    |--------------------------------------------------------------------------
    */

    const normalBookMaterials = [

        bookSideMaterial,
        bookSideMaterial,

        bookMaterial,
        bookMaterial,

        bookMaterial,
        bookMaterial

    ];


    /*
    |--------------------------------------------------------------------------
    | CREATE BOX
    |--------------------------------------------------------------------------
    */

    function createBox(
        width,
        height,
        depth,
        material
    ) {

        const geometry =
            new THREE.BoxGeometry(
                width,
                height,
                depth
            );


        const mesh =
            new THREE.Mesh(
                geometry,
                material
            );


        mesh.castShadow =
            true;


        mesh.receiveShadow =
            true;


        return mesh;

    }


    /*
    |--------------------------------------------------------------------------
    | TEXT SPRITE
    |--------------------------------------------------------------------------
    */

    function createTextSprite(
        text
    ) {

        const canvas =
            document.createElement(
                'canvas'
            );


        canvas.width =
            512;


        canvas.height =
            128;


        const ctx =
            canvas.getContext(
                '2d'
            );


        ctx.clearRect(
            0,
            0,
            canvas.width,
            canvas.height
        );


        ctx.fillStyle =
            '#ffffff';


        ctx.strokeStyle =
            '#9b846b';


        ctx.lineWidth =
            6;


        roundRect(
            ctx,
            8,
            8,
            496,
            112,
            18
        );


        ctx.fill();


        ctx.stroke();


        ctx.fillStyle =
            '#4d4033';


        ctx.font =
            'bold 42px Arial';


        ctx.textAlign =
            'center';


        ctx.textBaseline =
            'middle';


        ctx.fillText(
            text,
            canvas.width / 2,
            canvas.height / 2
        );


        const texture =
            new THREE.CanvasTexture(
                canvas
            );


        texture.needsUpdate =
            true;


        const material =
            new THREE.SpriteMaterial({

                map:
                    texture,

                transparent:
                    true

            });


        const sprite =
            new THREE.Sprite(
                material
            );


        sprite.scale.set(
            3.8,
            0.95,
            1
        );


        return sprite;

    }


    function roundRect(
        ctx,
        x,
        y,
        width,
        height,
        radius
    ) {

        ctx.beginPath();


        ctx.moveTo(
            x + radius,
            y
        );


        ctx.lineTo(
            x + width - radius,
            y
        );


        ctx.quadraticCurveTo(
            x + width,
            y,
            x + width,
            y + radius
        );


        ctx.lineTo(
            x + width,
            y + height - radius
        );


        ctx.quadraticCurveTo(
            x + width,
            y + height,
            x + width - radius,
            y + height
        );


        ctx.lineTo(
            x + radius,
            y + height
        );


        ctx.quadraticCurveTo(
            x,
            y + height,
            x,
            y + height - radius
        );


        ctx.lineTo(
            x,
            y + radius
        );


        ctx.quadraticCurveTo(
            x,
            y,
            x + radius,
            y
        );


        ctx.closePath();

    }


    /*
    |--------------------------------------------------------------------------
    | NORMALIZE SHELF
    |--------------------------------------------------------------------------
    */

    function normalizeShelf(
        shelf,
        index
    ) {

        return {

            ...shelf,

            id:
                Number(
                    shelf.id ??
                    shelf.shelf_id ??
                    index + 1
                ),

            code:
                String(
                    shelf.code ??
                    shelf.name ??
                    `RAK-${index + 1}`
                ),

            row_count:
                Math.max(
                    1,
                    Number(
                        shelf.row_count ??
                        CONFIG.rowCount
                    )
                ),

            column_count:
                Math.max(
                    1,
                    Number(
                        shelf.column_count ??
                        30
                    )
                )

        };

    }


    const normalizedShelves =
        shelves.map(
            normalizeShelf
        );


    /*
    |--------------------------------------------------------------------------
    | NORMALIZE COPY
    |--------------------------------------------------------------------------
    */

    function normalizeCopy(copy) {

        return {

            ...copy,

            id: Number(
                copy.id
            ),

            book_id: Number(
                copy.book_id ?? 0
            ),

            shelf_id: Number(
                copy.shelf_id ??
                copy.shelfId ??
                0
            ),

            row: Number(
                copy.row ??
                copy.row_number ??
                1
            ),

            column: Number(
                copy.column ??
                copy.column_number ??
                1
            ),

            side:
                String(
                    copy.side ??
                    'front'
                ).toLowerCase()

        };

    }


    const normalizedCopies =
        bookCopies.map(
            normalizeCopy
        );


    /*
    |--------------------------------------------------------------------------
    | TARGET
    |--------------------------------------------------------------------------
    */

    const targetCopy =
        normalizedCopies.find(
            copy =>
                Number(copy.id) ===
                targetCopyId
        );


    /*
    |--------------------------------------------------------------------------
    | GROUP PHYSICAL RACK
    |--------------------------------------------------------------------------
    */

    const physicalRacks =
        new Map();


    normalizedShelves.forEach(
        shelf => {

            const code =
                String(
                    shelf.code
                )
                    .trim()
                    .toUpperCase();


            const rackCode =
                code.includes('-')
                    ? code.split('-')[0]
                    : code.charAt(0);


            if (
                !physicalRacks.has(
                    rackCode
                )
            ) {

                physicalRacks.set(
                    rackCode,
                    []
                );

            }


            physicalRacks
                .get(rackCode)
                .push(shelf);

        }
    );


    /*
    |--------------------------------------------------------------------------
    | SORT SHELF
    |--------------------------------------------------------------------------
    */

    physicalRacks.forEach(
        shelfList => {

            shelfList.sort(
                (a, b) =>
                    String(a.code)
                        .localeCompare(
                            String(b.code)
                        )
            );

        }
    );


    const physicalRackList =
        Array.from(
            physicalRacks.entries()
        )
            .sort(
                (a, b) =>
                    a[0].localeCompare(
                        b[0]
                    )
            );


    /*
    |--------------------------------------------------------------------------
    | POSISI RAK Z
    |--------------------------------------------------------------------------
    |
    | CAMERA
    |   ↓
    | RAK A
    |   ↓
    | RAK B
    |   ↓
    | RAK C
    |--------------------------------------------------------------------------
    */

    function getRackZ(
        index
    ) {

        const total =
            physicalRackList.length;


        const spacing =
            CONFIG.rackDepth +
            CONFIG.rackSpacingZ;


        const centerOffset =
            (
                total - 1
            ) / 2;


        /*
        | index 0 = Rak A
        | index 1 = Rak B
        | index 2 = Rak C
        |
        | Rak A dibuat paling dekat kamera
        */

        return (
            (
                centerOffset -
                index
            ) *
            spacing
        );

    }


    /*
    |--------------------------------------------------------------------------
    | POSISI X BUKU
    |--------------------------------------------------------------------------
    |
    | 1 - 30  = KIRI
    | 31 - 60 = KANAN
    |--------------------------------------------------------------------------
    */

    function getBookX(
        physicalColumn
    ) {

        const column =
            Math.max(
                1,
                Math.min(
                    60,
                    Number(
                        physicalColumn
                    )
                )
            );


        const halfWidth =
            (
                CONFIG.rackWidth -
                CONFIG.dividerThickness
            ) / 2;


        const usableWidth =
            halfWidth -
            0.45;


        const spacing =
            usableWidth /
            30;


        /*
        |--------------------------------------------------------------------------
        | KIRI
        |--------------------------------------------------------------------------
        */

        if (
            column <= 30
        ) {

            const local =
                column - 1;


            const leftStart =
                -CONFIG.rackWidth / 2 +
                0.30;


            return (
                leftStart +
                (
                    local + 0.5
                ) *
                spacing
            );

        }


        /*
        |--------------------------------------------------------------------------
        | KANAN
        |--------------------------------------------------------------------------
        */

        const local =
            column - 31;


        const rightStart =
            0.30;


        return (
            rightStart +
            (
                local + 0.5
            ) *
            spacing
        );

    }


    /*
    |--------------------------------------------------------------------------
    | POSISI Y
    |--------------------------------------------------------------------------
    |
    | ROW 1 = ATAS
    | ROW 2 = TENGAH
    | ROW 3 = BAWAH
    |--------------------------------------------------------------------------
    */

    function getBookY(
        row
    ) {

        const safeRow =
            Math.max(
                1,
                Math.min(
                    3,
                    Number(row)
                )
            );


        const rowHeight =
            CONFIG.rackHeight /
            3;


        return (
            CONFIG.rackHeight -
            (
                safeRow - 0.5
            ) *
            rowHeight
        );

    }


    /*
    |--------------------------------------------------------------------------
    | SHELF → PHYSICAL COLUMN
    |--------------------------------------------------------------------------
    */

    function getPhysicalColumn(
        shelf,
        column
    ) {

        const safeColumn =
            Math.max(
                1,
                Math.min(
                    30,
                    Number(column)
                )
            );


        const code =
            String(
                shelf.code
            )
                .trim()
                .toUpperCase();


        /*
        |--------------------------------------------------------------------------
        | -02 = KANAN
        |--------------------------------------------------------------------------
        */

        if (
            code.endsWith('-02')
        ) {

            return (
                safeColumn +
                30
            );

        }


        /*
        |--------------------------------------------------------------------------
        | -01 = KIRI
        |--------------------------------------------------------------------------
        */

        return safeColumn;

    }


    /*
    |--------------------------------------------------------------------------
    | CREATE BOOK
    |--------------------------------------------------------------------------
    */

    function createBook(
        copy,
        physicalColumn,
        side,
        isDummy = false
    ) {

        const isTarget =
            !isDummy &&
            Number(copy.id) ===
            targetCopyId;


        const material =
            isTarget
                ? targetMaterial
                : normalBookMaterials;


        const book =
            createBox(

                CONFIG.bookWidth,

                CONFIG.bookHeight,

                CONFIG.bookDepth,

                material

            );


        /*
        |--------------------------------------------------------------------------
        | X
        |--------------------------------------------------------------------------
        */

        book.position.x =
            getBookX(
                physicalColumn
            );


        /*
        |--------------------------------------------------------------------------
        | Y
        |--------------------------------------------------------------------------
        */

        book.position.y =
            getBookY(
                copy.row
            );


        /*
        |--------------------------------------------------------------------------
        | Z
        |--------------------------------------------------------------------------
        |
        | FRONT = +Z
        | BACK  = -Z
        |--------------------------------------------------------------------------
        */

        if (
            side === 'front'
        ) {

            book.position.z =
                CONFIG.frontZ;

        } else {

            book.position.z =
                CONFIG.backZ;


            /*
            | Buku belakang menghadap
            | arah berlawanan.
            */

            book.rotation.y =
                Math.PI;

        }


        /*
        |--------------------------------------------------------------------------
        | USER DATA
        |--------------------------------------------------------------------------
        */

        book.userData = {

            type: 'book',

            copyId:
                isDummy
                    ? null
                    : Number(copy.id),

            shelfId:
                Number(copy.shelf_id),

            row:
                Number(copy.row),

            column:
                Number(copy.column),

            physicalColumn,

            side,

            target:
                isTarget

        };


        /*
        |--------------------------------------------------------------------------
        | TARGET OUTLINE
        |--------------------------------------------------------------------------
        */

        let outline =
            null;


        /*
        | HANYA TARGET ASLI.
        |
        | Dummy tidak pernah di-highlight.
        */

        if (
            isTarget
        ) {

            const outlineGeometry =
                new THREE.BoxGeometry(

                    CONFIG.bookWidth + 0.07,

                    CONFIG.bookHeight + 0.07,

                    CONFIG.bookDepth + 0.07

                );


            const outlineMaterial =
                new THREE.MeshBasicMaterial({

                    color:
                        CONFIG.targetEdgeColor,

                    wireframe:
                        true

                });


            outline =
                new THREE.Mesh(
                    outlineGeometry,
                    outlineMaterial
                );


            outline.position.copy(
                book.position
            );


            outline.rotation.copy(
                book.rotation
            );


            outline.userData = {

                type:
                    'target-outline',

                copyId:
                    Number(copy.id),

                side

            };

        }


        return {

            book,

            outline

        };

    }


    /*
    |--------------------------------------------------------------------------
    | BUAT BUKU SATU MUKA
    |--------------------------------------------------------------------------
    */

    function createBooksForSide(
        rackGroup,
        rackShelves,
        side
    ) {

        rackShelves.forEach(
            shelf => {

                const shelfId =
                    Number(
                        shelf.id
                    );


                /*
                |--------------------------------------------------------------------------
                | AMBIL COPY BERDASARKAN SHELF + SIDE
                |--------------------------------------------------------------------------
                |
                | FRONT hanya menampilkan buku FRONT.
                | BACK hanya menampilkan buku BACK.
                |--------------------------------------------------------------------------
                */

                const copies =
                    normalizedCopies.filter(
                        copy =>

                            Number(
                                copy.shelf_id
                            ) === shelfId

                            &&

                            String(
                                copy.side ??
                                'front'
                            ).toLowerCase()
                            === side

                    );


                /*
                |--------------------------------------------------------------------------
                | MAP POSISI
                |--------------------------------------------------------------------------
                */

                const copiesByPosition =
                    new Map();


                copies.forEach(
                    copy => {

                        const key =
                            `${copy.row}-${copy.column}`;


                        copiesByPosition.set(
                            key,
                            copy
                        );

                    }
                );


                /*
                |--------------------------------------------------------------------------
                | 3 BARIS
                |--------------------------------------------------------------------------
                */

                for (
                    let row = 1;
                    row <= 3;
                    row++
                ) {


                    /*
                    |--------------------------------------------------------------------------
                    | 30 KOLOM
                    |--------------------------------------------------------------------------
                    */

                    for (
                        let column = 1;
                        column <= 30;
                        column++
                    ) {

                        const key =
                            `${row}-${column}`;


                        const realCopy =
                            copiesByPosition.get(
                                key
                            );


                        /*
                        |--------------------------------------------------------------------------
                        | PHYSICAL COLUMN
                        |--------------------------------------------------------------------------
                        */

                        const physicalColumn =
                            getPhysicalColumn(
                                shelf,
                                column
                            );


                        /*
                        |--------------------------------------------------------------------------
                        | ADA BUKU
                        |--------------------------------------------------------------------------
                        */

                        if (
                            realCopy
                        ) {

                            const result =
                                createBook(

                                    realCopy,

                                    physicalColumn,

                                    side,

                                    false

                                );


                            rackGroup.add(
                                result.book
                            );


                            if (
                                result.outline
                            ) {

                                rackGroup.add(
                                    result.outline
                                );

                            }

                        }


                        /*
                        |--------------------------------------------------------------------------
                        | SLOT KOSONG
                        |--------------------------------------------------------------------------
                        */

                        else {

                            const dummyCopy = {

                                id:
                                    null,

                                shelf_id:
                                    shelfId,

                                row,

                                column,

                                side

                            };


                            const result =
                                createBook(

                                    dummyCopy,

                                    physicalColumn,

                                    side,

                                    true

                                );


                            result.book.material =
                                new THREE.MeshStandardMaterial({

                                    color:
                                        CONFIG.bookColor,

                                    roughness:
                                        0.85,

                                    transparent:
                                        true,

                                    opacity:
                                        0.32

                                });


                            rackGroup.add(
                                result.book
                            );

                        }

                    }

                }

            }
        );

    }


    /*
    |--------------------------------------------------------------------------
    | CREATE PHYSICAL RACK
    |--------------------------------------------------------------------------
    */

    function createPhysicalRack(
        rackCode,
        rackShelves,
        rackIndex
    ) {

        const rackGroup =
            new THREE.Group();


        rackGroup.userData = {

            type:
                'physical-rack',

            rackCode,

            shelfIds:
                rackShelves.map(
                    shelf =>
                        Number(
                            shelf.id
                        )
                )

        };


        /*
        |--------------------------------------------------------------------------
        | POSISI Z
        |--------------------------------------------------------------------------
        */

        rackGroup.position.z =
            getRackZ(
                rackIndex
            );


        /*
        |--------------------------------------------------------------------------
        | DIMENSI
        |--------------------------------------------------------------------------
        */

        const width =
            CONFIG.rackWidth;


        const height =
            CONFIG.rackHeight;


        const depth =
            CONFIG.rackDepth;


        /*
        |--------------------------------------------------------------------------
        | TIANG KIRI
        |--------------------------------------------------------------------------
        */

        const leftPost =
            createBox(

                CONFIG.rackSideThickness,

                height,

                depth,

                rackMaterial

            );


        leftPost.position.set(

            -width / 2,

            height / 2,

            0

        );


        rackGroup.add(
            leftPost
        );


        /*
        |--------------------------------------------------------------------------
        | TIANG KANAN
        |--------------------------------------------------------------------------
        */

        const rightPost =
            createBox(

                CONFIG.rackSideThickness,

                height,

                depth,

                rackMaterial

            );


        rightPost.position.set(

            width / 2,

            height / 2,

            0

        );


        rackGroup.add(
            rightPost
        );


        /*
        |--------------------------------------------------------------------------
        | SEKAT TENGAH
        |--------------------------------------------------------------------------
        */

        const centerPost =
            createBox(

                CONFIG.dividerThickness,

                height,

                depth,

                rackMaterial

            );


        centerPost.position.set(

            0,

            height / 2,

            0

        );


        rackGroup.add(
            centerPost
        );


        /*
        |--------------------------------------------------------------------------
        | FRAME ATAS
        |--------------------------------------------------------------------------
        */

        const topFrame =
            createBox(

                width +
                CONFIG.rackSideThickness,

                CONFIG.rackThickness,

                depth,

                rackMaterial

            );


        topFrame.position.set(

            0,

            height,

            0

        );


        rackGroup.add(
            topFrame
        );


        /*
        |--------------------------------------------------------------------------
        | FRAME BAWAH
        |--------------------------------------------------------------------------
        */

        const bottomFrame =
            createBox(

                width +
                CONFIG.rackSideThickness,

                CONFIG.rackThickness,

                depth,

                rackMaterial

            );


        bottomFrame.position.set(

            0,

            0,

            0

        );


        rackGroup.add(
            bottomFrame
        );


        /*
        |--------------------------------------------------------------------------
        | RAK HORIZONTAL
        |--------------------------------------------------------------------------
        */

        const rowHeight =
            height / 3;


        for (
            let row = 1;
            row < 3;
            row++
        ) {

            const shelfY =
                row *
                rowHeight;


            const shelfBoard =
                createBox(

                    width,

                    CONFIG.rackThickness,

                    depth,

                    shelfMaterial

                );


            shelfBoard.position.set(

                0,

                shelfY,

                0

            );


            rackGroup.add(
                shelfBoard
            );

        }


        /*
        |--------------------------------------------------------------------------
        | LABEL RAK
        |--------------------------------------------------------------------------
        */

        const rackLabel =
            createTextSprite(
                `Rak ${rackCode}`
            );


        rackLabel.position.set(

            0,

            height + 0.9,

            0

        );


        rackGroup.add(
            rackLabel
        );


        /*
        |--------------------------------------------------------------------------
        | LABEL -01
        |--------------------------------------------------------------------------
        */

        const leftLabel =
            createTextSprite(
                `${rackCode}-01`
            );


        leftLabel.scale.set(

            2.4,

            0.6,

            1

        );


        leftLabel.position.set(

            -width / 4,

            height + 0.25,

            0

        );


        rackGroup.add(
            leftLabel
        );


        /*
        |--------------------------------------------------------------------------
        | LABEL -02
        |--------------------------------------------------------------------------
        */

        const rightLabel =
            createTextSprite(
                `${rackCode}-02`
            );


        rightLabel.scale.set(

            2.4,

            0.6,

            1

        );


        rightLabel.position.set(

            width / 4,

            height + 0.25,

            0

        );


        rackGroup.add(
            rightLabel
        );


        /*
        |--------------------------------------------------------------------------
        | BUKU DEPAN
        |--------------------------------------------------------------------------
        */

        createBooksForSide(

            rackGroup,

            rackShelves,

            'front'

        );


        /*
        |--------------------------------------------------------------------------
        | BUKU BELAKANG
        |--------------------------------------------------------------------------
        */

        createBooksForSide(

            rackGroup,

            rackShelves,

            'back'

        );


        /*
        |--------------------------------------------------------------------------
        | ADD
        |--------------------------------------------------------------------------
        */

        locatorGroup.add(
            rackGroup
        );


        console.log(

            `Rak ${rackCode} berhasil dibuat`,

            rackShelves.map(
                shelf =>
                    shelf.code
            )

        );


        return rackGroup;

    }


    /*
    |--------------------------------------------------------------------------
    | BUILD RAK
    |--------------------------------------------------------------------------
    */

    if (
        physicalRackList.length === 0
    ) {

        console.warn(
            'Tidak ada data shelf untuk 3D.'
        );

    } else {

        physicalRackList.forEach(

            (
                [rackCode, rackShelves],
                index
            ) => {

                createPhysicalRack(

                    rackCode,

                    rackShelves,

                    index

                );

            }

        );

    }


    /*
    |--------------------------------------------------------------------------
    | CENTER
    |--------------------------------------------------------------------------
    */

    const box =
        new THREE.Box3().setFromObject(
            locatorGroup
        );


    if (
        !box.isEmpty()
    ) {

        const center =
            box.getCenter(
                new THREE.Vector3()
            );


        locatorGroup.position.x =
            -center.x;


        locatorGroup.position.y =
            -box.min.y;

    }


    /*
|--------------------------------------------------------------------------
| INITIAL CAMERA
|--------------------------------------------------------------------------
|
| KAMERA FOKUS KE BUKU TARGET
|
| Alur:
|
| targetCopy
|     ↓
| targetShelf
|     ↓
| physical rack A/B/C
|     ↓
| row + column
|     ↓
| posisi buku
|     ↓
| orbit target
|
|--------------------------------------------------------------------------
*/

    let cameraTargetX = 0;

    let cameraTargetY =
        CONFIG.rackHeight / 2;

    let cameraTargetZ =
        getRackZ(0);


    /*
    |--------------------------------------------------------------------------
    | CARI TARGET BOOK
    |--------------------------------------------------------------------------
    */

    if (targetCopy) {

        /*
        |--------------------------------------------------------------------------
        | CARI SHELF TARGET
        |--------------------------------------------------------------------------
        */

        const targetShelf =
            normalizedShelves.find(
                shelf =>
                    Number(shelf.id) ===
                    Number(targetCopy.shelf_id)
            );


        if (targetShelf) {

            /*
            |--------------------------------------------------------------------------
            | CARI RAK FISIK
            |--------------------------------------------------------------------------
            */

            const shelfCode =
                String(
                    targetShelf.code
                )
                    .trim()
                    .toUpperCase();


            const targetRackCode =
                shelfCode.includes('-')
                    ? shelfCode.split('-')[0]
                    : shelfCode.charAt(0);


            /*
            |--------------------------------------------------------------------------
            | CARI INDEX RAK
            |--------------------------------------------------------------------------
            */

            const targetRackIndex =
                physicalRackList.findIndex(
                    ([rackCode]) =>
                        rackCode ===
                        targetRackCode
                );


            /*
            |--------------------------------------------------------------------------
            | POSISI Z RAK
            |--------------------------------------------------------------------------
            */

            if (
                targetRackIndex >= 0
            ) {

                cameraTargetZ =
                    getRackZ(
                        targetRackIndex
                    );

            }


            /*
            |--------------------------------------------------------------------------
            | POSISI KOLOM FISIK
            |--------------------------------------------------------------------------
            |
            | DB:
            |
            | A-01 kolom 1-30
            | A-02 kolom 1-30
            |
            | 3D:
            |
            | A-01 → physical 1-30
            | A-02 → physical 31-60
            |
            */

            const physicalColumn =
                getPhysicalColumn(

                    targetShelf,

                    targetCopy.column

                );


            /*
            |--------------------------------------------------------------------------
            | POSISI X BUKU
            |--------------------------------------------------------------------------
            */

            cameraTargetX =
                getBookX(
                    physicalColumn
                );


            /*
            |--------------------------------------------------------------------------
            | POSISI Y BUKU
            |--------------------------------------------------------------------------
            */

            cameraTargetY =
                getBookY(
                    targetCopy.row
                );


            /*
            |--------------------------------------------------------------------------
            | SISI BUKU
            |--------------------------------------------------------------------------
            */

            const targetSide =
                String(
                    targetCopy.side ??
                    'front'
                )
                    .trim()
                    .toLowerCase();


            /*
            |--------------------------------------------------------------------------
            | POSISI DEPAN / BELAKANG
            |--------------------------------------------------------------------------
            */

            if (
                targetSide === 'back'
            ) {

                cameraTargetZ +=
                    CONFIG.backZ;

            } else {

                cameraTargetZ +=
                    CONFIG.frontZ;

            }


            /*
            |--------------------------------------------------------------------------
            | DEBUG
            |--------------------------------------------------------------------------
            */

            console.log(
                '===================================='
            );

            console.log(
                'CAMERA TARGET'
            );

            console.log(
                'Target copy:',
                targetCopy.id
            );

            console.log(
                'Target shelf:',
                targetShelf.code
            );

            console.log(
                'Target rack:',
                targetRackCode
            );

            console.log(
                'Target rack index:',
                targetRackIndex
            );

            console.log(
                'Target row:',
                targetCopy.row
            );

            console.log(
                'Target DB column:',
                targetCopy.column
            );

            console.log(
                'Target physical column:',
                physicalColumn
            );

            console.log(
                'Target side:',
                targetSide
            );

            console.log(
                'Camera target X:',
                cameraTargetX
            );

            console.log(
                'Camera target Y:',
                cameraTargetY
            );

            console.log(
                'Camera target Z:',
                cameraTargetZ
            );

            console.log(
                '===================================='
            );

        }

    }


    /*
    |--------------------------------------------------------------------------
    | ORBIT TARGET
    |--------------------------------------------------------------------------
    |
    | Orbit sekarang mengelilingi BUKU TARGET,
    | bukan tengah rak.
    |
    |--------------------------------------------------------------------------
    */

    const viewTargetY =
        cameraTargetY - 4.0;

    controls.target.set(

        cameraTargetX,

        viewTargetY,

        cameraTargetZ

    );


    /*
    |--------------------------------------------------------------------------
    | CAMERA POSITION
    |--------------------------------------------------------------------------
    |
    | Kamera ditempatkan sedikit di depan target.
    |
    |--------------------------------------------------------------------------
    */

    const targetSide =
        targetCopy
            ? String(
                targetCopy.side ??
                'front'
            )
                .trim()
                .toLowerCase()
            : 'front';


    /*
    |--------------------------------------------------------------------------
    | JARAK CAMERA DARI TARGET
    |--------------------------------------------------------------------------
    */

    const cameraOffset =
        CONFIG.cameraDistance;


    /*
    |--------------------------------------------------------------------------
    | DEPAN
    |--------------------------------------------------------------------------
    */

    if (
        targetSide !== 'back'
    ) {

        camera.position.set(

            cameraTargetX,

            cameraTargetY +
            CONFIG.rackHeight * 0.10,

            cameraTargetZ +
            cameraOffset

        );

    }


    /*
    |--------------------------------------------------------------------------
    | BELAKANG
    |--------------------------------------------------------------------------
    */

    else {

        camera.position.set(

            cameraTargetX,

            cameraTargetY +
            CONFIG.rackHeight * 0.10,

            cameraTargetZ -
            cameraOffset

        );

    }


    /*
    |--------------------------------------------------------------------------
    | AKTIFKAN ORBIT
    |--------------------------------------------------------------------------
    */

    controls.update();


    /*
    |--------------------------------------------------------------------------
    | RESIZE
    |--------------------------------------------------------------------------
    */

    function resize() {

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


    window.addEventListener(
        'resize',
        resize
    );


    /*
    |--------------------------------------------------------------------------
    | RESIZE OBSERVER
    |--------------------------------------------------------------------------
    */

    const resizeObserver =
        new ResizeObserver(
            () => resize()
        );


    resizeObserver.observe(
        container
    );


    /*
    |--------------------------------------------------------------------------
    | ANIMATION
    |--------------------------------------------------------------------------
    */

    function animate() {

        requestAnimationFrame(
            animate
        );


        controls.update();


        renderer.render(
            scene,
            camera
        );

    }


    animate();


    /*
    |--------------------------------------------------------------------------
    | DEBUG
    |--------------------------------------------------------------------------
    */

    console.log(
        '===================================='
    );

    console.log(
        '3D LOCATOR BERHASIL'
    );

    console.log(
        'Total rak fisik:',
        physicalRackList.length
    );

    console.log(
        'Rak:',
        physicalRackList.map(
            ([code, shelfList]) => ({

                code,

                shelves:
                    shelfList.map(
                        shelf =>
                            shelf.code
                    )

            })
        )
    );

    console.log(
        'Total book copy:',
        normalizedCopies.length
    );

    console.log(
        'Target:',
        targetCopyId
    );


    if (
        targetCopy
    ) {

        const targetShelf =
            normalizedShelves.find(
                shelf =>
                    Number(
                        shelf.id
                    ) ===
                    Number(
                        targetCopy.shelf_id
                    )
            );


        if (
            targetShelf
        ) {

            const physicalColumn =
                getPhysicalColumn(

                    targetShelf,

                    targetCopy.column

                );


            console.log(
                'Target shelf:',
                targetShelf.code
            );

            console.log(
                'Target row:',
                targetCopy.row
            );

            console.log(
                'Target DB column:',
                targetCopy.column
            );

            console.log(
                'Target physical column:',
                physicalColumn
            );

            console.log(
                'Target sisi:',
                physicalColumn <= 30
                    ? 'KIRI'
                    : 'KANAN'
            );

            console.log(
                'Target dibuat pada:',
                'FRONT + BACK'
            );

        }

    }


    console.log(
        '===================================='
    );

});