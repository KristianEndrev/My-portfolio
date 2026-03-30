<header>
        <div class="home-button">
            <a href="home.php">HOME</a>
        </div>
    <div class="nav-buttons">
        <ul>
            <?php
            if($isAdmin) {
                echo "<li><a href='admin.php'>ADMIN</a></li>";
            }
            ?>
            <li><a href="projects.php">PROJECTS</a></li>
            <li><a href="professional-skills.php">PROFESSIONAL SKILLS</a></li>
            <li><a href="documentation.php">DOCUMENTATION</a></li>
            <li><a href="">ABOUT ME</a></li>
            <li><a href="">GET IN TOUCH</a></li>
            <li><a class="log-out" href="log_out.php">LOG OUT</a></li>
        </ul>
    </div>
</header>