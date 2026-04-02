<?php
session_start();

if (!isset($_SESSION['username'])) {
    header("Location: log_in.php");
    exit;
}

$isAdmin = isset($_SESSION['role_id']) && $_SESSION['role_id'] == 1;
$msgs = [];

require("includes/db_connect.php");

$allowedCategories = ['minutes', 'reflection', 'feedback', 'presentations', 'training', 'certifications'];
$allowedExtensions = ['pdf', 'doc', 'docx', 'ppt', 'pptx', 'xls', 'xlsx', 'jpg', 'jpeg', 'png', 'zip'];

if ($isAdmin && $_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST["add_skill_file_submit"])) {
    $category = trim($_POST["category"] ?? '');
    $title = trim($_POST["title"] ?? '');
    $description = trim($_POST["description"] ?? '');
    $period = trim($_POST["period"] ?? '');

    if (!in_array($category, $allowedCategories, true) || $title === '' || $description === '' || $period === '') {
        $msgs[] = "Please fill in all fields correctly.";
    } elseif (!isset($_FILES["skill_file"]) || $_FILES["skill_file"]["error"] !== UPLOAD_ERR_OK) {
        $msgs[] = "Please choose a valid file.";
    } else {
        $originalFileName = $_FILES["skill_file"]["name"];
        $tmpFileName = $_FILES["skill_file"]["tmp_name"];
        $fileSize = $_FILES["skill_file"]["size"];
        $extension = strtolower(pathinfo($originalFileName, PATHINFO_EXTENSION));

        if (!in_array($extension, $allowedExtensions, true)) {
            $msgs[] = "This file type is not allowed.";
        } elseif ($fileSize > 10 * 1024 * 1024) {
            $msgs[] = "File is too large. Maximum size is 10MB.";
        } else {
            $uploadFolder = "uploads/professional-skills/";

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
                        INSERT INTO professional_skills_files
                        (category, title, description, period, original_file_name, stored_file_name, file_path)
                        VALUES
                        (:category, :title, :description, :period, :original_file_name, :stored_file_name, :file_path)
                    ");

                    $stmt->execute([
                        ":category" => $category,
                        ":title" => $title,
                        ":description" => $description,
                        ":period" => $period,
                        ":original_file_name" => $originalFileName,
                        ":stored_file_name" => $storedFileName,
                        ":file_path" => $filePath
                    ]);

                    header("Location: professional-skills.php");
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

if ($isAdmin && $_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST["delete_skill_file_submit"])) {
    $fileId = (int)($_POST["skill_file_id"] ?? 0);

    if ($fileId <= 0) {
        $msgs[] = "Invalid file selected for deletion.";
    } else {
        try {
            $stmt = $dbHandler->prepare("
                SELECT id, file_path
                FROM professional_skills_files
                WHERE id = :id
                LIMIT 1
            ");
            $stmt->execute([":id" => $fileId]);
            $fileToDelete = $stmt->fetch(PDO::FETCH_ASSOC);

            if (!$fileToDelete) {
                $msgs[] = "File not found.";
            } else {
                $filePath = $fileToDelete["file_path"];

                if (!empty($filePath) && str_starts_with($filePath, 'uploads/professional-skills/') && file_exists($filePath)) {
                    unlink($filePath);
                }

                $deleteStmt = $dbHandler->prepare("
                    DELETE FROM professional_skills_files
                    WHERE id = :id
                ");
                $deleteStmt->execute([":id" => $fileId]);

                header("Location: professional-skills.php");
                exit;
            }
        } catch (PDOException $e) {
            $msgs[] = "Failed to delete the file.";
        }
    }
}

$categoryDocuments = [
    'minutes' => [],
    'reflection' => [],
    'feedback' => [],
    'presentations' => [],
    'training' => [],
    'certifications' => []
];

if ($dbHandler) {
    try {
        $stmt = $dbHandler->query("SELECT * FROM professional_skills_files ORDER BY uploaded_at DESC");
        $documents = $stmt->fetchAll(PDO::FETCH_ASSOC);

        foreach ($documents as $document) {
            $category = $document["category"];
            if (isset($categoryDocuments[$category])) {
                $categoryDocuments[$category][] = $document;
            }
        }
    } catch (PDOException $e) {
        $msgs[] = "Could not load professional skills files.";
    }
}

function renderSkillDocumentCards($documents, $isAdmin) {
    if (count($documents) === 0) {
        echo "
            <div class='skills-empty-state'>
                <h3>No documents yet</h3>
                <p>There are no uploaded files in this category yet.</p>
            </div>
        ";
        return;
    }

    foreach ($documents as $index => $document) {
        $extension = strtolower(pathinfo($document["stored_file_name"], PATHINFO_EXTENSION));
        $uploadedDate = date('d M Y', strtotime($document["uploaded_at"]));

        echo "
            <article class='skill-document-card' style='animation-delay:" . (0.05 + $index * 0.07) . "s;'>
                <h3 class='document-title'>" . htmlspecialchars($document["title"]) . "</h3>

                <div class='document-meta'>
                    <span class='document-date'>" . htmlspecialchars($uploadedDate) . "</span>
                    <span class='document-period'>" . htmlspecialchars($document["period"]) . "</span>
                </div>

                <p class='document-description'>" . htmlspecialchars($document["description"]) . "</p>

                <div class='document-actions'>
                    <button
                        type='button'
                        class='document-view-btn open-viewer-btn'
                        data-file='" . htmlspecialchars($document["file_path"]) . "'
                        data-title='" . htmlspecialchars($document["title"]) . "'
                        data-extension='" . htmlspecialchars($extension) . "'
                    >
                        <svg class='view-svg-icon' viewBox='0 0 24 24' fill='none' aria-hidden='true'>
                            <path d='M14 3H8a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h8a2 2 0 0 0 2-2V9z'></path>
                            <path d='M14 3v6h6'></path>
                            <path d='M10 13h4'></path>
                            <path d='M10 17h4'></path>
                        </svg>
                        <span>View</span>
                    </button>

                    <a href='" . htmlspecialchars($document["file_path"]) . "' class='document-download-btn' download aria-label='Download document'>
                        <svg class='download-svg-icon' viewBox='0 0 24 24' fill='none' aria-hidden='true'>
                            <path d='M12 4v10'></path>
                            <path d='m8 10 4 4 4-4'></path>
                            <path d='M5 20h14'></path>
                        </svg>
                    </a>";

        if ($isAdmin) {
            echo "
                    <form action='' method='POST' class='document-delete-form' onsubmit=\"return confirm('Delete this file?');\">
                        <input type='hidden' name='skill_file_id' value='" . (int)$document["id"] . "'>
                        <input type='hidden' name='delete_skill_file_submit' value='1'>
                        <button type='submit' class='document-delete-btn'>Delete</button>
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
    <title>Professional Skills</title>
    <link rel="stylesheet" href="css/professional-skills.css">
    <link rel="stylesheet" href="components/header.css">
</head>
<body>
    <?php include("components/header.php") ?>
    <main class="skills-page">
        <section class="skills-hero">
            <h1 class="skills-title">Professional Skills</h1>
            <p class="skills-subtitle">
                Documenting growth through meetings, reflections, and continuous learning
            </p>
        </section>

        <?php
        if (count($msgs) > 0) {
            foreach ($msgs as $msg) {
                echo "<p class='skills-message'>" . htmlspecialchars($msg) . "</p>";
            }
        }
        ?>

        <section class="skills-categories" id="skillsCategories">
            <button class="skill-category-card" type="button" data-category="minutes" data-title="Minutes of Meeting" data-subtitle="Browse and access your documents">
                <span class="skill-icon-box"><svg class="skill-svg-icon" viewBox="0 0 24 24" fill="none" aria-hidden="true"><path d="M9 5H7a2 2 0 0 0-2 2v10a2 2 0 0 0 2 2h10a2 2 0 0 0 2-2V7a2 2 0 0 0-2-2h-2"></path><path d="M9 3h6v4H9z"></path><path d="M9 12h6"></path><path d="M9 16h4"></path></svg></span>
                <span class="skill-card-title">Minutes of Meeting</span>
                <span class="skill-card-count"><?= count($categoryDocuments['minutes']) ?> documents</span>
            </button>

            <button class="skill-category-card" type="button" data-category="reflection" data-title="Reflection Reports" data-subtitle="Review personal learning and development">
                <span class="skill-icon-box"><svg class="skill-svg-icon" viewBox="0 0 24 24" fill="none" aria-hidden="true"><path d="M4 6.5A2.5 2.5 0 0 1 6.5 4H12v16H6.5A2.5 2.5 0 0 0 4 22z"></path><path d="M20 6.5A2.5 2.5 0 0 0 17.5 4H12v16h5.5A2.5 2.5 0 0 1 20 22z"></path></svg></span>
                <span class="skill-card-title">Reflection Reports</span>
                <span class="skill-card-count"><?= count($categoryDocuments['reflection']) ?> documents</span>
            </button>

            <button class="skill-category-card" type="button" data-category="feedback" data-title="Feedback & Reviews" data-subtitle="Read received feedback and evaluation notes">
                <span class="skill-icon-box"><svg class="skill-svg-icon" viewBox="0 0 24 24" fill="none" aria-hidden="true"><path d="M21 15a2 2 0 0 1-2 2H8l-5 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"></path></svg></span>
                <span class="skill-card-title">Feedback & Reviews</span>
                <span class="skill-card-count"><?= count($categoryDocuments['feedback']) ?> documents</span>
            </button>

            <button class="skill-category-card" type="button" data-category="presentations" data-title="Presentations" data-subtitle="Open and review your presentation materials">
                <span class="skill-icon-box"><svg class="skill-svg-icon" viewBox="0 0 24 24" fill="none" aria-hidden="true"><path d="M8 6v12"></path><path d="M12 10v8"></path><path d="M16 4v14"></path></svg></span>
                <span class="skill-card-title">Presentations</span>
                <span class="skill-card-count"><?= count($categoryDocuments['presentations']) ?> documents</span>
            </button>

            <button class="skill-category-card" type="button" data-category="training" data-title="Training & Workshops" data-subtitle="Access workshop records and training files">
                <span class="skill-icon-box"><svg class="skill-svg-icon" viewBox="0 0 24 24" fill="none" aria-hidden="true"><path d="M12 3 4 7l8 4 8-4-8-4z"></path><path d="M4 12l8 4 8-4"></path><path d="M4 17l8 4 8-4"></path></svg></span>
                <span class="skill-card-title">Training & Workshops</span>
                <span class="skill-card-count"><?= count($categoryDocuments['training']) ?> documents</span>
            </button>

            <button class="skill-category-card" type="button" data-category="certifications" data-title="Certifications" data-subtitle="Browse earned certificates and achievements">
                <span class="skill-icon-box"><svg class="skill-svg-icon" viewBox="0 0 24 24" fill="none" aria-hidden="true"><circle cx="12" cy="8" r="4"></circle><path d="M10 12.5 8 21l4-2 4 2-2-8.5"></path></svg></span>
                <span class="skill-card-title">Certifications</span>
                <span class="skill-card-count"><?= count($categoryDocuments['certifications']) ?> documents</span>
            </button>

            <span class="moving-active-dot" id="movingActiveDot"></span>
        </section>

        <p class="skills-helper-text" id="skillsHelperText">
            Select a category above to view your professional documents
        </p>

        <section class="skills-details" id="skillsDetails">
            <div class="skills-details-header">
                <div class="skills-details-icon-box">
                    <span class="skills-details-icon" id="detailsIconHolder">
                        <svg class="skill-svg-icon details-svg-icon" viewBox="0 0 24 24" fill="none" aria-hidden="true">
                            <path d="M9 5H7a2 2 0 0 0-2 2v10a2 2 0 0 0 2 2h10a2 2 0 0 0 2-2V7a2 2 0 0 0-2-2h-2"></path>
                            <path d="M9 3h6v4H9z"></path>
                            <path d="M9 12h6"></path>
                            <path d="M9 16h4"></path>
                        </svg>
                    </span>
                </div>

                <div class="skills-details-heading">
                    <h2 id="detailsTitle">Minutes of Meeting</h2>
                    <p id="detailsSubtitle">Browse and access your documents</p>
                </div>
            </div>

            <?php if ($isAdmin): ?>
                <div class="admin-upload-panel">
                    <div class="upload-panel-text">
                        <h4>Add Professional Skills File</h4>
                        <p>Upload a new file to the currently selected category.</p>
                    </div>

                    <div class="upload-panel-actions">
                        <button type="button" class="file-add-btn open-skill-modal-btn">Add File</button>
                    </div>
                </div>
            <?php endif; ?>

            <div class="skills-category-content" id="category-minutes"><div class="skills-documents-grid"><?php renderSkillDocumentCards($categoryDocuments['minutes'], $isAdmin); ?></div></div>
            <div class="skills-category-content" id="category-reflection"><div class="skills-documents-grid"><?php renderSkillDocumentCards($categoryDocuments['reflection'], $isAdmin); ?></div></div>
            <div class="skills-category-content" id="category-feedback"><div class="skills-documents-grid"><?php renderSkillDocumentCards($categoryDocuments['feedback'], $isAdmin); ?></div></div>
            <div class="skills-category-content" id="category-presentations"><div class="skills-documents-grid"><?php renderSkillDocumentCards($categoryDocuments['presentations'], $isAdmin); ?></div></div>
            <div class="skills-category-content" id="category-training"><div class="skills-documents-grid"><?php renderSkillDocumentCards($categoryDocuments['training'], $isAdmin); ?></div></div>
            <div class="skills-category-content" id="category-certifications"><div class="skills-documents-grid"><?php renderSkillDocumentCards($categoryDocuments['certifications'], $isAdmin); ?></div></div>
        </section>
    </main>

    <?php if ($isAdmin): ?>
        <div class="project-modal-overlay" id="skillModalOverlay">
            <div class="project-modal">
                <button type="button" class="project-modal-close" id="skillModalClose">&times;</button>
                <h2>Add Professional Skills File</h2>

                <form action="" method="POST" enctype="multipart/form-data" class="project-modal-form">
                    <input type="hidden" name="category" id="modalSkillCategory">
                    <input type="hidden" name="add_skill_file_submit" value="1">

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
                        <input type="file" name="skill_file" id="file-upload-skills-modal" class="file-input-hidden" required>
                        <button type="button" class="file-select-btn" data-target="file-upload-skills-modal">Choose File</button>
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

    <script src="js/professional-skills.js"></script>
</body>
</html>