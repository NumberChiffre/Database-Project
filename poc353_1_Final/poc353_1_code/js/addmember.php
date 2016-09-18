<?php
     
    require 'database.php';
    $ids = 0;
    if ( !empty($_GET['id'])) {
        $ids = $_REQUEST['id'];
    }
 
    if ( !empty($_POST)) {
        // keep track validation errors
        $fnameError = null;
        $lnameError = null;
        $unameError = null;
        $passError = null;
         
        // keep track post values
        $fname = $_POST['fname'];
        $lname = $_POST['lname'];
        $uname = $_POST['uname'];
        $pass = $_POST['pass'];
        $id = $_POST['id'];
         
        // validate input
        $valid = true;
        if (empty($fname)) {
            $fnameError = 'Please enter First Name';
            $valid = false;
        }
        if (empty($lname)) {
            $lnameError = 'Please enter Last Name';
            $valid = false;
        }
        if (empty($uname)) {
            $unameError = 'Please enter User Name';
            $valid = false;
        }
        if (empty($pass)) {
            $passError = 'Please enter Password';
            $valid = false;
        }
         
        // insert data
        if ($valid) {
            var_dump($group_id);
            $pdo = Database::connect();
            $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            $sql = "INSERT INTO Users (user_id,firstname,last,username,password,user_type,grade,group_id,lastlogin) values(null,?, ?, ?, ?, 4, null, ?,null)";
            $q = $pdo->prepare($sql);
            $q->execute(array($fname, $lname, $uname, $pass, $id));
            Database::disconnect();
			header("Location: groupedit.php?id=$id");
        }
    }
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" href="http://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/css/bootstrap.min.css">
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.12.4/jquery.min.js"></script>
    <script src="http://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/js/bootstrap.min.js"></script>
</head>
 
<body>
    <div class="container">
     
                <div class="span10 offset1">
                    <div class="row">
                        <h3>Create a User</h3>
                    </div>
             
                    <form class="form-horizontal" action="addmember.php" method="post">
                        <input type="hidden" name="id" value="<?php echo $ids ;?>">
                      <div class="control-group <?php echo !empty($fnameError)?'error':'';?>">
                        <label class="control-label">First Name</label>
                        <div class="controls">
                            <input name="fname" type="text"  placeholder="First Name" value="<?php echo !empty($fname)?$fname:'';?>">
                            <?php if (!empty($fnameError)): ?>
                                <span class="help-inline"><?php echo $fnameError;?></span>
                            <?php endif; ?>
                        </div>
                      </div>
                      <div class="control-group <?php echo !empty($lnameError)?'error':'';?>">
                        <label class="control-label">Last Name</label>
                        <div class="controls">
                            <input name="lname" type="text"  placeholder="Last Name" value="<?php echo !empty($lname)?$lname:'';?>">
                            <?php if (!empty($lnameError)): ?>
                                <span class="help-inline"><?php echo $lnameError;?></span>
                            <?php endif; ?>
                        </div>
                      </div>
                      <div class="control-group <?php echo !empty($unameError)?'error':'';?>">
                        <label class="control-label">UserName</label>
                        <div class="controls">
                            <input name="uname" type="text"  placeholder="USerName" value="<?php echo !empty($uname)?$uname:'';?>">
                            <?php if (!empty($unameError)): ?>
                                <span class="help-inline"><?php echo $unameError;?></span>
                            <?php endif; ?>
                        </div>
                      </div>
                      <div class="control-group <?php echo !empty($passError)?'error':'';?>">
                        <label class="control-label">Password</label>
                        <div class="controls">
                            <input name="pass" type="password"  placeholder="Password" value="<?php echo !empty($pass)?$pass:'';?>">
                            <?php if (!empty($passError)): ?>
                                <span class="help-inline"><?php echo $passError;?></span>
                            <?php endif;?>
                        </div>
                      </div>
                      <div class="form-actions">
                          <button type="submit" class="btn btn-success">Create</button>
                          <a class="btn" href="groupadmin.php">Back</a>
                        </div>
                    </form>
                </div>
                 
    </div> <!-- /container -->
  </body>
</html>