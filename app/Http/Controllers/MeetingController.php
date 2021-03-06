<?php

namespace App\Http\Controllers;

use App\Meeting;
use Illuminate\Http\Request;

class MeetingController extends Controller
{
    public function __construct()
    {
        // $this->middleware('name');
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $meetings = Meeting::all();
        foreach ($meetings as $meeting) {
            $meeting->view_meeting = [
                'href'   => 'api/v1/meeting/' . $meeting->id,
                'method' => 'GET',
            ];
        }

        $response = [
            'msg'      => 'List of all Meetings',
            'meetings' => $meetings,
        ];
        return response()->json($response, 200);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $this->validate($request, [
            'title'       => 'required',
            'description' => 'required',
            'time'        => 'required|date_format:YmdHie',
            'user_id'     => 'required',
        ]);

        extract($request->all());

        $meeting = Meeting::create($request->except('user_id'));

        if ($meeting) {
            $meeting->users()->attach($user_id);
            $meeting->view_meeting = [
                'href'   => 'api/v1/meeting/' . $meeting->id,
                'method' => 'GET',
            ];

            $message = [
                'msg'     => 'Meeting created',
                'meeting' => $meeting,
            ];
            return response()->json($message, 201);
        }

        $response = [
            'msg' => 'Error during creationg',
        ];

        return response()->json($response, 404);
    }

    /**
     * Display the specified resource.
     *
     * @param  int $id<
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $meeting = Meeting::with('users')->where('id', $id)->firstOrFail();

        $response = [
            'msg'     => 'Meeting information',
            'meeting' => $meeting,
            'view_meetings' => [
                'href'   => 'api/v1/meeting',
                'method' => 'GET',
            ],
        ];
        return response()->json($response, 200);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request $request
     * @param  int $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        extract($request->all());

        $this->validate($request, [
            'title'       => 'required',
            'description' => 'required',
            'time'        => 'required|date_format:YmdHie',
            'user_id'     => 'required',
        ]);

        $meeting = Meeting::with('users')->findOrFail($id);

        if ($meeting->userIsNotRegistered($user_id)) {
            return response()->json(['msg' => 'user not registered for meeting, update not successful'], 401);
        }

        if (!$meeting->update($request->except('user_id'))) {
            return response()->json(['msg' => 'Error during updating'], 404);
        }

        $response = [
            'msg'     => 'Meeting updated',
            'meeting' => $meeting->view_meeting = [
                'href'   => 'api/v1/meeting/' . $meeting->id,
                'method' => 'GET',
            ],
        ];

        return response()->json($response, 200);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $meeting = Meeting::findOrFail($id);
        $users   = $meeting->users;
        $meeting->users()->detach();
        if (!$meeting->delete()) {
            foreach ($users as $user) {
                $meeting->users()->attach($user);
            }
            return response()->json(['msg' => 'deletion failed'], 404);
        }

        $response = [
            'msg'    => 'Meeting deleted',
            'create' => [
                'href'   => 'api/v1/meeting',
                'method' => 'POST',
                'params' => 'title, description, time',
            ],
        ];

        return response()->json($response, 200);
    }
}
