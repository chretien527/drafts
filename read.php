<?php
include 'connections.php';
$sql = "select * from users";
$result = $conn->query($sql);
?>

<html>
<head>
<title>View users data</title>
</head>
<body>
<h2>Users</h2>
<table>
<thead>
<tr>
<th>ID</th>
<th>firstname</th>
<th>lastname</th>
<th>email</th>
<th>gender</th>
<th>action</th>
</tr>
</thead>
<tbody>
<?php
if($result->num_rows>0){
    while($row = $result->fetch_assoc()){
        ?>
        <tr>
            <td><?php echo $rows['id']; ?></td>
            <td><?php echo $rows['fname']; ?></td>
            <td><?php echo $rows['lname']; ?></td>
            <td><?php echo $rows['email']; ?></td>
            <td><?php echo $rows['gender']; ?></td>
            <td><a class ="btn btn-info" href="update.php?id=<? echo $rows['id']; ?>">Edit</a>
        &nbsp
            <a class="btn btn-danger" href="delete.php?id=<?php echo $rows['id']; ?>">Delete</a>
    </td>
        </tr>
        <?php
    }
}
?>

</tbody>
</table>
</body>
</html>