@extends('admin.layout')

@section('title', 'Support Message | Admin')
@section('heading', 'Support Message')
@section('subheading', 'Read the full customer message and manage the reply thread.')

@section('content')
    <section class="support-detail-shell">
        <div class="support-detail-surface">
            <div class="support-thread-actions">
                <a href="{{ route('admin.support.index') }}" class="support-icon-link" aria-label="Back to inbox" title="Back to inbox">
                    <i class="fa-solid fa-arrow-left"></i>
                </a>
            </div>

            <div class="support-thread-topbar">
                <div class="support-thread-identity">
                    <div class="support-contact-card">
                        <div class="support-avatar">{{ strtoupper(substr($contactMessage->name, 0, 1)) }}</div>
                        <div>
                            <span class="support-kicker">Message Detail</span>
                            <h2>{{ $contactMessage->name }}</h2>
                            <div class="support-thread-recipient-line">
                                <span>{{ $contactMessage->email }}</span>
                                @if ($contactMessage->phone)
                                    <span>{{ $contactMessage->phone }}</span>
                                @endif
                            </div>
                        </div>
                    </div>
                    <div class="support-thread-status-row">
                        <span class="badge {{ $contactMessage->replies->isEmpty() ? 'badge-warning' : 'badge-success' }}">
                            {{ $contactMessage->replies->isEmpty() ? 'Awaiting reply' : 'Responded' }}
                        </span>
                        <span class="support-thread-status-note">Stored here and synced to customer email</span>
                    </div>
                </div>

                <div class="support-thread-facts">
                    <div>
                        <span>Received</span>
                        <strong>{{ $contactMessage->created_at->format('M j, Y') }}</strong>
                        <small>{{ $contactMessage->created_at->format('g:i A') }}</small>
                    </div>
                    <div>
                        <span>Last activity</span>
                        <strong>{{ $contactMessage->latest_activity_at->format('M j, Y') }}</strong>
                        <small>{{ $contactMessage->latest_activity_at->format('g:i A') }}</small>
                    </div>
                    <div>
                        <span>Replies</span>
                        <strong>{{ $contactMessage->replies->count() }}</strong>
                        <small>{{ \Illuminate\Support\Str::plural('message', $contactMessage->replies->count()) }} from admin</small>
                    </div>
                </div>
            </div>

            <div class="support-chat-stream">
                <article class="support-message is-customer">
                    <div class="support-message-badge">Customer</div>
                    <div class="support-message-bubble">
                        <p>{!! nl2br(e($contactMessage->message)) !!}</p>
                    </div>
                    <div class="helper-text">{{ $contactMessage->created_at->format('M j, Y g:i A') }}</div>
                </article>

                @foreach ($contactMessage->replies as $reply)
                    <article class="support-message is-admin">
                        <div class="support-message-badge">
                            {{ $reply->admin?->name ?? 'Admin' }}
                            @if ($reply->emailed_at)
                                <span class="support-reply-delivered">Emailed</span>
                            @endif
                        </div>
                        <div class="support-message-bubble">
                            <p>{!! nl2br(e($reply->body)) !!}</p>
                        </div>
                        <div class="helper-text">{{ $reply->created_at->format('M j, Y g:i A') }}</div>
                    </article>
                @endforeach
            </div>

            <div class="support-inline-composer">
                <div class="support-thread-head">
                    <div>
                        <h3>Reply</h3>
                        <p>Your response will appear in this thread and be sent to the customer by email.</p>
                    </div>
                </div>

                <form method="POST" action="{{ route('admin.support.reply', $contactMessage) }}" class="admin-form support-composer-form">
                    @csrf
                    <div class="admin-field">
                        <label for="body">Message</label>
                        <textarea id="body" name="body" placeholder="Write your response to the customer here..." required>{{ old('body') }}</textarea>
                    </div>

                    <div class="support-composer-actions">
                        <button type="submit" class="btn btn-primary">Send reply</button>
                    </div>
                </form>
            </div>
        </div>
    </section>
@endsection
