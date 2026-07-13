<?php

namespace App\Http\Controllers\Admin;

use Setting;
use App\Enums\Status;
use App\Models\Visitor;
use App\Models\Employee;
use App\Models\Person;
use App\Models\Facility;
use App\Models\PreRegister;
use App\Models\ResidentProfile;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use Yajra\DataTables\DataTables;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;
use App\Http\Requests\PreRegisterRequest;
use SimpleSoftwareIO\QrCode\Facades\QrCode;
use App\Http\Services\PreRegister\PreRegisterService;
use App\Http\Controllers\BackendController;

class PreRegisterController extends BackendController
{
    protected $preRegisterService;

    public function __construct(PreRegisterService $preRegisterService)
    {
        $this->preRegisterService = $preRegisterService;

        $this->middleware('auth');
        $this->data['sitetitle'] = 'Pre-registers';
        $this->middleware(['permission:pre-registers'])->only('index');
        $this->middleware(['permission:pre-registers_create'])->only('create', 'store');
        $this->middleware(['permission:pre-registers_edit'])->only('edit', 'update');
        $this->middleware(['permission:pre-registers_delete'])->only('destroy');
        $this->middleware(['permission:pre-registers_show'])->only('show');
    }

    public function index(Request $request)
    {
        $preRegisters = PreRegister::with(['visitor', 'employee.user', 'person', 'facility'])
            ->orderBy('created_at', 'desc')
            ->paginate(20);
            
        return view('admin.pre-registers.index', compact('preRegisters'));
    }

    public function create(Request $request)
    {
        $employees = Employee::with('user')->where('status', Status::ACTIVE)->get();
        $residents = Person::whereHas('residentProfile')->get();
        $facilities = Facility::where('is_active', true)->get();
        
        $this->data['employees'] = $employees;
        $this->data['residents'] = $residents;
        $this->data['facilities'] = $facilities;

        return view('admin.pre-registers.create', $this->data);
    }

    public function store(PreRegisterRequest $request)
    {
        $preRegister = $this->preRegisterService->make($request);

        if (setting('whatsapp_message')) {
            return redirect()->route('admin.pre-registers.show', $preRegister->id);
        }

        return redirect()->route('admin.pre-registers.index')->withSuccess('The data inserted successfully!');
    }

    public function show($id)
    {
        $this->data['preregister'] = $this->preRegisterService->find($id);
        if ($this->data['preregister']) {
            return view('admin.pre-registers.show', $this->data);
        } else {
            return redirect()->route('admin.pre-registers.index');
        }
    }

    public function edit($id)
    {
        $employees = Employee::with('user')->where('status', Status::ACTIVE)->get();
        $residents = Person::whereHas('residentProfile')->get();
        $facilities = Facility::where('is_active', true)->get();
        
        $this->data['employees'] = $employees;
        $this->data['residents'] = $residents;
        $this->data['facilities'] = $facilities;
        $this->data['preRegister'] = $this->preRegisterService->find($id);
        
        if ($this->data['preRegister']) {
            return view('admin.pre-registers.edit', $this->data);
        } else {
            return redirect()->route('admin.pre-registers.index');
        }
    }

    public function update(PreRegisterRequest $request, PreRegister $preRegister)
    {
        $this->preRegisterService->update($request, $preRegister->id);
        return redirect()->route('admin.pre-registers.index')->withSuccess('The data updated successfully!');
    }

    public function destroy($id)
    {
        $this->preRegisterService->delete($id);
        return redirect()->route('admin.pre-registers.index')->withSuccess('The data deleted successfully!');
    }

    public function getPreRegister(Request $request)
    {
        $pre_registers = $this->preRegisterService->all($request);
        $i = 1;
        $pre_registerArray = [];
        if (!blank($pre_registers)) {
            foreach ($pre_registers as $pre_register) {
                $pre_registerArray[$i] = $pre_register;
                $pre_registerArray[$i]['setID'] = $i;
                $i++;
            }
        }
        return Datatables::of($pre_registerArray)
            ->addColumn('action', function ($pre_register) {
                $retAction = '';

                if (auth()->user()->can('pre-registers_show')) {
                    $retAction .= '<a href="' . route('admin.pre-registers.show', $pre_register) . '" class="btn btn-sm btn-icon mr-2 float-left btn-info" data-toggle="tooltip" data-placement="top" title="View"><i class="far fa-eye"></i></a>';
                }

                if (auth()->user()->can('pre-registers_edit')) {
                    $retAction .= '<a href="' . route('admin.pre-registers.edit', $pre_register) . '" class="btn btn-sm btn-icon float-left btn-primary" data-toggle="tooltip" data-placement="top" title="Edit"> <i class="far fa-edit"></i></a>';
                }

                if (auth()->user()->can('pre-registers_delete')) {
                    $retAction .= '<form class="float-left pl-2" action="' . route('admin.pre-registers.destroy', $pre_register) . '" method="POST">' . method_field('DELETE') . csrf_field() . '<button class="btn btn-sm btn-icon btn-danger" onclick="return confirmDelete()" data-toggle="tooltip" data-placement="top" title="Delete"> <i class="fa fa-trash"></i></button></form>';
                }

                return $retAction;
            })
            ->editColumn('name', function ($pre_register) {
                return Str::limit(optional($pre_register->visitor)->name, 50);
            })
            ->editColumn('email', function ($pre_register) {
                return Str::limit(optional($pre_register->visitor)->email, 50);
            })
            ->editColumn('phone', function ($pre_register) {
                return Str::limit(optional($pre_register->visitor)->country_code . optional($pre_register->visitor)->phone, 50);
            })
            ->editColumn('employee_id', function ($pre_register) {
                return optional($pre_register->employee->user)->name;
            })
            ->editColumn('expected_date', function ($pre_register) {
                if (optional($pre_register->visitor)->is_pre_register == 1) {
                    $date = '<p class="text-danger">' . $pre_register->expected_date . '</p>';
                } else {
                    $date = '<p>' . $pre_register->expected_date . '</p>';
                }
                return $date;
            })
            ->editColumn('expected_time', function ($pre_register) {
                if (optional($pre_register->visitor)->is_pre_register == 1) {
                    $time = '<p class="text-danger">' . date('h:i A', strtotime($pre_register->expected_time)) . '</p>';
                } else {
                    $time = '<p>' . date('h:i A', strtotime($pre_register->expected_time)) . '</p>';
                }
                return $time;
            })
            ->editColumn('id', function ($pre_register) {
                return $pre_register->setID;
            })
            ->rawColumns(['name', 'action'])
            ->escapeColumns([])
            ->make(true);
    }
}