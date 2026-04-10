<?php

use App\Http\Controllers\DocumentoController;
use App\Http\Controllers\ItemController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ProveedorController;
use App\Http\Controllers\RoleController;
use App\Http\Controllers\SerieController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\WebformController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

// ── Webform público (embebible via iframe) ──────────────────────────────────
Route::get('/webform/pedido',  [WebformController::class, 'show'])->name('webform.show');
Route::post('/webform/pedido', [WebformController::class, 'store'])->name('webform.store');
Route::get('/webform/proveedores/{proveedor}/items', [WebformController::class, 'itemsProveedor'])
    ->name('webform.items-proveedor');
// ───────────────────────────────────────────────────────────────────────────

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    // Perfil propio
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    // Gestión de usuarios (requiere permiso users.view como mínimo)
    Route::middleware('permission:users.view')->group(function () {
        Route::get('/users', [UserController::class, 'index'])->name('users.index');
        Route::get('/users/{user}', [UserController::class, 'show'])->name('users.show');
    });

    Route::middleware('permission:users.edit')->group(function () {
        Route::get('/users/{user}/edit', [UserController::class, 'edit'])->name('users.edit');
        Route::put('/users/{user}', [UserController::class, 'update'])->name('users.update');
        Route::patch('/users/{user}/toggle', [UserController::class, 'toggleStatus'])->name('users.toggle');
    });

    Route::delete('/users/{user}', [UserController::class, 'destroy'])
        ->middleware('permission:users.delete')
        ->name('users.destroy');

    // Gestión de roles (requiere permiso roles.view como mínimo)
    Route::middleware('permission:roles.view')->group(function () {
        Route::get('/roles', [RoleController::class, 'index'])->name('roles.index');
    });

    Route::middleware('permission:roles.create')->group(function () {
        Route::get('/roles/create', [RoleController::class, 'create'])->name('roles.create');
        Route::post('/roles', [RoleController::class, 'store'])->name('roles.store');
    });

    Route::middleware('permission:roles.edit')->group(function () {
        Route::get('/roles/{role}/edit', [RoleController::class, 'edit'])->name('roles.edit');
        Route::put('/roles/{role}', [RoleController::class, 'update'])->name('roles.update');
    });

    Route::delete('/roles/{role}', [RoleController::class, 'destroy'])
        ->middleware('permission:roles.delete')
        ->name('roles.destroy');

    // Proveedores — rutas estáticas ANTES de las de parámetro
    Route::middleware('permission:proveedores.create')->group(function () {
        Route::get('/proveedores/create', [ProveedorController::class, 'create'])->name('proveedores.create');
        Route::post('/proveedores', [ProveedorController::class, 'store'])->name('proveedores.store');
    });
    Route::middleware('permission:proveedores.view')->group(function () {
        Route::get('/proveedores', [ProveedorController::class, 'index'])->name('proveedores.index');
        Route::get('/proveedores/{proveedor}', [ProveedorController::class, 'show'])->name('proveedores.show');
    });
    Route::middleware('permission:proveedores.create')->group(function () {
        Route::post('proveedores/{proveedor}/items', [ProveedorController::class, 'addItems'])->name('proveedores.items.add');
    });
    Route::middleware('permission:proveedores.edit')->group(function () {
        Route::get('/proveedores/{proveedor}/edit', [ProveedorController::class, 'edit'])->name('proveedores.edit');
        Route::put('/proveedores/{proveedor}', [ProveedorController::class, 'update'])->name('proveedores.update');
        Route::patch('/proveedores/{proveedor}', [ProveedorController::class, 'update']);
    });
    Route::delete('proveedores/{proveedor}', [ProveedorController::class, 'destroy'])
        ->middleware('permission:proveedores.delete')
        ->name('proveedores.destroy');
    Route::delete('proveedores/{proveedor}/items/{itemId}', [ProveedorController::class, 'removeItem'])
        ->middleware('permission:proveedores.edit')
        ->name('proveedores.items.remove');

    // Documentos — rutas estáticas ANTES de las de parámetro
    Route::middleware('permission:documentos.create')->group(function () {
        Route::get('/documentos/create', [DocumentoController::class, 'create'])->name('documentos.create');
        Route::post('/documentos', [DocumentoController::class, 'store'])->name('documentos.store');
        Route::get('proveedores/{proveedor}/items-json', [DocumentoController::class, 'itemsProveedor'])
            ->name('documentos.items-proveedor');
    });
    Route::middleware('permission:documentos.view')->group(function () {
        Route::get('/documentos', [DocumentoController::class, 'index'])->name('documentos.index');
        Route::get('/documentos/{documento}', [DocumentoController::class, 'show'])->name('documentos.show');
    });
    Route::patch('documentos/{documento}/estado', [DocumentoController::class, 'updateEstado'])
        ->middleware('permission:documentos.estado')
        ->name('documentos.estado');
    Route::delete('documentos/{documento}', [DocumentoController::class, 'destroy'])
        ->middleware('permission:documentos.delete')
        ->name('documentos.destroy');

    // Items SAP (BD remota, solo lectura)
    Route::middleware('permission:itemssap.view')->group(function () {
        Route::get('/items', [ItemController::class, 'index'])->name('items.index');
        Route::get('/items/{item}', [ItemController::class, 'show'])->name('items.show');
    });

    // Series SAP (BD remota, solo lectura)
    Route::middleware('permission:seriessap.view')->group(function () {
        Route::get('/series', [SerieController::class, 'index'])->name('series.index');
        Route::get('/series/{serie}', [SerieController::class, 'show'])->name('series.show');
    });
});

require __DIR__.'/auth.php';
