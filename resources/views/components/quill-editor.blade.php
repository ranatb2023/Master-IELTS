@props([
    'name' => 'description',
    'value' => '',
    'label' => 'Description',
    'required' => false,
    'height' => '300px',
    'placeholder' => 'Write your content here...',
])

<div {{ $attributes->merge(['class' => 'mb-6']) }}>
    @if($label)
        <label for="{{ $name }}" class="block text-sm font-medium text-gray-700 mb-2">
            {{ $label }}
            @if($required)
                <span class="text-red-500">*</span>
            @endif
        </label>
    @endif

    <!-- Hidden input to store the HTML content -->
    <input type="hidden" name="{{ $name }}" id="{{ $name }}_input" value="{{ old($name, $value) }}">

    <!-- Quill Editor Container -->
    <div id="{{ $name }}_editor" style="height: {{ $height }}; min-height: {{ $height }};" class="bg-white rounded-md border border-gray-300"></div>

    @error($name)
        <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
    @enderror
</div>

@once
    @push('styles')
        <link href="https://cdn.quilljs.com/1.3.6/quill.snow.css" rel="stylesheet">
        <style>
            .ql-editor {
                min-height: 200px;
                font-size: 16px;
                overflow-y: auto;
            }
            .ql-toolbar {
                background-color: #f9fafb;
                border-top-left-radius: 0.375rem;
                border-top-right-radius: 0.375rem;
                position: relative;
                z-index: 2;
            }
            .ql-container {
                border-bottom-left-radius: 0.375rem;
                border-bottom-right-radius: 0.375rem;
                font-family: inherit;
                position: relative;
                z-index: 1;
            }
            /* Ensure editors don't overlap */
            [id$='_editor'] {
                margin-bottom: 2rem;
                clear: both;
                overflow: visible;
            }
        </style>
    @endpush

    @push('scripts')
        <script src="https://cdn.quilljs.com/1.3.6/quill.min.js"></script>
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                // Initialize all Quill editors on the page
                const editorConfigs = [];

                @foreach(['description', 'body'] as $fieldName)
                    @if(isset($$fieldName) || old($fieldName))
                        editorConfigs.push({
                            name: '{{ $fieldName }}',
                            initialized: false
                        });
                    @endif
                @endforeach

                // Function to initialize a Quill editor
                window.initQuillEditor = function(name, placeholder = 'Write your content here...') {
                    const editorElement = document.getElementById(name + '_editor');
                    const inputElement = document.getElementById(name + '_input');

                    if (!editorElement || !inputElement) {
                        console.warn('Quill editor elements not found for:', name);
                        return null;
                    }

                    // Configure toolbar
                    const toolbarOptions = [
                        [{ 'header': [1, 2, 3, 4, 5, 6, false] }],
                        [{ 'font': [] }],
                        [{ 'size': ['small', false, 'large', 'huge'] }],

                        ['bold', 'italic', 'underline', 'strike'],
                        [{ 'color': [] }, { 'background': [] }],
                        [{ 'script': 'sub'}, { 'script': 'super' }],

                        [{ 'list': 'ordered'}, { 'list': 'bullet' }],
                        [{ 'indent': '-1'}, { 'indent': '+1' }],
                        [{ 'align': [] }],

                        ['blockquote', 'code-block'],
                        ['link', 'image', 'video'],

                        ['clean']
                    ];

                    // Initialize Quill
                    const quill = new Quill(editorElement, {
                        theme: 'snow',
                        placeholder: placeholder,
                        modules: {
                            toolbar: toolbarOptions
                        }
                    });

                    // Set initial content from hidden input
                    const initialContent = inputElement.value;
                    if (initialContent) {
                        quill.root.innerHTML = initialContent;
                    }

                    // Update hidden input when content changes
                    quill.on('text-change', function() {
                        inputElement.value = quill.root.innerHTML;
                    });

                    return quill;
                };
            });
        </script>
    @endpush
@endonce

@push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Initialize this specific editor
            window.initQuillEditor('{{ $name }}', '{{ $placeholder }}');
        });
    </script>
@endpush
