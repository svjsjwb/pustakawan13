import * as THREE from 'three';


export function createFurnitureSystem({
    locatorGroup,
    rackGroup,
    rackWidth,
    rackDepth
}) {

    const furnitureGroup =
        new THREE.Group();

    furnitureGroup.name =
        'FurnitureGroup';


    /*
    |--------------------------------------------------------------------------
    | MATERIAL
    |--------------------------------------------------------------------------
    */

    const tableMaterial =
        new THREE.MeshStandardMaterial({
            color: 0x8b6a4a,
            roughness: 0.75
        });


    const chairMaterial =
        new THREE.MeshStandardMaterial({
            color: 0x6b5038,
            roughness: 0.8
        });


    /*
    |--------------------------------------------------------------------------
    | HELPER BOX
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

        mesh.castShadow = true;
        mesh.receiveShadow = true;

        return mesh;
    }


    /*
    |--------------------------------------------------------------------------
    | MEJA
    |--------------------------------------------------------------------------
    */

    function createTable() {

        const table =
            new THREE.Group();

        table.name =
            'StudyTable';


        /*
        |--------------------------------------------------------------------------
        | INTERACTION DATA
        |--------------------------------------------------------------------------
        */

        table.userData.interactable =
            true;

        table.userData.interactionType =
            'table';


        const width = 7.5;
        const depth = 2.8;
        const height = 2.4;

        const topThickness = 0.18;


        /*
        |--------------------------------------------------------------------------
        | TOP
        |--------------------------------------------------------------------------
        */

        const top =
            createBox(
                width,
                topThickness,
                depth,
                tableMaterial
            );

        top.position.y =
            height;

        table.add(top);


        /*
        |--------------------------------------------------------------------------
        | KAKI MEJA
        |--------------------------------------------------------------------------
        */

        const legWidth = 0.18;
        const legDepth = 0.18;

        const legY =
            height / 2;


        const legPositions = [

            [
                -width / 2 + 0.35,
                legY,
                -depth / 2 + 0.35
            ],

            [
                width / 2 - 0.35,
                legY,
                -depth / 2 + 0.35
            ],

            [
                -width / 2 + 0.35,
                legY,
                depth / 2 - 0.35
            ],

            [
                width / 2 - 0.35,
                legY,
                depth / 2 - 0.35
            ]

        ];


        legPositions.forEach(
            ([x, y, z]) => {

                const leg =
                    createBox(
                        legWidth,
                        height,
                        legDepth,
                        tableMaterial
                    );

                leg.position.set(
                    x,
                    y,
                    z
                );

                table.add(leg);

            }
        );


        return table;
    }


    /*
    |--------------------------------------------------------------------------
    | KURSI
    |--------------------------------------------------------------------------
    */

    function createChair() {

        const chair =
            new THREE.Group();

        chair.name =
            'StudyChair';


        /*
        |--------------------------------------------------------------------------
        | INTERACTION DATA
        |--------------------------------------------------------------------------
        |
        | Kursi sekarang bisa dideteksi oleh
        | interaction.js.
        |
        */

        chair.userData.interactable =
            true;

        chair.userData.interactionType =
            'sit';

        chair.userData.seatHeight =
            1.25;


        /*
        |--------------------------------------------------------------------------
        | UKURAN KURSI
        |--------------------------------------------------------------------------
        */

        const seatWidth = 0.85;
        const seatDepth = 0.85;
        const seatHeight = 1.25;


        /*
        |--------------------------------------------------------------------------
        | DATA POSISI DUDUK
        |--------------------------------------------------------------------------
        |
        | Player akan ditempatkan sedikit
        | di atas seat.
        |
        */

        chair.userData.seatHeight =
            seatHeight;


        /*
        | Offset duduk dari pusat kursi.
        |
        | Nilai positif Z = ke arah belakang
        | kursi lokal.
        |
        | Kita tidak perlu hard-code
        | posisi dunia.
        */

        chair.userData.sitOffset =
            new THREE.Vector3(
                0,
                0,
                0
            );


        /*
        |--------------------------------------------------------------------------
        | SEAT
        |--------------------------------------------------------------------------
        */

        const seat =
            createBox(
                seatWidth,
                0.16,
                seatDepth,
                chairMaterial
            );

        seat.position.y =
            seatHeight;

        chair.add(seat);


        /*
        |--------------------------------------------------------------------------
        | KAKI
        |--------------------------------------------------------------------------
        */

        const legSize = 0.12;


        const legPositions = [

            [
                -seatWidth / 2 + 0.12,
                seatHeight / 2,
                -seatDepth / 2 + 0.12
            ],

            [
                seatWidth / 2 - 0.12,
                seatHeight / 2,
                -seatDepth / 2 + 0.12
            ],

            [
                -seatWidth / 2 + 0.12,
                seatHeight / 2,
                seatDepth / 2 - 0.12
            ],

            [
                seatWidth / 2 - 0.12,
                seatHeight / 2,
                seatDepth / 2 - 0.12
            ]

        ];


        legPositions.forEach(
            ([x, y, z]) => {

                const leg =
                    createBox(
                        legSize,
                        seatHeight,
                        legSize,
                        chairMaterial
                    );

                leg.position.set(
                    x,
                    y,
                    z
                );

                chair.add(leg);

            }
        );


        /*
        |--------------------------------------------------------------------------
        | SANDARAN
        |--------------------------------------------------------------------------
        */

        const back =
            createBox(
                seatWidth,
                1.15,
                0.14,
                chairMaterial
            );

        back.position.set(
            0,
            seatHeight + 0.55,
            -seatDepth / 2 + 0.07
        );

        chair.add(back);


        return chair;
    }


    /*
    |--------------------------------------------------------------------------
    | SATU SET MEJA + 8 KURSI
    |--------------------------------------------------------------------------
    */

    function createStudySet() {

        const group =
            new THREE.Group();

        group.name =
            'StudySet';


        /*
        |--------------------------------------------------------------------------
        | MEJA
        |--------------------------------------------------------------------------
        */

        const table =
            createTable();

        group.add(table);


        /*
        |--------------------------------------------------------------------------
        | SPACING KURSI
        |--------------------------------------------------------------------------
        */

        const chairSpacing =
            1.65;

        const startX =
            -(
                chairSpacing * 1.5
            );


        /*
        |--------------------------------------------------------------------------
        | 4 KURSI DEPAN
        |--------------------------------------------------------------------------
        */

        for (
            let i = 0;
            i < 4;
            i++
        ) {

            const chair =
                createChair();


            chair.position.set(

                startX +
                i * chairSpacing,

                0,

                -2.0

            );


            /*
            |--------------------------------------------------------------------------
            | HADAP MEJA
            |--------------------------------------------------------------------------
            */

            chair.rotation.y =
                0;


            /*
            |--------------------------------------------------------------------------
            | ID KURSI
            |--------------------------------------------------------------------------
            */

            chair.userData.seatIndex =
                i + 1;

            chair.userData.seatSide =
                'front';


            group.add(
                chair
            );

        }


        /*
        |--------------------------------------------------------------------------
        | 4 KURSI BELAKANG
        |--------------------------------------------------------------------------
        */

        for (
            let i = 0;
            i < 4;
            i++
        ) {

            const chair =
                createChair();


            chair.position.set(

                startX +
                i * chairSpacing,

                0,

                2.0

            );


            /*
            |--------------------------------------------------------------------------
            | BALIK MENGHADAP MEJA
            |--------------------------------------------------------------------------
            */

            chair.rotation.y =
                Math.PI;


            /*
            |--------------------------------------------------------------------------
            | ID KURSI
            |--------------------------------------------------------------------------
            */

            chair.userData.seatIndex =
                i + 5;

            chair.userData.seatSide =
                'back';


            group.add(
                chair
            );

        }


        return group;
    }


    /*
    |--------------------------------------------------------------------------
    | POSISI MEJA DI SAMPING RAK
    |--------------------------------------------------------------------------
    */

    const gap =
        1.5;


    const table =
        createStudySet();


    /*
    |--------------------------------------------------------------------------
    | POSISI X
    |--------------------------------------------------------------------------
    |
    | Rak:
    |
    | -9 ---------------- +9
    |
    | Meja berada di sebelah kanan rak.
    |
    */

    table.position.x =
        rackWidth / 2 +
        gap +
        7.5 / 2;


    table.position.y =
        0;


    table.position.z =
        0;


    /*
    |--------------------------------------------------------------------------
    | MASUKKAN KE FURNITURE GROUP
    |--------------------------------------------------------------------------
    */

    furnitureGroup.add(
        table
    );


    /*
    |--------------------------------------------------------------------------
    | IKUT POSISI RAK
    |--------------------------------------------------------------------------
    */

    furnitureGroup.position.copy(
        rackGroup.position
    );


    /*
    |--------------------------------------------------------------------------
    | IKUT ROTASI RAK
    |--------------------------------------------------------------------------
    */

    furnitureGroup.rotation.copy(
        rackGroup.rotation
    );


    /*
    |--------------------------------------------------------------------------
    | MASUK SCENE
    |--------------------------------------------------------------------------
    */

    locatorGroup.add(
        furnitureGroup
    );


    return furnitureGroup;
}