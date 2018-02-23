<nav class="navbar navbar-default" id = "columnid">
  <div class="container-fluid">
    <!-- Brand and toggle get grouped for better mobile display -->
    <div class="navbar-header">
      <button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#bs-example-navbar-collapse-1" aria-expanded="false">
        <span class="sr-only">Toggle navigation</span>
        <span class="icon-bar"></span>
        <span class="icon-bar"></span>
        <span class="icon-bar"></span>
      </button>
      <a class="navbar-brand" href="/csm/public/home"><p><img src="/csm/public/images/logo.png" class="logo"></p></a>
    </div>

    <form class="form-inline my-2 my-lg-0" style="width:39%;float:left">
      <button class="btn btn-success" style="padding:5px;float:right;margin-top:20px;margin-left:3px"><A HREF="javascript:history.go(0)" style="color:white;"><span class="glyphicon glyphicon-refresh"></span>REFRESH</A></button>
    <?php if(!empty($_SESSION['csm']['keyword'])) : ?>

      <input class="form-control mr-sm-2" type="text" placeholder="Keyword" id='keywordInput' style="margin-top:20px;float:right" value = "<?= $_SESSION['csm']['keyword'] ?>">
    <?php else : ?>
      <input class="form-control mr-sm-2" type="text" placeholder="Keyword" id='keywordInput' style="margin-top:20px;float:right">
    <?php endif; ?>

    </form>

    <div class="collapse navbar-collapse" id="bs-example-navbar-collapse-1">
      <ul class="nav navbar-nav navbar-right">
        <?php 
        if($_SESSION["csm"]['role'] != "3" && $_SESSION["csm"]['role'] != "4" && $_SESSION["csm"]['role'] != "10")
        {
          include("zeroMovement.php");
        } 
        ?>
      
        <li><a href="/csm/public/home/vendorNames" class="menuitems navrightmenu">Vendor list</a></li>
        
        <?php if($_SESSION["csm"]['role'] != "10") : ?>
          <li><a href="/csm/public/home/sectionNames" class="menuitems navrightmenu">Section list</a></li>
        <li><a href="/csm/public/home/departmentNames" class="menuitems navrightmenu">Department list</a></li>
          <li><a href="/csm/public/home/specials" class="menuitems navrightmenu">Special list</a></li>
        <?php else : ?>
        <?php endif; ?>
        <li class="dropdown">
          <a href="#" class="dropdown-toggle navrightmenu" data-toggle="dropdown" role="button" aria-haspopup="true" aria-expanded="false"><?= ucfirst($_SESSION["csm"]['firstname']); ?> <span class="caret"></span></a>
          <ul class="dropdown-menu">
            <li><a href="/csm/public/home">Home</a></li>
            <li><a href="/csm/public/account">Settings</a></li>
            <li><a target = "_blank" href="<?= $data['exportURL']; ?>" id="export">Export</a></li>
            <li><a href="/csm/public/home/logout">Log out</a></li>
          </ul>
        </li>
      </ul>
    </div><!-- /.navbar-collapse -->
  </div><!-- /.container-fluid -->
</nav>
<div class="container-fluid">
  <div class="row salesrow">
    <div class = "col-md-7 fildiv">
      <?php 
      if(!empty($data['title']))
      {
        echo '<p class="filArianne"><span class="csm"><a href="/csm/public/home">CSM</a></span><span class="glyphicon glyphicon-chevron-right"></span><span class="tablecaption">'.$data['title'].'</span>';
      }
      ?>
    </div>
    <div class = "col-md-5 salescol">
      <form class = "form-inline salesdiv">
      <label class="filAriann">Sales dates :</label>
              <input type="date" class="form-control" name = 'fromdate' class = 'dates' id = 'fromdate' value = "<?= $data['from']; ?>">
              <input type="date" class="form-control" name = 'todate' class = 'dates' id = 'todate' value = "<?= $data['to']; ?>">
            </form>
    </div>
  </div>

<a href = '#columnid'><button type="button" class="btn btn-default" id = "backtop">TOP</button></a>