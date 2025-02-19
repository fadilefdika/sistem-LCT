<?php // routes/breadcrumbs.php

// Note: Laravel will automatically resolve `Breadcrumbs::` without
// this import. This is nice for IDE syntax and refactoring.
use Diglactic\Breadcrumbs\Breadcrumbs;

// This import is also not required, and you could replace `BreadcrumbTrail $trail`
//  with `$trail`. This is nice for IDE type checking and completion.
use Diglactic\Breadcrumbs\Generator as BreadcrumbTrail;

// Dashboard
Breadcrumbs::for('dashboard', function (BreadcrumbTrail $trail) {
    $trail->push('Dashboard', route('admin.dashboard'));
});

// laporan lct
Breadcrumbs::for('laporan-lct', function (BreadcrumbTrail $trail) {
    $trail->parent('dashboard');
    $trail->push('Laporan LCT', route('admin.laporan-lct'));
});

Breadcrumbs::for('laporan-lct.detail', function (BreadcrumbTrail $trail) {
    $trail->parent('laporan-lct');
    $trail->push('Detail Laporan LCT #1233', route('admin.laporan-lct.detail'));
});

// // Home > Blog > [Category]
// Breadcrumbs::for('category', function (BreadcrumbTrail $trail, $category) {
//     $trail->parent('blog');
//     $trail->push($category->title, route('category', $category));
// });

// progress perbaikan
Breadcrumbs::for('progress-perbaikan', function (BreadcrumbTrail $trail) {
    $trail->parent('dashboard');
    $trail->push('Progress Perbaikan', route('admin.progress-perbaikan'));
});

// progress perbaikan detail 
Breadcrumbs::for('progress-perbaikan.detail', function (BreadcrumbTrail $trail) {
    $trail->parent('progress-perbaikan');
    $trail->push('Detail Progress Perbaikan #1233', route('admin.progress-perbaikan.detail'));
});

// laporan perbaikan lct
Breadcrumbs::for('laporan-perbaikan-lct', function (BreadcrumbTrail $trail) {
    $trail->parent('dashboard');
    $trail->push('Laporan Perbaikan LCT', route('admin.laporan-perbaikan-lct'));
});

// laporan perbaikan lct detail
Breadcrumbs::for('laporan-perbaikan-lct.detail', function (BreadcrumbTrail $trail) {
    $trail->parent('laporan-perbaikan-lct');
    $trail->push('Detail Laporan Perbaikan LCT #1233', route('admin.laporan-perbaikan-lct.detail'));
});