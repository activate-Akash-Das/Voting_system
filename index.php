<?php
require_once("inc/header.php");
require_once("inc/navigation.php");
?>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

<div class="row my-3">
    <div class="col-12">
        <h2> Voters Panel</h2>

        <?php
        $fetchingActiveElections = mysqli_query($db, "SELECT * FROM elections WHERE status ='Active'") or die(mysqli_error($db));
        $totalActiveElections = mysqli_num_rows($fetchingActiveElections);

        if ($totalActiveElections > 0) {
            while ($data = mysqli_fetch_assoc($fetchingActiveElections)) {
                $election_id = $data['id'];
                $election_topic = $data['election_topic'];
                ?>

                <table class="table">
                    <thead>
                        <tr>
                            <th colspan="4" class="bg-green">
                                <h5>Election name: <?php echo $election_topic; ?></h5>
                            </th>
                        </tr>
                        <tr>
                            <th>Photo</th>
                            <th>Candidate Details</th>
                            <th>Number of Votes</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        $fetchingCandidates = mysqli_query($db, "SELECT * FROM candidate_details WHERE election_id = '$election_id'") or die(mysqli_error($db));
                        $hasVoted = false;
                        $votedCandidateId = null;

                        // Check if current user has voted in this election
                        $checkIfVoteCasted = mysqli_query($db, "SELECT * FROM votings WHERE voters_id='" . $_SESSION['user_id'] . "' AND election_id='" . $election_id . "'") or die(mysqli_error($db));
                        if (mysqli_num_rows($checkIfVoteCasted) > 0) {
                            $voteData = mysqli_fetch_assoc($checkIfVoteCasted);
                            $hasVoted = true;
                            $votedCandidateId = $voteData['candidate_id'];
                        }

                        while ($candidateData = mysqli_fetch_assoc($fetchingCandidates)) {
                            $candidate_id = $candidateData['id'];
                            $candidate_photo = $candidateData['candidate_photo'];

                            $fetchingVotes = mysqli_query($db, "SELECT * FROM votings WHERE candidate_id = '$candidate_id'") or die(mysqli_error($db));
                            $totalVotes = mysqli_num_rows($fetchingVotes);
                            ?>
                            <tr>
                                <td>
                                    <img src="<?php echo $candidate_photo; ?>" style="width: 80px; height: 80px; border: 3px solid rgb(6, 184, 6); border-radius: 100%;">
                                </td>
                                <td><?php echo "<b>" . $candidateData['candidate_name'] . "</b><br>" . $candidateData['candidate_details']; ?></td>
                                <td><?php echo $totalVotes; ?></td>
                                <td>
                                    <?php
                                    if ($hasVoted) {
                                        if ($votedCandidateId == $candidate_id) {
                                            echo '<button class="btn btn-md btn-success">Voted</button>';
                                        } else {
                                            echo '<button class="btn btn-md btn-secondary" disabled>Vote</button>';
                                        }
                                    } else {
                                        ?>
                                        <button class="btn btn-md btn-success" onclick="CastVote(<?php echo $election_id; ?>, <?php echo $candidate_id; ?>, <?php echo $_SESSION['user_id']; ?>)">Vote</button>
                                        <?php
                                    }
                                    ?>
                                </td>
                            </tr>
                            <?php
                        }
                        ?>
                    </tbody>
                </table>
                <?php
            }
        } else {
            echo "There is no active election";
        }
        ?>
    </div>
</div>

<script>
const CastVote = (election_id, candidate_id, voters_id) => {
    $.ajax({
        type: "POST",
        url: "inc/ajaxCalls.php",
        data: "e_id=" + election_id + "&c_id=" + candidate_id + "&v_id=" + voters_id,
        success: function(response) {
            if (response.trim() === "Success") {
                location.assign("index.php?voteCasted=1");
            } else {
                location.assign("index.php?voteNotCasted=1");
            }
        }
    });
}
</script>

<?php
require_once("inc/footer.php");
?>
