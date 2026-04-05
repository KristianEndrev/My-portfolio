<?php
session_start();

if (!isset($_SESSION['username'])) {
    header("Location: log_in.php");
    exit;
}

$isAdmin = isset($_SESSION['role_id']) && $_SESSION['role_id'] == 1;
$msgs = [];

require("includes/db_connect.php");

$allowedCategories = [
    'project-plans',
    'network-diagrams',
    'functional-design',
    'requirements-analysis',
    'technical-design',
    'charts-period-plans'
];

$allowedExtensions = [
    'pdf', 'doc', 'docx', 'ppt', 'pptx',
    'xls', 'xlsx', 'jpg', 'jpeg', 'png',
    'zip', 'txt', 'md', 'ino', 'c', 'cpp', 'h', 'fig'
];

if ($isAdmin && $_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST["add_documentation_file_submit"])) {
    $category = trim($_POST["category"] ?? '');
    $title = trim($_POST["title"] ?? '');
    $description = trim($_POST["description"] ?? '');
    $period = trim($_POST["period"] ?? '');

    if (!in_array($category, $allowedCategories, true) || $title === '' || $description === '' || $period === '') {
        $msgs[] = "Please fill in all fields correctly.";
    } elseif (!isset($_FILES["documentation_file"]) || $_FILES["documentation_file"]["error"] !== UPLOAD_ERR_OK) {
        $msgs[] = "Please choose a valid file.";
    } else {
        $originalFileName = $_FILES["documentation_file"]["name"];
        $tmpFileName = $_FILES["documentation_file"]["tmp_name"];
        $fileSize = (int) $_FILES["documentation_file"]["size"];
        $extension = strtolower(pathinfo($originalFileName, PATHINFO_EXTENSION));

        if (!in_array($extension, $allowedExtensions, true)) {
            $msgs[] = "This file type is not allowed.";
        } elseif ($fileSize > 10 * 1024 * 1024) {
            $msgs[] = "File is too large. Maximum size is 10MB.";
        } else {
            $uploadFolder = "uploads/documentation/";

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
                        INSERT INTO documentation_files
                        (category, title, description, period, original_file_name, stored_file_name, file_path, file_size)
                        VALUES
                        (:category, :title, :description, :period, :original_file_name, :stored_file_name, :file_path, :file_size)
                    ");

                    $stmt->execute([
                        ":category" => $category,
                        ":title" => $title,
                        ":description" => $description,
                        ":period" => $period,
                        ":original_file_name" => $originalFileName,
                        ":stored_file_name" => $storedFileName,
                        ":file_path" => $filePath,
                        ":file_size" => $fileSize
                    ]);

                    header("Location: documentation.php");
                    exit;
                } catch (PDOException $e) {
                    if (file_exists($filePath)) {
                        unlink($filePath);
                    }
                    $msgs[] = "Database error while saving the file.";
                }
            } else {
                $msgs[] = "Failed to upload the file.";
            }
        }
    }
}

if ($isAdmin && $_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST["delete_documentation_file_submit"])) {
    $fileId = (int)($_POST["documentation_file_id"] ?? 0);

    if ($fileId <= 0) {
        $msgs[] = "Invalid file selected for deletion.";
    } else {
        try {
            $stmt = $dbHandler->prepare("
                SELECT id, file_path
                FROM documentation_files
                WHERE id = :id
                LIMIT 1
            ");
            $stmt->execute([":id" => $fileId]);
            $fileToDelete = $stmt->fetch(PDO::FETCH_ASSOC);

            if (!$fileToDelete) {
                $msgs[] = "File not found.";
            } else {
                $filePath = $fileToDelete["file_path"];

                if (!empty($filePath) && str_starts_with($filePath, 'uploads/documentation/') && file_exists($filePath)) {
                    unlink($filePath);
                }

                $deleteStmt = $dbHandler->prepare("
                    DELETE FROM documentation_files
                    WHERE id = :id
                ");
                $deleteStmt->execute([":id" => $fileId]);

                header("Location: documentation.php");
                exit;
            }
        } catch (PDOException $e) {
            $msgs[] = "Failed to delete the file.";
        }
    }
}

$documentationByCategory = [
    'project-plans' => [],
    'network-diagrams' => [],
    'functional-design' => [],
    'requirements-analysis' => [],
    'technical-design' => [],
    'charts-period-plans' => []
];

if ($dbHandler) {
    try {
        $stmt = $dbHandler->query("SELECT * FROM documentation_files ORDER BY uploaded_at DESC");
        $documents = $stmt->fetchAll(PDO::FETCH_ASSOC);

        foreach ($documents as $document) {
            $category = $document["category"];
            if (isset($documentationByCategory[$category])) {
                $documentationByCategory[$category][] = $document;
            }
        }
    } catch (PDOException $e) {
        $msgs[] = "Could not load documentation files.";
    }
}

function formatFileSize($bytes) {
    if ($bytes >= 1024 * 1024) {
        return number_format($bytes / (1024 * 1024), 1) . " MB";
    }

    return number_format($bytes / 1024, 0) . " KB";
}

function renderDocumentationCards($documents, $isAdmin) {
    if (count($documents) === 0) {
        echo "
            <div class='documentation-empty-state'>
                <h3>No files yet</h3>
                <p>There are no uploaded files in this category yet.</p>
            </div>
        ";
        return;
    }

    foreach ($documents as $index => $document) {
        $extension = strtolower(pathinfo($document["stored_file_name"], PATHINFO_EXTENSION));
        $typeLabel = strtoupper($extension);
        $fileSizeLabel = formatFileSize((int)$document["file_size"]);

        echo "
            <article class='documentation-card' style='animation-delay:" . (0.04 + $index * 0.06) . "s;'>
                <div class='documentation-card-main'>
                    <div class='documentation-card-title-row'>
                        <span class='documentation-card-file-icon'>
                            <svg class='doc-file-svg' viewBox='0 0 24 24' fill='none' aria-hidden='true'>
                                <path d='M14 3H8a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h8a2 2 0 0 0 2-2V9z'></path>
                                <path d='M14 3v6h6'></path>
                                <path d='M10 13h4'></path>
                                <path d='M10 17h4'></path>
                            </svg>
                        </span>

                        <h3 class='documentation-card-title'>" . htmlspecialchars($document["title"]) . "</h3>

                        <div class='documentation-card-badges'>
                            <span class='documentation-badge'>" . htmlspecialchars($typeLabel) . "</span>
                            <span class='documentation-badge'>" . htmlspecialchars($document["period"]) . "</span>
                        </div>
                    </div>

                    <p class='documentation-card-description'>" . htmlspecialchars($document["description"]) . "</p>
                    <div class='documentation-card-size'>File Size: " . htmlspecialchars($fileSizeLabel) . "</div>
                </div>

                <div class='documentation-card-actions'>
                    <button
                        type='button'
                        class='documentation-action-btn view-btn open-viewer-btn'
                        data-file='" . htmlspecialchars($document["file_path"]) . "'
                        data-title='" . htmlspecialchars($document["title"]) . "'
                        data-extension='" . htmlspecialchars($extension) . "'
                    >
                        <svg class='doc-action-svg' viewBox='0 0 24 24' fill='none' aria-hidden='true'>
                            <path d='M2 12s3.5-6 10-6 10 6 10 6-3.5 6-10 6-10-6-10-6z'></path>
                            <circle cx='12' cy='12' r='3'></circle>
                        </svg>
                        <span>View</span>
                    </button>

                    <a href='" . htmlspecialchars($document["file_path"]) . "' class='documentation-action-btn download-btn' download>
                        <svg class='doc-action-svg' viewBox='0 0 24 24' fill='none' aria-hidden='true'>
                            <path d='M12 4v10'></path>
                            <path d='m8 10 4 4 4-4'></path>
                            <path d='M5 20h14'></path>
                        </svg>
                        <span>Download</span>
                    </a>";

        if ($isAdmin) {
            echo "
                    <form action='' method='POST' class='documentation-delete-form' onsubmit=\"return confirm('Delete this file?');\">
                        <input type='hidden' name='documentation_file_id' value='" . (int)$document["id"] . "'>
                        <input type='hidden' name='delete_documentation_file_submit' value='1'>
                        <button type='submit' class='documentation-action-btn delete-btn'>Delete</button>
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
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Documentation</title>
    <link rel="stylesheet" href="css/documentation.css">
    <link rel="stylesheet" href="components/header.css">
</head>
<body>
    <?php include 'components/header.php' ?>
    <main class="documentation-page">
        <section class="documentation-layout">
            <aside class="documentation-sidebar">
                <div class="documentation-sidebar-top">
                    <h1 class="documentation-title">Documentation</h1>
                    <p class="documentation-subtitle">Project files & resources</p>
                </div>

                <div class="documentation-nav" id="documentationNav">
                    <button class="documentation-nav-item active" type="button" data-category="project-plans" data-title="Project Plans">
                        <span class="documentation-nav-icon"></span>
                        <span class="documentation-nav-text">
                            <span class="documentation-nav-name">Project Plans</span>
                            <span class="documentation-nav-count"><?= count($documentationByCategory['project-plans']) ?> files</span>
                        </span>
                    </button>

                    <button class="documentation-nav-item" type="button" data-category="network-diagrams" data-title="Network Diagrams">
                        <span class="documentation-nav-icon"></span>
                        <span class="documentation-nav-text">
                            <span class="documentation-nav-name">Network Diagrams</span>
                            <span class="documentation-nav-count"><?= count($documentationByCategory['network-diagrams']) ?> files</span>
                        </span>
                    </button>

                    <button class="documentation-nav-item" type="button" data-category="functional-design" data-title="Functional Design">
                        <span class="documentation-nav-icon"></span>
                        <span class="documentation-nav-text">
                            <span class="documentation-nav-name">Functional Design</span>
                            <span class="documentation-nav-count"><?= count($documentationByCategory['functional-design']) ?> files</span>
                        </span>
                    </button>

                    <button class="documentation-nav-item" type="button" data-category="requirements-analysis" data-title="Requirements Analysis">
                        <span class="documentation-nav-icon"></span>
                        <span class="documentation-nav-text">
                            <span class="documentation-nav-name">Requirements Analysis</span>
                            <span class="documentation-nav-count"><?= count($documentationByCategory['requirements-analysis']) ?> files</span>
                        </span>
                    </button>

                    <button class="documentation-nav-item" type="button" data-category="technical-design" data-title="Technical Design">
                        <span class="documentation-nav-icon"></span>
                        <span class="documentation-nav-text">
                            <span class="documentation-nav-name">Technical Design</span>
                            <span class="documentation-nav-count"><?= count($documentationByCategory['technical-design']) ?> files</span>
                        </span>
                    </button>

                    <button class="documentation-nav-item" type="button" data-category="charts-period-plans" data-title="Charts & Period Plans">
                        <span class="documentation-nav-icon"></span>
                        <span class="documentation-nav-text">
                            <span class="documentation-nav-name">Charts & Period Plans</span>
                            <span class="documentation-nav-count"><?= count($documentationByCategory['charts-period-plans']) ?> files</span>
                        </span>
                    </button>
                </div>
            </aside>

            <section class="documentation-content">
                <div class="documentation-content-header">
                    <h2 class="documentation-content-title" id="documentationContentTitle">Project Plans</h2>
                    <p class="documentation-content-count" id="documentationContentCount"><?= count($documentationByCategory['project-plans']) ?> documents available</p>
                </div>

                <?php
                if (count($msgs) > 0) {
                    foreach ($msgs as $msg) {
                        echo "<p class='documentation-message'>" . htmlspecialchars($msg) . "</p>";
                    }
                }
                ?>

                <?php if ($isAdmin): ?>
                    <div class="admin-upload-panel">
                        <div class="upload-panel-text">
                            <h4>Add Documentation File</h4>
                            <p>Upload a new file to the currently selected category.</p>
                        </div>

                        <div class="upload-panel-actions">
                            <button type="button" class="file-add-btn open-documentation-modal-btn">Add File</button>
                        </div>
                    </div>
                <?php endif; ?>

                <div class="documentation-category-section active" id="category-project-plans"><div class="documentation-cards"><?php renderDocumentationCards($documentationByCategory['project-plans'], $isAdmin); ?></div></div>
                <div class="documentation-category-section" id="category-network-diagrams"><div class="documentation-cards"><?php renderDocumentationCards($documentationByCategory['network-diagrams'], $isAdmin); ?></div></div>
                <div class="documentation-category-section" id="category-functional-design"><div class="documentation-cards"><?php renderDocumentationCards($documentationByCategory['functional-design'], $isAdmin); ?></div></div>
                <div class="documentation-category-section" id="category-requirements-analysis"><div class="documentation-cards"><?php renderDocumentationCards($documentationByCategory['requirements-analysis'], $isAdmin); ?></div></div>
                <div class="documentation-category-section" id="category-technical-design"><div class="documentation-cards"><?php renderDocumentationCards($documentationByCategory['technical-design'], $isAdmin); ?></div></div>
                <div class="documentation-category-section" id="category-charts-period-plans"><div class="documentation-cards"><?php renderDocumentationCards($documentationByCategory['charts-period-plans'], $isAdmin); ?></div></div>
            </section>
        </section>
    </main>

    <?php if ($isAdmin): ?>
        <div class="project-modal-overlay" id="documentationModalOverlay">
            <div class="project-modal">
                <button type="button" class="project-modal-close" id="documentationModalClose">&times;</button>
                <h2>Add Documentation File</h2>

                <form action="" method="POST" enctype="multipart/form-data" class="project-modal-form">
                    <input type="hidden" name="category" id="modalDocumentationCategory">
                    <input type="hidden" name="add_documentation_file_submit" value="1">

                    <input type="text" name="title" placeholder="Document title" required>
                    <textarea name="description" placeholder="Document description" required></textarea>

                    <select name="period" required>
                        <option value="">Choose period</option>
                        <option value="Period 1">Period 1</option>
                        <option value="Period 2">Period 2</option>
                        <option value="Period 3">Period 3</option>
                        <option value="Period 4">Period 4</option>
                    </select>

                    <div class="upload-panel-actions">
                        <input type="file" name="documentation_file" id="file-upload-documentation-modal" class="file-input-hidden" required>
                        <button type="button" class="file-select-btn" data-target="file-upload-documentation-modal">Choose File</button>
                        <span class="selected-file-name">No file selected</span>
                    </div>

                    <button type="submit" class="file-add-btn">Add File</button>
                </form>
            </div>
        </div>
    <?php endif; ?>

    <div class="file-viewer-overlay" id="fileViewerOverlay">
        <div class="file-viewer-modal">
            <button type="button" class="file-viewer-close" id="fileViewerClose">&times;</button>
            <div class="file-viewer-header">
                <h2 class="file-viewer-title" id="fileViewerTitle">Document Viewer</h2>
                <p class="file-viewer-subtitle">Preview your uploaded file without leaving the page.</p>
            </div>
            <div class="file-viewer-body" id="fileViewerBody"></div>
        </div>
    </div>

    <script src="js/documentation.js"></script>
</body>
</html>