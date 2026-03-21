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

yearBoxes.forEach((box) => {
    const header = box.querySelector('.year-header');
    const content = box.querySelector('.year-content');

    if (box.classList.contains('active')) {
        content.style.height = content.scrollHeight + 'px';
        header.setAttribute('aria-expanded', 'true');
    } else {
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
        const fileNameText = panelActions.querySelector('.selected-file-name');

        if (input.files.length > 0) {
            fileNameText.textContent = input.files[0].name;
        } else {
            fileNameText.textContent = 'No file selected';
        }

        const parentContent = input.closest('.year-content');
        if (parentContent) {
            parentContent.style.height = parentContent.scrollHeight + 'px';
        }
    });
});

window.addEventListener('resize', () => {
    document.querySelectorAll('.year-box.active .year-content').forEach((content) => {
        content.style.height = content.scrollHeight + 'px';
    });
});