<?php
require_once '../config/connection.php';
include_once '../api/queue_api.php';

$sort = isset($_GET['sort']) ? $_GET['sort'] : 'none';

// Fetch sorted offices based on the traffic
$result = getOffices($sort);

while ($row = $result->fetch_assoc()) { ?>
    <div class="office-entry" id="office-id-<?php echo $row['office_id']; ?>">
        <div class="title-grid">
            <p class="title"><?php echo $row['office_name']; ?></p>
        </div>
        <div class="line-separator"></div>
        <p><?php echo $row['office_description']; ?></p>
        <div class="queue-statistics">
            <div class="currently-in-line">
                <p class="current-stat"><?php echo $row['queue_count'] ?? 0; ?></p>
                <p>Currently in line</p>
            </div>
            <div class="currently-in-line">
                <p class="current-stat wait-time">--</p>
                <p>Average wait time</p>
            </div>
        </div>
        <button name="control-button" class="join-queue-btn" type="button" value="<?php echo $row['office_id']; ?>">Join queue</button>
    </div>
<?php } ?>