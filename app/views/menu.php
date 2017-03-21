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

    <div class="collapse navbar-collapse" id="bs-example-navbar-collapse-1">
      <ul class="nav navbar-nav navbar-right">
      <li class="dropdown">
          <a href="#" class="dropdown-toggle navrightmenu menuitems" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">Zero Movement <span class="caret"></span></a>
          <ul class="dropdown-menu">
            <li><a href="#" id="vendorMvt">Vendor</a>
            <form method = 'POST' action = '/csm/public/home/vendorMovement' name='vendorMvtform' id = 'vendorMvtform'>
                <input type='hidden' name = 'vendorMvtNumber' id = 'vendorMvtNumber'>
                <input type='hidden' name = 'fromMvtvendor' id = 'fromMvtvendor'>
                <input type='hidden' name = 'toMvtvendor' id = 'toMvtvendor'>
              </form>
            </li>
            <li><a href="#" id="sectionMvt">Section</a>
            <form method = 'POST' action = '/csm/public/home/sectionMovement' name='sectionMvtform' id = 'sectionMvtform'>
              <input type='hidden' name = 'sectionMvtNumber' id = 'sectionMvtNumber'>
              <input type='hidden' name = 'fromMvtsection' id = 'fromMvtsection'>
              <input type='hidden' name = 'toMvtsection' id = 'toMvtsection'>
            </form></li>
            <li><a href="#" id="vendorSectionMvt">Vendor Section</a>
            <form method = 'POST' action = '/csm/public/home/vendorSectionMovement' name='vendorSectionMvtform' id = 'vendorSectionMvtform'>
              <input type='hidden' name = 'svendorMvtNumber' id = 'svendorMvtNumber'>
              <input type='hidden' name = 'sctvendorMvtNumber' id = 'sctvendorMvtNumber'>
              <input type='hidden' name = 'fromvendorMvtSection' id = 'fromvendorMvtSection'>
              <input type='hidden' name = 'tovendorMvtSection' id = 'tovendorMvtSection'>
            </form></li>
          </ul>
        </li>
        <li><a href="/csm/public/home/vendorNames" class="menuitems navrightmenu">Vendor list</a></li>
        <li><a href="/csm/public/home/sectionNames" class="menuitems navrightmenu">Section list</a></li>
        <li><a href="/csm/public/home/departmentNames" class="menuitems navrightmenu">Department list</a></li>
        <li><a href="/csm/public/home/specials" class="menuitems navrightmenu">Special list</a></li>
        <li class="dropdown">
          <a href="#" class="dropdown-toggle navrightmenu" data-toggle="dropdown" role="button" aria-haspopup="true" aria-expanded="false"><?= ucfirst($_SESSION['firstname']); ?> <span class="caret"></span></a>
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