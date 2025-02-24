<?php // routes/breadcrumbs.php

use App\Models\LaporanLCT;
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

Breadcrumbs::for('laporan-lct.show', function (BreadcrumbTrail $trail, $laporan) {
    $trail->parent('laporan-lct');
    $trail->push("Detail Laporan LCT #{$laporan->id_laporan_lct}", route('admin.laporan-lct.show', $laporan));
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
Breadcrumbs::for('manajemen-lct', function (BreadcrumbTrail $trail) {
    $trail->parent('dashboard');
    $trail->push('Manajemen LCT', route('admin.manajemen-lct'));
});

// laporan perbaikan lct detail
Breadcrumbs::for('manajemen-lct.detail', function (BreadcrumbTrail $trail) {
    $trail->parent('manajemen-lct');
    $trail->push('Detail Manajemen LCT #1233', route('admin.manajemen-lct.detail'));
});

// laporan riwayat lct
Breadcrumbs::for('riwayat-lct', function (BreadcrumbTrail $trail) {
    $trail->parent('dashboard');
    $trail->push('Riwayat LCT', route('admin.riwayat-lct'));
});

// laporan manajemen pic
Breadcrumbs::for('manajemen-pic', function (BreadcrumbTrail $trail) {
    $trail->parent('dashboard');
    $trail->push('Manajemen PIC', route('admin.manajemen-pic'));
});