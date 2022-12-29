<div class='sortable-job' job_id='<?php echo $job_id; ?>' job_name = '<?php echo $job_name; ?>' job_location = '<?php echo $job_location; ?>'>

<div class="job-name"><?php echo $job_name; ?> | Slots: <?php  echo $job_slots; ?> | Time 
<?php

    $timeslot_length = 3600;

$effective_time = ($timeslot_length * $job_slots);


$h = floor($effective_time / 3600);
$m = floor(($effective_time % 3600) / 60);

if ($m == 0) {
    echo $h . " Hour(s)";
} else {
    echo $h . "Hour(s) ". $m . " Minute(s) ";
}

?>

</div>
<div class="job-description"><?php echo $job_description; ?></div>
<div class="job-location"><?php echo $job_location; ?></div>
</div>



