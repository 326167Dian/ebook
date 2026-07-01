<!doctype html>
<html lang="id">

<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1, minimum-scale=1, maximum-scale=1, viewport-fit=cover" />
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Editor Frontend E-Book</title>
    <link rel="stylesheet" href="{{ asset('Mobilekit/HTML/assets/css/style.css') }}">
    <style>
        :root {
            --editor-primary: #1f66ba;
            --editor-secondary: #4aa4ea;
            --editor-soft: #eef7ff;
        }

        body {
            background: radial-gradient(circle at 15% 0%, #f7fcff 0, #eaf5ff 45%, #dcebfa 100%);
            min-height: 100vh;
        }

        .appHeader {
            background: linear-gradient(120deg, var(--editor-primary), var(--editor-secondary));
        }

        .editor-panel {
            background: #fff;
            border-radius: 18px;
            box-shadow: 0 16px 32px rgba(31, 102, 186, 0.13);
            border: 1px solid rgba(31, 102, 186, 0.08);
        }

        .chapter-card {
            background: var(--editor-soft);
            border: 1px solid rgba(31, 102, 186, 0.12);
            border-radius: 14px;
            padding: 12px;
            margin-bottom: 12px;
        }

        .point-card {
            border: 1px dashed rgba(31, 102, 186, 0.35);
            border-radius: 12px;
            padding: 10px;
            background: #ffffff;
            margin-top: 10px;
        }

        .point-attachment {
            margin-top: 10px;
            padding: 10px;
            border-radius: 12px;
            background: rgba(31, 102, 186, 0.04);
            border: 1px solid rgba(31, 102, 186, 0.12);
        }

        .ck-editor__editable {
            min-height: 160px;
        }

        .btn-ebook {
            background: var(--editor-primary);
            border: 0;
            color: #fff;
        }

        .btn-ebook:hover {
            color: #fff;
            background: #2b7fd2;
        }

        .section-save-actions {
            display: flex;
            justify-content: flex-end;
            margin-top: 12px;
            position: sticky;
            bottom: 10px;
            z-index: 20;
            padding: 8px 0;
            background: linear-gradient(to top, rgba(255, 255, 255, 0.98), rgba(255, 255, 255, 0.78), rgba(255, 255, 255, 0));
            backdrop-filter: blur(1px);
            opacity: 0;
            transform: translateY(8px);
            pointer-events: none;
            transition: opacity 0.2s ease, transform 0.2s ease;
        }

        .section-save-actions.is-visible {
            opacity: 1;
            transform: translateY(0);
            pointer-events: auto;
        }

        .section-save-actions .btn {
            box-shadow: 0 8px 18px rgba(31, 102, 186, 0.25);
        }
    </style>
</head>

<body>
    <div id="loader">
        <div class="spinner-border text-primary" role="status"></div>
    </div>

    <div class="appHeader text-light">
        <div class="left"></div>
        <div class="pageTitle text-light">Editor Frontend E-Book</div>
        <div class="right">
            <form method="POST" action="{{ route('admin.logout') }}">
                @csrf
                <button type="submit" class="headerButton text-light" style="border:0; background:transparent;">
                    <ion-icon name="log-out-outline"></ion-icon>
                </button>
            </form>
        </div>
    </div>

    <div id="appCapsule" class="pt-4 pb-4">
        <div class="section">
            <div class="editor-panel p-3 p-md-4">
                <div class="d-flex justify-content-between align-items-start flex-wrap gap-2 mb-3">
                    <div>
                        <h2 class="mb-1">Pengaturan Konten Halaman E-Book</h2>
                        <p class="text-secondary mb-0">Semua perubahan di sini langsung memengaruhi halaman publik.</p>
                    </div>
                    <a href="{{ route('ebook.home') }}" class="btn btn-outline-secondary">Lihat Halaman Publik</a>
                </div>

                @if (session('status'))
                    <div class="alert alert-success">{{ session('status') }}</div>
                @endif

                @if ($errors->any())
                    <div class="alert alert-danger">
                        <ul class="mb-0 ps-3">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <form method="POST" action="{{ route('admin.editor.update') }}" id="editor-form" enctype="multipart/form-data">
                    @csrf

                    @php
                        $formChapters = old('chapters', $content->chapters ?? []);
                    @endphp

                    <div class="row g-2">
                        <div class="col-md-4">
                            <label class="form-label">Badge Hero</label>
                            <input type="text" class="form-control" name="badge" value="{{ old('badge', $content->badge) }}" required>
                        </div>
                        <div class="col-md-8">
                            <label class="form-label">Judul Hero</label>
                            <input type="text" class="form-control" name="hero_title" value="{{ old('hero_title', $content->hero_title) }}" required>
                        </div>
                    </div>

                    <div class="form-group mt-3">
                        <label class="form-label">Deskripsi Hero</label>
                        <textarea class="form-control" rows="3" name="hero_description" required>{{ old('hero_description', $content->hero_description) }}</textarea>
                    </div>

                    <div class="form-group mt-3">
                        <label class="form-label">Catatan Pengantar</label>
                        <textarea class="form-control" rows="4" name="intro_note">{{ old('intro_note', $content->intro_note) }}</textarea>
                    </div>

                    <div class="form-group mt-3">
                        <label class="form-label">Path Cover (relatif dari public)</label>
                        <input type="text" class="form-control" name="cover_image" placeholder="contoh: coverebook/coverebook.png" value="{{ old('cover_image', $content->cover_image) }}">
                        <small class="text-muted d-block mt-1">Boleh tetap isi manual, atau upload file gambar baru di bawah.</small>
                    </div>

                    <div class="form-group mt-2">
                        <label class="form-label">Upload Gambar Utama</label>
                        <input type="file" class="form-control" name="cover_upload" accept="image/png,image/jpeg,image/webp">
                        <small class="text-muted d-block mt-1">Format: JPG, PNG, WEBP. Maksimal 5MB.</small>
                    </div>

                    @if (!empty($content->cover_image))
                        <div class="mt-2">
                            <img src="{{ asset($content->cover_image) }}" alt="Preview Cover" style="width: 100%; max-width: 280px; height: auto; border-radius: 12px; border:1px solid rgba(31,102,186,.2);">
                        </div>
                    @endif

                    <div class="section-save-actions">
                        <button type="submit" class="btn btn-ebook">Simpan Section Konten</button>
                    </div>

                    <hr class="mt-4 mb-3">

                    <h3 class="mb-2">Tema Warna Frontend</h3>
                    <p class="text-secondary mb-3">Atur nuansa warna halaman publik eBook menggunakan color picker.</p>

                    <div class="row g-2">
                        <div class="col-md-4">
                            <label class="form-label">Warna Utama</label>
                            <input type="color" class="form-control form-control-color w-100" name="theme_primary" value="{{ old('theme_primary', $content->theme_primary ?? '#1e5fae') }}" required>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Warna Sekunder</label>
                            <input type="color" class="form-control form-control-color w-100" name="theme_secondary" value="{{ old('theme_secondary', $content->theme_secondary ?? '#4ea3e6') }}" required>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Warna Aksen</label>
                            <input type="color" class="form-control form-control-color w-100" name="theme_accent" value="{{ old('theme_accent', $content->theme_accent ?? '#9fd8ff') }}" required>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Latar Awal</label>
                            <input type="color" class="form-control form-control-color w-100" name="theme_bg_start" value="{{ old('theme_bg_start', $content->theme_bg_start ?? '#f7fcff') }}" required>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Latar Akhir</label>
                            <input type="color" class="form-control form-control-color w-100" name="theme_bg_end" value="{{ old('theme_bg_end', $content->theme_bg_end ?? '#dcebfa') }}" required>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Warna Teks Utama</label>
                            <input type="color" class="form-control form-control-color w-100" name="theme_text" value="{{ old('theme_text', $content->theme_text ?? '#16314f') }}" required>
                        </div>
                    </div>

                    <div class="section-save-actions">
                        <button type="submit" class="btn btn-ebook">Simpan Section Tema</button>
                    </div>

                    <hr class="mt-4 mb-3">

                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <h3 class="mb-0">Daftar Isi</h3>
                        <button type="button" class="btn btn-outline-primary" id="add-chapter">Tambah Bab</button>
                    </div>

                    <div id="chapters-wrapper">
                        @foreach ($formChapters as $index => $chapter)
                            <div class="chapter-card">
                                <div class="d-flex justify-content-between align-items-center mb-2">
                                    <strong>Bab <span class="chapter-number">{{ $index + 1 }}</span></strong>
                                    <button type="button" class="btn btn-sm btn-outline-danger remove-chapter">Hapus</button>
                                </div>

                                <div class="form-group mb-2">
                                    <label class="form-label">Judul Bab</label>
                                    <input type="text" class="form-control chapter-title" name="chapters[{{ $index }}][title]" value="{{ $chapter['title'] ?? '' }}" required>
                                </div>

                                <div class="d-flex justify-content-between align-items-center mt-2 mb-1">
                                    <label class="form-label mb-0">Poin Bab</label>
                                    <button type="button" class="btn btn-sm btn-outline-primary add-point">Tambah Poin</button>
                                </div>

                                <div class="points-wrapper">
                                    @foreach (($chapter['items'] ?? []) as $pointIndex => $point)
                                        <div class="point-card">
                                            <div class="d-flex justify-content-between align-items-center mb-2">
                                                <strong>Poin <span class="point-number">{{ $pointIndex + 1 }}</span></strong>
                                                <button type="button" class="btn btn-sm btn-outline-danger remove-point">Hapus Poin</button>
                                            </div>

                                            <div class="form-group mb-2">
                                                <label class="form-label">Judul Poin</label>
                                                <input type="text" class="form-control point-title" name="chapters[{{ $index }}][items][{{ $pointIndex }}][title]" value="{{ $point['title'] ?? '' }}" required>
                                            </div>

                                            <div class="form-group mb-0">
                                                <label class="form-label">Isi Halaman Poin</label>
                                                <textarea class="form-control point-content" name="chapters[{{ $index }}][items][{{ $pointIndex }}][content]" rows="4" placeholder="Tuliskan isi halaman detail untuk poin ini...">{{ $point['content'] ?? '' }}</textarea>
                                            </div>

                                            <div class="point-attachment">
                                                <label class="form-label">Dokumen Lampiran</label>
                                                <input type="file" class="form-control point-document" name="chapters[{{ $index }}][items][{{ $pointIndex }}][document_upload][]" accept=".pdf,.doc,.docx,.xls,.xlsx,.ppt,.pptx,.txt,.zip,.rar" multiple>
                                                <small class="text-muted d-block mt-1">Format: PDF, DOC, DOCX, XLS, XLSX, PPT, PPTX, TXT, ZIP, RAR. Maksimal 5 dokumen, masing-masing 5MB.</small>

                                                @php
                                                    $existingDocuments = collect($point['documents'] ?? []);
                                                @endphp

                                                @if ($existingDocuments->isNotEmpty())
                                                    <div class="mt-3 existing-document-list">
                                                        @foreach ($existingDocuments as $documentIndex => $document)
                                                            @if (!empty($document['path']))
                                                                <div class="existing-document-item" data-existing-document>
                                                                    <input type="hidden" class="existing-document-path-input" name="chapters[{{ $index }}][items][{{ $pointIndex }}][documents_existing][{{ $documentIndex }}][path]" value="{{ $document['path'] }}">
                                                                    <input type="hidden" class="existing-document-name-input" name="chapters[{{ $index }}][items][{{ $pointIndex }}][documents_existing][{{ $documentIndex }}][name]" value="{{ $document['name'] ?? basename($document['path']) }}">
                                                                    <div>
                                                                        <div class="fw-semibold">{{ $document['name'] ?? basename($document['path']) }}</div>
                                                                        <div class="small text-muted">{{ basename($document['path']) }}</div>
                                                                    </div>
                                                                    <button type="button" class="btn btn-sm btn-outline-danger remove-existing-document">Hapus</button>
                                                                </div>
                                                            @endif
                                                        @endforeach
                                                    </div>
                                                @endif

                                                <div class="point-document-preview mt-2"></div>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        @endforeach
                    </div>

                    <div class="section-save-actions">
                        <button type="submit" class="btn btn-ebook">Simpan Section Daftar Isi</button>
                    </div>

                    <button type="submit" class="btn btn-ebook btn-block mt-3">Simpan Perubahan</button>
                </form>
            </div>
        </div>
    </div>

    <template id="chapter-template">
        <div class="chapter-card">
            <div class="d-flex justify-content-between align-items-center mb-2">
                <strong>Bab <span class="chapter-number">1</span></strong>
                <button type="button" class="btn btn-sm btn-outline-danger remove-chapter">Hapus</button>
            </div>

            <div class="form-group mb-2">
                <label class="form-label">Judul Bab</label>
                <input type="text" class="form-control chapter-title" required>
            </div>

            <div class="d-flex justify-content-between align-items-center mt-2 mb-1">
                <label class="form-label mb-0">Poin Bab</label>
                <button type="button" class="btn btn-sm btn-outline-primary add-point">Tambah Poin</button>
            </div>

            <div class="points-wrapper"></div>
        </div>
    </template>

    <template id="point-template">
        <div class="point-card">
            <div class="d-flex justify-content-between align-items-center mb-2">
                <strong>Poin <span class="point-number">1</span></strong>
                <button type="button" class="btn btn-sm btn-outline-danger remove-point">Hapus Poin</button>
            </div>

            <div class="form-group mb-2">
                <label class="form-label">Judul Poin</label>
                <input type="text" class="form-control point-title" required>
            </div>

            <div class="form-group mb-0">
                <label class="form-label">Isi Halaman Poin</label>
                <textarea class="form-control point-content" rows="4" placeholder="Tuliskan isi halaman detail untuk poin ini..."></textarea>
            </div>

            <div class="point-attachment">
                <label class="form-label">Dokumen Lampiran</label>
                <input type="file" class="form-control point-document" accept=".pdf,.doc,.docx,.xls,.xlsx,.ppt,.pptx,.txt,.zip,.rar">
                <small class="text-muted d-block mt-1">Format: PDF, DOC, DOCX, XLS, XLSX, PPT, PPTX, TXT, ZIP, RAR. Maksimal 10MB.</small>
            </div>
        </div>
    </template>

    <script src="{{ asset('Mobilekit/HTML/assets/js/lib/bootstrap.min.js') }}"></script>
    <script type="module" src="https://unpkg.com/ionicons@5.5.2/dist/ionicons/ionicons.js"></script>
    <script src="{{ asset('Mobilekit/HTML/assets/js/base.js') }}"></script>
    <script src="https://cdn.ckeditor.com/ckeditor5/39.0.1/classic/ckeditor.js"></script>
    <script>
        const wrapper = document.getElementById('chapters-wrapper');
        const chapterTemplate = document.getElementById('chapter-template');
        const pointTemplate = document.getElementById('point-template');
        const addChapterButton = document.getElementById('add-chapter');
        const editorForm = document.getElementById('editor-form');
        const editorInstances = new Map();
        const sectionSaveActions = document.querySelectorAll('.section-save-actions');
        const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
        const uploadUrl = "{{ route('admin.ckeditor.upload') }}";
        let hasChanges = false;

        function setDirtyState(isDirty) {
            hasChanges = isDirty;

            sectionSaveActions.forEach((actionBar) => {
                actionBar.classList.toggle('is-visible', hasChanges);
            });
        }

        class LaravelUploadAdapter {
            constructor(loader) {
                this.loader = loader;
                this.abortController = new AbortController();
            }

            upload() {
                return this.loader.file.then((file) => {
                    const formData = new FormData();
                    formData.append('upload', file);

                    return fetch(uploadUrl, {
                        method: 'POST',
                        body: formData,
                        headers: {
                            'X-CSRF-TOKEN': csrfToken,
                            'Accept': 'application/json'
                        },
                        signal: this.abortController.signal
                    })
                        .then(async (response) => {
                            const data = await response.json();

                            if (!response.ok || !data.url) {
                                throw new Error(data.message || 'Upload gambar gagal.');
                            }

                            return {
                                default: data.url
                            };
                        });
                });
            }

            abort() {
                this.abortController.abort();
            }
        }

        function editorPlugin(editor) {
            editor.plugins.get('FileRepository').createUploadAdapter = (loader) => {
                return new LaravelUploadAdapter(loader);
            };
        }

        function initEditorForTextarea(textarea) {
            if (!textarea || textarea.dataset.ckeditorInitialized === '1') {
                return;
            }

            ClassicEditor
                .create(textarea, {
                    extraPlugins: [editorPlugin],
                    toolbar: [
                        'heading', '|',
                        'imageUpload',
                        'bold', 'italic', 'link', '|',
                        'bulletedList', 'numberedList', '|',
                        'blockQuote', 'undo', 'redo'
                    ]
                })
                .then((editor) => {
                    textarea.dataset.ckeditorInitialized = '1';
                    editorInstances.set(textarea, editor);
                    editor.model.document.on('change:data', () => {
                        setDirtyState(true);
                    });
                })
                .catch((error) => {
                    console.error(error);
                });
        }

        function initEditorsIn(container) {
            container.querySelectorAll('.point-content').forEach((textarea) => {
                initEditorForTextarea(textarea);
            });
        }

        function destroyEditorForTextarea(textarea) {
            const editor = editorInstances.get(textarea);
            if (!editor) {
                return;
            }

            editor.destroy();
            editorInstances.delete(textarea);
            delete textarea.dataset.ckeditorInitialized;
        }

        function formatFileSize(bytes) {
            if (bytes < 1024) {
                return `${bytes} B`;
            }

            if (bytes < 1024 * 1024) {
                return `${(bytes / 1024).toFixed(1)} KB`;
            }

            return `${(bytes / (1024 * 1024)).toFixed(1)} MB`;
        }

        function renderDocumentPreview(documentInput) {
            const attachment = documentInput.closest('.point-attachment');
            if (!attachment) {
                return;
            }

            const preview = attachment.querySelector('.point-document-preview');
            if (!preview) {
                return;
            }

            preview.innerHTML = '';

            Array.from(documentInput.files || []).forEach((file) => {
                const item = document.createElement('div');
                item.className = 'point-document-preview-item';

                const info = document.createElement('div');
                info.className = 'point-document-preview-info';

                const title = document.createElement('div');
                title.className = 'fw-semibold';
                title.textContent = file.name;

                const meta = document.createElement('div');
                meta.className = 'small text-muted';
                meta.textContent = formatFileSize(file.size);

                info.appendChild(title);
                info.appendChild(meta);
                item.appendChild(info);

                preview.appendChild(item);
            });
        }

        function refreshIndexes() {
            const chapterCards = wrapper.querySelectorAll('.chapter-card');

            chapterCards.forEach((chapterCard, chapterIndex) => {
                chapterCard.querySelector('.chapter-number').textContent = chapterIndex + 1;
                chapterCard.querySelector('.chapter-title').name = `chapters[${chapterIndex}][title]`;

                const pointCards = chapterCard.querySelectorAll('.point-card');
                pointCards.forEach((pointCard, pointIndex) => {
                    pointCard.querySelector('.point-number').textContent = pointIndex + 1;
                    pointCard.querySelector('.point-title').name = `chapters[${chapterIndex}][items][${pointIndex}][title]`;
                    pointCard.querySelector('.point-content').name = `chapters[${chapterIndex}][items][${pointIndex}][content]`;
                    pointCard.querySelector('.point-document').name = `chapters[${chapterIndex}][items][${pointIndex}][document_upload][]`;

                    pointCard.querySelectorAll('.existing-document-item').forEach((documentItem, documentIndex) => {
                        const pathInput = documentItem.querySelector('.existing-document-path-input');
                        const nameInput = documentItem.querySelector('.existing-document-name-input');

                        if (pathInput) {
                            pathInput.name = `chapters[${chapterIndex}][items][${pointIndex}][documents_existing][${documentIndex}][path]`;
                        }

                        if (nameInput) {
                            nameInput.name = `chapters[${chapterIndex}][items][${pointIndex}][documents_existing][${documentIndex}][name]`;
                        }
                    });
                });
            });
        }

        function ensurePoint(chapterCard) {
            const pointsWrapper = chapterCard.querySelector('.points-wrapper');

            if (pointsWrapper.querySelectorAll('.point-card').length === 0) {
                pointsWrapper.appendChild(pointTemplate.content.cloneNode(true));
            }
        }

        function addChapter() {
            wrapper.appendChild(chapterTemplate.content.cloneNode(true));
            const chapterCard = wrapper.lastElementChild;
            ensurePoint(chapterCard);
            refreshIndexes();
            initEditorsIn(chapterCard);
            setDirtyState(true);
        }

        function addPoint(chapterCard) {
            chapterCard.querySelector('.points-wrapper').appendChild(pointTemplate.content.cloneNode(true));
            refreshIndexes();
            initEditorsIn(chapterCard);
            setDirtyState(true);
        }

        addChapterButton.addEventListener('click', addChapter);

        wrapper.addEventListener('click', (event) => {
            const removeExistingDocumentButton = event.target.closest('.remove-existing-document');
            if (removeExistingDocumentButton) {
                const attachment = removeExistingDocumentButton.closest('.point-attachment');
                const documentItem = removeExistingDocumentButton.closest('.existing-document-item');

                if (attachment && documentItem) {
                    documentItem.remove();
                    setDirtyState(true);
                }

                return;
            }

            const addPointButton = event.target.closest('.add-point');
            if (addPointButton) {
                addPoint(addPointButton.closest('.chapter-card'));
                return;
            }

            const removePointButton = event.target.closest('.remove-point');
            if (removePointButton) {
                const chapterCard = removePointButton.closest('.chapter-card');
                const points = chapterCard.querySelectorAll('.point-card');
                if (points.length <= 1) {
                    return;
                }

                const pointCard = removePointButton.closest('.point-card');
                const textarea = pointCard.querySelector('.point-content');
                destroyEditorForTextarea(textarea);

                pointCard.remove();
                refreshIndexes();
                setDirtyState(true);
                return;
            }

            const removeChapterButton = event.target.closest('.remove-chapter');
            if (removeChapterButton) {
                const chapterCards = wrapper.querySelectorAll('.chapter-card');
                if (chapterCards.length <= 1) {
                    return;
                }

                removeChapterButton.closest('.chapter-card').remove();
                refreshIndexes();
                setDirtyState(true);
            }
        });

        wrapper.addEventListener('change', (event) => {
            const documentInput = event.target.closest('.point-document');
            if (documentInput) {
                renderDocumentPreview(documentInput);
            }
        });

        wrapper.querySelectorAll('.point-document').forEach((documentInput) => {
            renderDocumentPreview(documentInput);
        });

        editorForm.addEventListener('input', () => {
            setDirtyState(true);
        });

        editorForm.addEventListener('change', () => {
            setDirtyState(true);
        });

        wrapper.querySelectorAll('.chapter-card').forEach((chapterCard) => {
            ensurePoint(chapterCard);
        });

        if (wrapper.querySelectorAll('.chapter-card').length === 0) {
            addChapter();
        }

        initEditorsIn(wrapper);

        editorForm.addEventListener('submit', () => {
            editorInstances.forEach((editor) => {
                editor.updateSourceElement();
            });

            setDirtyState(false);
        });

        setDirtyState(false);

        refreshIndexes();
    </script>
</body>

</html>
