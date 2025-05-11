<?php
require_once("../../admin/inc/config.php");
session_start();

if(isset($_POST['e_id']) AND isset($_POST['c_id']) AND isset($_POST['v_id'])) {
    $election_id = $_POST['e_id'];
    $candidate_id = $_POST['c_id'];
    $voters_id = $_POST['v_id'];
    $vote_date = date("Y-m-d");
    $vote_time = date("h:i:s a");

    // Check if user already voted in this election
    $checkVote = mysqli_query($db, "SELECT * FROM votings WHERE election_id = '$election_id' AND voters_id = '$voters_id'") or die(mysqli_error($db));

    if(mysqli_num_rows($checkVote) > 0) {
        echo "AlreadyVoted";
    } else {
        $query = "INSERT INTO votings (election_id, voters_id, candidate_id, vote_date, vote_time)
                  VALUES ('$election_id', '$voters_id', '$candidate_id', '$vote_date', '$vote_time')";
        $insert = mysqli_query($db, $query) or die(mysqli_error($db));

        if($insert) {
            echo "Success";
        } else {
            echo "Failed";
        }
    }
}
?>
