<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css">
<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.4.1/jquery.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.7/umd/popper.min.js"></script>
<script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.3.1/js/bootstrap.min.js"></script>
<h2>History</h2>

<table class="table table-striped">
    <thead>
    <tr>
        <th>ID</th>
        <th>User Name</th>
        <th>IP Address</th>
        <th>Role</th>
        <th>Login Time</th>
        <th>Logout Time</th>
    </tr>
    </thead>
    <tbody>
    <?php global $wpdb;
    $timeLogout = time();
    $table_check_login =$wpdb->prefix ."check_login";
    $user = $wpdb->get_results("SELECT * FROM $table_check_login ORDER BY timeLogout DESC");
    foreach($user as $item)
    {
        ?>
        <tr>
            <td><?php echo $item->id; ?></td>
            <td><?php echo $item->username; ?></td>
            <td><?php echo $item->ip; ?></td>
            <td><?php echo $item->roles; ?></td>
            <td><?php echo $item->timeLogin; ?></td>
            <td><?php echo $item->timeLogout; ?></td>
        </tr>
    <?php
    }
    ?>
    </tbody>
</table>
