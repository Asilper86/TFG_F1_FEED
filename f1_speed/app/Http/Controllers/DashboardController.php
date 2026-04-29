<?php

namespace App\Http\Controllers;

use App\Models\Lap;
use App\Models\Racing_session;
use App\Models\Social_post;
use Illuminate\Http\Request;
use Inertia\Inertia;

class DashboardController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $activeSession = Racing_session::where('user_id', auth()->id())
            ->where('is_active', true)
            ->first();

        $laps = Lap::whereHas('session', function ($query) {
            $query->where('user_id', auth()->id());
        })
            ->with(['telemetryLogs', 'session'])
            ->latest()
            ->take(15)
            ->get();

        return Inertia::render('Dashboard', [
            'laps' => $laps,
            'activeSession' => $activeSession,
        ]);
    }

    public function shareLap(Request $request)
    {
        $request->validate([
            'lap_id' => 'required|exists:laps,id',
            'content' => 'required|string|max:280',
        ]);

        $lap = Lap::with('session')->findOrFail($request->lap_id);

        if ($lap->session->user_id !== auth()->id()) {
            abort(403);
        }

        $post = Social_post::create([
            'user_id' => auth()->id(),
            'content' => $request->content, 
            'lap_id' => $lap->id,
        ]);

        // Extraer y guardar hashtags
        preg_match_all('/#(\w+)/', $request->content, $matches);
        if (!empty($matches[1])) {
            foreach ($matches[1] as $tagName) {
                $hashtag = \App\Models\Hashtag::firstOrCreate(['name' => strtolower($tagName)]);
                $post->hashtags()->attach($hashtag->id);
            }
        }

        return redirect()->route('social.feed')->with('message', '¡Post publicado!');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Lap $lap)
    {
        if ($lap->session->user_id !== auth()->id()) {
            abort(403, 'Acceso Denegado: No tienes permisos para esa acción.');
        }

        $lap->delete();

        return redirect()->back();
    }
}
