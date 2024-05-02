



<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Arusha Technical College Timetable</title>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/css/bootstrap.min.css">
    <style>
        body {
            margin: 0;
            padding: 0;
            overflow-x: hidden; /* Hide horizontal scrollbar */
        }

        .top-bar {
            background-color: #353535;
            padding: 10px;
            text-align: center;
            position: fixed;
            width: 100%;
            z-index: 1000;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .logo-container {
            margin-right: 10px; /* Add some margin to the right of the logo */
        }

        .title-container {
            flex-grow: 1; /* Allow the title to take up remaining space */
            text-align: center;
            color: white;
        }

        .logo {
            max-width: 100px;
            height: auto;
        }

        .side-bar {
            background-color: black;
            height: 100vh;
            width: 200px;
            position: fixed;
            top: 60px; /* Set to the height of the top bar */
            left: 0;
            padding-top: 30px; /* Adjust top padding to align links with top bar */
            margin-bottom: 30px;
        }

        .side-bar a {
            display: block;
            padding: 10px;
            color: white;
            text-decoration: none;
            margin-bottom: 10px; /* Add margin between links */
        }

        .content {
            margin-left: 200px; /* Adjust based on side bar width */
            padding: 20px;
            transition: margin-left 0.3s; /* Add smooth transition effect */
            background-color: white; /* Add white background to content area */
        }
    </style>
</head>
<body>

<!-- Top Bar -->
<div class="top-bar">
    <div class="logo-container">
    <img class="logo" src="images/atclogo.jfif" alt="Logo" width="30" height="100">
    </div>
    <div class="title-container">
        <h2>Arusha Technical College</h2>
    </div>
</div>

<!-- Side Bar -->
<div class="side-bar">
    <a href="view-timetable.php">View Timetable</a>
    <a href="add-timetable.php">Add Timetable</a>
    <a href="view-courses.php">View Courses</a>
    <a href="add-course.php">Add Course</a>
    <a href="view-venues.php">View Venues</a>
    <a href="add-venue.php">Add Venue</a>
    <a href="view-teachers.php">View Lecturers</a>
    <a href="add-teacher.php">Add Lecturer</a>
    <a href="add-subject.php">Add Modules</a>
    <a href="view-subjects.php">View Modules</a>
    <a href="free-venue.php">Free Venue</a>

    <!-- Add more links for other pages as needed -->
</div>

<!-- Content Area -->
<div class="content" id="main-content">

  
<?php
include 'db_connection.php';

// Fetch courses and levels from the database
$coursesResult = $conn->query("SELECT * FROM courses GROUP BY name");
$levelsResult = $conn->query("SELECT DISTINCT level FROM courses");

// Initialize variables
$moduleName = $moduleCode = $selectedCourse = $selectedLevel = $selectedSemester = '';
$errorMsg = '';

// Check if the form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Sanitize and get form data
    $moduleName = sanitizeInput($_POST["subject"]);
    $moduleCode = sanitizeInput($_POST["code"]);
    $selectedCourse = sanitizeInput($_POST["course"]);
    $selectedLevel = sanitizeInput($_POST["level"]);
    $selectedSemester = sanitizeInput($_POST["semester"]);

    // Check for empty fields
    if (empty($moduleName) || empty($moduleCode) || empty($selectedCourse) || empty($selectedLevel) || empty($selectedSemester)) {
        $errorMsg = '<div class="alert alert-danger" role="alert">Please fill in all fields.</div>';
    } else {
        // Check for duplicate entries
        $duplicateCheckQuery = "SELECT COUNT(*) FROM subjects WHERE name = '$moduleName' AND code = '$moduleCode' AND course = '$selectedCourse' AND level = '$selectedLevel' AND semester = '$selectedSemester'";
        $duplicateCheckResult = $conn->query($duplicateCheckQuery);
        $count = $duplicateCheckResult->fetch_assoc()["COUNT(*)"];

        if ($count > 0) {
            $errorMsg = '<div class="alert alert-danger" role="alert">Duplicate entry. This module already exists.</div>';
        } else {
            // Insert data into the database
            $insertQuery = "INSERT INTO subjects (name, code, course, level, semester) VALUES ('$moduleName', '$moduleCode', '$selectedCourse', '$selectedLevel', '$selectedSemester')";

            if ($conn->query($insertQuery)) {
                // Redirect to view-courses.php after successful addition
                header("Location: view-subjects.php");
                exit();
            } else {
                $errorMsg = '<div class="alert alert-danger" role="alert">Error: ' . $conn->error . '</div>';
            }
        }
    }

    // Reset internal pointers after fetching courses and levels
    $coursesResult->data_seek(0);
    $levelsResult->data_seek(0);
}

function sanitizeInput($input)
{
    global $conn;
    return mysqli_real_escape_string($conn, htmlspecialchars(strip_tags(trim($input))));
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add Modules</title>
    <link href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/css/bootstrap.min.css" rel="stylesheet">
    <style>
        .container {
            margin-top: 50px;
        }
    </style>
</head>
<body>
    <div class="container mt-5">
        <h2>Add Module</h2>
        <?php echo $errorMsg; ?>
        <form action="" method="post">
            <div class="form-group">
                <label for="subject">Module Name:</label>
                <input type="text" class="form-control" id="subject" name="subject" value="<?php echo $moduleName; ?>">
            </div>

            <div class="form-group">
                <label for="code">Module Code :</label>
                <input type="text" class="form-control" id="code" name="code" value="<?php echo $moduleCode; ?>" >
            </div>

            <div class="form-group">
                <label for="course">Course:</label>
                <select class="form-control" id="course" name="course">
                    <option value="" disabled selected>Select Course</option>
                    <?php
                    while ($courseRow = $coursesResult->fetch_assoc()) {
                        echo "<option value='{$courseRow['name']}'" . ($selectedCourse == $courseRow['name'] ? " selected" : "") . ">{$courseRow['name']}</option>";
                    }
                    ?>
                </select>
            </div>

            <div class="form-group">
                <label for="level">Level</label>
                <select class="form-control" id="level" name="level">
                    <option value="" disabled selected>Select Level</option>
                    <?php
                    while ($levelRow = $levelsResult->fetch_assoc()) {
                        echo "<option value='{$levelRow['level']}'" . ($selectedLevel == $levelRow['level'] ? " selected" : "") . ">{$levelRow['level']}</option>";
                    }
                    ?>
                </select>
            </div>

            <div class="form-group">
                <label for="semester">Semester</label>
                <input type="text" class="form-control" id="semester" name="semester" value="<?php echo $selectedSemester; ?>">
            </div>

            <button type="submit" class="btn btn-primary">Add Module</button>
        </form>
    </div>
    <script src="https://code.jquery.com/jquery-3.2.1.slim.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.12.9/umd/popper.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/js/bootstrap.min.js"></script>
</body>
</html>


</div>

<script src="https://code.jquery.com/jquery-3.2.1.slim.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.12.9/umd/popper.min.js"></script>
<script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/js/bootstrap.min.js"></script>
<script>
    // Function to load content dynamically
    function loadContent(pageId) {
        $.ajax({
            url: pageId,
            type: 'GET',
            success: function (data) {
                $("#main-content").html(data);
            },
            error: function () {
                alert('Error loading content');
            }
        });
    }

    // Set default content to View Timetable
    loadContent("view-timetable.php");

    // Attach click event listeners to sidebar links
    $(".side-bar a").click(function (event) {
        event.preventDefault();
        var pageId = $(this).attr("href");
        loadContent(pageId);
    });
</script>
</body>
</html>

