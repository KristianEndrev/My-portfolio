const projectModalOverlay = document.getElementById('projectModalOverlay');
const projectModalClose = document.getElementById('projectModalClose');
const openProjectModalButtons = document.querySelectorAll('.open-project-modal-btn');
const projectYearSelect = document.getElementById('projectYearSelect');

openProjectModalButtons.forEach((button) => {
    button.addEventListener('click', () => {
        const year = button.getAttribute('data-year');

        if (projectYearSelect && year) {
            projectYearSelect.value = year;
        }

        if (projectModalOverlay) {
            projectModalOverlay.classList.add('active');
        }
    });
});

if (projectModalClose) {
    projectModalClose.addEventListener('click', () => {
        if (projectModalOverlay) {
            projectModalOverlay.classList.remove('active');
        }
    });
}

if (projectModalOverlay) {
    projectModalOverlay.addEventListener('click', (event) => {
        if (event.target === projectModalOverlay) {
            projectModalOverlay.classList.remove('active');
        }
    });
}

document.querySelectorAll('.year-header').forEach((header) => {
    header.addEventListener('click', () => {
        const yearBox = header.closest('.year-box');
        const content = yearBox.querySelector('.year-content');
        const isActive = yearBox.classList.contains('active');

        document.querySelectorAll('.year-box').forEach((box) => {
            box.classList.remove('active');
            const boxContent = box.querySelector('.year-content');
            if (boxContent) {
                boxContent.style.height = '0px';
            }
        });

        if (!isActive && content) {
            yearBox.classList.add('active');
            content.style.height = content.scrollHeight + 'px';
        }
    });
});

const fileSelectButtons = document.querySelectorAll('.file-select-btn');

fileSelectButtons.forEach((button) => {
    button.addEventListener('click', () => {
        const targetId = button.getAttribute('data-target');
        const input = document.getElementById(targetId);

        if (input) {
            input.click();
        }
    });
});

const hiddenFileInputs = document.querySelectorAll('.file-input-hidden');

hiddenFileInputs.forEach((input) => {
    input.addEventListener('change', () => {
        const panelActions = input.closest('.upload-panel-actions');
        if (!panelActions) return;

        const fileNameText = panelActions.querySelector('.selected-file-name');
        if (!fileNameText) return;

        if (input.files.length > 0) {
            fileNameText.textContent = input.files[0].name;
        } else {
            fileNameText.textContent = 'No file selected';
        }
    });
});

const viewerOverlay = document.getElementById('projectFileViewerOverlay');
const viewerCloseBtn = document.getElementById('projectFileViewerClose');
const viewerTitle = document.getElementById('projectFileViewerTitle');
const viewerBody = document.getElementById('projectFileViewerBody');
const openViewerButtons = document.querySelectorAll('.open-project-viewer-btn');

openViewerButtons.forEach((button) => {
    button.addEventListener('click', () => {
        const file = button.getAttribute('data-file');
        const title = button.getAttribute('data-title');
        const extension = button.getAttribute('data-extension');

        if (!viewerOverlay || !viewerTitle || !viewerBody) {
            return;
        }

        viewerTitle.textContent = title;
        viewerBody.innerHTML = '';

        if (['jpg', 'jpeg', 'png'].includes(extension)) {
            viewerBody.innerHTML = `
                <div class="file-viewer-image-wrap">
                    <img src="${file}" alt="${title}" class="file-viewer-image">
                </div>
            `;
        } else if (extension === 'pdf') {
            viewerBody.innerHTML = `
                <iframe src="${file}" class="file-viewer-frame"></iframe>
            `;
        } else if (['doc', 'docx', 'xls', 'xlsx', 'ppt', 'pptx'].includes(extension)) {
            viewerBody.innerHTML = `
                <div class="file-viewer-fallback">
                    <h3>Preview unavailable</h3>
                    <p>Office files cannot be previewed reliably in your current localhost setup, but you can still open or download the file.</p>
                    <div class="file-viewer-actions">
                        <a href="${file}" target="_blank" class="project-action-btn">Open File</a>
                        <a href="${file}" download class="project-action-btn secondary-btn">Download</a>
                    </div>
                </div>
            `;
        } else {
            viewerBody.innerHTML = `
                <div class="file-viewer-fallback">
                    <h3>Preview unavailable</h3>
                    <p>This file type cannot be previewed directly inside the page.</p>
                    <div class="file-viewer-actions">
                        <a href="${file}" target="_blank" class="project-action-btn">Open File</a>
                        <a href="${file}" download class="project-action-btn secondary-btn">Download</a>
                    </div>
                </div>
            `;
        }

        viewerOverlay.classList.add('active');
    });
});

if (viewerCloseBtn) {
    viewerCloseBtn.addEventListener('click', () => {
        if (viewerOverlay) {
            viewerOverlay.classList.remove('active');
        }

        if (viewerBody) {
            viewerBody.innerHTML = '';
        }
    });
}

if (viewerOverlay) {
    viewerOverlay.addEventListener('click', (event) => {
        if (event.target === viewerOverlay) {
            viewerOverlay.classList.remove('active');
            if (viewerBody) {
                viewerBody.innerHTML = '';
            }
        }
    });
}

document.addEventListener('keydown', (event) => {
    if (event.key === 'Escape') {
        if (projectModalOverlay && projectModalOverlay.classList.contains('active')) {
            projectModalOverlay.classList.remove('active');
        }

        if (viewerOverlay && viewerOverlay.classList.contains('active')) {
            viewerOverlay.classList.remove('active');
            if (viewerBody) {
                viewerBody.innerHTML = '';
            }
        }
    }
});