<?php
session_start();

if (!isset($_SESSION['username'])) {
    header("Location: log_in.php");
    exit;
}

$isAdmin = isset($_SESSION['role_id']) && $_SESSION['role_id'] == 1;
$msgs = [];

require("includes/db_connect.php");

if ($isAdmin && $_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST["add_project_submit"])) {
    $title = trim($_POST["title"] ?? '');
    $description = trim($_POST["description"] ?? '');
    $period = trim($_POST["period"] ?? '');
    $yearNumber = (int)($_POST["year_number"] ?? 0);

    if ($title === '' || $description === '' || $period === '' || $yearNumber < 1 || $yearNumber > 4) {
        $msgs[] = "Please fill in all fields.";
    } elseif (!isset($_FILES["project_file"]) || $_FILES["project_file"]["error"] !== UPLOAD_ERR_OK) {
        $msgs[] = "Please choose a valid file.";
    } else {
        $originalFileName = $_FILES["project_file"]["name"];
        $tmpFileName = $_FILES["project_file"]["tmp_name"];
        $fileSize = $_FILES["project_file"]["size"];
        $extension = strtolower(pathinfo($originalFileName, PATHINFO_EXTENSION));

        $allowedExtensions = ['pdf', 'doc', 'docx', 'ppt', 'pptx', 'xls', 'xlsx', 'jpg', 'jpeg', 'png', 'zip'];

        if (!in_array($extension, $allowedExtensions)) {
            $msgs[] = "This file type is not allowed.";
        } elseif ($fileSize > 10 * 1024 * 1024) {
            $msgs[] = "File is too large. Maximum size is 10MB.";
        } else {
            $uploadFolder = "uploads/projects/";

            if (!is_dir($uploadFolder)) {
                mkdir($uploadFolder, 0777, true);
            }

            $baseName = pathinfo($originalFileName, PATHINFO_FILENAME);
            $baseName = preg_replace('/[^A-Za-z0-9_-]/', '_', $baseName);
            $storedFileName = $baseName . "." . $extension;

            $counter = 1;
            while (file_exists($uploadFolder . $storedFileName)) {
                $storedFileName = $baseName . "_" . $counter . "." . $extension;
                $counter++;
            }           

$filePath = $uploadFolder . $storedFileName;

            if (move_uploaded_file($tmpFileName, $filePath)) {
                try {
                    $stmt = $dbHandler->prepare("
                        INSERT INTO project_files 
                        (title, description, period, year_number, original_file_name, stored_file_name, file_path)
                        VALUES
                        (:title, :description, :period, :year_number, :original_file_name, :stored_file_name, :file_path)
                    ");

                    $stmt->execute([
                        ":title" => $title,
                        ":description" => $description,
                        ":period" => $period,
                        ":year_number" => $yearNumber,
                        ":original_file_name" => $originalFileName,
                        ":stored_file_name" => $storedFileName,
                        ":file_path" => $filePath
                    ]);

                    header("Location: projects.php");
                    exit;
                } catch (PDOException $e) {
                    $msgs[] = "Database error while saving the project.";
                }
            } else {
                $msgs[] = "Failed to upload the file.";
            }
        }
    }
}

$projectsByYear = [
    1 => [],
    2 => [],
    3 => [],
    4 => []
];

if ($dbHandler) {
    try {
        $stmt = $dbHandler->query("SELECT * FROM project_files ORDER BY uploaded_at DESC");
        $projects = $stmt->fetchAll(PDO::FETCH_ASSOC);

        foreach ($projects as $project) {
            $year = (int)$project["year_number"];
            if (isset($projectsByYear[$year])) {
                $projectsByYear[$year][] = $project;
            }
        }
    } catch (PDOException $e) {
        $msgs[] = "Could not load projects.";
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="css/projects.css">
    <link rel="stylesheet" href="components/header.css">
    <title>Projects</title>
</head>
<body>
    <?php include("components/header.php") ?>
<main>
    <h1 class="title">Projects</h1>
    <h3 class="subtitle">Explore my journey through innovation and design</h3>

    <?php
    if (count($msgs) > 0) {
        foreach ($msgs as $msg) {
            echo "<p class='project-message'>" . htmlspecialchars($msg) . "</p>";
        }
    }
    ?>

    <section class="projects">

        <article class="year-box">
            <button class="year-header" type="button" aria-expanded="true">
                <div class="year-info">
                    <span class="year-title">Year 1</span>
                    <span class="year-count"><?= count($projectsByYear[1]) ?> Projects</span>
                </div>
                <span class="year-arrow"></span>
            </button>

            <div class="year-content">
                <?php
                if ($isAdmin) {
                    echo "<div class='admin-upload-panel'>
                    <div class='upload-panel-text'>
                        <h4>Add Project File</h4>
                        <p>Click below to add a new project for Year 1.</p>
                    </div>

                    <div class='upload-panel-actions'>
                        <button type='button' class='file-add-btn open-project-modal-btn' data-year='1'>Add Project</button>
                    </div>
                </div>";
                }
                ?>
                <div class="project-list">

                    <?php foreach ($projectsByYear[1] as $project): ?>
                        <article class="project-card">
                            <div class="project-card-left">
                                <div class="project-top-row">
                                    <h3 class="project-name"><?= htmlspecialchars($project["title"]) ?></h3>
                                    <span class="project-period"><?= htmlspecialchars($project["period"]) ?></span>
                                </div>
                                <p class="project-description"><?= htmlspecialchars($project["description"]) ?></p>
                            </div>

                            <div class="project-card-right">
                                <?php
                                    $extension = strtolower(pathinfo($project["stored_file_name"], PATHINFO_EXTENSION));
                                ?>
                                    <button type="button" class="project-action-btn open-viewer-btn" data-file="<?= htmlspecialchars($project["file_path"]) ?>" data-title="<?= htmlspecialchars($project["title"]) ?>" data-extension="<?= htmlspecialchars($extension) ?>">View Project</button>
                                <a href="<?= htmlspecialchars($project["file_path"]) ?>" download class="project-action-btn secondary-btn">Download</a>
                            </div>
                        </article>
                    <?php endforeach; ?>
                </div>
            </div>
        </article>

        <article class="year-box">
            <button class="year-header" type="button" aria-expanded="false">
                <div class="year-info">
                    <span class="year-title">Year 2</span>
                    <span class="year-count"><?= count($projectsByYear[2]) ?> Projects</span>
                </div>
                <span class="year-arrow"></span>
            </button>

            <div class="year-content">
                <?php
                if ($isAdmin) {
                    echo "<div class='admin-upload-panel'>
                    <div class='upload-panel-text'>
                        <h4>Add Project File</h4>
                        <p>Click below to add a new project for Year 2.</p>
                    </div>

                    <div class='upload-panel-actions'>
                        <button type='button' class='file-add-btn open-project-modal-btn' data-year='2'>Add Project</button>
                    </div>
                </div>";
                }
                ?>
                <div class="project-list">

                    <?php foreach ($projectsByYear[2] as $project): ?>
                        <article class="project-card">
                            <div class="project-card-left">
                                <div class="project-top-row">
                                    <h3 class="project-name"><?= htmlspecialchars($project["title"]) ?></h3>
                                    <span class="project-period"><?= htmlspecialchars($project["period"]) ?></span>
                                </div>
                                <p class="project-description"><?= htmlspecialchars($project["description"]) ?></p>
                            </div>

                            <div class="project-card-right">
                                <?php
                                    $extension = strtolower(pathinfo($project["stored_file_name"], PATHINFO_EXTENSION));
                                ?>
                                    <button type="button" class="project-action-btn open-viewer-btn" data-file="<?= htmlspecialchars($project["file_path"]) ?>" data-title="<?= htmlspecialchars($project["title"]) ?>" data-extension="<?= htmlspecialchars($extension) ?>">View Project</button>
                                <a href="<?= htmlspecialchars($project["file_path"]) ?>" download class="project-action-btn secondary-btn">Download</a>
                            </div>
                        </article>
                    <?php endforeach; ?>
                </div>
            </div>
        </article>

        <article class="year-box">
            <button class="year-header" type="button" aria-expanded="false">
                <div class="year-info">
                    <span class="year-title">Year 3</span>
                    <span class="year-count"><?= count($projectsByYear[3]) ?> Projects</span>
                </div>
                <span class="year-arrow"></span>
            </button>

            <div class="year-content">
                <?php
                if ($isAdmin) {
                    echo "<div class='admin-upload-panel'>
                    <div class='upload-panel-text'>
                        <h4>Add Project File</h4>
                        <p>Click below to add a new project for Year 3.</p>
                    </div>

                    <div class='upload-panel-actions'>
                        <button type='button' class='file-add-btn open-project-modal-btn' data-year='3'>Add Project</button>
                    </div>
                </div>";
                }
                ?>
                <div class="project-list">

                    <?php foreach ($projectsByYear[3] as $project): ?>
                        <article class="project-card">
                            <div class="project-card-left">
                                <div class="project-top-row">
                                    <h3 class="project-name"><?= htmlspecialchars($project["title"]) ?></h3>
                                    <span class="project-period"><?= htmlspecialchars($project["period"]) ?></span>
                                </div>
                                <p class="project-description"><?= htmlspecialchars($project["description"]) ?></p>
                            </div>

                            <div class="project-card-right">
                                <?php
                                    $extension = strtolower(pathinfo($project["stored_file_name"], PATHINFO_EXTENSION));
                                ?>
                                    <button type="button" class="project-action-btn open-viewer-btn" data-file="<?= htmlspecialchars($project["file_path"]) ?>" data-title="<?= htmlspecialchars($project["title"]) ?>" data-extension="<?= htmlspecialchars($extension) ?>">View Project</button>                                
                                    <a href="<?= htmlspecialchars($project["file_path"]) ?>" download class="project-action-btn secondary-btn">Download</a>
                            </div>
                        </article>
                    <?php endforeach; ?>
                </div>
            </div>
        </article>

        <article class="year-box">
            <button class="year-header" type="button" aria-expanded="false">
                <div class="year-info">
                    <span class="year-title">Year 4</span>
                    <span class="year-count"><?= count($projectsByYear[4]) ?> Projects</span>
                </div>
                <span class="year-arrow"></span>
            </button>

            <div class="year-content">
                <?php
                if ($isAdmin) {
                    echo "<div class='admin-upload-panel'>
                    <div class='upload-panel-text'>
                        <h4>Add Project File</h4>
                        <p>Click below to add a new project for Year 4.</p>
                    </div>

                    <div class='upload-panel-actions'>
                        <button type='button' class='file-add-btn open-project-modal-btn' data-year='4'>Add Project</button>
                    </div>
                </div>";
                }
                ?>
                <div class="project-list">

                    <?php foreach ($projectsByYear[4] as $project): ?>
                        <article class="project-card">
                            <div class="project-card-left">
                                <div class="project-top-row">
                                    <h3 class="project-name"><?= htmlspecialchars($project["title"]) ?></h3>
                                    <span class="project-period"><?= htmlspecialchars($project["period"]) ?></span>
                                </div>
                                <p class="project-description"><?= htmlspecialchars($project["description"]) ?></p>
                            </div>

                            <div class="project-card-right">
                                <?php
                                    $extension = strtolower(pathinfo($project["stored_file_name"], PATHINFO_EXTENSION));
                                ?>
                                    <button type="button" class="project-action-btn open-viewer-btn" data-file="<?= htmlspecialchars($project["file_path"]) ?>" data-title="<?= htmlspecialchars($project["title"]) ?>" data-extension="<?= htmlspecialchars($extension) ?>">View Project</button>                                <a href="<?= htmlspecialchars($project["file_path"]) ?>" download class="project-action-btn secondary-btn">Download</a>
                            </div>
                        </article>
                    <?php endforeach; ?>
                </div>
            </div>
        </article>

    </section>
</main>

<?php
if ($isAdmin) {
    echo "<div class='project-modal-overlay' id='projectModalOverlay'>
        <div class='project-modal'>
            <button type='button' class='project-modal-close' id='projectModalClose'>&times;</button>
            <h2>Add Project</h2>

            <form action='' method='POST' enctype='multipart/form-data' class='project-modal-form'>
                <input type='hidden' name='year_number' id='modalYearNumber'>
                <input type='hidden' name='add_project_submit' value='1'>

                <input type='text' name='title' placeholder='Project title' required>

                <textarea name='description' placeholder='Project description' required></textarea>

                <select name='period' required>
                    <option value=''>Choose period</option>
                    <option value='Period 1'>Period 1</option>
                    <option value='Period 2'>Period 2</option>
                    <option value='Period 3'>Period 3</option>
                    <option value='Period 4'>Period 4</option>
                </select>

                <div class='upload-panel-actions'>
                    <input type='file' name='project_file' id='file-upload-project-modal' class='file-input-hidden' required>
                    <button type='button' class='file-select-btn' data-target='file-upload-project-modal'>Choose File</button>
                    <span class='selected-file-name'>No file selected</span>
                </div>

                <button type='submit' class='file-add-btn'>Add Project</button>
            </form>
        </div>
    </div>";
}
?>

<div class="file-viewer-overlay" id="fileViewerOverlay">
    <div class="file-viewer-modal">
        <button type="button" class="file-viewer-close" id="fileViewerClose">&times;</button>

        <div class="file-viewer-header">
            <h2 class="file-viewer-title" id="fileViewerTitle">Project Viewer</h2>
            <p class="file-viewer-subtitle">Preview your uploaded project file without leaving the page.</p>
        </div>

        <div class="file-viewer-body" id="fileViewerBody"></div>
    </div>
</div>

<script src="js/projects.js"></script>
</body>
</html>