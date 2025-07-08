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

Breadcrumbs::for('ehs dashboard', function (BreadcrumbTrail $trail) {
    $trail->push('Dashboard', route('ehs.dashboard'));
});

// ehs laporan lct
Breadcrumbs::for('laporan-lct', function (BreadcrumbTrail $trail) {
    $trail->parent('dashboard');
    $trail->push('LCT Reports', route('admin.laporan-lct.index'));
});

Breadcrumbs::for('laporan-lct.show', function (BreadcrumbTrail $trail, $laporan) {
    $trail->parent('laporan-lct');
    $trail->push("LCT Report #{$laporan->id_laporan_lct}", route('admin.reporting.show.new', $laporan));
});

// laporan lct
Breadcrumbs::for('ehs laporan-lct', function (BreadcrumbTrail $trail) {
    $trail->parent('dashboard');
    $trail->push('LCT Reports', route('ehs.laporan-lct.index'));
});

Breadcrumbs::for('ehs laporan-lct.show', function (BreadcrumbTrail $trail, $laporan) {
    $trail->parent('laporan-lct');
    $trail->push("LCT Report #{$laporan->id_laporan_lct}", route('ehs.reporting.show.new', $laporan));
});

// permanent action
Breadcrumbs::for('budget-approval', function (BreadcrumbTrail $trail) {
    $trail->parent('dashboard');
    $trail->push('Permanent Action', route('admin.budget-approval.index'));
});

// permanent action detail
Breadcrumbs::for('budget-approval.show', function (BreadcrumbTrail $trail, $laporan) {
    $trail->parent('budget-approval');
    $trail->push("Permanent Action #{$laporan->id_laporan_lct}", route('admin.budget-approval.show', $laporan));
});

// permanent action history
Breadcrumbs::for('budget-approval-history', function (BreadcrumbTrail $trail) {
    $trail->parent('dashboard');
    $trail->push('Permanent Action History', route('admin.budget-approval-history.index'));
});

// permanent action history detail
Breadcrumbs::for('budget-approval-history.show', function (BreadcrumbTrail $trail, $laporan) {
    $trail->parent('budget-approval-history');
    $trail->push("History Approval #{$laporan->id_laporan_lct}", route('admin.budget-approval-history.show', $laporan));
});

// progress perbaikan
Breadcrumbs::for('progress-perbaikan', function (BreadcrumbTrail $trail) {
    $trail->parent('dashboard');
    $trail->push('Reporting', route('admin.reporting.index'));
});

// progress perbaikan detail 
Breadcrumbs::for('progress-perbaikan.show', function (BreadcrumbTrail $trail, $laporan) {
    $trail->parent('progress-perbaikan');
    $trail->push("Reporting #{$laporan->id_laporan_lct}", route('admin.reporting.show', $laporan));
});

// progress perbaikan
Breadcrumbs::for('ehs progress-perbaikan', function (BreadcrumbTrail $trail) {
    $trail->parent('dashboard');
    $trail->push('Reporting', route('ehs.reporting.index'));
});

// progress perbaikan detail 
Breadcrumbs::for('ehs progress-perbaikan.show', function (BreadcrumbTrail $trail, $laporan) {
    $trail->parent('progress-perbaikan');
    $trail->push("Reporting #{$laporan->id_laporan_lct}", route('ehs.reporting.show', $laporan));
});


// laporan perbaikan lct
Breadcrumbs::for('manajemen-lct', function (BreadcrumbTrail $trail) {
    $trail->parent('dashboard');
    $trail->push('Reporting', route('admin.manajemen-lct.index'));
});

// laporan perbaikan lct detail
Breadcrumbs::for('manajemen-lct.show', function (BreadcrumbTrail $trail, $laporan) {
    $trail->parent('manajemen-lct');
    $trail->push("LCT Report #{$laporan->id_laporan_lct}", route('admin.manajemen-lct.show', $laporan));
});

// laporan perbaikan lct
Breadcrumbs::for('finding-followup', function (BreadcrumbTrail $trail) {
    $trail->parent('dashboard');
    $trail->push('Finding Follow Up', route('admin.finding-followup.index'));
});

// laporan perbaikan lct detail
Breadcrumbs::for('finding-followup.show', function (BreadcrumbTrail $trail, $laporan) {
    $trail->parent('finding-followup');
    $trail->push("Finding Follow Up #{$laporan->id_laporan_lct}", route('admin.finding-followup.show', $laporan));
});

// laporan riwayat lct
Breadcrumbs::for('riwayat-lct', function (BreadcrumbTrail $trail) {
    $trail->parent('dashboard');
    $trail->push('LCT History', route('admin.riwayat-lct.index'));
});

Breadcrumbs::for('riwayat-lct.show', function (BreadcrumbTrail $trail, $laporan) {
    $trail->parent('riwayat-lct');
    $trail->push("LCT History #{$laporan->id_laporan_lct}", route('admin.riwayat-lct.show', $laporan));
});

// laporan master data
Breadcrumbs::for('master-data.role-data', function (BreadcrumbTrail $trail) {
    $trail->parent('dashboard');
    $trail->push('PIC Data', route('admin.master-data.role-data.index'));
});

// laporan master data
Breadcrumbs::for('master-data.category-data', function (BreadcrumbTrail $trail) {
    $trail->parent('dashboard');
    $trail->push('Category Data', route('admin.master-data.category-data.index'));
});

// laporan master data
Breadcrumbs::for('master-data.department-data', function (BreadcrumbTrail $trail) {
    $trail->parent('dashboard');
    $trail->push('Department Data', route('admin.master-data.department-data.index'));
});
// laporan master data
Breadcrumbs::for('master-data.area-data', function (BreadcrumbTrail $trail) {
    $trail->parent('dashboard');
    $trail->push('Department Data', route('admin.master-data.area-data.index'));
});

// laporan master data
Breadcrumbs::for('ehs.master-data.role-data', function (BreadcrumbTrail $trail) {
    $trail->parent('dashboard');
    $trail->push('PIC Data', route('ehs.master-data.role-data.index'));
});

// laporan master data
Breadcrumbs::for('ehs.master-data.category-data', function (BreadcrumbTrail $trail) {
    $trail->parent('dashboard');
    $trail->push('Category Data', route('ehs.master-data.category-data.index'));
});

// laporan master data
Breadcrumbs::for('ehs.master-data.department-data', function (BreadcrumbTrail $trail) {
    $trail->parent('dashboard');
    $trail->push('Department Data', route('ehs.master-data.department-data.index'));
});

// laporan master data
Breadcrumbs::for('ehs.master-data.area-data', function (BreadcrumbTrail $trail) {
    $trail->parent('dashboard');
    $trail->push('Area Data', route('ehs.master-data.area-data.index'));
});