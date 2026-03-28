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
    <link rel="stylesheet" href="css/home.css">
    <link rel="stylesheet" href="components/header.css">
    <title>My portfolio</title>
</head>
<body>
    <?php include 'components/header.php' ?>
        <section class="hero">
            <div class="top-corner-lines">
                <div class="left-top-lines">
                    <div class="left-horizontal-top-line"></div>
                    <div class="left-vertical-top-line"></div>
                </div>
                    <div class="right-top-lines">
                        <div class="right-horizontal-top-line"></div>
                        <div class="right-vertical-top-line"></div>
                    </div>
            </div>
            <h1 class="hero-text">PORTFOLIO</h1>
            <img class="hero-image" src="images/hero.png" alt="Hero Image">
                <div class="bottom-corner-lines">
                    <div class="left-bottom-lines">
                        <div class="left-vertical-bottom-line"></div>
                        <div class="left-horizontal-bottom-line"></div>
                    </div>
                        <div class="right-bottom-lines">
                            <div class="right-vertical-bottom-line"></div>
                            <div class="right-horizontal-bottom-line"></div>
                        </div>
                </div>
        </section>
    <section class="who-am-i-box">
        <h1 class="who-am-i">WHO AM I?</h1>
        <p class="who-am-i-text">My name is Kristian Endrev, a first-year NHL Stenden 
            student from Plovdiv, Bulgaria. I am passionate about coding, systems 
            and anything IT-related. I also love sports, meeting new people and 
            am always open to new experiences.</p>
        <a href="https://github.com/KristianEndrev" target="_blank"><img class="github-logo" src="images/github.png" alt="github logo"></a>
        <img class="second-pic-of-me" src="images/second_pic_of_me.png" alt="second picture of me">
    </section>
        <section class="my-skills-box">
            <div class="my-skills-items">
                <div class="my-skills-text">
                    <h1>WHAT I BRING TO THE TABLE</h1>
                    <p>I bring hands-on experience with HTML, CSS, PHP, and SQL, 
                        and I’m currently expanding my skills in C and Java. 
                        My knowledge of databases and servers helps me understand how systems connect
                        and function. I also offer strong communication, feedback, and presentation 
                        skills, along with a structured approach to planning, documentation, 
                        and reflection - ready to contribute effectively to any IT project.</p>
                </div>
                        <div class="professional-skills-box"><a href="professional_skills.php">PROFESSIONAL SKILLS</a></div>
                    <div class="skills-boxes">
                        <div class="documentation-box"><a href="documentation.php">DOCUMENTATION</a></div>
                        <div class="projects-box"><a href="projects.php">PROJECTS</a></div>
                    </div>
            </div>
            <h1 class="my-skills-header">MY SKILLS</h1>
        </section>
</body>
</html>