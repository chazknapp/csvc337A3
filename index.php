<?php
// Ensure "film" query parameter is provided
if (!isset($_REQUEST["film"])) {
    die("Error: No film specified. Please use a URL like ?film=fightclub");
}

$movie = $_REQUEST["film"];
$dir = "provided/$movie/";  // Adjusted to the correct folder

// Check if the movie directory exists
if (!is_dir($dir)) {
    die("Error: Movie '$movie' not found.");
}

// Read info.txt
$infoFile = "$dir/info.txt";
if (!file_exists($infoFile)) {
    die("Error: Missing info.txt for movie '$movie'.");
}

$info = file($infoFile, FILE_IGNORE_NEW_LINES);
if (count($info) < 4) {
    die("Error: Invalid info.txt format.");
}

$title = $info[0];
$year = $info[1];
$rating = (int)$info[2];
$review_count = (int)$info[3];

// Determine rating image
$ratingImage = ($rating >= 60) ? "provided/freshbig.png" : "provided/rottenbig.png";

// Read overview.txt
$overviewFile = "$dir/overview.txt";
if (!file_exists($overviewFile)) {
    die("Error: Missing overview.txt for movie '$movie'.");
}

$overview = file($overviewFile, FILE_IGNORE_NEW_LINES);

// Fetch reviews
$reviewFiles = glob("$dir/review*.txt");
$totalReviews = count($reviewFiles);
$leftReviews = array_slice($reviewFiles, 0, floor($totalReviews / 2));
$rightReviews = array_slice($reviewFiles, floor($totalReviews / 2));
?>



<!DOCTYPE html>
<html>
<head>
    <title><?= $title ?> (<?= $year ?>) - Rancid Tomatoes</title>
    <meta charset="utf-8">
    <link rel="stylesheet" href="movie.css">
    <link rel="icon" type="image/png" href="provided/rotten.gif" />
</head>
<body>

<div id="banner">
    <img src="provided/banner.png" alt="Rancid Tomatoes">
</div>

<h1><?= $title ?> (<?= $year ?>)</h1>

<div id="content">
    <div id="reviews">
        <div id="rating-section">
            <img src="<?= $ratingImage ?>" alt="Rating">
            <span class="rating-score"><?= $rating ?>%</span>
            <span class="review-count">(<?= $review_count ?> reviews total)</span>
        </div>
        
        <br><br>
        
        <div id="review-container">
            <div class="review-column left">
                <?php foreach ($leftReviews as $reviewFile) {
                    $reviewData = file($reviewFile, FILE_IGNORE_NEW_LINES);
                    $reviewText = $reviewData[0];
                    $reviewRating = strtolower($reviewData[1]) == "fresh" ? "provided/fresh.gif" : "provided/rotten.gif";
                    $reviewer = $reviewData[2];
                    $publication = $reviewData[3];
                ?>
                <div class="review-box">
                    <img src="<?= $reviewRating ?>" alt="Review">
                    <q><strong><?= $reviewText ?></strong></q>
                </div>
                <p class="reviewer">
                    <img src="provided/critic.gif" alt="Critic">
                    <span><strong><?= $reviewer ?></strong><br><em><?= $publication ?></em></span>
                </p>
                <?php } ?>
            </div>

            <div class="review-column right">
                <?php foreach ($rightReviews as $reviewFile) {
                    $reviewData = file($reviewFile, FILE_IGNORE_NEW_LINES);
                    $reviewText = $reviewData[0];
                    $reviewRating = strtolower($reviewData[1]) == "fresh" ? "provided/fresh.gif" : "provided/rotten.gif";
                    $reviewer = $reviewData[2];
                    $publication = $reviewData[3];
                ?>
                <div class="review-box">
                    <img src="<?= $reviewRating ?>" alt="Review">
                    <q><strong><?= $reviewText ?></strong></q>
                </div>
                <p class="reviewer">
                    <img src="provided/critic.gif" alt="Critic">
                    <span><strong><?= $reviewer ?></strong><br><em><?= $publication ?></em></span>
                </p>
                <?php } ?>
            </div>
        </div>
    </div>

    <div id="overview">
        <img src="<?= $dir ?>/overview.png" alt="General Overview">
        <dl>
            <?php foreach ($overview as $line) {
                list($key, $value) = explode(":", $line, 2);
            ?>
            <dt><strong><?= $key ?></strong></dt>
            <dd><?= $value ?></dd><br>
            <?php } ?>
        </dl>
    </div>
</div>

<p id="reviewNumBot">(<?= min(10, $totalReviews) ?> of <?= $review_count ?>)</p>

</body>
</html>
