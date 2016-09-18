<?php
    require 'database.php';
    $id = null;
    if ( !empty($_GET['id'])) {
        $id = $_REQUEST['id'];
    }
     
    if ( null==$id ) {
        header("Location: admin.php");
    } else {
        $pdo = Database::connect();
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        
        $sql = "SELECT * FROM Users where group_id = ?";
        $q = $pdo->prepare($sql);
        $q->execute(array($id));
        $user_data = $q->fetchALL(PDO::FETCH_ASSOC);

        $sql = "SELECT f.assignment_id, fv.name, fv.upload_date  FROM Files f, File_versions fv where f.file_id = fv.file_id and group_id = ?";
        $q = $pdo->prepare($sql);
        $q->execute(array($id));
        $group_data = $q->fetchALL(PDO::FETCH_ASSOC);

        Database::disconnect();
    }
?>
 
<!DOCTYPE html>
<html lang="en">
    <head>
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <link rel="stylesheet" href="bootstrap.min.css">
        <script src="bootstrap.min.js"></script>
    </head>
 
    <body>
        <div class="container">
            <div class="row">
                <h3>Assignment Details</h3>
                </div>
            <div class="row">
                <table class="table table-striped table-bordered">
                  <thead>
                    <tr>
                      <th>Assignment #</th>
                      <th>Name</th>
                      <th>Upload Date</th>
                    </tr>
                  </thead>
                  <tbody>
                  <?php
                   foreach ($group_data as $row) {
                            echo '<tr>';
                            echo '<td>'. $row['assignment_id'] . '</td>';
                            echo '<td>'. $row['name'] . '</td>';
                            echo '<td>'. $row['upload_date'] . '</td>';
                            echo '</tr>';
                   }
                  ?>
                  </tbody>
                </table>
            </div>
            <div class="row">
                <h3>Group Members</h3>
            </div>
            <div class="row">
                <table class="table table-striped table-bordered">
                  <thead>
                    <tr>
                      <th>User Id</th>
                      <th>Name</th>
                      <th>User Name</th>
                      <th>User Type</th>
                    </tr>
                  </thead>
                  <tbody>
                  <?php
                   foreach ($user_data as $row) {
                            echo '<tr>';
                            echo '<td>'. $row['user_id'] . '</td>';
                            echo '<td>'. $row['firstname'] . '</td>';
                            echo '<td>'. $row['username'] . '</td>';
                            if($row['user_type'] == 2) {
                              echo '<td>'. 'Teacher' . '</td>';
                            } else {
                               echo '<td>'. 'Student' . '</td>';
                            }
                   }
                  ?>
                  </tbody>
                </table>
            </div>
            <div class="row">
            <a class="btn" href="groupadmin.php">Back</a>
            </div>
            

        </div>
    
    </body>
</html>