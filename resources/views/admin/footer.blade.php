<!doctype html>
<html lang="id">

<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1, minimum-scale=1, maximum-scale=1, viewport-fit=cover" />
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Pengaturan Footer</title>
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

        .btn-ebook {
            background: var(--editor-primary);
            border: 0;
            color: #fff;
        }

        .btn-ebook:hover {
            color: #fff;
            background: #2b7fd2;
        }

        .rich-text-shell {
            padding: 14px;
            border-radius: 16px;
            background: linear-gradient(180deg, rgba(31, 102, 186, 0.05), rgba(78, 163, 230, 0.02));
            border: 1px solid rgba(31, 102, 186, 0.12);
        }

        .rich-text-shell .form-text {
            margin-bottom: 10px;
            color: #5b7083;
        }

        .rich-text-shell .ck-editor__editable {
            min-height: 220px;
        }

        .text-style-toolbar {
            display: flex;
            align-items: center;
            gap: 6px;
            margin-top: 8px;
            margin-bottom: 0;
            padding: 8px 10px;
            border-radius: 10px;
            background: rgba(31, 102, 186, 0.05);
            border: 1px solid rgba(31, 102, 186, 0.12);
            flex-wrap: wrap;
        }

        .text-style-toolbar label {
            font-size: 12px;
            color: #294c72;
            font-weight: 700;
        }

        .text-style-toolbar select {
            min-width: 120px;
            max-width: 160px;
            font-size: 12px;
            border-radius: 8px;
            border: 1px solid rgba(31, 102, 186, 0.18);
            background: #fff;
            color: #294c72;
            padding: 4px 8px;
        }
    </style>
</head>

<body>
    <div id="loader">
        <div class="spinner-border text-primary" role="status"></div>
    </div>

    <div class="appHeader text-light">
        <div class="left"></div>
        <div class="pageTitle text-light">Pengaturan Footer</div>
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
                        <h2 class="mb-1">Pengaturan Footer</h2>
                        <p class="text-secondary mb-0">Kelola bagian bawah halaman e-book: profil penulis dan logo apotek pendukung.</p>
                    </div>
                    <a href="{{ route('admin.editor') }}" class="btn btn-outline-secondary">&laquo; Kembali ke Editor</a>
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

                <h3 class="mb-2">Tentang Penulis</h3>
                <form method="POST" action="{{ route('admin.footer.update') }}" enctype="multipart/form-data" class="mb-4">
                    @csrf

                    <div class="form-group mb-3">
                        <label class="form-label">Nama Penulis</label>
                        <input type="text" class="form-control" name="author_name" placeholder="contoh: Ahmad Yasin" value="{{ old('author_name', $content->author_name) }}">
                        <small class="text-muted d-block mt-1">Dipakai untuk menampilkan nama penulis dan membuat inisial placeholder otomatis jika foto belum diisi.</small>
                    </div>

                    <div class="form-group mb-3">
                        <label class="form-label">Tentang Penulis</label>
                        <div class="rich-text-shell">
                            <div class="form-text">Gunakan paragraf, subjudul, daftar poin, tabel, atau gambar untuk menulis profil penulis dengan format yang lebih rapi.</div>
                            <textarea class="form-control rich-text-editor" rows="4" name="intro_note">{{ old('intro_note', $content->intro_note) }}</textarea>
                        </div>
                    </div>

                    <div class="form-group mb-3">
                        <label class="form-label">Path Foto Penulis (relatif dari public)</label>
                        <input type="text" class="form-control" name="author_photo" placeholder="contoh: storage-public/authors/nama-file.png" value="{{ old('author_photo', $content->author_photo) }}">
                        <small class="text-muted d-block mt-1">Opsional. Bisa isi path manual atau upload foto baru.</small>
                    </div>

                    <div class="form-group mb-2">
                        <label class="form-label">Upload Foto Penulis</label>
                        <input type="file" class="form-control" name="author_photo_upload" accept="image/png,image/jpeg,image/webp">
                        <small class="text-muted d-block mt-1">Format: JPG, PNG, WEBP. Maksimal 5MB.</small>
                    </div>

                    @if (!empty($content->author_photo))
                        <div class="mb-3">
                            <img src="{{ asset($content->author_photo) }}" alt="Preview Foto Penulis" style="width: 100%; max-width: 180px; height: auto; border-radius: 16px; border:1px solid rgba(31,102,186,.2); object-fit: cover;">
                        </div>
                    @endif

                    <button type="submit" class="btn btn-ebook">Simpan Tentang Penulis</button>
                </form>

                <hr class="mt-4 mb-3">

                <h3 class="mb-2">Logo Apotek Pendukung</h3>
                <p class="text-secondary mb-3">Kelola logo apotek yang tampil di bagian bawah dashboard, setelah section Tentang Penulis.</p>

                @if (($pharmacyLogos ?? collect())->isNotEmpty())
                    <div class="row g-3 mb-3">
                        @foreach ($pharmacyLogos as $logo)
                            <div class="col-md-6">
                                <div class="border rounded p-3 h-100">
                                    <div class="d-flex align-items-center gap-3 mb-3">
                                        <img src="{{ asset($logo->logo_path) }}" alt="{{ $logo->name }}" style="width:72px;height:56px;object-fit:contain;background:#f5f9ff;border-radius:8px;padding:4px;">
                                        <div class="flex-grow-1">
                                            <div class="fw-semibold">{{ $logo->name }}</div>
                                            @if (!empty($logo->website_url))
                                                <a href="{{ $logo->website_url }}" target="_blank" rel="noopener" class="small text-muted d-inline-block text-truncate" style="max-width:220px;">{{ $logo->website_url }}</a>
                                            @endif
                                        </div>
                                    </div>

                                    <form method="POST" action="{{ route('admin.pharmacy-logos.update', $logo) }}" enctype="multipart/form-data" class="mb-2">
                                        @csrf
                                        <div class="mb-2">
                                            <label class="form-label small mb-1">Nama Apotek</label>
                                            <input type="text" class="form-control form-control-sm" name="name" value="{{ $logo->name }}" required>
                                        </div>
                                        <div class="mb-2">
                                            <label class="form-label small mb-1">Website (opsional)</label>
                                            <input type="url" class="form-control form-control-sm" name="website_url" value="{{ $logo->website_url }}" placeholder="https://...">
                                        </div>
                                        <div class="mb-2">
                                            <label class="form-label small mb-1">Ganti Logo (opsional)</label>
                                            <input type="file" class="form-control form-control-sm" name="logo_upload" accept="image/png,image/jpeg,image/webp,image/svg+xml">
                                        </div>
                                        <button type="submit" class="btn btn-sm btn-primary">Simpan Perubahan</button>
                                    </form>

                                    <form method="POST" action="{{ route('admin.pharmacy-logos.destroy', $logo) }}" onsubmit="return confirm('Hapus logo {{ $logo->name }}?');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-outline-danger">Hapus</button>
                                    </form>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @else
                    <div class="alert alert-light border">Belum ada logo apotek. Tambahkan lewat form di bawah.</div>
                @endif

                <div class="border rounded p-3 bg-light">
                    <h5 class="mb-3">Tambah Logo Apotek</h5>
                    <form method="POST" action="{{ route('admin.pharmacy-logos.store') }}" enctype="multipart/form-data">
                        @csrf
                        <div class="row g-2">
                            <div class="col-md-6">
                                <label class="form-label">Nama Apotek</label>
                                <input type="text" class="form-control" name="name" placeholder="contoh: Apotek Sehat" required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Website (opsional)</label>
                                <input type="url" class="form-control" name="website_url" placeholder="https://...">
                            </div>
                        </div>
                        <div class="mt-2">
                            <label class="form-label">Upload Logo</label>
                            <input type="file" class="form-control" name="logo_upload" accept="image/png,image/jpeg,image/webp,image/svg+xml" required>
                            <small class="text-muted d-block mt-1">Format: JPG, PNG, WEBP, SVG. Maksimal 5MB.</small>
                        </div>
                        <button type="submit" class="btn btn-primary mt-3">Tambah Logo</button>
                    </form>
                </div>

                <div class="mt-4">
                    <a href="{{ route('admin.editor') }}" class="btn btn-outline-secondary">&laquo; Kembali ke Editor</a>
                </div>
            </div>
        </div>
    </div>

    <script src="{{ asset('Mobilekit/HTML/assets/js/lib/bootstrap.min.js') }}"></script>
    <script type="module" src="https://unpkg.com/ionicons@5.5.2/dist/ionicons/ionicons.js"></script>
    <script src="{{ asset('Mobilekit/HTML/assets/js/base.js') }}"></script>
    <script src="https://cdn.ckeditor.com/ckeditor5/39.0.1/classic/ckeditor.js"></script>
    <script>
        const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
        const uploadUrl = "{{ route('admin.ckeditor.upload') }}";

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

        function fontStylePlugin(editor) {
            editor.model.schema.extend('$text', {
                allowAttributes: ['customFontFamily', 'customFontSize']
            });

            editor.conversion.for('downcast').attributeToElement({
                model: 'customFontFamily',
                view: (value, { writer }) => {
                    if (!value) {
                        return;
                    }

                    return writer.createAttributeElement('span', { style: `font-family:${value}` }, { priority: 7 });
                }
            });

            editor.conversion.for('upcast').elementToAttribute({
                view: {
                    name: 'span',
                    styles: { 'font-family': /.+/ }
                },
                model: {
                    key: 'customFontFamily',
                    value: (viewElement) => viewElement.getStyle('font-family')
                }
            });

            editor.conversion.for('downcast').attributeToElement({
                model: 'customFontSize',
                view: (value, { writer }) => {
                    if (!value) {
                        return;
                    }

                    return writer.createAttributeElement('span', { style: `font-size:${value}` }, { priority: 6 });
                }
            });

            editor.conversion.for('upcast').elementToAttribute({
                view: {
                    name: 'span',
                    styles: { 'font-size': /.+/ }
                },
                model: {
                    key: 'customFontSize',
                    value: (viewElement) => viewElement.getStyle('font-size')
                }
            });
        }

        function addTextStyleControls(editor) {
            const toolbarElement = editor.ui.view.toolbar?.element;
            if (!toolbarElement) {
                return;
            }

            const editorRoot = toolbarElement.closest('.ck-editor');
            if (!editorRoot) {
                return;
            }

            const controlsWrapper = editorRoot.querySelector('.text-style-toolbar');
            if (controlsWrapper) {
                return;
            }

            const styleToolbar = document.createElement('div');
            styleToolbar.className = 'text-style-toolbar';
            styleToolbar.innerHTML = `
                <label for="font-family-select">Font</label>
                <select id="font-family-select" data-font-family-select>
                    <option value="">Pilih font</option>
                    <option value="Arial, sans-serif">Arial</option>
                    <option value="Verdana, Geneva, sans-serif">Verdana</option>
                    <option value="Tahoma, Geneva, sans-serif">Tahoma</option>
                    <option value="Georgia, serif">Georgia</option>
                    <option value="Times New Roman, serif">Times New Roman</option>
                </select>
                <label for="font-size-select">Ukuran</label>
                <select id="font-size-select" data-font-size-select>
                    <option value="">Pilih ukuran</option>
                    <option value="12px">12px</option>
                    <option value="14px">14px</option>
                    <option value="16px">16px</option>
                    <option value="18px">18px</option>
                    <option value="20px">20px</option>
                    <option value="24px">24px</option>
                    <option value="28px">28px</option>
                    <option value="32px">32px</option>
                </select>
            `;

            const fontSelect = styleToolbar.querySelector('[data-font-family-select]');
            const sizeSelect = styleToolbar.querySelector('[data-font-size-select]');

            const applySelectedStyles = () => {
                const selectedFont = fontSelect.value;
                const selectedSize = sizeSelect.value;

                if (!selectedFont && !selectedSize) {
                    return;
                }

                editor.model.change((writer) => {
                    const selection = editor.model.document.selection;

                    const applyAttribute = (attributeKey, value) => {
                        if (!value) {
                            return;
                        }

                        if (selection.isCollapsed) {
                            writer.setSelectionAttribute(attributeKey, value);
                            return;
                        }

                        for (const range of editor.model.schema.getValidRanges(selection.getRanges(), attributeKey)) {
                            writer.setAttribute(attributeKey, value, range);
                        }
                    };

                    applyAttribute('customFontFamily', selectedFont);
                    applyAttribute('customFontSize', selectedSize);
                });

                editor.editing.view.focus();
            };

            fontSelect.addEventListener('change', applySelectedStyles);
            sizeSelect.addEventListener('change', applySelectedStyles);

            toolbarElement.insertAdjacentElement('afterend', styleToolbar);
        }

        document.querySelectorAll('.rich-text-editor').forEach((textarea) => {
            ClassicEditor
                .create(textarea, {
                    extraPlugins: [editorPlugin, fontStylePlugin],
                    placeholder: 'Tuliskan profil penulis, latar belakang, pengalaman, atau pesan singkat untuk pembaca...',
                    toolbar: [
                        'heading', '|',
                        'bold', 'italic', 'link', '|',
                        'bulletedList', 'numberedList', '|',
                        'blockQuote', 'imageUpload', '|',
                        'undo', 'redo'
                    ]
                })
                .then((editor) => {
                    addTextStyleControls(editor);
                })
                .catch((error) => {
                    console.error(error);
                });
        });
    </script>
</body>

</html>
