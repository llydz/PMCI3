<?php
require "config.php";
function connect()
{
	$mysqli = new mysqli(SERVER, USERNAME, PASSWORD, DATABASE);
	// Error checker
	if ($mysqli->connect_errno != 0) {
		// error retriever
		$error = $mysqli->connect_error;
		// Date of error
		$error_date = date("F j, Y, g:i a");
		// Error message with date
		$message = "{$error} | {$error_date} \r\n";
		// Put the error in db-log.txt
		file_put_contents("db-log.txt", $message, FILE_APPEND);
		return false;
	} else {
		// Connection Successful
		$mysqli->set_charset("utf8mb4");
		return $mysqli;
	}
}
function register($admin_user, $admin_pass, $name, $email, $username, $password, $confirm_password)
{
	// Establish a database connection.
	$mysqli = connect();
	// If there's an error in database the program will stop function
	if (!$mysqli) {
		return false;
	}

	// Trim whitespace from input values.
	$admin_user = trim($admin_user);
	$admin_pass = trim($admin_pass);
	$name = trim($name);
	$email = trim($email);
	$username = trim($username);
	$password = trim($password);
	$confirm_password = trim($confirm_password);

	// Check if any field is empty.
	$args = func_get_args();
	foreach ($args as $value) {
		if (empty($value)) {
			// If any field is empty, return an error message.
			return "All fields are required";
		}
	}
	// Check for disallowed characters (< and >).
	foreach ($args as $value) {
		if (preg_match("/([<|>])/", $value)) {
			// If disallowed characters are found, 
			// return an error message.
			return "< and > characters are not allowed";
		}
	}
	// Validate email format.
	if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
		// If email is not valid, return an error message.
		return "Email is not valid";
	}
	$admin_user = filter_var($admin_user, FILTER_SANITIZE_STRING);
	$admin_pass = filter_var($admin_pass, FILTER_SANITIZE_STRING);

	// Admin account checker
	$sql = "SELECT username, password FROM account WHERE username = ?";
	$stmt = $mysqli->prepare($sql);
	$stmt->bind_param("s", $admin_user);
	$stmt->execute();
	$result = $stmt->get_result();
	$data = $result->fetch_assoc();
	if ($data == NULL) {
		return "Account not found";
	}
	if (password_verify($admin_pass, $data["password"]) == FALSE) {
		return "Wrong Admin Password";
	}
	// Check if the email already exists in the database.
	$stmt = $mysqli->prepare("SELECT email FROM account WHERE email = ?");
	$stmt->bind_param("s", $email);
	$stmt->execute();
	$result = $stmt->get_result();
	$data = $result->fetch_assoc();
	if ($data != NULL) {
		// If email already exists, return an error message.
		return "Email already exists";
	}
	// Check if the username is too long. 
	if (strlen($username) > 12) {
		// If username is too long, return an error message.
		return "Username must contain max 12 characters only";
	}
	// Check if the username already exists in the database.
	$stmt = $mysqli->prepare("SELECT username FROM account WHERE username = ?");
	$stmt->bind_param("s", $username);
	$stmt->execute();
	$result = $stmt->get_result();
	$data = $result->fetch_assoc();
	if ($data != NULL) {
		// If username already exists, return an error message.
		return "Username already exists, please use a different username";
	}
	// Check if the password is too long.
	if (strlen($password) < 8) {
		// If password is too long, return an error message.
		return "Password is too short, must be 8-24 characters";
	}
	if (strlen($password) > 24) {
		// If password is too long, return an error message.
		return "Password is too long, must be 8-24 characters ";
	}
	// Check if the passwords match.
	if ($password != $confirm_password) {
		// If passwords don't match, return an error message.
		return "Passwords doesn't match";
	}
	// Hash the password for security.
	$hashed_password = password_hash($password, PASSWORD_DEFAULT);
	// Insert user data into the 'account' table.
	$stmt = $mysqli->prepare("INSERT INTO account (username, password, email, name) VALUES (?, ?, ?, ?)");
	$stmt->bind_param("ssss", $username, $hashed_password, $email, $name);
	$stmt->execute();
	// Check if the insertion was successful.
	if ($stmt->affected_rows != 1) {
		// If an error occurred during insertion, return an error message.
		return "An error occurred. Please try again";
	} else {
		// If successful, return a success message.
		return "success";
	}
}
function login($username, $password)
{
	// Establish a database connection.
	$mysqli = connect();
	// If there's an error in database the program will stop function
	if (!$mysqli) {
		return false;
	}
	// Trim leading and trailing whitespaces 
	// from username and password.
	$username = trim($username);
	$password = trim($password);
	// Check if either username or password is empty.
	if ($username == "" || $password == "") {
		return "Both fields are required";
	}
	// Sanitize username and password to prevent SQL injection.
	$username = filter_var($username, FILTER_SANITIZE_STRING);
	$password = filter_var($password, FILTER_SANITIZE_STRING);
	// Prepare SQL statement to select username 
	// and password from account table.
	$sql = "SELECT username, password, id FROM account WHERE username = ?";
	$stmt = $mysqli->prepare($sql);
	// Bind the username parameter to the prepared statement.
	$stmt->bind_param("s", $username);
	// Execute the prepared statement to query the database.
	$stmt->execute();
	// Get the result set from the executed statement.
	$result = $stmt->get_result();
	// Fetch the associative array representing the first row of the result set.
	$data = $result->fetch_assoc();
	// Check if the username exists in the database.
	if ($data == NULL) {
		return "Username not recognized";
	}
	// Verify the provided password against the 
	// hashed password in the database.
	if (password_verify($password, $data["password"]) == FALSE) {
		return "Wrong password";
	} else {
		// If authentication is successful, 
		// set the user session and redirect to account page.
		$id = $data["id"];
		$_SESSION["admin"] = $id;
		header("location: home.php");
		exit();
	}
}
function uploadnews($file, $title, $description, $date)
{
	// Establish a database connection.
	$mysqli = connect();

	// If there's an error in the database, the program will stop the function
	if (!$mysqli) {
		return false;
	}

	$targetDir = "newspics/";
	$imageFileType = strtolower(pathinfo($file["name"], PATHINFO_EXTENSION));

	// Get the last inserted ID and increment by one for the new ID
	$sql = "SELECT MAX(id) AS last_id FROM news FOR UPDATE";
	$result = $mysqli->query($sql);
	$row = $result->fetch_assoc();
	$newID = $row['last_id'] + 1;

	// Rename the file using the new ID
	$targetFile = $targetDir . $newID . '.' . $imageFileType;

	if (!file_exists($targetDir)) {
		mkdir($targetDir, 0777, true); // Create directory if it doesn't exist
	}

	// Check if image file is an actual image or fake image
	$check = getimagesize($file["tmp_name"]);
	if ($check === false) {
		return "File is not an image.";
	}

	// Check if file already exists
	if (file_exists($targetFile)) {
		return "Sorry, file already exists.";
	}
	// Check file size, 25mb max
	if ($file["size"] > 25 * 1024 * 1024) {
		return "Sorry, your file is too large. Please upload up to 25mb only";
	}
	// Allow certain file formats
	$allowedFormats = array("jpg", "jpeg", "png", "webp");
	if (!in_array($imageFileType, $allowedFormats)) {
		return "Sorry, only WEBP, JPG, JPEG & PNG files are allowed.";
	}

	// Move uploaded file to target directory with the new name
	if (move_uploaded_file($file["tmp_name"], $targetFile)) {
		// Insert the news with the new ID and file path
		$sql = "INSERT INTO news (id, image_path, title, description, reg_date) VALUES (?, ?, ?, ?, ?)";
		$stmt = $mysqli->prepare($sql);
		$stmt->bind_param("issss", $newID, $targetFile, $title, $description, $date);
		$stmt->execute();
		$result = $stmt->affected_rows;
		if ($result > 0) {
			$mysqli->commit(); // Commit the transaction
			return "success";
		} else {
			return "Sorry, there was an error uploading the news.";
		}
	} else {
		return "Sorry, there was an error uploading your file.";
	}
}

function getnews()
{
	// Establish a database connection.
	$mysqli = connect();
	// If there's an error in the database, the program will stop function
	if (!$mysqli) {
		return false;
	}
	$sql = "SELECT * FROM news ORDER BY reg_date ASC";
	$result = $mysqli->query($sql);
	if ($result->num_rows > 0) {
		$news = '';
		while ($row = $result->fetch_assoc()) {
			$regDate = new DateTime($row['reg_date']);
			$readabledate = $regDate->format('F j, Y'); // Format the date as desired
			$fetchedDate = $row['reg_date'];
			$valuedate = date('Y-m-d', strtotime($fetchedDate));
			$news .= '
			<div class="news">
			<div class="del">
			<!-- Edit button -->
			<button class="editbtn" onclick="edit(' . $row['id'] . ')"><i class="fas fa-edit"></i> Edit</button>
			<button class="delbtn" onclick="submitDeleteForm()"><i class="fas fa-trash"></i> Delete</button>
			</div>
			<div class="news-img">
			<img src="' . $row['image_path'] . '" alt="Picture not found" draggable="false">
			</div>
			<div class="details">
			<h1>' . $row['title'] . '</h1>
			<H3> [' . $readabledate . '] </H3> <br>
			<p>' . $row['description'] . '</p>
			</div>
			</div>

			<form id="myDeleteForm" action="" method="POST">
			<input type="hidden" name="delete_form" value="1">
			</form>

			<form id="myForm' . $row['id'] . '" action="" method="POST" enctype="multipart/form-data">
			<div id="myModal' . $row['id'] . '" class="modal">
			<!-- Modal content -->
			<div class="modal-content">
			<span class="close" onclick="closeModal(' . $row['id'] . ')">&times;</span>
			<h2>Modal Form</h2>

			<label for="updateimage">IMAGE</label>
			<input type="file" name="updateimage" id="updateimage" accept="image/*"><br>

			<label for="title">TITLE</label>
			<input class="title" type="text" name="updatetitle" id="updatetitle" value="' . $row['title'] . '"
			placeholder="Title of the News" required><br>

			<label for="description">DESCRIPTION</label>
			<textarea name="updatedescription" class="desc" id="updatedescription" placeholder="Description of the News"
			required>' . $row['description'] . '</textarea>

			<label for="date">DATE</label>
			<input type="date" name="updatedate" id="updatedate" class="date" value="' . $valuedate . '" required><br>

			<div class="sub">
			<input class="submit" type="submit" value="Update" name="update_' . $row['id'] . '">
			</div>
			</div>
			</div>
			</form>
			<script>
			// Function to open the modal
			function edit(id) {
				var modal = document.getElementById("myModal" + id);
				modal.style.display = "block";
			}
			// Function to close the modal
			function closeModal(id) {
				var modal = document.getElementById("myModal" + id);
				modal.style.display = "none";
			}
			// Close the modal when the user clicks the close button
			function closeOnClick(id) {
				var modal = document.getElementById("myModal" + id);
				modal.style.display = "none";
			}
			// close the modal on pressing escape
			document.addEventListener("keydown", function(event) {
				if (event.key === "Escape") {
					closeModal(' . $row['id'] . ');
				}
				})

				function submitDeleteForm() {
					var response = confirm("Are you sure you want to delete this item?");
					if (response) {
						document.getElementById("myDeleteForm").submit(); // Yes
						} else {
							return false; // No
						}
					}


					</script>';

			if (isset($_POST['update_' . $row['id']])) {
				$id = $row['id'];
				$newfile = $_FILES['updateimage'];
				$title = $_POST['updatetitle'];
				$description = $_POST['updatedescription'];
				$date = $_POST['updatedate'];

				if (!empty($newfile['name'])) {
					$targetDir = "newspics/";
					$imageFileType = strtolower(pathinfo($newfile["name"], PATHINFO_EXTENSION));
					$newFileName = $id . '.' . $imageFileType;
					$targetFile = $targetDir . basename($newFileName);

					// Check if the target directory exists and is writable
					if (!file_exists($targetDir)) {
						mkdir($targetDir, 0777, true); // Create directory if it doesn't exist
					}

					if (!is_writable($targetDir)) {
						echo "Error: Target directory is not writable.";
						exit();
					}
					// Check if image file is an actual image or fake image
					$check = getimagesize($newfile["tmp_name"]);
					if ($check === false) {
						echo "<p class='error'><i class='fas fa-times'></i> File is not an image.</p>";
					} else {
						// Delete the old image from the directory
						$oldImagePath = $row['image_path'];
						if (file_exists($oldImagePath)) {
							unlink($oldImagePath);
						}
						// Check if file already exists
						if (file_exists($targetFile)) {
							echo "<p class='error'><i class='fas fa-times'></i> Sorry, file already exists.</p>";
						} else {
							// Check file size, 25mb max
							if ($newfile["size"] > 25 * 1024 * 1024) {
								echo "<p class='error'><i class='fas fa-times'></i> Sorry, your file is too large. Please upload up to 25mb only.</p>";
							} else {

								// Allow certain file formats
								$allowedFormats = array("jpg", "jpeg", "png", "webp");
								if (!in_array($imageFileType, $allowedFormats)) {
									echo "<p class='error'><i class='fas fa-times'></i> Sorry, only WEBP, JPG, JPEG & PNG files are allowed.</p>";
								} else {

									// Move the uploaded file to the target directory
									if (move_uploaded_file($newfile["tmp_name"], $targetFile)) {
										// Remove the old picture from the directory

										// Update the database with the new image path
										$sql_update = "UPDATE news SET image_path = '$targetFile', title='$title', description='$description', reg_date='$date' WHERE id='$id'";
										if ($mysqli->query($sql_update) === TRUE) {
											echo "<p class='success'> <i class='fas fa-check'></i> News updated successfully</p>";
											header("Location: " . $_SERVER['REQUEST_URI']); // Redirect to the same page
											exit();
										} else {
											return "Error updating database: " . $mysqli->error;
										}
									} else {
										return "Error moving picture: " . error_get_last()['message'];
									}
								}
							}
						}
					}
				} else {
					// Process the edited news data and update the database without changing the image
					$sql_update = "UPDATE news SET title='$title', description='$description', reg_date='$date' WHERE id='$id'";
					if ($mysqli->query($sql_update) === TRUE) {
						echo "<p class='success'> <i class='fas fa-check'></i> News updated successfully</p>";
						header("Location: " . $_SERVER['REQUEST_URI']); // Redirect to the same page
						exit();
					} else {
						echo "Error updating record: " . $mysqli->error;
					}
				}
			}

			if (isset($_POST['delete_form'])) {
				$id = $row['id'];
				$todelete = $row['image_path'];
				unlink($todelete);
				$sql_delete = "DELETE FROM news WHERE id='$id'";
				if ($mysqli->query($sql_delete) === TRUE) {
					header("Location: " . $_SERVER['REQUEST_URI']); // Redirect to the same page
					echo "<p class='success'> <i class='fas fa-check'></i> News deleted successfully</p>";
					exit();
				} else {
					echo "Error deleting record: " . $mysqli->error;
				}
			}

		}
		return $news;
	} else {
		echo "<h1 style='width:100; text-align:center'>No news found.</h1>";
	}
}
function news()
{
	// Establish a database connection.
	$mysqli = connect();
	// If there's an error in database the program will stop function
	if (!$mysqli) {
		return false;
	}

	$sql = "SELECT * FROM news ORDER BY reg_date ASC";
	$result = $mysqli->query($sql);
	if ($result->num_rows > 0) {
		$news = '';
		while ($row = $result->fetch_assoc()) {
			$regDate = new DateTime($row['reg_date']);
			$formattedDate = $regDate->format('F j, Y'); // Format the date as desired
			$news .= '<div class="news">
			<div class="news-img">
			<img src="./ADMIN/' . $row['image_path'] . '" alt="news" draggable="false" >
			</div>
			<div class="details">
			<h1>' . $row['title'] . '</h1>
			<H3> [' . $formattedDate . '] </H3> <br>
			<p>' . $row['description'] . '</p>
			</div>
			</div>';
		}
		return $news;
	} else {
		echo "<h1> No news found.</h1>";
		return '';
	}
}

function accountinfo()
{
	$mysqli = connect();
	if ($mysqli === false) {
		return false;
	}
	if (isset($_SESSION['admin'])) {
		$sql = "SELECT * FROM account WHERE id = ?";
		$stmt = $mysqli->prepare($sql);
		$stmt->bind_param("i", $_SESSION['admin']);
		$stmt->execute();
		$result = $stmt->get_result();
		if ($result->num_rows > 0) {
			$row = $result->fetch_assoc();
			return [
				'name' => $row['name'],
				'email' => $row['email'],
				'username' => $row['username']
			];
		}
	}
	return false;
}

function holidaytable()
{
	$mysqli = connect();
	if ($mysqli === false) {
		return false;
	}

	// Fetch holiday data from the database
	$sql = "SELECT holiday_name, holiday_date FROM holiday";
	$result = $mysqli->query($sql);

	if ($result->num_rows > 0) {
		while ($row = $result->fetch_assoc()) {
			$holidayName = $row["holiday_name"];
			$holidayDate = $row["holiday_date"];

			// Output holiday name and date in the text boxes
			echo '<tr>';
			echo '<td><input type="text" class="textbox" value="' . $holidayName . '"></td>';
			echo '<td><input type="date" class="textbox1" value="' . $holidayDate . '"></td>';
			echo '<td><input type="button" class="del" name="" id="" value="delete"></td>';
			echo '</tr>';
		}
	} else {
		echo '<tr>';
		echo '<td colspan="3">No Holiday Found</td>';
		echo '</tr>';
	}
	$mysqli->close();
}

function getholidays()
{
	$mysqli = connect();
	if ($mysqli === false) {
		return false;
	}

	// SQL query to fetch holiday dates and titles
	$sql = "SELECT holiday_date, holiday_name FROM holiday";

	// Execute the query
	$result = $mysqli->query($sql);

	// Array to hold holiday dates in JavaScript format
	$holidayArray = array();

	// Check if there are results
	if ($result->num_rows > 0) {
		// Output data of each row
		while ($row = $result->fetch_assoc()) {
			// Convert holiday date to JavaScript format
			$holidayTimestamp = strtotime($row["holiday_date"]);
			$holidayYear = date("Y", $holidayTimestamp);
			$holidayMonth = date("n", $holidayTimestamp) - 1; // Subtract one from the month
			$holidayDay = date("j", $holidayTimestamp);
			$holidayDate = "new Date($holidayYear, $holidayMonth, $holidayDay)";

			// Push date and title to the JavaScript array
			$holidayArray[] = "new Date(" . $holidayDate . ").getTime()";
		}
	}

	// Close the database connection
	$mysqli->close();

	// Return JavaScript array as a string
	return implode(",\n\t", $holidayArray);
}


function enrollment(
	$name,
	$age,
	$bday,
	$address,
	$contact,
	$email,
	$level,
	$school,
	$sy,
	$referral,
	$pic,
	$psa,
	$goodmoral,
	$card,
	$ecd,
	$fee,
	$appointdate,
	$appointtime
) {

	// $name
// $age
// $bday
// $address
// $contact
// $email
// $level
// $school
// $schoolyear
// $referral
// $pic
// $psa
// $goodmoral
// $card
// $ecd
// $fee
// $appointment

	$mysqli = connect();
	if ($mysqli === false) {
		return false;
	}

	$name = trim($name);
	$age = trim($age);
	$bday = trim($bday);
	$address = trim($address);
	$contact = trim($contact);
	$email = trim($email);
	$level = trim($level);
	$school = trim($school);
	$sy = trim($sy);
	$referral = trim($referral);

	$pic = isset($_POST['picture']) ? 1 : 0;
	$psa = isset($_POST['psa']) ? 1 : 0;
	$goodmoral = isset($_POST['goodmoral']) ? 1 : 0;
	$card = isset($_POST['card']) ? 1 : 0;
	$ecd = isset($_POST['ecd']) ? 1 : 0;
	$fee = isset($_POST['fee']) ? 1 : 0;



	$appointdate = trim($appointdate);
	$appointtime = trim($appointtime);
}