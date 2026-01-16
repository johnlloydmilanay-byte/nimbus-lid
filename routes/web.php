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
use App\Http\Controllers\Registrar\OtrController;
use App\Http\Controllers\PPFMO\ServiceRequestController;

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

        // Registrar Routes
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

        // --- OTR Management Routes ---
        Route::get('/otr', [OtrController::class, 'index'])->name('registrar.otr.index');
        Route::get('/otr/create', [OtrController::class, 'create'])->name('registrar.otr.create');
        Route::post('/otr', [OtrController::class, 'store'])->name('registrar.otr.store');
        Route::get('/otr/{id}', [OtrController::class, 'show'])->name('registrar.otr.show');
        Route::get('/otr/{id}/edit', [OtrController::class, 'edit'])->name('registrar.otr.edit');
        Route::put('/otr/{id}', [OtrController::class, 'update'])->name('registrar.otr.update');
        Route::delete('/otr/{id}', [OtrController::class, 'destroy'])->name('registrar.otr.destroy');
        
        // OTR Specific Actions
        Route::get('/otr/{id}/pdf', [OtrController::class, 'generatePDF'])->name('registrar.otr.pdf');
        Route::get('/registrar/otr/{id}/export-grades', [OtrController::class, 'exportGradesExcel'])->name('registrar.otr.export-grades');
        Route::get('/otr/search', [OtrController::class, 'search'])->name('registrar.otr.search');

        // === ADD IMPORT ROUTES HERE ===
        Route::get('/otr/import', [OtrController::class, 'importForm'])->name('registrar.otr.import');
        Route::post('/otr/import', [OtrController::class, 'import'])->name('registrar.otr.import.process');
        Route::get('/otr/import/template', [OtrController::class, 'downloadTemplate'])->name('registrar.otr.import.template');
        // ==============================

        // Add Grade Routes
        Route::get('/otr/{id}/grade/add', [OtrController::class, 'addGradeForm'])->name('registrar.otr.grade.add');
        Route::post('/otr/{id}/grade', [OtrController::class, 'storeGrade'])->name('registrar.otr.grade.store');
        Route::get('/otr/{id}/grade/{gradeId}/edit', [OtrController::class, 'editGradeForm'])->name('registrar.otr.grade.edit');
        Route::put('/otr/{id}/grade/{gradeId}', [OtrController::class, 'updateGrade'])->name('registrar.otr.grade.update');
        Route::delete('/otr/{id}/grade/{gradeId}', [OtrController::class, 'deleteGrade'])->name('registrar.otr.grade.delete');
        Route::post('/otr/{id}/grade/import', [OtrController::class, 'importGrades'])->name('registrar.otr.grade.import');
        Route::post('/otr/{id}/grade/store-multiple', [OtrController::class, 'storeMultipleGrades'])->name('registrar.otr.grade.store-multiple');
        Route::post('/otr/{id}/grades/bulk', [OtrController::class, 'bulkStoreGrades'])->name('registrar.otr.grade.bulk.store');    

        // Import routes
        Route::get('registrar/otr/import', [OtrController::class, 'showImportForm'])->name('registrar.otr.import');
        Route::post('registrar/otr/import', [OtrController::class, 'import'])->name('registrar.otr.import.store');
        Route::get('registrar/otr/template', [OtrController::class, 'downloadTemplate'])->name('registrar.otr.template');
        Route::get('registrar/otr/template/generate', [OtrController::class, 'generateTemplate'])->name('registrar.otr.template.generate');
                
        });

    // Human Resources Routes
    Route::prefix('hr/employees')->group(function () {
        Route::get('/', [HumanResourcesController::class, 'index'])->name('hr.index');
        Route::get('/create', [HumanResourcesController::class, 'create'])->name('hr.create');
        Route::post('/', [HumanResourcesController::class, 'store'])->name('hr.store');
    });

    // LID – Laboratory & Inventory Department
    Route::prefix('lid')->name('lid.')->group(function () {
        // Reservation routes
        Route::get('/reservations', [ReservationController::class, 'index'])->name('reservations.index');
        Route::get('/reservations/create', [ReservationController::class, 'create'])->name('reservations.create');
        Route::post('/reservations', [ReservationController::class, 'store'])->name('reservations.store');
        
        // Student reservation routes
        Route::get('/reservations/student-create', [ReservationController::class, 'studentCreate'])->name('student-reservations.create');
        Route::post('/reservations/student', [ReservationController::class, 'storeStudentReservation'])->name('student-reservations.store');
        Route::get('/reservations/faculty/{reference}', [ReservationController::class, 'getFacultyReservation'])->name('reservations.get-faculty');
        
        Route::get('/reservations/{id}', [ReservationController::class, 'show'])->name('reservations.show');
        Route::get('/reservations/{id}/edit', [ReservationController::class, 'edit'])->name('reservations.edit');
        Route::put('/reservations/{id}', [ReservationController::class, 'update'])->name('reservations.update');
        Route::post('/reservations/{id}/status', [ReservationController::class, 'updateStatus'])->name('reservations.updateStatus');
        Route::delete('/reservations/{id}', [ReservationController::class, 'destroy'])->name('reservations.destroy');

        // Chemical management routes
        Route::get('/chemicals', [ChemicalController::class, 'index'])->name('chemicals.index');
        Route::get('/chemicals/create', [ChemicalController::class, 'create'])->name('chemicals.create');
        Route::post('/chemicals', [ChemicalController::class, 'store'])->name('chemicals.store');
        Route::get('/chemicals/{id}/edit', [ChemicalController::class, 'edit'])->name('chemicals.edit');
        Route::put('/chemicals/{id}', [ChemicalController::class, 'update'])->name('chemicals.update');
        Route::delete('/chemicals/{id}', [ChemicalController::class, 'destroy'])->name('chemicals.destroy');

        // PDEA Chemical management routes
        Route::get('/pdea-chemicals', [PDEAChemicalController::class, 'index'])->name('pdea-chemicals.index');
        Route::get('/pdea-chemicals/create', [PDEAChemicalController::class, 'create'])->name('pdea-chemicals.create');
        Route::post('/pdea-chemicals', [PDEAChemicalController::class, 'store'])->name('pdea-chemicals.store');
        Route::get('/pdea-chemicals/{id}/edit', [PDEAChemicalController::class, 'edit'])->name('pdea-chemicals.edit');
        Route::put('/pdea-chemicals/{id}', [PDEAChemicalController::class, 'update'])->name('pdea-chemicals.update');
        Route::delete('/pdea-chemicals/{id}', [PDEAChemicalController::class, 'destroy'])->name('pdea-chemicals.destroy');


        Route::get('/glassware', [GlasswareController::class, 'index'])->name('glassware.index');
        Route::get('/glassware/create', [GlasswareController::class, 'create'])->name('glassware.create');
        Route::post('/glassware', [GlasswareController::class, 'store'])->name('glassware.store');
        Route::get('/glassware/{id}/edit', [GlasswareController::class, 'edit'])->name('glassware.edit');
        Route::put('/glassware/{id}', [GlasswareController::class, 'update'])->name('glassware.update');
        Route::delete('/glassware/{id}', [GlasswareController::class, 'destroy'])->name('glassware.destroy');

        Route::get('/equipment', [EquipmentController::class, 'index'])->name('equipment.index');
        Route::get('/equipment/create', [EquipmentController::class, 'create'])->name('equipment.create');
        Route::post('/equipment', [EquipmentController::class, 'store'])->name('equipment.store');
        Route::get('/equipment/{id}/edit', [EquipmentController::class, 'edit'])->name('equipment.edit');
        Route::put('/equipment/{id}', [EquipmentController::class, 'update'])->name('equipment.update');
        Route::delete('/equipment/{id}', [EquipmentController::class, 'destroy'])->name('equipment.destroy');

        Route::get('/reservations/{id}/edit-modal', [ReservationController::class, 'editModal'])->name('reservations.edit-modal');

        // Dashboard route
        Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard.index');
    });

    // PPFMO Service Requests Routes
    Route::prefix('ppfmo')->name('ppfmo.')->group(function () {
        Route::prefix('service-requests')->name('service-requests.')->group(function () {
            Route::get('/', [ServiceRequestController::class, 'index'])->name('index');
            Route::get('/create', [ServiceRequestController::class, 'create'])->name('create');
            Route::post('/', [ServiceRequestController::class, 'store'])->name('store');
            Route::get('/{serviceRequest}', [ServiceRequestController::class, 'show'])->name('show');
            Route::get('/{serviceRequest}/edit', [ServiceRequestController::class, 'edit'])->name('edit');
            Route::put('/{serviceRequest}', [ServiceRequestController::class, 'update'])->name('update');
            Route::delete('/{serviceRequest}', [ServiceRequestController::class, 'destroy'])->name('destroy');
            Route::get('/get-reports-by-type', [ServiceRequestController::class, 'getReportsByType'])->name('get-reports-by-type');
        });
        
    });

});
