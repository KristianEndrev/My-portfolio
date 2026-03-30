const categoryCards = document.querySelectorAll('.skill-category-card');
const detailsTitle = document.getElementById('detailsTitle');
const detailsSubtitle = document.getElementById('detailsSubtitle');
const detailsSection = document.getElementById('skillsDetails');
const helperText = document.getElementById('skillsHelperText');
const detailsIcon = document.getElementById('detailsIconHolder');
const movingActiveDot = document.getElementById('movingActiveDot');
const categoriesWrapper = document.getElementById('skillsCategories');
const categoryContentSections = document.querySelectorAll('.skills-category-content');

const icons = {
    minutes: `
        <svg class="skill-svg-icon details-svg-icon" viewBox="0 0 24 24" fill="none" aria-hidden="true">
            <path d="M9 5H7a2 2 0 0 0-2 2v10a2 2 0 0 0 2 2h10a2 2 0 0 0 2-2V7a2 2 0 0 0-2-2h-2"></path>
            <path d="M9 3h6v4H9z"></path>
            <path d="M9 12h6"></path>
            <path d="M9 16h4"></path>
        </svg>
    `,
    reflection: `
        <svg class="skill-svg-icon details-svg-icon" viewBox="0 0 24 24" fill="none" aria-hidden="true">
            <path d="M4 6.5A2.5 2.5 0 0 1 6.5 4H12v16H6.5A2.5 2.5 0 0 0 4 22z"></path>
            <path d="M20 6.5A2.5 2.5 0 0 0 17.5 4H12v16h5.5A2.5 2.5 0 0 1 20 22z"></path>
        </svg>
    `,
    feedback: `
        <svg class="skill-svg-icon details-svg-icon" viewBox="0 0 24 24" fill="none" aria-hidden="true">
            <path d="M21 15a2 2 0 0 1-2 2H8l-5 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"></path>
        </svg>
    `,
    presentations: `
        <svg class="skill-svg-icon details-svg-icon" viewBox="0 0 24 24" fill="none" aria-hidden="true">
            <path d="M8 6v12"></path>
            <path d="M12 10v8"></path>
            <path d="M16 4v14"></path>
        </svg>
    `,
    training: `
        <svg class="skill-svg-icon details-svg-icon" viewBox="0 0 24 24" fill="none" aria-hidden="true">
            <path d="M12 3 4 7l8 4 8-4-8-4z"></path>
            <path d="M4 12l8 4 8-4"></path>
            <path d="M4 17l8 4 8-4"></path>
        </svg>
    `,
    certifications: `
        <svg class="skill-svg-icon details-svg-icon" viewBox="0 0 24 24" fill="none" aria-hidden="true">
            <circle cx="12" cy="8" r="4"></circle>
            <path d="M10 12.5 8 21l4-2 4 2-2-8.5"></path>
        </svg>
    `
};

function moveActiveDot(targetCard) {
    if (!targetCard || !movingActiveDot || !categoriesWrapper) return;

    const wrapperRect = categoriesWrapper.getBoundingClientRect();
    const cardRect = targetCard.getBoundingClientRect();

    const x = cardRect.left - wrapperRect.left + cardRect.width - 32;
    const y = cardRect.top - wrapperRect.top + 16;

    movingActiveDot.style.opacity = '1';
    movingActiveDot.style.transform = `translate(${x}px, ${y}px)`;
}

function resetIconBox(iconBox) {
    iconBox.style.transition = 'none';
    iconBox.style.transform = 'rotate(0deg)';
    iconBox.offsetHeight;
    iconBox.style.transition = 'transform 0.46s cubic-bezier(0.22, 0.61, 0.36, 1)';
}

function rotateIconBox(card, direction = 'forward') {
    const iconBox = card.querySelector('.skill-icon-box');
    if (!iconBox) return;

    resetIconBox(iconBox);

    requestAnimationFrame(() => {
        iconBox.style.transform = direction === 'forward' ? 'rotate(360deg)' : 'rotate(-360deg)';
    });

    setTimeout(() => {
        resetIconBox(iconBox);
    }, 470);
}

function hideAllCategoryContent() {
    categoryContentSections.forEach((section) => {
        section.classList.remove('active');
    });
}

function openCategory(card) {
    const categoryKey = card.dataset.category;
    const categoryTitle = card.dataset.title;
    const categorySubtitle = card.dataset.subtitle;
    const targetSection = document.getElementById(`category-${categoryKey}`);

    categoryCards.forEach((item) => {
        item.classList.remove('active');
    });

    hideAllCategoryContent();

    card.classList.add('active');

    if (targetSection) {
        targetSection.classList.add('active');
    }

    detailsTitle.textContent = categoryTitle;
    detailsSubtitle.textContent = categorySubtitle;
    detailsIcon.innerHTML = icons[categoryKey];

    helperText.classList.add('hidden');
    detailsSection.classList.add('visible');

    requestAnimationFrame(() => {
        moveActiveDot(card);
        rotateIconBox(card, 'forward');
    });

    const modalCategoryInput = document.getElementById('modalSkillCategory');
    if (modalCategoryInput) {
        modalCategoryInput.value = categoryKey;
    }
}

function closeCategory(card) {
    card.classList.remove('active');
    detailsSection.classList.remove('visible');
    helperText.classList.remove('hidden');
    movingActiveDot.style.opacity = '0';
    hideAllCategoryContent();

    rotateIconBox(card, 'backward');
}

categoryCards.forEach((card) => {
    card.addEventListener('click', () => {
        const isActive = card.classList.contains('active');

        if (isActive) {
            closeCategory(card);
        } else {
            openCategory(card);
        }
    });
});

window.addEventListener('resize', () => {
    const activeCard = document.querySelector('.skill-category-card.active');
    if (activeCard) {
        moveActiveDot(activeCard);
    }
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

const skillModalOverlay = document.getElementById('skillModalOverlay');
const skillModalClose = document.getElementById('skillModalClose');
const openSkillModalBtn = document.querySelector('.open-skill-modal-btn');

if (openSkillModalBtn) {
    openSkillModalBtn.addEventListener('click', () => {
        const activeCard = document.querySelector('.skill-category-card.active');

        if (!activeCard) {
            alert('Please select a category first.');
            return;
        }

        const modalCategoryInput = document.getElementById('modalSkillCategory');
        if (modalCategoryInput) {
            modalCategoryInput.value = activeCard.dataset.category;
        }

        if (skillModalOverlay) {
            skillModalOverlay.classList.add('active');
        }
    });
}

if (skillModalClose) {
    skillModalClose.addEventListener('click', () => {
        if (skillModalOverlay) {
            skillModalOverlay.classList.remove('active');
        }
    });
}

if (skillModalOverlay) {
    skillModalOverlay.addEventListener('click', (event) => {
        if (event.target === skillModalOverlay) {
            skillModalOverlay.classList.remove('active');
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
                        <a href="${file}" target="_blank" class="document-view-btn">Open File</a>
                        <a href="${file}" download class="document-download-btn">Download</a>
                    </div>
                </div>
            `;
        } else {
            viewerBody.innerHTML = `
                <div class="file-viewer-fallback">
                    <h3>Preview unavailable</h3>
                    <p>This file type cannot be previewed directly inside the page.</p>
                    <div class="file-viewer-actions">
                        <a href="${file}" target="_blank" class="document-view-btn">Open File</a>
                        <a href="${file}" download class="document-download-btn">Download</a>
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
        if (skillModalOverlay && skillModalOverlay.classList.contains('active')) {
            skillModalOverlay.classList.remove('active');
        }

        if (viewerOverlay && viewerOverlay.classList.contains('active')) {
            viewerOverlay.classList.remove('active');
            viewerBody.innerHTML = '';
        }
    }
});