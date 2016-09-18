<?php
    session_start();
    loadGroups();
    
    if($_POST['action'] === 'joinGroup') {
        joinGroup();
    }
    
    function loadGroups() {
        require('config.php');
        $sql  = "SELECT group_id FROM Groups;";
        $result = mysql_query($sql, $link);
        global $groups;
        $groups = array();
        while ($row = mysql_fetch_assoc($result)) {
            // Append all rows to an array
            array_push($groups, $row['group_id']);
        }
        mysql_close($link);
    }
    
    function joinGroup() {
        require('config.php');
        $sql = "UPDATE Users SET group_id = '".$_POST['group']."' WHERE user_id = '".$_SESSION['user_id']."';";
        if(mysql_query($sql, $link)){
            $_SESSION['group_id']=$_POST['group'];
            mysql_close($link);
        }
    }
?>

<script>
    function joinGroup(id) {
      $.ajax({
           type: "POST",
           url: 'course.php',
           dataType: 'text',
           data:{action:'joinGroup', group:id},
	   success: function(data) {
	   	window.location="https://poc353_1.encs.concordia.ca/group.php";
	   }
      });
    }
	
	function viewGroup(id) 
	{
		$.ajax({
           type: "POST",
           url: 'course.php',
           dataType: 'text',
           data:{action:'viewGroup', group:id},
	   success: function(data) {
	   	window.location="https://poc353_1.encs.concordia.ca/group.php?group=" +id;
	   }
	   });	
	}
	
	function manageGroup(id) 
	{
		$.ajax({
           type: "POST",
           url: 'course.php',
           dataType: 'text',
           data:{action:'viewGroup', group:id},
	   success: function(data) {
	   	window.location="https://poc353_1.encs.concordia.ca/manage.php?group=" +id;
	   }
	   });	
	}
	
	function addGroup()
	{
	
	}
	
	function addAssignment()
	{
	
	}
	
	
</script>

<html>
    <head>
        <script type="text/javascript" src="https://ajax.googleapis.com/ajax/libs/jquery/1.12.4/jquery.min.js"></script>
    </head>    
    <body>
        <section id="course">
            <h2> Course:</h2>
            <table>
                <?php foreach($groups as $group): ?>
                <tr>
                    <td>Group <?php echo $group; ?></td>
                        <?php if($_SESSION['user_type'] === '4') 
							{
                            echo "<td><button onclick='joinGroup(";
                            echo $group;
                            echo ")'>Join</button></td>";
							}
							
							if($_SESSION['user_type'] === '2') 
							{
                            echo "<td><button onclick='viewGroup(";
                            echo $group;
                            echo ")'>View</button></td>";
							}
							
							if($_SESSION['user_type'] === '2') 
							{
                            echo "<td><button onclick='manageGroup(";
                            echo $group;
                            echo ")'>Manage</button></td>";
							}
                        ?>
		<?php endforeach; ?>
            </table>
			<br>
			<?php if ($_SESSION['user_type'] === '2')
			{
				echo "<td><button onclick='addGroup(";
                echo $group;
                echo ")'>Add Group</button></td>";
				echo "     ";
				echo "<td><button onclick='addAssignment(";
                echo $group;
                echo ")'>Add Assignment</button></td>";
			}
		?>
        <section>
    </body>
</html>
