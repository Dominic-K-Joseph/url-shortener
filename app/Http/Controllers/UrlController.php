<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Url;
use Illuminate\Support\Str;

class UrlController extends Controller
{
    public function index()
    {
        $urls = Url::where('user_id', auth()->id())
            ->latest()
            ->get();

        return view('urls.index', compact('urls'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'title'        => 'required|string|max:255',
            'original_url' => 'required|url|max:2048',
        ]);

        $url = Url::create([
            'user_id'      => auth()->id(),
            'title'        => $request->title,
            'original_url' => $request->original_url,
            'short_code'   => $this->generateCode(),
        ]);

        return response()->json([
            'success' => true,
            'message' => 'URL shortened successfully.',
            'url'     => [
                'id'           => $url->id,
                'title'        => $url->title,
                'original_url' => $url->original_url,
                'short_url'    => $url->short_url,
                'created_at'   => $url->created_at->format('d M Y'),
            ],
        ]);
    }

    public function redirect(string $code)
    {
        $url = Url::where('short_code', $code)->firstOrFail();
        return redirect()->away($url->original_url);
    }

    private function generateCode(): string
    {
        do {
            $code = Str::random(6);
        } while (Url::where('short_code', $code)->exists());

        return $code;
    }

    public function destroy($id)
    {
        $url = Url::where('user_id', auth()->id())->findOrFail($id);
        $url->delete();
        
        return response()->json(['success' => true]);
    }
}
