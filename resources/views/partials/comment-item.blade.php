@php
    $canEdit = $comment->canBeEditedBy(auth()->user());
    $canDelete = $comment->canBeDeletedBy(auth()->user());
    $canPin = $comment->canBePinnedBy(auth()->user());
    $isOwnComment = $comment->user_id === auth()->id();
    $userInitial = strtoupper(substr($comment->user->name, 0, 1));
@endphp

<div class="comment-item" id="comment-{{ $comment->id }}" x-data="{
        editing: false,
        editedComment: '{{ addslashes($comment->comment) }}',
        replying: false,
        replyText: '',
        submitting: false
    }">
    <div class="flex space-x-4">
        <!-- Avatar -->
        <div class="flex-shrink-0">
            @if($comment->user->avatar)
                <img class="h-10 w-10 rounded-full ring-2 ring-white shadow"
                    src="{{ asset('storage/' . $comment->user->avatar) }}" alt="{{ $comment->user->name }}"
                    onerror="this.style.display='none'; this.nextElementSibling.style.display='flex';">
                <div class="h-10 w-10 rounded-full bg-gradient-to-br from-indigo-500 to-purple-600 flex items-center justify-center text-white font-bold text-sm ring-2 ring-white shadow"
                    style="display:none;">
                    {{ $userInitial }}
                </div>
            @else
                <div
                    class="h-10 w-10 rounded-full bg-gradient-to-br from-indigo-500 to-purple-600 flex items-center justify-center text-white font-bold text-sm ring-2 ring-white shadow">
                    {{ $userInitial }}
                </div>
            @endif
        </div>

        <!-- Comment Content -->
        <div class="flex-1">
            <div
                class="bg-gradient-to-br from-gray-50 to-gray-100 rounded-2xl px-5 py-4 shadow-sm hover:shadow-md transition-shadow duration-200">
                <!-- Header -->
                <div class="flex items-center justify-between mb-2">
                    <div class="flex items-center space-x-2">
                        <span class="font-semibold text-gray-900">{{ $comment->user->name }}</span>

                        @if($comment->is_from_tutor)
                            <span
                                class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold bg-gradient-to-r {{ $comment->user->hasAnyAdminRole() ? 'from-purple-500 to-pink-500' : 'from-indigo-500 to-blue-500' }} text-white shadow-sm">
                                {{ $comment->user->hasAnyAdminRole() ? '👑 Admin' : '🎓 Tutor' }}
                            </span>
                        @endif

                        @if($comment->is_pinned)
                            <span class="inline-flex items-center text-yellow-600">
                                <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                                    <path
                                        d="M10 3a1 1 0 011 1v5.414l1.293-1.293a1 1 0 111.414 1.414l-3 3a1 1 0 01-1.414 0l-3-3a1 1 0 011.414-1.414L9 9.414V4a1 1 0 011-1zM3 17a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1z" />
                                </svg>
                            </span>
                        @endif

                        <span class="text-sm text-gray-500">{{ $comment->created_at->diffForHumans() }}</span>
                    </div>

                    <!-- Actions Dropdown -->
                    @if($canEdit || $canDelete || $canPin)
                        <div x-data="{ open: false }" class="relative">
                            <button @click="open = !open" class="text-gray-400 hover:text-gray-600">
                                <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                                    <path
                                        d="M10 6a2 2 0 110-4 2 2 0 010 4zM10 12a2 2 0 110-4 2 2 0 010 4zM10 18a2 2 0 110-4 2 2 0 010 4z" />
                                </svg>
                            </button>

                            <div x-show="open" @click.away="open = false"
                                class="absolute right-0 mt-2 w-48 bg-white rounded-md shadow-lg z-10 border border-gray-200">
                                @if($canEdit && $isOwnComment)
                                    <button @click="editing = true; open = false"
                                        class="block w-full text-left px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                                        Edit
                                    </button>
                                @endif

                                @if($canPin)
                                    <button @click="togglePin({{ $comment->id }}); open = false"
                                        class="block w-full text-left px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                                        {{ $comment->is_pinned ? 'Unpin' : 'Pin' }} Comment
                                    </button>
                                @endif

                                @if($canDelete)
                                    <button @click="deleteComment({{ $comment->id }}); open = false"
                                        class="block w-full text-left px-4 py-2 text-sm text-red-600 hover:bg-red-50">
                                        Delete
                                    </button>
                                @endif
                            </div>
                        </div>
                    @endif
                </div>

                <!-- Edit Form -->
                <div x-show="editing" x-cloak>
                    <textarea x-model="editedComment" rows="3"
                        class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500"
                        maxlength="1000"></textarea>
                    <div class="mt-2 flex space-x-2">
                        <button @click="updateComment({{ $comment->id }})" :disabled="submitting"
                            class="px-4 py-2 bg-indigo-600 text-white text-sm rounded-lg hover:bg-indigo-700 disabled:opacity-50">
                            Save
                        </button>
                        <button @click="editing = false; editedComment = '{{ addslashes($comment->comment) }}'"
                            class="px-4 py-2 bg-gray-200 text-gray-700 text-sm rounded-lg hover:bg-gray-300">
                            Cancel
                        </button>
                    </div>
                </div>

                <!-- Comment Text -->
                <div x-show="!editing">
                    <p class="text-gray-700 whitespace-pre-wrap">{{ $comment->comment }}</p>
                </div>
            </div>

            <!-- Reply Button -->
            @if(auth()->user()->hasAnyAdminRole() || auth()->user()->isTutor())
                <div class="mt-2">
                    <button @click="replying = !replying" class="text-sm text-indigo-600 hover:text-indigo-800 font-medium">
                        <svg class="w-4 h-4 inline-block" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M3 10h10a8 8 0 018 8v2M3 10l6 6m-6-6l6-6" />
                        </svg>
                        Reply
                    </button>
                </div>

                <!-- Reply Form -->
                <div x-show="replying" x-cloak class="mt-3">
                    <textarea x-model="replyText" rows="3"
                        class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500"
                        placeholder="Write a reply..." maxlength="1000"></textarea>
                    <div class="mt-2 flex space-x-2">
                        <button @click="submitReply({{ $comment->id }})" :disabled="!replyText.trim() || submitting"
                            class="px-4 py-2 bg-indigo-600 text-white text-sm rounded-lg hover:bg-indigo-700 disabled:opacity-50">
                            <span x-show="!submitting">Post Reply</span>
                            <span x-show="submitting">Posting...</span>
                        </button>
                        <button @click="replying = false; replyText = ''"
                            class="px-4 py-2 bg-gray-200 text-gray-700 text-sm rounded-lg hover:bg-gray-300">
                            Cancel
                        </button>
                    </div>
                </div>
            @endif

            <!-- Replies -->
            @if($comment->replies->count() > 0)
                <div class="mt-4 space-y-4 ml-8">
                    @foreach($comment->replies as $reply)
                        @include('partials.comment-item', ['comment' => $reply, 'lesson' => $lesson])
                    @endforeach
                </div>
            @endif
        </div>
    </div>
</div>