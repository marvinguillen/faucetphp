<?php require_once "header.php";

if( isset($_GET['a']) && $_GET['a']=='delete' ){

	$cod_coupons=$_GET['cod_coupons'];
  

	 $result=$db->query("delete from tbl_creator_coupons_details where cod_coupons='$cod_coupons'");
  
 //firt test

	header('Location:newcoupon.php');	

}elseif( isset($_POST['add']) ){
  

  $result=$db->mysqli->prepare("select username from tbl_admin where admin_id= '$aid'");
    
    $result->execute();
    $result->bind_result($username);

  $result->fetch();

  $result->close();
    
    $codecoupons= $_POST['codecoupons'];
    $valueticket =$_POST['valueticket'];
	$db->Query(" insert into tbl_creator_coupons_details (cod_coupons,value_coupons_sc,usercreator,coded_used) 
  values ('$codecoupons','$valueticket','$username',0) ");

	header('Location:newcoupon.php');	

}else{

?>

<section id="main-content">

	<section class="wrapper">



		<section class="panel">

			<div class="panel-heading">

				Tickets Administration 

			</div>

			

            <table width="100%" class="table table-hover personal-task">



            

    <tr>

    <th width="2%">#</th>

    	<th width="36%">Code Coupons</th>
      <th width="15%">User Creator</th>
      <th width="25%">Value SuperCoins</th>
      <th width="12%">Action</th>

    </tr>





<?php

$db->query("select * from  tbl_creator_coupons_details");

while($res=$db->fetcharray()){

?>



    <tr>

    <td>

    <?php echo ++$i; ?>

      	

    

    </td>

    	<td width="36%"><?php echo $res['cod_coupons']; ?></td>
      <td width="15%"><?php echo $res['usercreator'];?> 
      </td>
      <td width="25%"><?php echo $res['value_coupons_sc'];?> 
      </td>

      <td width="12%"><a href="?a=delete&cod_coupons=<?php echo $res['cod_coupons'];?>" class="btn btn-danger btn-xs" onClick="return conf_delete()"><span class="glyphicon glyphicon-remove"></span></a></td>

    </tr>







<?php } ?>









</table>

   

   

<div class="panel-body">    

    

    <button type="button" class="btn btn-primary" data-toggle="modal" data-target="#myModal">

  New

</button>

   

</div>  

   

   

   <!-- Modal -->

<div class="modal fade" id="myModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">

  <div class="modal-dialog">

  <form action="" method="post">

    <div class="modal-content">

      <div class="modal-header">

        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>

        <h4 class="modal-title" id="myModalLabel">Add a new Coupons</h4>

      </div>

      <div class="modal-body">

       

  

	<div class="form-group">

		<label for="exampleInputEmail1">Code Coupons</label>

		<input type="text" name="codecoupons" class="form-control" placeholder="">

	</div>

  

	<div class="form-group">

		<label for="exampleInputEmail1">Value</label>

		<input type="text" name="valueticket" class="form-control" placeholder="">

	</div>

   
    

       

       

       

      </div>

      <div class="modal-footer">

        <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>

        <button type="submit" name="add" class="btn btn-primary">Add</button>

      </div>

    </div>

    </form>

    

  </div>

</div>

   

   

   

   

    

    

            

		</section>

        

	</section>

</section>

<?php }require_once "footer.php"; ?>