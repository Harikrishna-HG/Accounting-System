<?php

// Guards the responsive behaviour that broke silently before: tables rendered
// without a scroll wrapper inside an overflow:hidden card (columns were clipped,
// not scrollable), and .detail-grid stayed two columns on phones because the
// layout only ever collapsed .form-grid.

/** @return array<string, string> relative path (forward slashes) => source */
function allViewSources(): array
{
    $out = [];
    $base = resource_path('views');

    $files = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($base));
    foreach ($files as $file) {
        if ($file->isFile() && $file->getExtension() === 'php') {
            // Normalise to forward slashes so the paths compare identically on
            // Windows and POSIX.
            $rel = str_replace('\\', '/', str_replace($base.DIRECTORY_SEPARATOR, '', $file->getPathname()));
            $out[$rel] = (string) file_get_contents($file->getPathname());
        }
    }
    ksort($out);

    return $out;
}

it('wraps every on-screen table in a horizontally scrollable container', function () {
    // The printed invoice is paper, not a screen, so it is exempt by design.
    $paper = 'accounting/invoices/print.blade.php';

    $checked = 0;
    $problems = [];

    foreach (allViewSources() as $rel => $src) {
        $tables = substr_count($src, '<table');

        if ($tables === 0 || $rel === $paper) {
            continue;
        }

        $wrappers = substr_count($src, 'table-responsive');
        $checked++;

        if ($wrappers < $tables) {
            $problems[] = "{$rel}: {$tables} table(s), {$wrappers} wrapper(s)";
        }
    }

    expect($checked)->toBeGreaterThan(10, 'expected to find many tables to check')
        ->and($problems)->toBe([], 'tables need a .table-responsive wrapper or columns get clipped');
});

it('leaves the printed invoice table unwrapped', function () {
    $src = (string) file_get_contents(resource_path('views/accounting/invoices/print.blade.php'));

    expect(substr_count($src, '<table'))->toBe(1)
        ->and(substr_count($src, 'table-responsive'))->toBe(0);
});

it('has no leftover news-table class from the previous project', function () {
    $offenders = [];
    foreach (allViewSources() as $rel => $src) {
        if (str_contains($src, 'news-table')) {
            $offenders[] = $rel;
        }
    }

    expect($offenders)->toBe([]);
});

it('never lets a view shadow the layout mobile breakpoint with its own grid base', function () {
    // The real trap: a view's body-level <style> lands after the layout
    // stylesheet, so if the view redeclares a grid as a fixed 1fr 1fr and does
    // not also collapse it in a max-width media query, it beats the layout's
    // override and the page stays two columns on a phone.
    $grids = ['.detail-grid', '.form-grid'];

    $offenders = [];
    foreach (allViewSources() as $rel => $src) {
        if (str_starts_with($rel, 'layouts/')) {
            continue;
        }

        // Collect the text of every media block so a collapse can be detected.
        $mediaText = '';
        foreach (preg_split('/@media[^{]*\{/', $src) as $i => $chunk) {
            if ($i > 0) {
                $mediaText .= $chunk;
            }
        }

        foreach ($grids as $grid) {
            $declaresBase = preg_match('/'.preg_quote($grid, '/').'\s*\{\s*display\s*:\s*grid/', $src) === 1;
            if (! $declaresBase) {
                continue;
            }

            $declaresCollapse = preg_match(
                '/'.preg_quote($grid, '/').'\s*\{\s*grid-template-columns\s*:\s*1fr\s*;?\s*\}/',
                $mediaText
            ) === 1;

            if (! $declaresCollapse) {
                $offenders[] = "{$rel} declares {$grid} but never collapses it on small screens";
            }
        }
    }

    expect($offenders)->toBe([]);
});

it('owns .detail-grid in the layout alone, with a base rule and a mobile override', function () {
    $offenders = [];
    foreach (allViewSources() as $rel => $src) {
        if (! str_starts_with($rel, 'layouts/') && preg_match('/\.detail-grid\s*\{/', $src)) {
            $offenders[] = $rel;
        }
    }
    expect($offenders)->toBe([], '.detail-grid should be defined once, in the layout');

    $layout = (string) file_get_contents(resource_path('views/layouts/dashboard.blade.php'));

    // Without a base rule the element is only styled by a media query, which
    // would leave wide screens on a single column.
    expect($layout)->toMatch('/\.detail-grid\s*\{\s*display:\s*grid/')
        ->and($layout)->toMatch('/\.detail-grid\s*\{\s*display:\s*grid;\s*grid-template-columns:\s*1fr 1fr/');
});

it('collapses the two-column detail grid on small screens', function () {
    $layout = (string) file_get_contents(resource_path('views/layouts/dashboard.blade.php'));

    // The layout defines more than one 768px block, so collect them all and
    // require that the one carrying .detail-grid actually narrows it.
    preg_match_all('/@media\s*\(max-width:\s*768px\)\s*\{(.*?)\n\s{8}\}/s', $layout, $all);

    expect($all[1])->not->toBeEmpty('the dashboard layout should define a 768px breakpoint');

    $blocksWithDetailGrid = array_values(array_filter(
        $all[1],
        fn ($block) => str_contains($block, '.detail-grid')
    ));

    expect($blocksWithDetailGrid)->toHaveCount(1, 'exactly one breakpoint should handle .detail-grid')
        ->and($blocksWithDetailGrid[0])
        ->toMatch('/\.detail-grid\s*\{\s*grid-template-columns:\s*1fr/');
});

it('declares a viewport meta tag and a percentage root font size in both layouts', function () {
    foreach (['dashboard.blade.php', 'auth.blade.php'] as $layout) {
        $src = (string) file_get_contents(resource_path('views/layouts/'.$layout));

        expect($src)->toContain('name="viewport"')
            ->and($src)->toContain('name="viewport" content="width=device-width')
            // A percentage root tracks the browser default, so the whole rem-based
            // UI scales with the reader's font-size preference. A px root would not.
            ->and($src)->toMatch('/html\s*\{\s*font-size:\s*[\d.]+%/');
    }
});

it('never pins the root font size in pixels', function () {
    $offenders = [];
    foreach (allViewSources() as $rel => $src) {
        if (preg_match('/html\s*\{\s*font-size:\s*\d+px/', $src)) {
            $offenders[] = $rel;
        }
    }

    expect($offenders)->toBe([]);
});

it('takes every screen font size from the fluid type scale', function () {
    // The printed invoice is the one deliberate exception: paper does not care
    // about a viewport, and rem there would change the printed output.
    $exempt = [
        'accounting/invoices/print.blade.php',
        'layouts/_type-scale.blade.php',
    ];

    $offenders = [];
    foreach (allViewSources() as $rel => $src) {
        if (in_array($rel, $exempt, true)) {
            continue;
        }

        // Anything that is not var(--fs-*) is a hard-coded size. The root
        // percentage is fine; it is what keeps browser font preference working.
        foreach (preg_split('/font-size:\s*/', $src) as $i => $tail) {
            if ($i === 0) {
                continue;
            }
            $value = trim((string) preg_split('/[\s;}!]/', $tail, 2)[0]);
            if ($value !== '' && ! str_starts_with($value, 'var(--fs-') && ! str_ends_with($value, '%')) {
                $offenders[] = "{$rel} hard-codes font-size: {$value}";
            }
        }
    }

    expect($offenders)->toBe([], 'screen type should come from the fluid scale tokens');
});

it('defines every font token it uses, and only as rem-bounded clamp()', function () {
    $partial = (string) file_get_contents(resource_path('views/layouts/_type-scale.blade.php'));

    preg_match_all('/(--fs-[\d-]+):\s*clamp\(([^;)]+)\)/', $partial, $defs, PREG_SET_ORDER);
    $defined = array_column($defs, 1);

    $used = [];
    foreach (allViewSources() as $src) {
        preg_match_all('/var\((--fs-[\d-]+)\)/', $src, $hits);
        foreach ($hits[1] as $name) {
            $used[$name] = true;
        }
    }

    expect($defined)->not->toBeEmpty('the type scale should define tokens');

    // A token that is never defined, or one defined but never used, both fail
    // silently: a missing var() just falls back to the inherited size.
    expect(array_values(array_diff(array_keys($used), $defined)))
        ->toBe([], 'these tokens are used but never defined');
    expect(array_values(array_diff($defined, array_keys($used))))
        ->toBe([], 'these tokens are defined but never used');

    // rem in every term is what preserves the reader's font-size preference;
    // a vw-only scale would cap anyone who enlarges their browser text size.
    $notFluid = [];
    foreach ($defs as $def) {
        [$name, $clamp] = [$def[1], $def[2]];
        if (! preg_match('/^[\d.]+rem,\s*[\d.]+rem\s*\+\s*[\d.]+vw,\s*[\d.]+rem$/', $clamp)) {
            $notFluid[] = "{$name}: {$clamp}";
        }
    }

    expect($notFluid)->toBe([]);
});

it('loads the fluid type scale in both layouts', function () {
    $missing = [];
    foreach (['dashboard.blade.php', 'auth.blade.php'] as $layout) {
        $src = (string) file_get_contents(resource_path('views/layouts/'.$layout));
        if (! str_contains($src, "@include('layouts._type-scale')")) {
            $missing[] = $layout;
        }
    }

    expect($missing)->toBe([]);
});

it('keeps the dashboard column shrinkable so wide tables scroll instead of widening the page', function () {
    $layout = (string) file_get_contents(resource_path('views/layouts/dashboard.blade.php'));

    expect($layout)->toMatch('/\.main-content-area\s*\{[^}]*min-width:\s*0/');
});

it('sizes the invoice for a screen without changing the printed sheet', function () {
    $src = (string) file_get_contents(resource_path('views/accounting/invoices/print.blade.php'));

    expect($src)->toContain('name="viewport"')
        ->and($src)->toContain('@media screen')
        ->and($src)->toContain('@media print');
});

it('sizes the error pages from the fluid scale too', function () {
    foreach (['404', '403', '419', '500'] as $code) {
        $src = (string) file_get_contents(resource_path("views/errors/{$code}.blade.php"));
        expect($src)->toContain("errors._shell");
    }

    $shell = (string) file_get_contents(resource_path('views/errors/_shell.blade.php'));

    expect($shell)->toContain('name="viewport"')
        ->toContain("@include('layouts._type-scale')")
        ->toMatch('/html\s*\{\s*font-size:\s*[\d.]+%/');
});
