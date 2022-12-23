<?php
    $search   = array('-', '/', '_');
    $replace    = array(' ', ' ', ' ');
    ?>

<section>
    <div class="container">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="<?= base_url('webapp/'); ?>">Homepage</a></li>
            <?php
                    $last_segment = "";
    $foreach_count = 1;
    foreach ($segments as $segment) {
        if ($foreach_count >= $count) {
            echo "<li class='breadcrumb-item active'>" . str_replace($search, $replace, $segment) . "</li>";
        } else {
            $last_segment = $last_segment . '/' . $segment;
            echo "<li class='breadcrumb-item'><a href='" . base_url("webapp/" . $last_segment) . "'>" . str_replace($search, $replace, $segment) . "</a></li>";
        }
        $foreach_count += 1;
    }
    ?>
        </ol>
    </div>
</section>