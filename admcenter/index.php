<?phprequire_once "header.php";require_once "../includes/AsmoneyAPI.php";?><section id="main-content">	<section class="wrapper">              <div class="row state-overview">                  <div class="col-md-3" >                      <section class="panel" data-step="1" data-intro="This is your calculated account balance including all of your currencies in sum" data-position="right">                          <div class="symbol blue"><i class="fa fa-user"></i></div>                          <div class="value">                              <h1><?php                              $db->queryres("select count(user_id) as ucount from tbl_user");							  echo number_format($db->res['ucount']);							  ?></h1>                              <p>Total User</p>                          </div>                      </section>                  </div>                                                      <div class="col-md-3" >                      <section class="panel" data-step="1" data-intro="This is your calculated account balance including all of your currencies in sum" data-position="right">                          <div class="symbol yellow"><i class="fa fa-btc"></i></div>                          <div class="value">                              <h1><?php                              $db->queryres("select sum(amount) as amsum from tbl_withdrawal where status=1 and type=1");							  echo number_format($db->res['amsum']);							  ?></h1>                              <p>Total Paid in  SUP coins                                                                  </p>                          </div>                      </section>                  </div>                                                      <div class="col-md-3" >                      <section class="panel" data-step="1" data-intro="This is your calculated account balance including all of your currencies in sum" data-position="right">                          <div class="symbol red"><i class="fa fa-credit-card"></i></div>                          <div class="value">                              <h1><?php                              $db->queryres("select sum(amount) as amsum from tbl_donate where status!=0");							  echo number_format($db->res['amsum']);							  ?></h1>                              <p>Total Donated</p>                          </div>                      </section>                  </div>                       </div>	</section></section>    <?phprequire_once "footer.php";?>