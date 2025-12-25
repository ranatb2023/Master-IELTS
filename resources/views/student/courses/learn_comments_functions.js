// Comment Functions
window.submitComment = function () {
    const form = event.target;
    const commentData = Alpine.$data(form.closest('[x-data]'));

    if (!commentData.comment.trim()) return;

    commentData.submitting = true;

    fetch('{{ route('student.lessons.comments.store', $currentLesson) }}', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
            'Accept': 'application/json'
        },
        body: JSON.stringify({ comment: commentData.comment })
    })
        .then(response => {
            if (!response.ok) {
                throw new Error('Network response was not ok');
            }
            return response.json();
        })
        .then(data => {
            if (data.success) {
                // Clear the form
                commentData.comment = '';
                commentData.submitting = false;

                // Reload to show new comment (simpler than building HTML)
                window.location.hash = 'comments';
                window.location.reload();
            } else {
                alert(data.message || 'Failed to post comment. Please try again.');
                commentData.submitting = false;
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('An error occurred. Please try again.');
            commentData.submitting = false;
        });
};

window.updateComment = function (commentId) {
    const commentElement = document.querySelector(`#comment-${commentId}`);
    const data = Alpine.$data(commentElement);

    if (!data.editedComment.trim()) return;

    data.submitting = true;

    fetch(`{{ route('student.lessons.comments.update', ['lesson' => $currentLesson, 'comment' => '__ID__']) }}`.replace('__ID__', commentId), {
        method: 'PUT',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
            'Accept': 'application/json'
        },
        body: JSON.stringify({ comment: data.editedComment })
    })
        .then(response => {
            if (!response.ok) {
                return response.json().then(err => { throw err; });
            }
            return response.json();
        })
        .then(result => {
            if (result.success) {
                // Update the comment text in the DOM
                const commentTextElements = commentElement.querySelectorAll('p');
                commentTextElements.forEach(p => {
                    if (p.classList.contains('whitespace-pre-wrap') && !p.closest('[x-show="editing"]')) {
                        p.textContent = data.editedComment;
                    }
                });
                // Exit edit mode
                data.editing = false;
                data.submitting = false;
            } else {
                alert(result.message || 'Failed to update comment. Please try again.');
                data.submitting = false;
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert(error.message || 'An error occurred. Please try again.');
            data.submitting = false;
        });
};

window.deleteComment = function (commentId) {
    if (!confirm('Are you sure you want to delete this comment?')) return;

    fetch(`{{ route('student.lessons.comments.destroy', ['lesson' => $currentLesson, 'comment' => '__ID__']) }}`.replace('__ID__', commentId), {
        method: 'DELETE',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
            'Accept': 'application/json'
        }
    })
        .then(response => {
            if (!response.ok) {
                return response.json().then(err => { throw err; });
            }
            return response.json();
        })
        .then(data => {
            if (data.success) {
                // Remove the comment from DOM with fade out
                const commentElement = document.querySelector(`#comment-${commentId}`);
                if (commentElement) {
                    commentElement.style.transition = 'opacity 0.3s ease';
                    commentElement.style.opacity = '0';
                    setTimeout(() => {
                        commentElement.remove();

                        // Check if there are no comments left
                        const commentsList = document.getElementById('comments-list');
                        if (commentsList && commentsList.children.length === 0) {
                            // Show empty state
                            commentsList.innerHTML = `
                                                    <div class="text-center py-12 bg-gradient-to-br from-gray-50 to-gray-100 rounded-xl border-2 border-dashed border-gray-300">
                                                        <svg class="mx-auto h-16 w-16 text-gray-400 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                                d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z" />
                                                        </svg>
                                                        <h3 class="text-lg font-semibold text-gray-900 mb-2">No comments yet</h3>
                                                        <p class="text-sm text-gray-500">Be the first to share your thoughts!</p>
                                                    </div>
                                                `;
                        }
                    }, 300);
                }

                // Update comment count in tab
                const badgeSelectors = [
                    'button[\\@click*="comments"] .bg-indigo-100',
                    'button[\\@click*="comments"] .bg-purple-100',
                    '[x-on\\:click*="comments"] .bg-indigo-100',
                    '[x-on\\:click*="comments"] .bg-purple-100'
                ];

                for (let selector of badgeSelectors) {
                    const badge = document.querySelector(selector);
                    if (badge) {
                        const currentCount = parseInt(badge.textContent) || 0;
                        if (currentCount > 1) {
                            badge.textContent = currentCount - 1;
                        } else {
                            badge.remove();
                        }
                        break;
                    }
                }
            } else {
                alert(data.message || 'Failed to delete comment. Please try again.');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert(error.message || 'An error occurred. Please try again.');
        });
};
