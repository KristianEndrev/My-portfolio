const categoryCards = document.querySelectorAll('.skill-category-card');
const detailsTitle = document.getElementById('detailsTitle');
const detailsSubtitle = document.getElementById('detailsSubtitle');
const detailsSection = document.getElementById('skillsDetails');
const documentsGrid = document.getElementById('skillsDocumentsGrid');
const helperText = document.getElementById('skillsHelperText');
const detailsIcon = document.getElementById('detailsIconHolder');
const movingActiveDot = document.getElementById('movingActiveDot');
const categoriesWrapper = document.getElementById('skillsCategories');

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

const categoryData = {
    minutes: {
        title: 'Minutes of Meeting',
        subtitle: 'Browse and access your documents',
        documents: [
            {
                title: 'Q4 Strategy Planning Meeting',
                date: 'Dec 15, 2025',
                period: 'Period 4',
                description: 'Comprehensive notes from quarterly strategy session covering business objectives, resource allocation, and key performance indicators for the upcoming quarter.'
            },
            {
                title: 'Product Development Sync',
                date: 'Nov 28, 2025',
                period: 'Period 3',
                description: 'Weekly sync meeting notes discussing feature roadmap, technical challenges, sprint planning, and cross-team collaboration initiatives.'
            },
            {
                title: 'Client Feedback Session',
                date: 'Oct 10, 2025',
                period: 'Period 2',
                description: 'Detailed documentation of client feedback meeting including pain points, feature requests, and action items for immediate implementation.'
            },
            {
                title: 'Annual Review Meeting',
                date: 'Jan 20, 2025',
                period: 'Period 1',
                description: 'Annual performance review meeting notes covering achievements, growth areas, and professional development goals for the year.'
            }
        ]
    },
    reflection: {
        title: 'Reflection Reports',
        subtitle: 'Review personal learning and development',
        documents: [
            {
                title: 'Semester Growth Reflection',
                date: 'Dec 18, 2025',
                period: 'Period 4',
                description: 'Reflection on key technical growth, project ownership, and collaboration improvements achieved during the semester.'
            },
            {
                title: 'Teamwork Reflection',
                date: 'Nov 05, 2025',
                period: 'Period 3',
                description: 'A report focused on communication, teamwork challenges, and lessons learned from working in project groups.'
            },
            {
                title: 'Presentation Skills Reflection',
                date: 'Oct 01, 2025',
                period: 'Period 2',
                description: 'Evaluation of presentation confidence, structure, delivery, and audience engagement during class demos.'
            },
            {
                title: 'Professional Behaviour Reflection',
                date: 'Feb 14, 2025',
                period: 'Period 1',
                description: 'Reflection about planning, responsibility, and building stronger professional habits in academic projects.'
            }
        ]
    },
    feedback: {
        title: 'Feedback & Reviews',
        subtitle: 'Read received feedback and evaluation notes',
        documents: [
            {
                title: 'Peer Review Summary',
                date: 'Dec 12, 2025',
                period: 'Period 4',
                description: 'Collected feedback from peers about communication, contribution, and technical execution within the project team.'
            },
            {
                title: 'Teacher Feedback Report',
                date: 'Nov 02, 2025',
                period: 'Period 3',
                description: 'Review notes from mentor sessions highlighting strengths, weaknesses, and suggested areas of development.'
            },
            {
                title: 'Prototype Review Notes',
                date: 'Sep 26, 2025',
                period: 'Period 2',
                description: 'Usability and design feedback based on early prototype testing and stakeholder observations.'
            },
            {
                title: 'Client Evaluation Notes',
                date: 'Jan 16, 2025',
                period: 'Period 1',
                description: 'Summary of evaluation points provided after the first project milestone presentation.'
            }
        ]
    },
    presentations: {
        title: 'Presentations',
        subtitle: 'Open and review your presentation materials',
        documents: [
            {
                title: 'Portfolio Presentation',
                date: 'Dec 21, 2025',
                period: 'Period 4',
                description: 'Presentation slides showing portfolio structure, technical work, and design decisions.'
            },
            {
                title: 'Project Demo Slides',
                date: 'Nov 09, 2025',
                period: 'Period 3',
                description: 'Slides for demonstrating system functionality, development process, and outcomes.'
            },
            {
                title: 'Research Findings Deck',
                date: 'Oct 03, 2025',
                period: 'Period 2',
                description: 'Visual summary of research findings, trends, and recommendations.'
            },
            {
                title: 'Kickoff Presentation',
                date: 'Feb 01, 2025',
                period: 'Period 1',
                description: 'Opening presentation explaining project scope, team roles, and expected deliverables.'
            }
        ]
    },
    training: {
        title: 'Training & Workshops',
        subtitle: 'Access workshop records and training files',
        documents: [
            {
                title: 'Design Thinking Workshop',
                date: 'Dec 04, 2025',
                period: 'Period 4',
                description: 'Workshop materials and notes focused on ideation, problem framing, and iterative design.'
            },
            {
                title: 'Agile Training Notes',
                date: 'Nov 14, 2025',
                period: 'Period 3',
                description: 'Materials covering sprint planning, backlog prioritization, standups, and agile delivery.'
            },
            {
                title: 'Git Collaboration Workshop',
                date: 'Sep 20, 2025',
                period: 'Period 2',
                description: 'Training content about branching strategies, pull requests, and safe collaboration workflows.'
            },
            {
                title: 'Communication Skills Session',
                date: 'Jan 29, 2025',
                period: 'Period 1',
                description: 'Notes and resources from a workshop on professional communication and meeting etiquette.'
            }
        ]
    },
    certifications: {
        title: 'Certifications',
        subtitle: 'Browse earned certificates and achievements',
        documents: [
            {
                title: 'Frontend Development Certificate',
                date: 'Dec 22, 2025',
                period: 'Period 4',
                description: 'Certificate of completion for advanced frontend development and responsive interface design.'
            },
            {
                title: 'Project Management Basics',
                date: 'Nov 11, 2025',
                period: 'Period 3',
                description: 'Achievement document for completing introductory project planning and coordination training.'
            },
            {
                title: 'UI/UX Fundamentals',
                date: 'Oct 06, 2025',
                period: 'Period 2',
                description: 'Certificate covering visual hierarchy, accessibility, wireframing, and usability principles.'
            },
            {
                title: 'Professional Communication',
                date: 'Feb 07, 2025',
                period: 'Period 1',
                description: 'Credential recognizing successful completion of a communication and presentation skills course.'
            }
        ]
    }
};

function createDocumentCard(document, index) {
    return `
        <article class="skill-document-card" style="animation-delay:${0.05 + index * 0.07}s;">
            <h3 class="document-title">${document.title}</h3>

            <div class="document-meta">
                <span class="document-date">${document.date}</span>
                <span class="document-period">${document.period}</span>
            </div>

            <p class="document-description">${document.description}</p>

            <div class="document-actions">
                <a href="#" class="document-view-btn">
                    <svg class="view-svg-icon" viewBox="0 0 24 24" fill="none" aria-hidden="true">
                        <path d="M14 3H8a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h8a2 2 0 0 0 2-2V9z"></path>
                        <path d="M14 3v6h6"></path>
                        <path d="M10 13h4"></path>
                        <path d="M10 17h4"></path>
                    </svg>
                    <span>View</span>
                </a>

                <a href="#" class="document-download-btn" aria-label="Download document">
                    <svg class="download-svg-icon" viewBox="0 0 24 24" fill="none" aria-hidden="true">
                        <path d="M12 4v10"></path>
                        <path d="m8 10 4 4 4-4"></path>
                        <path d="M5 20h14"></path>
                    </svg>
                </a>
            </div>
        </article>
    `;
}

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
        if (direction === 'forward') {
            iconBox.style.transform = 'rotate(360deg)';
        } else {
            iconBox.style.transform = 'rotate(-360deg)';
        }
    });

    setTimeout(() => {
        resetIconBox(iconBox);
    }, 470);
}

function updateCategory(categoryKey) {
    const category = categoryData[categoryKey];
    if (!category) return;

    detailsTitle.textContent = category.title;
    detailsSubtitle.textContent = category.subtitle;
    detailsIcon.innerHTML = icons[categoryKey];

    documentsGrid.innerHTML = category.documents
        .map((document, index) => createDocumentCard(document, index))
        .join('');
}

function openCategory(card) {
    const categoryKey = card.dataset.category;

    categoryCards.forEach((item) => {
        item.classList.remove('active');
    });

    card.classList.add('active');

    updateCategory(categoryKey);
    helperText.classList.add('hidden');
    detailsSection.classList.add('visible');

    requestAnimationFrame(() => {
        moveActiveDot(card);
        rotateIconBox(card, 'forward');
    });
}

function closeCategory(card) {
    card.classList.remove('active');
    detailsSection.classList.remove('visible');
    helperText.classList.remove('hidden');
    movingActiveDot.style.opacity = '0';

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

const defaultActiveCard = document.querySelector('.skill-category-card.active');

if (defaultActiveCard) {
    updateCategory(defaultActiveCard.dataset.category);

    requestAnimationFrame(() => {
        moveActiveDot(defaultActiveCard);
        movingActiveDot.style.opacity = '1';
    });
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
        const fileNameText = panelActions.querySelector('.selected-file-name');

        if (input.files.length > 0) {
            fileNameText.textContent = input.files[0].name;
        } else {
            fileNameText.textContent = 'No file selected';
        }
    });
});