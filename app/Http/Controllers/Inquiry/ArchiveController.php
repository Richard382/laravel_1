<?php

namespace App\Http\Controllers\Inquiry;

use App\Inquiry;
use App\Repositories\InquiryRepository;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ArchiveController extends \App\Http\Controllers\Controller
{
    public function index(Request $request)
    {
        $inquiries = InquiryRepository::getAvailable(true);

        if ($request->ajax()) {
            $inquiries = $inquiries->paginate();

            return response()->json([
                'items' => $inquiries->items()
            ], 201);
        }

        return view('inquiries.archive', [
            'inquiries' => $inquiries->paginate(),
        ]);
    }

    /**
     * @param Inquiry $inquiry
     * @return \Illuminate\Http\JsonResponse
     */
    public function archive(Inquiry $inquiry)
    {
        if (! $inquiry->archivable()) {
            return response()
                ->json([
                    'message' => 'Negalite suarchyvuoti šios užklausos!'
                ], 403);
        }

        $inquiry->archive();

        return response()
            ->json([
                'message' => 'Užklausa sėkmingai perkelta į archyvą!'
            ]);
    }

    public function erase(Inquiry $inquiry)
    {
        if ( ! $inquiry->in_archive) {
            return response()
                ->json([
                    'message' => 'Užklausa nėra jūsų archyve!'
                ], 403);
        }

        $inquiry->eraseFromArchive();

        return response()
            ->json([
                'message' => 'Užklausa sėkmingai ištrinta iš archyvo!'
            ]);
    }
}
