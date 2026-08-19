import tinymce from 'tinymce';

import 'tinymce/themes/silver';
import 'tinymce/icons/default';
import 'tinymce/models/dom';

// Import plugins
import 'tinymce/plugins/advlist';
import 'tinymce/plugins/autolink';
import 'tinymce/plugins/lists';
import 'tinymce/plugins/link';
import 'tinymce/plugins/image';
import 'tinymce/plugins/charmap';
import 'tinymce/plugins/preview';
import 'tinymce/plugins/anchor';
import 'tinymce/plugins/searchreplace';
import 'tinymce/plugins/visualblocks';
import 'tinymce/plugins/code';
import 'tinymce/plugins/fullscreen';
import 'tinymce/plugins/insertdatetime';
import 'tinymce/plugins/media';
import 'tinymce/plugins/table';
import 'tinymce/plugins/wordcount';

// Import skin
import 'tinymce/skins/ui/oxide/skin.css';
import 'tinymce/skins/ui/oxide/content.css';
import 'tinymce/skins/content/default/content.css';

function buildTrustedReferenceTemplate(config) {
    const safeTitle = escapeHtml(config.title || 'Referensi tepercaya');
    const safeDescription = escapeHtml(config.description || 'Sumber primer yang digunakan sebagai dasar penulisan dan verifikasi informasi.');
    const titleIcon = String(config.titleIcon ?? '').trim() || 'icon-[tabler--books]';
    const titleIconMarkup = /^icon-\[[^\]]+\]$/u.test(titleIcon)
        ? `<span class="${escapeHtmlAttribute(titleIcon)} size-6"></span>`
        : `<span>${escapeHtml(titleIcon)}</span>`;
    const buildItemIcon = (source) => {
        const iconText = String(source.icon ?? '').trim();
        return `<span class="item-icon">${escapeHtml(iconText || '🔖')}</span>`;
    };

    const sourceList = (config.sources || [])
        .filter((source) => source.text && source.url)
        .map(
            (source) => `<a href="${escapeHtmlAttribute(source.url)}" target="_blank" rel="noopener noreferrer" class="reference-item">${buildItemIcon(source)}<span class="item-text">${escapeHtml(source.text)}</span><span class="external-icon"><span class="icon-[tabler--external-link] size-4 shrink-0 transition group-hover:translate-x-0.5" aria-hidden="true"></span></span></a>`,
        )
        .join('');

    return `<div class="reference-card"><div class="reference-header"><div class="reference-icon">${titleIconMarkup}</div><div class="reference-titles"><h4>${safeTitle}</h4><p>${safeDescription}</p></div></div><div class="reference-links">${sourceList}</div></div>`;
}

function escapeHtml(value) {
    return String(value).replace(/[&<>"']/gu, (char) => ({
        '&': '&amp;',
        '<': '&lt;',
        '>': '&gt;',
        '"': '&quot;',
        "'": '&#039;',
    })[char]);
}

function escapeHtmlAttribute(value) {
    return String(value).replace(/[&<>"']/gu, (char) => ({
        '&': '&amp;',
        '<': '&lt;',
        '>': '&gt;',
        '"': '&quot;',
        "'": '&#039;',
    })[char]);
}

function openTrustedReferenceBuilder(editor) {
    const existingDialog = document.getElementById('trusted-reference-builder-modal');

    if (existingDialog) {
        existingDialog.remove();
    }

    const modal = document.createElement('div');
    modal.id = 'trusted-reference-builder-modal';
    modal.style.cssText = 'align-items: center; background: rgba(15, 23, 42, 0.55); inset: 0; display: flex; justify-content: center; position: fixed; z-index: 9999;';
    modal.innerHTML = `
        <div style="background: #ffffff; border-radius: 14px; box-sizing: border-box; max-width: 760px; max-height: 86vh; overflow: auto; padding: 16px; width: min(94vw, 760px);">
            <h3 style="font-size: 18px; font-weight: 700; margin: 0 0 10px;">Sisipkan Referensi Tepercaya</h3>
            <p style="color: #475569; font-size: 13px; margin: 0 0 14px;">Isi judul, deskripsi, lalu tambah sumber referensi sesuai kebutuhan.</p>
            <div style="display: flex; flex-direction: column; gap: 10px; width: 100%; box-sizing: border-box;">
                <label style="font-size: 12px; font-weight: 600;">Judul</label>
                <input id="trusted-reference-title" value="Referensi tepercaya" style="border: 1px solid #d8dde4; border-radius: 10px; box-sizing: border-box; padding: 10px; width: 100%;">
                <label style="font-size: 12px; font-weight: 600;">Icon judul</label>
                <input id="trusted-reference-title-icon" value="icon-[tabler--books]" style="border: 1px solid #d8dde4; border-radius: 10px; box-sizing: border-box; padding: 10px; width: 100%;" placeholder="icon class (contoh: icon-[tabler--books])">
                <label style="font-size: 12px; font-weight: 600;">Deskripsi</label>
                <textarea id="trusted-reference-description" rows="2" style="border: 1px solid #d8dde4; border-radius: 10px; box-sizing: border-box; padding: 10px; resize: vertical; width: 100%;">Sumber primer yang digunakan sebagai dasar penulisan dan verifikasi informasi.</textarea>
                <div id="trusted-reference-rows"></div>
                <div style="display: flex; flex-wrap: wrap; gap: 10px; justify-content: space-between;">
                    <button id="trusted-reference-add" type="button" style="border: none; background: #e2f1ef; border-radius: 10px; padding: 8px 12px; color: #0f766e; font-weight: 700; cursor: pointer;">+ Tambah sumber</button>
                    <div style="display: flex; flex-wrap: wrap; gap: 8px; width: 100%; justify-content: flex-end;">
                        <button id="trusted-reference-cancel" type="button" style="border: none; background: #f1f5f9; border-radius: 10px; padding: 8px 12px; cursor: pointer; min-width: 96px; width: fit-content;">Batal</button>
                        <button id="trusted-reference-insert" type="button" style="border: none; background: #0d8a72; border-radius: 10px; color: #fff; padding: 8px 12px; font-weight: 700; cursor: pointer; min-width: 96px; width: fit-content;">Sisipkan</button>
                    </div>
                </div>
            </div>
        </div>
    `;

    const rowsContainer = modal.querySelector('#trusted-reference-rows');

    const addRow = (label = '', url = '', icon = '🔖') => {
        const row = document.createElement('div');
        row.className = 'trusted-reference-row';
        row.style.cssText = 'border: 1px solid #d8dde4; border-radius: 10px; box-sizing: border-box; display: grid; gap: 8px; grid-template-columns: repeat(auto-fit, minmax(150px, 1fr)); margin-bottom: 8px; padding: 10px;';
        row.innerHTML = `
            <div style="display: flex; flex-direction: column; gap: 4px;">
                <label style="font-size: 11px; font-weight: 700;">Nama sumber</label>
                <input data-source-title style="border: 1px solid #d8dde4; border-radius: 8px; box-sizing: border-box; min-width: 0; padding: 8px; width: 100%;" value="${escapeHtmlAttribute(label)}">
            </div>
            <div style="display: flex; flex-direction: column; gap: 4px;">
                <label style="font-size: 11px; font-weight: 700;">URL</label>
                <input data-source-url style="border: 1px solid #d8dde4; border-radius: 8px; box-sizing: border-box; min-width: 0; padding: 8px; width: 100%;" value="${escapeHtmlAttribute(url)}">
            </div>
            <div style="display: flex; flex-direction: column; gap: 4px;">
                <label style="font-size: 11px; font-weight: 700;">Icon</label>
                <input data-source-icon style="border: 1px solid #d8dde4; border-radius: 8px; box-sizing: border-box; min-width: 0; padding: 8px; width: 100%;" placeholder="Mis. 🔖, 📄, 🌐" value="${escapeHtmlAttribute(icon)}">
            </div>
            <div style="align-self: end; min-width: 80px;">
                <button type="button" data-action="remove" style="border: none; background: #fee2e2; color: #b91c1c; border-radius: 8px; padding: 8px 10px; font-weight: 700; cursor: pointer;">Hapus</button>
            </div>
        `;
        row.querySelector('[data-action="remove"]').addEventListener('click', () => {
            if (rowsContainer.children.length > 1) {
                row.remove();
            } else {
                alert('Minimal 1 sumber tetap ada.');
            }
        });
        rowsContainer.append(row);
    };

    addRow('W3C WebAuthn Level 3', 'https://www.w3.org/TR/webauthn-3/', '📚');
    addRow('FIDO Alliance — The State of Passkeys 2026', 'https://fidoalliance.org/wp-content/uploads/2026/05/The-State-of-Passkeys-Global-Consumer-and-Workforce-Report-1.pdf', '🔗');

    modal.querySelector('#trusted-reference-add').addEventListener('click', () => addRow());

    modal.querySelector('#trusted-reference-cancel').addEventListener('click', () => {
        modal.remove();
    });

    modal.querySelector('#trusted-reference-insert').addEventListener('click', () => {
        const title = modal.querySelector('#trusted-reference-title').value.trim();
        const titleIcon = modal.querySelector('#trusted-reference-title-icon').value.trim();
        const description = modal.querySelector('#trusted-reference-description').value.trim();
        const sources = Array.from(modal.querySelectorAll('.trusted-reference-row')).map((row) => ({
            text: row.querySelector('[data-source-title]')?.value.trim() ?? '',
            url: row.querySelector('[data-source-url]')?.value.trim() ?? '',
            icon: row.querySelector('[data-source-icon]')?.value.trim() ?? '',
        })).filter((source) => source.text !== '' && source.url !== '');

        if (title === '') {
            alert('Judul tidak boleh kosong.');

            return;
        }

        if (sources.length === 0) {
            alert('Tambahkan minimal satu sumber.');

            return;
        }

        editor.insertContent(buildTrustedReferenceTemplate({
            title,
            description,
            titleIcon,
            sources,
        }));
        modal.remove();
    });

    modal.addEventListener('click', (event) => {
        if (event.target === modal) {
            modal.remove();
        }
    });

    document.body.append(modal);
}

function initTinyMce() {
    const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '';
    const uploadUrl = window.tinymceUploadUrl || '/';

    if (typeof tinymce === 'undefined' || !tinymce) {
        console.error('TinyMCE library not loaded');
        return;
    }

    document.querySelectorAll('textarea[data-richtext-editor="1"]').forEach(textarea => {
        const existingEditor = tinymce.get(textarea.id || textarea.getAttribute('name'));
        const alreadyInitialized = textarea.dataset.tinymceInitialized === '1' || existingEditor !== null;

        if (alreadyInitialized) {
            return;
        }

        tinymce.init({
            license_key: 'gpl',
            target: textarea,
            plugins: 'advlist autolink lists link image charmap preview anchor searchreplace visualblocks code fullscreen insertdatetime media table wordcount',
            toolbar: 'undo redo | formatselect | bold italic underline strikethrough | forecolor backcolor | alignleft aligncenter alignright alignjustify | outdent indent | numlist bullist | link image media table | blockquote code removeformat | fullscreen preview | trusted_reference',
            menubar: 'file edit view insert format tools table',
            skin: false,
            content_css: false,
            branding: false,
            relative_urls: false,
            remove_script_host: false,
            convert_urls: true,
            height: 550,
            promotion: false,
            content_style: `
body { font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, "Helvetica Neue", Arial, sans-serif; font-size: 16px; line-height: 1.6; }
.reference-card { background-color: #f2f9f6; border: 1px solid #d1eae1; border-radius: 16px; padding: 20px; margin: 24px 0; }
.reference-header { display: flex; align-items: flex-start; gap: 12px; margin-bottom: 16px; }
.reference-icon { background-color: #0d8a72; color: #ffffff; padding: 10px; border-radius: 12px; display: inline-flex; align-items: center; justify-content: center; }
.reference-titles h4 { margin: 0 0 4px 0; font-size: 18px; font-weight: 700; color: #0f172a; }
.reference-titles p { margin: 0; font-size: 13px; color: #64748b; }
.reference-links { display: flex; flex-direction: column; gap: 10px; }
.reference-item { display: flex; align-items: center; background: #ffffff; border: 1px solid #e2e8f0; border-radius: 12px; padding: 12px 16px; text-decoration: none; color: #0f172a; transition: all 0.2s ease; }
.reference-item:hover { border-color: #0d8a72; box-shadow: 0 2px 4px rgba(0,0,0,0.05); }
.item-icon { color: #0d8a72; margin-right: 12px; display: inline-flex; }
.item-text { flex-grow: 1; font-weight: 600; font-size: 14px; }
.external-icon { color: #64748b; display: inline-flex; }
            `,
            setup: (editor) => {
                editor.ui.registry.addMenuItem('trusted_reference_menu', {
                    text: 'Referensi Tepercaya',
                    icon: 'bookmark',
                    onAction: () => openTrustedReferenceBuilder(editor),
                });
                editor.ui.registry.addButton('trusted_reference', {
                    text: 'Referensi',
                    icon: 'bookmark',
                    tooltip: 'Buat blok Referensi Tepercaya',
                    onAction: () => openTrustedReferenceBuilder(editor),
                });
            },
            menu: {
                insert: {
                    title: 'Insert',
                    items: 'link image media table anchor charmap insertdatetime trusted_reference_menu',
                },
            },
            images_upload_handler: (blobInfo, success, failure) => {
                const formData = new FormData();
                formData.append('image', blobInfo.blob(), blobInfo.filename());
                formData.append('alt_text', blobInfo.filename());
                formData.append('_token', csrfToken);

                fetch(uploadUrl, {
                    method: 'POST',
                    body: formData,
                    headers: {
                        'X-CSRF-TOKEN': csrfToken,
                        'Accept': 'application/json',
                    },
                })
                    .then(response => response.json())
                    .then(result => {
                        const publicUrl = result.data?.publicUrl ?? result.data?.public_url ?? result.data?.url;

                        if (result.success && publicUrl) {
                            success(publicUrl);
                        } else {
                            failure('Upload gagal: ' + (result.message || 'Unknown error'));
                        }
                    })
                    .catch(err => failure('Upload error: ' + err.message));
            },
            images_upload_credentials: true,
            automatic_uploads: true,
            images_reuse_filename: true,
            init_instance_callback: editor => {
                textarea.dataset.tinymceInitialized = '1';
            },
        });
    });
}

// Ensure DOM is fully loaded before initializing TinyMCE
if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', initTinyMce);
} else {
    // DOM is already ready, initialize immediately
    initTinyMce();
}

// Also expose globally for debugging
window.tinymce = tinymce;
