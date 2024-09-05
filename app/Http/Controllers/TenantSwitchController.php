<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Inertia\Inertia;


class TenantSwitchController extends Controller
{
    public function switch(Request $request)
    {
        $request->validate([
            'tenant_id' => 'required|exists:tenants,id',
        ]);

        $tenant = $request->user()->tenants()->findOrFail($request->tenant_id);
        $request->user()->setActiveTenant($tenant);
        $request->session()->flash('success', 'Tenant switched successfully');
        return back()->with('success', 'Tenant switched successfully');
    }
}
