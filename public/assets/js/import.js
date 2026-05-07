const importDropzone = document.querySelector('[data-import-dropzone]');
const importFileInput = document.querySelector('[data-import-file-input]');

if (importDropzone && importFileInput) {
    importDropzone.addEventListener('click', () => {
        importFileInput.click();
    });

    importFileInput.addEventListener('change', () => {
        if (importFileInput.form) {
            importFileInput.form.submit();
        }
    });
}
