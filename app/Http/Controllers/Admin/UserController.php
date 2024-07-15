<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Traits\ApiResponseTrait;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class UserController extends Controller
{
    use ApiResponseTrait;
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

            $users = User::where('role', 'user')->orderBy('created_at', $dir);

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

                $action_buttons = "<a href='" . route('admin_user_view', ['user_id' => $user->uuid]) . "' title='View' class='btn btn-sm btn-primary'>View</a>&nbsp;&nbsp;<a href='" . route('admin_user_edit', ['user_id' => $user->uuid]) . "' title='Edit' class='btn btn-sm btn-success'>Edit </a>";

                // fetch trade status
                $return[] = [
                    'id' => $key + 1,
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

    public function userView($user_id)
    {
        $data["user"] = User::find($user_id);
        return view('users.view', $data);
    }

    public function userEdit($user_id)
    {
        $data["user"] = User::find($user_id);
        return view('users.edit', $data);
    }

    public function userUpdate(Request $request)
    {
        $request->validate([
            'name' => 'required',
            'email' => 'required|email',
            'mobile_no' => 'required'
        ]);

        try {
            $user = User::find($request->user_id);
            if ($user) {
                $data = [
                    "name" => $request->name,
                    "email" => $request->email,
                    "mobile_no" => $request->mobile_no,
                ];
                $user->update($data);
                return redirect()->route('admin_user_list')->with('success', ucfirst($request->name) . " successfully updated.");
            } else {

                return redirect()->back()->with('error', "User Not Found.");
            }
        } catch (Exception $e) {
            return redirect()->back()->with('error', $e->getMessage());
        }
    }

    public function userStatusUpdate(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'status_type' => 'required|string',
            'user_id' => 'required|string',
        ]);

        if ($validator->fails()) {
            return $this->validationErrorResponse($validator->errors()->first());
        }
        try {
            $user = User::find($request->user_id);
            $status_type = $request->status_type;
            if ($user) {
                if ($status_type == "user_status") {
                    $user->update([
                        "user_status" => $user->user_status == "active" ? "block" : "active"
                    ]);
                } elseif ($status_type == "user_ban") {
                    $user->update([
                        "user_status" => $user->user_status == "active" || $user->user_status == "block" ? "ban" : ($user->user_status == "ban" ? "active" : $user->user_status)
                    ]);
                } elseif ($status_type == "email_verify") {
                    $user->update([
                        "email_verified_at" => $user->email_verified_at == null ? now() : null
                    ]);
                } elseif ($status_type == "mobile_verify") {
                    $user->update([
                        "mobile_verified_at" => $user->mobile_verified_at == null ? now() : null
                    ]);
                } elseif ($status_type == "aadhar_verify") {
                    $user->update([
                        "aadhar_verified" => $user->aadhar_verified == 1 ? 0 : 1
                    ]);
                } elseif ($status_type == "pan_verify") {
                    $user->update([
                        "pan_verified" => $user->pan_verified == 1 ? 0 : 1
                    ]);
                } elseif ($status_type == "bank_verify") {
                    $user->update([
                        "bank_verified" => $user->bank_verified == 1 ? 0 : 1
                    ]);
                } elseif ($status_type == "vpa_verify") {
                    $user->update([
                        "vpa_verified" => $user->vpa_verified == 1 ? 0 : 1
                    ]);
                } elseif ($status_type == "kyc_verify") {
                    $user->update([
                        "kyc_verified" => $user->kyc_verified == 1 ? 0 : 1
                    ]);
                } elseif ($status_type == "2fa") {
                    $user->update([
                        "google2fa_enable" => $user->google2fa_enable == "yes" ? "no" : "yes"
                    ]);
                } else {
                    return $this->errorResponse("Invalid Request.");
                }
                return $this->successResponse([], "User Status Successfully Changed.");
            } else {
                return $this->errorResponse("User Not Found.");
            }
        } catch (Exception $e) {
            return $this->errorResponse($e->getMessage());
        }
    }
}
