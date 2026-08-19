import Quill from 'quill';
import 'quill/dist/quill.snow.css';

const initQuill = () => {
    document.querySelectorAll('[data-quill-editor]').forEach(textarea => {
        if (textarea.dataset.quillInitialized === '1') {
            return;
        }

        const quill = new Quill(textarea, {
            theme: 'snow',
            modules: {
                toolbar: [
                    ['bold', 'italic', 'underline', 'strike'],
                    [{ 'list': 'ordered'}, { 'list': 'bullet' }],
                    [{ 'indent': '-1'}, { 'indent': '+1' }],
                    ['link', 'image'],
                    [{ 'header': [1, 2, 3, 4, 5, 6, false] }],
                    [{ 'color': [] }, { 'background': [] }],
                    [{ 'align': [] }],
                    ['clean']
                ]
            },
            placeholder: 'Tulis konten artikel di sini...'
        });

        textarea.dataset.quillInitialized = '1';
        textarea.dataset.quillInstance = quill.id;

        // Sync Quill content to textarea on change
        quill.on('text-change', () => {
            textarea.value = quill.root.innerHTML;
        });

        // Initialize textarea value
        textarea.value = quill.root.innerHTML;
    });
};

// Sync all Quill editors to their textareas before form submit
document.addEventListener('submit', event => {
    if (event.target.matches('form')) {
        document.querySelectorAll('[data-quill-editor]').forEach(textarea => {
            const quillId = textarea.dataset.quillInstance;
            if (quillId && Quill.instances[quillId]) {
                textarea.value = Quill.instances[quillId].root.innerHTML;
            }
        });
    }
});

// Auto-init on DOM content loaded
if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', initQuill);
} else {
    initQuill();
};

window.initQuill = initQuill;
export { initQuill };