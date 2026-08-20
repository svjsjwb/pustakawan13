import * as THREE from 'three';
import { CONFIG } from './config.js';

export function buildRackSystem({ locatorGroup, shelves, bookCopies, targetCopyId }) {
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



    let cameraTargetX = 0;
    let cameraTargetY = CONFIG.rackHeight / 2;
    let cameraTargetZ = getRackZ(0);
    let targetSide = 'front';

    if (targetCopy) {
        const targetShelf = normalizedShelves.find(
            shelf => Number(shelf.id) === Number(targetCopy.shelf_id)
        );

        if (targetShelf) {
            const shelfCode = String(targetShelf.code).trim().toUpperCase();
            const targetRackCode = shelfCode.includes('-')
                ? shelfCode.split('-')[0]
                : shelfCode.charAt(0);

            const targetRackIndex = physicalRackList.findIndex(
                ([rackCode]) => rackCode === targetRackCode
            );

            if (targetRackIndex >= 0) {
                cameraTargetZ = getRackZ(targetRackIndex);
            }

            const physicalColumn = getPhysicalColumn(
                targetShelf,
                targetCopy.column
            );

            cameraTargetX = getBookX(physicalColumn);
            cameraTargetY = getBookY(targetCopy.row);

            targetSide = String(
                targetCopy.side ?? 'front'
            ).trim().toLowerCase();

            cameraTargetZ += targetSide === 'back'
                ? CONFIG.backZ
                : CONFIG.frontZ;

            console.log('====================================');
            console.log('CAMERA TARGET');
            console.log('Target copy:', targetCopy.id);
            console.log('Target shelf:', targetShelf.code);
            console.log('Target rack:', targetRackCode);
            console.log('Target rack index:', targetRackIndex);
            console.log('Target row:', targetCopy.row);
            console.log('Target DB column:', targetCopy.column);
            console.log('Target physical column:', physicalColumn);
            console.log('Target side:', targetSide);
            console.log('Camera target X:', cameraTargetX);
            console.log('Camera target Y:', cameraTargetY);
            console.log('Camera target Z:', cameraTargetZ);
            console.log('====================================');
        }
    }

    return {
        locatorGroup,
        normalizedShelves,
        normalizedCopies,
        targetCopy,
        physicalRackList,
        cameraTargetX,
        cameraTargetY,
        cameraTargetZ,
        targetSide,
        getRackZ,
        getBookX,
        getBookY,
        getPhysicalColumn
    };
}
