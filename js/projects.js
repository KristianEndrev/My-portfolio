const yearBoxes = document.querySelectorAll('.year-box');

function closeAllYearBoxes() {
    yearBoxes.forEach((box) => {
        const header = box.querySelector('.year-header');
        const content = box.querySelector('.year-content');

        box.classList.remove('active');
        header.setAttribute('aria-expanded', 'false');
        content.style.height = '0px';
    });
}

function openYearBox(box) {
    const header = box.querySelector('.year-header');
    const content = box.querySelector('.year-content');

    box.classList.add('active');
    header.setAttribute('aria-expanded', 'true');
    content.style.height = content.scrollHeight + 'px';
}

yearBoxes.forEach((box, index) => {
    const header = box.querySelector('.year-header');
    const content = box.querySelector('.year-content');

    if (index === 0) {
        box.classList.add('active');
        content.style.height = content.scrollHeight + 'px';
        header.setAttribute('aria-expanded', 'true');
    } else {
        box.classList.remove('active');
        content.style.height = '0px';
        header.setAttribute('aria-expanded', 'false');
    }

    header.addEventListener('click', () => {
        const isActive = box.classList.contains('active');

        closeAllYearBoxes();

        if (!isActive) {
            openYearBox(box);
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

        if (!panelActions) {
            return;
        }

        const fileNameText = panelActions.querySelector('.selected-file-name');

        if (!fileNameText) {
            return;
        }

        if (input.files.length > 0) {
            fileNameText.textContent = input.files[0].name;
        } else {
            fileNameText.textContent = 'No file selected';
        }
    });
});

const modalOverlay = document.getElementById('projectModalOverlay');
const modalCloseBtn = document.getElementById('projectModalClose');
const modalYearInput = document.getElementById('modalYearNumber');
const openModalButtons = document.querySelectorAll('.open-project-modal-btn');

openModalButtons.forEach((button) => {
    button.addEventListener('click', () => {
        const year = button.getAttribute('data-year');

        if (modalYearInput) {
            modalYearInput.value = year;
        }

        if (modalOverlay) {
            modalOverlay.classList.add('active');
        }
    });
});

if (modalCloseBtn) {
    modalCloseBtn.addEventListener('click', () => {
        if (modalOverlay) {
            modalOverlay.classList.remove('active');
        }
    });
}

if (modalOverlay) {
    modalOverlay.addEventListener('click', (event) => {
        if (event.target === modalOverlay) {
            modalOverlay.classList.remove('active');
        }
    });
}

document.addEventListener('keydown', (event) => {
    if (event.key === 'Escape' && modalOverlay && modalOverlay.classList.contains('active')) {
        modalOverlay.classList.remove('active');
    }
});

window.addEventListener('resize', () => {
    document.querySelectorAll('.year-box.active .year-content').forEach((content) => {
        content.style.height = content.scrollHeight + 'px';
    });
});

const viewerOverlay = document.getElementById('fileViewerOverlay');
const viewerCloseBtn = document.getElementById('fileViewerClose');
const viewerTitle = document.getElementById('fileViewerTitle');
const viewerBody = document.getElementById('fileViewerBody');
const openViewerButtons = document.querySelectorAll('.open-viewer-btn');

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
            viewerBody.innerHTML = '';
        }
    });
}

document.addEventListener('keydown', (event) => {
    if (event.key === 'Escape' && viewerOverlay && viewerOverlay.classList.contains('active')) {
        viewerOverlay.classList.remove('active');
        viewerBody.innerHTML = '';
    }
});