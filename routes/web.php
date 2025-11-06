<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ProfileController;

// Dashboards por papel
use App\Http\Controllers\Admin\DashboardController       as AdminDash;
use App\Http\Controllers\Coordenador\DashboardController as CoordDash;
use App\Http\Controllers\Revisor\DashboardController     as RevDash;
use App\Http\Controllers\Autor\DashboardController       as AutorDash;


use App\Http\Controllers\Coordenador\DashboardController as CoordDashboard;
use App\Http\Controllers\Coordenador\RevisorController    as CoordRevisores;
use App\Http\Controllers\Coordenador\SubmissionsController as CoordSubs;

use App\Http\Controllers\Admin\ReportsController;

use App\Http\Controllers\Autor\SubmissionController as AutorSubmission;
use App\Http\Controllers\Admin\SubmissionController as AdminSubmission;
// Autor – Submissões

use App\Http\Controllers\Revisor\ReviewsController as RevisorReviews;
use App\Http\Controllers\Common\SubmissionCommentController as CommentCtrl;


use App\Http\Controllers\Admin\UserController as AdminUserController;

// Home pública
Route::get('/', fn () => view('welcome'));

//SE ALGUM ALUNO FOR VER AQUI, CUIDADO COM AS ROTAS ABAIXO!

// /dashboard redireciona conforme papel
Route::get('/dashboard', function () {
    $u = auth()->user();
    if (!$u) return redirect()->route('login');

    if ($u->hasRole('Admin'))       return redirect()->route('admin.dashboard');
    if ($u->hasRole('Coordenador')) return redirect()->route('coordenador.dashboard');
    if ($u->hasRole('Revisor'))     return redirect()->route('revisor.dashboard');
    if ($u->hasRole('Autor'))       return redirect()->route('autor.dashboard');

    return view('dashboard-sem-papel', ['user' => $u, 'roles' => $u->getRoleNames()]);
})->middleware(['auth','verified'])->name('dashboard');

// Perfil (padrão)
Route::middleware('auth')->group(function () {
    Route::get('/profile',  [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile',[ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile',[ProfileController::class, 'destroy'])->name('profile.destroy');
});

//Admin

Route::middleware(['auth','verified','role:Admin'])
    ->prefix('admin')->as('admin.')
    ->group(function () {
        Route::get('/dashboard', [AdminDash::class, 'index'])->name('dashboard');

         Route::prefix('submissoes')->as('submissions.')->group(function () {
            Route::get('/',                 [AdminSubmission::class,'index'])->name('index');
            Route::get('/{submission}',     [AdminSubmission::class,'show'])->name('show');
            Route::post('/{submission}/transition', [AdminSubmission::class,'transition'])->name('transition');


            Route::get('/{submission}/ler',          [AdminSubmission::class,'read'])->name('read');
            Route::get('/{submission}/comments',     [AdminSubmission::class,'commentsIndex'])->name('comments.index');
            Route::post('/{submission}/comments',    [AdminSubmission::class,'commentsStore'])->name('comments.store');
            Route::delete('/{submission:slug}/comments/{comment}', [\App\Http\Controllers\Admin\SubmissionController::class,'commentsDestroy'])
                ->whereNumber('comment')
                ->name('comments.destroy');

        });

        Route::resource('users', AdminUserController::class)
             ->parameters(['users' => 'user'])
             ->names('users');


        Route::prefix('triagem')->as('triage.')->group(function () {
            Route::get('/', [\App\Http\Controllers\Admin\TriagemController::class,'index'])->name('index');
            Route::get('/{submission}', [\App\Http\Controllers\Admin\TriagemController::class,'show'])->name('show');
        });

        Route::prefix('categorias')->as('categories.')->group(function () {
            Route::get('/',              [\App\Http\Controllers\Admin\CategoryController::class,'index'])->name('index');
            Route::post('/',             [\App\Http\Controllers\Admin\CategoryController::class,'store'])->name('store');
            Route::get('/{category}/editar', [\App\Http\Controllers\Admin\CategoryController::class,'edit'])->name('edit');
            Route::put('/{category}',    [\App\Http\Controllers\Admin\CategoryController::class,'update'])->name('update');
            Route::delete('/{category}', [\App\Http\Controllers\Admin\CategoryController::class,'destroy'])->name('destroy');
        });

        Route::get('/relatorios', [ReportsController::class, 'index'])->name('reports.index');



    });

//Coordenador

Route::middleware(['auth'])->group(function () {

    Route::prefix('coordenador')->name('coordenador.')->middleware(['role:Coordenador|Admin'])->group(function () {

        Route::get('/dashboard', [CoordDashboard::class, 'index'])->name('dashboard');

        Route::resource('revisores', CoordRevisores::class)
             ->parameters(['revisores' => 'user'])
             ->except(['show']);

        Route::get('/submissoes',         [CoordSubs::class,'index'])->name('submissions.index');
        Route::get('/submissoes/{submission}', [CoordSubs::class,'show'])->name('submissions.show');
    });;

    Route::get('/submissions/{submission:slug}/comments', [CommentCtrl::class, 'index'])
        ->name('comments.index');
    Route::post('/submissions/{submission:slug}/comments', [CommentCtrl::class,'store'])->name('comments.store');
    Route::patch('/submissions/{submission:slug}/comments/{comment}/author-resolved', [CommentCtrl::class,'authorResolved'])->whereNumber('comment')->name('comments.author_resolved');
    Route::patch('/submissions/{submission:slug}/comments/{comment}/verify', [CommentCtrl::class,'verify'])->whereNumber('comment')->name('comments.verify');
    Route::delete('/submissions/{submission:slug}/comments/{comment}', [CommentCtrl::class, 'destroy'])
    ->whereNumber('comment')
    ->name('comments.destroy');


});

/*
|--------------------------------------------------------------------------
| Revisor
|--------------------------------------------------------------------------
*/
Route::middleware(['auth','verified','role:Revisor'])
    ->prefix('revisor')->as('revisor.')
    ->group(function () {
        Route::get('/dashboard', [RevDash::class, 'index'])->name('dashboard');

        Route::get('/revisoes',          [RevisorReviews::class,'index'])->name('reviews.index');
        Route::get('/revisoes/{review}', [RevisorReviews::class,'show'])->name('reviews.show');

        // nome usado no Blade: route('revisor.reviews.submitOpinion', $review)
        Route::post('/revisoes/{review}/opinar', [RevisorReviews::class,'submitOpinion'])
            ->name('reviews.submitOpinion');

    });

/*
|--------------------------------------------------------------------------
| Autor
|--------------------------------------------------------------------------
*/
Route::middleware('auth')
    ->prefix('autor')
    ->as('autor.')
    ->scopeBindings()
    ->group(function () {

        // Dashboard do autor
        Route::view('/dashboard', 'autor.dashboard')->name('dashboard');

        // Submissões do autor
        // listagem + criação
        Route::get('/submissoes',            [AutorSubmission::class, 'index'])->name('submissions.index');
        Route::get('/submissoes/nova',       [AutorSubmission::class, 'create'])->name('submissions.create');
        Route::post('/submissoes',           [AutorSubmission::class, 'store'])->name('submissions.store');

        // wizard
        Route::get('/submissoes/{submission}/wizard', [AutorSubmission::class, 'wizard'])->name('submissions.wizard');

        // seções do wizard
        Route::get('/submissoes/{submission}/section/{section}/edit',  [AutorSubmission::class, 'editSection'])->name('submissions.section.edit');
        Route::put('/submissoes/{submission}/section/{section}',       [AutorSubmission::class, 'updateSection'])->name('submissions.section.update');
        Route::post('/submissoes/{submission}/sections/bootstrap',     [AutorSubmission::class, 'bootstrapSections'])->name('submissions.sections.bootstrap');
        Route::post('/submissoes/{submission}/sections/reset',         [AutorSubmission::class, 'sectionsReset'])->name('submissions.sections.reset');

        // metadata + categorias
        Route::post('/submissoes/{submission}/metadata',  [AutorSubmission::class, 'updateMetadata'])->name('submissions.metadata.update');
        Route::post('/submissoes/{submission}/configs',   [AutorSubmission::class, 'updateConfigs'])->name('submissions.configs.update');

        // assets/anexos
        Route::post('/submissoes/{submission}/assets',          [AutorSubmission::class, 'assetsStore'])->name('submissions.assets.store');
        Route::post('/submissoes/{submission}/assets/upload',   [AutorSubmission::class, 'uploadAsset'])->name('submissions.assets.upload');
        Route::delete('/submissoes/{submission}/assets/{asset}',[AutorSubmission::class, 'destroyAsset'])->name('submissions.assets.destroy');

        // referências
        Route::post('/submissoes/{submission}/refs',                [AutorSubmission::class, 'storeReference'])->name('submissions.refs.store');
        Route::delete('/submissoes/{submission}/refs/{reference}',  [AutorSubmission::class, 'destroyReference'])->name('submissions.refs.destroy');

        Route::post('/submissions/{submission:slug}/comments', [CommentCtrl::class, 'store'])
        ->name('comments.store');

        // Autor marca como resolvido (sem fechar)
        Route::patch('/submissions/{submission:slug}/comments/{comment}/author-resolved', [CommentCtrl::class, 'authorResolved'])
            ->name('comments.author_resolved');

        // Revisor/Admin verifica e fecha (ou reabre)
        Route::patch('/submissions/{submission:slug}/comments/{comment}/verify', [CommentCtrl::class, 'verify'])
            ->name('comments.verify');

        // enviar e excluir
        Route::post('/submissoes/{submission}/submit', [AutorSubmission::class, 'submit'])->name('submissions.submit');
        Route::delete('/submissoes/{submission}',      [AutorSubmission::class, 'destroy'])->name('submissions.destroy');
    });

require __DIR__.'/auth.php';
