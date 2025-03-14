<?php // routes/breadcrumbs.php

use App\Models\LaporanLct;
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
    $trail->push('LCT Reports', route('admin.laporan-lct'));
});

Breadcrumbs::for('laporan-lct.show', function (BreadcrumbTrail $trail, $laporan) {
    $trail->parent('laporan-lct');
    $trail->push("Detail LCT Report #{$laporan->id_laporan_lct}", route('admin.laporan-lct.show', $laporan));
});


// activity approval
Breadcrumbs::for('budget-approval', function (BreadcrumbTrail $trail) {
    $trail->parent('dashboard');
    $trail->push('Activity Approval', route('admin.budget-approval'));
});

// activity approval detail
Breadcrumbs::for('budget-approval.show', function (BreadcrumbTrail $trail, $laporan) {
    $trail->parent('budget-approval');
    $trail->push("Detail Activity Approval LCT #{$laporan->id_laporan_lct}", route('admin.budget-approval.show', $laporan));
});

// activity approval history
Breadcrumbs::for('budget-approval-history', function (BreadcrumbTrail $trail) {
    $trail->parent('dashboard');
    $trail->push('Activity Approval History', route('admin.budget-approval-history'));
});

// activity approval history detail
Breadcrumbs::for('budget-approval-history.show', function (BreadcrumbTrail $trail, $laporan) {
    $trail->parent('budget-approval-history');
    $trail->push("Detail History Activity Approval LCT #{$laporan->id_laporan_lct}", route('admin.budget-approval-history.show', $laporan));
});

// progress perbaikan
Breadcrumbs::for('progress-perbaikan', function (BreadcrumbTrail $trail) {
    $trail->parent('dashboard');
    $trail->push('Activity Progress', route('admin.progress-perbaikan'));
});

// progress perbaikan detail 
Breadcrumbs::for('progress-perbaikan.show', function (BreadcrumbTrail $trail, $laporan) {
    $trail->parent('progress-perbaikan');
    $trail->push("Detail Activity Progress LCT #{$laporan->id_laporan_lct}", route('admin.progress-perbaikan.show', $laporan));
});


// laporan perbaikan lct
Breadcrumbs::for('manajemen-lct', function (BreadcrumbTrail $trail) {
    $trail->parent('dashboard');
    $trail->push('LCT Management', route('admin.manajemen-lct'));
});

// laporan perbaikan lct detail
Breadcrumbs::for('manajemen-lct.show', function (BreadcrumbTrail $trail, $laporan) {
    $trail->parent('manajemen-lct');
    $trail->push("Detail LCT Report #{$laporan->id_laporan_lct}", route('admin.manajemen-lct.show', $laporan));
});

// laporan riwayat lct
Breadcrumbs::for('riwayat-lct', function (BreadcrumbTrail $trail) {
    $trail->parent('dashboard');
    $trail->push('LCT History', route('admin.riwayat-lct'));
});

Breadcrumbs::for('riwayat-lct.show', function (BreadcrumbTrail $trail, $laporan) {
    $trail->parent('riwayat-lct');
    $trail->push("Detail LCT History #{$laporan->id_laporan_lct}", route('admin.riwayat-lct.show', $laporan));
});

// laporan manajemen pic
Breadcrumbs::for('manajemen-pic', function (BreadcrumbTrail $trail) {
    $trail->parent('dashboard');
    $trail->push('Master Data', route('admin.manajemen-pic'));
});