<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <link   href="bootstrap.min.css" rel="stylesheet">
    <script src="bootstrap.min.js"></script>
</head>
<body>
    <div class="container">
            <div class="row">
                <h2>Teacher's Page</h2>
            </div>
            <div class="row">
              <div class="panel panel-default">
                <div class="panel-body">
                  <h4>Groups</h4>
                  <span><a href="groupadd.php" class="btn btn-default">Add Group</a></span>
                  <span><a href="create.php" class="btn btn-default">Add Assignment</a></span>
                  
                  <br/>
                  <table class="table table-hover">
                    <thead>
                      <tr>
                        <th width=50>Group Number</th>
                        <th width=250>Operations</th>
                      </tr>
                    </thead>
                    <tbody>
                      <?php
                       include 'database.php';
                       $pdo = Database::connect();
                       $sql = 'SELECT * FROM Groups ORDER BY group_id DESC';
                       foreach ($pdo->query($sql) as $row) {
                                echo '<tr>';
                                echo '<td>'. $row['group_id'] . '</td>';
                               echo '<td width=250>';
                                    echo '<a class="btn btn-default" href="display.php?id='.$row['group_id'].'">Display</a>';
                                    echo ' ';
                                    echo '<a class="btn btn-default" href="groupedit.php?id='.$row['group_id'].'">Edit</a>';
                                    echo ' ';
                                    echo '<a class="btn btn-default" href="groupdelete.php?id='.$row['group_id'].'">Delete</a>';
                                    echo ' ';
                                    echo '</td>';
                                echo '</tr>';
                       }
                       Database::disconnect();
                  ?>
                    </tbody>
                  </table>
                </div><!-- /panel body -->
              </div><!-- /panel -->
            </div>
    </div> <!-- /container -->
  </body>
</html>