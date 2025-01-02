<?php

namespace App\Http\Controllers;
use App\Mail\BranchCreated;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class BranchController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $branches = DB::table('branch')->get();
        return view('pages.Branches',compact('branches'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(Request $request)
    {

        $request->validate([
            'branch' => 'required|string|max:255',
        ]);

        // Insert into the database
        $branch = DB::table('branch')->insertGetId([
            'Name' => $request->branch,
            'status' => 0, // Default to Active
        ]);

        // Generate the activation link (modify this URL as per your application)
        $activationLink = url('/activate-branch/'.$branch); // Assuming the URL is like /activate-branch/{id}

        Mail::to('janeesameera@gmail.com')->send(new BranchCreated($request->branch, $activationLink));

        // Return response
        return response()->json(['success' => true, 'message' => 'Branch saved successfully and email sent.']);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }


    public function updateBranch(Request $request)
    {
        // Validate the branch ID
        $request->validate([
            'branch_id' => 'required|integer',
        ]);

        // Update the session with the new branch ID
        session(['branch_id' => $request->branch_id]);


        return response()->json(['success' => true, 'message' => 'Branch updated successfully']);
    }

    public function activateBranch($id)
    {
        // Update the branch status to 1 (Active)
        DB::table('branch')->where('id', $id)->update(['status' => 1]);

        // Redirect to a confirmation page
        return redirect()->route('branch.index')->with('success', 'Branch activated successfully!');
    }

}
