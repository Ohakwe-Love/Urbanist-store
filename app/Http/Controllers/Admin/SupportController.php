<?php

namespace App\Http\Controllers\Admin;

use App\Http\Requests\AdminReplyToContactMessageRequest;
use App\Mail\ContactMessageReplyMail;
use App\Http\Controllers\Controller;
use App\Models\ContactMessage;
use App\Models\ContactMessageReply;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;
use Illuminate\View\View;
use Throwable;

class SupportController extends Controller
{
    public function index(Request $request): View
    {
        $search = trim($request->string('search')->toString());

        $messages = ContactMessage::query()
            ->withCount('replies')
            ->withMax('replies', 'created_at')
            ->when($search !== '', function ($query) use ($search) {
                $query->where(function ($subQuery) use ($search) {
                    $subQuery->where('name', 'like', "%{$search}%")
                        ->orWhere('email', 'like', "%{$search}%")
                        ->orWhere('phone', 'like', "%{$search}%")
                        ->orWhere('message', 'like', "%{$search}%");
                });
            })
            ->orderByRaw('COALESCE(replies_max_created_at, created_at) DESC')
            ->orderByDesc('id')
            ->paginate(14)
            ->withQueryString();

        return view('admin.support.index', compact('messages'));
    }

    public function show(ContactMessage $contactMessage): View
    {
        if (!$contactMessage->is_read) {
            $contactMessage->forceFill([
                'is_read' => true,
                'read_at' => Carbon::now(),
            ])->save();
        }

        $contactMessage->load(['replies.admin']);

        return view('admin.support.show', compact('contactMessage'));
    }

    public function reply(AdminReplyToContactMessageRequest $request, ContactMessage $contactMessage): RedirectResponse
    {
        $reply = ContactMessageReply::create([
            'contact_message_id' => $contactMessage->id,
            'admin_id' => auth('admin')->id(),
            'body' => $request->string('body')->trim()->toString(),
            'emailed_at' => null,
        ]);

        try {
            Mail::mailer('failover')->to($contactMessage->email)->send(new ContactMessageReplyMail($contactMessage, $reply));

            $reply->forceFill([
                'emailed_at' => now(),
            ])->save();

            return redirect()
                ->route('admin.support.show', $contactMessage)
                ->with('success', 'Reply sent to the customer successfully.');
        } catch (Throwable $exception) {
            Log::warning('Support reply email failed.', [
                'contact_message_id' => $contactMessage->id,
                'reply_id' => $reply->id,
                'recipient' => $contactMessage->email,
                'error' => $exception->getMessage(),
            ]);

            return redirect()
                ->route('admin.support.show', $contactMessage)
                ->with('error', 'The reply was saved, but the email could not be delivered right now. Please check your mail settings and try again.');
        }
    }
}
