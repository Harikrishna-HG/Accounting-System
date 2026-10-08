{{-- Fluid type scale.

     Every size the UI uses is defined once here as a custom property, so type
     responds to the viewport without each view hard-coding its own numbers.

     The clamp bounds are in rem and the preferred value mixes rem with vw on
     purpose. A viewport-only root font size would cap anyone who raises their
     browser's default text size, because vw ignores that preference. Because
     every term here is rem-based, a reader at 200% still gets 200% larger text
     at every screen width.

     1280px reproduces the previous fixed rem size exactly, so desktop is
     unchanged; below and above that the type eases in and out. --}}

        :root {
            --fs-1-1: clamp(0.968rem, 0.836rem + 0.257vw, 1.232rem);
            --fs-1-15: clamp(1.012rem, 0.874rem + 0.269vw, 1.288rem);
            --fs-1-2: clamp(1.056rem, 0.912rem + 0.281vw, 1.344rem);
            --fs-1-3: clamp(1.144rem, 0.988rem + 0.304vw, 1.456rem);
            --fs-1-35: clamp(1.188rem, 1.026rem + 0.316vw, 1.512rem);
            --fs-1-4: clamp(1.232rem, 1.064rem + 0.328vw, 1.568rem);
            --fs-1-5: clamp(1.32rem, 1.14rem + 0.351vw, 1.68rem);
            --fs-1-6: clamp(1.408rem, 1.216rem + 0.374vw, 1.792rem);
            --fs-1-8: clamp(1.584rem, 1.368rem + 0.421vw, 2.016rem);
            --fs-2: clamp(1.76rem, 1.52rem + 0.468vw, 2.24rem);
            --fs-2-2: clamp(1.936rem, 1.672rem + 0.515vw, 2.464rem);
            --fs-2-4: clamp(2.112rem, 1.824rem + 0.562vw, 2.688rem);
            --fs-2-8: clamp(2.464rem, 2.128rem + 0.655vw, 3.136rem);
            --fs-3-6: clamp(3.168rem, 2.736rem + 0.842vw, 4.032rem);
        }
