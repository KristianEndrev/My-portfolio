<?php
session_start();

if (!isset($_SESSION['username'])) {
    header("Location: log_in.php");
    exit;
}

$isAdmin = isset($_SESSION['role_id']) && $_SESSION['role_id'] == 1;
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="css/projects.css">
    <title>Projects</title>
</head>
<body>
    <h1 class="title">Projects</h1>
    <h3 class="subtitle">Explore my journey through innovation and design</h3>

    <section class="projects">

        <article class="year-box">
            <button class="year-header" type="button" aria-expanded="true">
                <div class="year-info">
                    <span class="year-title">Year 1</span>
                    <span class="year-count">4 Projects</span>
                </div>
                <span class="year-arrow"></span>
            </button>

            <div class="year-content">
                <?php
                if ($isAdmin) {
                    echo "<div class='admin-upload-panel'>
                    <div class='upload-panel-text'>
                        <h4>Add Project File</h4>
                        <p>Select a file and then use your own backend later to save it.</p>
                    </div>

                    <div class='upload-panel-actions'>
                        <input type='file' id='file-upload-year1' class='file-input-hidden'>
                        <button type='button' class='file-select-btn' data-target='file-upload-year1'>Choose File</button>
                        <span class='selected-file-name'>No file selected</span>
                        <button type='button' class='file-add-btn'>Add Project</button>
                    </div>
                </div>";
                }
                ?>
                <div class="project-list">

                    <article class="project-card">
                        <div class="project-card-left">
                            <div class="project-top-row">
                                <h3 class="project-name">Neural Tech Platform</h3>
                                <span class="project-period">Period 1</span>
                            </div>
                            <p class="project-description">
                                Advanced AI-powered platform leveraging cutting-edge neural networks for predictive analytics and machine learning capabilities.
                            </p>
                        </div>

                        <div class="project-card-right">
                            <a href="#" class="project-action-btn">View Project</a>
                            <a href="#" class="project-action-btn secondary-btn">Download</a>
                        </div>
                    </article>

                    <article class="project-card">
                        <div class="project-card-left">
                            <div class="project-top-row">
                                <h3 class="project-name">Digital Experience Suite</h3>
                                <span class="project-period">Period 2</span>
                            </div>
                            <p class="project-description">
                                Comprehensive digital interface system designed for seamless multi-platform experiences with modern responsive design.
                            </p>
                        </div>

                        <div class="project-card-right">
                            <a href="#" class="project-action-btn">View Project</a>
                            <a href="#" class="project-action-btn secondary-btn">Download</a>
                        </div>
                    </article>

                    <article class="project-card">
                        <div class="project-card-left">
                            <div class="project-top-row">
                                <h3 class="project-name">Workspace Revolution</h3>
                                <span class="project-period">Period 3</span>
                            </div>
                            <p class="project-description">
                                Next-generation workspace management system optimizing productivity and collaboration across distributed teams.
                            </p>
                        </div>

                        <div class="project-card-right">
                            <a href="#" class="project-action-btn">View Project</a>
                            <a href="#" class="project-action-btn secondary-btn">Download</a>
                        </div>
                    </article>

                    <article class="project-card">
                        <div class="project-card-left">
                            <div class="project-top-row">
                                <h3 class="project-name">Crypto Ledger Pro</h3>
                                <span class="project-period">Period 4</span>
                            </div>
                            <p class="project-description">
                                Secure blockchain platform with advanced encryption and real-time transaction monitoring for digital assets.
                            </p>
                        </div>

                        <div class="project-card-right">
                            <a href="#" class="project-action-btn">View Project</a>
                            <a href="#" class="project-action-btn secondary-btn">Download</a>
                        </div>
                    </article>

                </div>
            </div>
        </article>

        <article class="year-box">
            <button class="year-header" type="button" aria-expanded="false">
                <div class="year-info">
                    <span class="year-title">Year 2</span>
                    <span class="year-count">4 Projects</span>
                </div>
                <span class="year-arrow"></span>
            </button>

            <div class="year-content">
                <?php
                if ($isAdmin) {
                    echo "<div class='admin-upload-panel'>
                    <div class='upload-panel-text'>
                        <h4>Add Project File</h4>
                        <p>Select a file and then use your own backend later to save it.</p>
                    </div>

                    <div class='upload-panel-actions'>
                        <input type='file' id='file-upload-year2' class='file-input-hidden'>
                        <button type='button' class='file-select-btn' data-target='file-upload-year2'>Choose File</button>
                        <span class='selected-file-name'>No file selected</span>
                        <button type='button' class='file-add-btn'>Add Project</button>
                    </div>
                </div>";
                }
                ?>
                <div class="project-list">
                    <article class="project-card">
                        <div class="project-card-left">
                            <div class="project-top-row">
                                <h3 class="project-name">Project Name</h3>
                                <span class="project-period">Period 1</span>
                            </div>
                            <p class="project-description">Project description goes here.</p>
                        </div>

                        <div class="project-card-right">
                            <a href="#" class="project-action-btn">View Project</a>
                            <a href="#" class="project-action-btn secondary-btn">Download</a>
                        </div>
                    </article>
                </div>
            </div>
        </article>

        <article class="year-box">
            <button class="year-header" type="button" aria-expanded="false">
                <div class="year-info">
                    <span class="year-title">Year 3</span>
                    <span class="year-count">4 Projects</span>
                </div>
                <span class="year-arrow"></span>
            </button>

            <div class="year-content">
                <?php
                if ($isAdmin) {
                    echo "<div class='admin-upload-panel'>
                    <div class='upload-panel-text'>
                        <h4>Add Project File</h4>
                        <p>Select a file and then use your own backend later to save it.</p>
                    </div>

                    <div class='upload-panel-actions'>
                        <input type='file' id='file-upload-year3' class='file-input-hidden'>
                        <button type='button' class='file-select-btn' data-target='file-upload-year3'>Choose File</button>
                        <span class='selected-file-name'>No file selected</span>
                        <button type='button' class='file-add-btn'>Add Project</button>
                    </div>
                </div>";
                }
                ?>
                <div class="project-list">
                    <article class="project-card">
                        <div class="project-card-left">
                            <div class="project-top-row">
                                <h3 class="project-name">Project Name</h3>
                                <span class="project-period">Period 1</span>
                            </div>
                            <p class="project-description">Project description goes here.</p>
                        </div>

                        <div class="project-card-right">
                            <a href="#" class="project-action-btn">View Project</a>
                            <a href="#" class="project-action-btn secondary-btn">Download</a>
                        </div>
                    </article>
                </div>
            </div>
        </article>

        <article class="year-box">
            <button class="year-header" type="button" aria-expanded="false">
                <div class="year-info">
                    <span class="year-title">Year 4</span>
                    <span class="year-count">4 Projects</span>
                </div>
                <span class="year-arrow"></span>
            </button>

            <div class="year-content">
                <?php
                if ($isAdmin) {
                    echo "<div class='admin-upload-panel'>
                    <div class='upload-panel-text'>
                        <h4>Add Project File</h4>
                        <p>Select a file and then use your own backend later to save it.</p>
                    </div>

                    <div class='upload-panel-actions'>
                        <input type='file' id='file-upload-year4' class='file-input-hidden'>
                        <button type='button' class='file-select-btn' data-target='file-upload-year4'>Choose File</button>
                        <span class='selected-file-name'>No file selected</span>
                        <button type='button' class='file-add-btn'>Add Project</button>
                    </div>
                </div>";
                }
                ?>
                <div class="project-list">
                    <article class="project-card">
                        <div class="project-card-left">
                            <div class="project-top-row">
                                <h3 class="project-name">Project Name</h3>
                                <span class="project-period">Period 1</span>
                            </div>
                            <p class="project-description">Project description goes here.</p>
                        </div>

                        <div class="project-card-right">
                            <a href="#" class="project-action-btn">View Project</a>
                            <a href="#" class="project-action-btn secondary-btn">Download</a>
                        </div>
                    </article>
                </div>
            </div>
        </article>

    </section>

    <script src="js/projects.js"></script>
</body>
</html>