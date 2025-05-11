<?php
require_once("inc/header.php");
require_once("inc/navigation.php");
require_once("../admin/inc/config.php");

if (!isset($_GET['viewResult']) || empty($_GET['viewResult'])) {
    echo "<div class='alert alert-danger'>Invalid Election ID.</div>";
    exit;
}

$election_id = $_GET['viewResult'];
?>

<div class="row my-3">
    <div class="col-12">
        <h2>Election Result</h2>

        <?php
        $fetchingElection = mysqli_query($db, "SELECT * FROM elections WHERE id = '$election_id'") or die(mysqli_error($db));
        if (mysqli_num_rows($fetchingElection) > 0) {
            $electionData = mysqli_fetch_assoc($fetchingElection);
            $election_topic = $electionData['election_topic'];

            // Find winner
            $winnerQuery = mysqli_query($db,
                "SELECT cd.candidate_name, COUNT(v.id) AS total_votes
                FROM candidate_details cd
                LEFT JOIN votings v ON cd.id = v.candidate_id
                WHERE cd.election_id = '$election_id'
                GROUP BY cd.id
                ORDER BY total_votes DESC
                LIMIT 1"
            ) or die(mysqli_error($db));

            $winner = mysqli_fetch_assoc($winnerQuery);
            $winner_name = $winner['candidate_name'];
            $winner_votes = $winner['total_votes'];
            ?>

            <div class="alert alert-success">
                <strong>Winner:</strong> <?php echo $winner_name; ?> <br>
                <strong>Total Votes:</strong> <?php echo $winner_votes; ?>
            </div>

            <table class="table">
                <thead>
                    <tr>
                        <th colspan="3" class="bg-green">
                            <h5>Election Name: <?php echo $election_topic; ?></h5>
                        </th>
                    </tr>
                    <tr>
                        <th>Photo</th>
                        <th>Candidate Details</th>
                        <th>Number of Votes</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    $fetchingCandidates = mysqli_query($db, "SELECT * FROM candidate_details WHERE election_id = '$election_id'") or die(mysqli_error($db));
                    while ($candidate = mysqli_fetch_assoc($fetchingCandidates)) {
                        $candidate_id = $candidate['id'];
                        $candidate_photo = $candidate['candidate_photo'];
                        $candidate_name = $candidate['candidate_name'];
                        $candidate_details = $candidate['candidate_details'];

                        $votesQuery = mysqli_query($db, "SELECT COUNT(*) AS total FROM votings WHERE candidate_id = '$candidate_id'") or die(mysqli_error($db));
                        $voteCountData = mysqli_fetch_assoc($votesQuery);
                        $totalVotes = $voteCountData['total'];
                        ?>
                        <tr>
                            <td><img src="<?php echo $candidate_photo; ?>" style="width: 80px; height: 80px; border: 3px solid rgb(6, 184, 6); border-radius: 100%;"></td>
                            <td><b><?php echo $candidate_name; ?></b><br><?php echo $candidate_details; ?></td>
                            <td><?php echo $totalVotes; ?></td>
                        </tr>
                        <?php
                    }
                    ?>
                </tbody>
            </table>

            <?php
        } else {
            echo "<div class='alert alert-warning'>No election found for the given ID.</div>";
        }
        ?>
    </div>
</div>

<?php
require_once("inc/footer.php");
?>
