<?php

namespace App\Http\Controllers;

use App\Models\User;
use Carbon\Carbon;
use Cmgmyr\Messenger\Models\Message;
use Cmgmyr\Messenger\Models\Participant;
use Cmgmyr\Messenger\Models\Thread;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Request;
use Illuminate\Support\Facades\Session;

class MessagesController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }
    /**
     * Show all of the message threads to the user.
     *
     * @return mixed
     */
    public function index()
    {
        $messages = [];
        $userId = Auth::id();
        // Appropriate participants, ignore deleted/archived participants
        $participants = Participant::where('user_id', $userId) -> get();
// 
        for($index = 0; $index < sizeof($participants); $index++) {
            $thread_id = $participants[$index] -> thread_id;
            $message = Message::where('thread_id', $thread_id)->first();;
            $thread = Thread::where('id', $thread_id)->first();;
            $user = User::where('id', $message -> user_id)->first();;
            $array = array(
                'subject'=> $thread -> subject,
                'message'=> $message -> body,
                'sender'=> $user -> name
            );
            array_push($messages, $array);
        }
        
        return view('messenger.index', compact('messages'));
    }

    // /**
    //  * Shows a message thread.
    //  *
    //  * @param $id
    //  * @return mixed
    //  */
    public function show()
    {
        try {
            $thread = Thread::findOrFail($id);
        } catch (ModelNotFoundException $e) {
            Session::flash('error_message', 'The thread with ID: ' . $id . ' was not found.');

            return redirect()->route('messages');
        }

        // show current user in list if not a current participant
        // $users = User::whereNotIn('id', $thread->participantsUserIds())->get();

        // don't show the current user in list
        $userId = Auth::id();
        $users = User::whereNotIn('id', $thread->participantsUserIds($userId))->get();

        $thread->markAsRead($userId);
        var_dump($thread);

        // return view('messengr.show', compact('thread', 'users'));
    }

    /**
     * Creates a new message thread.
     *
     * @return mixed
     */
    public function create()
    {
        $users = User::where('id', '!=', Auth::id())->get();

        return view('messenger.create', compact('users'));
    }

    /**
     * Stores a new message thread.
     *
     * @return mixed
     */
    public function store()
    {
        $input = Request::all();

        $thread = Thread::create([
            'subject' => $input['subject'],
        ]);

        // Message
        Message::create([
            'thread_id' => $thread->id,
            'user_id' => Auth::id(),
            'body' => $input['message'],
        ]);
        // Sender
        function participant($x, $thr) {
            Participant::create([
                'thread_id' => $thr->id,
                'user_id' => $x,
                'last_read' => new Carbon(),
            ]);
            return true;
        }
        for($index = 0; $index < sizeof($input['recipients']); $index++) {
            participant($input['recipients'][$index], $thread);
        }

        $threads = Thread::getAllLatest()->get();

        // return view('messages');
        // return redirect(route('messages', $threads));
        return redirect()->back()->with('message', $threads);

    }

    // /**
    //  * Adds a new message to a current thread.
    //  *
    //  * @param $id
    //  * @return mixed
    //  */
    // public function update($id)
    // {
    //     try {
    //         $thread = Thread::findOrFail($id);
    //     } catch (ModelNotFoundException $e) {
    //         Session::flash('error_message', 'The thread with ID: ' . $id . ' was not found.');

    //         return redirect()->route('messages');
    //     }

    //     $thread->activateAllParticipants();

    //     // Message
    //     Message::create([
    //         'thread_id' => $thread->id,
    //         'user_id' => Auth::id(),
    //         'body' => Request::input('message'),
    //     ]);

    //     // Add replier as a participant
    //     $participant = Participant::firstOrCreate([
    //         'thread_id' => $thread->id,
    //         'user_id' => Auth::id(),
    //     ]);
    //     $participant->last_read = new Carbon();
    //     $participant->save();

    //     // Recipients
    //     if (Request::has('recipients')) {
    //         $thread->addParticipant(Request::input('recipients'));
    //     }

    //     return redirect()->route('messages.show', $id);
    // }
}