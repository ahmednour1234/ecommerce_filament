@php
    $messages = $messages ?? collect();
@endphp

<div class="space-y-3 max-h-[28rem] overflow-y-auto rounded-lg border border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-900/50 p-3">
    @forelse($messages as $message)
        @php
            $isComplaints = $message->department === 'complaints';
            $initials = mb_substr($message->creator?->name ?? '?', 0, 1, 'UTF-8');
        @endphp
        <div class="flex items-start gap-2.5 {{ $isComplaints ? '' : 'flex-row-reverse' }}">
            {{-- Avatar --}}
            <div class="flex h-8 w-8 shrink-0 items-center justify-center rounded-full text-xs font-bold text-white
                {{ $isComplaints ? 'bg-red-500' : 'bg-blue-500' }}">
                {{ $initials }}
            </div>

            {{-- Bubble --}}
            <div class="flex min-w-0 max-w-[80%] flex-col gap-1 {{ $isComplaints ? 'items-start' : 'items-end' }}">
                {{-- Meta row --}}
                <div class="flex items-center gap-1.5 text-xs {{ $isComplaints ? '' : 'flex-row-reverse' }}">
                    <span class="font-semibold text-gray-800 dark:text-gray-100">
                        {{ $message->creator?->name ?? 'مجهول' }}
                    </span>
                    <span class="rounded-full px-2 py-0.5 text-[10px] font-medium
                        {{ $isComplaints
                            ? 'bg-red-100 text-red-700 dark:bg-red-900/40 dark:text-red-300'
                            : 'bg-blue-100 text-blue-700 dark:bg-blue-900/40 dark:text-blue-300' }}">
                        {{ $isComplaints ? 'قسم الشكاوي' : 'قسم التنسيق' }}
                    </span>
                    <span class="text-gray-400">{{ $message->created_at->diffForHumans() }}</span>
                </div>

                {{-- Message body --}}
                <div class="rounded-2xl px-3.5 py-2 text-sm leading-relaxed text-gray-800 dark:text-gray-200
                    {{ $isComplaints
                        ? 'rounded-tl-sm bg-white dark:bg-gray-800 shadow-sm border border-gray-200 dark:border-gray-700'
                        : 'rounded-tr-sm bg-blue-500 text-white dark:bg-blue-600' }}">
                    {{ $message->body }}
                </div>

                {{-- Attachment if exists --}}
                @if($message->attachment)
                    <div class="mt-1.5 rounded-lg overflow-hidden {{ $isComplaints ? 'bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700' : 'bg-blue-50 dark:bg-blue-900/20' }}">
                        @php
                            $attachmentPath = $message->attachment;
                            $isImage = preg_match('/\.(jpg|jpeg|png|gif|webp)$/i', $attachmentPath);
                            $isPdf = preg_match('/\.pdf$/i', $attachmentPath);
                        @endphp

                        @if($isImage)
                            <img src="/storage/{{ $attachmentPath }}" alt="الصورة المرفقة"
                                 class="max-w-xs h-auto rounded cursor-pointer hover:opacity-90 transition"
                                 onclick="window.open('/storage/{{ $attachmentPath }}', '_blank')">
                        @else
                            <a href="/storage/{{ $attachmentPath }}" target="_blank"
                               class="flex items-center gap-2 px-3 py-2 text-sm {{ $isComplaints ? 'text-blue-600 dark:text-blue-400 hover:text-blue-800' : 'text-white hover:opacity-90' }}">
                                <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M4 4a2 2 0 012-2h4.586A2 2 0 0112 2.586L15.414 6A2 2 0 0116 7.414V16a2 2 0 01-2 2H6a2 2 0 01-2-2V4zm2 6a1 1 0 011-1h6a1 1 0 011 1v6a1 1 0 01-1 1H7a1 1 0 01-1-1v-6z" clip-rule="evenodd"/>
                                </svg>
                                <span>{{ basename($attachmentPath) }}</span>
                            </a>
                        @endif
                    </div>
                @endif
            </div>
        </div>
    @empty
        <div class="flex flex-col items-center justify-center py-10 text-gray-400">
            <svg class="h-10 w-10 mb-2 opacity-40" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                    d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"/>
            </svg>
            <p class="text-sm">لا توجد رسائل بعد</p>
        </div>
    @endforelse
</div>
