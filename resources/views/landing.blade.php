<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">

    <meta name="viewport" content="width=device-width, initial-scale=1.0, viewport-fit=cover">

    <title>Perpustakaan Tiga Serangkai</title>

    <meta name="description"
        content="Perpustakaan Tiga Serangkai – Temukan pengetahuan, jelajahi cerita, dan mulai perjalanan membacamu.">

    <meta name="theme-color" content="#020f10">
    <meta name="color-scheme" content="dark light">

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>

    <link
        href="https://fonts.googleapis.com/css2?family=Cinzel:wght@400;500;600;700;800;900&family=Inter:wght@300;400;500;600;700;800&family=Poppins:wght@300;400;500;600;700;800&display=swap"
        rel="stylesheet">

    <link rel="preload" as="image" href="{{ asset('images/bg-ts.jpeg') }}" fetchpriority="high">

    <link rel="stylesheet" href="{{ asset('css/landing.css') }}?v={{ time() }}">

    <link rel="stylesheet" href="{{ asset('css/footer.css') }}">
</head>

<body>

    <div class="viewport-wrapper">

        {{-- ============================================================
             LANDING PAGE
        ============================================================ --}}
        <div id="landing-stage">

            <div class="landing">

                <div class="landing-hero">

                    {{-- Background photo --}}
                    <div class="hero-bg" aria-hidden="true"></div>

                    {{-- Cinematic overlays --}}
                    <div class="hero-overlay-dark" aria-hidden="true"></div>

                    <div class="hero-overlay-teal" aria-hidden="true"></div>

                    <div class="hero-glow-edge" aria-hidden="true"></div>


                    {{-- Futuristic corner decorations --}}
                    <div class="hero-decor" aria-hidden="true">

                        {{-- Top-left arcs --}}
                        <svg class="decor-arc decor-arc--tl" viewBox="0 0 200 200" fill="none"
                            xmlns="http://www.w3.org/2000/svg">
                            <path d="M0 180 Q0 0 180 0" stroke="rgba(94,234,212,0.22)" stroke-width="1.5"
                                fill="none" />

                            <path d="M0 140 Q0 0 140 0" stroke="rgba(94,234,212,0.14)" stroke-width="1"
                                fill="none" />

                            <path d="M0 100 Q0 0 100 0" stroke="rgba(94,234,212,0.09)" stroke-width="0.8"
                                fill="none" />

                            <circle cx="0" cy="0" r="6" fill="rgba(94,234,212,0.5)" />

                            <circle cx="0" cy="0" r="3" fill="rgba(94,234,212,0.9)" />
                        </svg>


                        {{-- Bottom-right arcs --}}
                        <svg class="decor-arc decor-arc--br" viewBox="0 0 200 200" fill="none"
                            xmlns="http://www.w3.org/2000/svg">
                            <path d="M200 20 Q200 200 20 200" stroke="rgba(94,234,212,0.22)" stroke-width="1.5"
                                fill="none" />

                            <path d="M200 60 Q200 200 60 200" stroke="rgba(94,234,212,0.14)" stroke-width="1"
                                fill="none" />

                            <path d="M200 100 Q200 200 100 200" stroke="rgba(94,234,212,0.09)" stroke-width="0.8"
                                fill="none" />

                            <circle cx="200" cy="200" r="6" fill="rgba(94,234,212,0.5)" />

                            <circle cx="200" cy="200" r="3" fill="rgba(94,234,212,0.9)" />
                        </svg>

                    </div>


                    {{-- Hero content --}}
                    <div class="hero-content">

                        {{-- Logo --}}
                        <div class="hero-logo-wrap">
                            <img src="{{ asset('images/logo-tiga-serangkai.png') }}"
                                alt="Logo TS Perpustakaan Tiga Serangkai" class="hero-logo" draggable="false">
                        </div>


                        {{-- Title --}}
                        <div class="hero-title-wrap">

                            <p class="hero-subtitle-top">
                                PERPUSTAKAAN
                            </p>

                            <h1 class="hero-title">
                                TIGA SERANGKAI
                            </h1>

                        </div>


                        {{-- Tagline --}}
                        <p class="hero-tagline">
                            Temukan pengetahuan, jelajahi cerita,<br>
                            dan mulai perjalanan membacamu.
                        </p>


                        {{-- Ornament --}}
                        <div class="hero-ornament" aria-hidden="true">

                            <span class="orn-line"></span>

                            <svg class="orn-book" viewBox="0 0 24 24" fill="currentColor" aria-hidden="true">
                                <path d="M2 4h6a4 4 0 0 1 4 4v12.5a5.5 5.5 0 0 0-4-1.5H2V4Z" />
                                <path d="M22 4h-6a4 4 0 0 0-4 4v12.5a5.5 5.5 0 0 1 4-1.5h6V4Z" />
                            </svg>

                            <span class="orn-line"></span>

                        </div>

                    </div>

                </div>


                {{-- Footer --}}
                @include('partials.footer')

            </div>

        </div>


        {{-- ============================================================
             INTERACTIVE PORTAL RINGS
        ============================================================ --}}
        <div id="interactive-portal" aria-hidden="true">
            <div id="portal-ring-primary" class="portal-ring"></div>

            <div id="portal-ring-secondary" class="portal-ring"></div>
        </div>

    </div>


    {{-- GSAP --}}
    <script src="{{ asset('js/gsap.min.js') }}"></script>


    {{-- Landing transition --}}
    <script>
        (function() {
            'use strict';

            var landingStage = document.getElementById('landing-stage');
            var portalOverlay = document.getElementById('interactive-portal');
            var ringPrimary = document.getElementById('portal-ring-primary');
            var ringSecondary = document.getElementById('portal-ring-secondary');

            var loginUrl = @json(route('login'));

            var hasTriggered = false;
            var autoTimer = null;


            function completeTransition() {

                document.body.style.pointerEvents = '';

                if (landingStage) {
                    landingStage.style.display = 'none';
                    landingStage.setAttribute('aria-hidden', 'true');
                }

                if (portalOverlay) {
                    portalOverlay.style.display = 'none';
                }

                if (typeof gsap !== 'undefined') {
                    gsap.set(
                        [
                            landingStage,
                            ringPrimary,
                            ringSecondary
                        ].filter(Boolean), {
                            clearProps: 'transform,opacity,scale,willChange,clipPath,webkitClipPath,transformOrigin'
                        }
                    );
                }

                window.location.href = loginUrl;
            }


            function runPortalTransition(clientX, clientY) {

                if (hasTriggered) {
                    return;
                }

                hasTriggered = true;

                if (autoTimer) {
                    clearTimeout(autoTimer);
                }

                document.body.style.pointerEvents = 'none';


                if (!landingStage) {
                    completeTransition();
                    return;
                }


                var isReduced =
                    window.matchMedia &&
                    window.matchMedia(
                        '(prefers-reduced-motion: reduce)'
                    ).matches;


                if (
                    typeof gsap === 'undefined' ||
                    isReduced
                ) {
                    completeTransition();
                    return;
                }


                var originX =
                    typeof clientX === 'number' &&
                    !isNaN(clientX) ?
                    clientX :
                    Math.round(window.innerWidth / 2);


                var originY =
                    typeof clientY === 'number' &&
                    !isNaN(clientY) ?
                    clientY :
                    Math.round(window.innerHeight / 2);


                var cornerX = Math.max(
                    originX,
                    window.innerWidth - originX
                );

                var cornerY = Math.max(
                    originY,
                    window.innerHeight - originY
                );


                var maxRadius =
                    Math.ceil(
                        Math.hypot(
                            cornerX,
                            cornerY
                        )
                    ) + 80;


                if (
                    portalOverlay &&
                    ringPrimary &&
                    ringSecondary
                ) {
                    portalOverlay.style.display = 'block';

                    ringPrimary.style.left =
                        originX + 'px';

                    ringPrimary.style.top =
                        originY + 'px';

                    ringSecondary.style.left =
                        originX + 'px';

                    ringSecondary.style.top =
                        originY + 'px';
                }


                var ringScale =
                    (maxRadius / 20) * 1.05;


                var animTargets = [
                    landingStage,
                    ringPrimary,
                    ringSecondary
                ].filter(Boolean);


                gsap.set(
                    animTargets, {
                        willChange: 'transform, opacity'
                    }
                );


                gsap.set(
                    landingStage, {
                        transformOrigin: originX +
                            'px ' +
                            originY +
                            'px'
                    }
                );


                var tl = gsap.timeline({

                    defaults: {
                        force3D: true,
                        overwrite: 'auto'
                    },

                    onComplete: function() {

                        gsap.set(
                            animTargets, {
                                clearProps: 'willChange'
                            }
                        );

                        completeTransition();
                    }
                });


                // Landing zoom out
                tl.to(
                    landingStage, {
                        scale: 0.92,
                        opacity: 0,
                        y: -10,
                        duration: 0.76,
                        ease: 'power2.inOut'
                    },
                    0
                );


                // Primary ring
                if (ringPrimary) {

                    tl.fromTo(
                        ringPrimary, {
                            scale: 0,
                            opacity: 1
                        }, {
                            scale: ringScale,
                            opacity: 0,
                            duration: 0.86,
                            ease: 'power2.out'
                        },
                        0
                    );

                }


                // Secondary ring
                if (ringSecondary) {

                    tl.fromTo(
                        ringSecondary, {
                            scale: 0,
                            opacity: 0.75
                        }, {
                            scale: ringScale * 1.08,
                            opacity: 0,
                            duration: 0.92,
                            ease: 'power2.out'
                        },
                        0.04
                    );

                }

            }


            /*
             * Click pada landing.
             */
            if (landingStage) {

                landingStage.addEventListener(
                    'click',
                    function(e) {

                        if (
                            e.target.closest('a') ||
                            e.target.closest('button')
                        ) {
                            return;
                        }

                        runPortalTransition(
                            e.clientX,
                            e.clientY
                        );
                    }
                );


                /*
                 * Touch pada mobile.
                 */
                landingStage.addEventListener(
                    'touchstart',
                    function(e) {

                        if (
                            e.target.closest('a') ||
                            e.target.closest('button')
                        ) {
                            return;
                        }

                        if (
                            e.touches &&
                            e.touches[0]
                        ) {
                            runPortalTransition(
                                e.touches[0].clientX,
                                e.touches[0].clientY
                            );
                        }

                    }, {
                        passive: true
                    }
                );

            }


            /*
             * Keyboard interaction.
             */
            window.addEventListener(
                'keydown',
                function(e) {

                    if (hasTriggered) {
                        return;
                    }

                    if (
                        e.key === 'Enter' ||
                        e.key === ' ' ||
                        e.key === 'ArrowRight' ||
                        e.key === 'ArrowDown'
                    ) {
                        runPortalTransition();
                    }

                }
            );


            /*
             * Auto transition setelah 2.2 detik.
             */
            autoTimer = setTimeout(
                function() {
                    runPortalTransition();
                },
                2200
            );

        })();
    </script>

</body>

</html>
