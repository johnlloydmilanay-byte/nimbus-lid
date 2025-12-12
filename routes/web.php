<?php

use App\Http\Controllers\Admission\CollegeController;
use App\Http\Controllers\Admission\JhsController;
use App\Http\Controllers\Admission\PSEController;
use App\Http\Controllers\Admission\ShsController;
use App\Http\Controllers\CampusOnlineLogsController;
use App\Http\Controllers\Cashiering\CollectionsController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\Department\DepartmentStudentListingController;
use App\Http\Controllers\HumanResources\HumanResourcesController;
use App\Http\Controllers\Registrar\StudentManagementController;
use App\Http\Controllers\AccountingController;
use App\Http\Controllers\Accounting\FeesManagementController;
use App\Http\Controllers\Enrollment\SubjectManagerController;
use App\Http\Controllers\Enrollment\CurriculumManagementController;
use App\Http\Controllers\Accounting\InstallmentSchemeController;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

use App\Http\Controllers\LID\ReservationController;

/*
|--------------------------------------------------------------------------
| Public Routes (No Login Required)
|--------------------------------------------------------------------------
*/
Route::get('/elogs', [CampusOnlineLogsController::class, 'index'])->name('elogs.index');
Route::post('/elogs', [CampusOnlineLogsController::class, 'store'])->name('elogs.store');

Route::get('/', function () {
    if (auth()->check()) {
        return app(DashboardController::class)->index();
    }

    return view('login');
})->name('root');

Auth::routes();

/*
|--------------------------------------------------------------------------
| Protected Routes (Require Login)
|--------------------------------------------------------------------------
*/
Route::middleware('auth')->group(function () {
    Route::prefix('elogs/')->group(function () {
        Route::get('/list', [CampusOnlineLogsController::class, 'list'])->name('elogs.list');
    });

    // Admission Routes
    Route::prefix('admission')->group(function () {
        // PSE Routes
        Route::prefix('pse')->group(function () {
            Route::get('/', [PSEController::class, 'index'])->name('admission.pse.index');
            Route::get('/create', [PSEController::class, 'create'])->name('admission.pse.create');
            Route::post('/', [PSEController::class, 'store'])->name('admission.pse.store');

            Route::get('/totalapplicants', [PSEController::class, 'totalapplicants'])->name('admission.pse.totalapplicants');
            Route::get('/totalenrolled', [PSEController::class, 'totalenrolled'])->name('admission.pse.totalenrolled');
            Route::get('/unsched', [PSEController::class, 'unsched'])->name('admission.pse.unsched');

            Route::get('/update/{applicationNumber}', [PSEController::class, 'edit'])->name('admission.pse.edit');
            Route::put('/update/{applicationNumber}', [PSEController::class, 'update'])->name('admission.pse.update');
            Route::delete('/delete/{applicationNumber}', [PSEController::class, 'destroy'])->name('admission.pse.destroy');

            Route::get('/search-students', [PSEController::class, 'searchStudents'])->name('admission.pse.search-students');
            Route::get('/student-details/{id}', [PSEController::class, 'getStudentDetails'])->name('admission.pse.student-details');

            Route::get('/print/{applicationNumber}', [PSEController::class, 'view_print'])->name('admission.pse.view_print');
        });

        // JHS Routes
        Route::prefix('jhs')->group(function () {
            Route::get('/', [JhsController::class, 'index'])->name('admission.jhs.index');
            Route::get('/create', [JhsController::class, 'create'])->name('admission.jhs.create');
            Route::post('/', [JhsController::class, 'store'])->name('admission.jhs.store');

            Route::get('/totalapplicants', [JhsController::class, 'totalapplicants'])->name('admission.jhs.totalapplicants');
            Route::get('/totalenrolled', [JhsController::class, 'totalenrolled'])->name('admission.jhs.totalenrolled');
            Route::get('/unsched', [JhsController::class, 'unsched'])->name('admission.jhs.unsched');

            Route::get('/update/{applicationNumber}', [JhsController::class, 'edit'])->name('admission.jhs.edit');
            Route::put('/update/{applicationNumber}', [JhsController::class, 'update'])->name('admission.jhs.update');
            Route::delete('/delete/{applicationNumber}', [JhsController::class, 'destroy'])->name('admission.jhs.destroy');

            Route::get('/search-students', [JhsController::class, 'searchStudents'])->name('admission.jhs.search-students');
            Route::get('/student-details/{id}', [JhsController::class, 'getStudentDetails'])->name('admission.jhs.student-details');

            Route::get('/print/{applicationNumber}', [JhsController::class, 'view_print'])->name('admission.jhs.view_print');
        });

        // Shs Routes
        Route::prefix('shs')->group(function () {
            Route::get('/', [ShsController::class, 'index'])->name('admission.shs.index');
            Route::get('/create', [ShsController::class, 'create'])->name('admission.shs.create');
            Route::post('/', [ShsController::class, 'store'])->name('admission.shs.store');

            Route::get('/totalapplicants', [ShsController::class, 'totalapplicants'])->name('admission.shs.totalapplicants');
            Route::get('/totalenrolled', [ShsController::class, 'totalenrolled'])->name('admission.shs.totalenrolled');
            Route::get('/unsched', [ShsController::class, 'unsched'])->name('admission.shs.unsched');

            Route::get('/update/{applicationNumber}', [ShsController::class, 'edit'])->name('admission.shs.edit');
            Route::put('/update/{applicationNumber}', [ShsController::class, 'update'])->name('admission.shs.update');
            Route::delete('/delete/{applicationNumber}', [ShsController::class, 'destroy'])->name('admission.shs.destroy');

            Route::get('/search-students', [ShsController::class, 'searchStudents'])->name('admission.shs.search-students');
            Route::get('/student-details/{id}', [ShsController::class, 'getStudentDetails'])->name('admission.shs.student-details');

            Route::get('/print/{applicationNumber}', [ShsController::class, 'view_print'])->name('admission.shs.view_print');
        });

        // College Routes
        Route::prefix('college')->group(function () {
            Route::get('/', [CollegeController::class, 'index'])->name('admission.college.index');
            Route::get('/create', [CollegeController::class, 'create'])->name('admission.college.create');
            Route::post('/', [CollegeController::class, 'store'])->name('admission.college.store');

            Route::get('/totalapplicants', [CollegeController::class, 'totalapplicants'])->name('admission.college.totalapplicants');
            Route::get('/totalenrolled', [CollegeController::class, 'totalenrolled'])->name('admission.college.totalenrolled');
            Route::get('/unsched', [CollegeController::class, 'unsched'])->name('admission.college.unsched');

            Route::get('/update/{applicationNumber}', [CollegeController::class, 'edit'])->name('admission.college.edit');
            Route::put('/update/{applicationNumber}', [CollegeController::class, 'update'])->name('admission.college.update');
            Route::get('/print/{applicationNumber}', [CollegeController::class, 'view_print'])->name('admission.college.view_print');
            Route::delete('/delete/{applicationNumber}', [CollegeController::class, 'destroy'])->name('admission.college.destroy');

            Route::get('/search-students', [CollegeController::class, 'searchStudents'])->name('admission.college.search-students');
            Route::get('/student-details/{id}', [CollegeController::class, 'getStudentDetails'])->name('admission.college.student-details');
        });
    });

    // Accounting Routes
    Route::prefix('accounting/')->group(function () {
        Route::prefix('feesmanagement')->group(function () {
            Route::get('/', [FeesManagementController::class, 'index'])->name('accounting.feesmanagement.index');
            Route::post('/store/tuition', [FeesManagementController::class, 'store_tuition'])->name('accounting.feesmanagement.store.tuition');
            Route::post('/store/post', [FeesManagementController::class, 'store_post'])->name('accounting.feesmanagement.store.post');
            
            Route::post('/fee/{year}/tuition/{department_id}/import', [FeesManagementController::class, 'import_tuition'])->name('fees.import.tuition');
            Route::put('/update/tuition', [FeesManagementController::class, 'update_tuition'])->name('accounting.feesmanagement.update.tuition');
            Route::delete('/delete/tuition', [FeesManagementController::class, 'destroy_tuition'])->name('accounting.feesmanagement.destroy.tuition');

            Route::post('/fee/{year}/post/import', [FeesManagementController::class, 'import_post'])->name('fees.import.post');
            Route::put('/update/post', [FeesManagementController::class, 'update_post'])->name('accounting.feesmanagement.update.post');
            Route::delete('/delete/post', [FeesManagementController::class, 'destroy_post'])->name('accounting.feesmanagement.destroy.post');
        });
        Route::prefix('installmentscheme')->group(function () {
            Route::get('/', [InstallmentSchemeController::class, 'index'])->name('accounting.installmentscheme.index');
            Route::post('/store', [InstallmentSchemeController::class, 'store'])->name('accounting.installmentscheme.store');
            Route::put('/update/{id}', [InstallmentSchemeController::class, 'update'])->name('accounting.installmentscheme.update');
            Route::delete('/delete/{id}', [InstallmentSchemeController::class, 'destroy'])->name('accounting.installmentscheme.destroy');

            Route::post('/store/manage', [InstallmentSchemeController::class, 'store_manage'])->name('accounting.installmentscheme.save_manage');
            Route::post('/store/{id}/fees', [InstallmentSchemeController::class, 'store_fees'])->name('accounting.installmentscheme.store_fees');
        });
    });

    // Registar Routes
    Route::prefix('department')->group(function () {
        Route::prefix('studentlisting')->group(function () {
            Route::get('/', [DepartmentStudentListingController::class, 'index'])->name('department.studentlisting.index');
            Route::get('/view/{application_number}', [DepartmentStudentListingController::class, 'view'])->name('department.studentlisting.view');
            Route::patch('/department/student/{id}/update-remarks', [DepartmentStudentListingController::class, 'updateRemarks'])->name('department.student.updateRemarks');
        });
    });

    // Enrollment Routes
    Route::prefix('enrollment/')->group(function () {
        Route::prefix('subjectmanager')->group(function () {
            Route::get('/', [SubjectManagerController::class, 'index'])->name('enrollment.subjectmanager.index');
            Route::post('/store', [SubjectManagerController::class, 'store'])->name('enrollment.subjectmanager.store');
            Route::get('/get-programs/{deptId}', [SubjectManagerController::class, 'getPrograms'])->name('enrollment.subjectmanager.getPrograms');
            Route::post('/update/{id}', [SubjectManagerController::class, 'update'])->name('enrollment.subjectmanager.update');
            Route::delete('/delete/{id}', [SubjectManagerController::class, 'destroy']) ->name('enrollment.subjectmanager.destroy');
        });
        Route::prefix('curriculum')->group(function () {
            Route::get('/', [CurriculumManagementController::class, 'index'])->name('enrollment.curriculum.index');
            Route::get('/get-programs/{deptId}', [CurriculumManagementController::class, 'getPrograms'])->name('enrollment.curriculum.getPrograms');
            Route::post('/store', [CurriculumManagementController::class, 'store'])->name('enrollment.curriculum.store');
            Route::post('/update', [CurriculumManagementController::class, 'update'])->name('enrollment.curriculum.update');
            Route::get('/manage/{program}/{curriculumyear}', [CurriculumManagementController::class, 'manage'])->name('enrollment.curriculum.manage');
            Route::post('/manageStore', [CurriculumManagementController::class, 'manageStore'])->name('enrollment.curriculum.manageStore');
            Route::delete('/delete/{id}', [CurriculumManagementController::class, 'destroy']) ->name('enrollment.curriculum.destroy');
        });
    });

    // Cashiering Routes
    Route::prefix('cashier')->group(function () {
        Route::prefix('collections')->group(function () {
            Route::get('/', [CollectionsController::class, 'index'])->name('cashiering.collections.index');
            Route::post('/search', [CollectionsController::class, 'search'])->name('cashiering.collections.search');
            Route::post('/store', [CollectionsController::class, 'store'])->name('cashiering.collections.store');
            Route::get('/receipt/{or_number}', [CollectionsController::class, 'receipt'])->name('cashiering.collections.receipt');
            Route::get('/paymentfor/{paymentcode_id}', [CollectionsController::class, 'getPaymentFor']);
        });
    });

    // Registar Routes
    Route::prefix('registrar')->group(function () {
        Route::get('/student', [StudentManagementController::class, 'index'])->name('registrar.studentmanagement.index');
        Route::get('/student/create', [StudentManagementController::class, 'create'])->name('registrar.studentmanagement.create');
        Route::get('/get-programs/{departmentId}', [StudentManagementController::class, 'getProgramsByDepartment'])->name('get.programs');
        Route::get('/student/search', [StudentManagementController::class, 'search'])->name('registrar.studentmanagement.search');
        Route::post('/student/store', [StudentManagementController::class, 'store'])->name('registrar.studentmanagement.store');
        Route::get('/studentmanagement/edit/{applicationNumber}', [StudentManagementController::class, 'edit'])->name('registrar.studentmanagement.edit');
        Route::put('/studentmanagement/edit/{applicationNumber}', [StudentManagementController::class, 'update'])->name('registrar.studentmanagement.update');

        Route::patch('/studentmanagement/edit/{applicationNumber}/college/update', [StudentManagementController::class, 'collegeRequirementUpdate'])->name('registrar.studentmanagement.collegeRequirementUpdate');
        Route::patch('/studentmanagement/edit/{applicationNumber}/shs/update', [StudentManagementController::class, 'shsRequirementUpdate'])->name('registrar.studentmanagement.shsRequirementUpdate');
        Route::patch('/studentmanagement/edit/{applicationNumber}/jhs/update', [StudentManagementController::class, 'jhsRequirementUpdate'])->name('registrar.studentmanagement.jhsRequirementUpdate');
        Route::patch('/studentmanagement/edit/{applicationNumber}/pse/update', [StudentManagementController::class, 'pseRequirementUpdate'])->name('registrar.studentmanagement.pseRequirementUpdate');
    });

    // Human Resources Routes
    Route::prefix('hr/employees')->group(function () {
        Route::get('/', [HumanResourcesController::class, 'index'])->name('hr.index');
        Route::get('/create', [HumanResourcesController::class, 'create'])->name('hr.create');
        Route::post('/', [HumanResourcesController::class, 'store'])->name('hr.store');
    });

   // LID – Laboratory & Inventory Department
    Route::prefix('lid')->name('lid.')->group(function () {
        Route::get('/reservations', [ReservationController::class, 'index'])->name('reservations.index');
        Route::get('/reservations/create', [ReservationController::class, 'create'])->name('reservations.create');
        Route::post('/reservations', [ReservationController::class, 'store'])->name('reservations.store');
    });

});
