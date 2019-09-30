<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css">
<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.4.1/jquery.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.7/umd/popper.min.js"></script>
<script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.3.1/js/bootstrap.min.js"></script>
<div>
<style>
    .padding-list{
        padding: 10px;
    }
    .list-active{
        background: #c8c8c8;
    }
</style>
<h2>History</h2>
    <div style="text-align: right;margin-right: 20px">
        <!--        ../wp-content/plugins/CheckLogin/exportExcel.php-->
        <form action="admin.php?page=checkLogin&action=download_csv" method="post">
            <input name="checkLogs" value="<?php echo $user?>"  hidden>
            <input type="submit" name="export_excel" class="btn btn-success" value="EXPORT EXCEL">
        </form>
    </div>
    <br>
<div>
<table class="table table-striped">
    <thead>
    <tr>
        <th>ID</th>
        <th>User Name</th>
        <th>Action</th>
        <th>Time</th>
    </tr>
    </thead>
    <tbody>
    <?php global $wpdb;
    $table_check_logs =$wpdb->prefix ."check_logs";
    $table_user=$wpdb->prefix ."users";
    $table_posts=$wpdb->prefix ."posts";

    $result = $wpdb->get_var("SELECT count(id) FROM $table_check_logs Where user_action!='';");
    $total_records=$result;
    $current_page = isset($_GET['id']) ? $_GET['id'] : 1;
    $limit = 10;
    $total_page = ceil($total_records / $limit);
    // Giới hạn current_page trong khoảng 1 đến total_page
    if ($current_page > $total_page){
        $current_page = $total_page;
    }
    else if ($current_page < 1){
        $current_page = 1;
    }
    // Tìm Start
    $start = ($current_page - 1) * $limit;
    $user = $wpdb->get_results("SELECT * FROM $table_check_logs 
                INNER JOIN $table_user ON $table_user.ID = $table_check_logs.id_user  
                WHERE user_action != '' ORDER BY time_logs DESC
                                 LIMIT $start, $limit
                                ");
    foreach($user as $item)
    {
        if($item->id_post!=null)
        {
            $post=$wpdb->get_results("SELECT * FROM $table_posts WHERE ID=$item->id_post");
            foreach ($post as $item1)
            {
                $post_type=$item1->post_type;
                $post_title=$item1->post_title;
            }
        }
        else{
            $post_type="";
            $post_title="";
        }
        ?>
        <tr>
            <td><?php echo $item->id; ?></td>
            <td><?php echo $item->user_nicename; ?></td>
            <?php if ($item->user_action=="deleted") {?>
                <td><?php echo " have " .$item->user_action." a <span style='color: red'>".$post_type." </span> : ".$post_title ." ( 
Do you want to restore it?"; ?> <a href="admin.php?page=restorePost&id=<?php echo $item->id_post?>">Restore</a>)</td>
            <?php }elseif($item->id_post==null){?>
            <td><?php echo $item->user_action; ?></td>
            <?php }else{?>
            <td><?php echo " have " . $item->user_action."  a  <span style='color: red'>".$post_type." </span>:  " .$post_title; ?></td>
            <?php }?>
            <td><?php echo $item->time_logs; ?></td>
        </tr>
    <?php
    }
    ?>
    </tbody>
</table>

    </div>

<div  style="text-align: center">
    <?php
    // PHẦN HIỂN THỊ PHÂN TRANG
    // BƯỚC 7: HIỂN THỊ PHÂN TRANG

    // nếu current_page > 1 và total_page > 1 mới hiển thị nút prev
    if ($current_page > 1 && $total_page > 1){
        echo '<a class="padding-list"  href="admin.php?page=checkLogin&pid='.($current_page-1).'"><< </a>';
    }
    // Lặp khoảng giữa
    for ($i = 1; $i <= $total_page; $i++){
        // Nếu là trang hiện tại thì hiển thị thẻ span
        // ngược lại hiển thị thẻ a
        if ($i == $current_page){
            echo '<a style="color: red" class="padding-list "><span>'.$i.'</span> </a> ';
        }
        else{
            echo '<a class="padding-list" href="admin.php?page=checkLogin&id='.$i.'">'.$i.'</a>';
        }
    }

    // nếu current_page < $total_page và total_page > 1 mới hiển thị nút prev
    if ($current_page < $total_page && $total_page > 1){
        echo '<a class="padding-list" href="admin.php?page=checkLogin&id='.($current_page+1).'">>></a> ';
    }
    ?>
</div>
