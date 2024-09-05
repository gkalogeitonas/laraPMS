<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Inertia\Inertia;
use Symfony\Component\HttpFoundation\Response;

class TenantSwitchController extends Controller
{
    public function switch(Request $request)
    {
        $request->validate([
            'tenant_id' => 'required|exists:tenants,id',
        ]);

        $tenant = $request->user()->tenants()->find($request->tenant_id);

        if (!$tenant) {
            return response()->json(['error' => 'Tenant not found or does not belong to the user'], Response::HTTP_FORBIDDEN);
        }

        $request->user()->setActiveTenant($tenant);
        $request->session()->flash('success', 'Tenant switched successfully');

        return back()->with('success', 'Tenant switched successfully');
    }
}
