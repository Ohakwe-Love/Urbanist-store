@extends('admin.layout')

@section('title', 'Support Inbox | Admin')
@section('heading', 'Support Inbox')
@section('subheading', 'Review the message list and open any conversation in its own full page.')

@section('content')
    <section class="support-index-shell">
        <div class="support-list-panel support-list-panel-wide">
            <div class="support-list-head">
                <div>
                    <span class="support-kicker">Messages</span>
                    <h2>Customer Inbox</h2>
                    <div class="support-list-caption">{{ $messages->total() }} messages</div>
                </div>

                <form method="GET" class="support-search-form">
                    <div class="support-search-input-wrap">
                        <i class="fa-solid fa-magnifying-glass"></i>
                        <input type="text" name="search" value="{{ request('search') }}" placeholder="Search messages">
                    </div>
                    @if (request('search'))
                        <a href="{{ route('admin.support.index') }}" class="btn-link">Reset</a>
                    @endif
                </form>
            </div>

            <div class="support-conversation-list">
                @forelse ($messages as $message)
                    <a href="{{ route('admin.support.show', $message) }}" class="support-conversation-item support-conversation-item-row">
                        <div class="support-conversation-body">
                            <div class="support-conversation-topline">
                                <strong>{{ $message->name }}</strong>
                                <span>{{ $message->latest_activity_at->format('M j, g:i A') }}</span>
                            </div>
                            <div class="support-conversation-subject">Contact message from {{ $message->name }}</div>
                            <p>{{ \Illuminate\Support\Str::limit(preg_replace('/\s+/', ' ', $message->message), 140) }}</p>
                            <div class="support-conversation-meta">
                                <span class="support-conversation-email">{{ $message->email }}</span>
                                <span class="support-reply-count">{{ $message->replies_count }} {{ \Illuminate\Support\Str::plural('reply', $message->replies_count) }}</span>
                                <span class="support-reply-count">Updated {{ $message->latest_activity_at->diffForHumans() }}</span>
                                @if (!$message->is_read)
                                    <span class="support-unread-dot"></span>
                                @endif
                            </div>
                        </div>
                    </a>
                @empty
                    <div class="empty-state">No contact messages yet.</div>
                @endforelse
            </div>

            @if ($messages->hasPages())
                <div class="pagination">{{ $messages->links() }}</div>
            @endif
        </div>
    </section>
@endsection
