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

        .point-youtube-preview {
            margin-top: 10px;
            padding: 10px;
            border-radius: 12px;
            background: rgba(31, 102, 186, 0.04);
            border: 1px solid rgba(31, 102, 186, 0.12);
        }

        .point-youtube-preview-title {
            font-weight: 700;
            color: #294c72;
            margin-bottom: 8px;
        }

        .point-youtube-preview-frame {
            position: relative;
            width: 100%;
            padding-top: 56.25%;
            border-radius: 12px;
            overflow: hidden;
            background: #000;
            box-shadow: 0 10px 20px rgba(31, 102, 186, 0.2);
        }

        .point-youtube-preview-frame iframe {
            position: absolute;
            inset: 0;
            width: 100%;
            height: 100%;
            border: 0;
        }

        .point-youtube-preview-empty {
            font-size: 13px;
            color: #5b7083;
        }

        .ck-editor__editable {
            min-height: 160px;
        }

        .ck-content ol {
            list-style-type: decimal;
        }

        .ck-content ol ol {
            list-style-type: lower-alpha;
        }

        .ck-content ol ol ol {
            list-style-type: lower-roman;
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

        .member-approval-card {
            border-radius: 14px;
            border: 1px solid rgba(31, 102, 186, 0.14);
            background: rgba(31, 102, 186, 0.03);
            padding: 12px;
            margin-bottom: 10px;
        }

        .member-approval-proof {
            width: 100%;
            max-width: 220px;
            border-radius: 10px;
            border: 1px solid rgba(31, 102, 186, 0.18);
            margin-top: 8px;
        }

        .member-approval-actions {
            display: flex;
            gap: 8px;
            margin-top: 10px;
            flex-wrap: wrap;
        }

        .chapter-tabs,
        .point-tabs {
            display: flex;
            flex-wrap: wrap;
            gap: 8px;
            margin-bottom: 14px;
        }

        .tab-pill {
            display: inline-flex;
            align-items: center;
            padding: 7px 12px;
            border-radius: 999px;
            border: 1px solid rgba(31, 102, 186, 0.18);
            background: #fff;
            color: #294c72;
            font-size: 13px;
            font-weight: 600;
            cursor: pointer;
            transition: background 0.15s ease, color 0.15s ease, border-color 0.15s ease;
        }

        .tab-pill:hover {
            border-color: rgba(31, 102, 186, 0.4);
        }

        .tab-pill.is-active {
            background: var(--editor-primary);
            border-color: var(--editor-primary);
            color: #fff;
        }

        .chapter-card,
        .point-card {
            display: none;
        }

        .chapter-card.is-active,
        .point-card.is-active {
            display: block;
        }

        .point-editor-nav {
            display: grid;
            grid-template-columns: repeat(2, minmax(0, 1fr));
            gap: 12px;
            margin-top: 14px;
        }

        .point-editor-progress {
            grid-column: 1 / -1;
            font-size: 12px;
            font-weight: 700;
            color: #5b7083;
        }

        .point-editor-nav-link {
            display: block;
            width: 100%;
            border-radius: 14px;
            padding: 12px 14px;
            background: var(--editor-soft);
            border: 1px solid rgba(31, 102, 186, 0.14);
            color: #294c72;
            cursor: pointer;
        }

        .point-editor-nav-link.next {
            text-align: right;
        }

        .point-editor-nav-label {
            display: block;
            font-size: 11px;
            font-weight: 700;
            letter-spacing: 0.04em;
            text-transform: uppercase;
            color: #5b7083;
            margin-bottom: 4px;
        }

        .point-editor-nav-title {
            display: block;
            font-size: 14px;
            font-weight: 700;
        }

        @media (max-width: 576px) {
            .point-editor-nav {
                grid-template-columns: 1fr;
            }

            .point-editor-nav-link.next {
                text-align: left;
            }
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

                <div class="mb-4">
                    <h3 class="mb-2">Moderasi Member</h3>
                    <p class="text-secondary mb-2">Approve pendaftar setelah bukti transfer Rp. 99.000 sesuai.</p>

                    @if (($pendingMembers ?? collect())->isEmpty())
                        <div class="alert alert-light border">Tidak ada pendaftar yang menunggu verifikasi.</div>
                    @else
                        @foreach ($pendingMembers as $member)
                            <div class="member-approval-card">
                                <div><strong>{{ $member->name }}</strong></div>
                                <div class="small text-muted">{{ $member->email }}</div>
                                <div class="small text-muted">Daftar: {{ optional($member->created_at)->format('d M Y H:i') }}</div>

                                @if (!empty($member->payment_proof_path))
                                    <a href="{{ asset($member->payment_proof_path) }}" target="_blank" rel="noopener">
                                        <img src="{{ asset($member->payment_proof_path) }}" alt="Bukti Transfer {{ $member->name }}" class="member-approval-proof">
                                    </a>
                                @endif

                                <div class="member-approval-actions">
                                    <form method="POST" action="{{ route('admin.members.approve', $member) }}">
                                        @csrf
                                        <button type="submit" class="btn btn-sm btn-success">Approve</button>
                                    </form>
                                    <form method="POST" action="{{ route('admin.members.reject', $member) }}">
                                        @csrf
                                        <button type="submit" class="btn btn-sm btn-outline-danger">Tolak</button>
                                    </form>
                                </div>
                            </div>
                        @endforeach
                    @endif

                    @if (($approvedMembers ?? collect())->isNotEmpty())
                        <div class="mt-3">
                            <h4 class="mb-2">Member Aktif Terbaru</h4>
                            <div class="table-responsive">
                                <table class="table table-sm align-middle">
                                    <thead>
                                        <tr>
                                            <th>Nama</th>
                                            <th>Email</th>
                                            <th>Aktif Sejak</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($approvedMembers as $member)
                                            <tr>
                                                <td>{{ $member->name }}</td>
                                                <td>{{ $member->email }}</td>
                                                <td>{{ optional($member->updated_at)->format('d M Y H:i') }}</td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    @endif
                </div>

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
                    <p class="text-secondary small mb-2">Pilih bab dan poin di bawah untuk mengisi satu poin per halaman, seperti tampilan pembaca di frontend.</p>

                    <div id="point-editor-anchor"></div>
                    <div id="chapter-tabs" class="chapter-tabs"></div>
                    <div class="chapter-tabs">
                        <a href="{{ route('admin.footer.edit') }}" class="tab-pill">Footer (Logo Apotek &amp; Tentang Penulis)</a>
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

                                <div class="point-tabs" data-point-tabs></div>

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

                                            <div class="form-group mt-2 mb-0">
                                                <label class="form-label">Link YouTube (opsional)</label>
                                                <input type="url" class="form-control point-youtube-url" name="chapters[{{ $index }}][items][{{ $pointIndex }}][youtube_url]" value="{{ $point['youtube_url'] ?? '' }}" placeholder="contoh: https://www.youtube.com/watch?v=xxxx atau https://youtu.be/xxxx">
                                                <small class="text-muted d-block mt-1">Video akan tampil otomatis di halaman poin saat link valid YouTube diisi.</small>
                                            </div>

                                            <div class="point-youtube-preview" data-youtube-preview>
                                                <div class="point-youtube-preview-title">Preview Video</div>
                                                <div class="point-youtube-preview-body"></div>
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

                    <div id="point-editor-nav" class="point-editor-nav"></div>

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

            <div class="point-tabs" data-point-tabs></div>

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

            <div class="form-group mt-2 mb-0">
                <label class="form-label">Link YouTube (opsional)</label>
                <input type="url" class="form-control point-youtube-url" placeholder="contoh: https://www.youtube.com/watch?v=xxxx atau https://youtu.be/xxxx">
                <small class="text-muted d-block mt-1">Video akan tampil otomatis di halaman poin saat link valid YouTube diisi.</small>
            </div>

            <div class="point-youtube-preview" data-youtube-preview>
                <div class="point-youtube-preview-title">Preview Video</div>
                <div class="point-youtube-preview-body"></div>
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
    <script src="https://cdn.ckeditor.com/ckeditor5/39.0.1/super-build/ckeditor.js"></script>
    <script>
        const ClassicEditor = window.CKEDITOR.ClassicEditor;
        const CKEDITOR_REMOVE_PLUGINS = [
            'CKBox', 'CKFinder', 'CKFinderUploadAdapter', 'EasyImage', 'CloudServices',
            'Comments', 'TrackChanges', 'TrackChangesData',
            'RealTimeCollaborativeComments', 'RealTimeCollaborativeRevisionHistory', 'RealTimeCollaborativeTrackChanges',
            'PresenceList', 'RevisionHistory', 'StandardEditingMode',
            'ExportPdf', 'ExportWord', 'MathType', 'WProofreader', 'Pagination',
            'DocumentOutline', 'TableOfContents', 'FormatPainter', 'Template', 'SlashCommand',
            'PasteFromOfficeEnhanced'
        ];
        const wrapper = document.getElementById('chapters-wrapper');
        const chapterTemplate = document.getElementById('chapter-template');
        const pointTemplate = document.getElementById('point-template');
        const addChapterButton = document.getElementById('add-chapter');
        const editorForm = document.getElementById('editor-form');
        const editorInstances = new Map();
        const sectionSaveActions = document.querySelectorAll('.section-save-actions');
        const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
        const uploadUrl = "{{ route('admin.ckeditor.upload') }}";
        const SCROLL_Y_KEY = 'adminEditorScrollY';
        const RESTORE_SCROLL_FLAG_KEY = 'adminEditorRestoreScroll';
        const ACTIVE_CHAPTER_INDEX_KEY = 'adminEditorActiveChapterIndex';
        const ACTIVE_POINT_INDEX_KEY = 'adminEditorActivePointIndex';
        const chapterTabsContainer = document.getElementById('chapter-tabs');
        const pointEditorNav = document.getElementById('point-editor-nav');
        const pointEditorAnchor = document.getElementById('point-editor-anchor');
        let hasChanges = false;
        let activeChapterCard = null;
        let activePointCard = null;

        function saveScrollPositionForReload() {
            sessionStorage.setItem(SCROLL_Y_KEY, String(window.scrollY || window.pageYOffset || 0));
            sessionStorage.setItem(RESTORE_SCROLL_FLAG_KEY, '1');
        }

        function restoreScrollPositionAfterReload() {
            if (sessionStorage.getItem(RESTORE_SCROLL_FLAG_KEY) !== '1') {
                return;
            }

            const savedY = Number.parseInt(sessionStorage.getItem(SCROLL_Y_KEY) || '', 10);
            sessionStorage.removeItem(RESTORE_SCROLL_FLAG_KEY);
            sessionStorage.removeItem(SCROLL_Y_KEY);

            if (Number.isNaN(savedY)) {
                return;
            }

            requestAnimationFrame(() => {
                requestAnimationFrame(() => {
                    window.scrollTo({
                        top: savedY,
                        behavior: 'auto',
                    });
                });
            });
        }

        function saveActiveChapterPointForReload() {
            if (!activeChapterCard || !activePointCard) {
                return;
            }

            const chapterIndex = Array.from(wrapper.querySelectorAll('.chapter-card')).indexOf(activeChapterCard);
            const pointIndex = Array.from(activeChapterCard.querySelectorAll('.point-card')).indexOf(activePointCard);

            if (chapterIndex === -1 || pointIndex === -1) {
                return;
            }

            sessionStorage.setItem(ACTIVE_CHAPTER_INDEX_KEY, String(chapterIndex));
            sessionStorage.setItem(ACTIVE_POINT_INDEX_KEY, String(pointIndex));
        }

        function restoreActiveChapterPointAfterReload() {
            const chapterIndex = Number.parseInt(sessionStorage.getItem(ACTIVE_CHAPTER_INDEX_KEY) || '', 10);
            const pointIndex = Number.parseInt(sessionStorage.getItem(ACTIVE_POINT_INDEX_KEY) || '', 10);

            sessionStorage.removeItem(ACTIVE_CHAPTER_INDEX_KEY);
            sessionStorage.removeItem(ACTIVE_POINT_INDEX_KEY);

            if (Number.isNaN(chapterIndex) || Number.isNaN(pointIndex)) {
                return false;
            }

            const chapterCard = wrapper.querySelectorAll('.chapter-card')[chapterIndex];
            if (!chapterCard) {
                return false;
            }

            const pointCard = chapterCard.querySelectorAll('.point-card')[pointIndex];
            if (!pointCard) {
                return false;
            }

            activateChapter(chapterCard, { pointCard, skipScroll: true });
            return true;
        }

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

        function initEditorForTextarea(textarea) {
            if (!textarea || textarea.dataset.ckeditorInitialized === '1') {
                return;
            }

            const isRichTextEditor = textarea.classList.contains('rich-text-editor');
            const editorConfig = {
                extraPlugins: [editorPlugin, fontStylePlugin],
                removePlugins: CKEDITOR_REMOVE_PLUGINS,
                list: {
                    properties: {
                        styles: true
                    }
                },
                toolbar: isRichTextEditor ? [
                    'heading', '|',
                    'bold', 'italic', 'link', '|',
                    'bulletedList', 'numberedList', '|',
                    'blockQuote', 'imageUpload', '|',
                    'undo', 'redo'
                ] : [
                    'heading', '|',
                    'imageUpload',
                    'bold', 'italic', 'link', '|',
                    'bulletedList', 'numberedList', '|',
                    'blockQuote', 'undo', 'redo'
                ]
            };

            if (isRichTextEditor) {
                editorConfig.placeholder = 'Tuliskan profil penulis, latar belakang, pengalaman, atau pesan singkat untuk pembaca...';
            }

            ClassicEditor
                .create(textarea, editorConfig)
                .then((editor) => {
                    addTextStyleControls(editor);
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
            container.querySelectorAll('.point-content, .rich-text-editor').forEach((textarea) => {
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

        function resolveYoutubeEmbedUrl(url) {
            const value = String(url || '').trim();
            if (!value) {
                return null;
            }

            let parsed;
            try {
                parsed = new URL(value);
            } catch (error) {
                return null;
            }

            const host = parsed.hostname.toLowerCase();
            const path = parsed.pathname || '';
            let videoId = '';

            if (host.includes('youtu.be')) {
                videoId = path.replace(/^\/+/, '').split('/')[0] || '';
            } else if (host.includes('youtube.com')) {
                if (path.startsWith('/watch')) {
                    videoId = parsed.searchParams.get('v') || '';
                } else if (path.startsWith('/shorts/')) {
                    videoId = path.slice('/shorts/'.length).split('/')[0] || '';
                } else if (path.startsWith('/embed/')) {
                    videoId = path.slice('/embed/'.length).split('/')[0] || '';
                }
            }

            if (!/^[A-Za-z0-9_-]{11}$/.test(videoId)) {
                return null;
            }

            return `https://www.youtube.com/embed/${videoId}`;
        }

        function renderYoutubePreviewForInput(youtubeInput) {
            const pointCard = youtubeInput.closest('.point-card');
            if (!pointCard) {
                return;
            }

            const previewBody = pointCard.querySelector('.point-youtube-preview-body');
            if (!previewBody) {
                return;
            }

            const embedUrl = resolveYoutubeEmbedUrl(youtubeInput.value);

            if (!embedUrl) {
                previewBody.innerHTML = '<div class="point-youtube-preview-empty">Tempel link YouTube yang valid untuk melihat preview video.</div>';
                return;
            }

            previewBody.innerHTML = `
                <div class="point-youtube-preview-frame">
                    <iframe
                        src="${embedUrl}"
                        title="Preview Video YouTube"
                        loading="lazy"
                        referrerpolicy="strict-origin-when-cross-origin"
                        allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture; web-share"
                        allowfullscreen>
                    </iframe>
                </div>
            `;
        }

        function renderAllYoutubePreviews(container) {
            container.querySelectorAll('.point-youtube-url').forEach((youtubeInput) => {
                renderYoutubePreviewForInput(youtubeInput);
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
                    pointCard.querySelector('.point-youtube-url').name = `chapters[${chapterIndex}][items][${pointIndex}][youtube_url]`;
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

        function chapterTabLabel(chapterCard, index) {
            const value = (chapterCard.querySelector('.chapter-title')?.value || '').trim();
            return value ? `${index + 1}. ${value}` : `Bab ${index + 1}`;
        }

        function pointTabLabel(pointCard, index) {
            const value = (pointCard.querySelector('.point-title')?.value || '').trim();
            return value || `Poin ${index + 1}`;
        }

        function pointLabelForEntry(entry) {
            const siblings = Array.from(entry.chapterCard.querySelectorAll('.point-card'));
            return pointTabLabel(entry.pointCard, siblings.indexOf(entry.pointCard));
        }

        function getFlattenedPointCards() {
            const flattened = [];

            wrapper.querySelectorAll('.chapter-card').forEach((chapterCard) => {
                chapterCard.querySelectorAll('.point-card').forEach((pointCard) => {
                    flattened.push({ chapterCard, pointCard });
                });
            });

            return flattened;
        }

        function buildChapterTabs() {
            if (!chapterTabsContainer) {
                return;
            }

            chapterTabsContainer.innerHTML = '';

            wrapper.querySelectorAll('.chapter-card').forEach((chapterCard, index) => {
                const pill = document.createElement('button');
                pill.type = 'button';
                pill.className = 'tab-pill' + (chapterCard === activeChapterCard ? ' is-active' : '');
                pill.textContent = chapterTabLabel(chapterCard, index);
                pill.addEventListener('click', () => activateChapter(chapterCard));
                chapterTabsContainer.appendChild(pill);
            });
        }

        function buildPointTabs(chapterCard) {
            const pointTabsContainer = chapterCard.querySelector('[data-point-tabs]');
            if (!pointTabsContainer) {
                return;
            }

            pointTabsContainer.innerHTML = '';

            chapterCard.querySelectorAll('.point-card').forEach((pointCard, index) => {
                const pill = document.createElement('button');
                pill.type = 'button';
                pill.className = 'tab-pill' + (pointCard === activePointCard ? ' is-active' : '');
                pill.textContent = pointTabLabel(pointCard, index);
                pill.addEventListener('click', () => activatePoint(chapterCard, pointCard));
                pointTabsContainer.appendChild(pill);
            });
        }

        function activateChapter(chapterCard, options = {}) {
            if (!chapterCard) {
                return;
            }

            wrapper.querySelectorAll('.chapter-card').forEach((card) => {
                card.classList.toggle('is-active', card === chapterCard);
            });

            activeChapterCard = chapterCard;
            buildChapterTabs();

            const targetPointCard = options.pointCard || chapterCard.querySelector('.point-card');
            activatePoint(chapterCard, targetPointCard, options);
        }

        function activatePoint(chapterCard, pointCard, options = {}) {
            if (!chapterCard || !pointCard) {
                return;
            }

            wrapper.querySelectorAll('.point-card').forEach((card) => {
                card.classList.toggle('is-active', card === pointCard);
            });

            activePointCard = pointCard;
            buildPointTabs(chapterCard);
            updatePointEditorNav();

            if (!options.skipScroll && pointEditorAnchor) {
                pointEditorAnchor.scrollIntoView({ behavior: 'smooth', block: 'start' });
            }
        }

        function updatePointEditorNav() {
            if (!pointEditorNav) {
                return;
            }

            pointEditorNav.innerHTML = '';

            const flattened = getFlattenedPointCards();
            const currentIndex = flattened.findIndex((entry) => entry.pointCard === activePointCard);

            if (currentIndex === -1) {
                return;
            }

            const progress = document.createElement('div');
            progress.className = 'point-editor-progress';
            progress.textContent = `Poin ${currentIndex + 1} dari ${flattened.length}`;
            pointEditorNav.appendChild(progress);

            const previousEntry = currentIndex > 0 ? flattened[currentIndex - 1] : null;
            const nextEntry = currentIndex < flattened.length - 1 ? flattened[currentIndex + 1] : null;

            const previousSlot = document.createElement('div');
            if (previousEntry) {
                const button = document.createElement('button');
                button.type = 'button';
                button.className = 'point-editor-nav-link previous';
                button.innerHTML = '<span class="point-editor-nav-label">&laquo; Poin Sebelumnya</span><span class="point-editor-nav-title"></span>';
                button.querySelector('.point-editor-nav-title').textContent = pointLabelForEntry(previousEntry);
                button.addEventListener('click', () => {
                    activateChapter(previousEntry.chapterCard, { pointCard: previousEntry.pointCard });
                });
                previousSlot.appendChild(button);
            }
            pointEditorNav.appendChild(previousSlot);

            const nextSlot = document.createElement('div');
            if (nextEntry) {
                const button = document.createElement('button');
                button.type = 'button';
                button.className = 'point-editor-nav-link next';
                button.innerHTML = '<span class="point-editor-nav-label">Poin Berikutnya &raquo;</span><span class="point-editor-nav-title"></span>';
                button.querySelector('.point-editor-nav-title').textContent = pointLabelForEntry(nextEntry);
                button.addEventListener('click', () => {
                    activateChapter(nextEntry.chapterCard, { pointCard: nextEntry.pointCard });
                });
                nextSlot.appendChild(button);
            }
            pointEditorNav.appendChild(nextSlot);
        }

        function addChapter() {
            wrapper.appendChild(chapterTemplate.content.cloneNode(true));
            const chapterCard = wrapper.lastElementChild;
            ensurePoint(chapterCard);
            refreshIndexes();
            initEditorsIn(chapterCard);
            renderAllYoutubePreviews(chapterCard);
            setDirtyState(true);
            activateChapter(chapterCard);
        }

        function addPoint(chapterCard) {
            chapterCard.querySelector('.points-wrapper').appendChild(pointTemplate.content.cloneNode(true));
            refreshIndexes();
            initEditorsIn(chapterCard);
            renderAllYoutubePreviews(chapterCard);
            setDirtyState(true);

            const pointCards = chapterCard.querySelectorAll('.point-card');
            activateChapter(chapterCard, { pointCard: pointCards[pointCards.length - 1] });
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
                const wasActive = pointCard === activePointCard;
                const textarea = pointCard.querySelector('.point-content');
                destroyEditorForTextarea(textarea);

                pointCard.remove();
                refreshIndexes();
                setDirtyState(true);

                if (wasActive) {
                    activateChapter(chapterCard, { pointCard: chapterCard.querySelector('.point-card') });
                } else {
                    buildPointTabs(chapterCard);
                    updatePointEditorNav();
                }
                return;
            }

            const removeChapterButton = event.target.closest('.remove-chapter');
            if (removeChapterButton) {
                const chapterCards = wrapper.querySelectorAll('.chapter-card');
                if (chapterCards.length <= 1) {
                    return;
                }

                const chapterCard = removeChapterButton.closest('.chapter-card');
                const wasActive = chapterCard === activeChapterCard;
                chapterCard.remove();
                refreshIndexes();
                setDirtyState(true);

                if (wasActive) {
                    activateChapter(wrapper.querySelector('.chapter-card'));
                } else {
                    buildChapterTabs();
                }
            }
        });

        wrapper.addEventListener('change', (event) => {
            const documentInput = event.target.closest('.point-document');
            if (documentInput) {
                renderDocumentPreview(documentInput);
            }

            const youtubeInput = event.target.closest('.point-youtube-url');
            if (youtubeInput) {
                renderYoutubePreviewForInput(youtubeInput);
            }
        });

        wrapper.addEventListener('input', (event) => {
            const youtubeInput = event.target.closest('.point-youtube-url');
            if (youtubeInput) {
                renderYoutubePreviewForInput(youtubeInput);
            }

            if (event.target.closest('.chapter-title')) {
                buildChapterTabs();
            }

            if (event.target.closest('.point-title')) {
                const chapterCard = event.target.closest('.chapter-card');
                if (chapterCard) {
                    buildPointTabs(chapterCard);
                }
                updatePointEditorNav();
            }
        });

        wrapper.querySelectorAll('.point-document').forEach((documentInput) => {
            renderDocumentPreview(documentInput);
        });

        renderAllYoutubePreviews(wrapper);

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

        initEditorsIn(editorForm);

        editorForm.addEventListener('submit', () => {
            saveScrollPositionForReload();
            saveActiveChapterPointForReload();

            editorInstances.forEach((editor) => {
                editor.updateSourceElement();
            });

            setDirtyState(false);
        });

        setDirtyState(false);

        refreshIndexes();

        if (!restoreActiveChapterPointAfterReload() && !activeChapterCard) {
            const initialChapterCard = wrapper.querySelector('.chapter-card');
            if (initialChapterCard) {
                activateChapter(initialChapterCard, { skipScroll: true });
            }
        }

        restoreScrollPositionAfterReload();
    </script>
</body>

</html>
