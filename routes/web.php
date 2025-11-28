<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ProfileController;

// Dashboards
use App\Http\Controllers\Admin\DashboardController as AdminDash;
use App\Http\Controllers\Coordenador\DashboardController as CoordDash;
use App\Http\Controllers\Revisor\DashboardController as RevDash;
use App\Http\Controllers\Autor\DashboardController as AutorDash;

// Admin
use App\Http\Controllers\Admin\CategoryController as AdminCategoryController;
use App\Http\Controllers\Admin\UserController as AdminUserController;
use App\Http\Controllers\Admin\SubmissionController as AdminSubmission;
use App\Http\Controllers\Admin\ReportsController;
use App\Http\Controllers\Admin\EditionController;
use App\Http\Controllers\Admin\EditionSubmissionController;

// Coordenador
use App\Http\Controllers\Coordenador\RevisorController as CoordRevisores;
use App\Http\Controllers\Coordenador\SubmissionsController as CoordSubs;
use App\Http\Controllers\Coordenador\RelatorioRevisoresController;

// Autor
use App\Http\Controllers\Autor\SubmissionController as AutorSubmission;
use App\Http\Controllers\Autor\NotificationController;

// Comum
use App\Http\Controllers\Revisor\ReviewsController as RevisorReviews;
use App\Http\Controllers\Common\SubmissionCommentController as CommentCtrl;

// Callblade + Favoritos + Estatísticas
use App\Http\Controllers\Common\FavoriteController;
use App\Http\Controllers\Common\StatsController;
use App\Http\Controllers\Common\CallController;

// >>> CONTROLLERS DA HOME PAGE E ARTIGO
use App\Http\Controllers\HomeController;
use App\Http\Controllers\ArtigoController; // 🚀 NOVO IMPORT: ArtigoController

/*
|--------------------------------------------------------------------------
| Rotas Públicas – Cabeçalho da Home
|--------------------------------------------------------------------------
*/

// FORMULÁRIO PARA CRIAR ARTIGO
Route::get('/artigos/create', [ArtigoController::class, 'create'])->name('artigos.create');


// SALVAR ARTIGO NO BANCO
Route::post('/artigos', [ArtigoController::class, 'store'])->name('artigos.store');


// ROTA ATUALIZADA: Chama o HomeController para buscar e passar os dados
Route::get('/', [HomeController::class, 'index'])->name('inicio'); 

// 🚀 NOVA ROTA: Exibir detalhes de um artigo
// Note que usamos {id} para capturar o ID do artigo
Route::get('/artigo/{id}', [ArtigoController::class, 'show'])->name('artigo.show'); 


Route::get('/edicao-atual', fn () => view('edicao-atual'))->name('edicao.atual');
Route::get('/edicoes', fn () => view('edicoes'))->name('edicoes');

Route::get('/artigos', [ArtigoController::class, 'index'])->name('artigos.index');
Route::get('/autores', fn () => view('autores'))->name('autores');
Route::get('/categorias', fn () => view('categorias'))->name('categorias');
Route::get('/diretrizes', fn () => view('diretrizes'))->name('diretrizes');

Route::get('/conselho-editorial', fn () => view('conselho-editorial'))->name('conselho.editorial');

Route::get('/sobre', fn () => view('sobre'))->name('sobre');

Route::get('/submissao', fn () => view('submissao'))->name('submissao');
Route::get('/submeter-projeto', fn () => view('submissao'))->name('submeter.projeto');


/*
|--------------------------------------------------------------------------
| Dashboard Global
|--------------------------------------------------------------------------
*/
Route::get('/dashboard', function () {
    $u = auth()->user();
    if (!$u) return redirect()->route('login');

    if ($u->hasRole('Admin')) return redirect()->route('admin.dashboard');
    if ($u->hasRole('Coordenador')) return redirect()->route('coordenador.dashboard');
    if ($u->hasRole('Revisor')) return redirect()->route('revisor.dashboard');
    if ($u->hasRole('Autor')) return redirect()->route('autor.dashboard');

    return view('dashboard-sem-papel', [
        'user' => $u,
        'roles' => $u->getRoleNames()
    ]);
})->middleware(['auth', 'verified'])->name('dashboard');


/*
|--------------------------------------------------------------------------
| Perfil
|--------------------------------------------------------------------------
*/
Route::middleware('auth')->group(function () {

    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::patch('/profile/password', [ProfileController::class, 'updatePassword'])->name('profile.password');
    Route::post('/profile/avatar', [ProfileController::class, 'updateAvatar'])->name('profile.avatar');

});


/*
|--------------------------------------------------------------------------
| ADMIN
|--------------------------------------------------------------------------
*/
Route::middleware(['auth', 'verified', 'role:Admin'])
    ->prefix('admin')->as('admin.')
    ->group(function () {

        Route::get('/dashboard', [AdminDash::class, 'index'])->name('dashboard');

        Route::prefix('submissoes')->as('submissions.')->group(function () {
            Route::get('/', [AdminSubmission::class, 'index'])->name('index');
            Route::get('/{submission:slug}', [AdminSubmission::class, 'show'])->name('show');
            Route::delete('/{submission:slug}/comments/{comment}', 
                [AdminSubmission::class, 'commentsDestroy'])
                ->whereNumber('comment')->name('comments.destroy');
        });

        Route::resource('categorias', AdminCategoryController::class)
            ->parameters(['categorias' => 'category'])
            ->names('categories')
            ->except(['show']);

        Route::resource('users', AdminUserController::class)
            ->parameters(['users' => 'user'])
            ->names('users');

        Route::prefix('triagem')->as('triage.')->group(function () {
            Route::get('/', [\App\Http\Controllers\Admin\TriagemController::class, 'index'])->name('index');
            Route::get('/{submission:slug}', [\App\Http\Controllers\Admin\TriagemController::class, 'show'])->name('show');
        });

        Route::resource('edicoes', EditionController::class)
            ->parameters(['edicoes' => 'edition'])
            ->names('editions');

        Route::get('edicoes/{edition}/submissions', [EditionSubmissionController::class, 'index'])
            ->name('editions.submissions.index');

        Route::post('edicoes/{edition}/submissions', [EditionSubmissionController::class, 'store'])
            ->name('editions.submissions.store');

        Route::delete('edicoes/{edition}/submissions/{submission}', [EditionSubmissionController::class, 'destroy'])
            ->name('editions.submissions.destroy');

        Route::patch('edicoes/{edition}/submissions/reorder', [EditionSubmissionController::class, 'reorder'])
            ->name('editions.submissions.reorder');

        // Notifications
        Route::get('/notifications', [NotificationController::class, 'index'])->name('notifications.index');
        Route::get('/notifications/{notification}', [NotificationController::class, 'show'])->name('notifications.show');
        Route::post('/notifications/{notification}/read', [NotificationController::class, 'markAsRead'])->name('notifications.read');
        Route::post('/notifications/read-all', [NotificationController::class, 'markAllAsRead'])->name('notifications.read_all');
        Route::delete('/notifications/{notification}', [NotificationController::class, 'destroy'])->name('notifications.destroy');

        // Reports
        Route::get('/relatorios', [ReportsController::class, 'index'])->name('reports.index');
        Route::get('/relatorios/autores', [ReportsController::class, 'authors'])->name('reports.authors');
        Route::get('/relatorios/revisores', [ReportsController::class, 'reviewers'])->name('reports.reviewers');
        Route::get('/relatorios/coordenadores', [ReportsController::class, 'coordinators'])->name('reports.coordinators');
        Route::get('/relatorios/submissoes', [ReportsController::class, 'submissions'])->name('reports.submissions');
        Route::get('/relatorios/edicoes', [ReportsController::class, 'editions'])->name('reports.editions');
        Route::get('/relatorios/sistema', [ReportsController::class, 'system'])->name('reports.system');
        Route::get('/relatorios/export/csv', [ReportsController::class, 'exportCsv'])->name('reports.export.csv');
        Route::get('/relatorios/export/pdf', [ReportsController::class, 'exportPdf'])->name('reports.export.pdf');

    });


/*
|--------------------------------------------------------------------------
| Coordenador
|--------------------------------------------------------------------------
*/
Route::middleware(['auth', 'verified', 'role:Coordenador|Admin'])
    ->prefix('coordenador')->as('coordenador.')
    ->group(function () {

        Route::get('/dashboard', [CoordDash::class, 'index'])->name('dashboard');

        Route::resource('revisores', CoordRevisores::class)
            ->parameters(['revisores' => 'user'])
            ->except(['show']);

        Route::get('/submissoes', [CoordSubs::class, 'index'])->name('submissions.index');
        Route::get('/submissoes/{submission:slug}', [CoordSubs::class, 'show'])->name('submissions.show');

        Route::get('relatorios/revisores', [RelatorioRevisoresController::class, 'index'])
            ->name('relatorios.revisores.index');

        Route::get('relatorios/revisores/{reviewer}', [RelatorioRevisoresController::class, 'show'])
            ->name('relatorios.revisores.show');
    });


/*
|--------------------------------------------------------------------------
| Rotas comuns (comentários, favoritos, stats, call)
|--------------------------------------------------------------------------
*/
Route::middleware(['auth', 'verified'])->scopeBindings()->group(function () {

    // Comentários
    Route::get('/submissions/{submission:slug}/comments', [CommentCtrl::class, 'index'])->name('comments.index');
    Route::post('/submissions/{submission:slug}/comments', [CommentCtrl::class, 'store'])->name('comments.store');

    Route::patch('/submissions/{submission:slug}/comments/{comment}/apply', [CommentCtrl::class, 'applySuggestion'])
        ->whereNumber('comment')->name('comments.apply');

    Route::patch('/submissions/{submission:slug}/comments/{comment}/author-resolved', [CommentCtrl::class, 'authorResolved'])
        ->whereNumber('comment')->name('comments.author_resolved');

    Route::patch('/submissions/{submission:slug}/comments/{comment}/verify', [CommentCtrl::class, 'verify'])
        ->whereNumber('comment')->name('comments.verify');

    Route::delete('/submissions/{submission:slug}/comments/{comment}', [CommentCtrl::class, 'destroy'])
        ->whereNumber('comment')->name('comments.destroy');

    // ⭐ Favoritos
    Route::post('/favorites/{submission:slug}', [FavoriteController::class, 'toggle'])->name('favorites.toggle');
    Route::get('/perfil/favoritos', [FavoriteController::class, 'index'])->name('favorites.index');

    // 📈 Estatísticas
    Route::get('/stats', [StatsController::class, 'index'])->name('stats.index');

    // 📞 Callblade
    Route::get('/call/{submission:slug}', [CallController::class, 'index'])->name('call.index');
});


/*
|--------------------------------------------------------------------------
| Revisor
|--------------------------------------------------------------------------
*/
Route::middleware(['auth', 'verified', 'role:Revisor|Admin'])
    ->prefix('revisor')->as('revisor.')
    ->group(function () {

        Route::get('/dashboard', [RevDash::class, 'index'])->name('dashboard');
        Route::get('/revisoes', [RevisorReviews::class, 'index'])->name('reviews.index');
        Route::get('/revisoes/{review}', [RevisorReviews::class, 'show'])->name('reviews.show');
        Route::post('/revisoes/{review}/opinar', [RevisorReviews::class, 'submitOpinion'])->name('reviews.submitOpinion');
    });


/*
|--------------------------------------------------------------------------
| Autor
|--------------------------------------------------------------------------
*/
Route::middleware(['auth', 'verified'])
    ->prefix('autor')->as('autor.')->scopeBindings()
    ->group(function () {

        Route::get('/dashboard', [AutorDash::class, 'index'])->name('dashboard');

        Route::get('/submissoes', [AutorSubmission::class, 'index'])->name('submissions.index');
        Route::get('/submissoes/nova', [AutorSubmission::class, 'create'])->name('submissions.create');
        Route::post('/submissoes', [AutorSubmission::class, 'store'])->name('submissions.store');

        Route::get('/submissoes/{submission:slug}/wizard', [AutorSubmission::class, 'wizard'])->name('submissions.wizard');
        Route::get('/submissoes/{submission:slug}/section/{section}', [AutorSubmission::class, 'editSection'])->name('submissions.section.edit');

        Route::middleware('can:update,submission')->group(function () {

            Route::put('/submissoes/{submission:slug}/section/{section}', [AutorSubmission::class, 'updateSection'])->name('submissoes.section.update');

            Route::post('/submissoes/{submission:slug}/sections/bootstrap', [AutorSubmission::class, 'bootstrapSections'])->name('submissions.sections.bootstrap');
            Route::post('/submissoes/{submission:slug}/sections/reset', [AutorSubmission::class, 'sectionsReset'])->name('submissions.sections.reset');

            Route::post('/submissoes/{submission:slug}/metadata', [AutorSubmission::class, 'updateMetadata'])->name('submissoes.metadata.update');
            Route::post('/submissoes/{submission:slug}/configs', [AutorSubmission::class, 'updateConfigs'])->name('submissoes.configs.update');

            Route::post('/submissoes/{submission:slug}/assets', [AutorSubmission::class, 'assetsStore'])->name('submissions.assets.store');
            Route::post('/submissoes/{submission:slug}/assets/upload', [AutorSubmission::class, 'uploadAsset'])->name('submissoes.assets.upload');
            Route::delete('/submissoes/{submission:slug}/assets/{asset}', [AutorSubmission::class, 'destroyAsset'])->name('submissoes.assets.destroy');

            Route::post('/submissoes/{submission:slug}/refs', [AutorSubmission::class, 'storeReference'])->name('submissoes.refs.store');
            Route::delete('/submissoes/{submission:slug}/refs/{reference}', [AutorSubmission::class, 'destroyReference'])->name('submissoes.refs.destroy');

            Route::post('/submissoes/{submission:slug}/submit', [AutorSubmission::class, 'submit'])->name('submissoes.submit');
            Route::delete('/submissoes/{submission:slug}', [AutorSubmission::class, 'destroy'])->name('submissoes.destroy');

            Route::patch('/submissoes/{submission:slug}/update-after-submit', [AutorSubmission::class, 'updateAfterSubmit'])->name('submissoes.updateAfterSubmit');
        });

        // Notifications
        Route::get('/notifications', [NotificationController::class, 'index'])->name('notifications.index');
        Route::get('/notifications/{notification}', [NotificationController::class, 'show'])->name('notifications.show');
        Route::post('/notifications/{notification}/read', [NotificationController::class, 'markAsRead'])->name('notifications.read');
        Route::post('/notifications/read-all', [NotificationController::class, 'markAllAsRead'])->name('notifications.read_all');
        Route::delete('/notifications/{notification}', [NotificationController::class, 'destroy'])->name('notifications.destroy');

    });

require __DIR__ . '/auth.php';