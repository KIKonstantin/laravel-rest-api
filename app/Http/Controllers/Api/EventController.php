<?php

namespace App\Http\Controllers\Api;

use Illuminate\Routing\Controller;

// use App\Http\Controllers\Controller;
use App\Http\Resources\EventResource;
use App\Http\Traits\canLoadRelations;
use Illuminate\Http\Request;
use App\Models\Event;

class EventController extends Controller
{

    use canLoadRelations;
    
    private $relations = ['user', 'attendees', 'user.attendees'];
    
    public function __construct()
    {   
        $this->middleware('auth:sanctum')->except(['index', 'show']);
    }
    
    public function index()
    {
        $query = $this->loadRelationships(Event::query());
        return EventResource::collection(
            $query->latest()->paginate()
        );
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validate = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'start_time' => 'required|date',
            'end_time' => 'required|date|after:start_time',
            'user_id' => 'required'
        ]);

        $event = Event::create([
            ...$validate,
            'user_id' => $request->user()->id
        ]);
        return new EventResource($this->loadRelationships($event));
    }

    /**
     * Display the specified resource.
     */
    public function show(Event $event)
    {
        return new EventResource($this->loadRelationships($event));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Event $event)
    {
        $validate = $request->validate([
            'name' => 'sometimes|string|max:255',
            'description' => 'sometimes|nullable|string',
            'start_time' => 'sometimes|date',
            'end_time' => 'sometimes|date|after:start_time',
            'user_id'
        ]);
        $event->update($validate);
        return new EventResource($this->loadRelationships($event));
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Event $event)
    {
        $event->delete();
        return response(status: 204);
    }
}
