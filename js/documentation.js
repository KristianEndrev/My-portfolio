const navItems = document.querySelectorAll('.documentation-nav-item');
const contentTitle = document.getElementById('documentationContentTitle');
const contentCount = document.getElementById('documentationContentCount');
const categorySections = document.querySelectorAll('.documentation-category-section');

const closedFolderIcon = `
    <svg class="doc-nav-svg" viewBox="0 0 24 24" fill="none" aria-hidden="true">
        <path d="M3 7a2 2 0 0 1 2-2h4l2 2h8a2 2 0 0 1 2 2v8a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"></path>
    </svg>
`;

const openFolderIcon = `
    <svg class="doc-nav-svg" viewBox="0 0 24 24" fill="none" aria-hidden="true">
        <path d="M3 8a2 2 0 0 1 2-2h4l2 2h8a2 2 0 0 1 1.94 2.48l-1.2 4.8A2 2 0 0 1 18.8 19H5.2a2 2 0 0 1-1.94-2.48L4.2 10.7A2 2 0 0 1 6.14 9H21"></path>
    </svg>
`;

function updateSidebarIcons() {
    navItems.forEach((item) => {
        const iconHolder = item.querySelector('.documentation-nav-icon');
        if (!iconHolder) return;

        iconHolder.innerHTML = item.classList.contains('active')
            ? openFolderIcon
            : closedFolderIcon;
    });
}

function hideAllCategorySections() {
    categorySections.forEach((section) => {
        section.classList.remove('active');
    });
}

function updateDocumentation(categoryKey, titleText, countText) {
    const targetSection = document.getElementById(`category-${categoryKey}`);
    if (!targetSection) return;

    contentTitle.textContent = titleText;
    contentCount.textContent = countText;

    hideAllCategorySections();
    targetSection.classList.add('active');
}

navItems.forEach((item) => {
    item.addEventListener('click', () => {
        navItems.forEach((nav) => nav.classList.remove('active'));
        item.classList.add('active');

        const countElement = item.querySelector('.documentation-nav-count');
        const countNumber = countElement ? countElement.textContent.replace('files', '').trim() : '0';

        updateSidebarIcons();
        updateDocumentation(
            item.dataset.category,
            item.dataset.title,
            `${countNumber} documents available`
        );
    });
});

const defaultActiveItem = document.querySelector('.documentation-nav-item.active');

if (defaultActiveItem) {
    const countElement = defaultActiveItem.querySelector('.documentation-nav-count');
    const countNumber = countElement ? countElement.textContent.replace('files', '').trim() : '0';

    updateSidebarIcons();
    updateDocumentation(
        defaultActiveItem.dataset.category,
        defaultActiveItem.dataset.title,
        `${countNumber} documents available`
    );
}

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

const documentationModalOverlay = document.getElementById('documentationModalOverlay');
const documentationModalClose = document.getElementById('documentationModalClose');
const openDocumentationModalBtn = document.querySelector('.open-documentation-modal-btn');

if (openDocumentationModalBtn) {
    openDocumentationModalBtn.addEventListener('click', () => {
        const activeNav = document.querySelector('.documentation-nav-item.active');

        if (!activeNav) {
            alert('Please select a category first.');
            return;
        }

        const modalCategoryInput = document.getElementById('modalDocumentationCategory');
        if (modalCategoryInput) {
            modalCategoryInput.value = activeNav.dataset.category;
        }

        if (documentationModalOverlay) {
            documentationModalOverlay.classList.add('active');
        }
    });
}

if (documentationModalClose) {
    documentationModalClose.addEventListener('click', () => {
        if (documentationModalOverlay) {
            documentationModalOverlay.classList.remove('active');
        }
    });
}

if (documentationModalOverlay) {
    documentationModalOverlay.addEventListener('click', (event) => {
        if (event.target === documentationModalOverlay) {
            documentationModalOverlay.classList.remove('active');
        }
    });
}

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
                        <a href="${file}" target="_blank" class="documentation-action-btn view-btn">Open File</a>
                        <a href="${file}" download class="documentation-action-btn download-btn">Download</a>
                    </div>
                </div>
            `;
        } else {
            viewerBody.innerHTML = `
                <div class="file-viewer-fallback">
                    <h3>Preview unavailable</h3>
                    <p>This file type cannot be previewed directly inside the page.</p>
                    <div class="file-viewer-actions">
                        <a href="${file}" target="_blank" class="documentation-action-btn view-btn">Open File</a>
                        <a href="${file}" download class="documentation-action-btn download-btn">Download</a>
                    </div>
                </div>
            `;
        }

        viewerOverlay.classList.add('active');
    });
});

if (viewerCloseBtn) {
    viewerCloseBtn.addEventListener('click', () => {
        viewerOverlay.classList.remove('active');
        viewerBody.innerHTML = '';
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
    if (event.key === 'Escape') {
        if (documentationModalOverlay && documentationModalOverlay.classList.contains('active')) {
            documentationModalOverlay.classList.remove('active');
        }

        if (viewerOverlay && viewerOverlay.classList.contains('active')) {
            viewerOverlay.classList.remove('active');
            viewerBody.innerHTML = '';
        }
    }
});