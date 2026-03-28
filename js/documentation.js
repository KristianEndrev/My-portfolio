const navItems = document.querySelectorAll('.documentation-nav-item');
const contentTitle = document.getElementById('documentationContentTitle');
const contentCount = document.getElementById('documentationContentCount');
const cardsContainer = document.getElementById('documentationCards');

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

const fileIcon = `
    <svg class="doc-file-svg" viewBox="0 0 24 24" fill="none" aria-hidden="true">
        <path d="M14 3H8a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h8a2 2 0 0 0 2-2V9z"></path>
        <path d="M14 3v6h6"></path>
        <path d="M10 13h4"></path>
        <path d="M10 17h4"></path>
    </svg>
`;

const eyeIcon = `
    <svg class="doc-action-svg" viewBox="0 0 24 24" fill="none" aria-hidden="true">
        <path d="M2 12s3.5-6 10-6 10 6 10 6-3.5 6-10 6-10-6-10-6z"></path>
        <circle cx="12" cy="12" r="3"></circle>
    </svg>
`;

const downloadIcon = `
    <svg class="doc-action-svg" viewBox="0 0 24 24" fill="none" aria-hidden="true">
        <path d="M12 4v10"></path>
        <path d="m8 10 4 4 4-4"></path>
        <path d="M5 20h14"></path>
    </svg>
`;

const documentationData = {
    "project-plans": {
        title: "Project Plans",
        count: "4 documents available",
        documents: [
            {
                title: "Master Project Plan 2026",
                type: "PDF",
                period: "Period 1",
                description: "Comprehensive master plan outlining project scope, milestones, resource allocation, and strategic objectives.",
                size: "2.4 MB"
            },
            {
                title: "Sprint Planning Document",
                type: "PDF",
                period: "Period 2",
                description: "Detailed sprint planning documentation including user stories, acceptance criteria, and velocity tracking.",
                size: "1.8 MB"
            },
            {
                title: "Resource Allocation Matrix",
                type: "XLSX",
                period: "Period 3",
                description: "Strategic resource allocation framework mapping team members to project tasks with time estimates.",
                size: "956 KB"
            },
            {
                title: "Risk Management Plan",
                type: "PDF",
                period: "Period 4",
                description: "Comprehensive risk assessment and mitigation strategy identifying potential project risks.",
                size: "3.2 MB"
            }
        ]
    },

    "network-diagrams": {
        title: "Network Diagrams",
        count: "4 documents available",
        documents: [
            {
                title: "Infrastructure Topology Overview",
                type: "PDF",
                period: "Period 1",
                description: "High-level network topology showing routers, switches, endpoints, and subnet relationships.",
                size: "1.3 MB"
            },
            {
                title: "Office VLAN Structure",
                type: "PDF",
                period: "Period 2",
                description: "Detailed diagram of VLAN segmentation, addressing strategy, and device grouping.",
                size: "1.1 MB"
            },
            {
                title: "Server Connection Map",
                type: "PNG",
                period: "Period 3",
                description: "Visual server connection layout showing dependencies between core infrastructure components.",
                size: "824 KB"
            },
            {
                title: "Disaster Recovery Network Map",
                type: "PDF",
                period: "Period 4",
                description: "Backup network design used for failover and disaster recovery scenarios.",
                size: "1.9 MB"
            }
        ]
    },

    "functional-design": {
        title: "Functional Design",
        count: "4 documents available",
        documents: [
            {
                title: "User Flow Specification",
                type: "PDF",
                period: "Period 1",
                description: "Functional overview of user journeys, decision points, and navigation paths across the system.",
                size: "1.6 MB"
            },
            {
                title: "Dashboard Interaction Model",
                type: "PDF",
                period: "Period 2",
                description: "Detailed functional design for dashboard components, widgets, and interaction behavior.",
                size: "2.1 MB"
            },
            {
                title: "Authentication Process Design",
                type: "DOCX",
                period: "Period 3",
                description: "Functional breakdown of login, role handling, session control, and access restrictions.",
                size: "688 KB"
            },
            {
                title: "Notification System Logic",
                type: "PDF",
                period: "Period 4",
                description: "Logic specification describing how in-app alerts and notifications behave in key scenarios.",
                size: "1.2 MB"
            }
        ]
    },

    "requirements-analysis": {
        title: "Requirements Analysis",
        count: "4 documents available",
        documents: [
            {
                title: "Stakeholder Requirements Summary",
                type: "PDF",
                period: "Period 1",
                description: "Collected functional and non-functional requirements gathered from project stakeholders.",
                size: "1.4 MB"
            },
            {
                title: "System Requirement Matrix",
                type: "XLSX",
                period: "Period 2",
                description: "Structured matrix mapping business needs to system requirements and priorities.",
                size: "744 KB"
            },
            {
                title: "Use Case Analysis",
                type: "PDF",
                period: "Period 3",
                description: "Breakdown of core use cases with actors, preconditions, triggers, and expected outcomes.",
                size: "1.7 MB"
            },
            {
                title: "Requirement Validation Report",
                type: "PDF",
                period: "Period 4",
                description: "Validation document confirming feasibility, scope alignment, and implementation readiness.",
                size: "1.1 MB"
            }
        ]
    },

    "technical-design": {
        title: "Technical Design",
        count: "4 documents available",
        documents: [
            {
                title: "System Architecture Blueprint",
                type: "PDF",
                period: "Period 1",
                description: "Technical architecture overview covering layers, components, and communication flow.",
                size: "2.2 MB"
            },
            {
                title: "Database Schema Design",
                type: "SQL",
                period: "Period 2",
                description: "Structured relational schema design with entities, relationships, and indexing strategy.",
                size: "512 KB"
            },
            {
                title: "API Integration Specification",
                type: "DOCX",
                period: "Period 3",
                description: "Technical specification for endpoints, payload formats, authentication, and validation rules.",
                size: "936 KB"
            },
            {
                title: "Deployment Environment Setup",
                type: "PDF",
                period: "Period 4",
                description: "Infrastructure setup guide for deployment environments, dependencies, and configuration flow.",
                size: "1.5 MB"
            }
        ]
    },

    "charts-period-plans": {
        title: "Charts & Period Plans",
        count: "4 documents available",
        documents: [
            {
                title: "Academic Period Roadmap",
                type: "PDF",
                period: "Period 1",
                description: "Overview of planned milestones, deliverables, and timelines across the academic period.",
                size: "1.0 MB"
            },
            {
                title: "Progress Tracking Chart",
                type: "XLSX",
                period: "Period 2",
                description: "Live progress tracking document visualizing task completion, blockers, and velocity.",
                size: "689 KB"
            },
            {
                title: "Milestone Burndown Chart",
                type: "PDF",
                period: "Period 3",
                description: "Burndown-style visual tracking of project completion progress against planned milestones.",
                size: "1.3 MB"
            },
            {
                title: "Final Delivery Timeline",
                type: "PDF",
                period: "Period 4",
                description: "Closing period plan showing handover dates, presentation points, and final delivery stages.",
                size: "1.1 MB"
            }
        ]
    }
};

function updateSidebarIcons() {
    navItems.forEach((item) => {
        const iconHolder = item.querySelector('.documentation-nav-icon');
        if (!iconHolder) return;

        iconHolder.innerHTML = item.classList.contains('active')
            ? openFolderIcon
            : closedFolderIcon;
    });
}

function createDocumentationCard(document, index) {
    return `
        <article class="documentation-card" style="animation-delay:${0.04 + index * 0.06}s;">
            <div class="documentation-card-main">
                <div class="documentation-card-title-row">
                    <span class="documentation-card-file-icon">${fileIcon}</span>
                    <h3 class="documentation-card-title">${document.title}</h3>

                    <div class="documentation-card-badges">
                        <span class="documentation-badge">${document.type}</span>
                        <span class="documentation-badge">${document.period}</span>
                    </div>
                </div>

                <p class="documentation-card-description">${document.description}</p>
                <div class="documentation-card-size">File Size: ${document.size}</div>
            </div>

            <div class="documentation-card-actions">
                <a href="#" class="documentation-action-btn view-btn">
                    ${eyeIcon}
                    <span>View</span>
                </a>

                <a href="#" class="documentation-action-btn download-btn">
                    ${downloadIcon}
                    <span>Download</span>
                </a>
            </div>
        </article>
    `;
}

function updateDocumentation(categoryKey) {
    const category = documentationData[categoryKey];
    if (!category) return;

    contentTitle.textContent = category.title;
    contentCount.textContent = category.count;

    cardsContainer.innerHTML = category.documents
        .map((document, index) => createDocumentationCard(document, index))
        .join('');
}

navItems.forEach((item) => {
    item.addEventListener('click', () => {
        navItems.forEach((nav) => nav.classList.remove('active'));
        item.classList.add('active');

        updateSidebarIcons();
        updateDocumentation(item.dataset.category);
    });
});

const defaultActiveItem = document.querySelector('.documentation-nav-item.active');

if (defaultActiveItem) {
    updateSidebarIcons();
    updateDocumentation(defaultActiveItem.dataset.category);
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