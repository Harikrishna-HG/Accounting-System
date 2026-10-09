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
            --fs-1-1: clamp(0.717rem, 0.836rem + 0.257vw, 0.912rem);
            --fs-1-15: clamp(0.750rem, 0.874rem + 0.269vw, 0.954rem);
            --fs-1-2: clamp(0.782rem, 0.912rem + 0.281vw, 0.995rem);
            --fs-1-3: clamp(0.847rem, 0.988rem + 0.304vw, 1.079rem);
            --fs-1-35: clamp(0.880rem, 1.026rem + 0.316vw, 1.120rem);
            --fs-1-4: clamp(0.912rem, 1.064rem + 0.328vw, 1.162rem);
            --fs-1-5: clamp(0.977rem, 1.140rem + 0.351vw, 1.244rem);
            --fs-1-6: clamp(1.042rem, 1.216rem + 0.374vw, 1.327rem);
            --fs-1-8: clamp(1.173rem, 1.368rem + 0.421vw, 1.494rem);
            --fs-2: clamp(1.303rem, 1.520rem + 0.468vw, 1.659rem);
            --fs-2-2: clamp(1.434rem, 1.672rem + 0.515vw, 1.825rem);
            --fs-2-4: clamp(1.565rem, 1.824rem + 0.562vw, 1.991rem);
            --fs-2-8: clamp(1.825rem, 2.128rem + 0.655vw, 2.322rem);
            --fs-3-6: clamp(2.347rem, 2.736rem + 0.842vw, 2.986rem);
        }
