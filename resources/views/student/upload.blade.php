@extends('layouts.student')
@section('title', __('messages.student.upload.title'))
@section('page-title', __('messages.student.upload.title'))

@push('styles')
<style>
.upload-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 1.5rem; }
@media(max-width:860px) { .upload-grid { grid-template-columns: 1fr; } }

/* Drop Zone */
.drop-zone {
    background: var(--card);
    border: 2px dashed rgba(124,58,237,0.35);
    border-radius: 20px; padding: 3rem 2rem;
    text-align: center; cursor: pointer;
    transition: border-color 0.3s, background 0.3s;
    position: relative;
}
.drop-zone.dragging {
    border-color: var(--primary);
    background: rgba(124,58,237,0.08);
}
.drop-zone input[type="file"] {
    position: absolute; inset: 0;
    opacity: 0; cursor: pointer; width: 100%; height: 100%;
}
.drop-icon { font-size: 3.5rem; margin-bottom: 1rem; }
.drop-title { font-size: 1.1rem; font-weight: 700; margin-bottom: 0.5rem; }
.drop-sub   { color: var(--muted); font-size: 0.85rem; line-height: 1.6; }
.drop-types {
    display: flex; gap: 0.5rem; flex-wrap: wrap;
    justify-content: center; margin-top: 1.2rem;
}
.type-chip {
    background: rgba(124,58,237,0.12);
    border: 1px solid rgba(124,58,237,0.2);
    color: #A78BFA; padding: 0.25rem 0.7rem;
    border-radius: 50px; font-size: 0.72rem; font-weight: 600;
}
.file-preview {
    margin-top: 1.2rem; padding: 1rem;
    background: rgba(16,185,129,0.08);
    border: 1px solid rgba(16,185,129,0.2);
    border-radius: 12px; display: none;
    align-items: center; gap: 0.8rem;
    font-size: 0.88rem; color: #34D399;
}
.file-preview.visible { display: flex; }

/* Form panel */
.form-panel {
    background: var(--card);
    border: 1px solid var(--border);
    border-radius: 20px; padding: 2rem;
}
.form-panel h3 { font-size: 1rem; font-weight: 700; margin-bottom: 1.5rem; }
.form-group { margin-bottom: 1.2rem; }
.form-label {
    display: block; font-size: 0.82rem; font-weight: 600;
    color: var(--muted); text-transform: uppercase;
    letter-spacing: 1px; margin-bottom: 0.5rem;
}
.form-control {
    width: 100%;
    background: rgba(255,255,255,0.05);
    border: 1px solid var(--border);
    border-radius: 12px; padding: 0.85rem 1.1rem;
    color: var(--text); font-size: 0.9rem;
    font-family: 'Inter', sans-serif;
    transition: border-color 0.2s;
}
.form-control:focus { outline: none; border-color: var(--primary); }
.form-control::placeholder { color: var(--muted); }
.form-error { font-size: 0.78rem; color: #FCA5A5; margin-top: 0.3rem; }

.btn-upload {
    width: 100%;
    background: linear-gradient(135deg, var(--primary), #9333EA);
    color: #fff; border: none; border-radius: 14px;
    padding: 1rem; font-weight: 700; font-size: 1rem;
    cursor: pointer; margin-top: 0.5rem;
    box-shadow: 0 0 25px var(--glow);
    transition: all 0.25s; display: flex;
    align-items: center; justify-content: center; gap: 0.6rem;
}
.btn-upload:hover { transform: translateY(-2px); box-shadow: 0 0 40px var(--glow); }
.btn-upload:disabled { opacity: 0.5; cursor: not-allowed; transform: none; }

/* Upload Progress */
.upload-progress {
    margin-top: 1rem; display: none;
}
.upload-progress.visible { display: block; }
.progress-track {
    background: rgba(255,255,255,0.06);
    border-radius: 50px; height: 6px; overflow: hidden;
}
.progress-fill {
    height: 100%; border-radius: 50px;
    background: linear-gradient(90deg, var(--primary), var(--secondary));
    transition: width 0.4s ease;
    animation: progressPulse 1.5s ease-in-out infinite;
}
@keyframes progressPulse {
    0%,100% { opacity: 1; } 50% { opacity: 0.7; }
}
.progress-text { font-size: 0.78rem; color: var(--muted); margin-top: 0.4rem; text-align: center; }

/* Recent Uploads Table */
.uploads-panel {
    background: var(--card); border: 1px solid var(--border);
    border-radius: 20px; padding: 1.5rem; margin-top: 1.5rem;
}
.uploads-panel h3 { font-size: 1rem; font-weight: 700; margin-bottom: 1.2rem; }
.upload-row {
    display: flex; align-items: center; gap: 1rem;
    padding: 0.9rem 0; border-bottom: 1px solid rgba(255,255,255,0.04);
}
.upload-row:last-child { border-bottom: none; }
.upload-file-icon {
    width: 40px; height: 40px; border-radius: 10px;
    display: flex; align-items: center; justify-content: center;
    font-size: 1.1rem; flex-shrink: 0;
}
.upload-file-icon.pdf   { background: rgba(239,68,68,0.15); }
.upload-file-icon.pptx  { background: rgba(245,158,11,0.15); }
.upload-file-icon.text  { background: rgba(99,102,241,0.15); }
.upload-file-icon.voice { background: rgba(16,185,129,0.15); }
.upload-info { flex: 1; overflow: hidden; }
.upload-name { font-size: 0.85rem; font-weight: 600; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; }
.upload-date { font-size: 0.72rem; color: var(--muted); margin-top: 0.2rem; }
</style>
@endpush

@section('content')
<div class="upload-grid">

    <!-- Drop Zone -->
    <div>
        <form method="POST" action="{{ route('student.upload.store') }}"
            enctype="multipart/form-data" id="uploadForm">
            @csrf
            <div class="drop-zone" id="dropZone">
                <input type="file" name="file" id="fileInput" accept=".pdf,.pptx,.txt,.doc,.docx,.mp3,.wav,.ogg">
                <div class="drop-icon">📂</div>
                <div class="drop-title">{{ __('messages.student.upload.drop_title') }}</div>
                <div class="drop-sub">{{ __('messages.student.upload.drop_sub') }}<br>{{ __('messages.student.upload.max_size') }}</div>
                <div class="drop-types">
                    <span class="type-chip">PDF</span>
                    <span class="type-chip">PPTX</span>
                    <span class="type-chip">{{ __('messages.student.upload.text_files') }}</span>
                    <span class="type-chip">{{ __('messages.student.upload.audio_files') }}</span>
                </div>
            </div>

            <!-- File Preview -->
            <div class="file-preview" id="filePreview">
                <span>📄</span>
                <span id="fileName">{{ __('messages.student.upload.no_file') }}</span>
                <span id="fileSize" style="color:var(--muted);font-size:0.75rem;margin-left:auto;"></span>
            </div>

            @error('file')
                <div class="form-error">{{ $message }}</div>
            @enderror

            <!-- Upload Progress -->
            <div class="upload-progress" id="uploadProgress">
                <div class="progress-track">
                    <div class="progress-fill" id="progressBar" style="width:0%"></div>
                </div>
                <div class="progress-text" id="progressText">{{ __('messages.student.upload.uploading') }}...</div>
            </div>

            <!-- Caption & Submit (in form panel) -->
            <div class="form-panel" style="margin-top:1.2rem;">
                <h3>{{ __('messages.student.upload.settings_title') }}</h3>
                <div class="form-group">
                    <label class="form-label">{{ __('messages.student.upload.caption_label') }}</label>
                    <input type="text" name="caption" class="form-control"
                        placeholder="{{ __('messages.student.upload.caption_placeholder') }}"
                        value="{{ old('caption') }}" required>
                    @error('caption') <div class="form-error">{{ $message }}</div> @enderror
                </div>

                <div class="form-group" style="padding:1rem;background:rgba(124,58,237,0.08);border:1px solid rgba(124,58,237,0.2);border-radius:12px;">
                    <div style="font-size:0.78rem;font-weight:600;color:#A78BFA;margin-bottom:0.6rem;">🧠 {{ __('messages.student.upload.ai_apply') }}</div>
                    <div style="font-size:0.82rem;color:var(--muted);display:flex;flex-direction:column;gap:0.3rem;">
                        <span>{{ __('messages.student.upload.learning_style') }}: <strong style="color:var(--text);">{{ ucfirst(auth()->user()->learning_style ?? __('messages.student.upload.auto_detect')) }}</strong></span>
                        <span>{{ __('messages.student.upload.proficiency') }}: <strong style="color:var(--text);">{{ ucfirst(auth()->user()->proficiency_level ?? __('messages.student.upload.beginner')) }}</strong></span>
                        <span>{{ __('messages.student.upload.language') }}: <strong style="color:var(--text);">{{ strtoupper(auth()->user()->language_preference ?? __('messages.student.upload.en')) }}</strong></span>
                    </div>
                </div>

                <button type="submit" class="btn-upload" id="submitBtn" disabled>
                    <i class="fas fa-magic"></i> {{ __('messages.student.upload.upload_button') }}
                </button>
            </div>
        </form>
    </div>

    <!-- Right: Info + Recent Uploads -->
    <div>
        <!-- How it works -->
        <div class="form-panel" style="margin-bottom:1.2rem;">
            <h3>{{ __('messages.student.upload.how_it_works_title') }}</h3>
            <div style="display:flex;flex-direction:column;gap:1rem;margin-top:0.5rem;">
                @foreach([
                    ['📤', __('messages.student.upload.step1_title'), __('messages.student.upload.step1_desc')],
                    ['🧹', __('messages.student.upload.step2_title'), __('messages.student.upload.step2_desc')],
                    ['📝', __('messages.student.upload.step3_title'), __('messages.student.upload.step3_desc')],
                    ['🎬', __('messages.student.upload.step4_title'), __('messages.student.upload.step4_desc')],
                    ['📝', __('messages.student.upload.step5_title'), __('messages.student.upload.step5_desc')],
                ] as [$icon, $title, $desc])
                <div style="display:flex;gap:0.9rem;align-items:flex-start;">
                    <div style="width:36px;height:36px;border-radius:10px;background:rgba(124,58,237,0.15);display:flex;align-items:center;justify-content:center;flex-shrink:0;font-size:1rem;">{{ $icon }}</div>
                    <div>
                        <div style="font-weight:600;font-size:0.88rem;margin-bottom:0.2rem;">{{ $title }}</div>
                        <div style="font-size:0.8rem;color:var(--muted);">{{ $desc }}</div>
                    </div>
                </div>
                @endforeach
            </div>
        </div>

        <!-- Recent Uploads -->
        @if($uploads->isNotEmpty())
        <div class="uploads-panel">
            <h3>{{ __('messages.student.upload.recent_uploads') }}</h3>
            @foreach($uploads as $upload)
            <div class="upload-row">
                <div class="upload-file-icon {{ $upload->file_type }}">
                    @switch($upload->file_type)
                        @case('pdf')   📄 @break
                        @case('pptx')  📊 @break
                        @case('voice') 🎙️ @break
                        @default       📝
                    @endswitch
                </div>
                <div class="upload-info">
                    <div class="upload-name">{{ $upload->original_filename }}</div>
                    <div class="upload-date">{{ $upload->created_at->diffForHumans() }} · {{ strtoupper($upload->file_type) }}</div>
                </div>
                <span class="status-badge status-{{ $upload->status }}">{{ ucfirst(__('messages.student.video_status.' . $upload->status)) }}</span>
            </div>
            @endforeach
        </div>
        @endif
    </div>
</div>
@endsection

@push('scripts')
<script>
const messages = {
    noFile: '{{ __("messages.student.upload.no_file") }}',
    uploading: '{{ __("messages.student.upload.uploading") }}',
    processing: '{{ __("messages.student.upload.processing") }}',
    uploadButton: '{{ __("messages.student.upload.upload_button") }}',
    uploadingText: '{{ __("messages.student.upload.uploading") }}'
};

const dropZone   = document.getElementById('dropZone');
const fileInput  = document.getElementById('fileInput');
const filePreview= document.getElementById('filePreview');
const fileName   = document.getElementById('fileName');
const fileSize   = document.getElementById('fileSize');
const submitBtn  = document.getElementById('submitBtn');
const uploadForm = document.getElementById('uploadForm');
const progressWrap = document.getElementById('uploadProgress');
const progressBar  = document.getElementById('progressBar');
const progressText = document.getElementById('progressText');

// Drag & Drop styling
['dragenter','dragover'].forEach(e => dropZone.addEventListener(e, ev => { ev.preventDefault(); dropZone.classList.add('dragging'); }));
['dragleave','drop'].forEach(e => dropZone.addEventListener(e, ev => { ev.preventDefault(); dropZone.classList.remove('dragging'); }));
dropZone.addEventListener('drop', ev => handleFile(ev.dataTransfer.files[0]));
fileInput.addEventListener('change', () => handleFile(fileInput.files[0]));

function formatBytes(bytes) {
    if (bytes < 1024) return bytes + ' B';
    if (bytes < 1048576) return (bytes/1024).toFixed(1) + ' KB';
    return (bytes/1048576).toFixed(1) + ' MB';
}

function handleFile(file) {
    if (!file) return;
    fileName.textContent = file.name;
    fileSize.textContent = formatBytes(file.size);
    filePreview.classList.add('visible');
    submitBtn.disabled = false;
}

// Simulate upload progress on submit
uploadForm.addEventListener('submit', () => {
    submitBtn.disabled = true;
    submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> ' + messages.uploading + '...';
    progressWrap.classList.add('visible');

    let pct = 0;
    const interval = setInterval(() => {
        pct = Math.min(pct + Math.random() * 15, 90);
        progressBar.style.width = pct + '%';
        progressText.textContent = messages.uploadingText + '... ' + Math.round(pct) + '%';
        if (pct >= 90) {
            clearInterval(interval);
            progressText.textContent = messages.processing + '...';
        }
    }, 300);
});
</script>
@endpush