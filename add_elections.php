<?php
if (isset($_GET['added'])) {
    echo '<div class="alert alert-success my-3" role="alert">Election has been added successfully!</div>';
}

if (isset($_GET['deleted'])) {
    echo '<div class="alert alert-danger my-3" role="alert">Election has been deleted successfully!</div>';
}

if (isset($_GET['editElection'])) {
    $editId = $_GET['editElection'];
    $editQuery = mysqli_query($db, "SELECT * FROM elections WHERE id='$editId'");
    if (mysqli_num_rows($editQuery) > 0) {
        $editData = mysqli_fetch_assoc($editQuery);
    }
}

if (isset($_POST['updateElectionBtn'])) {
    $election_id = $_POST['election_id'];
    $election_topic = mysqli_real_escape_string($db, $_POST['election_topic']);
    $number_of_candidates = mysqli_real_escape_string($db, $_POST['number_of_candidates']);
    $starting_date = mysqli_real_escape_string($db, $_POST['starting_date']);
    $ending_date = mysqli_real_escape_string($db, $_POST['ending_date']);

    mysqli_query($db, "UPDATE elections SET election_topic='$election_topic', no_of_candidates='$number_of_candidates', starting_date='$starting_date', ending='$ending_date' WHERE id='$election_id'");

    echo '<script>location.assign("index.php?addElectionPage=1&updated=1");</script>';
    exit;
}

if (isset($_GET['deleteElection'])) {
    $deleteId = $_GET['deleteElection'];
    mysqli_query($db, "DELETE FROM elections WHERE id='$deleteId'");
    echo '<script>location.assign("index.php?addElectionPage=1&deleted=1");</script>';
    exit;
}
?>

<div class="row my-3">
    <div class="col-4">
        <h3><?php echo isset($editData) ? 'Edit Election' : 'Add New Election'; ?></h3>
        <form method="POST">
            <input type="hidden" name="election_id" value="<?php echo isset($editData) ? $editData['id'] : ''; ?>">

            <div class="form-group my-3">
                <input type="text" name="election_topic" placeholder="Election Topic" class="form-control" required value="<?php echo isset($editData) ? $editData['election_topic'] : ''; ?>"/>
            </div>

            <div class="form-group my-3">
                <input type="number" name="number_of_candidates" placeholder="Number of candidates" class="form-control" required value="<?php echo isset($editData) ? $editData['no_of_candidates'] : ''; ?>"/>
            </div>

            <div class="form-group my-3">
                <input type="text" onfocus="this.type='Date'" name="starting_date" placeholder="Start Date" class="form-control" required value="<?php echo isset($editData) ? $editData['starting_date'] : ''; ?>"/>
            </div>

            <div class="form-group my-3">
                <input type="text" onfocus="this.type='Date'" name="ending_date" placeholder="End Date" class="form-control" required value="<?php echo isset($editData) ? $editData['ending'] : ''; ?>"/>
            </div>

            <input type="submit" value="<?php echo isset($editData) ? 'Update Election' : 'Add Election'; ?>" name="<?php echo isset($editData) ? 'updateElectionBtn' : 'addElectionBtn'; ?>" class="btn btn-success"/>
        </form>
    </div>

    <div class="col-8">
        <h3>Upcoming Elections</h3>
        <table class="table">
            <thead>
                <tr>
                    <th scope="col">Serial no.</th>
                    <th scope="col">Election</th>
                    <th scope="col">Number of Candidates</th>
                    <th scope="col">Starting Date</th>
                    <th scope="col">Ending Date</th>
                    <th scope="col">Status</th>
                    <th scope="col">Action</th>
                </tr>
            </thead>
            <tbody>
                <?php
                $fetchingData = mysqli_query($db, "SELECT * FROM elections") or die(mysqli_error($db));
                $isAnyElectionAdded = mysqli_num_rows($fetchingData);
                $sno = 1;

                if ($isAnyElectionAdded > 0) {
                    while ($row = mysqli_fetch_assoc($fetchingData)) {
                        echo '<tr>';
                        echo '<td>' . $sno++ . '</td>';
                        echo '<td>' . $row['election_topic'] . '</td>';
                        echo '<td>' . $row['no_of_candidates'] . '</td>';
                        echo '<td>' . $row['starting_date'] . '</td>';
                        echo '<td>' . $row['ending'] . '</td>';
                        echo '<td>' . $row['status'] . '</td>';
                        echo '<td>';
                        echo '<a href="index.php?addElectionPage=1&editElection=' . $row['id'] . '" class="btn btn-sm btn-warning">Edit</a> ';
                        echo '<a href="index.php?addElectionPage=1&deleteElection=' . $row['id'] . '" class="btn btn-sm btn-danger" onclick="return confirm(\'Are you sure you want to delete this election?\')">Delete</a>';
                        echo '</td>';
                        echo '</tr>';
                    }
                } else {
                    echo '<tr><td colspan="7">No elections have been added yet.</td></tr>';
                }
                ?>
            </tbody>
        </table>
    </div>
</div>

<?php
if (isset($_POST['addElectionBtn'])) {
    $election_topic = mysqli_real_escape_string($db, $_POST['election_topic']);
    $number_of_candidates = mysqli_real_escape_string($db, $_POST['number_of_candidates']);
    $starting_date = mysqli_real_escape_string($db, $_POST['starting_date']);
    $ending_date = mysqli_real_escape_string($db, $_POST['ending_date']);
    $inserted_by = $_SESSION['username'];
    $inserted_on = date("Y-m-d");

    $date1 = date_create($inserted_on);
    $date2 = date_create($starting_date);
    $diff = date_diff($date1, $date2);
    $status = ((int)$diff->format("%R%a") > 0) ? "InActive" : "Active";

    mysqli_query($db, "INSERT INTO elections(election_topic, no_of_candidates, starting_date, ending, status, inserted_by, inserted_on) VALUES ('$election_topic', '$number_of_candidates', '$starting_date', '$ending_date', '$status', '$inserted_by', '$inserted_on')") or die(mysqli_error($db));
    echo '<script>location.assign("index.php?addElectionPage=1&added=1");</script>';
    exit;
}
?>
