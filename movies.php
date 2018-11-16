<!DOCTYPE html>
<!--
	Movie review website, similar to Rotten Tomatoes, switches movies with PHP
	movies.php is styled by movies.css
-->

<?php
	# retrieve the target movie passed in from user
	if(!isset($_GET["movie"])) {
		$movie_filename = "princessbride"; # default/placeholder
	} else {
		$movie_filename = $_GET["movie"];
	}

	$movie_options = array("princessbride"=>"The Princess Bride",
												 "tmnt"=>"TMNT",
												 "tmnt2"=>"Teenage Mutant Ninja Turtles II",
												 "mortalkombat"=>"Mortal Kombat",
											 	 "fightclub"=>"Fight Club");

	# setup info variables based on movie
	$info_filename = "moviefiles/" . $movie_filename . "/info.txt";
	$info = file($info_filename, FILE_IGNORE_NEW_LINES);
	$movie_title = $info[0];
	$movie_year = $info[1];

	# setup review files
	$review_files = glob("moviefiles/$movie_filename/review*");
	$num = count($review_files);
	$total = 0.0;
	foreach ($review_files as $review_file) {
		$review = file($review_file, FILE_IGNORE_NEW_LINES);
		$review_rank = $review[1]; # second line, rank (1-4)
		$review_rank = trim($review_rank);
		$total += $review_rank;
	}
	$movie_ranking = round((float)($total / $num), 1);

	# for top and bottom page banners
	function show_banner() { ?>
		<div class="banner">
			<h1>Rancid Tomatoes</h1>
		</div> <?php
	}

?>

<html lang="en">
	<head>
		<title>Rancid Tomatoes</title>
		<meta charset="utf-8" />
		<link href="tomayto.css" type="text/css" rel="stylesheet" />
	</head>

	<body>
		<?php show_banner(); ?>
		<p id="movietitle"><?=$movie_title?> (<?=$movie_year?>)</p>

		<div id="main">
			<div id="generaloverview">
				<img src="moviefiles/<?=$movie_filename?>/overview.png" alt="movie poster"/>
				<div id="generaltext">
					<dl>
						<?php # display overview.txt data into general overview
						$overview_filename = "moviefiles/" . $movie_filename . "/overview.txt";
						$overview = file($overview_filename, FILE_IGNORE_NEW_LINES);
						foreach ($overview as $line) {
							$line_data = explode(":", $line); ?>
							<dt><?= $line_data[0]; ?></dt>
							<dd><?= $line_data[1]; ?></dd> <?php
						}
						?>
					</dl>
				</div>
			</div> <!-- end #generaloverview -->

			<div id="reviewsmain">
				<div id="ratingbar">
					<p>
						<img src="images/tomato.png" alt="tomato" />
						<?=$movie_ranking?>
						<form method="get" action="movies.php">
							<select name="movie">
								<?php # display drop down list and properly show 'selected'
								foreach($movie_options as $opt => $opt_full) {
									?>
									<option value="<?=$opt?>"<?php
									if ($movie_filename==$opt) echo ' selected="selected"';
									?>><?=$opt_full?></option>
									<?php
								} ?>
								<input type="submit" name="submit" />
							</select>
						</form>
					</p>
				</div>

				<?php # display individual reviews

				# handler function to setup review columns
				function show_all_reviews($all_files) {
					$midpoint = ceil(count($all_files) / 2);
					show_review_column($all_files, 0, $midpoint);
					show_review_column($all_files, $midpoint, count($all_files));
				}

				# shows one column of reviews (half of total available)
				function show_review_column($files, $start_index, $end_index) { ?>
					<div class="reviewcolumn">
					<?php
					for ($i = $start_index; $i < $end_index; $i++) {
						# store contents of each line of each file
						list($rev_text, $rev_rank, $rev_auth, $rev_org) = file($files[$i]); ?>
						<div class = "review">
							<p>
								<?php # display tomatoes reprenting reviewer's score
							 	for ($j = 0; $j < $rev_rank; $j++) { ?>
									<img src="images/tomatosmall.png" alt="tomatosmall"/>
								<?php
								} ?>
								<?= $rev_text ?>
							</p>
							<p>
								<?= $rev_auth ?>
							</p>
							<p>
								<?= $rev_org ?>
							</p>
						</div>
					<?php
					}
					?>
					</div>
				<?php
				}

				show_all_reviews($review_files);

				?>
			</div> <!-- end #reviewmain -->
		</div> <!-- end #main -->
		<?php show_banner(); ?>
	</body>
</html>
