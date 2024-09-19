<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Illuminate\Validation\Rule;



class CustomerController extends Controller
{

    public function __construct()
    {
        $this->authorizeResource(Customer::class, 'customer');
    }
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        if (auth()->user()->hasActiveTenant()) {
            $customers = Customer::ofActiveTenant()
                ->when($request->search, function ($query, $search) {
                    $query->where('name', 'like', '%' . $search . '%');
                })
                ->paginate(10);
            return Inertia::render('Customers/Index', [
                'customers' => $customers,
                'filters' => request()->all('search'),
            ]);
        }else{
            return Inertia::render('Customers/Index', ['customers' => []]);
        }
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return Inertia::render('Customers/Create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $attributes = $this->validateCustomer($request);
        $attributes['tenant_id'] = auth()->user()->getActiveTenant()->id;
        Customer::create($attributes);
        return redirect()->route('customers.index')->with('success', 'Customer created successfully.');

    }

    /**
     * Display the specified resource.
     */
    public function show(Customer $customer)
    {
        return Inertia::render('Customers/Show', ['customer' => $customer]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Customer $customer)
    {
        return Inertia::render('Customers/Edit', ['customer' => $customer]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Customer $customer)
    {
        $attributes = $this->validateCustomer($request, $customer);
        $customer->update($attributes);
        return redirect()->route('customers.index')->with('success', 'Customer updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Customer $customer)
    {
        $customer->delete();
    }

    public function search(Request $request)
    {
        $customers = Customer::ofActiveTenant()
            ->where('name', 'like', '%' . $request->search . '%')
            ->limit(20)
            ->get();
        return response()->json($customers);
    }



    private function validateCustomer(Request $request, Customer $customer = null)
    {
        $attributes = $this->validate($request, [
            'name' => 'required|string|min:3|max:255',
            'email' => 'email|unique:users,email,' . $customer?->id ,
            'phone' => 'nullable|string|max:255',
            'address' => 'nullable|string|max:255',
        ]);

        return $attributes;
    }
}
