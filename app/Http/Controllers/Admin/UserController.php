<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;

class UserController extends Controller
{
    public function userList(Request $request)
    {

        if ($request->ajax()) {

            // Page Length
            $pageNumber = ($request->start / $request->length) + 1;
            $pageLength = $request->length;
            $skip = ($pageNumber - 1) * $pageLength;
            $search = $request->search['value'];
            // $order = $request->order[0]['column'];
            $dir = $request->order[0]['dir'];
            // $column = $request->columns[$order]['data'];

            $users = User::where('role','user')->orderBy('created_at', $dir);

            if ($search) {
                $users->where(function ($q) use ($search) {
                    $q->orWhere('name', 'like', '%' . $search . '%');
                    $q->orWhere('email', 'like', '%' . $search . '%');
                    $q->orWhere('mobile_no', 'like', '%' . $search . '%');
                });
            }
            $total = $users->count();
            $users = $users->skip($skip)->take($pageLength)->get();
            $return = [];
            foreach ($users as $key => $user) {

                $action_buttons = "<a href='#' title='View' class='btn btn-sm btn-primary'>View</a>&nbsp;&nbsp;<a href='#' title='Edit' class='btn btn-sm btn-success'>Edit </a>";

                // fetch trade status
                $return[] = [
                    'id' => $key+1,
                    'name' => $user->name,
                    'email' => $user->email,
                    'mobile_no' => $user->mobile_no,
                    'actions' => $action_buttons,
                ];
            }
            return response()->json([
                'draw' => $request->draw,
                'recordsTotal' => $total,
                'recordsFiltered' => $total,
                'data' => $return,
            ]);
        }
        return view('users.index');

    }
}
