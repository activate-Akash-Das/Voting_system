<?php
// Alert messages based on GET parameters
if (isset($_GET['added'])) {
    echo '<div class="alert alert-success my-3" role="alert">Candidate has been added successfully!</div>';
} elseif (isset($_GET['largeFile'])) {
    echo '<div class="alert alert-danger my-3" role="alert">Image file is too large!</div>';
} elseif (isset($_GET['invalidFile'])) {
    echo '<div class="alert alert-danger my-3" role="alert">Invalid file type!</div>';
} elseif (isset($_GET['failed'])) {
    echo '<div class="alert alert-danger my-3" role="alert">Image uploading failed. Try again!</div>';
} elseif (isset($_GET['deleted'])) {
    echo '<div class="alert alert-success my-3" role="alert">Candidate has been deleted successfully!</div>';
}

if (isset($_GET['delete']) && is_numeric($_GET['delete'])) {
    $deleteId = $_GET['delete'];
    mysqli_query($db, "DELETE FROM candidate_details WHERE id='$deleteId'") or die(mysqli_error($db));
    echo "<script>location.assign('index.php?addCandidatePage=1&deleted=1');</script>";
}
?>

<div class="row my-3">
    <div class="col-4">
        <h3>Add New Candidates</h3>
        <form method="POST" enctype="multipart/form-data">
            <div class="form-group my-3">
                <select class="form-control" name="election_id" required>
                    <option value="">Select Election</option>
                    <?php
                    $fetchinElections = mysqli_query($db, "SELECT * FROM elections") or die(mysqli_error($db));
                    $isAnyElectionAdded = mysqli_num_rows($fetchinElections);

                    if ($isAnyElectionAdded > 0) {
                        while ($row = mysqli_fetch_assoc($fetchinElections)) {
                            $election_id = $row['id'];
                            $election_name = $row['election_topic'];
                            echo "<option value='$election_id'>$election_name</option>";
                        }
                    } else {
                        echo "<option value=''>Please add election first</option>";
                    }
                    ?>
                </select>
            </div>

            <div class="form-group my-3">
                <input type="text" name="candidate_name" placeholder="Candidate Name" class="form-control" required />
            </div>

            <div class="form-group my-3">
                <input type="file" name="candidate_photo" class="form-control" required />
            </div>

            <div class="form-group my-3">
                <input type="text" name="candidate_details" placeholder="Candidate Details" class="form-control" required />
            </div>

            <input type="submit" value="Add Candidate" name="addCandidateBtn" class="btn btn-success" />
        </form>
    </div>

    <div class="col-8">
        <h3>Candidate Details</h3>
        <table class="table">
            <thead>
                <tr>
                    <th>Serial No.</th>
                    <th>Photo</th>
                    <th>Name</th>
                    <th>Details</th>
                    <th>Election</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                <?php
                $fetchingData = mysqli_query($db, "SELECT * FROM candidate_details") or die(mysqli_error($db));
                $isAnyCandidateAdded = mysqli_num_rows($fetchingData);
                $sno = 1;

                if ($isAnyCandidateAdded > 0) {
                    while ($row = mysqli_fetch_assoc($fetchingData)) {
                        $election_id = $row['election_id'];

                        $fetchinElectionName = mysqli_query($db, "SELECT * FROM elections WHERE id = '$election_id'") or die(mysqli_error($db));
                        $execFetchingElectionNameQuery = mysqli_fetch_assoc($fetchinElectionName);
                        $election_name = $execFetchingElectionNameQuery !== null ? $execFetchingElectionNameQuery['election_topic'] : "Unknown Election";

                        $candidate_photo = $row['candidate_photo'];
                        ?>
                        <tr>
                            <td><?php echo $sno++; ?></td>
                            <td>
                                <img src="<?php echo $candidate_photo; ?>" style="width: 80px; height: 80px; border: 3px solid rgb(6, 184, 6); border-radius: 100%;" />
                            </td>
                            <td><?php echo $row['candidate_name']; ?></td>
                            <td><?php echo $row['candidate_details']; ?></td>
                            <td><?php echo $election_name; ?></td>
                            <td>
                                <a href="index.php?editCandidate=<?php echo $row['id']; ?>" class="btn btn-sm btn-warning">Edit</a>
                                <a href="index.php?addCandidatePage=1&delete=<?php echo $row['id']; ?>" class="btn btn-sm btn-danger" onclick="return confirm('Are you sure you want to delete this candidate?');">Delete</a>
                            </td>
                        </tr>
                        <?php
                    }
                } else {
                    echo "<tr><td colspan='6'>No candidate has been added yet.</td></tr>";
                }
                ?>
            </tbody>
        </table>
    </div>
</div>

<?php
// Handle form submission
if (isset($_POST['addCandidateBtn'])) {
    $election_id = mysqli_real_escape_string($db, $_POST['election_id']);
    $candidate_name = mysqli_real_escape_string($db, $_POST['candidate_name']);
    $candidate_details = mysqli_real_escape_string($db, $_POST['candidate_details']);
    $inserted_by = $_SESSION['username'];
    $inserted_on = date("Y-m-d");

    // Handle image upload
    $targetted_folder = "../assets/images/candidate/";
    $candidate_photo = $targetted_folder . rand(1111111111, 999999999) . $_FILES['candidate_photo']['name'];
    $candidate_photo_tmp_name = $_FILES['candidate_photo']['tmp_name'];
    $candidate_photo_type = strtolower(pathinfo($candidate_photo, PATHINFO_EXTENSION));
    $image_size = $_FILES['candidate_photo']['size'];
    $allowed_types = array("jpg", "jpeg", "png");

    if ($image_size < 2000000) {
        if (in_array($candidate_photo_type, $allowed_types)) {
            if (move_uploaded_file($candidate_photo_tmp_name, $candidate_photo)) {
                mysqli_query($db, "INSERT INTO candidate_details (election_id, candidate_name, candidate_details, candidate_photo, inserted_by, inserted_on) VALUES ('$election_id', '$candidate_name', '$candidate_details', '$candidate_photo', '$inserted_by', '$inserted_on')") or die(mysqli_error($db));
                echo "<script>location.assign('index.php?addCandidatePage=1&added=1');</script>";
            } else {
                echo "<script>location.assign('index.php?addCandidatePage=1&failed=1');</script>";
            }
        } else {
            echo "<script>location.assign('index.php?addCandidatePage=1&invalidFile=1');</script>";
        }
    } else {
        echo "<script>location.assign('index.php?addCandidatePage=1&largeFile=1');</script>";
    }
}
?>
