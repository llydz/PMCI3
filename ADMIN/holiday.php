<!DOCTYPE html>
<html lang="en">

<head>
    <link rel="stylesheet" href="./styles/holiday.css">
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>CALENDAR EDITOR</title>
</head>
<?php
include 'header.php';



if (isset($_POST['add'])) {
    addholiday();
} elseif (isset($_POST['save'])) {
    saveholidays();
} elseif (isset($_POST['load'])) {
    loadDefaultData();
} elseif (isset($_POST['delete'])) {

}


?>

<body>

    <main>
        <nav>
            <div class="navi">

                <a href="./enrollment.php">Enrollment</a>
                <a class="active" href="./holiday.php">Holiday</a>
            </div>
        </nav>

        <div class="holiday">

            <div class="form">
                <div class="TAAS">
                    <h1>HOLIDAY EDITOR</h1>
                </div>
                <form action="" method="POST">
                    <div class="buttonz">
                        <input style="background-color: rgb(47, 47, 228)" type="submit" name="add" id="add" value="Add">
                        <input type="submit" name="save" id="save" value="Save" onclick="return save()">
                        <input style="background-color: rgb(148, 148, 0)" type="submit" name="load" id="load"
                            value="Load Default" onclick="return load()">
                    </div>
                    <div class="table">
                        <table>
                            <tr>
                                <th>HOLIDAY</th>
                                <th>DATE</th>
                                <th></th>
                            </tr>
                            <?php
                            echo holidaytable(); // Echo the function call to populate the form
                            ?>
                </form>
                </table>
            </div>

        </div>
        </div>
    </main>

</body>
<script>
    function save() {
        var response = confirm('Are you sure you want to save?');
        if (response) {
            return true;
        } else {
            return false;
        }
    }
    function load() {
        var response = confirm('Are you sure you want to log out?');
        if (response) {
            return true;
        } else {
            return false;
        }
    }
    function delete () {
        var response = confirm('Are you sure you want to log out?');
        if (response) {
            return true;
        } else {
            return false;
        }
    }


</script>

</html>