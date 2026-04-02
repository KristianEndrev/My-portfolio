<?php
session_start();

if (!isset($_SESSION['username'])) {
    header("Location: log_in.php");
    exit;
}

$isAdmin = isset($_SESSION['role_id']) && $_SESSION['role_id'] == 1;
$msgs = [];

require("includes/db_connect.php");

$allowedExtensions = ['pdf', 'doc', 'docx', 'ppt', 'pptx', 'xls', 'xlsx', 'jpg', 'jpeg', 'png', 'zip'];

if ($isAdmin && $_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST["add_project_submit"])) {
    $title = trim($_POST["title"] ?? '');
    $description = trim($_POST["description"] ?? '');
    $period = trim($_POST["period"] ?? '');
    $yearNumber = (int)($_POST["year_number"] ?? 0);

    if ($title === '' || $description === '' || $period === '' || !in_array($yearNumber, [1, 2, 3, 4], true)) {
        $msgs[] = "Please fill in all fields correctly.";
    } elseif (!isset($_FILES["project_file"]) || $_FILES["project_file"]["error"] !== UPLOAD_ERR_OK) {
        $msgs[] = "Please choose a valid project file.";
    } else {
        $originalFileName = $_FILES["project_file"]["name"];
        $tmpFileName = $_FILES["project_file"]["tmp_name"];
        $fileSize = $_FILES["project_file"]["size"];
        $extension = strtolower(pathinfo($originalFileName, PATHINFO_EXTENSION));

        if (!in_array($extension, $allowedExtensions, true)) {
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
                    $msgs[] = "Database error while saving the project file.";
                }
            } else {
                $msgs[] = "Failed to upload the file.";
            }
        }
    }
}

if ($isAdmin && $_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST["delete_project_file_submit"])) {
    $projectFileId = (int)($_POST["project_file_id"] ?? 0);

    if ($projectFileId <= 0) {
        $msgs[] = "Invalid project selected for deletion.";
    } else {
        try {
            $stmt = $dbHandler->prepare("
                SELECT project_file_id, file_path
                FROM project_files
                WHERE project_file_id = :id
                LIMIT 1
            ");
            $stmt->execute([":id" => $projectFileId]);
            $projectToDelete = $stmt->fetch(PDO::FETCH_ASSOC);

            if (!$projectToDelete) {
                $msgs[] = "Project file not found.";
            } else {
                $filePath = $projectToDelete["file_path"];

                if (!empty($filePath) && str_starts_with($filePath, 'uploads/projects/') && file_exists($filePath)) {
                    unlink($filePath);
                }

                $deleteStmt = $dbHandler->prepare("
                    DELETE FROM project_files
                    WHERE project_file_id = :id
                ");
                $deleteStmt->execute([":id" => $projectFileId]);

                header("Location: projects.php");
                exit;
            }
        } catch (PDOException $e) {
            $msgs[] = "Failed to delete the project file.";
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
            $yearNumber = (int)$project["year_number"];
            if (isset($projectsByYear[$yearNumber])) {
                $projectsByYear[$yearNumber][] = $project;
            }
        }
    } catch (PDOException $e) {
        $msgs[] = "Could not load project files.";
    }
}

function renderProjectCards($projects, $isAdmin) {
    if (count($projects) === 0) {
        echo "
            <div class='projects-empty-state'>
                <h3>No project files yet</h3>
                <p>There are no uploaded project files in this year yet.</p>
            </div>
        ";
        return;
    }

    foreach ($projects as $project) {
        $extension = strtolower(pathinfo($project["stored_file_name"], PATHINFO_EXTENSION));
        $uploadedDate = date('d M Y', strtotime($project["uploaded_at"]));

        echo "
            <article class='project-card'>
                <div class='project-card-left'>
                    <div class='project-top-row'>
                        <h3 class='project-name'>" . htmlspecialchars($project["title"]) . "</h3>
                        <span class='project-period'>" . htmlspecialchars($project["period"]) . "</span>
                    </div>
                    <p class='project-description'>" . htmlspecialchars($project["description"]) . "</p>
                </div>

                <div class='project-card-right'>
                    <button
                        type='button'
                        class='project-action-btn open-project-viewer-btn'
                        data-file='" . htmlspecialchars($project["file_path"]) . "'
                        data-title='" . htmlspecialchars($project["title"]) . "'
                        data-extension='" . htmlspecialchars($extension) . "'
                    >
                        View
                    </button>

                    <a href='" . htmlspecialchars($project["file_path"]) . "' class='project-action-btn secondary-btn' download>
                        Download
                    </a>";

        if ($isAdmin) {
            echo "
                    <form action='' method='POST' class='project-delete-form' onsubmit=\"return confirm('Delete this project file?');\">
                        <input type='hidden' name='project_file_id' value='" . (int)$project["project_file_id"] . "'>
                        <input type='hidden' name='delete_project_file_submit' value='1'>
                        <button type='submit' class='project-delete-btn'>Delete</button>
                    </form>
            ";
        }

        echo "
                </div>
            </article>
        ";
    }
}
?>
<!DOCTYPE html>
<html lang='en'>
<head>
    <meta charset='UTF-8'>
    <meta name='viewport' content='width=device-width, initial-scale=1.0'>
    <title>Projects</title>
    <link rel='stylesheet' href='css/projects.css'>
    <link rel='stylesheet' href='components/header.css'>
</head>
<body>
    <?php include("components/header.php") ?>

    <main>
        <h1 class="title">Projects</h1>
        <p class="subtitle">A showcase of project files and technical progress</p>

        <?php
        if (count($msgs) > 0) {
            foreach ($msgs as $msg) {
                echo "<p class='projects-message'>" . htmlspecialchars($msg) . "</p>";
            }
        }
        ?>

        <section class="projects">
            <?php for ($year = 1; $year <= 4; $year++): ?>
                <div class="year-box">
                    <button type="button" class="year-header">
                        <div class="year-info">
                            <span class="year-title">Year <?= $year ?></span>
                            <span class="year-count"><?= count($projectsByYear[$year]) ?> file<?= count($projectsByYear[$year]) === 1 ? '' : 's' ?></span>
                        </div>
                        <span class="year-arrow"></span>
                    </button>

                    <div class="year-content">
                        <?php if ($isAdmin): ?>
                            <section class='admin-upload-panel'>
                                <div class='upload-panel-text'>
                                    <h4>Add Project File</h4>
                                    <p>Upload a new project file to Year <?= $year ?>.</p>
                                </div>

                                <div class='upload-panel-actions'>
                                    <button
                                        type='button'
                                        class='file-add-btn open-project-modal-btn'
                                        data-year='<?= $year ?>'
                                    >
                                        Add File
                                    </button>
                                </div>
                            </section>
                        <?php endif; ?>

                        <div class="project-list">
                            <?php renderProjectCards($projectsByYear[$year], $isAdmin); ?>
                        </div>
                    </div>
                </div>
            <?php endfor; ?>
        </section>
    </main>

    <?php if ($isAdmin): ?>
        <div class='project-modal-overlay' id='projectModalOverlay'>
            <div class='project-modal'>
                <button type='button' class='project-modal-close' id='projectModalClose'>&times;</button>
                <h2>Add Project File</h2>

                <form action='' method='POST' enctype='multipart/form-data' class='project-modal-form'>
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

                    <select name='year_number' id='projectYearSelect' required>
                        <option value=''>Choose year</option>
                        <option value='1'>Year 1</option>
                        <option value='2'>Year 2</option>
                        <option value='3'>Year 3</option>
                        <option value='4'>Year 4</option>
                    </select>

                    <div class='upload-panel-actions'>
                        <input type='file' name='project_file' id='file-upload-project-modal' class='file-input-hidden' required>
                        <button type='button' class='file-select-btn' data-target='file-upload-project-modal'>Choose File</button>
                        <span class='selected-file-name'>No file selected</span>
                    </div>

                    <button type='submit' class='file-add-btn'>Add File</button>
                </form>
            </div>
        </div>
    <?php endif; ?>

    <div class='file-viewer-overlay' id='projectFileViewerOverlay'>
        <div class='file-viewer-modal'>
            <button type='button' class='file-viewer-close' id='projectFileViewerClose'>&times;</button>

            <div class='file-viewer-header'>
                <h2 class='file-viewer-title' id='projectFileViewerTitle'>Project Viewer</h2>
                <p class='file-viewer-subtitle'>Preview your uploaded file without leaving the page.</p>
            </div>

            <div class='file-viewer-body' id='projectFileViewerBody'></div>
        </div>
    </div>

    <script src='js/projects.js'></script>
</body>
</html>