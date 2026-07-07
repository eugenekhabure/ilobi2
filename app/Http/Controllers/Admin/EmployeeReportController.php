<?php

namespace App\Http\Controllers\Admin;

use App\Enums\Status;
use App\Models\Employee;
use App\Models\Department;
use App\Models\Designation;
use Illuminate\Http\Request;
use App\Http\Controllers\BackendController;

class EmployeeReportController extends BackendController
{
    public function __construct()
    {
        parent::__construct();
        $this->data['sitetitle'] = 'Employee Report';
        $this->middleware(['permission:employee-report'])->only('index');
    }

    public function index(Request $request)
    {
        $this->data['showView']        = false;
        $this->data['all_department']  = Department::where('status', Status::ACTIVE)->get();
        $this->data['all_designation'] = Designation::where('status', Status::ACTIVE)->get();
        $this->data['set_department']  = '';
        $this->data['set_designation'] = '';
        $this->data['set_gender']      = '';

        if ($_POST) {
            $request->validate([
                'department'  => 'nullable|numeric',
                'designation' => 'nullable|numeric',
                'gender'      => 'nullable|numeric',
            ]);

            $this->data['showView']        = true;
            $this->data['set_department']  = $request->department;
            $this->data['set_designation'] = $request->designation;
            $this->data['set_gender']      = $request->gender;

            $query = Employee::query();
            if (auth()->user()->id != 1) {
                $query->where('added_by', auth()->user()->id);
            }

            if ($request->filled('department')) {
                $query->where('department_id', $request->department);
            }

            if ($request->filled('designation')) {
                $query->where('designation_id', $request->designation);
            }

            if ($request->filled('gender')) {
                $query->where('gender', $request->gender);
            }

            $this->data['employees'] = $query->orderBy('id', 'DESC')->get();
        }

        return view('admin.report.employee.index', $this->data);
    }
}
