<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\StudentLoginController;
use App\Http\Controllers\CatalogController;
use App\Http\Controllers\AttendanceController;
use App\Http\Controllers\BorrowingController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\BookController;
use App\Http\Controllers\Admin\BorrowingController as AdminBorrowingController;
use App\Http\Controllers\Admin\StudentController;
use App\Http\Controllers\Admin\GradeController;
use App\Http\Controllers\Admin\ShelfController;
use App\Http\Controllers\Admin\CategoryController;
use App\Http\Controllers\Admin\SettingsController;
use App\Http\Controllers\Admin\ReportController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
*/

// Home / Welcome
Route::get('/', function () {
    return view('welcome');
})->name('home');

// Admin Authentication Routes
Route::get('/login', function () {
    return view('auth.login');
})->name('login');

Route::post('/login', function (\Illuminate\Http\Request $request) {
    $credentials = $request->validate([
        'email' => 'required|email',
        'password' => 'required',
    ]);

    if (\Illuminate\Support\Facades\Auth::attempt($credentials)) {
        $request->session()->regenerate();
        return redirect()->intended('/admin');
    }

    return back()->withErrors([
        'email' => 'Email atau password salah.',
    ])->onlyInput('email');
})->name('login.submit');

Route::post('/logout', function (\Illuminate\Http\Request $request) {
    \Illuminate\Support\Facades\Auth::logout();
    $request->session()->invalidate();
    $request->session()->regenerateToken();
    return redirect('/');
})->name('logout');

// Student Authentication
Route::prefix('login')->group(function () {
    Route::get('/student', [StudentLoginController::class, 'showLoginForm'])->name('student.login');
    Route::post('/student', [StudentLoginController::class, 'login'])->name('student.login.submit');
});
Route::post('/logout/student', [StudentLoginController::class, 'logout'])->name('student.logout');

// Public Catalog
Route::prefix('catalog')->group(function () {
    Route::get('/', [CatalogController::class, 'index'])->name('catalog.index');
    Route::get('/search', [CatalogController::class, 'search'])->name('catalog.search');
    Route::get('/{book}', [CatalogController::class, 'show'])->name('catalog.show');
});

// Attendance (QR Scanning)
Route::prefix('attendance')->group(function () {
    Route::get('/scan', [AttendanceController::class, 'scan'])->name('attendance.scan');
    Route::post('/store', [AttendanceController::class, 'store'])->name('attendance.store');
    Route::get('/stats', [AttendanceController::class, 'todayStats'])->name('attendance.stats');
});

// Student Borrowing
Route::prefix('borrowings')->group(function () {
    Route::post('/', [BorrowingController::class, 'store'])->name('borrowings.store');
    Route::get('/my-books', [BorrowingController::class, 'myBooks'])->name('borrowings.my-books');
});

// Admin Routes (protected by auth middleware)
Route::prefix('admin')->middleware(['auth'])->name('admin.')->group(function () {
    // Dashboard
    Route::get('/', [DashboardController::class, 'index'])->name('dashboard');
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard.index');
    Route::get('/chart-data', [DashboardController::class, 'chartData'])->name('dashboard.chart-data');
    
    // Books CRUD
    Route::resource('books', BookController::class);
    Route::get('/books-export', [BookController::class, 'export'])->name('books.export');
    
    // Borrowings Management
    Route::get('/borrowings', [AdminBorrowingController::class, 'index'])->name('borrowings.index');
    Route::get('/borrowings/search-recommendations', [AdminBorrowingController::class, 'searchRecommendations'])->name('borrowings.search-recommendations');
    Route::get('/borrowings/create', [AdminBorrowingController::class, 'create'])->name('borrowings.create');
    Route::post('/borrowings', [AdminBorrowingController::class, 'store'])->name('borrowings.store');
    Route::get('/borrowings/{borrowing}', [AdminBorrowingController::class, 'show'])->name('borrowings.show');
    Route::put('/borrowings/{borrowing}/return', [AdminBorrowingController::class, 'returnBook'])->name('borrowings.return');
    
    // Master Data
    Route::resource('students', StudentController::class);
    Route::resource('grades', GradeController::class);
    Route::resource('shelves', ShelfController::class);
    Route::resource('categories', CategoryController::class);
    
    // Settings
    Route::get('/settings', [SettingsController::class, 'index'])->name('settings');
    Route::post('/settings/password', [SettingsController::class, 'updatePassword'])->name('settings.password');
    
    // Reports
    Route::get('/reports/books', [ReportController::class, 'books'])->name('reports.books');
    Route::get('/reports/books/export', [ReportController::class, 'exportBooks'])->name('reports.books.export');
    Route::get('/reports/students', [ReportController::class, 'students'])->name('reports.students');
    Route::get('/reports/students/export', [ReportController::class, 'exportStudents'])->name('reports.students.export');
    Route::get('/reports/visitors', [ReportController::class, 'visitors'])->name('reports.visitors');
    Route::get('/reports/visitors/export', [ReportController::class, 'exportVisitors'])->name('reports.visitors.export');
    
    // Barcode Scanner
    Route::get('/scan', [\App\Http\Controllers\Admin\ScanController::class, 'index'])->name('scan');
    Route::post('/scan/process', [\App\Http\Controllers\Admin\ScanController::class, 'process'])->name('scan.process');
    
    // Attendance QR Generator
    Route::get('/attendance', [\App\Http\Controllers\Admin\AttendanceQrController::class, 'index'])->name('attendance');
    Route::post('/attendance/generate', [\App\Http\Controllers\Admin\AttendanceQrController::class, 'generateQr'])->name('attendance.generate');
});

// Storage file serve route (fallback when symlink not available on shared hosting)
Route::get('/storage/{path}', function ($path) {
    $fullPath = storage_path('app/public/' . $path);
    
    if (!file_exists($fullPath)) {
        abort(404);
    }
    
    $mimeType = mime_content_type($fullPath);
    
    return response()->file($fullPath, [
        'Content-Type' => $mimeType,
        'Cache-Control' => 'public, max-age=31536000',
    ]);
})->where('path', '.*')->name('storage.serve');
