<!DOCTYPE html>
<html lang="en">

<head>


    <link rel="stylesheet" href="./styles/enrollment.css">
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ENROLLMENT</title>


    <link rel="stylesheet" type="text/css"
        href="https://cdnjs.cloudflare.com/ajax/libs/jqueryui/1.12.1/jquery-ui.min.css">
    <link rel="stylesheet" type="text/css"
        href="https://cdnjs.cloudflare.com/ajax/libs/jqueryui/1.12.1/jquery-ui.theme.css">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.3.1/jquery.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jqueryui/1.12.1/jquery-ui.min.js"></script>



</head>

<body>
    <?php
    include 'header.php';

    $name = $_POST['name'];
    $age = $_POST['age'];
    $bday = $_POST['bday'];
    $address = $_POST['address'];
    $contact = $_POST['contact'];
    $email = $_POST['email'];
    $level = $_POST['level'];
    $school = $_POST['school'];
    $schoolyear = $_POST['schoolyear'];
    $referral = $_POST['referral'];
    $pic = $_POST['pic'];
    $psa = $_POST['psa'];
    $goodmoral = $_POST['goodmoral'];
    $card = $_POST['card'];
    $ecd = $_POST['ecd'];
    $fee = $_POST['fee'];
    $appointment = $_POST['appointdate'];
    $appointment = $_POST['appointtime'];

    if (isset($_POST['enroll'])) {
        $response = enrollment($name, $age, $bday, $address, $contact, $email, $level, $school, $sy, $referral, $pic, $psa, $goodmoral, $card, $ecd, $fee, $appointdate, $appointtime);

        if ($response == "success") {
            echo "<script>alert('Enrollment Successful!')</script>";
        } else {
            echo "<script>alert('" . $response . "')</script>";
        }
    }
    ?>

    <main>

        <h1 class="title">ENROLLMENT</h1>

        <div class="form-container">
            <div class="form-flex">
                <form action="" method="POST" autocomplete="false">
                    <h1 class="h1">STUDENT'S INFORMATION</h1>
                    <div class="block">
                        <label for="name">NAME:</label>
                        <input type="text" name="name" id="name" placeholder="Format: Surname, First Name Middle Name"
                            value="<?php echo @$_POST['name']; ?>"><br>

                        <label for="age">AGE:</label>
                        <input type="number" name="age" id="age" placeholder="Age"
                            value="<?php echo @$_POST['age']; ?>"><br>
                    </div>
                    <div class="block">
                        <label for="bday">BIRTHDAY:</label>
                        <input type="date" name="bday" id="bday" value="<?php echo @$_POST['bday']; ?>"><br>

                        <label for="address">ADDRESS</label>
                        <input type="text" name="address" id="address" placeholder="Home Address"
                            value="<?php echo @$_POST['address']; ?>"><br>
                    </div>
                    <div class="block">
                        <label for="contact">CONTACT NUMBER</label>
                        <input type="number" name="contact" id="contact" placeholder="Contact Number"
                            value="<?php echo @$_POST['contact']; ?>">

                        <label for="email">EMAIL</label>
                        <input type="email" name="email" id="email" placeholder="E-Mail"
                            value="<?php echo @$_POST['email']; ?>">
                    </div><br>

                    <H1 class="h1">ACADEMIC INFORMATION</H1>

                    <div class="block">
                        <label for="level">YEAR LEVEL</label>
                        <select name="level" id="level" required>
                            <option value="" selected hidden>Choose Year Level</option>
                            <option value="Kinder 1">Kinder 1</option>
                            <option value="Kinder 2">Kinder 2</option>
                            <option value="Grade 1">Grade 1</option>
                            <option value="Grade 2">Grade 2</option>
                            <option value="Grade 3">Grade 3</option>
                            <option value="Grade 4">Grade 4</option>
                            <option value="Grade 5">Grade 5</option>
                            <option value="Grade 6">Grade 6</option>
                            <option value="Grade 7">Grade 7</option>
                            <option value="Grade 8">Grade 8</option>
                            <option value="Grade 9">Grade 9</option>
                            <option value="Grade 10">Grade 10</option>
                            <option value="Grade 11">Grade 11</option>
                            <option value="Grade 12">Grade 12</option>
                        </select>

                        <label for="school">SCHOOL NAME <br>(If Transfer)</label>
                        <input type="text" name="school" id="school" placeholder="School Name (If transferee)"
                            value="<?php echo @$_POST['school']; ?>">


                    </div>

                    <div class="block">
                        <label for="sy">SCHOOL YEAR <br>(If Transfer)</label>
                        <input type="number" name="sy" id="sy" placeholder="eg. 2020-2021"
                            value="<?php echo @$_POST['sy']; ?>">
                        <label for="referral">REFERRAL NAME</label>
                        <input type="text" name="referral" id="referral" placeholder="Referral Name"
                            value="<?php echo @$_POST['referral']; ?>">
                    </div><BR></BR>

                    <h1 class="h1">REQUIREMENTS</h1>
                    <div class="req">

                        <div class="check">
                            <label for="picture">2pcs 2x2 Picture</label>
                            <input type="checkbox" name="picture" id="picture">
                        </div>
                        <div class="check">
                            <label for="psa">Original Copy of PSA</label>
                            <input type="checkbox" name="psa" id="psa">
                        </div>
                        <div class="check">
                            <label for="goodmoral">Good Moral</label>
                            <input type="checkbox" name="goodmoral" id="goodmoral">
                        </div>
                    </div>

                    <div class="req">
                        <div class="check">
                            <label for="card">Report Card(From previous SY)</label>
                            <input type="checkbox" name="card" id="card">
                        </div>
                        <div class="check">
                            <label for="ecd">ECD Checklist(Kinder)</label>
                            <input type="checkbox" name="ecd" id="ecd">
                        </div>
                        <div class="check">
                            <label for="fee">PHP 5,000(Reservation Fee)</label>
                            <input type="checkbox" name="fee" id="fee">
                        </div>
                    </div>

                    <br>

                    <h1 class="h1">APPOINTMENT CALENDAR</h1><br>
                    <div class="calendar">
                        <div class="date">
                            <label for="appointdate">DATE</label><br>
                            <input type="text" name="appointdate" id="appointdate" class="datepicker"
                                value="<?php echo @$_POST['appointdate']; ?>">
                        </div>

                        <div class="time">
                            <label for="appointtime">TIME</label><br>
                            <select name="appointtime" id="appointtime">
                                <option value="" selected hidden>Choose Appointment Time</option>
                                <option value="8:00 AM - 9:30 AM">8:00 AM - 9:30 AM</option>
                                <option value="1:00 PM - 2:30 PM">10:00 PM - 11:30 PM</option>
                                <option value="1:00 PM - 2:30 PM">1:00 PM - 2:30 PM</option>
                                <option value="3:00 PM - 4:30 PM">3:00 PM - 4:30 PM</option>
                            </select>
                        </div>
                    </div>
                    <div class="submit">
                        <input type="submit" name="enroll" id="enroll" value="Enroll">
                    </div>
                </form>
            </div>
        </div>

    </main>

    <?php include 'footer.php'; ?>
</body>
<script>
    //DATE PICKER
    var dd = new Date().getDate();
    var mm = new Date().getMonth();
    var yyyy = new Date().getFullYear();
    // var tommorrow = new Date().getDate() + 1;

    <?php
    $getholidays = getholidays();
    echo "var holiday = [\n\t" . $getholidays . "\n];";
    ?>

    $('.datepicker').datepicker({
        // Current date 
        minDate: new Date(yyyy, mm, dd + 1),
        beforeShowDay: function (date) {
            var showDay = true;

            // Disabler for weekends
            if (date.getDay() == 0 || date.getDay() == 6) {
                showDay = false;
            }
            // Disabler for holiday array
            if ($.inArray(date.getTime(), holiday) > -1) {
                showDay = false;
            }
            return [showDay];
        }
    });
</script>

</html>